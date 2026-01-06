<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AIChatController extends Controller
{
    /**
     * Send message to AI Assistant and get response
     * This is an isolated AI chat endpoint
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $userMessage = $request->input('message');
        $userId = auth()->id();

        // Generate AI response
        $aiResponse = $this->generateAIResponse($userMessage);

        // Return response
        return response()->json([
            'success' => true,
            'reply' => $aiResponse,
            'timestamp' => now(),
        ]);
    }

    /**
     * Generate AI response based on message
     * Currently uses keyword matching and predefined responses
     * Can be replaced with OpenAI API integration
     */
    private function generateAIResponse($message)
    {
        $messageLower = strtolower($message);
        $isMalay = $this->isMalay($messageLower);

        // Java Fundamentals
        if ($this->contains($messageLower, ['variable', 'data type', 'int', 'string', 'boolean', 'double', 'float']) ||
            $this->contains($messageLower, ['pembolehubah', 'jenis data', 'integer', 'rentetan', 'boolean', 'nombor perpuluhan'])) {
            if ($isMalay) {
                return $this->getRandomResponse([
                    "Pembolehubah dalam Java ialah bekas untuk menyimpan data. Anda perlu nyatakan jenis data seperti int, String, boolean, dan berikan nama. Contoh: `int umur = 25;` mencipta pembolehubah integer bernama 'umur' dengan nilai 25.",
                    "Jenis data dalam Java menentukan jenis nilai yang boleh disimpan. Jenis asas ialah int, double, boolean, char. Jenis rujukan ialah String, array, dan objek.",
                    "Dalam Java, anda mesti nyatakan jenis setiap pembolehubah. Jenis biasa: `int` untuk nombor bulat, `double` untuk perpuluhan, `String` untuk teks, `boolean` untuk benar/palsu.",
                ]);
            } else {
                return $this->getRandomResponse([
                    "Variables in Java are containers that hold data. Think of them like labeled boxes. You declare a variable by specifying its type (int, String, boolean, etc.) and giving it a name. For example: `int age = 25;` creates an integer variable named 'age' with value 25.",
                    "Data types in Java define what kind of data a variable can hold. There are primitive types (int, double, boolean, char) for basic values, and reference types (String, arrays, objects) for complex objects.",
                    "In Java, you must declare the type of each variable. Common types are: `int` for whole numbers, `double` for decimals, `String` for text, and `boolean` for true/false values.",
                ]);
            }
        }

        // Object-Oriented Programming
        if ($this->contains($messageLower, ['class', 'object', 'inheritance', 'polymorphism', 'encapsulation', 'oop']) ||
            $this->contains($messageLower, ['kelas', 'objek', 'pewarisan', 'polimorfisme', 'enkapsulasi', 'oop'])) {
            if ($isMalay) {
                return $this->getRandomResponse([
                    "Kelas ialah pelan untuk mencipta objek. Ia menentukan sifat (pembolehubah) dan kaedah (fungsi) yang objek akan ada. Contoh, kelas 'Kereta' menentukan bagaimana semua kereta berfungsi.",
                    "Objek ialah contoh kepada kelas. Jika kelas ialah pelan, objek ialah bangunan sebenar. Anda cipta objek dengan: `Kereta keretaSaya = new Kereta();`",
                    "Pewarisan membenarkan satu kelas mewarisi sifat dan kaedah dari kelas lain. Contoh, 'Kereta' dan 'Basikal' boleh mewarisi dari kelas 'Kenderaan'.",
                    "Polimorfisme bermaksud 'banyak bentuk'. Ia membenarkan objek mempunyai pelbagai bentuk. Contoh, haiwan berbeza boleh ada kaedah 'buatBunyi()', tetapi bunyinya berbeza.",
                    "Enkapsulasi menyembunyikan butiran dalaman objek dan hanya dedahkan yang perlu. Guna `private` untuk pembolehubah tersembunyi dan `public` untuk kaedah yang boleh digunakan.",
                ]);
            } else {
                return $this->getRandomResponse([
                    "A class is like a blueprint for creating objects. It defines properties (variables) and methods (functions) that objects will have. For example, a 'Car' class defines how all cars should behave.",
                    "An object is an instance of a class. If the class is a blueprint, the object is the actual building made from that blueprint. You create objects using: `Car myCar = new Car();`",
                    "Inheritance allows one class to inherit properties and methods from another class. For example, 'Car' and 'Bike' can both inherit from a 'Vehicle' class.",
                    "Polymorphism means 'many forms'. It allows objects to take multiple forms. For example, you can have different animals that all have a 'makeSound()' method, but each sounds different.",
                    "Encapsulation hides the internal details of an object and only exposes what's necessary. Use `private` for hidden variables and `public` for methods others can use.",
                ]);
            }
        }

        // Loops and Control Structures
        if ($this->contains($messageLower, ['loop', 'for', 'while', 'if', 'else', 'switch', 'condition', 'iteration']) ||
            $this->contains($messageLower, ['gelung', 'untuk', 'sementara', 'jika', 'selain', 'tukar', 'syarat', 'iterasi'])) {
            if ($isMalay) {
                return $this->getRandomResponse([
                    "Gelung `for` menjalankan kod beberapa kali. Contoh: `for(int i = 0; i < 10; i++)` berulang 10 kali, i dari 0 hingga 9.",
                    "Gelung `while` berulang selagi syarat benar. Contoh: `while(x > 0)` berulang sehingga x menjadi 0 atau kurang.",
                    "Penyataan `if-else` membuat keputusan. Jika syarat benar, satu kod dijalankan; jika tidak, kod lain dijalankan. Contoh: `if(umur >= 18) { dewasa } else { kanak-kanak }`",
                    "Penyataan `switch` memeriksa satu pembolehubah terhadap beberapa nilai. Ia lebih kemas daripada banyak if-else.",
                ]);
            } else {
                return $this->getRandomResponse([
                    "A `for` loop runs a block of code a specific number of times. Example: `for(int i = 0; i < 10; i++)` runs 10 times, with i going from 0 to 9.",
                    "A `while` loop keeps running as long as a condition is true. Example: `while(x > 0)` keeps running until x becomes 0 or less.",
                    "An `if-else` statement makes decisions. If the condition is true, it runs one block of code; otherwise, it runs another. Example: `if(age >= 18) { adult } else { child }`",
                    "A `switch` statement checks one variable against multiple values. It's cleaner than many if-else statements when checking one value against many options.",
                ]);
            }
        }

        // Methods and Constructors
        if ($this->contains($messageLower, ['method', 'function', 'constructor', 'return', 'parameter']) ||
            $this->contains($messageLower, ['kaedah', 'fungsi', 'pembina', 'kembali', 'parameter'])) {
            if ($isMalay) {
                return $this->getRandomResponse([
                    "Kaedah ialah blok kod untuk tugas tertentu. Anda definisikan dalam kelas dan panggil bila perlu. Kaedah membantu susun kod dan elak pengulangan.",
                    "Pembina ialah kaedah khas yang dijalankan bila objek dicipta. Ia digunakan untuk inisialisasi pembolehubah. Nama pembina sama dengan kelas dan tiada jenis pulangan.",
                    "Parameter ialah input kepada kaedah. Contoh: `public void sapa(String nama)` ada satu parameter 'nama'. Bila panggil, beri nilai: `sapa('Ali')`",
                    "Penyataan kembali (`return`) menghantar nilai dari kaedah. Jika kaedah pulang `int`, mesti pulang integer. Jika `void`, tiada pulangan.",
                ]);
            } else {
                return $this->getRandomResponse([
                    "A method is a block of code that performs a specific task. You define it in a class and call it whenever needed. Methods help organize code and prevent repetition.",
                    "A constructor is a special method that runs when you create an object. It's used to initialize variables. It has the same name as the class and no return type.",
                    "Parameters are inputs to a method. For example: `public void greet(String name)` has one parameter called 'name'. When you call it, you provide the value: `greet('Ali')`",
                    "A return statement sends a value back from a method. If a method returns `int`, it must send back an integer. If it returns `void`, it doesn't return anything.",
                ]);
            }
        }

        // Exception Handling
        if ($this->contains($messageLower, ['exception', 'error', 'try', 'catch', 'throw', 'finally']) ||
            $this->contains($messageLower, ['pengecualian', 'ralat', 'cuba', 'tangkap', 'lempar', 'akhirnya'])) {
            if ($isMalay) {
                return $this->getRandomResponse([
                    "Pengecualian ialah ralat semasa program berjalan. Untuk tangani, guna try-catch: `try { kod berisiko } catch(Exception e) { tangani ralat }`",
                    "Blok `try` mengandungi kod yang mungkin sebabkan ralat. Blok `catch` tangani ralat jika berlaku. Blok `finally` sentiasa dijalankan.",
                    "Pengecualian biasa: `NullPointerException` (akses null), `ArrayIndexOutOfBoundsException` (indeks array tidak sah), `InputMismatchException` (jenis input salah).",
                ]);
            } else {
                return $this->getRandomResponse([
                    "An exception is an error that happens during program execution. To handle it gracefully, use try-catch: `try { risky code } catch(Exception e) { handle error }`",
                    "The `try` block contains code that might cause an error. The `catch` block handles that error if it occurs. The `finally` block runs regardless of whether an error happened.",
                    "Common exceptions: `NullPointerException` (accessing null), `ArrayIndexOutOfBoundsException` (invalid array index), `InputMismatchException` (wrong input type).",
                ]);
            }
        }

        // Collections
        if ($this->contains($messageLower, ['arraylist', 'hashmap', 'collection', 'array', 'list', 'map', 'set']) ||
            $this->contains($messageLower, ['arraylist', 'hashmap', 'koleksi', 'array', 'senarai', 'peta', 'set'])) {
            if ($isMalay) {
                return $this->getRandomResponse([
                    "`ArrayList` ialah array dinamik yang boleh membesar secara automatik. Guna bila anda tidak tahu saiz awal: `ArrayList<String> senarai = new ArrayList<>();`",
                    "`HashMap` menyimpan pasangan kunci-nilai seperti kamus. Anda cari nilai berdasarkan kunci: `HashMap<String, Integer> peta = new HashMap<>();`",
                    "Array bersaiz tetap, tetapi Koleksi seperti ArrayList lebih fleksibel. Array guna sintaks `[]`, Koleksi guna kaedah seperti `add()`, `get()`, `remove()`.",
                ]);
            } else {
                return $this->getRandomResponse([
                    "An `ArrayList` is like a dynamic array that grows automatically. Use it when you don't know the size in advance: `ArrayList<String> list = new ArrayList<>();`",
                    "A `HashMap` stores key-value pairs like a dictionary. You look up values by their key: `HashMap<String, Integer> map = new HashMap<>();`",
                    "Arrays have fixed size, but Collections like ArrayList are flexible. Arrays use `[]` syntax, Collections use methods like `add()`, `get()`, `remove()`.",
                ]);
            }
        }

        // Algorithms and Logic
        if ($this->contains($messageLower, ['algorithm', 'sort', 'search', 'logic', 'complexity', 'recursion']) ||
            $this->contains($messageLower, ['algoritma', 'susun', 'cari', 'logik', 'kerumitan', 'rekursi'])) {
            if ($isMalay) {
                return $this->getRandomResponse([
                    "Algoritma ialah langkah demi langkah untuk selesaikan masalah. Algoritma yang baik adalah cekap (pantas) dan guna memori minimum.",
                    "Rekursi ialah kaedah di mana fungsi memanggil dirinya sendiri. Berguna untuk masalah berulang seperti pokok atau pengiraan faktorial.",
                    "Algoritma biasa: susunan (mengatur data), carian (mencari data), traversal (melawat semua elemen).",
                ]);
            } else {
                return $this->getRandomResponse([
                    "An algorithm is a step-by-step solution to a problem. Good algorithms are efficient (fast) and use minimal memory.",
                    "Recursion is when a method calls itself. It's useful for problems that have a repeating structure (like tree navigation or factorial calculation).",
                    "Common algorithms: sorting (arranging data), searching (finding data), traversal (visiting all elements).",
                ]);
            }
        }

        // Error Message Help
        if ($this->contains($messageLower, ['nullpointer', 'cannot find symbol', 'unexpected token', 'error', 'exception', 'compile', 'bug']) ||
            $this->contains($messageLower, ['nullpointer', 'tidak jumpa simbol', 'token tidak dijangka', 'ralat', 'pengecualian', 'kompil', 'pepijat'])) {
            if ($isMalay) {
                return $this->getRandomResponse([
                    "NullPointerException berlaku bila anda cuba guna sesuatu yang kosong (null). Pastikan objek diinisialisasi sebelum digunakan.",
                    "'Tidak jumpa simbol' bermaksud Java tidak kenal nama pembolehubah, kaedah, atau kelas. Semak ejaan dan pastikan ia diisytiharkan.",
                    "'Token tidak dijangka' ialah ralat sintaks. Java tidak faham struktur kod anda. Semak kurungan, titik koma, dan ejaan.",
                    "Ralat dan pengecualian berbeza: ralat masa kompilasi hentikan kod dari berjalan; pengecualian berlaku semasa program berjalan.",
                ]);
            } else {
                return $this->getRandomResponse([
                    "NullPointerException happens when you try to use something that's null (empty). Check if your object is properly initialized before using it.",
                    "'Cannot find symbol' means Java doesn't recognize a variable, method, or class name. Check for typos and make sure the item is declared.",
                    "'Unexpected token' is a syntax error. Java doesn't understand your code structure. Check brackets, semicolons, and spelling.",
                    "Errors and exceptions are different: compile-time errors stop the code from running; runtime exceptions happen while the program runs.",
                ]);
            }
        }

        // General help
        if ($this->contains($messageLower, ['help', 'hello', 'hi', 'what', 'how', 'can you', 'explain']) ||
            $this->contains($messageLower, ['bantu', 'helo', 'hai', 'apa', 'bagaimana', 'boleh', 'terangkan'])) {
            if ($isMalay) {
                return $this->getRandomResponse([
                    "👋 Hai! Saya EduSpark AI Assistant. Saya boleh bantu anda dengan konsep pengaturcaraan Java. Tanya saya tentang pembolehubah, kelas, gelung, kaedah, pengecualian, koleksi dan banyak lagi!",
                    "Saya boleh terangkan asas Java, konsep OOP, struktur kawalan, kaedah, mesej ralat, dan algoritma asas. Apa yang anda ingin pelajari?",
                ]);
            } else {
                return $this->getRandomResponse([
                    "👋 Hello! I'm the EduSpark AI Assistant. I'm here to help you with Java programming concepts. Ask me about variables, classes, loops, methods, exceptions, collections, and more!",
                    "I can help explain Java fundamentals, OOP concepts, control structures, methods, error messages, and basic algorithms. What would you like to learn?",
                ]);
            }
        }

        // Out of scope response
        if ($isMalay) {
            return $this->getRandomResponse([
                "Saya pakar dalam pengaturcaraan Java. Saya boleh bantu dengan: sintaks Java, OOP, gelung, kaedah, pengecualian, koleksi, dan penjelasan ralat. Ada topik Java yang anda ingin tahu?",
                "Itu di luar kepakaran saya. Saya fokus pada topik pengaturcaraan Java. Boleh saya bantu dengan sesuatu berkaitan Java?",
                "Saya di sini untuk bantu dengan konsep Java. Sila tanya apa-apa tentang Java! 📚",
            ]);
        } else {
            return $this->getRandomResponse([
                "I'm specialized in Java programming. I can help with: Java syntax, OOP, loops, methods, exceptions, collections, and error explanations. Is there a Java topic you'd like help with?",
                "That's outside my expertise. I focus on Java programming topics. Can I help you with something Java-related?",
                "I'm here to help with Java concepts. Feel free to ask me about anything Java! 📚",
            ]);
        }
    }

    /**
     * Generate a general response for any topic (simple placeholder)
     */
    private function generateGeneralResponse($message, $isMalay)
    {
        // For now, just echo the question back, or provide a generic message
        if ($isMalay) {
            return "(Maaf, saya hanya AI asas. Jawapan saya mungkin terhad. Anda bertanya: '" . $message . "')";
        } else {
            return "(Sorry, I'm a basic AI. My answer may be limited. You asked: '" . $message . "')";
        }
    }

    /**
     * Detect if message is in Malay (simple keyword-based)
     */
    private function isMalay($message)
    {
        $malayWords = [
            'apa', 'bagaimana', 'boleh', 'terangkan', 'contoh', 'pembolehubah', 'jenis data', 'kelas', 'objek', 'pewarisan', 'polimorfisme', 'enkapsulasi',
            'gelung', 'untuk', 'sementara', 'jika', 'selain', 'tukar', 'syarat', 'iterasi', 'kaedah', 'fungsi', 'pembina', 'parameter', 'kembali',
            'pengecualian', 'ralat', 'cuba', 'tangkap', 'lempar', 'akhirnya', 'koleksi', 'senarai', 'peta', 'set', 'algoritma', 'susun', 'cari', 'logik', 'kerumitan', 'rekursi',
            'nullpointer', 'tidak jumpa simbol', 'token tidak dijangka', 'kompil', 'pepijat', 'bantu', 'helo', 'hai', 'saya', 'anda', 'jawapan', 'soalan', 'pelajar', 'guru', 'pengaturcaraan', 'java'
        ];
        foreach ($malayWords as $word) {
            if (strpos($message, $word) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if message contains any of the keywords
     */
    private function contains($message, $keywords)
    {
        foreach ($keywords as $keyword) {
            if (strpos($message, strtolower($keyword)) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get random response from array
     */
    private function getRandomResponse($responses)
    {
        return $responses[array_rand($responses)];
    }
}
