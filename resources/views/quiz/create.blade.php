@extends('layouts.app')

@section('content')
<div class="app">
    <main class="main">
        <div class="header">
            <div>
                <div class="title">Create New Quiz</div>
                <div class="sub">Set up your quiz with questions and options</div>
            </div>
            <a href="{{ route('teacher.quizzes.index') }}" class="btn btn-secondary">
                ← Back to Quizzes
            </a>
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
            <section class="panel panel-spaced" style="margin-top: 60px; background: rgba(230, 57, 70, 0.1); border-left: 3px solid var(--danger);">
                <div style="margin-bottom: 12px;">
                    <div style="font-weight: 700; color: var(--danger); margin-bottom: 8px;">Please fix the following errors:</div>
                    <ul style="margin: 0; padding-left: 20px; color: var(--danger);">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </section>
        @endif

        <!-- Quiz Setup Form -->
        <section class="panel panel-spaced" style="margin-top: 60px; max-width: 800px;">
            <div class="panel-header">Quiz Setup</div>

            <form method="POST" action="{{ route('teacher.quizzes.store') }}" style="margin-top: 20px;">
                @csrf

                <!-- Title -->
                <div class="form-group">
                    <label for="title">Quiz Title</label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        class="form-input"
                        placeholder="e.g. Chapter 1 Assessment"
                        value="{{ old('title') }}" 
                        required
                    >
                    @error('title')<span class="error-msg">{{ $message }}</span>@enderror
                </div>

                <!-- Max Attempts -->
                <div class="form-group">
                    <label for="max_attempts">Max Attempts Allowed</label>
                    <input 
                        type="number" 
                        id="max_attempts" 
                        name="max_attempts" 
                        class="form-input"
                        value="{{ old('max_attempts', 1) }}" 
                        min="1"
                        required
                    >
                    @error('max_attempts')<span class="error-msg">{{ $message }}</span>@enderror
                </div>

                <!-- Due Date -->
                <div class="form-group">
                    <label for="due_at">Due Date (Optional)</label>
                    <input 
                        type="datetime-local" 
                        id="due_at" 
                        name="due_at" 
                        class="form-input"
                        value="{{ old('due_at') }}"
                    >
                    @error('due_at')<span class="error-msg">{{ $message }}</span>@enderror
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label for="description">Description (Optional)</label>
                    <textarea 
                        id="description" 
                        name="description" 
                        rows="3"
                        placeholder="Add instructions or additional information..."
                        style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 1px solid var(--control-border); background: var(--input-bg); color: inherit; font-size: 14px; outline: none; transition: box-shadow 0.12s ease, border-color 0.12s ease; resize: vertical; box-sizing: border-box;"
                    >{{ old('description') }}</textarea>
                    @error('description')<span class="error-msg">{{ $message }}</span>@enderror
                </div>

                <!-- Publish Checkbox -->
                <div class="form-group" style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                    <input 
                        type="checkbox" 
                        id="is_published" 
                        name="is_published"
                        {{ old('is_published') ? 'checked' : '' }}
                        style="width: 20px; height: 20px; cursor: pointer;"
                    >
                    <label for="is_published" style="margin: 0; cursor: pointer;">Publish Quiz Immediately</label>
                </div>

                <!-- Questions Container -->
                <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #d4c5f9;">
                    <div style="font-size: 16px; font-weight: 700; margin-bottom: 16px;">Questions <span style="color: var(--danger);">*</span></div>
                    <div id="questions-container"></div>
                    
                    <button type="button" class="btn btn-secondary" id="add-question-btn" style="margin-top: 16px; margin-bottom: 24px;">
                        ➕ Add Question
                    </button>
                </div>

                <!-- Form Actions -->
                <div style="display: flex; gap: 12px; margin-top: 24px; padding-top: 20px; border-top: 2px solid #d4c5f9;">
                    <button type="submit" class="btn btn-primary">
                        ✨ Save & Publish Quiz
                    </button>
                    <a href="{{ route('teacher.quizzes.index') }}" class="btn btn-secondary">
                        Cancel
                    </a>
                </div>
            </form>
        </section>
    </main>
</div>

