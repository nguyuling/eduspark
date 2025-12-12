<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lesson;
use Illuminate\Support\Facades\Storage;

class LessonController extends Controller
{
    /**
     * List lessons with optional search and filters
     */
    public function index(Request $request)
    {
        $q = $request->query('q');
        $fileType = $request->query('file_type');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $query = Lesson::with('uploader')->orderBy('created_at', 'desc');

        if ($q) {
            $query->where(function($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        if ($fileType) {
            $fileType = ltrim(strtolower($fileType), '.');
            $query->where('file_path', 'like', "%.$fileType");
        }

        if ($dateFrom) $query->whereDate('created_at', '>=', $dateFrom);
        if ($dateTo) $query->whereDate('created_at', '<=', $dateTo);

        $lessons = $query->get()->map(function($lesson) {
            $lesson->file_url = $lesson->file_path ? asset('storage/' . $lesson->file_path) : null;
            $lesson->file_name = $lesson->file_path ? basename($lesson->file_path) : null;
            $lesson->file_ext = $lesson->file_path ? strtolower(pathinfo($lesson->file_path, PATHINFO_EXTENSION)) : null;
            return $lesson;
        });

        return response()->json($lessons);
    }

    /**
     * Create a new lesson
     */
    public function store(Request $request)
    {
        try {
            $user = auth()->user() ?? (object)[
                'id' => 1,
                'class_group' => 'A'
            ];

            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'file' => 'nullable|file|max:10240',
                'class_group' => 'nullable|string',
                'visibility' => 'nullable|string',
            ]);

            $path = $request->file('file') ? $request->file('file')->store('lessons', 'public') : null;

            $lesson = Lesson::create([
                'title' => $request->title,
                'description' => $request->description,
                'file_path' => $path,
                'uploaded_by' => $user->id,
                'class_group' => $request->class_group ?? $user->class_group,
                'visibility' => $request->visibility ?? 'class',
            ]);

            return response()->json(['success' => true, 'lesson' => $lesson], 201);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update lesson
     */
    public function update(Request $request, $id)
    {
        try {
            $lesson = Lesson::findOrFail($id);

            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'file' => 'nullable|file|max:10240',
            ]);

            if ($request->hasFile('file')) {
                if ($lesson->file_path) Storage::disk('public')->delete($lesson->file_path);
                $lesson->file_path = $request->file('file')->store('lessons', 'public');
            }

            $lesson->title = $request->title;
            $lesson->description = $request->description;
            $lesson->save();

            return response()->json([
                'success' => true,
                'message' => 'Lesson updated successfully!',
                'lesson' => $lesson
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete lesson
     */
    public function destroy($id)
    {
        $lesson = Lesson::findOrFail($id);

        if ($lesson->file_path) Storage::disk('public')->delete($lesson->file_path);

        $lesson->delete();

        return response()->json(['message' => 'Lesson deleted successfully.']);
    }

    /**
     * Preview file
     */
    public function preview($id)
    {
        $lesson = Lesson::findOrFail($id);

        if (!$lesson->file_path) {
            return response()->json(['success' => false, 'message' => 'No file attached.'], 404);
        }

        $path = $lesson->file_path;
        $url = asset('storage/' . $path);

        try {
            $mime = Storage::disk('public')->mimeType($path);
        } catch (\Exception $e) {
            $mime = null;
        }

        return response()->json([
            'success' => true,
            'url' => $url,
            'mime' => $mime,
            'file_name' => basename($path),
            'file_ext' => strtolower(pathinfo($path, PATHINFO_EXTENSION)),
            'lesson' => $lesson,
        ]);
    }

    /**
     * Download file
     */
    public function download($id)
    {
        $lesson = Lesson::findOrFail($id);

        if (!$lesson->file_path || !Storage::disk('public')->exists($lesson->file_path)) {
            abort(404, 'File not found.');
        }

        $downloadName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $lesson->title) . '.' . pathinfo($lesson->file_path, PATHINFO_EXTENSION);

        return Storage::disk('public')->download($lesson->file_path, $downloadName);
    }
}

