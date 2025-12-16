<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Import ALL options from the SQL dump
$options = [
    // Q1 options
    ['id' => 1, 'question_id' => 1, 'option_text' => 'char', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 2, 'question_id' => 1, 'option_text' => 'int', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 3, 'question_id' => 1, 'option_text' => 'boolean', 'is_correct' => 1, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 4, 'question_id' => 1, 'option_text' => 'Boolean', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    // Q2 options
    ['id' => 5, 'question_id' => 2, 'option_text' => 'Console.print()', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 6, 'question_id' => 2, 'option_text' => 'System.out.println()', 'is_correct' => 1, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 7, 'question_id' => 2, 'option_text' => 'print.line()', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 8, 'question_id' => 2, 'option_text' => 'System.print()', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    // Q3 options
    ['id' => 9, 'question_id' => 3, 'option_text' => 'True', 'is_correct' => 1, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 10, 'question_id' => 3, 'option_text' => 'False', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    // Q6 options
    ['id' => 13, 'question_id' => 6, 'option_text' => 'Integer', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 14, 'question_id' => 6, 'option_text' => 'double', 'is_correct' => 1, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 15, 'question_id' => 6, 'option_text' => 'short', 'is_correct' => 1, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 16, 'question_id' => 6, 'option_text' => 'String', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    // Q7 options
    ['id' => 17, 'question_id' => 7, 'option_text' => 'double menggunakan 32 bit, float menggunakan 64 bit.', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 18, 'question_id' => 7, 'option_text' => 'double dan float adalah sama.', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 19, 'question_id' => 7, 'option_text' => 'float menggunakan 32 bit, double menggunakan 64 bit.', 'is_correct' => 1, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 20, 'question_id' => 7, 'option_text' => 'float hanya boleh menyimpan integer.', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    // Q8 options
    ['id' => 21, 'question_id' => 8, 'option_text' => 'True', 'is_correct' => 1, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 22, 'question_id' => 8, 'option_text' => 'False', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    // Q9 options
    ['id' => 23, 'question_id' => 9, 'option_text' => 'True', 'is_correct' => 1, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 24, 'question_id' => 9, 'option_text' => 'False', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    // Q10 options
    ['id' => 25, 'question_id' => 10, 'option_text' => 'True', 'is_correct' => 1, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 26, 'question_id' => 10, 'option_text' => 'False', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    // Q11 options
    ['id' => 27, 'question_id' => 11, 'option_text' => 'Pewarisan (Inheritance)', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 28, 'question_id' => 11, 'option_text' => 'Pengkapsulan (Encapsulation)', 'is_correct' => 1, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 29, 'question_id' => 11, 'option_text' => 'Polimorfisme (Polymorphism)', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 30, 'question_id' => 11, 'option_text' => 'Pengabstrakan (Abstraction)', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    // Q12 options
    ['id' => 31, 'question_id' => 12, 'option_text' => 'True', 'is_correct' => 1, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 32, 'question_id' => 12, 'option_text' => 'False', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    // Q14 options
    ['id' => 34, 'question_id' => 14, 'option_text' => 'True', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 35, 'question_id' => 14, 'option_text' => 'False', 'is_correct' => 1, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    // Q15 options
    ['id' => 36, 'question_id' => 15, 'option_text' => 'Pewarisan (Inheritance)', 'is_correct' => 1, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 37, 'question_id' => 15, 'option_text' => 'Pengkapsulan (Encapsulation)', 'is_correct' => 1, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 38, 'question_id' => 15, 'option_text' => 'Struktur (Structure)', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 39, 'question_id' => 15, 'option_text' => 'Polimorfisme (Polymorphism)', 'is_correct' => 1, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    // Q16 options
    ['id' => 40, 'question_id' => 16, 'option_text' => 'Merujuk kepada objek semasa.', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 41, 'question_id' => 16, 'option_text' => 'Memanggil pembina (constructor) kelas induk.', 'is_correct' => 1, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 42, 'question_id' => 16, 'option_text' => 'Menamatkan pelaksanaan program.', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 43, 'question_id' => 16, 'option_text' => 'Membuat objek baharu.', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    // Q17 options
    ['id' => 44, 'question_id' => 17, 'option_text' => 'True', 'is_correct' => 1, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 45, 'question_id' => 17, 'option_text' => 'False', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    // Q18 options
    ['id' => 46, 'question_id' => 18, 'option_text' => 'True', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 47, 'question_id' => 18, 'option_text' => 'False', 'is_correct' => 1, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    // Q20 options
    ['id' => 55, 'question_id' => 20, 'option_text' => 'True', 'is_correct' => 1, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 56, 'question_id' => 20, 'option_text' => 'False', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
];

DB::table('options')->insert($options);

echo "✓ Imported " . count($options) . " options\n";
echo "Total options: " . DB::table('options')->count() . "\n";