<script>
    // Define the question types based on the Question Model constants
    const QUESTION_TYPES = {
        MC: 'multiple_choice',
        SA: 'short_answer',
        TF: 'true_false',
        CHECKBOX: 'checkbox'
    };
    
    // Global counter for question indices
    let questionIndex = 0;

    // --- TEMPLATES ---
    
    // Template for a new question card
    const questionTemplate = (index) => `
        <div class="card mb-3 question-card" data-index="${index}">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Question #${index + 1}</h5>
                <button type="button" class="btn btn-sm btn-outline-danger remove-question-btn" data-index="${index}">Remove</button>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-7">  <label class="form-label">Question Text <span class="text-danger">*</span></label>
                        <textarea name="questions[${index}][question_text]" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="col-md-1">  <label class="form-label">Points <span class="text-danger">*</span></label>
                        <input type="number" name="questions[${index}][points]" class="form-control" value="1" min="1" required>
                    </div>
                    <div class="col-md-4">  <label class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="questions[${index}][type]" class="form-select question-type-select" data-index="${index}" required>
                            <option value="${QUESTION_TYPES.MC}">Multiple Choice</option>
                            <option value="${QUESTION_TYPES.CHECKBOX}">Checkbox</option>
                            <option value="${QUESTION_TYPES.SA}">Short Answer</option>
                            <option value="${QUESTION_TYPES.TF}">True/False</option>
                        </select>
                    </div>
                </div>

                <hr class="mt-0">
                <div class="answers-container" id="answers-container-${index}">
                    ${optionTemplate(index, QUESTION_TYPES.MC)} </div>
            </div>
        </div>
    `;
  

    // Template for the Short Answer input (Only one text box)
    const shortAnswerTemplate = (index) => `
        <div class="mb-3">
            <label class="form-label text-success">Correct Answer <span class="text-danger">*</span></label>
            {{-- CRITICAL FIX: The key 'correct_answer' holds the text for short answer --}}
            <input type="text" name="questions[${index}][correct_answer]" class="form-control" placeholder="Enter the exact correct short answer" required>
        </div>
    `;

    // Template for Options container (used by MC, TF, and CHECKBOX)
    const optionTemplate = (qIndex, type) => {
        const rowTemplate = (type === QUESTION_TYPES.CHECKBOX) ? checkboxOptionRow : optionRow;

        return `
            <h6 class="mb-2">Options & Correct Answer(s) <span class="text-danger">*</span></h6>
            <div class="options-list" data-q-index="${qIndex}">
                
                ${rowTemplate(qIndex, 0)}
                ${rowTemplate(qIndex, 1)}

            </div>
            <button type="button" class="btn btn-sm btn-outline-primary mt-2 add-option-btn" data-q-index="${qIndex}">
                + Add Option
            </button>
        `;
    };
    
    // Template for a single option row (Radio button for MC/TF)
    const optionRow = (qIndex, oIndex) => `
        <div class="input-group mb-2 option-row" data-o-index="${oIndex}">
            <div class="input-group-text">
                {{-- CRITICAL FIX: The value sent is the option's text itself, simplifying controller logic --}}
                <input class="form-check-input mt-0 correct-option-radio" type="radio" 
                       name="questions[${qIndex}][correct_answer]" 
                       value="" 
                       data-option-text-target 
                       ${oIndex === 0 ? 'checked' : ''} required>
            </div>
            {{-- CRITICAL FIX: Changed name to option_text and used empty [] to send an array of options --}}
            <input type="text" 
                   name="questions[${qIndex}][options][]" 
                   class="form-control option-text-input" 
                   placeholder="Option Text" 
                   required 
                   oninput="updateRadioValue(this)">
            <button type="button" class="btn btn-outline-danger remove-option-btn" data-o-index="${oIndex}">X</button>
        </div>
    `;

    // Template for a single option row (Checkbox)
    const checkboxOptionRow = (qIndex, oIndex) => `
        <div class="input-group mb-2 option-row" data-o-index="${oIndex}">
            <div class="input-group-text">
                {{-- CRITICAL FIX: The name is now an array to capture multiple correct answers --}}
                <input class="form-check-input mt-0 correct-option-checkbox" type="checkbox" 
                       name="questions[${qIndex}][correct_answers][]" 
                       value="" 
                       data-option-text-target> 
            </div>
            {{-- CRITICAL FIX: Changed name to option_text and used empty [] to send an array of options --}}
            <input type="text" 
                   name="questions[${qIndex}][options][]" 
                   class="form-control option-text-input" 
                   placeholder="Option Text" 
                   required
                   oninput="updateCheckboxValue(this)">
            <button type="button" class="btn btn-outline-danger remove-option-btn" data-o-index="${oIndex}">X</button>
        </div>
    `;

    // Function to dynamically update the radio button's value when the option text changes
    function updateRadioValue(inputElement) {
        const optionText = inputElement.value;
        const radio = inputElement.closest('.input-group').querySelector('[data-option-text-target]');
        if (radio && radio.type === 'radio') {
            radio.value = optionText;
        }
    }

    // Function to dynamically update the checkbox button's value when the option text changes
    function updateCheckboxValue(inputElement) {
        const optionText = inputElement.value;
        const checkbox = inputElement.closest('.input-group').querySelector('[data-option-text-target]');
        if (checkbox && checkbox.type === 'checkbox') {
            checkbox.value = optionText;
        }
    }


    // --- FUNCTIONS ---
    
    // Renders the correct answer fields based on the selected type
    const renderAnswerFields = (qIndex, type) => {
        const container = document.getElementById(`answers-container-${qIndex}`);
        container.innerHTML = ''; // Clear previous content
        
        const rowTemplate = (type === QUESTION_TYPES.CHECKBOX) ? checkboxOptionRow : optionRow;

        if (type === QUESTION_TYPES.SA) {
            container.innerHTML = shortAnswerTemplate(qIndex);
        } else if (type === QUESTION_TYPES.MC || type === QUESTION_TYPES.TF || type === QUESTION_TYPES.CHECKBOX) {
            
            container.innerHTML = optionTemplate(qIndex, type);
            
            // Re-bind the add option button using the correct template
            const addBtn = container.querySelector('.add-option-btn');
            addBtn.onclick = function() {
                addOptionRow(qIndex, this.previousElementSibling, rowTemplate);
            };

            // For True/False, immediately adjust to only two options: True and False
            if (type === QUESTION_TYPES.TF) {
                const optionsList = container.querySelector('.options-list');
                optionsList.innerHTML = `
                    <div class="input-group mb-2 option-row" data-o-index="0">
                        <div class="input-group-text">
                            <input class="form-check-input mt-0 correct-option-radio" type="radio" 
                                   name="questions[${qIndex}][correct_answer]" value="True" checked required>
                        </div>
                        <input type="text" name="questions[${qIndex}][options][]" class="form-control option-text-input" value="True" readonly oninput="updateRadioValue(this)">
                        <button type="button" class="btn btn-outline-secondary" disabled>X</button>
                    </div>
                    <div class="input-group mb-2 option-row" data-o-index="1">
                        <div class="input-group-text">
                            <input class="form-check-input mt-0 correct-option-radio" type="radio" 
                                   name="questions[${qIndex}][correct_answer]" value="False" required>
                        </div>
                        <input type="text" name="questions[${qIndex}][options][]" class="form-control option-text-input" value="False" readonly oninput="updateRadioValue(this)">
                        <button type="button" class="btn btn-outline-secondary" disabled>X</button>
                    </div>
                `;
                container.querySelector('.add-option-btn').style.display = 'none'; // Hide add button
            }
        }
    };

    // Adds a new option row to a question (Now accepts the rowTemplate function)
    const addOptionRow = (qIndex, optionsList, rowTemplate) => {
        const currentOptions = optionsList.querySelectorAll('.option-row').length;
        if (currentOptions >= 10) { 
            alert("A question cannot have more than 10 options.");
            return;
        }
        
        // Use the passed-in rowTemplate (radio or checkbox)
        optionsList.insertAdjacentHTML('beforeend', rowTemplate(qIndex, currentOptions));
    };


    // --- EVENT LISTENERS & INITIALIZATION ---
    
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('questions-container');
        const addQuestionBtn = document.getElementById('add-question-btn');
        let questionIndex = 0;
        
        // 1. Initial Load: Render the first question
        // Fix: Use insertAdjacentHTML and renderAnswerFields to ensure correct setup
        container.insertAdjacentHTML('beforeend', questionTemplate(questionIndex));
        renderAnswerFields(questionIndex, QUESTION_TYPES.MC); 
        questionIndex++;

        // 2. Add Question Button
        addQuestionBtn.addEventListener('click', function() {
            container.insertAdjacentHTML('beforeend', questionTemplate(questionIndex));
            renderAnswerFields(questionIndex, QUESTION_TYPES.MC);
            questionIndex++;
        });

        // 3. Delegation for Dynamic Events (Type Change, Remove Question/Option)
        container.addEventListener('click', function(e) {
            
            // Remove Question
            if (e.target.classList.contains('remove-question-btn')) {
                const card = e.target.closest('.question-card');
                const questionCards = container.querySelectorAll('.question-card');
                if (questionCards.length > 1) {
                     // Get the index of the card being removed
                     const removedIndex = parseInt(card.getAttribute('data-index'));
                     card.remove();
                     
                     // Re-index all cards that came after the removed one
                     container.querySelectorAll('.question-card').forEach((qCard, i) => {
                         const currentCardIndex = parseInt(qCard.getAttribute('data-index'));
                         
                         // Only process cards that need re-indexing
                         if(currentCardIndex > removedIndex) {
                             const newIndex = currentCardIndex - 1;
                             
                             // Update attribute
                             qCard.setAttribute('data-index', newIndex);
                             
                             // Update display number
                             qCard.querySelector('.card-header h5').textContent = `Question #${newIndex + 1}`;
                             
                             // Update all names and IDs within the card using regex
                             qCard.innerHTML = qCard.innerHTML.replace(new RegExp(`questions\\[${currentCardIndex}\\]`, 'g'), `questions[${newIndex}]`)
                                                              .replace(new RegExp(`answers-container-${currentCardIndex}`, 'g'), `answers-container-${newIndex}`)
                                                              .replace(new RegExp(`data-index="${currentCardIndex}"`, 'g'), `data-index="${newIndex}"`);

                            // Re-bind the remove button with the correct new data-index
                            qCard.querySelector('.remove-question-btn').setAttribute('data-index', newIndex);
                         }
                     });
                     
                     questionIndex--; // Decrement the global counter
                     
                } else {
                    alert("A quiz must have at least one question.");
                }
            }
            
            // Remove Option
            if (e.target.classList.contains('remove-option-btn')) {
                const optionRow = e.target.closest('.option-row');
                const optionsList = optionRow.closest('.options-list');
                
                // Get the question type from the select field to apply correct minimum rule
                const qIndex = optionsList.getAttribute('data-q-index');
                const typeSelect = document.querySelector(`.question-type-select[data-index="${qIndex}"]`);
                const type = typeSelect ? typeSelect.value : QUESTION_TYPES.MC;

                if (optionsList.querySelectorAll('.option-row').length > 2) {
                    optionRow.remove();
                    // Re-index remaining options to maintain sequential array keys
                    optionsList.querySelectorAll('.option-row').forEach((row, i) => {
                        // Update the text input name (the key [${oIndex}])
                        const textInput = row.querySelector('.option-text-input');
                        if (textInput) {
                            textInput.name = `questions[${qIndex}][options][]`; // Revert to simple array index
                        }
                    });

                } else if (type === QUESTION_TYPES.SA) {
                    // Do nothing, Short Answer is handled by its own template
                } else {
                    alert("Multiple Choice/True False/Checkbox questions must have at least two options.");
                }
            }

            // Delegation for add option button (must be rebound after type change)
            if (e.target.classList.contains('add-option-btn')) {
                const qIndex = e.target.getAttribute('data-q-index');
                const typeSelect = document.querySelector(`.question-type-select[data-index="${qIndex}"]`);
                const type = typeSelect ? typeSelect.value : QUESTION_TYPES.MC;
                
                const rowTemplate = (type === QUESTION_TYPES.CHECKBOX) ? checkboxOptionRow : optionRow;
                const optionsList = e.target.previousElementSibling;
                addOptionRow(qIndex, optionsList, rowTemplate);
            }
        });

        // 4. Type Change Listener (Delegation required for dynamic elements)
        container.addEventListener('change', function(e) {
            if (e.target.classList.contains('question-type-select')) {
                const qIndex = e.target.getAttribute('data-index');
                const type = e.target.value;
                renderAnswerFields(qIndex, type);
            }
        });
        
    });
</script>
@endsection