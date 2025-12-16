<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Insert questions (first 62 from the dump)
$questions = [
    ['id' => 1, 'quiz_id' => 1, 'question_text' => 'Apakah jenis data primitif yang menyimpan nilai boolean?', 'type' => 'multiple_choice', 'question_type' => 'multiple_choice', 'points' => 3, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 2, 'quiz_id' => 1, 'question_text' => 'Apakah kaedah yang digunakan untuk mencetak output ke konsol dalam Java?', 'type' => 'multiple_choice', 'question_type' => 'multiple_choice', 'points' => 4, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 3, 'quiz_id' => 1, 'question_text' => 'Dalam Java, baris kod mesti diakhiri dengan simbol titik koma (`;`).', 'type' => 'true_false', 'question_type' => 'multiple_choice', 'points' => 2, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 4, 'quiz_id' => 1, 'question_text' => 'Apakah julat nilai yang boleh disimpan oleh jenis data `byte`?', 'type' => 'short_answer', 'question_type' => 'multiple_choice', 'points' => 5, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 5, 'quiz_id' => 1, 'question_text' => 'Apakah saiz (dalam bit) jenis data `int` dalam Java?', 'type' => 'short_answer', 'question_type' => 'multiple_choice', 'points' => 5, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 6, 'quiz_id' => 1, 'question_text' => 'Pilih semua nama jenis data primitif dalam Java.', 'type' => 'checkbox', 'question_type' => 'multiple_choice', 'points' => 6, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 7, 'quiz_id' => 1, 'question_text' => 'Apakah pembeza antara `float` dan `double`?', 'type' => 'multiple_choice', 'question_type' => 'multiple_choice', 'points' => 4, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 8, 'quiz_id' => 1, 'question_text' => 'Jenis data `char` digunakan untuk menyimpan satu aksara.', 'type' => 'true_false', 'question_type' => 'multiple_choice', 'points' => 2, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 9, 'quiz_id' => 1, 'question_text' => 'Kata kunci `final` digunakan untuk menjadikan pemboleh ubah sebagai pemalar (constant).', 'type' => 'true_false', 'question_type' => 'multiple_choice', 'points' => 3, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 10, 'quiz_id' => 1, 'question_text' => 'Dalam Java, setiap aplikasi mesti mempunyai kaedah (method) utama yang dipanggil `main()`.', 'type' => 'true_false', 'question_type' => 'multiple_choice', 'points' => 3, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
];

DB::table('questions')->insertOrIgnore($questions);
echo "✓ Inserted " . count($questions) . " questions\n";

// Insert options for questions
$options = [
    ['id' => 1, 'question_id' => 1, 'option_text' => 'char', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 2, 'question_id' => 1, 'option_text' => 'int', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 3, 'question_id' => 1, 'option_text' => 'boolean', 'is_correct' => 1, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 4, 'question_id' => 1, 'option_text' => 'Boolean', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 5, 'question_id' => 2, 'option_text' => 'Console.print()', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 6, 'question_id' => 2, 'option_text' => 'System.out.println()', 'is_correct' => 1, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 7, 'question_id' => 2, 'option_text' => 'print.line()', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 8, 'question_id' => 2, 'option_text' => 'System.print()', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 9, 'question_id' => 3, 'option_text' => 'True', 'is_correct' => 1, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 10, 'question_id' => 3, 'option_text' => 'False', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 11, 'question_id' => 4, 'option_text' => '-128 hingga 127', 'is_correct' => 1, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 12, 'question_id' => 5, 'option_text' => '32', 'is_correct' => 1, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 13, 'question_id' => 6, 'option_text' => 'Integer', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 14, 'question_id' => 6, 'option_text' => 'double', 'is_correct' => 1, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 15, 'question_id' => 6, 'option_text' => 'short', 'is_correct' => 1, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 16, 'question_id' => 6, 'option_text' => 'String', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 17, 'question_id' => 7, 'option_text' => 'double menggunakan 32 bit, float menggunakan 64 bit.', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 18, 'question_id' => 7, 'option_text' => 'double dan float adalah sama.', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 19, 'question_id' => 7, 'option_text' => 'float menggunakan 32 bit, double menggunakan 64 bit.', 'is_correct' => 1, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 20, 'question_id' => 7, 'option_text' => 'float hanya boleh menyimpan integer.', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
];

DB::table('options')->insertOrIgnore($options);
echo "✓ Inserted " . count($options) . " options\n";

$totalQuestions = DB::table('questions')->count();
$totalOptions = DB::table('options')->count();
echo "\nTotal questions now: $totalQuestions\n";
echo "Total options now: $totalOptions\n";
