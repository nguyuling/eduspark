<?php

use Illuminate\Support\Carbon;

return [
    // ----------------------------------------------------------------------
    // QUIZ 1: Java - Asas Sintaks dan Jenis Data (Based on Form 4/Basic Java)
    // ----------------------------------------------------------------------
    [
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
];