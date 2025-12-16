<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Insert quizzes
$quizzes = [
    [
        'id' => 1,
        'user_id' => 1,
        'teacher_id' => 3,
        'title' => 'Java: Asas Sintaks dan Jenis Data',
        'description' => 'Kuiz ini menguji pengetahuan asas tentang sintaks Java, pemboleh ubah, dan jenis data primitif.',
        'max_attempts' => 5,
        'due_at' => '2026-01-03 20:57:04',
        'is_published' => 1,
        'unique_code' => '1yCEBbtZ',
        'created_at' => '2025-12-04 20:57:04',
        'updated_at' => '2025-12-04 20:57:04',
    ],
    [
        'id' => 2,
        'user_id' => 1,
        'teacher_id' => 3,
        'title' => 'Java: Konsep Pengaturcaraan Berorientasi Objek (OOP)',
        'description' => 'Kuiz ini menguji pemahaman anda tentang Kelas, Objek, Pewarisan, dan Polimorfisme.',
        'max_attempts' => 3,
        'due_at' => '2026-01-03 20:57:04',
        'is_published' => 1,
        'unique_code' => 'TkJeAnU3',
        'created_at' => '2025-12-04 20:57:04',
        'updated_at' => '2025-12-04 20:57:04',
    ],
    [
        'id' => 3,
        'user_id' => 1,
        'teacher_id' => 3,
        'title' => 'Java: Struktur Kawalan dan Titasusunan (Array)',
        'description' => 'Kuiz mengenai pernyataan if-else, loops, dan manipulasi tatasusunan (arrays).',
        'max_attempts' => 5,
        'due_at' => '2026-01-03 20:57:04',
        'is_published' => 1,
        'unique_code' => 't3glSyx0',
        'created_at' => '2025-12-04 20:57:04',
        'updated_at' => '2025-12-04 20:57:04',
    ],
    [
        'id' => 4,
        'user_id' => 1,
        'teacher_id' => 3,
        'title' => 'Tingkatan 5, Bab 1: Etika Komputer dan Undang-undang Siber',
        'description' => 'Kuiz ini merangkumi konsep etika dalam pengkomputeran, hak cipta, dan undang-undang siber.',
        'max_attempts' => 2,
        'due_at' => '2026-01-18 20:57:04',
        'is_published' => 1,
        'unique_code' => '0gPSoeEZ',
        'created_at' => '2025-12-04 20:57:04',
        'updated_at' => '2025-12-04 20:57:04',
    ],
    [
        'id' => 5,
        'user_id' => 1,
        'teacher_id' => 3,
        'title' => 'Tingkatan 5, Bab 1: Pembangunan Aplikasi Laman Web (Asas)',
        'description' => 'Kuiz ini menguji asas pembangunan aplikasi web termasuk bahasa skrip.',
        'max_attempts' => 4,
        'due_at' => '2026-01-18 20:57:04',
        'is_published' => 1,
        'unique_code' => 'xOTNqoKo',
        'created_at' => '2025-12-04 20:57:04',
        'updated_at' => '2025-12-04 20:57:04',
    ],
];

DB::table('quizzes')->insertOrIgnore($quizzes);

echo "✓ Inserted " . count($quizzes) . " quizzes\n";

// Check result
$count = DB::table('quizzes')->count();
echo "Total quizzes now: $count\n";
