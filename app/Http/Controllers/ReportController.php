<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    /**
     * Reports landing page.
     */
    public function index()
    {
        $classes = [];

        if (Schema::hasTable('classrooms')) {
            $classes = DB::table('classrooms')->orderBy('name')->pluck('name')->toArray();
        } elseif (Schema::hasTable('students')) {
            $possible = ['class','class_level','level','form','grade','class_name','group','classroom'];
            foreach ($possible as $col) {
                if (Schema::hasColumn('students', $col)) {
                    $classes = DB::table('students')->select($col)->distinct()->orderBy($col)->pluck($col)->toArray();
                    break;
                }
            }
        }

        // Normalize classes: trim whitespace, remove empty and duplicate values
        if (! empty($classes) && is_array($classes)) {
            $classes = array_filter(array_map(function($v) {
                return is_string($v) ? trim($v) : $v;
            }, $classes));
            $classes = array_values(array_unique($classes));
        }

        $studentCount = Schema::hasTable('students') ? DB::table('students')->count() : 0;

        return view('reports.index', compact('classes','studentCount'));
    }

    /**
     * AJAX: return students for a chosen class name.
     */
    public function studentsByClass($class)
    {
        // Try to find students by class — start by trying classrooms table
        $rows = null;
        if (Schema::hasTable('classrooms')) {
            $classroom = DB::table('classrooms')->where('name', $class)->first();
            if ($classroom && Schema::hasColumn('students', 'classroom_id')) {
                $rows = DB::table('students')->where('classroom_id', $classroom->id)->get();
            } elseif ($classroom && Schema::hasColumn('students', 'classroom')) {
                $rows = DB::table('students')->where('classroom', $classroom->name)->get();
            }
        }

        // If classrooms didn't work, try direct class column in students
        if (!$rows || $rows->isEmpty()) {
            $possible = ['class','class_level','level','form','grade','class_name','group','classroom'];
            foreach ($possible as $col) {
                if (Schema::hasColumn('students', $col)) {
                    $rows = DB::table('students')->where($col, $class)->get();
                    if ($rows && $rows->isNotEmpty()) break;
                }
            }
        }

        if (!$rows || $rows->isEmpty()) {
            \Log::info("studentsByClass: No rows found for class: {$class}");
            return response()->json([]);
        }

        \Log::info("studentsByClass: Found {$rows->count()} rows for class: {$class}");

        // detect available student name columns to avoid N/A
        $studentNameCol = null;
        if (Schema::hasColumn('students','name')) $studentNameCol = 'name';
        elseif (Schema::hasColumn('students','full_name')) $studentNameCol = 'full_name';
        elseif (Schema::hasColumn('students','fullName')) $studentNameCol = 'fullName';
        elseif (Schema::hasColumn('students','first_name') && Schema::hasColumn('students','last_name')) $studentNameCol = 'first_last';

        $map = [];
        foreach ($rows as $r) {
            $key = $r->user_id ?? $r->id;
            if (isset($map[$key])) continue; // dedupe duplicates

            $name = null;
            // prefer linked users.name when available
            if (isset($r->user_id) && Schema::hasTable('users')) {
                $u = DB::table('users')->where('id', $r->user_id)->first();
                $name = $u->name ?? null;
            }

            // fallback to student table columns in order of preference
            if (! $name && $studentNameCol) {
                if ($studentNameCol === 'first_last') {
                    $fn = $r->first_name ?? '';
                    $ln = $r->last_name ?? '';
                    $name = trim($fn . ' ' . $ln);
                } else {
                    $name = $r->{$studentNameCol} ?? null;
                }
            }

            // final fallbacks
            if (! $name) $name = $r->name ?? $r->full_name ?? $r->fullName ?? null;

            // skip entries without a valid name to avoid N/A in dropdown
            if (! $name || strtoupper(trim($name)) === 'N/A') {
                continue;
            }
            $map[$key] = ['id' => ($r->user_id ?? $r->id), 'name' => $name];
        }
        return response()->json(array_values($map));
    }

    /**
     * Student report page (used by /reports/student/{id}).
     * When requested via AJAX it will return a JSON payload containing the rendered partial HTML.
     */
    public function studentReport(Request $request, $id)
    {
        $displayStudent = (object)['id' => null, 'name' => 'N/A'];
        $lookupUserId = null;

        // $id could be either students.id or users.id (from AJAX dropdown)
        // Strategy: try all possible lookups and prioritize user.name from dropdown
        
        $studentRecord = null;
        $userRecord = null;

        // Prefer treating $id as users.id (what the dropdown sends) to avoid student/user id collisions
        if (Schema::hasTable('users')) {
            $userRecord = DB::table('users')->where('id', $id)->first();
        }
        if (Schema::hasTable('students')) {
            $studentRecord = DB::table('students')->where('id', $id)->first();
        }

        if ($userRecord) {
            // Use user first
            $displayStudent->id = $userRecord->id;
            $displayStudent->name = $userRecord->name ?? 'N/A';
            $lookupUserId = $userRecord->id;

            // If there is a student row linked to this user, prefer that for stats
            if (Schema::hasTable('students')) {
                $stu = DB::table('students')->where('user_id', $userRecord->id)->first();
                if ($stu) {
                    $studentRecord = $stu;
                }
            }
        }

        if (!$userRecord && $studentRecord) {
            // Fallback: only student found
            $displayStudent->id = $studentRecord->id;
            $studentName = null;
            if (isset($studentRecord->name) && !empty($studentRecord->name) && $studentRecord->name !== 'N/A') {
                $studentName = $studentRecord->name;
            } elseif (isset($studentRecord->full_name) && !empty($studentRecord->full_name)) {
                $studentName = $studentRecord->full_name;
            }
            if (isset($studentRecord->user_id) && $studentRecord->user_id && Schema::hasTable('users')) {
                $linkedUser = DB::table('users')->where('id', $studentRecord->user_id)->first();
                if ($linkedUser && isset($linkedUser->name)) {
                    $studentName = $linkedUser->name;
                    $lookupUserId = $studentRecord->user_id;
                }
            }
            if ($studentName) $displayStudent->name = $studentName;
            if (is_null($lookupUserId)) $lookupUserId = $studentRecord->id;
        }

        // Fetch attempts with quiz titles
        $attempts = [];
        if (Schema::hasTable('quiz_attempts') && $lookupUserId) {
            $attempts = DB::table('quiz_attempts as qa')
                ->leftJoin('quizzes as q', 'qa.quiz_id', '=', 'q.id')
                ->select('qa.created_at', 'qa.score', 'q.title', 'qa.quiz_id')
                ->where('qa.student_id', $lookupUserId)
                ->orderBy('qa.created_at', 'desc')
                ->get()
                ->toArray();
        } elseif (Schema::hasTable('quiz_attempt') && $lookupUserId) {
            $attempts = DB::table('quiz_attempt as qa')
                ->leftJoin('quiz as q', 'qa.quiz_id', '=', 'q.id')
                ->select('qa.created_at', 'qa.score', 'q.title', 'qa.quiz_id')
                ->where('qa.user_id', $lookupUserId)
                ->orderBy('qa.created_at', 'desc')
                ->get()
                ->toArray();
        }

        // Compute stats
        $scores = [];
        $topicScores = [];
        foreach ($attempts as $a) {
            if (isset($a->score) && is_numeric($a->score)) {
                $scores[] = (float)$a->score;
                $title = $a->title ?? 'Unknown';
                if (!isset($topicScores[$title])) {
                    $topicScores[$title] = [];
                }
                $topicScores[$title][] = (float)$a->score;
            }
        }

        // Calculate averages and find strongest/weakest topics
        $topicAverages = [];
        foreach ($topicScores as $topic => $scores_arr) {
            $topicAverages[$topic] = array_sum($scores_arr) / count($scores_arr);
        }

        $avg = count($scores) ? round(array_sum($scores) / count($scores), 2) . '%' : 'N/A';
        $highest = count($scores) ? round(max($scores), 2) . '%' : 'N/A';
        $weakest = count($scores) ? round(min($scores), 2) . '%' : 'N/A';
        
        $highestTopic = count($topicAverages) > 0 ? array_search(max($topicAverages), $topicAverages) : 'N/A';
        $weakestTopic = count($topicAverages) > 0 ? array_search(min($topicAverages), $topicAverages) : 'N/A';

        // Map attempts for view partial
        $attemptsForView = [];
        foreach ($attempts as $a) {
            $attemptsForView[] = [
                'date' => $a->created_at ? date('Y-m-d', strtotime($a->created_at)) : '',
                'title' => $a->title ?? 'N/A',
                'score' => isset($a->score) ? round($a->score, 2) . '%' : ''
            ];
        }

        $stats = [
            'average_score' => $avg,
            'highest_score' => $highest,
            'highest_subject' => $highestTopic,
            'weakest_score' => $weakest,
            'weakest_subject' => $weakestTopic,
            'attempts' => $attemptsForView
        ];

        // If AJAX: return partial HTML as JSON
        if ($request->ajax()) {
            $html = view('reports.partials.student_panel', [
                'student' => $displayStudent,
                'stats' => $stats
            ])->render();

            return response()->json(['html' => $html]);
        }

        // Otherwise return full page
        return view('reports.student', [
            'student' => $displayStudent,
            'stats' => $stats,
            'avgQuiz' => $avg,
            'avgGame' => 'N/A',
            'attendanceRate' => 'N/A',
            'weakTopic' => 'N/A'
        ]);
    }

    /**
     * Alias to keep route compatibility with existing blades/JS.
     */
    public function student(Request $request, $id)
    {
        return $this->studentReport($request, $id);
    }

    /**
     * Stream CSV export for student (formatted header + table).
     */
    public function exportStudentCsv($id)
    {
        // Try students row first, fallback to users
        $student = null;
        if (Schema::hasTable('students')) {
            $student = DB::table('students')->where('id', $id)->first();
        }
        if (! $student && Schema::hasTable('users')) {
            $student = DB::table('users')->where('id', $id)->first();
        }
        if (! $student) abort(404);

        // Determine user id used in quiz_attempts (some schemas store students.user_id)
        $userId = $student->user_id ?? $student->id;

        // Retrieve attempts joined with quizzes if available
        $attemptsQuery = null;
        if (Schema::hasTable('quiz_attempts')) {
            $attemptsQuery = DB::table('quiz_attempts as qa')
                ->leftJoin('quizzes as q', 'qa.quiz_id', '=', 'q.id')
                ->select('qa.created_at as date', DB::raw("'Kuiz' as type"), 'q.title as topic', 'qa.score')
                ->where('qa.student_id', $userId)
                ->orderBy('qa.created_at', 'desc')
                ->get();
        } elseif (Schema::hasTable('quiz_attempt')) {
            $attemptsQuery = DB::table('quiz_attempt as qa')
                ->leftJoin('quiz as q', 'qa.quiz_id', '=', 'q.id')
                ->select('qa.created_at as date', DB::raw("'Kuiz' as type"), 'q.title as topic', 'qa.score')
                ->where('qa.user_id', $userId)
                ->orderBy('qa.created_at','desc')
                ->get();
        } else {
            $attemptsQuery = collect();
        }

        // Resolve student name (check users link) and class (check classroom_id)
        $studentName = null;
        $studentClass = 'N/A';

        // If this row is from students table and has user_id, fetch user for name
        if (isset($student->user_id) && Schema::hasTable('users')) {
            $u = DB::table('users')->where('id', $student->user_id)->first();
            $studentName = $student->name ?? $student->full_name ?? ($u->name ?? null);
        } else {
            // maybe row itself is a user row or contains name directly
            $studentName = $student->name ?? $student->full_name ?? $student->username ?? null;
        }

        // Try classroom name if classroom_id exists
        if (isset($student->classroom_id) && Schema::hasTable('classrooms')) {
            $c = DB::table('classrooms')->where('id', $student->classroom_id)->first();
            if ($c) $studentClass = $c->name ?? $c->title ?? $studentClass;
        }

        // other possible class columns
        if ($studentClass === 'N/A') {
            if (isset($student->class) && $student->class) $studentClass = $student->class;
            elseif (isset($student->classroom) && $student->classroom) $studentClass = $student->classroom;
            elseif (isset($student->class_name) && $student->class_name) $studentClass = $student->class_name;
        }

        $studentName = $studentName ?? 'N/A';
        $filename = 'laporan_prestasi_' . Str::slug($studentName) . '.csv';

        $response = new StreamedResponse(function() use ($attemptsQuery, $studentName, $studentClass) {
            $out = fopen('php://output','w');

            // Header block
            fputcsv($out, ['Name:', $studentName]);
            fputcsv($out, ['Class:', $studentClass]);
            fputcsv($out, []); // blank
            fputcsv($out, ['LAPORAN PRESTASI PELAJAR']);
            fputcsv($out, []); // blank

            // Table header
            fputcsv($out, ['Tarikh','Jenis','Topik','Skor']);

            // Rows
            foreach ($attemptsQuery as $r) {
                fputcsv($out, [
                    $r->date ?? '',
                    $r->type ?? '',
                    $r->topic ?? 'N/A',
                    $r->score ?? ''
                ]);
            }

            fclose($out);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);

        return $response;
    }

    /**
     * Printable student view (blade).
     */
    public function exportStudentPrintable($id)
    {
        // Try students row first, fallback to users
        $student = null;
        if (Schema::hasTable('students')) {
            $student = DB::table('students')->where('id', $id)->first();
        }
        if (! $student && Schema::hasTable('users')) {
            $student = DB::table('users')->where('id', $id)->first();
        }
        if (! $student) abort(404);

        $userId = $student->user_id ?? $student->id;

        $attempts = [];
        if (Schema::hasTable('quiz_attempts')) {
            $attempts = DB::table('quiz_attempts as qa')
                ->leftJoin('quizzes as q', 'qa.quiz_id', '=', 'q.id')
                ->select('qa.created_at as date', DB::raw("'Kuiz' as type"), 'q.title as topic', 'qa.score')
                ->where('qa.student_id', $userId)
                ->orderBy('qa.created_at', 'desc')
                ->get()
                ->map(function($r){
                    return [
                        'date' => $r->date ? date('Y-m-d', strtotime($r->date)) : '',
                        'type' => $r->type ?? 'Kuiz',
                        'topic'=> $r->topic ?? 'N/A',
                        'score'=> $r->score ?? ''
                    ];
                })->toArray();
        }

        // Resolve name and class similar to CSV
        $studentName = null;
        $studentClass = 'N/A';
        if (isset($student->user_id) && Schema::hasTable('users')) {
            $u = DB::table('users')->where('id', $student->user_id)->first();
            $studentName = $student->name ?? $student->full_name ?? ($u->name ?? null);
        } else {
            $studentName = $student->name ?? $student->full_name ?? $student->username ?? null;
        }
        if (isset($student->classroom_id) && Schema::hasTable('classrooms')) {
            $c = DB::table('classrooms')->where('id', $student->classroom_id)->first();
            if ($c) $studentClass = $c->name ?? $c->title ?? $studentClass;
        }
        if ($studentClass === 'N/A') {
            if (isset($student->class) && $student->class) $studentClass = $student->class;
            elseif (isset($student->classroom) && $student->classroom) $studentClass = $student->classroom;
            elseif (isset($student->class_name) && $student->class_name) $studentClass = $student->class_name;
        }
        $studentName = $studentName ?? 'N/A';

        return view('reports.student_pdf', [
            'student' => $student,
            'studentName' => $studentName,
            'studentClass' => $studentClass,
            'attempts' => $attempts,
            'title' => 'LAPORAN PRESTASI PELAJAR'
        ]);
    }

    /**
     * Excel export (XLSX) using Maatwebsite\Excel.
     */
    public function exportStudentExcel($id)
    {
        // Try students row first, fallback to users
        $student = null;
        if (Schema::hasTable('students')) {
            $student = DB::table('students')->where('id', $id)->first();
        }
        if (! $student && Schema::hasTable('users')) {
            $student = DB::table('users')->where('id', $id)->first();
        }
        if (! $student) abort(404);

        $userId = $student->user_id ?? $student->id;

        $attempts = [];
        if (Schema::hasTable('quiz_attempts')) {
            $attempts = DB::table('quiz_attempts as qa')
                ->leftJoin('quizzes as q', 'qa.quiz_id', '=', 'q.id')
                ->select('qa.created_at as date', DB::raw("'Kuiz' as type"), 'q.title as topic', 'qa.score')
                ->where('qa.student_id', $userId)
                ->orderBy('qa.created_at', 'desc')
                ->get()
                ->map(function ($r) {
                    return [
                        'Tarikh' => $r->date ? date('Y-m-d', strtotime($r->date)) : '',
                        'Jenis' => $r->type ?? 'Kuiz',
                        'Topik' => $r->topic ?? 'N/A',
                        'Skor' => $r->score ?? ''
                    ];
                })->toArray();
        }

        // Resolve name and class
        $studentName = null;
        $studentClass = 'N/A';
        if (isset($student->user_id) && Schema::hasTable('users')) {
            $u = DB::table('users')->where('id', $student->user_id)->first();
            $studentName = $student->name ?? $student->full_name ?? ($u->name ?? null);
        } else {
            $studentName = $student->name ?? $student->full_name ?? $student->username ?? null;
        }
        if (isset($student->classroom_id) && Schema::hasTable('classrooms')) {
            $c = DB::table('classrooms')->where('id', $student->classroom_id)->first();
            if ($c) $studentClass = $c->name ?? $c->title ?? $studentClass;
        }
        if ($studentClass === 'N/A') {
            if (isset($student->class) && $student->class) $studentClass = $student->class;
            elseif (isset($student->classroom) && $student->classroom) $studentClass = $student->classroom;
            elseif (isset($student->class_name) && $student->class_name) $studentClass = $student->class_name;
        }
        $studentName = $studentName ?? 'N/A';

        // Build rows
        $rows = [];
        $rows[] = ['Name:', $studentName];
        $rows[] = ['Class:', $studentClass];
        $rows[] = [];
        $rows[] = ['LAPORAN PRESTASI PELAJAR'];
        $rows[] = [];
        $rows[] = ['Tarikh','Jenis','Topik','Skor'];

        foreach ($attempts as $a) {
            $rows[] = [$a['Tarikh'], $a['Jenis'], $a['Topik'], $a['Skor']];
        }

        $export = new class($rows) implements \Maatwebsite\Excel\Concerns\FromArray,
                                             \Maatwebsite\Excel\Concerns\WithTitle,
                                             \Maatwebsite\Excel\Concerns\WithStyles {
            private $rows;
            public function __construct($rows) { $this->rows = $rows; }
            public function array(): array { return $this->rows; }
            public function title(): string { return 'Laporan'; }
            public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
            {
                // header meta bold
                $sheet->getStyle('A1:B5')->getFont()->setBold(true);
                $headerRow = 6;
                $sheet->getStyle("A{$headerRow}:D{$headerRow}")->getFont()->setBold(true);
                $sheet->getColumnDimension('A')->setWidth(14);
                $sheet->getColumnDimension('B')->setWidth(12);
                $sheet->getColumnDimension('C')->setWidth(40);
                $sheet->getColumnDimension('D')->setWidth(10);
            }
        };

        $filename = 'laporan_prestasi_' . Str::slug($studentName) . '.xlsx';
        return Excel::download($export, $filename);
    }

    /**
     * PDF export (uses blade view resources/views/reports/student_pdf.blade.php)
     */
    public function exportStudentPdf($id)
    {
        // Try students row first, fallback to users
        $student = null;
        if (Schema::hasTable('students')) {
            $student = DB::table('students')->where('id', $id)->first();
        }
        if (! $student && Schema::hasTable('users')) {
            $student = DB::table('users')->where('id', $id)->first();
        }
        if (! $student) abort(404);

        $userId = $student->user_id ?? $student->id;

        $attempts = [];
        if (Schema::hasTable('quiz_attempts')) {
            $attempts = DB::table('quiz_attempts as qa')
                ->leftJoin('quizzes as q', 'qa.quiz_id', '=', 'q.id')
                ->select('qa.created_at as date', DB::raw("'Kuiz' as type"), 'q.title as topic', 'qa.score')
                ->where('qa.student_id', $userId)
                ->orderBy('qa.created_at', 'desc')
                ->get()
                ->map(function($r){
                    return [
                        'date' => $r->date ? date('Y-m-d', strtotime($r->date)) : '',
                        'type' => $r->type ?? 'Kuiz',
                        'topic'=> $r->topic ?? 'N/A',
                        'score'=> $r->score ?? ''
                    ];
                });
        }

        // Resolve name and class
        $studentName = null;
        $studentClass = 'N/A';
        if (isset($student->user_id) && Schema::hasTable('users')) {
            $u = DB::table('users')->where('id', $student->user_id)->first();
            $studentName = $student->name ?? $student->full_name ?? ($u->name ?? null);
        } else {
            $studentName = $student->name ?? $student->full_name ?? $student->username ?? null;
        }
        if (isset($student->classroom_id) && Schema::hasTable('classrooms')) {
            $c = DB::table('classrooms')->where('id', $student->classroom_id)->first();
            if ($c) $studentClass = $c->name ?? $c->title ?? $studentClass;
        }
        if ($studentClass === 'N/A') {
            if (isset($student->class) && $student->class) $studentClass = $student->class;
            elseif (isset($student->classroom) && $student->classroom) $studentClass = $student->classroom;
            elseif (isset($student->class_name) && $student->class_name) $studentClass = $student->class_name;
        }
        $studentName = $studentName ?? 'N/A';

        $data = [
            'student' => $student,
            'studentName' => $studentName,
            'studentClass' => $studentClass,
            'attempts' => $attempts,
            'title' => 'LAPORAN PRESTASI PELAJAR'
        ];

        $pdf = Pdf::loadView('reports.student_pdf', $data)
                  ->setPaper('a4', 'portrait');

        $fileName = 'laporan_prestasi_' . Str::slug($studentName) . '.pdf';
        return $pdf->download($fileName);
    }

    /**
     * Class report page (expects ?class=Name)
     * Supports AJAX: returns partial HTML JSON when requested via X-Requested-With.
     */
    public function classIndex(Request $request)
    {
        $selectedClass = $request->query('class', null);
        $classes = [];

        if (Schema::hasTable('classrooms')) {
            $classes = DB::table('classrooms')->orderBy('name')->pluck('name')->toArray();
        } elseif (Schema::hasTable('students')) {
            $possible = ['class','class_level','level','form','grade','class_name','group','classroom'];
            foreach ($possible as $col) {
                if (Schema::hasColumn('students', $col)) {
                    $classes = DB::table('students')->select($col)->distinct()->orderBy($col)->pluck($col)->toArray();
                    break;
                }
            }
        }

        // Normalize classes: trim whitespace, remove empty and duplicate values
        if (! empty($classes) && is_array($classes)) {
            $classes = array_filter(array_map(function($v) {
                return is_string($v) ? trim($v) : $v;
            }, $classes));
            $classes = array_values(array_unique($classes));
        }

        $students = collect();
        $classStats = null;

        if ($selectedClass) {
            if (Schema::hasTable('classrooms')) {
                $classroom = DB::table('classrooms')->where('name', $selectedClass)->first();
                if ($classroom) {
                    if (Schema::hasColumn('students', 'classroom_id')) {
                        $students = DB::table('students')->where('classroom_id', $classroom->id)->get();
                    } else {
                        $students = DB::table('students')->where('classroom', $selectedClass)->get();
                    }
                }
            } else {
                $possible = ['class','class_level','level','form','grade','class_name','group','classroom'];
                $classCol = null;
                foreach ($possible as $col) { if (Schema::hasColumn('students', $col)) { $classCol = $col; break; } }
                if ($classCol) {
                    $students = DB::table('students')->where($classCol, $selectedClass)->get();
                }
            }

            $userIds = [];
            foreach ($students as $s) {
                if (isset($s->user_id)) $userIds[] = $s->user_id;
            }

            $avgScore = 'N/A';
            if (!empty($userIds) && Schema::hasTable('quiz_attempts')) {
                $avg = DB::table('quiz_attempts')->whereIn('student_id', $userIds)->avg('score');
                $avgScore = $avg ? round($avg, 0) : 'N/A';
            }

            $classStats = [
                'student_count' => count($students),
                'avg_score' => $avgScore,
                'weakest_subject' => 'N/A'
            ];
        }

        $studentsForView = collect();
        foreach ($students as $s) {
            $name = $s->name ?? null;
            if (!$name && isset($s->user_id) && Schema::hasTable('users')) {
                $u = DB::table('users')->where('id', $s->user_id)->first();
                $name = $u->name ?? null;
            }
            $studentsForView->push((object)['id' => $s->id, 'name' => $name ?? 'N/A']);
        }

        // If AJAX request (X-Requested-With), return only the panel partial HTML as JSON
        if ($request->ajax()) {
            $html = view('reports.partials.class_panel', [
                'selectedClass' => $selectedClass,
                'students' => $studentsForView,
                'classStats' => $classStats
            ])->render();

            return response()->json(['html' => $html]);
        }

        // otherwise return full page
        return view('reports.class', [
            'classes' => $classes,
            'selectedClass' => $selectedClass,
            'students' => $studentsForView,
            'classStats' => $classStats
        ]);
    }

    /**
     * Export class CSV - simple stream implementation
     */
    public function exportClassCsv($class)
    {
        $students = collect();
        if (Schema::hasTable('classrooms')) {
            $classroom = DB::table('classrooms')->where('name', $class)->first();
            if ($classroom) {
                $students = DB::table('students')->where('classroom_id', $classroom->id)->get();
            }
        } elseif (Schema::hasTable('students')) {
            $possible = ['class','class_level','level','form','grade','class_name','group','classroom'];
            $classCol = null;
            foreach ($possible as $col) { if (Schema::hasColumn('students', $col)) { $classCol = $col; break; } }
            if ($classCol) $students = DB::table('students')->where($classCol, $class)->get();
        }

        $filename = 'class_' . Str::slug($class) . '_report.csv';
        $response = new StreamedResponse(function() use ($students) {
            $out = fopen('php://output','w');
            // header
            fputcsv($out, ['Class:', $students->isEmpty() ? '' : ($students[0]->class ?? $students[0]->classroom ?? $students[0]->class_name ?? '')]);
            fputcsv($out, []);
            fputcsv($out, ['student_id','name']);
            foreach ($students as $s) {
                $name = $s->name ?? null;
                if (!$name && isset($s->user_id) && Schema::hasTable('users')) {
                    $u = DB::table('users')->where('id', $s->user_id)->first();
                    $name = $u->name ?? null;
                }
                fputcsv($out, [$s->id ?? '', $name ?? 'N/A']);
            }
            fclose($out);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}"
        ]);
        return $response;
    }

    public function exportClassPdf($class)
    {
        $students = collect();
        if (Schema::hasTable('classrooms')) {
            $classroom = DB::table('classrooms')->where('name', $class)->first();
            if ($classroom) $students = DB::table('students')->where('classroom_id', $classroom->id)->get();
        } elseif (Schema::hasTable('students')) {
            $possible = ['class','class_level','level','form','grade','class_name','group','classroom'];
            $classCol = null;
            foreach ($possible as $col) { if (Schema::hasColumn('students', $col)) { $classCol = $col; break; } }
            if ($classCol) $students = DB::table('students')->where($classCol, $class)->get();
        }

        $studentRows = $students->map(function($s){
            $name = $s->name ?? null;
            if (!$name && isset($s->user_id) && Schema::hasTable('users')) {
                $u = DB::table('users')->where('id', $s->user_id)->first();
                $name = $u->name ?? null;
            }
            return ['id' => $s->id ?? '', 'name' => $name ?? 'N/A'];
        })->toArray();

        $pdf = Pdf::loadView('reports.class_pdf', [
            'class' => $class,
            'students' => $studentRows,
            'title' => 'LAPORAN PRESTASI KELAS'
        ])->setPaper('a4','portrait');

        return $pdf->download('class_' . Str::slug($class) . '_report.pdf');
    }

    //
    // Lightweight placeholders for optional routes you kept:
    //
    public function studentsPerformance()
    {
        $students = [];
        if (Schema::hasTable('students')) {
            $students = DB::table('students')->limit(200)->get();
        }
        return view('reports.students', ['students' => $students]);
    }

    public function exportStudentsCsv()
    {
        $rows = Schema::hasTable('students') ? DB::table('students')->get() : collect();
        $filename = 'students_all.csv';
        $response = new StreamedResponse(function() use ($rows) {
            $out = fopen('php://output','w');
            fputcsv($out, ['id','name']);
            foreach ($rows as $r) {
                fputcsv($out, [$r->id ?? '', $r->name ?? 'N/A']);
            }
            fclose($out);
        }, 200, ['Content-Type'=>'text/csv','Content-Disposition'=>"attachment; filename={$filename}"]);
        return $response;
    }

    public function studentsChartData()
    {
        return response()->json([
            'labels' => [],
            'data' => []
        ]);
    }

    /**
     * API: Get statistics for dashboard
     */
    public function getStatistics(Request $request)
    {
        $selectedClass = $request->query('class', '');
        $dateRange = $request->query('range', 'month');

        // Calculate date range
        $fromDate = now()->startOfDay();
        switch ($dateRange) {
            case 'week':
                $fromDate = now()->subDays(7)->startOfDay();
                break;
            case 'quarter':
                $fromDate = now()->subMonths(3)->startOfDay();
                break;
            case 'all':
                $fromDate = now()->subYears(10)->startOfDay();
                break;
            case 'month':
            default:
                $fromDate = now()->subMonth()->startOfDay();
        }

        $attempts = collect();
        if (Schema::hasTable('quiz_attempts')) {
            $query = DB::table('quiz_attempts as qa')
                ->leftJoin('quizzes as q', 'qa.quiz_id', '=', 'q.id');
            
            if ($selectedClass && $selectedClass !== '') {
                // Filter by class if specified
                if (Schema::hasTable('classrooms') && Schema::hasColumn('students', 'classroom_id')) {
                    $classroom = DB::table('classrooms')->where('name', $selectedClass)->first();
                    if ($classroom) {
                        $studentIds = DB::table('students')->where('classroom_id', $classroom->id)->pluck('user_id');
                        $query = $query->whereIn('qa.student_id', $studentIds);
                    }
                } elseif (Schema::hasTable('students')) {
                    $possible = ['class','class_level','level','form','grade','class_name','group','classroom'];
                    foreach ($possible as $col) {
                        if (Schema::hasColumn('students', $col)) {
                            $studentIds = DB::table('students')->where($col, $selectedClass)->pluck('user_id');
                            $query = $query->whereIn('qa.student_id', $studentIds);
                            break;
                        }
                    }
                }
            }

            $attempts = $query->where('qa.created_at', '>=', $fromDate)
                ->select('qa.created_at', 'qa.score', 'q.title', 'qa.student_id')
                ->get();
        }

        // Calculate statistics
        $scores = $attempts->pluck('score')->filter(function($v) { return is_numeric($v); })->map(function($v) { return (float)$v; });
        $avgScore = $scores->count() > 0 ? round($scores->avg(), 1) : 0;
        $totalAttempts = $attempts->count();
        $activeStudents = $attempts->pluck('student_id')->unique()->count();
        $successRate = $scores->count() > 0 ? round(($scores->filter(function($v) { return $v >= 70; })->count() / $scores->count()) * 100) : 0;

        // Topic performance
        $topicData = $attempts->groupBy('title')
            ->map(function($group) {
                $scores = $group->pluck('score')->filter(function($v) { return is_numeric($v); });
                return [
                    'label' => $group->first()->title ?? 'Unknown',
                    'score' => $scores->count() > 0 ? round($scores->avg(), 1) : 0
                ];
            })
            ->sortByDesc('score')
            ->take(10)
            ->values();

        // Trend data (by week)
        $trendData = $attempts->groupBy(function($item) {
            return $item->created_at ? date('Y-m-d', strtotime($item->created_at)) : 'Unknown';
        })
        ->map(function($group) {
            $scores = $group->pluck('score')->filter(function($v) { return is_numeric($v); });
            return round($scores->count() > 0 ? $scores->avg() : 0, 1);
        })
        ->sort()
        ->values();

        $trendDates = $attempts->groupBy(function($item) {
            return $item->created_at ? date('Y-m-d', strtotime($item->created_at)) : 'Unknown';
        })
        ->keys()
        ->sort()
        ->values();

        // Class comparison
        $classStats = [];
        $classes = [];
        if (Schema::hasTable('classrooms')) {
            $classes = DB::table('classrooms')->orderBy('name')->pluck('name')->toArray();
        } elseif (Schema::hasTable('students')) {
            $possible = ['class','class_level','level','form','grade','class_name','group','classroom'];
            foreach ($possible as $col) {
                if (Schema::hasColumn('students', $col)) {
                    $classes = DB::table('students')->select($col)->distinct()->orderBy($col)->pluck($col)->toArray();
                    break;
                }
            }
        }

        foreach ($classes as $cls) {
            $classAttempts = $attempts;
            if (Schema::hasTable('classrooms') && Schema::hasColumn('students', 'classroom_id')) {
                $classroom = DB::table('classrooms')->where('name', $cls)->first();
                if ($classroom) {
                    $studentIds = DB::table('students')->where('classroom_id', $classroom->id)->pluck('user_id');
                    $classAttempts = $attempts->whereIn('student_id', $studentIds->toArray());
                }
            }

            $classScores = $classAttempts->pluck('score')->filter(function($v) { return is_numeric($v); })->map(function($v) { return (float)$v; });
            
            if ($classScores->count() > 0) {
                $classStats[] = [
                    'name' => $cls,
                    'avgScore' => round($classScores->avg(), 1),
                    'maxScore' => $classScores->max(),
                    'minScore' => $classScores->min(),
                    'studentCount' => $classAttempts->pluck('student_id')->unique()->count()
                ];
            }
        }

        return response()->json([
            'avgScore' => $avgScore,
            'totalAttempts' => $totalAttempts,
            'activeStudents' => $activeStudents,
            'successRate' => $successRate,
            'topicData' => [
                'labels' => $topicData->pluck('label')->toArray(),
                'scores' => $topicData->pluck('score')->toArray()
            ],
            'trendData' => [
                'dates' => $trendDates->toArray(),
                'scores' => $trendData->toArray()
            ],
            'classStats' => $classStats
        ]);
    }

    /**
     * Export statistics as PDF
     */
    public function exportStatistics(Request $request)
    {
        $selectedClass = $request->query('class', 'semua');
        $dateRange = $request->query('range', 'month');

        $data = json_decode(json_encode([
            'class' => $selectedClass,
            'range' => $dateRange,
            'generatedAt' => now()->format('Y-m-d H:i:s')
        ]));

        $pdf = Pdf::loadView('reports.statistics_pdf', $data)->setPaper('a4', 'landscape');
        return $pdf->download('statistik_prestasi_' . now()->format('Y-m-d') . '.pdf');
    }
}
