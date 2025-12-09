@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card shadow-lg">
                <div class="card-header bg-success text-white">
                    <h2>Create New Quiz</h2>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('teacher.quizzes.store') }}">
                        @csrf

                        {{-- === QUIZ HEADER DETAILS === --}}
                        <div class="mb-4 p-3 border rounded bg-light">
                            <h4>Quiz Setup</h4>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="title" class="form-label">Quiz Title</label>
                                    <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="max_attempts" class="form-label">Max Attempts Allowed</label>
                                    <input type="number" class="form-control" id="max_attempts" name="max_attempts" value="{{ old('max_attempts', 1) }}" min="1" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="due_at" class="form-label">Due Date (Optional)</label>
                                    <input type="datetime-local" class="form-control" id="due_at" name="due_at" value="{{ old('due_at') }}">
                                </div>
                                <div class="col-md-6 mb-3 d-flex align-items-end">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="publish" id="publish" {{ old('publish') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="publish">
                                            Publish Quiz Immediately
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description (Optional)</label>
                                <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            </div>
                        </div>


                        {{-- === QUESTION CONTAINER (Dynamically managed by JavaScript) === --}}
                        <h4 class="mt-4 mb-3">Questions <span class="text-danger">*</span></h4>
                        <div id="questions-container">
                            {{-- Initial question template will be rendered here --}}
                        </div>

                        <button type="button" class="btn btn-outline-secondary btn-sm mb-4" id="add-question-btn">
                            + Add Another Question
                        </button>

                        <div class="mt-4 border-top pt-3">
                            <button type="submit" class="btn btn-success btn-lg">Save & Publish Quiz</button>
                            <a href="{{ route('teacher.quizzes.index') }}" class="btn btn-secondary btn-lg">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Define the question types based on the Question Model constants
    const QUESTION_TYPES = {
        MC: 'multiple_choice',
        SA: 'short_answer',
        TF: 'true_false',
        CHECKBOX: 'checkbox' // <--- ADDED CHECKBOX TYPE
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
                        <textarea name="questions[${index}][text]" class="form-control" rows="2" required></textarea>
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
            <input type="text" name="questions[${index}][correct_answer]" class="form-control" placeholder="Enter the exact correct short answer" required>
        </div>
    `;

    // Template for Options container (used by MC, TF, and CHECKBOX)
    // Takes the current type to determine input style (radio or checkbox)
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
                <input class="form-check-input mt-0 correct-option-radio" type="radio" 
                       name="questions[${qIndex}][correct_option_index]" 
                       value="${oIndex}" ${oIndex === 0 ? 'checked' : ''} required>
            </div>
            <input type="text" name="questions[${qIndex}][options][${oIndex}][text]" class="form-control" placeholder="Option Text" required>
            <button type="button" class="btn btn-outline-danger remove-option-btn" data-o-index="${oIndex}">X</button>
        </div>
    `;

    // Template for a single option row (Checkbox)
    const checkboxOptionRow = (qIndex, oIndex) => `
        <div class="input-group mb-2 option-row" data-o-index="${oIndex}">
            <div class="input-group-text">
                <input class="form-check-input mt-0 correct-option-checkbox" type="checkbox" 
                       name="questions[${qIndex}][correct_option_indices][]" 
                       value="${oIndex}"> </div>
            <input type="text" name="questions[${qIndex}][options][${oIndex}][text]" class="form-control" placeholder="Option Text" required>
            <button type="button" class="btn btn-outline-danger remove-option-btn" data-o-index="${oIndex}">X</button>
        </div>
    `;


    // --- FUNCTIONS ---
    
    // Renders the correct answer fields based on the selected type
    const renderAnswerFields = (qIndex, type) => {
        const container = document.getElementById(`answers-container-${qIndex}`);
        container.innerHTML = ''; // Clear previous content
        
        // Define which option row template to use when adding new options
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
                                   name="questions[${qIndex}][correct_option_index]" value="0" checked required>
                        </div>
                        <input type="text" name="questions[${qIndex}][options][0][text]" class="form-control" value="True" readonly>
                        <button type="button" class="btn btn-outline-secondary" disabled>X</button>
                    </div>
                    <div class="input-group mb-2 option-row" data-o-index="1">
                        <div class="input-group-text">
                            <input class="form-check-input mt-0 correct-option-radio" type="radio" 
                                   name="questions[${qIndex}][correct_option_index]" value="1" required>
                        </div>
                        <input type="text" name="questions[${qIndex}][options][1][text]" class="form-control" value="False" readonly>
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
        if (currentOptions >= 10) { // Increased max limit for Checkbox flexibility
            alert("A question cannot have more than 10 options.");
            return;
        }
        
        // Use the passed-in rowTemplate (radio or checkbox)
        optionsList.insertAdjacentHTML('beforeend', rowTemplate(qIndex, currentOptions));
    };


    // --- EVENT LISTENERS ---
    
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('questions-container');
        const addQuestionBtn = document.getElementById('add-question-btn');
        
        // 1. Initial Load: Render the first question
        container.insertAdjacentHTML('beforeend', questionTemplate(questionIndex));
        renderAnswerFields(questionIndex, QUESTION_TYPES.MC); // Default to Multiple Choice
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
                if (container.querySelectorAll('.question-card').length > 1) {
                     card.remove();
                     // Re-index all cards after removal
                     container.querySelectorAll('.question-card').forEach((qCard, i) => {
                         const oldIndex = parseInt(qCard.getAttribute('data-index'));
                         qCard.setAttribute('data-index', i);

                         // Update all names and IDs within the card using regex or manual replacement
                         qCard.innerHTML = qCard.innerHTML.replace(new RegExp(`questions\\[${oldIndex}\\]`, 'g'), `questions[${i}]`)
                                                          .replace(new RegExp(`answers-container-${oldIndex}`, 'g'), `answers-container-${i}`)
                                                          .replace(new RegExp(`data-index="${oldIndex}"`, 'g'), `data-index="${i}"`);
                     });
                     questionIndex = container.querySelectorAll('.question-card').length; // Reset counter
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

                if (optionsList.querySelectorAll('.option-row').length > 2 || type === QUESTION_TYPES.SA) {
                    optionRow.remove();
                    // Re-index remaining options to maintain sequential array keys
                    optionsList.querySelectorAll('.option-row').forEach((row, i) => {
                        row.setAttribute('data-o-index', i);
                        
                        // Update the radio/checkbox name value
                        const input = row.querySelector('input[type="radio"], input[type="checkbox"]');
                        if (input) input.value = i;
                        
                        // Update the text input name (the key [${oIndex}])
                        const textInput = row.querySelector('input[type="text"]');
                        if (textInput) {
                            textInput.name = textInput.name.replace(/options\[\d+\]/, `options[${i}]`);
                        }
                        
                        // If it's a radio button, re-check the first one if it was deleted and this is the new 0
                        if(input && input.type === 'radio' && i === 0) {
                            input.checked = true;
                        }
                    });

                } else {
                    alert("Multiple Choice/True False/Checkbox questions must have at least two options.");
                }
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