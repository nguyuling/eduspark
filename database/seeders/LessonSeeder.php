<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Skip if lessons already exist (from import or previous seeding)
        if (Lesson::count() > 0) {
            echo "Lessons already seeded. Skipping.\n";
            return;
        }

        // Get all teachers from UserSeeder
        $teachers = User::where('role', 'teacher')->get();

        if ($teachers->isEmpty()) {
            echo "No teachers found. Please run UserSeeder first.\n";
            return;
        }

        // Lesson 1: Java Fundamentals - By Cikgu Ahmad
        Lesson::create([
            'title' => 'Asas-Asas Pengaturcaraan Java',
            'description' => 'Pelajaran ini merangkumi asas-asas pengaturcaraan Java termasuk pembolehubah, jenis data, operator, dan aliran kawalan. Pelajar akan mempelajari cara menulis program Java mudah dan memahami konsep-konsep teras pengaturcaraan.',
            'file_name' => 'Java_Fundamentals_MY.pdf',
            'file_path' => 'lessons/Java_Fundamentals_MY.pdf',
            'file_ext' => 'pdf',
            'uploaded_by' => $teachers[0]->id,
            'class_group' => 'Form 4',
            'visibility' => 'public',
        ]);

        // Lesson 2: Object-Oriented Programming - By Cikgu Farah
        Lesson::create([
            'title' => 'Pengaturcaraan Berorientasikan Objek (OOP)',
            'description' => 'Memahami konsep-konsep OOP dalam Java: kelas, objek, enkapsulasi, warisan, dan polimorfisme. Pelajaran ini membantu pelajar merancang dan membina aplikasi yang modular dan mudah dikekalkan.',
            'file_name' => 'OOP_Concepts_MY.pdf',
            'file_path' => 'lessons/OOP_Concepts_MY.pdf',
            'file_ext' => 'pdf',
            'uploaded_by' => $teachers[1]->id,
            'class_group' => 'Form 4',
            'visibility' => 'public',
        ]);

        // Lesson 3: Data Structures - By Cikgu Ravi
        Lesson::create([
            'title' => 'Struktur Data dalam Java',
            'description' => 'Pelajaran komprehensif tentang struktur data: array, senarai, tindanan, baris antrian, dan pokok. Memahami struktur data adalah penting untuk mengoptimalkan kod dan meningkatkan prestasi aplikasi.',
            'file_name' => 'Data_Structures_MY.pdf',
            'file_path' => 'lessons/Data_Structures_MY.pdf',
            'file_ext' => 'pdf',
            'uploaded_by' => $teachers[2]->id,
            'class_group' => 'Form 5',
            'visibility' => 'public',
        ]);

        // Lesson 4: Exception Handling - By Cikgu Siti
        Lesson::create([
            'title' => 'Pengendalian Pengecualian dalam Java',
            'description' => 'Belajar cara menangani ralat dan pengecualian dalam Java dengan betul. Pelajaran ini mencakup try-catch-finally, custom exceptions, dan best practices untuk robust error handling.',
            'file_name' => 'Exception_Handling_MY.pdf',
            'file_path' => 'lessons/Exception_Handling_MY.pdf',
            'file_ext' => 'pdf',
            'uploaded_by' => $teachers[3]->id,
            'class_group' => 'Form 4',
            'visibility' => 'public',
        ]);

        // Lesson 5: Collections Framework - By Cikgu Budi
        Lesson::create([
            'title' => 'Rangka Kerja Koleksi Java',
            'description' => 'Mendalami Collections Framework Java: List, Set, Map, dan implementasinya. Pelajar akan belajar memilih struktur data yang tepat untuk setiap situasi dan memaksimalkan penggunaan koleksi.',
            'file_name' => 'Collections_Framework_MY.pdf',
            'file_path' => 'lessons/Collections_Framework_MY.pdf',
            'file_ext' => 'pdf',
            'uploaded_by' => $teachers[4]->id,
            'class_group' => 'Form 5',
            'visibility' => 'public',
        ]);

        // Lesson 6: Multithreading - By Cikgu Ahmad
        Lesson::create([
            'title' => 'Multithreading dalam Java',
            'description' => 'Memahami konsep multithreading: thread, runnable, sinkronisasi, dan manajemen thread. Pelajaran ini penting untuk menulis aplikasi yang responsif dan dapat menangani tugas konkuren.',
            'file_name' => 'Multithreading_MY.pdf',
            'file_path' => 'lessons/Multithreading_MY.pdf',
            'file_ext' => 'pdf',
            'uploaded_by' => $teachers[0]->id,
            'class_group' => 'Form 5',
            'visibility' => 'public',
        ]);

        // Lesson 7: File Input/Output - By Cikgu Farah
        Lesson::create([
            'title' => 'Input/Output dan Pemfailan dalam Java',
            'description' => 'Belajar membaca dan menulis fail dalam Java, bekerja dengan stream, reader, dan writer. Pelajaran ini mencakup handling fail teks dan binari, serta operasi fail dasar.',
            'file_name' => 'File_IO_MY.pdf',
            'file_path' => 'lessons/File_IO_MY.pdf',
            'file_ext' => 'pdf',
            'uploaded_by' => $teachers[1]->id,
            'class_group' => 'Form 4',
            'visibility' => 'public',
        ]);

        // Lesson 8: Database Connectivity - By Cikgu Ravi
        Lesson::create([
            'title' => 'Konektivitas Pangkalan Data dengan Java',
            'description' => 'Membuat sambungan pangkalan data menggunakan JDBC, menjalankan query, dan mengurus hasil. Pelajaran ini menunjukkan cara aplikasi Java berinteraksi dengan pangkalan data.',
            'file_name' => 'Database_Connectivity_MY.pdf',
            'file_path' => 'lessons/Database_Connectivity_MY.pdf',
            'file_ext' => 'pdf',
            'uploaded_by' => $teachers[2]->id,
            'class_group' => 'Form 5',
            'visibility' => 'public',
        ]);

        // Lesson 9: Design Patterns - By Cikgu Siti
        Lesson::create([
            'title' => 'Corak Reka Bentuk dalam Pengaturcaraan Java',
            'description' => 'Pelajaran tentang corak reka bentuk biasa: Singleton, Factory, Observer, Strategy, dan lain-lain. Memahami design patterns membantu menulis kod yang lebih baik dan boleh digunakan semula.',
            'file_name' => 'Design_Patterns_MY.pdf',
            'file_path' => 'lessons/Design_Patterns_MY.pdf',
            'file_ext' => 'pdf',
            'uploaded_by' => $teachers[3]->id,
            'class_group' => 'Form 5',
            'visibility' => 'public',
        ]);

        // Lesson 10: Algorithm Analysis - By Cikgu Budi
        Lesson::create([
            'title' => 'Analisis Algoritma dan Big O Notation',
            'description' => 'Memahami cara menganalisis efisiensi algoritma menggunakan Big O notation. Pelajaran ini penting untuk menulis kod yang efisien dan memahami trade-off antara masa dan ruang.',
            'file_name' => 'Algorithm_Analysis_MY.pdf',
            'file_path' => 'lessons/Algorithm_Analysis_MY.pdf',
            'file_ext' => 'pdf',
            'uploaded_by' => $teachers[4]->id,
            'class_group' => 'Form 5',
            'visibility' => 'public',
        ]);

        // Lesson 11: GUI Development - By Cikgu Ahmad (Restricted to class)
        Lesson::create([
            'title' => 'Pembangunan Antara Muka Pengguna dengan Swing',
            'description' => 'Belajar membina antara muka pengguna grafis menggunakan Swing. Pelajaran ini mencakup komponen, layout managers, event handling, dan cara mebuat aplikasi desktop yang interaktif.',
            'file_name' => 'Swing_GUI_MY.pdf',
            'file_path' => 'lessons/Swing_GUI_MY.pdf',
            'file_ext' => 'pdf',
            'uploaded_by' => $teachers[0]->id,
            'class_group' => 'Form 4',
            'visibility' => 'class',
        ]);

        // Lesson 12: Web Services with Java - By Cikgu Farah (Restricted to class)
        Lesson::create([
            'title' => 'Perkhidmatan Web dengan Java',
            'description' => 'Pengenalan kepada pembangunan perkhidmatan web menggunakan Java. Pelajaran ini mencakup REST API, JSON, HTTP, dan cara membina backend yang scalable untuk aplikasi web.',
            'file_name' => 'Web_Services_MY.pdf',
            'file_path' => 'lessons/Web_Services_MY.pdf',
            'file_ext' => 'pdf',
            'uploaded_by' => $teachers[1]->id,
            'class_group' => 'Form 5',
            'visibility' => 'class',
        ]);

        echo "Lesson materials seeded successfully!\n";
    }
}
