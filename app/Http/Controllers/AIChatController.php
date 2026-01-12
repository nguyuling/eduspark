<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AIChatController extends Controller
{
    /**
     * Send message to AI Assistant and get response
     * This is an isolated AI chat endpoint with enhanced intelligence
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'context' => 'nullable|array', // Previous conversation context
        ]);

        $userMessage = $request->input('message');
        $context = $request->input('context', []);
        $userId = auth()->id();

        // Generate enhanced AI response with context
        $aiResponse = $this->generateSmartResponse($userMessage, $context);

        // Return response with metadata
        return response()->json([
            'success' => true,
            'reply' => $aiResponse['message'],
            'type' => $aiResponse['type'], // text, code, example, quiz
            'code' => $aiResponse['code'] ?? null,
            'timestamp' => now(),
        ]);
    }

    /**
     * Generate intelligent AI response with context awareness
     */
    private function generateSmartResponse($message, $context = [])
    {
        $messageLower = strtolower($message);
        $isMalay = $this->isMalay($messageLower);
        
        // Analyze user intent
        $intent = $this->analyzeIntent($messageLower);
        
        // Handle different intents
        switch($intent) {
            case 'code_request':
                return $this->generateCodeResponse($messageLower, $isMalay);
            case 'debug_help':
                return $this->generateDebugHelp($messageLower, $isMalay);
            case 'concept_explanation':
                return $this->generateConceptExplanation($messageLower, $isMalay);
            case 'practice_quiz':
                return $this->generatePracticeQuestion($messageLower, $isMalay);
            default:
                return $this->generateGeneralAIResponse($messageLower, $isMalay, $context);
        }
    }
    
    /**
     * Analyze user's intent from message
     */
    private function analyzeIntent($message)
    {
        // Code generation requests
        if ($this->contains($message, ['write code', 'show code', 'code example', 'how to code', 'tulis kod', 'tunjuk kod', 'contoh kod'])) {
            return 'code_request';
        }
        
        // Debugging help
        if ($this->contains($message, ['error', 'bug', 'fix', 'debug', 'not working', 'ralat', 'pepijat', 'betul', 'tak jalan'])) {
            return 'debug_help';
        }
        
        // Practice/quiz
        if ($this->contains($message, ['practice', 'quiz', 'test', 'exercise', 'question', 'latihan', 'kuiz', 'ujian', 'latih', 'soalan'])) {
            return 'practice_quiz';
        }
        
        // Concept explanation
        if ($this->contains($message, ['what is', 'explain', 'how', 'why', 'apa itu', 'terangkan', 'bagaimana', 'kenapa'])) {
            return 'concept_explanation';
        }
        
        return 'general';
    }
    
    /**
     * Generate code examples based on request
     */
    private function generateCodeResponse($message, $isMalay)
    {
        // Calculator
        if ($this->contains($message, ['calculator', 'kalkulator', 'tambah', 'add', 'subtract'])) {
            $code = "public class Calculator {\n    public int add(int a, int b) {\n        return a + b;\n    }\n    \n    public int subtract(int a, int b) {\n        return a - b;\n    }\n    \n    public static void main(String[] args) {\n        Calculator calc = new Calculator();\n        System.out.println(\"5 + 3 = \" + calc.add(5, 3));\n        System.out.println(\"5 - 3 = \" + calc.subtract(5, 3));\n    }\n}";
            
            return [
                'message' => $isMalay 
                    ? "Ini contoh kelas Calculator mudah dengan kaedah tambah dan tolak:" 
                    : "Here's a simple Calculator class with add and subtract methods:",
                'type' => 'code',
                'code' => $code
            ];
        }
        
        // Array/List manipulation
        if ($this->contains($message, ['array', 'arraylist', 'list', 'senarai'])) {
            $code = "import java.util.ArrayList;\n\npublic class ListExample {\n    public static void main(String[] args) {\n        // Create ArrayList\n        ArrayList<String> fruits = new ArrayList<>();\n        \n        // Add items\n        fruits.add(\"Apple\");\n        fruits.add(\"Banana\");\n        fruits.add(\"Orange\");\n        \n        // Print all items\n        for(String fruit : fruits) {\n            System.out.println(fruit);\n        }\n        \n        // Get specific item\n        System.out.println(\"First: \" + fruits.get(0));\n        \n        // Remove item\n        fruits.remove(\"Banana\");\n        System.out.println(\"Size: \" + fruits.size());\n    }\n}";
            
            return [
                'message' => $isMalay
                    ? "Ini contoh menggunakan ArrayList untuk simpan dan manipulasi data:"
                    : "Here's an example using ArrayList to store and manipulate data:",
                'type' => 'code',
                'code' => $code
            ];
        }
        
        // Loop examples
        if ($this->contains($message, ['loop', 'gelung', 'for', 'while', 'iteration', 'ulang'])) {
            $code = "public class LoopExamples {\n    public static void main(String[] args) {\n        // For loop\n        System.out.println(\"For loop:\");\n        for(int i = 1; i <= 5; i++) {\n            System.out.println(\"Count: \" + i);\n        }\n        \n        // While loop\n        System.out.println(\"\\nWhile loop:\");\n        int x = 1;\n        while(x <= 5) {\n            System.out.println(\"Value: \" + x);\n            x++;\n        }\n        \n        // Enhanced for loop (for arrays)\n        System.out.println(\"\\nEnhanced for:\");\n        int[] numbers = {10, 20, 30, 40, 50};\n        for(int num : numbers) {\n            System.out.println(num);\n        }\n    }\n}";
            
            return [
                'message' => $isMalay
                    ? "Ini contoh 3 jenis gelung dalam Java - for, while, dan enhanced for:"
                    : "Here are 3 types of loops in Java - for, while, and enhanced for:",
                'type' => 'code',
                'code' => $code
            ];
        }
        
        // Class/Object example
        if ($this->contains($message, ['class', 'object', 'kelas', 'objek', 'oop'])) {
            $code = "// Define a Student class\npublic class Student {\n    // Properties\n    private String name;\n    private int age;\n    private double grade;\n    \n    // Constructor\n    public Student(String name, int age) {\n        this.name = name;\n        this.age = age;\n        this.grade = 0.0;\n    }\n    \n    // Methods\n    public void setGrade(double grade) {\n        this.grade = grade;\n    }\n    \n    public void displayInfo() {\n        System.out.println(\"Name: \" + name);\n        System.out.println(\"Age: \" + age);\n        System.out.println(\"Grade: \" + grade);\n    }\n    \n    public static void main(String[] args) {\n        Student student1 = new Student(\"Ali\", 20);\n        student1.setGrade(85.5);\n        student1.displayInfo();\n    }\n}";
            
            return [
                'message' => $isMalay
                    ? "Ini contoh kelas Student lengkap dengan constructor, properties dan methods:"
                    : "Here's a complete Student class with constructor, properties and methods:",
                'type' => 'code',
                'code' => $code
            ];
        }
        
        // Default code example
        $code = "public class HelloWorld {\n    public static void main(String[] args) {\n        System.out.println(\"Hello, World!\");\n    }\n}";
        
        return [
            'message' => $isMalay
                ? "Ini contoh program Java asas. Beritahu saya apa kod anda perlukan!"
                : "Here's a basic Java program. Tell me what code you need!",
            'type' => 'code',
            'code' => $code
        ];
    }
    
    /**
     * Generate debugging help
     */
    private function generateDebugHelp($message, $isMalay)
    {
        if ($this->contains($message, ['nullpointer', 'null pointer'])) {
            return [
                'message' => $isMalay
                    ? "🔍 **NullPointerException** berlaku bila anda cuba guna objek yang null.\n\n**Punca biasa:**\n1. Objek tidak diinisialisasi\n2. Method pulang null\n3. Array element kosong\n\n**Penyelesaian:**\n```java\n// Semak null sebelum guna\nif(object != null) {\n    object.doSomething();\n}\n```"
                    : "🔍 **NullPointerException** happens when you try to use an object that is null.\n\n**Common causes:**\n1. Object not initialized\n2. Method returned null\n3. Array element is empty\n\n**Solution:**\n```java\n// Check for null before using\nif(object != null) {\n    object.doSomething();\n}\n```",
                'type' => 'text'
            ];
        }
        
        if ($this->contains($message, ['cannot find symbol', 'tidak jumpa'])) {
            return [
                'message' => $isMalay
                    ? "🔍 **Cannot find symbol** bermaksud Java tidak kenal nama yang anda tulis.\n\n**Semak:**\n✓ Ejaan betul?\n✓ Variable/method sudah declare?\n✓ Import statement ada?\n✓ Typo dalam nama?\n\n**Contoh masalah:**\n```java\nString name = \"Ali\";\nSystem.out.println(nama); // Salah! Patut 'name'\n```"
                    : "🔍 **Cannot find symbol** means Java doesn't recognize the name you wrote.\n\n**Check:**\n✓ Correct spelling?\n✓ Variable/method declared?\n✓ Import statement present?\n✓ Typo in name?\n\n**Example problem:**\n```java\nString name = \"Ali\";\nSystem.out.println(nama); // Wrong! Should be 'name'\n```",
                'type' => 'text'
            ];
        }
        
        if ($this->contains($message, ['arrayindex', 'out of bounds', 'index'])) {
            return [
                'message' => $isMalay
                    ? "🔍 **ArrayIndexOutOfBoundsException** berlaku bila akses index yang tidak wujud.\n\n**Ingat:** Array bermula dari index 0!\n\n```java\nint[] numbers = {10, 20, 30}; // Size 3\n// Valid: index 0, 1, 2\n// Invalid: index 3 or higher\n\n// Elak error:\nfor(int i = 0; i < numbers.length; i++) {\n    System.out.println(numbers[i]);\n}\n```"
                    : "🔍 **ArrayIndexOutOfBoundsException** occurs when accessing a non-existent index.\n\n**Remember:** Arrays start at index 0!\n\n```java\nint[] numbers = {10, 20, 30}; // Size 3\n// Valid: index 0, 1, 2\n// Invalid: index 3 or higher\n\n// Prevent error:\nfor(int i = 0; i < numbers.length; i++) {\n    System.out.println(numbers[i]);\n}\n```",
                'type' => 'text'
            ];
        }
        
        return [
            'message' => $isMalay
                ? "Beritahu saya error message yang anda dapat, saya akan bantu debug! Contoh: 'NullPointerException', 'Cannot find symbol', etc."
                : "Tell me the error message you're getting, and I'll help debug! Example: 'NullPointerException', 'Cannot find symbol', etc.",
            'type' => 'text'
        ];
    }
    
    /**
     * Generate practice questions
     */
    private function generatePracticeQuestion($message, $isMalay)
    {
        $questions = [
            [
                'malay' => "📝 **Latihan: Variables**\n\nSoalan: Cipta variable untuk simpan umur pelajar (integer), nama (String), dan lulus/gagal (boolean).\n\nCuba tulis kod, kemudian saya akan beri jawapan!",
                'english' => "📝 **Practice: Variables**\n\nQuestion: Create variables to store student age (integer), name (String), and pass/fail status (boolean).\n\nTry writing the code, then I'll give you the answer!"
            ],
            [
                'malay' => "📝 **Latihan: Loops**\n\nSoalan: Tulis program yang cetak nombor 1 hingga 10 menggunakan for loop.\n\nCuba dulu, lepas tu saya tunjuk jawapan!",
                'english' => "📝 **Practice: Loops**\n\nQuestion: Write a program that prints numbers 1 to 10 using a for loop.\n\nTry it first, then I'll show the answer!"
            ],
            [
                'malay' => "📝 **Latihan: Methods**\n\nSoalan: Cipta method bernama 'isEven' yang terima integer dan pulang true jika nombor genap, false jika ganjil.\n\nCuba buat sendiri dulu!",
                'english' => "📝 **Practice: Methods**\n\nQuestion: Create a method called 'isEven' that takes an integer and returns true if the number is even, false if odd.\n\nTry it yourself first!"
            ]
        ];
        
        $randomQ = $questions[array_rand($questions)];
        
        return [
            'message' => $isMalay ? $randomQ['malay'] : $randomQ['english'],
            'type' => 'quiz'
        ];
    }
    
    /**
     * Enhanced concept explanation with examples
     */
    private function generateConceptExplanation($message, $isMalay)
    {
        $messageLower = strtolower($message);
        return $this->generateDetailedResponse($messageLower, $isMalay);
    }
    
    /**
     * Generate general intelligent response with context
     */
    private function generateGeneralAIResponse($message, $isMalay, $context = [])
    {
        $messageLower = strtolower($message);
        return $this->generateDetailedResponse($messageLower, $isMalay);
    }
    
    /**
     * Generate detailed response with enhanced explanations
     */
    private function generateDetailedResponse($messageLower, $isMalay)
    {
        // What is Java - Core explanation
        if ($this->contains($messageLower, ['what is java', 'what\'s java', 'apa itu java', 'apakah java'])) {
            if ($isMalay) {
                return [
                    'message' => "☕ **Apa itu Java?**\n\nJava adalah bahasa pengaturcaraan yang popular dan berkuasa!\n\n**Kenapa belajar Java?**\n✅ Digunakan oleh syarikat besar (Google, Netflix, Amazon)\n✅ Platform-independent (Write Once, Run Anywhere)\n✅ Bahasa berorientasikan objek (OOP)\n✅ Selamat dan stabil untuk aplikasi besar\n✅ Banyak resources dan komuniti besar\n\n**Java digunakan untuk:**\n🔹 Aplikasi Android\n🔹 Aplikasi web enterprise\n🔹 Software backend\n🔹 Game dan aplikasi desktop\n\n**Contoh kod Java mudah:**\n```java\npublic class HelloWorld {\n    public static void main(String[] args) {\n        System.out.println(\"Hello, Java!\");\n    }\n}\n```\n\nMula belajar dengan basic syntax dan OOP concepts! 🚀",
                    'type' => 'text'
                ];
            } else {
                return [
                    'message' => "☕ **What is Java?**\n\nJava is a popular and powerful programming language!\n\n**Why learn Java?**\n✅ Used by major companies (Google, Netflix, Amazon)\n✅ Platform-independent (Write Once, Run Anywhere)\n✅ Object-oriented programming (OOP)\n✅ Safe and stable for large applications\n✅ Extensive resources and large community\n\n**Java is used for:**\n🔹 Android applications\n🔹 Enterprise web applications\n🔹 Backend software\n🔹 Games and desktop applications\n\n**Simple Java code example:**\n```java\npublic class HelloWorld {\n    public static void main(String[] args) {\n        System.out.println(\"Hello, Java!\");\n    }\n}\n```\n\nStart learning with basic syntax and OOP concepts! 🚀",
                    'type' => 'text'
                ];
            }
        }

        // Java Fundamentals - Enhanced
        if ($this->contains($messageLower, ['variable', 'data type', 'int', 'string', 'boolean', 'double', 'float']) ||
            $this->contains($messageLower, ['pembolehubah', 'jenis data', 'integer', 'rentetan', 'boolean', 'nombor perpuluhan'])) {
            if ($isMalay) {
                return [
                    'message' => "💡 **Pembolehubah & Jenis Data**\n\nPembolehubah seperti bekas untuk simpan data. Java ada beberapa jenis:\n\n✅ **int** - Nombor bulat (contoh: 25, -10, 0)\n✅ **double** - Nombor perpuluhan (contoh: 3.14, -0.5)\n✅ **String** - Teks (contoh: \"Hello\", \"Ali\")\n✅ **boolean** - Benar/Palsu (true/false)\n✅ **char** - Satu huruf (contoh: 'A', '5')\n\n**Contoh:**\n```java\nint umur = 20;\ndouble harga = 49.99;\nString nama = \"Ali\";\nboolean lulus = true;\n```\n\nCuba tulis kod sendiri! 🚀",
                    'type' => 'text'
                ];
            } else {
                return [
                    'message' => "💡 **Variables & Data Types**\n\nVariables are like containers that hold data. Java has several types:\n\n✅ **int** - Whole numbers (e.g., 25, -10, 0)\n✅ **double** - Decimal numbers (e.g., 3.14, -0.5)\n✅ **String** - Text (e.g., \"Hello\", \"Ali\")\n✅ **boolean** - True/False values\n✅ **char** - Single character (e.g., 'A', '5')\n\n**Example:**\n```java\nint age = 20;\ndouble price = 49.99;\nString name = \"Ali\";\nboolean passed = true;\n```\n\nTry writing your own code! 🚀",
                    'type' => 'text'
                ];
            }
        }

        // Object-Oriented Programming - Enhanced
        if ($this->contains($messageLower, ['class', 'object', 'inheritance', 'polymorphism', 'encapsulation', 'oop']) ||
            $this->contains($messageLower, ['kelas', 'objek', 'pewarisan', 'polimorfisme', 'enkapsulasi', 'oop'])) {
            if ($isMalay) {
                return [
                    'message' => "💡 **OOP (Pengaturcaraan Berorientasikan Objek)**\n\n🔹 **Kelas** - Pelan untuk cipta objek\n🔹 **Objek** - Instance dari kelas\n🔹 **Pewarisan** - Kelas anak warisi sifat kelas induk\n🔹 **Polimorfisme** - Satu kaedah, banyak bentuk\n🔹 **Enkapsulasi** - Sembunyikan data dalaman\n\n**Contoh mudah:**\n```java\nclass Kereta {\n    private String warna;\n    private int kelajuan;\n    \n    public Kereta(String w) {\n        warna = w;\n        kelajuan = 0;\n    }\n    \n    public void pandu() {\n        kelajuan += 10;\n    }\n}\n```\n\nTanya 'show code class' untuk contoh lengkap! 💻",
                    'type' => 'text'
                ];
            } else {
                return [
                    'message' => "💡 **OOP (Object-Oriented Programming)**\n\n🔹 **Class** - Blueprint for creating objects\n🔹 **Object** - Instance of a class\n🔹 **Inheritance** - Child class inherits parent properties\n🔹 **Polymorphism** - One method, many forms\n🔹 **Encapsulation** - Hide internal data\n\n**Simple example:**\n```java\nclass Car {\n    private String color;\n    private int speed;\n    \n    public Car(String c) {\n        color = c;\n        speed = 0;\n    }\n    \n    public void drive() {\n        speed += 10;\n    }\n}\n```\n\nAsk 'show code class' for full example! 💻",
                    'type' => 'text'
                ];
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

        // General help (but exclude "what is" questions)
        if (!$this->contains($messageLower, ['what is', 'apa itu', 'apakah']) && 
            ($this->contains($messageLower, ['help', 'hello', 'hi', 'can you', 'guide me']) ||
             $this->contains($messageLower, ['bantu', 'helo', 'hai', 'boleh', 'pandu saya']))) {
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
        return [
            'message' => $responses[array_rand($responses)],
            'type' => 'text'
        ];
    }
}
