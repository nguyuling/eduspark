<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Add missing options for questions 8, 9, 10
$options = [
    // Question 8: Jenis data `char` digunakan untuk menyimpan satu aksara.
    ['id' => 21, 'question_id' => 8, 'option_text' => 'True', 'is_correct' => 1, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 22, 'question_id' => 8, 'option_text' => 'False', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    
    // Question 9: Kata kunci `final` digunakan untuk menjadikan pemboleh ubah sebagai pemalar (constant).
    ['id' => 23, 'question_id' => 9, 'option_text' => 'True', 'is_correct' => 1, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 24, 'question_id' => 9, 'option_text' => 'False', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    
    // Question 10: Dalam Java, setiap aplikasi mesti mempunyai kaedah (method) utama yang dipanggil `main()`.
    ['id' => 25, 'question_id' => 10, 'option_text' => 'True', 'is_correct' => 1, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
    ['id' => 26, 'question_id' => 10, 'option_text' => 'False', 'is_correct' => 0, 'sort_order' => null, 'created_at' => '2025-12-04 20:57:04', 'updated_at' => '2025-12-04 20:57:04'],
];

DB::table('options')->insertOrIgnore($options);

echo "✓ Inserted " . count($options) . " missing options\n";
echo "Total options now: " . DB::table('options')->count() . "\n";
