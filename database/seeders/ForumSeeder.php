<?php

namespace Database\Seeders;

use App\Models\ForumPost;
use App\Models\ForumReply;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ForumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Skip if forum posts already exist (from import or previous seeding)
        if (ForumPost::count() > 0) {
            echo "Forum posts already seeded. Skipping.\n";
            return;
        }

        // Skip if no users exist
        if (User::count() === 0) {
            echo "No users found. Skipping forum seeding.\n";
            return;
        }

        // Get existing teachers and students
        $teacher1 = User::where('email', 'ahmad@example.com')->first();
        $teacher2 = User::where('email', 'farah@example.com')->first();
        $student1 = User::where('email', 'ali@example.com')->first();
        $student2 = User::where('email', 'aisha@example.com')->first();
        $student3 = User::where('email', 'rajesh@example.com')->first();

        // Use fallback if users don't exist
        $user1 = $student1 ?? User::where('role', 'student')->first();
        $user2 = $teacher1 ?? User::where('role', 'teacher')->first();
        $user3 = $student2 ?? User::where('role', 'student')->where('id', '!=', $user1->id ?? 0)->first();

        // Skip if we don't have enough users
        if (!$user1 || !$user2 || !$user3) {
            echo "Not enough users for forum seeding. Skipping.\n";
            return;
        }

        // Forum Post 1: Java OOP Concepts
        $post1 = ForumPost::create([
            'title' => 'Memahami Konsep Pengaturcaraan Berorientasikan Objek dalam Java',
            'content' => 'Hai semua! Saya sedang mencuba memahami konsep teras OOP dalam Java. Bolehkah seseorang menjelaskan enkapsulasi, warisan, dan polimorfisme dengan istilah yang mudah? Saya telah membaca dokumentasi Java tetapi ia agak rumit. Contoh dunia nyata akan sangat membantu!',
            'author_id' => $user1->id,
            'author_name' => $user1->name,
            'author_avatar' => $user1->avatar ?? '/images/default-user.png'
        ]);

        // Replies for Post 1
        ForumReply::create([
            'post_id' => $post1->id,
            'reply_content' => 'Soalan yang bagus! Biarkan saya menjelaskan untuk anda:\n\n1. **Enkapsulasi**: Ia seperti meletakkan data anda dalam kapsul. Anda menyembunyikan butir-butir dalaman dan hanya menunjukkan apa yang perlu. Sebagai contoh, kelas BankAccount menyembunyikan butir-butir baki akaun dan hanya membenarkan pengeluaran dan deposit melalui kaedah.\n\n2. **Warisan**: Fikirkan ia seperti pohon keluarga. Kelas Dog boleh mewarisi dari kelas Animal, mendapatkan semua sifat dan kaedahnya.\n\n3. **Polimorfisme**: Nama kaedah yang sama, tingkah laku berbeza. Seperti kelas Shape dengan kaedah draw(). Circle, Square, dan Triangle semuanya boleh melaksanakan draw() secara berbeza.\n\nAdakah ini membantu?',
            'author_name' => $user2->name,
            'author_avatar' => $user2->avatar ?? '/images/default-user.png'
        ]);

        ForumReply::create([
            'post_id' => $post1->id,
            'reply_content' => 'Terima kasih atas penjelasannya! Ini jauh lebih masuk akal sekarang. Saya akan mencuba melaksanakan konsep-konsep ini dalam projek Java saya.',
            'author_name' => $user1->name,
            'author_avatar' => $user1->avatar ?? '/images/default-user.png'
        ]);

        // Forum Post 2: Exception Handling
        $post2 = ForumPost::create([
            'title' => 'Amalan Terbaik untuk Pengendalian Pengecualian dalam Java',
            'content' => 'Saya telah bekerja pada aplikasi Java dan saya tidak pasti tentang amalan terbaik untuk pengendalian pengecualian. Patutkah saya menangkap semua pengecualian atau hanya yang spesifik? Bilakah saya harus menggunakan blok try-catch berbanding kata kunci throws? Saya menantikan untuk belajar daripada pengalaman anda!',
            'author_id' => $user3->id,
            'author_name' => $user3->name,
            'author_avatar' => $user3->avatar ?? '/images/default-user.png'
        ]);

        // Replies for Post 2
        ForumReply::create([
            'post_id' => $post2->id,
            'reply_content' => 'Pengendalian pengecualian adalah penting untuk aplikasi yang teguh. Berikut adalah beberapa amalan terbaik:\n\n1. **Tangkap pengecualian spesifik**: Sentiasa tangkap pengecualian yang paling spesifik yang anda jangkakan, bukan hanya Exception atau Throwable.\n\n2. **Gunakan try-with-resources**: Untuk sumber seperti fail atau aliran, gunakan try-with-resources untuk memastikan ia ditutup dengan betul.\n\n3. **Jangan tangkap dan abaikan**: Jangan mempunyai blok catch kosong - sekurang-kurangnya log pengecualian.\n\n4. **Gunakan throws untuk kaedah**: Jika kaedah tidak boleh mengendalikan pengecualian, nyatakan dengan throws dan biarkan pemanggil mengendalikannya.\n\n5. **Buat pengecualian tersuai**: Untuk ralat khusus domain, cipta kelas pengecualian tersuai.',
            'author_name' => $user2->name,
            'author_avatar' => $user2->avatar ?? '/images/default-user.png'
        ]);

        // Forum Post 3: Data Structures
        $post3 = ForumPost::create([
            'title' => 'Rangka Kerja Koleksi dalam Java - Array vs ArrayList vs LinkedList',
            'content' => 'Bolehkah seseorang menjelaskan perbezaan antara Array, ArrayList, dan LinkedList? Bilakah saya harus menggunakan yang masing-masing? Saya sedang bekerja pada projek yang memerlukan penyimpanan dan akses sejumlah besar objek dengan cekap. Apakah pilihan terbaik?',
            'author_id' => $user1->id,
            'author_name' => $user1->name,
            'author_avatar' => $user1->avatar ?? '/images/default-user.png'
        ]);

        // Replies for Post 3
        ForumReply::create([
            'post_id' => $post3->id,
            'reply_content' => 'Soalan bagus! Berikut adalah perbandingan:\n\n**Array**:\n- Saiz tetap\n- Akses paling cepat (O(1))\n- Gunakan apabila: Anda tahu saiz yang tepat dan memerlukan akses rawak cepat\n\n**ArrayList**:\n- Saiz dinamik (boleh diubah saiz)\n- Akses cepat O(1)\n- Sisipan/pemadaman perlahan di awal (O(n))\n- Gunakan apabila: Anda memerlukan senarai dinamik dengan akses kerap\n\n**LinkedList**:\n- Saiz dinamik\n- Akses perlahan O(n)\n- Sisipan/pemadaman pantas (O(1)) di mana-mana kedudukan\n- Gunakan apabila: Anda memerlukan sisipan/pemadaman kerap\n\nUntuk set data besar anda, jika anda kebanyakannya membaca, ArrayList adalah terbaik. Jika anda melakukan banyak sisipan/pemadaman, LinkedList akan lebih baik.',
            'author_name' => $user2->name,
            'author_avatar' => $user2->avatar ?? '/images/default-user.png'
        ]);

        ForumReply::create([
            'post_id' => $post3->id,
            'reply_content' => 'Terima kasih! Saya akan menggunakan ArrayList kerana projek saya kebanyakannya melibatkan membaca dan kemas kini sekali-sekala.',
            'author_name' => $user1->name,
            'author_avatar' => $user1->avatar ?? '/images/default-user.png'
        ]);

        // Forum Post 4: Multithreading
        $post4 = ForumPost::create([
            'title' => 'Pengenalan kepada Multithreading dalam Java',
            'content' => 'Saya baru dalam multithreading dalam Java. Bagaimanakah cara saya membuat thread? Apakah perbezaan antara melaksanakan Runnable dan memanjangkan kelas Thread? Adakah sebarang perangkap yang perlu saya waspadai?',
            'author_id' => $user3->id,
            'author_name' => $user3->name,
            'author_avatar' => $user3->avatar ?? '/images/default-user.png'
        ]);

        // Replies for Post 4
        ForumReply::create([
            'post_id' => $post4->id,
            'reply_content' => 'Ada dua cara untuk membuat thread:\n\n**1. Panjangkan kelas Thread**:\n```java\nclass MyThread extends Thread {\n    public void run() {\n        // kod thread\n    }\n}\nMyThread t = new MyThread();\nt.start();\n```\n\n**2. Laksanakan Runnable (pilihan dipilih)**:\n```java\nclass MyRunnable implements Runnable {\n    public void run() {\n        // kod thread\n    }\n}\nThread t = new Thread(new MyRunnable());\nt.start();\n```\n\n**Mengapa Runnable lebih baik**: Java tidak menyokong pewarisan berganda. Jika anda memanjangkan Thread, anda tidak boleh memanjangkan kelas lain.\n\n**Perangkap untuk dielakkan**:\n- Jangan panggil run() secara langsung - sentiasa panggil start()\n- Berhati-hati dengan sumber bersama - gunakan sinkronisasi\n- Elakkan gelung busy-waiting\n- Tangani InterruptedException dengan betul',
            'author_name' => $user2->name,
            'author_avatar' => $user2->avatar ?? '/images/default-user.png'
        ]);

        // Forum Post 5: Design Patterns
        $post5 = ForumPost::create([
            'title' => 'Corak Reka Bentuk Biasa dalam Pembangunan Java',
            'content' => 'Apakah corak reka bentuk yang paling penting yang perlu saya ketahui sebagai pembangun Java? Saya terus mendengar tentang Singleton, Factory, Observer, dan lain-lain. Bagaimanakah mereka membantu dalam projek dunia sebenar?',
            'author_id' => $user2->id,
            'author_name' => $user2->name,
            'author_avatar' => $user2->avatar ?? '/images/default-user.png'
        ]);

        // Replies for Post 5
        ForumReply::create([
            'post_id' => $post5->id,
            'reply_content' => 'Soalan bagus! Berikut adalah corak yang paling penting:\n\n**Corak Kreatif**:\n- **Singleton**: Hanya satu kejadian (sambungan Pangkalan Data, logger)\n- **Factory**: Buat objek tanpa menentukan kelas yang tepat\n\n**Corak Struktur**:\n- **Adapter**: Buat antara muka yang tidak sesuai berfungsi bersama\n- **Decorator**: Tambah fungsi baru kepada objek secara dinamik\n\n**Corak Tingkah Laku**:\n- **Observer**: Beritahu berbagai objek tentang perubahan keadaan\n- **Strategy**: Pilih algoritma pada masa lari\n- **Command**: Enkapsulkan permintaan sebagai objek\n\nUntuk pemula, saya akan mengesyorkan belajar Singleton, Factory, dan Observer terlebih dahulu. Mereka adalah yang paling biasa digunakan dalam projek sebenar.',
            'author_name' => $user1->name,
            'author_avatar' => $user1->avatar ?? '/images/default-user.png'
        ]);

        // Forum Post 6: Computer Science Fundamentals
        $post6 = ForumPost::create([
            'title' => 'Memahami Kerumitan Masa dan Ruang dalam Sains Komputer',
            'content' => 'Saya sedang bergelut dengan notasi Big O dan analisis kerumitan. Bolehkah seseorang menjelaskan O(1), O(n), O(n²), O(log n) dalam istilah yang mudah? Bagaimanakah cara saya menentukan kerumitan algoritma?',
            'author_id' => $user3->id,
            'author_name' => $user3->name,
            'author_avatar' => $user3->avatar ?? '/images/default-user.png'
        ]);

        // Replies for Post 6
        ForumReply::create([
            'post_id' => $post6->id,
            'reply_content' => 'Notasi Big O mengukur bagaimanakah prestasi algoritma berskala dengan saiz input:\n\n**O(1) - Masa Tetap**: Mengakses elemen array mengikut indeks. Masa sentiasa sama.\n\n**O(n) - Masa Linear**: Gelung mudah melalui n elemen. Masa bertumbuh secara linear.\n\n**O(n²) - Masa Kuadratik**: Gelung bersarang. Masa bertumbuh secara eksponen.\n\n**O(log n) - Masa Logaritma**: Carian binari. Masa bertumbuh sangat perlahan.\n\n**Susunan dari paling cepat hingga paling perlahan**: O(1) < O(log n) < O(n) < O(n²) < O(2^n) < O(n!)\n\n**Bagaimanakah cara menentukan**:\n1. Kira gelung dan tahap bersarang\n2. Abaikan pemalar\n3. Simpan hanya istilah yang dominan\n\nContoh: Gelung dengan n lelaran adalah O(n). Dua gelung bersarang dengan n lelaran masing-masing adalah O(n²).',
            'author_name' => $user2->name,
            'author_avatar' => $user2->avatar ?? '/images/default-user.png'
        ]);

        ForumReply::create([
            'post_id' => $post6->id,
            'reply_content' => 'Penjelasan ini benar-benar membantu! Saya kini boleh menganalisis algoritma dengan lebih baik. Terima kasih!',
            'author_name' => $user3->name,
            'author_avatar' => $user3->avatar ?? '/images/default-user.png'
        ]);

        echo "Forum posts and replies seeded successfully!";
    }
}
