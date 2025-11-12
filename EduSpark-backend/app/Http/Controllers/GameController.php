<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GameController extends Controller
{
    /**
     * Show the form for editing the specified game.
     */
    public function edit($id)
    {
        $game = Game::with('creator')->findOrFail($id);
        
        return response()->json([
            'game' => $game,
            'editable_fields' => [
                'title', 'description', 'difficulty', 'category',
                'time_limit', 'points_per_question', 'cover_image',
                'game_file', 'additional_files', 'game_settings'
            ]
        ]);
    }

    /**
     * Update the specified game in storage.
     */
    public function update(Request $request, $id)
    {
        $game = Game::findOrFail($id);

        // Validate the update request
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255|unique:games,title,' . $id,
            'description' => 'sometimes|string',
            'difficulty' => ['sometimes', Rule::in(['easy', 'medium', 'hard'])],
            'category' => 'sometimes|string|max:100',
            'time_limit' => 'sometimes|nullable|integer|min:1',
            'points_per_question' => 'sometimes|integer|min:1',
            'cover_image' => 'sometimes|file|image|mimes:jpeg,png,jpg,gif|max:5120',
            'game_file' => 'sometimes|file|mimes:zip,html,js,css,json,php|max:20480', // 20MB max
            'additional_files.*' => 'sometimes|file|max:5120',
            'notify_students' => 'sometimes|boolean',
            'game_settings' => 'sometimes|json',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Start transaction for atomic update
            \DB::beginTransaction();

            $updateData = $request->only([
                'title', 'description', 'difficulty', 'category',
                'time_limit', 'points_per_question', 'game_settings'
            ]);

            // Handle cover image update
            if ($request->hasFile('cover_image')) {
                // Delete old cover image if exists
                if ($game->cover_image && Storage::exists($game->cover_image)) {
                    Storage::delete($game->cover_image);
                }
                
                $coverPath = $request->file('cover_image')->store('games/covers', 'public');
                $updateData['cover_image'] = $coverPath;
            }

            // Handle main game file update (triggers version increment)
            $gameFileUpdated = false;
            if ($request->hasFile('game_file')) {
                // Delete old game file if exists
                if ($game->game_file && Storage::exists($game->game_file)) {
                    Storage::delete($game->game_file);
                }
                
                $gameFilePath = $request->file('game_file')->store('games/files', 'public');
                $updateData['game_file'] = $gameFilePath;
                $updateData['version'] = $game->version + 1;
                $gameFileUpdated = true;
            }

            // Handle additional files update
            if ($request->hasFile('additional_files')) {
                $additionalFiles = [];
                
                // Delete old additional files if they exist
                if ($game->additional_files) {
                    foreach ($game->additional_files as $oldFile) {
                        if (Storage::exists($oldFile)) {
                            Storage::delete($oldFile);
                        }
                    }
                }

                // Store new additional files
                foreach ($request->file('additional_files') as $file) {
                    $filePath = $file->store('games/additional', 'public');
                    $additionalFiles[] = $filePath;
                }
                
                $updateData['additional_files'] = $additionalFiles;
            }

            // Update timestamps
            $updateData['last_updated_at'] = now();

            // Perform the update
            $game->update($updateData);

            // Notify students if requested and game file was updated
            $studentsNotified = false;
            if ($request->boolean('notify_students') && $gameFileUpdated) {
                $studentsNotified = $this->notifyStudentsAboutUpdate($game);
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Game updated successfully',
                'game' => $game->fresh('creator'),
                'version_incremented' => $gameFileUpdated,
                'students_notified' => $studentsNotified,
                'changes' => $game->getChanges()
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update game',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Partial update for specific fields (PATCH)
     */
    public function partialUpdate(Request $request, $id)
    {
        $game = Game::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255|unique:games,title,' . $id,
            'description' => 'sometimes|string',
            'difficulty' => ['sometimes', Rule::in(['easy', 'medium', 'hard'])],
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $game->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Game updated successfully',
            'game' => $game->fresh('creator')
        ]);
    }

    /**
     * Get game update history
     */
    public function getUpdateHistory($id)
    {
        $game = Game::findOrFail($id);
        
        return response()->json([
            'game_id' => $game->id,
            'title' => $game->title,
            'current_version' => $game->version,
            'last_updated' => $game->last_updated_at,
            'created_at' => $game->created_at,
            'total_updates' => $game->version - 1,
            'has_major_changes' => $game->version > 1
        ]);
    }

    /**
     * Validate game data before update
     */
    public function validateGameData(Request $request, $id = null)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:games,title,' . $id,
            'description' => 'required|string',
            'difficulty' => ['required', Rule::in(['easy', 'medium', 'hard'])],
            'category' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'valid' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        return response()->json([
            'valid' => true,
            'message' => 'Game data is valid'
        ]);
    }

    /**
     * Notify students about game updates
     */
    private function notifyStudentsAboutUpdate(Game $game): bool
    {
        try {
            // Get students who have progress in this game
            $students = \DB::table('user_game_progress')
                ->where('game_id', $game->id)
                ->join('users', 'user_game_progress.user_id', '=', 'users.id')
                ->select('users.id', 'users.email', 'users.name')
                ->get();

            // Create notification records
            $notifications = [];
            foreach ($students as $student) {
                $notifications[] = [
                    'user_id' => $student->id,
                    'type' => 'game_updated',
                    'title' => 'Game Updated: ' . $game->title,
                    'message' => 'The game "' . $game->title . '" has been updated to version ' . $game->version . '.',
                    'data' => json_encode([
                        'game_id' => $game->id,
                        'game_title' => $game->title,
                        'version' => $game->version,
                        'updated_at' => $game->last_updated_at
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Insert notifications (you might have a notifications table)
            if (!empty($notifications)) {
                \DB::table('notifications')->insert($notifications);
            }

            // Log the notification
            \Log::info("Game update notification sent for '{$game->title}' to {$students->count()} students");

            return true;

        } catch (\Exception $e) {
            \Log::error("Failed to notify students about game update: " . $e->getMessage());
            return false;
        }
    }
}