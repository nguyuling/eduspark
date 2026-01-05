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
        $message = strtolower($message);

        // Java Fundamentals
        if ($this->contains($message, ['variable', 'data type', 'int', 'string', 'boolean', 'double', 'float'])) {
            return $this->getRandomResponse([
                "Variables in Java are containers that hold data. Think of them like labeled boxes. You declare a variable by specifying its type (int, String, boolean, etc.) and giving it a name. For example: `int age = 25;` creates an integer variable named 'age' with value 25.",
                "Data types in Java define what kind of data a variable can hold. There are primitive types (int, double, boolean, char) for basic values, and reference types (String, arrays, objects) for complex objects.",
                "In Java, you must declare the type of each variable. Common types are: `int` for whole numbers, `double` for decimals, `String` for text, and `boolean` for true/false values.",
            ]);
        }

        // Object-Oriented Programming
        if ($this->contains($message, ['class', 'object', 'inheritance', 'polymorphism', 'encapsulation', 'oop'])) {
            return $this->getRandomResponse([
                "A class is like a blueprint for creating objects. It defines properties (variables) and methods (functions) that objects will have. For example, a 'Car' class defines how all cars should behave.",
                "An object is an instance of a class. If the class is a blueprint, the object is the actual building made from that blueprint. You create objects using: `Car myCar = new Car();`",
                "Inheritance allows one class to inherit properties and methods from another class. For example, 'Car' and 'Bike' can both inherit from a 'Vehicle' class.",
                "Polymorphism means 'many forms'. It allows objects to take multiple forms. For example, you can have different animals that all have a 'makeSound()' method, but each sounds different.",
                "Encapsulation hides the internal details of an object and only exposes what's necessary. Use `private` for hidden variables and `public` for methods others can use.",
            ]);
        }

        // Loops and Control Structures
        if ($this->contains($message, ['loop', 'for', 'while', 'if', 'else', 'switch', 'condition', 'iteration'])) {
            return $this->getRandomResponse([
                "A `for` loop runs a block of code a specific number of times. Example: `for(int i = 0; i < 10; i++)` runs 10 times, with i going from 0 to 9.",
                "A `while` loop keeps running as long as a condition is true. Example: `while(x > 0)` keeps running until x becomes 0 or less.",
                "An `if-else` statement makes decisions. If the condition is true, it runs one block of code; otherwise, it runs another. Example: `if(age >= 18) { adult } else { child }`",
                "A `switch` statement checks one variable against multiple values. It's cleaner than many if-else statements when checking one value against many options.",
            ]);
        }

        // Methods and Constructors
        if ($this->contains($message, ['method', 'function', 'constructor', 'return', 'parameter'])) {
            return $this->getRandomResponse([
                "A method is a block of code that performs a specific task. You define it in a class and call it whenever needed. Methods help organize code and prevent repetition.",
                "A constructor is a special method that runs when you create an object. It's used to initialize variables. It has the same name as the class and no return type.",
                "Parameters are inputs to a method. For example: `public void greet(String name)` has one parameter called 'name'. When you call it, you provide the value: `greet('Ali')`",
                "A return statement sends a value back from a method. If a method returns `int`, it must send back an integer. If it returns `void`, it doesn't return anything.",
            ]);
        }

        // Exception Handling
        if ($this->contains($message, ['exception', 'error', 'try', 'catch', 'throw', 'finally'])) {
            return $this->getRandomResponse([
                "An exception is an error that happens during program execution. To handle it gracefully, use try-catch: `try { risky code } catch(Exception e) { handle error }`",
                "The `try` block contains code that might cause an error. The `catch` block handles that error if it occurs. The `finally` block runs regardless of whether an error happened.",
                "Common exceptions: `NullPointerException` (accessing null), `ArrayIndexOutOfBoundsException` (invalid array index), `InputMismatchException` (wrong input type).",
            ]);
        }

        // Collections
        if ($this->contains($message, ['arraylist', 'hashmap', 'collection', 'array', 'list', 'map', 'set'])) {
            return $this->getRandomResponse([
                "An `ArrayList` is like a dynamic array that grows automatically. Use it when you don't know the size in advance: `ArrayList<String> list = new ArrayList<>();`",
                "A `HashMap` stores key-value pairs like a dictionary. You look up values by their key: `HashMap<String, Integer> map = new HashMap<>();`",
                "Arrays have fixed size, but Collections like ArrayList are flexible. Arrays use `[]` syntax, Collections use methods like `add()`, `get()`, `remove()`.",
            ]);
        }

        // Algorithms and Logic
        if ($this->contains($message, ['algorithm', 'sort', 'search', 'logic', 'complexity', 'recursion'])) {
            return $this->getRandomResponse([
                "An algorithm is a step-by-step solution to a problem. Good algorithms are efficient (fast) and use minimal memory.",
                "Recursion is when a method calls itself. It's useful for problems that have a repeating structure (like tree navigation or factorial calculation).",
                "Common algorithms: sorting (arranging data), searching (finding data), traversal (visiting all elements).",
            ]);
        }

        // Error Message Help
        if ($this->contains($message, ['nullpointer', 'cannot find symbol', 'unexpected token', 'error', 'exception', 'compile', 'bug'])) {
            return $this->getRandomResponse([
                "NullPointerException happens when you try to use something that's null (empty). Check if your object is properly initialized before using it.",
                "'Cannot find symbol' means Java doesn't recognize a variable, method, or class name. Check for typos and make sure the item is declared.",
                "'Unexpected token' is a syntax error. Java doesn't understand your code structure. Check brackets, semicolons, and spelling.",
                "Errors and exceptions are different: compile-time errors stop the code from running; runtime exceptions happen while the program runs.",
            ]);
        }

        // General help
        if ($this->contains($message, ['help', 'hello', 'hi', 'what', 'how', 'can you', 'explain'])) {
            return $this->getRandomResponse([
                "👋 Hello! I'm the EduSpark AI Assistant. I'm here to help you with Java programming concepts. Ask me about variables, classes, loops, methods, exceptions, collections, and more!",
                "I can help explain Java fundamentals, OOP concepts, control structures, methods, error messages, and basic algorithms. What would you like to learn?",
            ]);
        }

        // Out of scope response
        return $this->getRandomResponse([
            "I'm specialized in Java programming. I can help with: Java syntax, OOP, loops, methods, exceptions, collections, and error explanations. Is there a Java topic you'd like help with?",
            "That's outside my expertise. I focus on Java programming topics. Can I help you with something Java-related?",
            "I'm here to help with Java concepts. Feel free to ask me about anything Java! 📚",
        ]);
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
