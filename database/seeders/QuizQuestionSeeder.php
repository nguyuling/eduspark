<?php

use Illuminate\Support\Carbon;

return [
    // ----------------------------------------------------------------------
    // QUIZ 1: Java - Asas Sintaks dan Jenis Data (Based on Form 4/Basic Java)
    // ----------------------------------------------------------------------
    [
        'teacher_id' => 2,
        'title' => 'Java: Asas Sintaks dan Jenis Data',
        'description' => 'Kuiz ini menguji pengetahuan asas tentang sintaks Java, pemboleh ubah, dan jenis data primitif.',
        'max_attempts' => 5,
        'due_at' => Carbon::now()->addDays(30),
        'is_published' => true,
        'questions' => [
            [
                'text' => 'Apakah jenis data primitif yang menyimpan nilai boolean?',
                'type' => 'multiple_choice', 'points' => 3,
                'options' => [
                    ['text' => 'char', 'is_correct' => false],
                    ['text' => 'int', 'is_correct' => false],
                    ['text' => 'boolean', 'is_correct' => true],
                    ['text' => 'Boolean', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Apakah kaedah yang digunakan untuk mencetak output ke konsol dalam Java?',
                'type' => 'multiple_choice', 'points' => 4,
                'options' => [
                    ['text' => 'Console.print()', 'is_correct' => false],
                    ['text' => 'System.out.println()', 'is_correct' => true],
                    ['text' => 'print.line()', 'is_correct' => false],
                    ['text' => 'System.print()', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Dalam Java, baris kod mesti diakhiri dengan simbol titik koma (`;`).',
                'type' => 'true_false', 'points' => 2,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Apakah julat nilai yang boleh disimpan oleh jenis data `byte`?',
                'type' => 'short_answer', 'points' => 5,
                'correct_text' => '-128 hingga 127',
            ],
            [
                'text' => 'Apakah saiz (dalam bit) jenis data `int` dalam Java?',
                'type' => 'short_answer', 'points' => 5,
                'correct_text' => '32',
            ],
            [
                'text' => 'Pilih semua nama jenis data primitif dalam Java.',
                'type' => 'checkbox', 'points' => 6,
                'options' => [
                    ['text' => 'Integer', 'is_correct' => false],
                    ['text' => 'double', 'is_correct' => true],
                    ['text' => 'short', 'is_correct' => true],
                    ['text' => 'String', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Apakah pembeza antara `float` dan `double`?',
                'type' => 'multiple_choice', 'points' => 4,
                'options' => [
                    ['text' => 'double menggunakan 32 bit, float menggunakan 64 bit.', 'is_correct' => false],
                    ['text' => 'double dan float adalah sama.', 'is_correct' => false],
                    ['text' => 'float menggunakan 32 bit, double menggunakan 64 bit.', 'is_correct' => true],
                    ['text' => 'float hanya boleh menyimpan integer.', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Jenis data `char` digunakan untuk menyimpan satu aksara.',
                'type' => 'true_false', 'points' => 2,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Kata kunci `final` digunakan untuk menjadikan pemboleh ubah sebagai pemalar (constant).',
                'type' => 'true_false', 'points' => 3,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Dalam Java, setiap aplikasi mesti mempunyai kaedah (method) utama yang dipanggil `main()`.',
                'type' => 'true_false', 'points' => 3,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
        ], // Total 10 Questions
    ],

    // ----------------------------------------------------------------------
    // QUIZ 2: Java - Konsep Pengaturcaraan Berorientasi Objek (OOP)
    // ----------------------------------------------------------------------
    [
        'teacher_id' => 3,
        'title' => 'Java: Konsep Pengaturcaraan Berorientasi Objek (OOP)',
        'description' => 'Kuiz ini menguji pemahaman anda tentang Kelas, Objek, Pewarisan, dan Polimorfisme.',
        'max_attempts' => 3,
        'due_at' => Carbon::now()->addDays(30),
        'is_published' => true,
        'questions' => [
            // ... (15 Questions generated for brevity, following the structure) ...
            [
                'text' => 'Apakah istilah yang merujuk kepada proses membungkus data dan kaedah yang beroperasi pada data ke dalam satu unit?',
                'type' => 'multiple_choice', 'points' => 5,
                'options' => [
                    ['text' => 'Pewarisan (Inheritance)', 'is_correct' => false],
                    ['text' => 'Pengkapsulan (Encapsulation)', 'is_correct' => true],
                    ['text' => 'Polimorfisme (Polymorphism)', 'is_correct' => false],
                    ['text' => 'Pengabstrakan (Abstraction)', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Dalam Java, Kelas adalah pelan tindakan (blueprint) untuk mencipta Objek.',
                'type' => 'true_false', 'points' => 3,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Kaedah yang mempunyai nama yang sama dengan kelas dan digunakan untuk menginisialisasi objek dipanggil ______.',
                'type' => 'short_answer', 'points' => 7,
                'correct_text' => 'Constructor',
            ],
            [
                'text' => 'Kata kunci `extends` digunakan untuk melaksanakan (implement) pewarisan.',
                'type' => 'true_false', 'points' => 3,
                'options' => [
                    ['text' => 'True', 'is_correct' => false],
                    ['text' => 'False', 'is_correct' => true], // `implements` is for interfaces, `extends` for classes
                ]
            ],
            [
                'text' => 'Pilih semua prinsip utama Pengaturcaraan Berorientasi Objek (OOP).',
                'type' => 'checkbox', 'points' => 8,
                'options' => [
                    ['text' => 'Pewarisan (Inheritance)', 'is_correct' => true],
                    ['text' => 'Pengkapsulan (Encapsulation)', 'is_correct' => true],
                    ['text' => 'Struktur (Structure)', 'is_correct' => false],
                    ['text' => 'Polimorfisme (Polymorphism)', 'is_correct' => true],
                ]
            ],
            [
                'text' => 'Apakah kegunaan kata kunci `super` dalam subkelas?',
                'type' => 'multiple_choice', 'points' => 5,
                'options' => [
                    ['text' => 'Merujuk kepada objek semasa.', 'is_correct' => false],
                    ['text' => 'Memanggil pembina (constructor) kelas induk.', 'is_correct' => true],
                    ['text' => 'Menamatkan pelaksanaan program.', 'is_correct' => false],
                    ['text' => 'Membuat objek baharu.', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Dalam Java, semua kelas secara automatik mewarisi daripada kelas `Object`.',
                'type' => 'true_false', 'points' => 4,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Definisi dua kaedah (methods) dalam kelas yang sama dengan nama yang sama tetapi senarai parameter yang berbeza dipanggil Overriding.',
                'type' => 'true_false', 'points' => 3,
                'options' => [
                    ['text' => 'True', 'is_correct' => false],
                    ['text' => 'False', 'is_correct' => true],
                ]
            ],
            [
                'text' => 'Kelas yang tidak boleh diinstantiate dipanggil kelas ______.',
                'type' => 'short_answer', 'points' => 6,
                'correct_text' => 'Abstrak',
            ],
            [
                'text' => 'Kata kunci `this` merujuk kepada objek kelas yang mana ia digunakan.',
                'type' => 'true_false', 'points' => 2,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Pilih mana-mana pengubah suai akses (access modifier) dalam Java.',
                'type' => 'checkbox', 'points' => 6,
                'options' => [
                    ['text' => 'protected', 'is_correct' => true],
                    ['text' => 'public', 'is_correct' => true],
                    ['text' => 'friend', 'is_correct' => false],
                    ['text' => 'private', 'is_correct' => true],
                ]
            ],
            [
                'text' => 'Polimorfisme membolehkan objek mengambil pelbagai bentuk.',
                'type' => 'true_false', 'points' => 3,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Kelas induk juga dikenali sebagai kelas super.',
                'type' => 'true_false', 'points' => 2,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Antara muka (Interface) boleh mengandungi pembina (constructor).',
                'type' => 'true_false', 'points' => 4,
                'options' => [
                    ['text' => 'True', 'is_correct' => false],
                    ['text' => 'False', 'is_correct' => true],
                ]
            ],
            [
                'text' => 'Pewarisan digunakan untuk mencapai penggunaan semula kod.',
                'type' => 'true_false', 'points' => 3,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
        ], // Total 15 Questions
    ],
    
    // ----------------------------------------------------------------------
    // QUIZ 3: Java - Struktur Kawalan
    // ----------------------------------------------------------------------
    [
        'teacher_id' => 4,
        'title' => 'Java: Struktur Kawalan dan Titasusunan (Array) ',
        'description' => 'Kuiz mengenai pernyataan if-else, loops, dan manipulasi tatasusunan (arrays).',
        'max_attempts' => 5,
        'due_at' => Carbon::now()->addDays(30),
        'is_published' => true,
        'questions' => [
            [
                'text' => 'Yang manakah antara berikut BUKAN gelung (loop) yang sah dalam Java?',
                'type' => 'multiple_choice', 'points' => 4,
                'options' => [
                    ['text' => 'do-while', 'is_correct' => false],
                    ['text' => 'for', 'is_correct' => false],
                    ['text' => 'until', 'is_correct' => true],
                    ['text' => 'while', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Pernyataan `switch` mesti diakhiri dengan pernyataan `break`.',
                'type' => 'true_false', 'points' => 3,
                'options' => [
                    ['text' => 'True', 'is_correct' => false],
                    ['text' => 'False', 'is_correct' => true], // break is usually used, but default is often required
                ]
            ],
            [
                'text' => 'Kaedah `length` digunakan untuk mendapatkan saiz tatasusunan (array) dalam Java.',
                'type' => 'true_false', 'points' => 3,
                'options' => [
                    ['text' => 'True', 'is_correct' => false], // It's a property: array.length
                    ['text' => 'False', 'is_correct' => true],
                ]
            ],
            [
                'text' => 'Indeks pertama dalam tatasusunan Java ialah ______.',
                'type' => 'short_answer', 'points' => 5,
                'correct_text' => '0',
            ],
            [
                'text' => 'Pilih pernyataan yang boleh digunakan untuk melangkau lelaran gelung semasa (skip the current loop iteration).',
                'type' => 'multiple_choice', 'points' => 4,
                'options' => [
                    ['text' => 'exit', 'is_correct' => false],
                    ['text' => 'break', 'is_correct' => false],
                    ['text' => 'continue', 'is_correct' => true],
                    ['text' => 'return', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Pernyataan `if-else` digunakan untuk pelaksanaan kod bercabang.',
                'type' => 'true_false', 'points' => 2,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Tatasusunan pelbagai dimensi boleh menyimpan data dalam format jadual (rows and columns).',
                'type' => 'true_false', 'points' => 3,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Pilih mana-mana operator logik (logical operator) dalam Java.',
                'type' => 'checkbox', 'points' => 6,
                'options' => [
                    ['text' => '|| (OR)', 'is_correct' => true],
                    ['text' => '&& (AND)', 'is_correct' => true],
                    ['text' => '!= (NOT EQUAL)', 'is_correct' => false],
                    ['text' => '! (NOT)', 'is_correct' => true],
                ]
            ],
            [
                'text' => 'Gelung `do-while` sentiasa melaksanakan badannya sekurang-kurangnya sekali.',
                'type' => 'true_false', 'points' => 4,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Pernyataan `break` menghentikan pelaksanaan gelung sepenuhnya.',
                'type' => 'true_false', 'points' => 3,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Apakah gelung yang sesuai digunakan apabila bilangan lelaran diketahui?',
                'type' => 'multiple_choice', 'points' => 4,
                'options' => [
                    ['text' => 'while', 'is_correct' => false],
                    ['text' => 'do-while', 'is_correct' => false],
                    ['text' => 'for', 'is_correct' => true],
                    ['text' => 'if-else', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Setiap tatasusunan (array) dalam Java adalah satu objek.',
                'type' => 'true_false', 'points' => 2,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
        ], // Total 12 Questions
    ],

    // ----------------------------------------------------------------------
    // QUIZ 4: Tingkatan 5, Bab 1 - Etika Komputer & Undang-undang Siber
    // (Assumed Chapter 1 based on snippets: Ethics, Phishing)
    // ----------------------------------------------------------------------
    [
        'teacher_id' => 5,
        'title' => 'Tingkatan 5, Bab 1: Etika Komputer dan Undang-undang Siber',
        'description' => 'Kuiz ini merangkumi konsep etika dalam pengkomputeran, hak cipta, dan undang-undang siber.',
        'max_attempts' => 2,
        'due_at' => Carbon::now()->addDays(45),
        'is_published' => true,
        'questions' => [
            // ... (15 Questions generated for brevity, following the structure) ...
            [
                'text' => 'Pilih mana-mana contoh pelanggaran etika komputer.',
                'type' => 'checkbox', 'points' => 6,
                'options' => [
                    ['text' => 'Menggunakan perisian cetak rompak', 'is_correct' => true],
                    ['text' => 'Berkongsi kata laluan tanpa izin', 'is_correct' => true],
                    ['text' => 'Mengemas kini perisian antivirus', 'is_correct' => false],
                    ['text' => 'Memuat turun bahan berhak cipta secara haram', 'is_correct' => true],
                ]
            ],
            [
                'text' => 'Apakah yang dimaksudkan dengan "Phishing"?',
                'type' => 'multiple_choice', 'points' => 5,
                'options' => [
                    ['text' => 'Proses menghantar e-mel promosi.', 'is_correct' => false],
                    ['text' => 'Percubaan mendapatkan maklumat sensitif (seperti kata laluan) secara penipuan.', 'is_correct' => true],
                    ['text' => 'Protokol untuk memuat turun fail.', 'is_correct' => false],
                    ['text' => 'Penggunaan komputer yang berlebihan.', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Undang-undang Hak Cipta melindungi idea, dan bukannya ekspresi idea.',
                'type' => 'true_false', 'points' => 3,
                'options' => [
                    ['text' => 'True', 'is_correct' => false],
                    ['text' => 'False', 'is_correct' => true], // Protects the expression/form, not the idea itself
                ]
            ],
            [
                'text' => 'Memberi atribusi yang betul apabila menggunakan bahan orang lain adalah amalan etika.',
                'type' => 'true_false', 'points' => 2,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Penyalahgunaan data peribadi tanpa kebenaran adalah melanggar Akta Perlindungan Data Peribadi.',
                'type' => 'true_false', 'points' => 4,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Jenis perisian yang dibenarkan untuk digunakan dan diedarkan secara percuma oleh pencipta asalnya dipanggil perisian ______.',
                'type' => 'short_answer', 'points' => 6,
                'correct_text' => 'Freeware',
            ],
            [
                'text' => 'Apakah istilah yang digunakan untuk merujuk kepada perisian yang direka untuk menyebabkan kerosakan pada sistem komputer?',
                'type' => 'short_answer', 'points' => 5,
                'correct_text' => 'Malware',
            ],
            [
                'text' => 'Hacker yang melanggar undang-undang siber dipanggil Black Hat Hacker.',
                'type' => 'true_false', 'points' => 3,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Pilih undang-undang siber Malaysia yang berkaitan dengan penyalahgunaan komputer.',
                'type' => 'checkbox', 'points' => 8,
                'options' => [
                    ['text' => 'Akta Komunikasi dan Multimedia', 'is_correct' => true],
                    ['text' => 'Akta Jenayah Komputer', 'is_correct' => true],
                    ['text' => 'Akta Hak Cipta', 'is_correct' => true],
                    ['text' => 'Akta Jalan, Parit dan Bangunan', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Melakukan pemindahan data peribadi tanpa pengetahuan pengguna adalah etika yang baik.',
                'type' => 'true_false', 'points' => 2,
                'options' => [
                    ['text' => 'True', 'is_correct' => false],
                    ['text' => 'False', 'is_correct' => true],
                ]
            ],
            [
                'text' => 'Akta Hak Cipta melindungi nama perniagaan atau jenama produk.',
                'type' => 'true_false', 'points' => 3,
                'options' => [
                    ['text' => 'True', 'is_correct' => false],
                    ['text' => 'False', 'is_correct' => true], // That's Trademark law
                ]
            ],
            [
                'text' => 'Jenayah siber adalah jenayah yang dilakukan menggunakan komputer dan internet.',
                'type' => 'true_false', 'points' => 2,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Deface laman web adalah satu bentuk jenayah siber.',
                'type' => 'true_false', 'points' => 3,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Penggunaan sumber komputasi yang berlebihan tanpa kebenaran adalah satu isu etika.',
                'type' => 'true_false', 'points' => 2,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Apakah yang dimaksudkan dengan "hak digital"?',
                'type' => 'multiple_choice', 'points' => 5,
                'options' => [
                    ['text' => 'Hak untuk memiliki peranti digital.', 'is_correct' => false],
                    ['text' => 'Hak untuk mengakses, menggunakan, mencipta, dan menerbitkan karya digital.', 'is_correct' => true],
                    ['text' => 'Hak untuk memadam data secara kekal.', 'is_correct' => false],
                    ['text' => 'Hak untuk menjadi seorang pengaturcara.', 'is_correct' => false],
                ]
            ],
        ], // Total 15 Questions
    ],

    // ----------------------------------------------------------------------
    // QUIZ 5: Tingkatan 5, Bab 1 - Pembangunan Aplikasi Laman Web (Basics)
    // (Assumed Chapter 1 based on snippets: Scripting Language, Website Design)
    // ----------------------------------------------------------------------
    [
        'teacher_id' => 6,
        'title' => 'Tingkatan 5, Bab 1: Pembangunan Aplikasi Laman Web (Asas)',
        'description' => 'Kuiz ini menguji asas pembangunan aplikasi web termasuk bahasa skrip.',
        'max_attempts' => 4,
        'due_at' => Carbon::now()->addDays(45),
        'is_published' => true,
        'questions' => [
            // ... (10 Questions generated for brevity, following the structure) ...
            [
                'text' => 'Apakah bahasa yang digunakan untuk strukturkan kandungan (content) laman web?',
                'type' => 'multiple_choice', 'points' => 3,
                'options' => [
                    ['text' => 'CSS', 'is_correct' => false],
                    ['text' => 'JavaScript', 'is_correct' => false],
                    ['text' => 'HTML', 'is_correct' => true],
                    ['text' => 'Java', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Bahasa Skrip "Client-side" (contohnya JavaScript) dilaksanakan oleh pelayan (server).',
                'type' => 'true_false', 'points' => 4,
                'options' => [
                    ['text' => 'True', 'is_correct' => false],
                    ['text' => 'False', 'is_correct' => true], // Client-side script is executed by the browser
                ]
            ],
            [
                'text' => 'Pilih mana-mana bahasa skrip "Server-side".',
                'type' => 'checkbox', 'points' => 6,
                'options' => [
                    ['text' => 'PHP', 'is_correct' => true],
                    ['text' => 'JavaScript', 'is_correct' => false],
                    ['text' => 'Python', 'is_correct' => true],
                    ['text' => 'HTML', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'CSS bertanggungjawab untuk memastikan fungsi (functionality) interaktif laman web.',
                'type' => 'true_false', 'points' => 3,
                'options' => [
                    ['text' => 'True', 'is_correct' => false],
                    ['text' => 'False', 'is_correct' => true], // CSS handles styling/appearance
                ]
            ],
            [
                'text' => 'Apakah istilah yang merujuk kepada proses memastikan laman web mudah digunakan oleh pengguna?',
                'type' => 'short_answer', 'points' => 5,
                'correct_text' => 'Usability',
            ],
            [
                'text' => 'Tag `<p>` digunakan untuk mewakili perenggan dalam HTML.',
                'type' => 'true_false', 'points' => 2,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'HTML adalah bahasa pengaturcaraan.',
                'type' => 'true_false', 'points' => 3,
                'options' => [
                    ['text' => 'True', 'is_correct' => false],
                    ['text' => 'False', 'is_correct' => true], // It's a markup language
                ]
            ],
            [
                'text' => 'URL bermaksud Uniform Resource Locator.',
                'type' => 'true_false', 'points' => 2,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Domain Name System (DNS) menukarkan nama domain kepada alamat IP.',
                'type' => 'true_false', 'points' => 4,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Apakah tujuan utama bahasa skrip "Server-side"?',
                'type' => 'multiple_choice', 'points' => 5,
                'options' => [
                    ['text' => 'Untuk mengubah rupa dan gaya (style) laman web.', 'is_correct' => false],
                    ['text' => 'Untuk menguruskan pangkalan data dan logik aplikasi.', 'is_correct' => true],
                    ['text' => 'Untuk mengesahkan input pengguna di pelayar (browser).', 'is_correct' => false],
                    ['text' => 'Untuk menghantar e-mel ke pelanggan.', 'is_correct' => false],
                ]
            ],
        ], // Total 10 Questions
    ],

    // ----------------------------------------------------------------------
    // QUIZ 6: Tingkatan 4 - Sistem Nombor dan Perwakilan Data
    // ----------------------------------------------------------------------
    [
        'teacher_id' => 2,
        'title' => 'Tingkatan 4: Sistem Nombor dan Perwakilan Data',
        'description' => 'Kuiz ini merangkumi sistem nombor binari, perlapanan, heksadesimal, dan perwakilan data.',
        'max_attempts' => 4,
        'due_at' => Carbon::now()->addDays(35),
        'is_published' => true,
        'questions' => [
            [
                'text' => 'Apakah asas (base) untuk sistem nombor binari?',
                'type' => 'multiple_choice', 'points' => 3,
                'options' => [
                    ['text' => '8', 'is_correct' => false],
                    ['text' => '10', 'is_correct' => false],
                    ['text' => '2', 'is_correct' => true],
                    ['text' => '16', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Tukarkan nombor perpuluhan 15 kepada binari. Jawapan: ______',
                'type' => 'short_answer', 'points' => 5,
                'correct_text' => '1111',
            ],
            [
                'text' => 'Sistem nombor heksadesimal menggunakan digit 0-9 dan huruf A-F.',
                'type' => 'true_false', 'points' => 2,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Asas untuk sistem nombor perlapanan adalah ______.',
                'type' => 'short_answer', 'points' => 3,
                'correct_text' => '8',
            ],
            [
                'text' => 'Apakah nilai perpuluhan bagi binari 10101?',
                'type' => 'multiple_choice', 'points' => 4,
                'options' => [
                    ['text' => '20', 'is_correct' => false],
                    ['text' => '21', 'is_correct' => true],
                    ['text' => '19', 'is_correct' => false],
                    ['text' => '22', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Pilih jenis-jenis perwakilan data dalam komputer.',
                'type' => 'checkbox', 'points' => 6,
                'options' => [
                    ['text' => 'Audio', 'is_correct' => true],
                    ['text' => 'Video', 'is_correct' => true],
                    ['text' => 'Teks', 'is_correct' => true],
                    ['text' => 'Masa', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Konversi 255 dalam perpuluhan kepada heksadesimal: ______',
                'type' => 'short_answer', 'points' => 6,
                'correct_text' => 'FF',
            ],
            [
                'text' => 'Bit adalah unit asas (fundamental unit) untuk mewakili data dalam komputer.',
                'type' => 'true_false', 'points' => 2,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Satu bait (byte) setara dengan berapa bit?',
                'type' => 'multiple_choice', 'points' => 3,
                'options' => [
                    ['text' => '4', 'is_correct' => false],
                    ['text' => '8', 'is_correct' => true],
                    ['text' => '16', 'is_correct' => false],
                    ['text' => '32', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'ASCII adalah standard untuk perwakilan aksara dalam komputer.',
                'type' => 'true_false', 'points' => 3,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
        ], // Total 10 Questions
    ],

    // ----------------------------------------------------------------------
    // QUIZ 7: Tingkatan 4 - Pengurusan Sistem Fail dan Keselamatan Data
    // ----------------------------------------------------------------------
    [
        'teacher_id' => 3,
        'title' => 'Tingkatan 4: Pengurusan Sistem Fail dan Keselamatan Data',
        'description' => 'Kuiz mengenai pengurusan fail, organisasi data, backup, dan keselamatan maklumat.',
        'max_attempts' => 3,
        'due_at' => Carbon::now()->addDays(40),
        'is_published' => true,
        'questions' => [
            [
                'text' => 'Apakah fungsi utama sistem fail dalam komputer?',
                'type' => 'multiple_choice', 'points' => 4,
                'options' => [
                    ['text' => 'Menyimpan dan mengatur maklumat dalam disk.', 'is_correct' => true],
                    ['text' => 'Menjalankan perisian aplikasi sahaja.', 'is_correct' => false],
                    ['text' => 'Mengawal input/output perangkat keras.', 'is_correct' => false],
                    ['text' => 'Menyambung ke internet sahaja.', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Folder dalam komputer juga dikenali sebagai ______.',
                'type' => 'short_answer', 'points' => 3,
                'correct_text' => 'Directory',
            ],
            [
                'text' => 'Backup data adalah satu amalan penting untuk melindungi maklumat daripada kehilangan.',
                'type' => 'true_false', 'points' => 3,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Apakah kaedah enkripsi data yang paling biasa digunakan?',
                'type' => 'multiple_choice', 'points' => 5,
                'options' => [
                    ['text' => 'Penyejajaran teks', 'is_correct' => false],
                    ['text' => 'Algoritma AES (Advanced Encryption Standard)', 'is_correct' => true],
                    ['text' => 'Pemampatan fail', 'is_correct' => false],
                    ['text' => 'Sinkronisasi folder', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Pilih langkah-langkah untuk melindungi data dari ancaman siber.',
                'type' => 'checkbox', 'points' => 7,
                'options' => [
                    ['text' => 'Menggunakan kata laluan yang kuat', 'is_correct' => true],
                    ['text' => 'Melakukan backup berkala', 'is_correct' => true],
                    ['text' => 'Menutup antivirus', 'is_correct' => false],
                    ['text' => 'Memperbarui perisian secara berkala', 'is_correct' => true],
                ]
            ],
            [
                'text' => 'Pemampatan fail (file compression) mengurangkan saiz fail sambil mengekalkan kualiti data.',
                'type' => 'true_false', 'points' => 4,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Apakah format fail yang biasa digunakan untuk fail termampat?',
                'type' => 'multiple_choice', 'points' => 4,
                'options' => [
                    ['text' => '.doc', 'is_correct' => false],
                    ['text' => '.zip', 'is_correct' => true],
                    ['text' => '.mp3', 'is_correct' => false],
                    ['text' => '.html', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Kerosakan sektor dalam hard disk dapat menyebabkan kehilangan data yang tidak dapat dipulihkan.',
                'type' => 'true_false', 'points' => 3,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Apakah yang dimaksudkan dengan "data integrity"?',
                'type' => 'multiple_choice', 'points' => 5,
                'options' => [
                    ['text' => 'Data tidak hilang atau rosak semasa disimpan atau dihantar.', 'is_correct' => true],
                    ['text' => 'Data yang dienkripsi sahaja.', 'is_correct' => false],
                    ['text' => 'Data yang disimpan di cloud.', 'is_correct' => false],
                    ['text' => 'Data yang tidak diperlukan.', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Ancam kepada privasi data termasuk ______.',
                'type' => 'short_answer', 'points' => 5,
                'correct_text' => 'Phishing,Malware,Unauthorized Access',
            ],
        ], // Total 10 Questions
    ],

    // ----------------------------------------------------------------------
    // QUIZ 8: Tingkatan 5 - Pangkalan Data dan SQL
    // ----------------------------------------------------------------------
    [
        'teacher_id' => 4,
        'title' => 'Tingkatan 5: Pangkalan Data dan SQL',
        'description' => 'Kuiz tentang konsep pangkalan data relasional, normalisasi, dan bahasa SQL.',
        'max_attempts' => 4,
        'due_at' => Carbon::now()->addDays(50),
        'is_published' => true,
        'questions' => [
            [
                'text' => 'Apakah maksud RDBMS dalam pangkalan data?',
                'type' => 'multiple_choice', 'points' => 4,
                'options' => [
                    ['text' => 'Relational Database Management System', 'is_correct' => true],
                    ['text' => 'Real Database Module System', 'is_correct' => false],
                    ['text' => 'Remote Database Management Service', 'is_correct' => false],
                    ['text' => 'Rapid Database Mobile System', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Jadual dalam pangkalan data terdiri daripada baris dan lajur. Baris juga dipanggil ______.',
                'type' => 'short_answer', 'points' => 4,
                'correct_text' => 'Record',
            ],
            [
                'text' => 'Kunci primer (Primary Key) digunakan untuk mengenal pasti rekod secara unik dalam jadual.',
                'type' => 'true_false', 'points' => 3,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Apakah perintah SQL yang digunakan untuk menambah data baru ke dalam jadual?',
                'type' => 'multiple_choice', 'points' => 4,
                'options' => [
                    ['text' => 'INSERT', 'is_correct' => true],
                    ['text' => 'UPDATE', 'is_correct' => false],
                    ['text' => 'SELECT', 'is_correct' => false],
                    ['text' => 'DELETE', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Pilih perintah SQL yang benar untuk mencari data.',
                'type' => 'checkbox', 'points' => 6,
                'options' => [
                    ['text' => 'SELECT', 'is_correct' => true],
                    ['text' => 'WHERE', 'is_correct' => true],
                    ['text' => 'FROM', 'is_correct' => true],
                    ['text' => 'REMOVE', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Normalisasi pangkalan data bertujuan untuk mengurangkan redundansi data.',
                'type' => 'true_false', 'points' => 4,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Kunci asing (Foreign Key) digunakan untuk ______.',
                'type' => 'short_answer', 'points' => 5,
                'correct_text' => 'menghubungkan jadual,membuat hubungan antara jadual',
            ],
            [
                'text' => 'Perintah SQL yang digunakan untuk mengubah data yang sedia ada adalah ______.',
                'type' => 'short_answer', 'points' => 4,
                'correct_text' => 'UPDATE',
            ],
            [
                'text' => 'Berapa bentuk normal (Normal Form) dalam normalisasi pangkalan data?',
                'type' => 'multiple_choice', 'points' => 4,
                'options' => [
                    ['text' => '2', 'is_correct' => false],
                    ['text' => '3', 'is_correct' => true],
                    ['text' => '4', 'is_correct' => false],
                    ['text' => '5', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Kueri (query) adalah perintah untuk meminta maklumat daripada pangkalan data.',
                'type' => 'true_false', 'points' => 2,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
        ], // Total 10 Questions
    ],

    // ----------------------------------------------------------------------
    // QUIZ 9: Tingkatan 4 - Algoritma dan Pseudokod
    // ----------------------------------------------------------------------
    [
        'teacher_id' => 5,
        'title' => 'Tingkatan 4: Algoritma dan Pseudokod',
        'description' => 'Kuiz mengenai konsep algoritma, analisis algoritma, dan penulisan pseudokod.',
        'max_attempts' => 5,
        'due_at' => Carbon::now()->addDays(42),
        'is_published' => true,
        'questions' => [
            [
                'text' => 'Apakah definisi algoritma yang paling tepat?',
                'type' => 'multiple_choice', 'points' => 4,
                'options' => [
                    ['text' => 'Satu set langkah-langkah yang jelas untuk menyelesaikan masalah.', 'is_correct' => true],
                    ['text' => 'Nama seorang pengaturcara terkenal.', 'is_correct' => false],
                    ['text' => 'Bahasa pengaturcaraan.', 'is_correct' => false],
                    ['text' => 'Sejenis perisian anti-virus.', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Pseudokod adalah tulisan yang menyerupai bahasa pengaturcaraan tetapi tidak dapat dilaksanakan oleh komputer.',
                'type' => 'true_false', 'points' => 3,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Apakah tiga komponen utama yang digunakan dalam analisis algoritma?',
                'type' => 'multiple_choice', 'points' => 5,
                'options' => [
                    ['text' => 'Masa, Ruang, dan Ketepatan', 'is_correct' => true],
                    ['text' => 'Panjang, Lebar, dan Tinggi', 'is_correct' => false],
                    ['text' => 'Input, Output, dan Proses', 'is_correct' => false],
                    ['text' => 'Permulaaan, Pertengahan, dan Akhir', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Carta alir (flowchart) digunakan untuk mewakili algoritma secara visual.',
                'type' => 'true_false', 'points' => 2,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Pilih langkah-langkah dalam membangunkan algoritma.',
                'type' => 'checkbox', 'points' => 7,
                'options' => [
                    ['text' => 'Memahami masalah', 'is_correct' => true],
                    ['text' => 'Merangka penyelesaian', 'is_correct' => true],
                    ['text' => 'Melupakan keputusan', 'is_correct' => false],
                    ['text' => 'Menguji algoritma', 'is_correct' => true],
                ]
            ],
            [
                'text' => 'Kerumitan waktu O(1) bermaksud algoritma berjalan dalam ______.',
                'type' => 'short_answer', 'points' => 5,
                'correct_text' => 'Masa tetap,Malar',
            ],
            [
                'text' => 'Algoritma pencarian sekuensial (linear search) adalah lebih cepat daripada pencarian binari (binary search).',
                'type' => 'true_false', 'points' => 4,
                'options' => [
                    ['text' => 'True', 'is_correct' => false],
                    ['text' => 'False', 'is_correct' => true],
                ]
            ],
            [
                'text' => 'Simbol berlian (diamond) dalam carta alir mewakili ______.',
                'type' => 'short_answer', 'points' => 4,
                'correct_text' => 'Keputusan,Syarat',
            ],
            [
                'text' => 'Apakah jenis-jenis struktur data asas yang sering digunakan dalam algoritma?',
                'type' => 'multiple_choice', 'points' => 5,
                'options' => [
                    ['text' => 'Array, Stack, Queue, Linked List', 'is_correct' => true],
                    ['text' => 'Tikus, Papan Kekunci, Monitor', 'is_correct' => false],
                    ['text' => 'Merah, Hijau, Biru', 'is_correct' => false],
                    ['text' => 'Keras, Lembut, Tengah', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Keadaan penamat (terminating condition) adalah penting dalam gelung untuk mengelakkan ______.',
                'type' => 'short_answer', 'points' => 4,
                'correct_text' => 'Gelung tak terhingga,Infinite loop',
            ],
        ], // Total 10 Questions
    ],

    // ----------------------------------------------------------------------
    // QUIZ 10: Tingkatan 5 - Rangkaian Komputer dan Internet
    // ----------------------------------------------------------------------
    [
        'teacher_id' => 6,
        'title' => 'Tingkatan 5: Rangkaian Komputer dan Internet',
        'description' => 'Kuiz mengenai topologi rangkaian, protokol komunikasi, dan keselamatan rangkaian.',
        'max_attempts' => 3,
        'due_at' => Carbon::now()->addDays(55),
        'is_published' => true,
        'questions' => [
            [
                'text' => 'Apakah topologi rangkaian yang menghubungkan semua komputer ke satu peranti pusat?',
                'type' => 'multiple_choice', 'points' => 4,
                'options' => [
                    ['text' => 'Topologi Bus', 'is_correct' => false],
                    ['text' => 'Topologi Bintang', 'is_correct' => true],
                    ['text' => 'Topologi Cincin', 'is_correct' => false],
                    ['text' => 'Topologi Mesh', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'TCP/IP adalah protokol yang digunakan dalam Internet.',
                'type' => 'true_false', 'points' => 3,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Apakah kepanjangan bagi IP dalam rangkaian komputer?',
                'type' => 'multiple_choice', 'points' => 3,
                'options' => [
                    ['text' => 'Internet Protocol', 'is_correct' => true],
                    ['text' => 'Internal Program', 'is_correct' => false],
                    ['text' => 'Internet Provider', 'is_correct' => false],
                    ['text' => 'Integrated Processor', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Firewall adalah alat untuk melindungi rangkaian dari ancaman luar.',
                'type' => 'true_false', 'points' => 4,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Pilih topologi rangkaian yang ada.',
                'type' => 'checkbox', 'points' => 6,
                'options' => [
                    ['text' => 'Topologi Bintang', 'is_correct' => true],
                    ['text' => 'Topologi Merah', 'is_correct' => false],
                    ['text' => 'Topologi Mesh', 'is_correct' => true],
                    ['text' => 'Topologi Cincin', 'is_correct' => true],
                ]
            ],
            [
                'text' => 'Alamat IP versi 4 terdiri daripada berapa oktet?',
                'type' => 'multiple_choice', 'points' => 3,
                'options' => [
                    ['text' => '2', 'is_correct' => false],
                    ['text' => '4', 'is_correct' => true],
                    ['text' => '6', 'is_correct' => false],
                    ['text' => '8', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Protokol HTTP digunakan untuk komunikasi data yang selamat di Internet.',
                'type' => 'true_false', 'points' => 4,
                'options' => [
                    ['text' => 'True', 'is_correct' => false],
                    ['text' => 'False', 'is_correct' => true], // HTTPS is secure, not HTTP
                ]
            ],
            [
                'text' => 'Bandwidth dalam rangkaian merujuk kepada ______.',
                'type' => 'short_answer', 'points' => 5,
                'correct_text' => 'kapasiti pemindahan data,kecepatan transfer',
            ],
            [
                'text' => 'Teknologi VPN membenarkan pengguna untuk mengakses rangkaian secara selamat melalui rangkaian awam.',
                'type' => 'true_false', 'points' => 4,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Istilah "latency" dalam rangkaian merujuk kepada ______.',
                'type' => 'short_answer', 'points' => 4,
                'correct_text' => 'Kelewatan,Masa tunda',
            ],
        ], // Total 10 Questions
    ],
];
