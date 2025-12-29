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

    // ---------------------------------------------------------------
    // QUIZ 6: Tingkatan 5 Bab 1 - Komputer & Impak
    // ---------------------------------------------------------------
    [
        'teacher_id' => 7,
        'title' => 'Tingkatan 5 Bab 1: Komputer & Impak Sosial',
        'description' => 'Kuiz mengenai impak komputer dan teknologi terhadap masyarakat dan ekonomi.',
        'max_attempts' => 3,
        'due_at' => Carbon::now()->addDays(30),
        'is_published' => true,
        'questions' => [
            [
                'text' => 'Apakah definisi teknologi maklumat (IT)?',
                'type' => 'multiple_choice', 'points' => 3,
                'options' => [
                    ['text' => 'Penggunaan komputer untuk memproses dan menyimpan data', 'is_correct' => true],
                    ['text' => 'Hanya merujuk kepada rangkaian internet', 'is_correct' => false],
                    ['text' => 'Sistem komunikasi tradisional', 'is_correct' => false],
                    ['text' => 'Teknologi untuk pembuatan kereta', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Antara berikut, yang manakah merupakan impak POSITIF penggunaan komputer?',
                'type' => 'multiple_choice', 'points' => 4,
                'options' => [
                    ['text' => 'Meningkatkan produktiviti kerja', 'is_correct' => true],
                    ['text' => 'Kehilangan pekerjaan tradisional', 'is_correct' => false],
                    ['text' => 'Peningkatan jenayah siber', 'is_correct' => false],
                    ['text' => 'Gangguan kesihatan mata', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Kemajuan teknologi komputer telah mengubah cara orang bekerja dan berinteraksi.',
                'type' => 'true_false', 'points' => 2,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Antara berikut, yang manakah adalah impak NEGATIF penggunaan komputer yang berlebihan?',
                'type' => 'checkbox', 'points' => 5,
                'options' => [
                    ['text' => 'Masalah kesihatan mental', 'is_correct' => true],
                    ['text' => 'Keadilan dalam pendidikan', 'is_correct' => false],
                    ['text' => 'Ketagihan media sosial', 'is_correct' => true],
                    ['text' => 'Efisiensi perniagaan', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Sebutkan tiga industri yang diubah oleh komputer dan internet.',
                'type' => 'short_answer', 'points' => 5,
                'correct_text' => 'Perbankan,Pendidikan,Runcit,Kesihatan,Komunikasi',
            ],
            [
                'text' => 'Apakah yang dimaksudkan dengan "digital divide"?',
                'type' => 'multiple_choice', 'points' => 4,
                'options' => [
                    ['text' => 'Jurang antara mereka yang mempunyai akses teknologi dan yang tidak', 'is_correct' => true],
                    ['text' => 'Perbezaan dalam jenis komputer yang digunakan', 'is_correct' => false],
                    ['text' => 'Pembahagian pasar komputer', 'is_correct' => false],
                    ['text' => 'Terbahagi layar komputer', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Privasi data adalah salah satu cabaran utama dalam era digital.',
                'type' => 'true_false', 'points' => 3,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Apa itu e-commerce?',
                'type' => 'short_answer', 'points' => 4,
                'correct_text' => 'Perdagangan elektronik,Jualan dalam talian,Transaksi perniagaan dalam talian',
            ],
            [
                'text' => 'Komputer telah membantu dalam pemajuan ekonomi negara.',
                'type' => 'true_false', 'points' => 2,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Antara berikut, yang manakah adalah kebaikan globalisasi digital?',
                'type' => 'multiple_choice', 'points' => 4,
                'options' => [
                    ['text' => 'Kemudahan berbagi maklumat dan pengetahuan', 'is_correct' => true],
                    ['text' => 'Penghapusan semua masalah sosial', 'is_correct' => false],
                    ['text' => 'Pengurangan penggunaan komputer', 'is_correct' => false],
                    ['text' => 'Pemusatan kuasa politik', 'is_correct' => false],
                ]
            ],
        ], // Total 10 Questions
    ],

    // ---------------------------------------------------------------
    // QUIZ 7: Tingkatan 5 Bab 1 - Seni Bina Komputer
    // ---------------------------------------------------------------
    [
        'teacher_id' => 2,
        'title' => 'Tingkatan 5 Bab 1: Seni Bina Komputer',
        'description' => 'Kuiz mengenai struktur dan komponen utama seni bina komputer.',
        'max_attempts' => 3,
        'due_at' => Carbon::now()->addDays(30),
        'is_published' => true,
        'questions' => [
            [
                'text' => 'Apakah Unit Pemprosesan Pusat (CPU)?',
                'type' => 'multiple_choice', 'points' => 3,
                'options' => [
                    ['text' => 'Otak komputer yang melakukan pemprosesan', 'is_correct' => true],
                    ['text' => 'Peranti penyimpanan data', 'is_correct' => false],
                    ['text' => 'Papan litar utama', 'is_correct' => false],
                    ['text' => 'Sistem pendingin komputer', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Apa fungsi utama RAM?',
                'type' => 'multiple_choice', 'points' => 4,
                'options' => [
                    ['text' => 'Penyimpanan sementara data semasa pemprosesan', 'is_correct' => true],
                    ['text' => 'Penyimpanan permanen data', 'is_correct' => false],
                    ['text' => 'Penghantaran data melalui internet', 'is_correct' => false],
                    ['text' => 'Penampilan grafik', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'ROM adalah jenis memori yang boleh diubah oleh pengguna.',
                'type' => 'true_false', 'points' => 2,
                'options' => [
                    ['text' => 'True', 'is_correct' => false],
                    ['text' => 'False', 'is_correct' => true],
                ]
            ],
            [
                'text' => 'Pilih semua komponen perkakasan utama komputer.',
                'type' => 'checkbox', 'points' => 5,
                'options' => [
                    ['text' => 'Papan ibu (Motherboard)', 'is_correct' => true],
                    ['text' => 'Sistem operasi', 'is_correct' => false],
                    ['text' => 'Peranti grafis (GPU)', 'is_correct' => true],
                    ['text' => 'Peranti penyimpanan (HDD/SSD)', 'is_correct' => true],
                ]
            ],
            [
                'text' => 'Apakah unit asas bagi laju pemprosesan CPU?',
                'type' => 'short_answer', 'points' => 4,
                'correct_text' => 'GHz,Gigahertz',
            ],
            [
                'text' => 'Antara berikut, yang manakah menyimpan data secara permanen?',
                'type' => 'multiple_choice', 'points' => 4,
                'options' => [
                    ['text' => 'Hard Disk Drive (HDD)', 'is_correct' => true],
                    ['text' => 'RAM', 'is_correct' => false],
                    ['text' => 'Cache', 'is_correct' => false],
                    ['text' => 'Daftar (Register)', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'GPU (Graphics Processing Unit) hanya digunakan untuk permainan video.',
                'type' => 'true_false', 'points' => 3,
                'options' => [
                    ['text' => 'True', 'is_correct' => false],
                    ['text' => 'False', 'is_correct' => true],
                ]
            ],
            [
                'text' => 'Sebutkan tiga jenis ingatan komputer mengikut susunan kecepatan dari paling cepat.',
                'type' => 'short_answer', 'points' => 5,
                'correct_text' => 'Register,Cache,RAM',
            ],
            [
                'text' => 'Papan ibu (Motherboard) menghubungkan semua komponen perkakasan komputer.',
                'type' => 'true_false', 'points' => 2,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Apakah nisbah perbandingan kecepatan Cache berbanding RAM?',
                'type' => 'multiple_choice', 'points' => 4,
                'options' => [
                    ['text' => 'Cache lebih cepat 10-100 kali dari RAM', 'is_correct' => true],
                    ['text' => 'RAM lebih cepat dari Cache', 'is_correct' => false],
                    ['text' => 'Kecepatan sama', 'is_correct' => false],
                    ['text' => 'Cache lebih lambat 10 kali', 'is_correct' => false],
                ]
            ],
        ], // Total 10 Questions
    ],

    // ---------------------------------------------------------------
    // QUIZ 8: Tingkatan 5 Bab 1 - Gerbang Logik
    // ---------------------------------------------------------------
    [
        'teacher_id' => 3,
        'title' => 'Tingkatan 5 Bab 1: Gerbang Logik',
        'description' => 'Kuiz mengenai gerbang logik asas dan operasi logik dalam seni bina digital.',
        'max_attempts' => 3,
        'due_at' => Carbon::now()->addDays(30),
        'is_published' => true,
        'questions' => [
            [
                'text' => 'Apakah gerbang logik AND?',
                'type' => 'multiple_choice', 'points' => 3,
                'options' => [
                    ['text' => 'Keluaran 1 apabila SEMUA input adalah 1', 'is_correct' => true],
                    ['text' => 'Keluaran 1 apabila SALAH SATU input adalah 1', 'is_correct' => false],
                    ['text' => 'Keluaran 1 apabila input adalah 0', 'is_correct' => false],
                    ['text' => 'Tidak berkaitan dengan input', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Apakah keluaran gerbang OR jika input adalah 0 dan 0?',
                'type' => 'multiple_choice', 'points' => 4,
                'options' => [
                    ['text' => '0', 'is_correct' => true],
                    ['text' => '1', 'is_correct' => false],
                    ['text' => '2', 'is_correct' => false],
                    ['text' => 'Tidak tentu', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Gerbang NOT mengubah 1 menjadi 0 dan 0 menjadi 1.',
                'type' => 'true_false', 'points' => 2,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Pilih semua gerbang logik asas.',
                'type' => 'checkbox', 'points' => 5,
                'options' => [
                    ['text' => 'AND', 'is_correct' => true],
                    ['text' => 'OR', 'is_correct' => true],
                    ['text' => 'NOT', 'is_correct' => true],
                    ['text' => 'MAYBE', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Apakah keluaran gerbang XOR jika input adalah 1 dan 0?',
                'type' => 'short_answer', 'points' => 4,
                'correct_text' => '1',
            ],
            [
                'text' => 'Gerbang NAND adalah gabungan daripada gerbang apa?',
                'type' => 'multiple_choice', 'points' => 4,
                'options' => [
                    ['text' => 'AND dan NOT', 'is_correct' => true],
                    ['text' => 'OR dan NOT', 'is_correct' => false],
                    ['text' => 'AND dan OR', 'is_correct' => false],
                    ['text' => 'Gerbang asas sahaja', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Jadual kebenaran gerbang AND dengan 2 input mempunyai 4 kombinasi kemungkinan.',
                'type' => 'true_false', 'points' => 3,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Senaraikan keluaran gerbang AND untuk semua kombinasi input 2 bit.',
                'type' => 'short_answer', 'points' => 5,
                'correct_text' => '0,0,0,1',
            ],
            [
                'text' => 'Gerbang universal adalah gerbang yang dapat membina semua gerbang logik yang lain.',
                'type' => 'true_false', 'points' => 2,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Apakah keluaran gerbang NOR jika semua input adalah 0?',
                'type' => 'multiple_choice', 'points' => 4,
                'options' => [
                    ['text' => '1', 'is_correct' => true],
                    ['text' => '0', 'is_correct' => false],
                    ['text' => '2', 'is_correct' => false],
                    ['text' => 'Tidak tentu', 'is_correct' => false],
                ]
            ],
        ], // Total 10 Questions
    ],

    // ---------------------------------------------------------------
    // QUIZ 14: Tingkatan 5 Bab 1 - Pengaturcaraan Java (Coding)
    // ---------------------------------------------------------------
    [
        'teacher_id' => 4,
        'title' => 'Tingkatan 5: Pengaturcaraan Java - Soalan Kod',
        'description' => 'Kuiz pengekodan yang memerlukan pelajar menulis kod Java dari awal atau melengkapkan templat yang disediakan.',
        'max_attempts' => 3,
        'due_at' => Carbon::now()->addDays(45),
        'is_published' => true,
        'questions' => [
            [
                'text' => 'Tulis kaedah Java yang menerima dua integer sebagai parameter dan mengembalikan hasil tambah mereka. Nama kaedah: `tambah`',
                'type' => 'coding', 'points' => 10,
                'coding_language' => 'java',
                'coding_template' => null,
                'coding_expected_output' => null,
            ],
            [
                'text' => 'Lengkapkan kaedah Java untuk menghitung faktorial bagi nombor yang diberikan.',
                'type' => 'coding', 'points' => 15,
                'coding_language' => 'java',
                'coding_template' => 'public static int faktorial(int n) {
    // Lengkapkan kod di sini
    
}',
                'coding_expected_output' => null,
            ],
            [
                'text' => 'Tulis program Java untuk mencetak segi tiga bintang dengan n baris. Contoh (n=3):
*
**
***',
                'type' => 'coding', 'points' => 12,
                'coding_language' => 'java',
                'coding_template' => null,
                'coding_expected_output' => '*
**
***',
            ],
            [
                'text' => 'Lengkapkan kaedah yang menyemak sama ada satu integer adalah nombor perdana.',
                'type' => 'coding', 'points' => 15,
                'coding_language' => 'java',
                'coding_template' => 'public static boolean isPerdana(int num) {
    // Lengkapkan kod di sini
    
}',
                'coding_expected_output' => null,
            ],
            [
                'text' => 'Tulis kaedah Java yang mengembalikan rentetan terbalik (reverse) bagi rentetan input.',
                'type' => 'coding', 'points' => 10,
                'coding_language' => 'java',
                'coding_template' => null,
                'coding_expected_output' => null,
            ],
            [
                'text' => 'Lengkapkan kaedah untuk mengira jumlah digit dalam integer positif.',
                'type' => 'coding', 'points' => 10,
                'coding_language' => 'java',
                'coding_template' => 'public static int jumlahDigit(int num) {
    // Lengkapkan kod di sini
    
}',
                'coding_expected_output' => null,
            ],
            [
                'text' => 'Tulis program Java untuk mencetak jadual pendaraban 5x5.',
                'type' => 'coding', 'points' => 15,
                'coding_language' => 'java',
                'coding_template' => null,
                'coding_expected_output' => null,
            ],
            [
                'text' => 'Lengkapkan kaedah yang mencari nilai maksimum dalam array integer.',
                'type' => 'coding', 'points' => 12,
                'coding_language' => 'java',
                'coding_template' => 'public static int cariMaksimum(int[] arr) {
    // Lengkapkan kod di sini
    
}',
                'coding_expected_output' => null,
            ],
            [
                'text' => 'Tulis kaedah yang menyemak sama ada satu rentetan adalah palindrom.',
                'type' => 'coding', 'points' => 15,
                'coding_language' => 'java',
                'coding_template' => null,
                'coding_expected_output' => null,
            ],
            [
                'text' => 'Lengkapkan kaedah yang mengira purata nilai dalam array dan mengembalikan hasilnya.',
                'type' => 'coding', 'points' => 10,
                'coding_language' => 'java',
                'coding_template' => 'public static double purata(int[] arr) {
    // Lengkapkan kod di sini
    
}',
                'coding_expected_output' => null,
            ],
        ], // Total 10 Questions
    ],

    // ----------------------------------------------------------------------
    // QUIZ 3: Java - Soalan Kod OOP Mudah (5 Coding Questions)
    // ----------------------------------------------------------------------
    [
        'teacher_id' => 5,
        'title' => 'Java: Soalan Kod OOP Mudah',
        'description' => 'Kuiz ini mengandungi 5 soalan pengaturcaraan tentang konsep OOP yang mudah dalam Java.',
        'max_attempts' => 3,
        'due_at' => Carbon::now()->addDays(45),
        'is_published' => true,
        'questions' => [
            [
                'text' => 'Tulis kod untuk mencipta kelas simple bernama "Kereta" dengan pemboleh ubah instance: brand (String), color (String), dan speed (int). Tambahkan kaedah constructor untuk menginisialisasi ketiga-tiga pemboleh ubah tersebut.',
                'type' => 'coding', 'points' => 10,
                'coding_template' => 'public class Kereta {
    // Tambahkan pemboleh ubah instance di sini
    
    // Tambahkan constructor di sini
}',
                'coding_expected_output' => 'Kereta k = new Kereta("Toyota", "Merah", 120);
System.out.println(k.brand); // Output: Toyota',
            ],
            [
                'text' => 'Tulis kaedah bernama `displayInfo()` dalam kelas Person yang mencetak maklumat peribadi (nama, umur, email). Kelas Person mempunyai tiga pemboleh ubah: name, age, dan email.',
                'type' => 'coding', 'points' => 10,
                'coding_template' => 'public class Person {
    private String name;
    private int age;
    private String email;
    
    public Person(String name, int age, String email) {
        this.name = name;
        this.age = age;
        this.email = email;
    }
    
    // Tulis kaedah displayInfo di sini
}',
                'coding_expected_output' => 'Nama: Ali
Umur: 25
Email: ali@example.com',
            ],
            [
                'text' => 'Buat kelas Student yang mewarisi daripada kelas Person. Tambahkan pemboleh ubah studentID dan kaedah untuk mendapatkan (getter) studentID.',
                'type' => 'coding', 'points' => 10,
                'coding_template' => 'public class Person {
    protected String name;
    protected int age;
    
    public Person(String name, int age) {
        this.name = name;
        this.age = age;
    }
}

public class Student extends Person {
    // Tambahkan pemboleh ubah dan kaedah di sini
}',
                'coding_expected_output' => 'Student s = new Student("Siti", 17, "S12345");
System.out.println(s.getStudentID()); // Output: S12345',
            ],
            [
                'text' => 'Tulis kaedah bernama `calculateArea()` dalam kelas Rectangle yang menerima panjang (length) dan lebar (width), kemudian mengembalikan nilai luas segi empat tepat.',
                'type' => 'coding', 'points' => 10,
                'coding_template' => 'public class Rectangle {
    // Tulis kaedah calculateArea di sini
    
    public static void main(String[] args) {
        Rectangle rect = new Rectangle();
        double area = rect.calculateArea(5, 10);
        System.out.println("Luas: " + area);
    }
}',
                'coding_expected_output' => 'Luas: 50.0',
            ],
            [
                'text' => 'Tulis kaedah bernama `increaseAge()` dalam kelas Person yang menambah umur sebanyak 1 tahun setiap kali dipanggil. Paparkan umur selepas peningkatan.',
                'type' => 'coding', 'points' => 10,
                'coding_template' => 'public class Person {
    private String name;
    private int age;
    
    public Person(String name, int age) {
        this.name = name;
        this.age = age;
    }
    
    // Tulis kaedah increaseAge di sini
}',
                'coding_expected_output' => 'Umur: 26',
            ],
        ], // Total 5 Coding Questions
    ],

    // ----------------------------------------------------------------------
    // QUIZ 4: Java - OOP Characteristics (Encapsulation, Abstract, Inheritance)
    // ----------------------------------------------------------------------
    [
        'teacher_id' => 6,
        'title' => 'Java: Ciri-ciri OOP (Pengkapsulan, Abstrak, Pewarisan)',
        'description' => 'Kuiz ini menguji pemahaman mendalam tentang pengkapsulan, kelas abstrak, dan pewarisan dalam OOP.',
        'max_attempts' => 3,
        'due_at' => Carbon::now()->addDays(50),
        'is_published' => true,
        'questions' => [
            [
                'text' => 'Apakah tujuan utama Pengkapsulan (Encapsulation) dalam OOP?',
                'type' => 'multiple_choice', 'points' => 6,
                'options' => [
                    ['text' => 'Untuk menyembunyikan data dalam dan hanya mengekspos interface yang diperlukan', 'is_correct' => true],
                    ['text' => 'Untuk mempertingkat kecepatan program', 'is_correct' => false],
                    ['text' => 'Untuk mengurangkan saiz fail program', 'is_correct' => false],
                    ['text' => 'Untuk memudahkan sintaks bahasa Java', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Kata kunci `private` dalam Java digunakan untuk membatasi akses ke ahli kelas kepada kelas itu sahaja.',
                'type' => 'true_false', 'points' => 4,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Apakah perbezaan antara kelas abstrak dan interface dalam Java?',
                'type' => 'short_answer', 'points' => 8,
                'correct_text' => 'Kelas abstrak boleh mempunyai pemboleh ubah dan kaedah konkrit, manakala interface hanya mempunyai kaedah abstrak dan pemalar (sebelum Java 8)',
            ],
            [
                'text' => 'Kelas abstrak boleh diinstan (instantiated) secara langsung.',
                'type' => 'true_false', 'points' => 4,
                'options' => [
                    ['text' => 'True', 'is_correct' => false],
                    ['text' => 'False', 'is_correct' => true],
                ]
            ],
            [
                'text' => 'Pilih semua kenyataan yang benar tentang Pewarisan (Inheritance).',
                'type' => 'checkbox', 'points' => 8,
                'options' => [
                    ['text' => 'Kelas anak mewarisi semua kaedah dari kelas induk', 'is_correct' => true],
                    ['text' => 'Kelas anak boleh mengubah suai kaedah kelas induk melalui overriding', 'is_correct' => true],
                    ['text' => 'Java menyokong pewarisan berganda', 'is_correct' => false],
                    ['text' => 'Kelas anak boleh menambah pemboleh ubah baru', 'is_correct' => true],
                ]
            ],
            [
                'text' => 'Apakah istilah untuk mengubah suai kaedah kelas induk dalam kelas anak?',
                'type' => 'short_answer', 'points' => 6,
                'correct_text' => 'Method Overriding',
            ],
            [
                'text' => 'Kata kunci `abstract` digunakan untuk mengisytiharkan kaedah atau kelas yang tidak mempunyai pelaksanaan.',
                'type' => 'true_false', 'points' => 4,
                'options' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Apakah kegunaan kaedah getter dan setter dalam pengkapsulan?',
                'type' => 'multiple_choice', 'points' => 6,
                'options' => [
                    ['text' => 'Untuk mengawal akses kepada pemboleh ubah private', 'is_correct' => true],
                    ['text' => 'Untuk mempercepatkan program', 'is_correct' => false],
                    ['text' => 'Untuk membuat kod lebih pendek', 'is_correct' => false],
                    ['text' => 'Untuk menghapus pemboleh ubah', 'is_correct' => false],
                ]
            ],
        ], // Total 8 Questions
    ],

    // ----------------------------------------------------------------------
    // QUIZ 5: Java - Program Komprehensif (2 Long Coding Questions - 30+ Lines Each)
    // ----------------------------------------------------------------------
    [
        'teacher_id' => 7,
        'title' => 'Java: Program Pengaturcaraan Komprehensif',
        'description' => 'Kuiz ini mengandungi 2 soalan pengaturcaraan jarak panjang yang menguji kefahaman mendalam tentang konsep Java.',
        'max_attempts' => 2,
        'due_at' => Carbon::now()->addDays(60),
        'is_published' => true,
        'questions' => [
            [
                'text' => 'Tulis program lengkap untuk sistem pengurusan perpustakaan mudah dengan kelas Book, Library, dan Librarian. Program mestilah dapat menambah buku, membuang buku, mencari buku mengikut tajuk, dan memaparkan senarai semua buku.',
                'type' => 'coding', 'points' => 25,
                'coding_template' => 'import java.util.ArrayList;
import java.util.Scanner;

public class Book {
    private String title;
    private String author;
    private String isbn;
    
    public Book(String title, String author, String isbn) {
        this.title = title;
        this.author = author;
        this.isbn = isbn;
    }
    
    public String getTitle() { return title; }
    public String getAuthor() { return author; }
    public String getIsbn() { return isbn; }
    
    public void displayInfo() {
        System.out.println("Tajuk: " + title + ", Pengarang: " + author + ", ISBN: " + isbn);
    }
}

public class Library {
    private ArrayList<Book> books = new ArrayList<>();
    
    // Tambahkan kaedah addBook di sini
    // Tambahkan kaedah removeBook di sini
    // Tambahkan kaedah searchBook di sini
    // Tambahkan kaedah displayAllBooks di sini
}

public class Librarian {
    public static void main(String[] args) {
        Library library = new Library();
        // Tulis program aplikasi di sini
    }
}',
                'coding_expected_output' => 'Buku "Algoritma" ditambah
Buku "Algoritma" dicari ditemui
Senarai Buku:
Tajuk: Algoritma, Pengarang: Aziz Deraman, ISBN: 123-456
Tajuk: OOP Java, Pengarang: Tan Lee, ISBN: 789-012',
            ],
            [
                'text' => 'Tulis program lengkap untuk sistem pengurusan pelajar dengan kelas Student, Course, dan Enrollment. Program mestilah dapat mendaftarkan pelajar dalam kursus, mencatat gred, mengira purata gred pelajar, dan memaparkan transkrip akademik lengkap setiap pelajar.',
                'type' => 'coding', 'points' => 25,
                'coding_template' => 'import java.util.ArrayList;
import java.util.HashMap;

public class Student {
    private String studentId;
    private String name;
    private ArrayList<String> courses;
    private HashMap<String, Double> grades;
    
    public Student(String studentId, String name) {
        this.studentId = studentId;
        this.name = name;
        this.courses = new ArrayList<>();
        this.grades = new HashMap<>();
    }
    
    public String getStudentId() { return studentId; }
    public String getName() { return name; }
    
    // Tambahkan kaedah enrollCourse di sini
    // Tambahkan kaedah setGrade di sini
    // Tambahkan kaedah getAverageGrade di sini
    // Tambahkan kaedah displayTranscript di sini
}

public class Course {
    private String courseCode;
    private String courseName;
    private int credits;
    
    public Course(String courseCode, String courseName, int credits) {
        this.courseCode = courseCode;
        this.courseName = courseName;
        this.credits = credits;
    }
    
    public String getCourseCode() { return courseCode; }
    public String getCourseName() { return courseName; }
}

public class Enrollment {
    public static void main(String[] args) {
        // Tulis kod sistem pengurusan pelajar di sini
    }
}',
                'coding_expected_output' => 'Transkrip Akademik:
Nombor Pelajar: S001
Nama: Ali bin Mohamed
Kursus: JAVA101 - Pengaturcaraan Java (3 kredit) - Gred: A (4.0)
Kursus: WEB101 - Pembangunan Web (3 kredit) - Gred: B+ (3.5)
Purata Gred Keseluruhan (GPA): 3.75',
            ],
        ], // Total 2 Long Coding Questions
    ],
];

