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
                <div class="card-header bg-warning text-white">
                    <h2>Edit Quiz: {{ $quiz->title }}</h2>
                </div>

                <div class="card-body">
                    {{-- FORM ACTION & METHOD CHANGED FOR UPDATE --}}
                    <form method="POST" action="{{ route('teacher.quizzes.update', $quiz->id) }}">
                        @csrf
                        @method('PUT') {{-- Required for the Update method in the controller --}}

                        {{-- === QUIZ HEADER DETAILS === --}}
                        <div class="mb-4 p-3 border rounded bg-light">
                            <h4>Quiz Setup</h4>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="title" class="form-label">Quiz Title</label>
                                    {{-- Use $quiz->title or old('title') --}}
                                    <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $quiz->title) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="max_attempts" class="form-label">Max Attempts Allowed</label>
                                    {{-- Use $quiz->max_attempts or old('max_attempts') --}}
                                    <input type="number" class="form-control" id="max_attempts" name="max_attempts" value="{{ old('max_attempts', $quiz->max_attempts) }}" min="1" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="due_at" class="form-label">Due Date (Optional)</label>
                                    {{-- Format the date/time correctly for the input field --}}
                                    <input type="datetime-local" class="form-control" id="due_at" name="due_at" value="{{ old('due_at', optional($quiz->due_at)->format('Y-m-d\TH:i')) }}">
                                </div>
                                <div class="col-md-6 mb-3 d-flex align-items-end">
                                    <div class="form-check">
                                        {{-- Check the box if old('publish') is set OR $quiz->is_published is true --}}
                                        <input class="form-check-input" type="checkbox" name="publish" id="publish" {{ old('publish', $quiz->is_published) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="publish">
                                            Publish Quiz Immediately
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description (Optional)</label>
                                <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $quiz->description) }}</textarea>
                            </div>
                        </div>


                        {{-- === QUESTION CONTAINER (Dynamically managed by JavaScript) === --}}
                        <h4 class="mt-4 mb-3">Questions <span class="text-danger">*</span></h4>
                        <div id="questions-container">
                            {{-- EXISTING QUESTIONS WILL BE POPULATED HERE --}}
                        </div>

                        <button type="button" class="btn btn-outline-secondary btn-sm mb-4" id="add-question-btn">
                            + Add Another Question
                        </button>

                        <div class="mt-4 border-top pt-3">
                            <button type="submit" class="btn btn-warning btn-lg">Update Quiz</button>
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
        CHECKBOX: 'checkbox'
    };
    
    // Global counter for question indices
    let questionIndex = 0;
    
    // PHP/Blade passes the existing quiz data to JavaScript
    const existingQuestions = @json($quiz->questions);


    // --- TEMPLATES ---
    
    // Template for a new question card
    const questionTemplate = (index, questionData = {}) => {
        // Determine selected type or default to MC
        const currentType = questionData.type || QUESTION_TYPES.MC;
        
        return `
        <div class="card mb-3 question-card" data-index="${index}">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Question #${index + 1}</h5>
                <button type="button" class="btn btn-sm btn-outline-danger remove-question-btn" data-index="${index}">Remove</button>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-8">
                        <label class="form-label">Question Text <span class="text-danger">*</span></label>
                        <textarea name="questions[${index}][text]" class="form-control" rows="2" required>${questionData.question_text || ''}</textarea>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Points <span class="text-danger">*</span></label>
                        <input type="number" name="questions[${index}][points]" class="form-control" value="${questionData.points || 1}" min="1" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="questions[${index}][type]" class="form-select question-type-select" data-index="${index}" required>
                            <option value="${QUESTION_TYPES.MC}" ${currentType === QUESTION_TYPES.MC ? 'selected' : ''}>Multiple Choice</option>
                            <option value="${QUESTION_TYPES.CHECKBOX}" ${currentType === QUESTION_TYPES.CHECKBOX ? 'selected' : ''}>Checkbox</option>
                            <option value="${QUESTION_TYPES.SA}" ${currentType === QUESTION_TYPES.SA ? 'selected' : ''}>Short Answer</option>
                            <option value="${QUESTION_TYPES.TF}" ${currentType === QUESTION_TYPES.TF ? 'selected' : ''}>True/False</option>
                        </select>
                    </div>
                </div>

                <hr class="mt-0">
                <div class="answers-container" id="answers-container-${index}">
                    ${renderAnswerFieldsContent(index, currentType, questionData)}
                </div>
            </div>
        </div>
        `;
    };

    // Template for the Short Answer input
    const shortAnswerTemplate = (index, questionData) => {
        // Short Answer is stored as the option_text of the first option
        const existingAnswer = questionData.options ? questionData.options[0].option_text : '';
        return `
            <div class="mb-3">
                <label class="form-label text-success">Correct Answer <span class="text-danger">*</span></label>
                <input type="text" name="questions[${index}][correct_answer]" class="form-control" 
                       placeholder="Enter the exact correct short answer" value="${existingAnswer}" required>
            </div>
        `;
    };

    // Template for a single option row (Radio button for MC/TF)
    const optionRow = (qIndex, oIndex, optionData = {}, isCorrect = false) => `
        <div class="input-group mb-2 option-row" data-o-index="${oIndex}">
            <div class="input-group-text">
                <input class="form-check-input mt-0 correct-option-radio" type="radio" 
                       name="questions[${qIndex}][correct_option_index]" 
                       value="${oIndex}" ${isCorrect ? 'checked' : ''} required>
            </div>
            <input type="text" name="questions[${qIndex}][options][${oIndex}][text]" class="form-control" placeholder="Option Text" value="${optionData.option_text || ''}" required>
            <button type="button" class="btn btn-outline-danger remove-option-btn" data-o-index="${oIndex}">X</button>
        </div>
    `;

    // Template for a single option row (Checkbox for Multiple Select)
    const checkboxOptionRow = (qIndex, oIndex, optionData = {}, isCorrect = false) => `
        <div class="input-group mb-2 option-row" data-o-index="${oIndex}">
            <div class="input-group-text">
                <input class="form-check-input mt-0 correct-option-checkbox" type="checkbox" 
                       name="questions[${qIndex}][correct_option_indices][]" 
                       value="${oIndex}" ${isCorrect ? 'checked' : ''}>
            </div>
            <input type="text" name="questions[${qIndex}][options][${oIndex}][text]" class="form-control" placeholder="Option Text" value="${optionData.option_text || ''}" required>
            <button type="button" class="btn btn-outline-danger remove-option-btn" data-o-index="${oIndex}">X</button>
        </div>
    `;

    // Template for Options container (used by MC, TF, and CHECKBOX)
    const optionsContainerTemplate = (qIndex, type, questionData) => {
        const rowTemplate = (type === QUESTION_TYPES.CHECKBOX) ? checkboxOptionRow : optionRow;
        let optionsHtml = '';
        let options = questionData.options || [{}, {}]; // Default to two empty options if none exist

        options.forEach((option, oIndex) => {
            const isCorrect = option.is_correct || false; // Check if option is marked correct in DB
            optionsHtml += rowTemplate(qIndex, oIndex, option, isCorrect);
        });

        // Ensure at least two options are present if loading empty
        if (options.length < 2) {
             optionsHtml += rowTemplate(qIndex, options.length, {});
             optionsHtml += rowTemplate(qIndex, options.length + 1, {});
        }

        return `
            <h6 class="mb-2">Options & Correct Answer(s) <span class="text-danger">*</span></h6>
            <div class="options-list" data-q-index="${qIndex}">
                ${optionsHtml}
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary mt-2 add-option-btn" data-q-index="${qIndex}">
                + Add Option
            </button>
        `;
    };

    // Main function to determine content based on type
    const renderAnswerFieldsContent = (qIndex, type, questionData = {}) => {
        if (type === QUESTION_TYPES.SA) {
            return shortAnswerTemplate(qIndex, questionData);
        } else if (type === QUESTION_TYPES.MC || type === QUESTION_TYPES.TF || type === QUESTION_TYPES.CHECKBOX) {
            
            let content = optionsContainerTemplate(qIndex, type, questionData);

            // SPECIAL CASE: True/False logic
            if (type === QUESTION_TYPES.TF) {
                 // Force options to True/False text and disable removal/adding
                 const options = questionData.options || [{}, {}];
                 const isTrueCorrect = options[0] ? options[0].is_correct : false;
                 const isFalseCorrect = options[1] ? options[1].is_correct : false;

                 content = `
                    <h6 class="mb-2">Correct Answer <span class="text-danger">*</span></h6>
                    <div class="options-list" data-q-index="${qIndex}">
                        <div class="input-group mb-2 option-row" data-o-index="0">
                            <div class="input-group-text">
                                <input class="form-check-input mt-0 correct-option-radio" type="radio" 
                                       name="questions[${qIndex}][correct_option_index]" value="0" ${isTrueCorrect ? 'checked' : ''} required>
                            </div>
                            <input type="text" name="questions[${qIndex}][options][0][text]" class="form-control" value="True" readonly>
                            <button type="button" class="btn btn-outline-secondary" disabled>X</button>
                        </div>
                        <div class="input-group mb-2 option-row" data-o-index="1">
                            <div class="input-group-text">
                                <input class="form-check-input mt-0 correct-option-radio" type="radio" 
                                       name="questions[${qIndex}][correct_option_index]" value="1" ${isFalseCorrect ? 'checked' : ''} required>
                            </div>
                            <input type="text" name="questions[${qIndex}][options][1][text]" class="form-control" value="False" readonly>
                            <button type="button" class="btn btn-outline-secondary" disabled>X</button>
                        </div>
                    </div>
                 `;
            }
            return content;
        }
        return ''; // Return empty string for unsupported types
    };
    
    // Renders the correct answer fields based on the selected type
    const renderAnswerFields = (qIndex, type, questionData = {}) => {
        const container = document.getElementById(`answers-container-${qIndex}`);
        container.innerHTML = renderAnswerFieldsContent(qIndex, type, questionData);
        
        // Re-bind the add option button to ensure it uses the correct row template
        const addBtn = container.querySelector('.add-option-btn');
        if (addBtn) {
            addBtn.onclick = function() {
                // Determine the row template function dynamically
                const typeSelect = document.querySelector(`.question-type-select[data-index="${qIndex}"]`);
                const currentType = typeSelect ? typeSelect.value : QUESTION_TYPES.MC;
                const rowTemplate = (currentType === QUESTION_TYPES.CHECKBOX) ? checkboxOptionRow : optionRow;
                
                const optionsList = this.previousElementSibling; // The options-list div
                addOptionRow(qIndex, optionsList, rowTemplate);
            };
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
        optionsList.insertAdjacentHTML('beforeend', rowTemplate(qIndex, currentOptions, {}));
    };


    // --- EVENT LISTENERS ---
    
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('questions-container');
        const addQuestionBtn = document.getElementById('add-question-btn');
        
        // 1. Initial Load: Check for existing questions
        if (existingQuestions.length > 0) {
            existingQuestions.forEach((qData, i) => {
                container.insertAdjacentHTML('beforeend', questionTemplate(i, qData));
                renderAnswerFields(i, qData.type, qData);
                questionIndex++;
            });
        } else {
             // If no existing questions (shouldn't happen on edit, but safe)
            container.insertAdjacentHTML('beforeend', questionTemplate(questionIndex));
            renderAnswerFields(questionIndex, QUESTION_TYPES.MC); 
            questionIndex++;
        }

        // 2. Add Question Button
        addQuestionBtn.addEventListener('click', function() {
            container.insertAdjacentHTML('beforeend', questionTemplate(questionIndex));
            renderAnswerFields(questionIndex, QUESTION_TYPES.MC);
            questionIndex++;
        });

        // 3. Delegation for Dynamic Events (Remove Question/Option)
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

                         // Update all names and IDs within the card
                         const content = qCard.innerHTML.replace(new RegExp(`questions\\[${oldIndex}\\]`, 'g'), `questions[${i}]`)
                                                          .replace(new RegExp(`answers-container-${oldIndex}`, 'g'), `answers-container-${i}`)
                                                          .replace(new RegExp(`data-index="${oldIndex}"`, 'g'), `data-index="${i}"`);
                         qCard.innerHTML = content;
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
                
                const qIndex = optionsList.getAttribute('data-q-index');
                const typeSelect = document.querySelector(`.question-type-select[data-index="${qIndex}"]`);
                const type = typeSelect ? typeSelect.value : QUESTION_TYPES.MC;

                // Check minimum options for option-based types (MC, TF, CB)
                if (optionsList.querySelectorAll('.option-row').length > 2) {
                    optionRow.remove();
                    // Re-index remaining options to maintain sequential array keys
                    optionsList.querySelectorAll('.option-row').forEach((row, i) => {
                        row.setAttribute('data-o-index', i);
                        
                        const input = row.querySelector('input[type="radio"], input[type="checkbox"]');
                        if (input) input.value = i;
                        
                        const textInput = row.querySelector('input[type="text"]');
                        if (textInput) {
                            textInput.name = textInput.name.replace(/options\[\d+\]/, `options[${i}]`);
                        }
                        
                        // Ensure radio buttons maintain a checked state if the checked one was deleted
                        if(input && input.type === 'radio' && i === 0 && !optionsList.querySelector('input[type="radio"]:checked')) {
                            input.checked = true;
                        }
                    });

                } else if (type !== QUESTION_TYPES.SA) {
                    alert("Multiple Choice, True/False, and Checkbox questions must have at least two options.");
                }
            }
        });

        // 4. Type Change Listener (Delegation required for dynamic elements)
        container.addEventListener('change', function(e) {
            if (e.target.classList.contains('question-type-select')) {
                const qIndex = e.target.getAttribute('data-index');
                const type = e.target.value;
                // When changing type, render with no previous answer data, as the format changes
                renderAnswerFields(qIndex, type, {}); 
            }
        });
        
    });
</script>
@endsection