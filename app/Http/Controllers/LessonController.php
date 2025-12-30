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

        // Get limit from request (default 10, increments by 10)
        $limit = (int) $request->get('limit', 10);
        if ($limit < 10) $limit = 10;
        if ($limit > 1000) $limit = 1000; // Safety limit
        
        // Get lessons with the specified limit
        $allLessons = $lessons;
        $lessons = $allLessons->take($limit);
        $hasMore = count($allLessons) > $limit;
        $nextLimit = $limit + 10;
        
        // Prepare filters for view
        $filters = $request->only(['q', 'file_type', 'date_from', 'date_to']);

        // Return JSON if API request
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
            return view('lesson.index-teacher', compact('lessons', 'filters', 'limit', 'hasMore', 'nextLimit'));
        }
        
        return view('lesson.index-student', compact('lessons', 'filters', 'limit', 'hasMore', 'nextLimit'));
    }

    /**
     * Show create lesson form (teacher only)
     */
    public function create()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'teacher') {
            abort(403, 'Hanya guru dibenarkan mencipta bahan.');
        }
        
        return view('lesson.create');
    }

    /**
     * Store a new lesson
     */
    public function store(Request $request)
{
    try {
        // Check if user is teacher
        $user = Auth::user();
        if (!$user || $user->role !== 'teacher') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
            abort(403, 'Hanya guru dibenarkan mencipta bahan.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'class_group' => 'required|string',
            'visibility' => 'required|in:class,public',
            'file' => 'nullable|file|mimes:pdf,docx,pptx,txt,jpg,jpeg,png|max:10240', // Added mimes validation
        ]);

        $lesson = new Lesson();
        $lesson->title = $validated['title'];
        $lesson->description = $validated['description'] ?? null;
        $lesson->class_group = $validated['class_group'];
        $lesson->visibility = $validated['visibility'];
        $lesson->uploaded_by = Auth::id();

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $file->getClientOriginalName());
            $fileExt = strtolower($file->getClientOriginalExtension());
            
            // Store in public disk
            $path = $file->storeAs('lesson', $fileName, 'public');
            
            $lesson->file_name = $file->getClientOriginalName();
            $lesson->file_path = $path;
            $lesson->file_ext = $fileExt;
        }

        $lesson->save();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Lesson created successfully',
                'lesson' => $lesson
            ], 201);
        }

        return redirect()->route('lesson.index')->with('success', 'Bahan berjaya dicipta!');

    } catch (\Exception $e) {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }

        return back()->withErrors(['error' => $e->getMessage()])->withInput();
    }
}

    /**
     * Show single lesson details (VIEW PAGE)
     */
    public function show($id)
    {
        $lesson = Lesson::findOrFail($id);
        
        // Load uploader relationship
        $lesson->load('uploader');
        
        return view('lesson.show', compact('lesson'));
    }

    /**
     * Show edit form (teacher only, owner restriction)
     */
    public function edit($id)
    {
        $lesson = Lesson::findOrFail($id);
        
        // Check if user is teacher and owner
        $user = Auth::user();
        if (!$user || $user->role !== 'teacher') {
            abort(403, 'Hanya guru dibenarkan mengedit bahan.');
        }
        
        if ($lesson->uploaded_by !== $user->id) {
            abort(403, 'Anda tidak dibenarkan mengedit bahan ini. Hanya pemilik boleh mengedit.');
        }

        return view('lesson.edit', compact('lesson'));
    }

    /**
     * Update a lesson (owner restriction)
     */
    public function update(Request $request, $id)
    {
        try {
            $lesson = Lesson::findOrFail($id);

            // Check ownership
            $user = Auth::user();
            if (!$user || $user->role !== 'teacher') {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized'
                    ], 403);
                }
                abort(403, 'Hanya guru dibenarkan mengedit bahan.');
            }
            
            if ($lesson->uploaded_by !== $user->id) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized - Not the owner'
                    ], 403);
                }
                abort(403, 'Anda tidak dibenarkan mengedit bahan ini. Hanya pemilik boleh mengedit.');
            }

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'class_group' => 'required|string',
                'visibility' => 'required|in:class,public',
                'file' => 'nullable|file|max:10240',
            ]);

            $lesson->title = $validated['title'];
            $lesson->description = $validated['description'] ?? null;
            $lesson->class_group = $validated['class_group'];
            $lesson->visibility = $validated['visibility'];

            // Handle file upload
            if ($request->hasFile('file')) {
                // Delete old file if exists
                if ($lesson->file_path && Storage::disk('public')->exists($lesson->file_path)) {
                    Storage::disk('public')->delete($lesson->file_path);
                }

                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $fileExt = strtolower($file->getClientOriginalExtension());
                
                $path = $file->storeAs('lesson', $fileName, 'public');
                
                $lesson->file_name = $file->getClientOriginalName();
                $lesson->file_path = $path;
                $lesson->file_ext = $fileExt;
            }

            $lesson->save();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lesson updated successfully',
                    'lesson' => $lesson
                ]);
            }

            return redirect()->route('lesson.index')->with('success', 'Bahan berjaya dikemaskini!');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }

            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Delete a lesson (owner restriction)
     */
    public function destroy(Request $request, $id)
    {
        try {
            $lesson = Lesson::findOrFail($id);

            // Check ownership
            $user = Auth::user();
            if (!$user || $user->role !== 'teacher') {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized'
                    ], 403);
                }
                abort(403, 'Hanya guru dibenarkan memadam bahan.');
            }
            
            if ($lesson->uploaded_by !== $user->id) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized - Not the owner'
                    ], 403);
                }
                abort(403, 'Anda tidak dibenarkan memadam bahan ini. Hanya pemilik boleh memadam.');
            }

            // Delete file if exists
            if ($lesson->file_path && Storage::disk('public')->exists($lesson->file_path)) {
                Storage::disk('public')->delete($lesson->file_path);
            }

            $lesson->delete();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lesson deleted successfully'
                ]);
            }

            return redirect()->route('lesson.index')->with('success', 'Bahan berjaya dipadam!');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }

            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Get preview data for a lesson
     */
    /**
 * Serve file for preview in iframe
 */
