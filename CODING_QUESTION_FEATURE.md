# Coding Question Feature - Enhanced Line Hiding System

## Overview

The improved coding question feature allows teachers to:
1. Paste complete Java code/programs
2. Interactively select specific lines to hide from students
3. See a real-time preview of what students will see
4. Students must fill in the hidden lines as their answer

## How It Works

### For Teachers (Quiz Creation)

#### Step 1: Create a Coding Question
1. Click "Tambah Soalan" (Add Question) when creating a quiz
2. Select "Pengaturcaraan" (Programming) as the question type

#### Step 2: Enter Full Java Code
- Paste or type the complete Java program into the "Kod Penuh (Java)" textarea
- Support for:
  - Multiple lines
  - Tab indentation (converts to 4 spaces)
  - Any valid Java syntax

#### Step 3: Select Hidden Lines
- Click on any line in the "Pilih Baris yang Akan Disembunyikan" section to toggle it as hidden
- Each selected line will:
  - Display with a light purple background
  - Be marked as hidden
  - Show "[___BARIS DISEMBUNYIKAN___]" in the student preview
- You can select multiple non-consecutive lines

#### Step 4: Review Student View
- The "Pratonton (Pandangan Pelajar)" section shows:
  - Exact code the student will see
  - Hidden lines replaced with "[___BARIS DISEMBUNYIKAN___]"
  - Line numbers for reference
- Updates in real-time as you select/deselect hidden lines

#### Step 5: Enter Expected Output
- Input the expected program output when students run their code
- This is used for validation against student submissions

### For Students (Quiz Taking)

Students see:
- Complete code structure with line numbers
- Specific lines hidden with "[___BARIS DISEMBUNYIKAN___]" placeholders
- A textarea to enter the hidden code lines
- The expected output to help them debug

## Database Schema

### New Columns in `questions` Table

| Column | Type | Purpose |
|--------|------|---------|
| `coding_full_code` | longText | Complete Java code provided by teacher |
| `hidden_line_numbers` | text | Comma-separated list of line numbers to hide (e.g., "3,5,7") |

### Related Fields

| Column | Type | Purpose |
|--------|------|---------|
| `coding_template` | longText | Legacy template field (still available) |
| `coding_language` | string | Programming language (default: "java") |
| `coding_expected_output` | longText | Expected output for code execution |

## Backend Implementation

### QuizTeacherController

The `store()` method now handles:

```php
'coding_full_code' => ($questionType === 'coding')
                        ? ($questionData['coding_full_code'] ?? null)
                        : null,
'hidden_line_numbers' => ($questionType === 'coding')
                            ? ($questionData['hidden_line_numbers'] ?? null)
                            : null,
```

### Model Fillable

QuizQuestion model updated:
```php
protected $fillable = [
    'quiz_id', 
    'question_text', 
    'points', 
    'type', 
    'coding_template', 
    'coding_full_code',
    'coding_language', 
    'coding_expected_output', 
    'hidden_line_numbers'
];
```

## Frontend Implementation

### JavaScript Functions

#### `updateCodeLineNumbers(textarea, index)`
- Expands textarea based on content
- Updates line number display
- Triggers line selector and preview updates
- Called on textarea input

#### `updateLineSelector(index, lines)`
- Generates clickable line selector UI
- Displays current hidden state
- Handles click events for line selection

#### `updateHiddenLines(index)`
- Collects selected line numbers
- Updates hidden lines input value
- Updates row styling
- Triggers preview update

#### `updateCodePreview(index, lines)`
- Shows what students will see
- Replaces hidden lines with "[___BARIS DISEMBUNYIKAN___]"
- Updates line numbers in preview
- Real-time updates as lines are selected/deselected

### Event Handlers

- **Input events**: Trigger line number and preview updates
- **Tab key**: Converts to 4 spaces for proper indentation
- **Click events**: Toggle line selection in the line selector

### User Interface Components

1. **Full Code Input**
   - Large textarea for complete Java program
   - Line numbers on the left
   - Auto-expand based on content

2. **Line Selector**
   - Interactive list of all code lines
   - Checkboxes for selection
   - Color-coded (light purple when selected)
   - Shows line content truncated to 60 chars

3. **Preview Section**
   - Shows exact student view
   - Hidden lines highlighted in yellow
   - Line numbers maintained
   - Updates in real-time

4. **Expected Output**
   - Textarea for program output
   - Line numbers support
   - Tab expansion support

## Migration

Migration: `2025_12_17_091600_add_hidden_lines_to_questions_table.php`

Creates/modifies:
- `coding_full_code` text column
- `hidden_line_numbers` text column

## Usage Example

### Teacher Setup
```
Code provided:
1: public class Hello {
2:     public static void main(String[] args) {
3:         System.out.println("Hello");
4:         System.out.println("World");
5:     }
6: }

Hidden lines selected: 3, 4
```

### Student View
```
1: public class Hello {
2:     public static void main(String[] args) {
[___BARIS DISEMBUNYIKAN___]
[___BARIS DISEMBUNYIKAN___]
5:     }
6: }

Expected Output:
Hello
World
```

### Student Must Answer
```
Students fill in lines 3-4:
        System.out.println("Hello");
        System.out.println("World");
```

## Supported Features

✅ Multiple non-consecutive line hiding
✅ Real-time preview updates
✅ Tab to 4-space conversion
✅ Line number display
✅ Complete code preservation
✅ Expected output specification
✅ Java language support

## Future Enhancements

- [ ] Support for multiple programming languages (Python, C++, JavaScript)
- [ ] Line range selection shortcuts (select lines 5-10)
- [ ] Code syntax highlighting
- [ ] Student code execution verification
- [ ] Partial credit for correct hidden lines
- [ ] Code quality metrics and feedback

## Testing Checklist

- [ ] Create coding question with multiple hidden lines
- [ ] Verify preview updates correctly
- [ ] Test with various Java code formats
- [ ] Check line selection/deselection
- [ ] Verify database storage
- [ ] Test student submission view
- [ ] Verify output comparison

## Troubleshooting

### Preview Not Updating
- Ensure you've entered code in the full code textarea
- Check that JavaScript is enabled
- Verify browser console for errors

### Hidden Lines Not Saved
- Verify at least one line is selected
- Check that form submission was successful
- Look for validation errors in quiz creation

### Line Numbers Not Displaying
- Refresh the page
- Clear browser cache
- Check if custom CSS is overriding display

## File References

- **Create Form**: `/resources/views/quiz/create.blade.php`
- **Controller**: `/app/Http/Controllers/QuizTeacherController.php`
- **Model**: `/app/Models/QuizQuestion.php`
- **Migration**: `/database/migrations/2025_12_17_091600_add_hidden_lines_to_questions_table.php`

## Related Files Modified

1. **create.blade.php**
   - Updated `codingTemplate()` function with full code input
   - Added line selector UI
   - Added real-time preview section
   - Updated event listeners for new textarea class

2. **QuizTeacherController.php**
   - Updated `store()` method to save `coding_full_code` and `hidden_line_numbers`

3. **QuizQuestion.php**
   - Updated `$fillable` array with new fields

---

**Last Updated**: December 17, 2025
**Status**: Active
**Version**: 1.0
