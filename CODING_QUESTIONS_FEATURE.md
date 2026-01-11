# Coding Question Type Feature Documentation

## Overview
A new **Coding** question type has been added to the quiz system. This allows teachers to create programming assignments where students can:
- Write code from scratch
- Complete code templates provided by the teacher
- Have their code evaluated based on expected output

## Features

### 1. Question Types Support
The system now supports 5 question types:
- **multiple_choice** - Traditional multiple choice questions
- **true_false** - Boolean questions
- **checkbox** - Multi-select questions
- **short_answer** - Text input questions
- **coding** - NEW: Programming code questions

### 2. Coding Question Configuration

#### Template Support
Teachers can provide students with:
- **From Scratch**: No template provided - students write complete code
- **Template Code**: Partial code provided - students complete the implementation

Example template in Java:
```java
public static int faktorial(int n) {
    // Lengkapkan kod di sini
    
}
```

#### Programming Languages
Currently supported:
- `java` (default)
- Extensible for: `python`, `javascript`, `cpp`, `csharp`, etc.

#### Expected Output (Optional)
Teachers can specify expected program output for validation:
```
*
**
***
```

### 3. Database Schema

#### New Columns in `questions` table:
| Column | Type | Purpose |
|--------|------|---------|
| `coding_template` | LONGTEXT | Template code provided to students |
| `coding_language` | VARCHAR | Programming language (java, python, etc.) |
| `coding_expected_output` | LONGTEXT | Expected output for program validation |

#### New Columns in `student_answers` table:
| Column | Type | Purpose |
|--------|------|---------|
| `submitted_code` | LONGTEXT | Student's code submission |
| `code_output` | LONGTEXT | Output from running the code |
| `code_compiled` | BOOLEAN | Whether code compiled/executed successfully |
| `compilation_error` | TEXT | Error message if compilation failed |

### 4. Model Updates

#### QuizQuestion Model
- New constant: `TYPE_CODING = 'coding'`
- New fillable fields: `coding_template`, `coding_language`, `coding_expected_output`

#### QuizAnswer Model
- New fillable fields: `submitted_code`, `code_output`, `code_compiled`, `compilation_error`

### 5. Example Coding Quiz

A sample quiz "Tingkatan 5: Pengaturcaraan Java - Soalan Kod" (Quiz 14) has been created with 10 coding questions covering:

1. **Simple Method Writing**: Create a method that adds two integers
2. **Template Completion**: Implement factorial function
3. **Program Output**: Print a star triangle pattern
4. **Logic Implementation**: Check if a number is prime
5. **String Manipulation**: Reverse a string
6. **Counting Logic**: Count digits in a number
7. **Nested Loops**: Print multiplication table
8. **Array Operations**: Find maximum value
9. **String Validation**: Check for palindrome
10. **Statistical Function**: Calculate average

Each question specifies:
- Points value (10-15 points)
- Programming language (Java)
- Template code (when applicable)
- Expected output (when applicable)

## Seeder Usage

### QuizQuestionSeeder.php Structure
```php
[
    'text' => 'Question description...',
    'type' => 'coding',
    'points' => 15,
    'coding_language' => 'java',
    'coding_template' => 'public static void method() {\n    // Complete this\n}',
    'coding_expected_output' => 'Optional output specification',
]
```

### QuizSeeder.php Handling
The seeder automatically extracts and stores:
- `coding_template` → Saved to questions.coding_template
- `coding_language` → Saved to questions.coding_language
- `coding_expected_output` → Saved to questions.coding_expected_output

## Future Implementation

### Recommended Next Steps:

1. **Frontend Form Component**
   - Create a code editor component (suggest Monaco Editor or Ace Editor)
   - Display template code with editable section
   - Syntax highlighting for supported languages

2. **Code Execution Engine**
   - Integrate Docker/sandbox for code execution
   - Compile and run student code safely
   - Capture output and errors

3. **Code Evaluation**
   - Compare output with expected output
   - Implement partial credit system
   - Support for multiple test cases

4. **Teacher Interface**
   - Add form fields for coding question creation
   - Template editor with syntax highlighting
   - Test case/expected output editor

5. **Student Interface**
   - Code editor for solution submission
   - Run button to test code
   - Error display and debugging help

6. **Grading Features**
   - Automatic output comparison
   - Manual code review by teachers
   - Plagiarism detection (optional)

## Migrations

Two new migrations have been created:

1. **2025_12_17_000001_add_coding_fields_to_questions_table.php**
   - Adds coding-related columns to questions table

2. **2025_12_17_000002_add_coding_submission_fields_to_student_answers_table.php**
   - Adds code submission tracking columns to student_answers table

Both migrations are reversible.

## Usage Example

### Creating a Coding Question in Seeder:
```php
[
    'text' => 'Write a Java method that calculates the sum of all numbers in an array.',
    'type' => 'coding',
    'points' => 15,
    'coding_language' => 'java',
    'coding_template' => null, // No template - code from scratch
    'coding_expected_output' => null, // Teacher will grade manually
]
```

### Creating a Coding Question with Template:
```php
[
    'text' => 'Complete the factorial method.',
    'type' => 'coding',
    'points' => 20,
    'coding_language' => 'java',
    'coding_template' => 'public static int factorial(int n) {
    // TODO: Implement factorial calculation
    return 0;
}',
    'coding_expected_output' => '120', // If n=5
]
```

## API Integration Points

### Code Execution Service (to be implemented)
```php
interface CodeExecutionService {
    public function execute(
        string $code,
        string $language,
        array $testCases = []
    ): CodeExecutionResult;
}
```

### Result Model (to be implemented)
```php
class CodeExecutionResult {
    public bool $compiled;
    public string $output;
    public ?string $error;
    public float $executionTime;
}
```

## Security Considerations

- Student code must be executed in isolated sandbox (Docker recommended)
- Prevent access to file system or network
- Set execution time limits
- Monitor resource usage
- Validate input before execution

---

**Last Updated**: December 17, 2025
**Added by**: GitHub Copilot
**Status**: Structure Complete | Execution Engine Pending
