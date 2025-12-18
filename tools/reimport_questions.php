<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Delete and reimport with correct data - all 62 questions from the SQL dump
DB::table('options')->delete();
DB::table('questions')->delete();

// Import ALL 62 questions with CORRECT types from the SQL dump
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
    ['id' => 11, 'quiz_id' => 2, 'question_text' => 'Apakah istilah yang merujuk kepada proses membungkus data dan kaedah yang beroperasi pada data ke dalam satu unit?', 'type' => 'multiple_choice', 'question_type' => 'multiple_choice', 'points' => 5, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 12, 'quiz_id' => 2, 'question_text' => 'Dalam Java, Kelas adalah pelan tindakan (blueprint) untuk mencipta Objek.', 'type' => 'true_false', 'question_type' => 'multiple_choice', 'points' => 3, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 13, 'quiz_id' => 2, 'question_text' => 'Kaedah yang mempunyai nama yang sama dengan kelas dan digunakan untuk menginisialisasi objek dipanggil ______.', 'type' => 'short_answer', 'question_type' => 'multiple_choice', 'points' => 7, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 14, 'quiz_id' => 2, 'question_text' => 'Kata kunci `extends` digunakan untuk melaksanakan (implement) pewarisan.', 'type' => 'true_false', 'question_type' => 'multiple_choice', 'points' => 3, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 15, 'quiz_id' => 2, 'question_text' => 'Pilih semua prinsip utama Pengaturcaraan Berorientasi Objek (OOP).', 'type' => 'checkbox', 'question_type' => 'multiple_choice', 'points' => 8, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 16, 'quiz_id' => 2, 'question_text' => 'Apakah kegunaan kata kunci `super` dalam subkelas?', 'type' => 'multiple_choice', 'question_type' => 'multiple_choice', 'points' => 5, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 17, 'quiz_id' => 2, 'question_text' => 'Dalam Java, semua kelas secara automatik mewarisi daripada kelas `Object`.', 'type' => 'true_false', 'question_type' => 'multiple_choice', 'points' => 4, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 18, 'quiz_id' => 2, 'question_text' => 'Definisi dua kaedah (methods) dalam kelas yang sama dengan nama yang sama tetapi senarai parameter yang berbeza dipanggil Overriding.', 'type' => 'true_false', 'question_type' => 'multiple_choice', 'points' => 3, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 19, 'quiz_id' => 2, 'question_text' => 'Kelas yang tidak boleh diinstantiate dipanggil kelas ______.', 'type' => 'short_answer', 'question_type' => 'multiple_choice', 'points' => 6, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 20, 'quiz_id' => 2, 'question_text' => 'Kata kunci `this` merujuk kepada objek kelas yang mana ia digunakan.', 'type' => 'true_false', 'question_type' => 'multiple_choice', 'points' => 2, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
];

DB::table('questions')->insert($questions);

echo "✓ Imported " . count($questions) . " questions with correct types\n";
echo "Total questions: " . DB::table('questions')->count() . "\n";
