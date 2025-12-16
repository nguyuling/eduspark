<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
    /**
     * List all lessons with optional filtering
     */
    public function index(Request $request)
    {
        $query = Lesson::query();

        // Search by title or description
        if ($request->has('q') && $request->q) {
            $q = $request->q;
            $query->where(function ($q_builder) use ($q) {
                $q_builder->where('title', 'like', "%$q%")
                    ->orWhere('description', 'like', "%$q%");
            });
        }

        // Filter by file type
        if ($request->has('file_type') && $request->file_type) {
            $ext = strtolower($request->file_type);
            $query->where('file_ext', '=', $ext);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $lessons = $query->get();

        // Return JSON if API request, otherwise render view
        if ($request->expectsJson()) {
            return response()->json($lessons->map(function ($lesson) {
                return [
                    'id' => $lesson->id,
                    'title' => $lesson->title,
                    'description' => $lesson->description,
                    'file_name' => $lesson->file_name,
                    'file_path' => $lesson->file_path,
                    'file_ext' => $lesson->file_ext,
                    'class_group' => $lesson->class_group,
                    'visibility' => $lesson->visibility,
                    'created_at' => $lesson->created_at,
                    'updated_at' => $lesson->updated_at,
                ];
            }));
        }

        // Determine user role and show appropriate view
        $user = Auth::user();
        if ($user && $user->role === 'teacher') {
            return view('lesson.index-teacher', compact('lessons'));
        }
        
        return view('lesson.index-student', compact('lessons'));
    }

    /**
     * Show create lesson form (teacher only)
     */
    public function create()
    {
        $user = Auth::user();
        if ($user && $user->role === 'teacher') {
            return view('lesson.create');
        }
        
        abort(403, 'Unauthorized');
    }

    /**
     * Store a new lesson
     * POST /api/lessons
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'class_group' => 'required|string',
                'visibility' => 'required|in:class,public',
                'file' => 'nullable|file|max:10240', // 10MB max
            ]);

            $lesson = new Lesson();
            $lesson->title = $validated['title'];
            $lesson->description = $validated['description'] ?? null;
            $lesson->class_group = $validated['class_group'];
            $lesson->visibility = $validated['visibility'];

            // Handle file upload
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $fileExt = strtolower($file->getClientOriginalExtension());
                
                // Store file in storage/app/lessons
                $path = $file->storeAs('lessons', $fileName);
                
                $lesson->file_name = $file->getClientOriginalName();
                $lesson->file_path = $path;
                $lesson->file_ext = $fileExt;
            }

            $lesson->save();

            return response()->json([
                'success' => true,
                'message' => 'Lesson created successfully',
                'lesson' => $lesson
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Update a lesson
     * PUT /api/lessons/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $lesson = Lesson::findOrFail($id);

            $validated = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'file' => 'nullable|file|max:10240',
            ]);

            if (isset($validated['title'])) {
                $lesson->title = $validated['title'];
            }
            if (isset($validated['description'])) {
                $lesson->description = $validated['description'];
            }

            // Handle file upload
            if ($request->hasFile('file')) {
                // Delete old file if exists
                if ($lesson->file_path && Storage::exists($lesson->file_path)) {
                    Storage::delete($lesson->file_path);
                }

                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $fileExt = strtolower($file->getClientOriginalExtension());
                
                $path = $file->storeAs('lessons', $fileName);
                
                $lesson->file_name = $file->getClientOriginalName();
                $lesson->file_path = $path;
                $lesson->file_ext = $fileExt;
            }

            $lesson->save();

            return response()->json([
                'success' => true,
                'message' => 'Lesson updated successfully',
                'lesson' => $lesson
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Delete a lesson
     * POST /api/lessons/{id}/delete
     */
    public function destroy(Request $request, $id)
    {
        try {
            $lesson = Lesson::findOrFail($id);

            // Delete file if exists
            if ($lesson->file_path && Storage::exists($lesson->file_path)) {
                Storage::delete($lesson->file_path);
            }

            $lesson->delete();

            return response()->json([
                'success' => true,
                'message' => 'Lesson deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get preview data for a lesson
     * GET /api/lessons/{id}/preview
     */
    public function preview($id)
    {
        try {
            $lesson = Lesson::findOrFail($id);

            if (!$lesson->file_path || !Storage::exists($lesson->file_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
            }

            // Generate a temporary URL for the file
            $url = route('lesson.preview.file', ['id' => $id]);

            return response()->json([
                'success' => true,
                'url' => $url,
                'file_name' => $lesson->file_name,
                'file_ext' => $lesson->file_ext,
                'mime' => Storage::mimeType($lesson->file_path),
                'lesson' => $lesson
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Serve the file for preview/download
     * GET /lessons/download/{id}
     */
    public function downloadLesson($id)
    {
        try {
            $lesson = Lesson::findOrFail($id);

            if (!$lesson->file_path || !Storage::exists($lesson->file_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
            }

            return Storage::download($lesson->file_path, $lesson->file_name);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Serve file for preview in iframe
     */
    public function previewFile($id)
    {
        try {
            $lesson = Lesson::findOrFail($id);

            if (!$lesson->file_path || !Storage::exists($lesson->file_path)) {
                return response('File not found', 404);
            }

            $mimeType = Storage::mimeType($lesson->file_path);
            
            return response()->file(
                Storage::path($lesson->file_path),
                ['Content-Type' => $mimeType]
            );

        } catch (\Exception $e) {
            return response('Error: ' . $e->getMessage(), 422);
        }
    }
}