public function previewFile($id)
{
    try {
        $lesson = Lesson::findOrFail($id);

        if (!$lesson->file_path) {
            abort(404, 'File not found');
        }

        // Try public disk first
        if (Storage::disk('public')->exists($lesson->file_path)) {
            $filePath = Storage::disk('public')->path($lesson->file_path);
            $mimeType = Storage::disk('public')->mimeType($lesson->file_path);
        } 
        // Fallback to default disk
        elseif (Storage::exists($lesson->file_path)) {
            $filePath = Storage::path($lesson->file_path);
            $mimeType = Storage::mimeType($lesson->file_path);
        } 
        else {
            abort(404, 'File not found in storage');
        }

        // Check if file actually exists on filesystem
        if (!file_exists($filePath)) {
            abort(404, 'Physical file not found');
        }

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $lesson->file_name . '"'
        ]);

    } catch (\Exception $e) {
        return response('Error: ' . $e->getMessage(), 500);
    }
}

    /**
     * Serve the file for preview/download
     */
    public function downloadLesson($id)
{
    try {
        $lesson = Lesson::findOrFail($id);

        if (!$lesson->file_path) {
            abort(404, 'File not found');
        }

        // Try public disk first
        if (Storage::disk('public')->exists($lesson->file_path)) {
            return Storage::disk('public')->download($lesson->file_path, $lesson->file_name);
        } 
        // Fallback to default disk
        elseif (Storage::exists($lesson->file_path)) {
            return Storage::download($lesson->file_path, $lesson->file_name);
        } 
        else {
            abort(404, 'File not found in storage');
        }

    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Gagal memuat turun fail: ' . $e->getMessage()]);
    }
}

    /**
     * Serve file for preview in iframe
     */
    public function preview($id)
    {
        try {
            $lesson = Lesson::findOrFail($id);

            if (!$lesson->file_path || !Storage::disk('public')->exists($lesson->file_path)) {
                return response('File not found', 404);
            }

            $mimeType = Storage::disk('public')->mimeType($lesson->file_path);
            $filePath = Storage::disk('public')->path($lesson->file_path);
            
            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . $lesson->file_name . '"'
            ]);

        } catch (\Exception $e) {
            return response('Error: ' . $e->getMessage(), 422);
        }
    }
}
