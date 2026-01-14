<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Import 62 quiz questions for first 5 quizzes and set their max_points
     */
    public function up(): void
    {
        // Only run if questions table is empty
        if (DB::table('questions')->count() === 0) {
            $questions = [
                // Quiz 1: Java Basic Syntax
                ['id' => 1, 'quiz_id' => 1, 'teacher_id' => 1, 'question_text' => 'Apakah jenis data primitif yang menyimpan nilai boolean?', 'type' => 'multiple_choice', 'points' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 2, 'quiz_id' => 1, 'teacher_id' => 1, 'question_text' => 'Apakah kaedah yang digunakan untuk mencetak output ke konsol dalam Java?', 'type' => 'multiple_choice', 'points' => 4, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 3, 'quiz_id' => 1, 'teacher_id' => 1, 'question_text' => 'Dalam Java, baris kod mesti diakhiri dengan simbol titik koma (`;`).', 'type' => 'true_false', 'points' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 4, 'quiz_id' => 1, 'teacher_id' => 1, 'question_text' => 'Apakah julat nilai yang boleh disimpan oleh jenis data `byte`?', 'type' => 'short_answer', 'points' => 5, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 5, 'quiz_id' => 1, 'teacher_id' => 1, 'question_text' => 'Apakah saiz (dalam bit) jenis data `int` dalam Java?', 'type' => 'short_answer', 'points' => 5, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 6, 'quiz_id' => 1, 'teacher_id' => 1, 'question_text' => 'Pilih semua nama jenis data primitif dalam Java.', 'type' => 'checkbox', 'points' => 6, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 7, 'quiz_id' => 1, 'teacher_id' => 1, 'question_text' => 'Apakah pembeza antara `float` dan `double`?', 'type' => 'multiple_choice', 'points' => 4, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 8, 'quiz_id' => 1, 'teacher_id' => 1, 'question_text' => 'Jenis data `char` digunakan untuk menyimpan satu aksara.', 'type' => 'true_false', 'points' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 9, 'quiz_id' => 1, 'teacher_id' => 1, 'question_text' => 'Kata kunci `final` digunakan untuk menjadikan pemboleh ubah sebagai pemalar (constant).', 'type' => 'true_false', 'points' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 10, 'quiz_id' => 1, 'teacher_id' => 1, 'question_text' => 'Dalam Java, setiap aplikasi mesti mempunyai kaedah (method) utama yang dipanggil `main()`.', 'type' => 'true_false', 'points' => 3, 'created_at' => now(), 'updated_at' => now()],
                
                // Quiz 2: OOP
                ['id' => 11, 'quiz_id' => 2, 'teacher_id' => 1, 'question_text' => 'Apakah istilah yang merujuk kepada proses membungkus data dan kaedah yang beroperasi pada data ke dalam satu unit?', 'type' => 'multiple_choice', 'points' => 5, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 12, 'quiz_id' => 2, 'teacher_id' => 1, 'question_text' => 'Dalam Java, Kelas adalah pelan tindakan (blueprint) untuk mencipta Objek.', 'type' => 'true_false', 'points' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 13, 'quiz_id' => 2, 'teacher_id' => 1, 'question_text' => 'Kaedah yang mempunyai nama yang sama dengan kelas dan digunakan untuk menginisialisasi objek dipanggil ______.', 'type' => 'short_answer', 'points' => 7, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 14, 'quiz_id' => 2, 'teacher_id' => 1, 'question_text' => 'Kata kunci `extends` digunakan untuk melaksanakan (implement) pewarisan.', 'type' => 'true_false', 'points' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 15, 'quiz_id' => 2, 'teacher_id' => 1, 'question_text' => 'Pilih semua prinsip utama Pengaturcaraan Berorientasi Objek (OOP).', 'type' => 'checkbox', 'points' => 8, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 16, 'quiz_id' => 2, 'teacher_id' => 1, 'question_text' => 'Apakah kegunaan kata kunci `super` dalam subkelas?', 'type' => 'multiple_choice', 'points' => 5, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 17, 'quiz_id' => 2, 'teacher_id' => 1, 'question_text' => 'Dalam Java, semua kelas secara automatik mewarisi daripada kelas `Object`.', 'type' => 'true_false', 'points' => 4, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 18, 'quiz_id' => 2, 'teacher_id' => 1, 'question_text' => 'Definisi dua kaedah (methods) dalam kelas yang sama dengan nama yang sama tetapi senarai parameter yang berbeza dipanggil Overriding.', 'type' => 'true_false', 'points' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 19, 'quiz_id' => 2, 'teacher_id' => 1, 'question_text' => 'Kelas yang tidak boleh diinstantiate dipanggil kelas ______.', 'type' => 'short_answer', 'points' => 6, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 20, 'quiz_id' => 2, 'teacher_id' => 1, 'question_text' => 'Kata kunci `this` merujuk kepada objek kelas yang mana ia digunakan.', 'type' => 'true_false', 'points' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 21, 'quiz_id' => 2, 'teacher_id' => 1, 'question_text' => 'Pilih mana-mana pengubah suai akses (access modifier) dalam Java.', 'type' => 'checkbox', 'points' => 6, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 22, 'quiz_id' => 2, 'teacher_id' => 1, 'question_text' => 'Polimorfisme membolehkan objek mengambil pelbagai bentuk.', 'type' => 'true_false', 'points' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 23, 'quiz_id' => 2, 'teacher_id' => 1, 'question_text' => 'Kelas induk juga dikenali sebagai kelas super.', 'type' => 'true_false', 'points' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 24, 'quiz_id' => 2, 'teacher_id' => 1, 'question_text' => 'Antara muka (Interface) boleh mengandungi pembina (constructor).', 'type' => 'true_false', 'points' => 4, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 25, 'quiz_id' => 2, 'teacher_id' => 1, 'question_text' => 'Pewarisan digunakan untuk mencapai penggunaan semula kod.', 'type' => 'true_false', 'points' => 3, 'created_at' => now(), 'updated_at' => now()],
                
                // Quiz 3: Control Structures and Arrays
                ['id' => 26, 'quiz_id' => 3, 'teacher_id' => 1, 'question_text' => 'Yang manakah antara berikut BUKAN gelung (loop) yang sah dalam Java?', 'type' => 'multiple_choice', 'points' => 4, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 27, 'quiz_id' => 3, 'teacher_id' => 1, 'question_text' => 'Pernyataan `switch` mesti diakhiri dengan pernyataan `break`.', 'type' => 'true_false', 'points' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 28, 'quiz_id' => 3, 'teacher_id' => 1, 'question_text' => 'Kaedah `length` digunakan untuk mendapatkan saiz tatasusunan (array) dalam Java.', 'type' => 'true_false', 'points' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 29, 'quiz_id' => 3, 'teacher_id' => 1, 'question_text' => 'Indeks pertama dalam tatasusunan Java ialah ______.', 'type' => 'short_answer', 'points' => 5, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 30, 'quiz_id' => 3, 'teacher_id' => 1, 'question_text' => 'Pilih pernyataan yang boleh digunakan untuk melangkau lelaran gelung semasa (skip the current loop iteration).', 'type' => 'multiple_choice', 'points' => 4, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 31, 'quiz_id' => 3, 'teacher_id' => 1, 'question_text' => 'Pernyataan `if-else` digunakan untuk pelaksanaan kod bercabang.', 'type' => 'true_false', 'points' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 32, 'quiz_id' => 3, 'teacher_id' => 1, 'question_text' => 'Tatasusunan pelbagai dimensi boleh menyimpan data dalam format jadual (rows and columns).', 'type' => 'true_false', 'points' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 33, 'quiz_id' => 3, 'teacher_id' => 1, 'question_text' => 'Pilih mana-mana operator logik (logical operator) dalam Java.', 'type' => 'checkbox', 'points' => 6, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 34, 'quiz_id' => 3, 'teacher_id' => 1, 'question_text' => 'Gelung `do-while` sentiasa melaksanakan badannya sekurang-kurangnya sekali.', 'type' => 'true_false', 'points' => 4, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 35, 'quiz_id' => 3, 'teacher_id' => 1, 'question_text' => 'Pernyataan `break` menghentikan pelaksanaan gelung sepenuhnya.', 'type' => 'true_false', 'points' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 36, 'quiz_id' => 3, 'teacher_id' => 1, 'question_text' => 'Apakah gelung yang sesuai digunakan apabila bilangan lelaran diketahui?', 'type' => 'multiple_choice', 'points' => 4, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 37, 'quiz_id' => 3, 'teacher_id' => 1, 'question_text' => 'Setiap tatasusunan (array) dalam Java adalah satu objek.', 'type' => 'true_false', 'points' => 2, 'created_at' => now(), 'updated_at' => now()],
                
                // Quiz 4: Computer Ethics and Cybersecurity
                ['id' => 38, 'quiz_id' => 4, 'teacher_id' => 1, 'question_text' => 'Pilih mana-mana contoh pelanggaran etika komputer.', 'type' => 'checkbox', 'points' => 6, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 39, 'quiz_id' => 4, 'teacher_id' => 1, 'question_text' => 'Apakah yang dimaksudkan dengan "Phishing"?', 'type' => 'multiple_choice', 'points' => 5, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 40, 'quiz_id' => 4, 'teacher_id' => 1, 'question_text' => 'Undang-undang Hak Cipta melindungi idea, dan bukannya ekspresi idea.', 'type' => 'true_false', 'points' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 41, 'quiz_id' => 4, 'teacher_id' => 1, 'question_text' => 'Memberi atribusi yang betul apabila menggunakan bahan orang lain adalah amalan etika.', 'type' => 'true_false', 'points' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 42, 'quiz_id' => 4, 'teacher_id' => 1, 'question_text' => 'Penyalahgunaan data peribadi tanpa kebenaran adalah melanggar Akta Perlindungan Data Peribadi.', 'type' => 'true_false', 'points' => 4, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 43, 'quiz_id' => 4, 'teacher_id' => 1, 'question_text' => 'Jenis perisian yang dibenarkan untuk digunakan dan diedarkan secara percuma oleh pencipta asalnya dipanggil perisian ______.', 'type' => 'short_answer', 'points' => 6, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 44, 'quiz_id' => 4, 'teacher_id' => 1, 'question_text' => 'Apakah istilah yang digunakan untuk merujuk kepada perisian yang direka untuk menyebabkan kerosakan pada sistem komputer?', 'type' => 'short_answer', 'points' => 5, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 45, 'quiz_id' => 4, 'teacher_id' => 1, 'question_text' => 'Hacker yang melanggar undang-undang siber dipanggil Black Hat Hacker.', 'type' => 'true_false', 'points' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 46, 'quiz_id' => 4, 'teacher_id' => 1, 'question_text' => 'Pilih undang-undang siber Malaysia yang berkaitan dengan penyalahgunaan komputer.', 'type' => 'checkbox', 'points' => 8, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 47, 'quiz_id' => 4, 'teacher_id' => 1, 'question_text' => 'Melakukan pemindahan data peribadi tanpa pengetahuan pengguna adalah etika yang baik.', 'type' => 'true_false', 'points' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 48, 'quiz_id' => 4, 'teacher_id' => 1, 'question_text' => 'Akta Hak Cipta melindungi nama perniagaan atau jenama produk.', 'type' => 'true_false', 'points' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 49, 'quiz_id' => 4, 'teacher_id' => 1, 'question_text' => 'Jenayah siber adalah jenayah yang dilakukan menggunakan komputer dan internet.', 'type' => 'true_false', 'points' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 50, 'quiz_id' => 4, 'teacher_id' => 1, 'question_text' => 'Deface laman web adalah satu bentuk jenayah siber.', 'type' => 'true_false', 'points' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 51, 'quiz_id' => 4, 'teacher_id' => 1, 'question_text' => 'Penggunaan sumber komputasi yang berlebihan tanpa kebenaran adalah satu isu etika.', 'type' => 'true_false', 'points' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 52, 'quiz_id' => 4, 'teacher_id' => 1, 'question_text' => 'Apakah yang dimaksudkan dengan "hak digital"?', 'type' => 'multiple_choice', 'points' => 5, 'created_at' => now(), 'updated_at' => now()],
                
                // Quiz 5: Web Development Basics
                ['id' => 53, 'quiz_id' => 5, 'teacher_id' => 1, 'question_text' => 'Apakah bahasa yang digunakan untuk strukturkan kandungan (content) laman web?', 'type' => 'multiple_choice', 'points' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 54, 'quiz_id' => 5, 'teacher_id' => 1, 'question_text' => 'Bahasa Skrip "Client-side" (contohnya JavaScript) dilaksanakan oleh pelayan (server).', 'type' => 'true_false', 'points' => 4, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 55, 'quiz_id' => 5, 'teacher_id' => 1, 'question_text' => 'Pilih mana-mana bahasa skrip "Server-side".', 'type' => 'checkbox', 'points' => 6, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 56, 'quiz_id' => 5, 'teacher_id' => 1, 'question_text' => 'CSS bertanggungjawab untuk memastikan fungsi (functionality) interaktif laman web.', 'type' => 'true_false', 'points' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 57, 'quiz_id' => 5, 'teacher_id' => 1, 'question_text' => 'Apakah istilah yang merujuk kepada proses memastikan laman web mudah digunakan oleh pengguna?', 'type' => 'short_answer', 'points' => 5, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 58, 'quiz_id' => 5, 'teacher_id' => 1, 'question_text' => 'Tag `<p>` digunakan untuk mewakili perenggan dalam HTML.', 'type' => 'true_false', 'points' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 59, 'quiz_id' => 5, 'teacher_id' => 1, 'question_text' => 'HTML adalah bahasa pengaturcaraan.', 'type' => 'true_false', 'points' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 60, 'quiz_id' => 5, 'teacher_id' => 1, 'question_text' => 'URL bermaksud Uniform Resource Locator.', 'type' => 'true_false', 'points' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 61, 'quiz_id' => 5, 'teacher_id' => 1, 'question_text' => 'Domain Name System (DNS) menukarkan nama domain kepada alamat IP.', 'type' => 'true_false', 'points' => 4, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 62, 'quiz_id' => 5, 'teacher_id' => 1, 'question_text' => 'Apakah tujuan utama bahasa skrip "Server-side"?', 'type' => 'multiple_choice', 'points' => 5, 'created_at' => now(), 'updated_at' => now()],
            ];

            DB::table('questions')->insert($questions);

            // Set max_points for each quiz
            DB::table('quizzes')->where('id', 1)->update(['max_points' => 37]);
            DB::table('quizzes')->where('id', 2)->update(['max_points' => 64]);
            DB::table('quizzes')->where('id', 3)->update(['max_points' => 43]);
            DB::table('quizzes')->where('id', 4)->update(['max_points' => 59]);
            DB::table('quizzes')->where('id', 5)->update(['max_points' => 37]);
        }
    }

    public function down(): void
    {
        // Not reversible - this is a data import migration
    }
};
