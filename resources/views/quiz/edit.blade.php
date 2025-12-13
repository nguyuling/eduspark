@extends('layouts.app')

@section('content')

<div class="app">
  <!-- Main -->
  <main class="main">
    <div class="header">
      <div>
        <div class="title">Edit Kuiz: {{ $quiz->title }}</div>
        <div class="sub">Ubah soalan dan tetapan kuiz anda</div>
      </div>
      <a href="{{ route('teacher.quizzes.index') }}" style="display:inline-block; padding:12px 24px; background:transparent; color:var(--accent); border:2px solid var(--accent); text-decoration:none; border-radius:8px; font-weight:700; font-size:14px; transition:all .2s ease;" onmouseover="this.style.background='rgba(106,77,247,0.1)';" onmouseout="this.style.background='transparent';">
        ← Kembali ke Kuiz
      </a>
    </div>

    @if (session('error'))
      <div style="background:var(--danger);color:#fff;padding:12px 14px;border-radius:var(--card-radius);margin-bottom:20px;margin-left:40px;margin-right:40px;font-size:14px;">{{ session('error') }}</div>
    @endif

    <!-- Error Messages -->
    @if ($errors->any())
      <section style="margin-left:40px; margin-right:40px; margin-bottom:20px; background: rgba(230, 57, 70, 0.1); border-left: 3px solid var(--danger); padding:16px 18px; border-radius:var(--card-radius);">
        <div style="font-weight: 700; color: var(--danger); margin-bottom: 8px;">Sila betulkan ralat berikut:</div>
        <ul style="margin: 0; padding-left: 20px; color: var(--danger); font-size: 14px;">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </section>
    @endif

    <!-- Start Main Form -->
    <form method="POST" action="{{ route('teacher.quizzes.update', $quiz->id) }}" id="quiz-form">
      @csrf
      @method('PUT')

      <!-- Quiz Format Section -->
      <section class="panel" style="margin-left:40px; margin-right:40px; margin-bottom:20px;">
        <h2 style="margin:0 0 20px 0; font-size:18px; font-weight:700; border-bottom:2px solid #d4c5f9; padding-bottom:12px;">Format Kuiz</h2>

        <!-- Title -->
        <div style="margin-bottom: 20px;">
          <label for="title" style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 6px;">Tajuk Kuiz <span style="color: var(--danger);">*</span></label>
          <input 
            type="text" 
            id="title" 
            name="title" 
            placeholder="Contoh: Penilaian Bab 1"
            value="{{ old('title', $quiz->title) }}" 
            required
            style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 2px solid #d1d5db; background: transparent; color: inherit; font-size: 14px; outline: none; transition: border-color 0.2s ease, background 0.2s ease; box-sizing: border-box;" 
            onmouseover="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';"
            onmouseout="this.style.borderColor='#d1d5db'; this.style.background='transparent';"
            onfocus="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';"
            onblur="this.style.borderColor='#d1d5db'; this.style.background='transparent';"
          >
          @error('title')<span style="color: var(--danger); font-size: 12px;">{{ $message }}</span>@enderror
        </div>

        <!-- Description -->
        <div style="margin-bottom: 20px;">
          <label for="description" style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 6px;">Penerangan (Pilihan)</label>
          <textarea 
            id="description" 
            name="description" 
            rows="3"
            placeholder="Tambah arahan atau maklumat tambahan..."
            style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 2px solid #d1d5db; background: transparent; color: inherit; font-size: 14px; outline: none; transition: border-color 0.2s ease, background 0.2s ease; resize: vertical; box-sizing: border-box;"
            onmouseover="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';"
            onmouseout="this.style.borderColor='#d1d5db'; this.style.background='transparent';"
            onfocus="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';"
            onblur="this.style.borderColor='#d1d5db'; this.style.background='transparent';"
          >{{ old('description', $quiz->description) }}</textarea>
          @error('description')<span style="color: var(--danger); font-size: 12px;">{{ $message }}</span>@enderror
        </div>

        <!-- Bottom Row: Due Date, Max Attempts, Publish Checkbox -->
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
          <!-- Due Date -->
          <div>
            <label for="due_at" style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 6px;">Tarikh Akhir (Pilihan)</label>
            <input 
              type="datetime-local" 
              id="due_at" 
              name="due_at" 
              value="{{ old('due_at', $quiz->due_at?->format('Y-m-d\TH:i')) }}"
              style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 2px solid #d1d5db; background: transparent; color: inherit; font-size: 14px; outline: none; transition: border-color 0.2s ease, background 0.2s ease; box-sizing: border-box;"
              onmouseover="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';"
              onmouseout="this.style.borderColor='#d1d5db'; this.style.background='transparent';"
              onfocus="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';"
              onblur="this.style.borderColor='#d1d5db'; this.style.background='transparent';"
            >
            @error('due_at')<span style="color: var(--danger); font-size: 12px;">{{ $message }}</span>@enderror
          </div>

          <!-- Max Attempts -->
          <div>
            <label for="max_attempts" style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 6px;">Percubaan Maksimum <span style="color: var(--danger);">*</span></label>
            <input 
              type="number" 
              id="max_attempts" 
              name="max_attempts" 
              value="{{ old('max_attempts', $quiz->max_attempts) }}" 
              min="1"
              required
              style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 2px solid #d1d5db; background: transparent; color: inherit; font-size: 14px; outline: none; transition: border-color 0.2s ease, background 0.2s ease; box-sizing: border-box;"
              onmouseover="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';"
              onmouseout="this.style.borderColor='#d1d5db'; this.style.background='transparent';"
              onfocus="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';"
              onblur="this.style.borderColor='#d1d5db'; this.style.background='transparent';"
            >
            @error('max_attempts')<span style="color: var(--danger); font-size: 12px;">{{ $message }}</span>@enderror
          </div>

          <!-- Publish Checkbox -->
          <div style="display: flex; align-items: flex-end; padding-bottom: 2px;">
            <div style="display: flex; align-items: center; gap: 12px; padding: 11px 14px; background: rgba(106,77,247,0.05); border-radius: 8px; border: 2px solid #d1d5db; width: 100%;">
              <input 
                type="checkbox" 
                id="is_published" 
                name="is_published"
                {{ old('is_published', $quiz->is_published) ? 'checked' : '' }}
                style="width: 18px; height: 18px; cursor: pointer; flex-shrink: 0;"
              >
              <label for="is_published" style="margin: 0; cursor: pointer; font-weight: 500; font-size: 14px; white-space: nowrap;">Terbitkan Segera</label>
            </div>
          </div>
        </div>
      </section>

      <!-- Questions Header -->
      <section style="margin-left:40px; margin-right:40px; margin-top:40px; margin-bottom:20px;">
        <h2 style="margin:0; font-size:18px; font-weight:700;">Soalan <span style="color: var(--danger);">*</span></h2>
      </section>

      <!-- Questions Container - Each question appears as its own section -->
      <div id="questions-container"></div>

      <!-- Add Question Button -->
      <section style="margin-left:40px; margin-right:40px; margin-bottom:40px;">
        <button type="button" id="add-question-btn" style="display:inline-block; padding:10px 18px; background:transparent; color:var(--accent); border:2px solid var(--accent); text-decoration:none; border-radius:8px; font-weight:600; font-size:14px; cursor:pointer;" onmouseover="this.style.background='rgba(106,77,247,0.1)';" onmouseout="this.style.background='transparent';">
          ➕ Tambah Soalan
        </button>
      </section>

      <!-- Action Buttons - Outside all containers but inside form -->
      <div style="display:flex; gap:12px; justify-content:center; margin-top:40px; margin-bottom:40px; padding:0;">
        <button type="submit" style="display:inline-block; padding:12px 24px; background:linear-gradient(90deg,var(--accent),var(--accent-2)); color:#fff; text-decoration:none; border-radius:8px; font-weight:700; font-size:14px; transition:transform .2s ease, box-shadow .2s ease; box-shadow: 0 4px 12px rgba(106,77,247,0.3); border:none; cursor:pointer;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(106,77,247,0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(106,77,247,0.3)';">
          Simpan Kuiz
        </button>
        <a href="{{ route('teacher.quizzes.show', $quiz->id) }}" style="display:inline-block; padding:12px 24px; background:transparent; color:var(--accent); border:2px solid var(--accent); text-decoration:none; border-radius:8px; font-weight:700; font-size:14px; transition:all .2s ease;" onmouseover="this.style.background='rgba(106,77,247,0.1)';" onmouseout="this.style.background='transparent';">
          Batal
        </a>
      </div>
    </form>
  </main>
</div>

<script>

<script>
    // Define the question types based on the Question Model constants
    const QUESTION_TYPES = {
        MC: 'multiple_choice',
        SA: 'short_answer',
        TF: 'true_false',
        CHECKBOX: 'checkbox'
    };
    
    // Global counter for question indices (initialized in DOMContentLoaded)
    let questionIndex = 0;
    
    // PHP/Blade passes the existing quiz data to JavaScript
    const existingQuestions = @json($quiz->questions);


    // --- HELPER FUNCTION ---
    
    /**
     * Updates the value of the corresponding correct-answer input (radio/checkbox) 
     * when the option text input is changed, if the option is currently marked correct.
     */
    function updateCorrectAnswerValue(textInput) {
        const optionRow = textInput.closest('.option-row');
        const correctInput = optionRow.querySelector('.correct-option-radio, .correct-option-checkbox');
        
        // Only update the value of the radio/checkbox if it is currently checked
        if (correctInput && correctInput.checked) {
            correctInput.value = textInput.value;
        }
    }


    // --- TEMPLATES ---
    
    // Template for a new question card
    const questionTemplate = (index, questionData = {}) => {
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
                        {{-- FIX: Changed [text] to [question_text] --}}
                        <textarea name="questions[${index}][question_text]" class="form-control" rows="2" required>${questionData.question_text || ''}</textarea>
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
        // FIX: Use questionData.correct_answer directly
        const existingAnswer = questionData.correct_answer || '';
        return `
            <div class="mb-3">
                <label class="form-label text-success">Correct Answer <span class="text-danger">*</span></label>
                {{-- FIX: Correct name for Short Answer and ensure value is preserved --}}
                <input type="text" name="questions[${index}][correct_answer]" class="form-control" 
                       placeholder="Enter the exact correct short answer" value="${existingAnswer}" required>
                
                {{-- FIX: Add a hidden options field for short answer, as the controller expects one --}}
                <input type="hidden" name="questions[${index}][options][]" value="Short Answer Placeholder">
            </div>
        `;
    };

    // Template for a single option row (Radio button for MC/TF)
    const optionRow = (qIndex, oIndex, optionData = {}, isCorrect = false) => `
        <div class="input-group mb-2 option-row" data-o-index="${oIndex}">
            <div class="input-group-text">
                <input class="form-check-input mt-0 correct-option-radio" type="radio" 
                       {{-- FIX: Name is correct_answer, value is the option text --}}
                       name="questions[${qIndex}][correct_answer]" 
                       value="${optionData.option_text || ''}" ${isCorrect ? 'checked' : ''} required>
            </div>
            {{-- FIX: Simplified option text name to be an array of strings --}}
            <input type="text" name="questions[${qIndex}][options][]" class="form-control option-text-input" 
                   placeholder="Option Text" value="${optionData.option_text || ''}" required
                   oninput="updateCorrectAnswerValue(this)">
            <button type="button" class="btn btn-outline-danger remove-option-btn" data-o-index="${oIndex}">X</button>
        </div>
    `;

    // Template for a single option row (Checkbox for Multiple Select)
    const checkboxOptionRow = (qIndex, oIndex, optionData = {}, isCorrect = false) => `
        <div class="input-group mb-2 option-row" data-o-index="${oIndex}">
            <div class="input-group-text">
                <input class="form-check-input mt-0 correct-option-checkbox" type="checkbox" 
                       {{-- FIX: Name is correct_answer[], value is the option text --}}
                       name="questions[${qIndex}][correct_answer][]" 
                       value="${optionData.option_text || ''}" ${isCorrect ? 'checked' : ''}>
            </div>
            {{-- FIX: Simplified option text name to be an array of strings --}}
            <input type="text" name="questions[${qIndex}][options][]" class="form-control option-text-input" 
                   placeholder="Option Text" value="${optionData.option_text || ''}" required
                   oninput="updateCorrectAnswerValue(this)">
            <button type="button" class="btn btn-outline-danger remove-option-btn" data-o-index="${oIndex}">X</button>
        </div>
    `;

    // Template for Options container (used by MC, TF, and CHECKBOX)
    const optionsContainerTemplate = (qIndex, type, questionData) => {
        const rowTemplate = (type === QUESTION_TYPES.CHECKBOX) ? checkboxOptionRow : optionRow;
        let optionsHtml = '';
        let options = questionData.options || []; // Start with actual options

        options.forEach((option, oIndex) => {
            const isCorrect = option.is_correct || false; 
            optionsHtml += rowTemplate(qIndex, oIndex, option, isCorrect);
        });

        // Ensure at least two options are present
        while (options.length + optionsHtml.split('option-row').length - 1 < 2) {
             optionsHtml += rowTemplate(qIndex, options.length + optionsHtml.split('option-row').length - 1, {});
        }
        
        // Find the highest existing index to continue adding options
        const highestIndex = options.length > 0 ? options.length : 0;
        
        return `
            <h6 class="mb-2">Options & Correct Answer(s) <span class="text-danger">*</span></h6>
            <div class="options-list" data-q-index="${qIndex}" data-next-o-index="${highestIndex}">
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
                 // Force options to True/False text
                 const options = questionData.options || [
                     { option_text: 'True', is_correct: false }, 
                     { option_text: 'False', is_correct: false }
                 ];
                 
                 const isTrueCorrect = options.find(o => o.option_text === 'True')?.is_correct || false;
                 const isFalseCorrect = options.find(o => o.option_text === 'False')?.is_correct || false;

                 content = `
                    <h6 class="mb-2">Correct Answer <span class="text-danger">*</span></h6>
                    <div class="options-list" data-q-index="${qIndex}">
                        <div class="input-group mb-2 option-row" data-o-index="0">
                            <div class="input-group-text">
                                <input class="form-check-input mt-0 correct-option-radio" type="radio" 
                                       name="questions[${qIndex}][correct_answer]" value="True" ${isTrueCorrect ? 'checked' : ''} required>
                            </div>
                            <input type="text" name="questions[${qIndex}][options][]" class="form-control option-text-input" value="True" readonly>
                            <button type="button" class="btn btn-outline-secondary" disabled>X</button>
                        </div>
                        <div class="input-group mb-2 option-row" data-o-index="1">
                            <div class="input-group-text">
                                <input class="form-check-input mt-0 correct-option-radio" type="radio" 
                                       name="questions[${qIndex}][correct_answer]" value="False" ${isFalseCorrect ? 'checked' : ''} required>
                            </div>
                            <input type="text" name="questions[${qIndex}][options][]" class="form-control option-text-input" value="False" readonly>
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
        
        // Re-bind the add option button logic
        const addBtn = container.querySelector('.add-option-btn');
        if (addBtn) {
            addBtn.onclick = function() {
                const typeSelect = document.querySelector(`.question-type-select[data-index="${qIndex}"]`);
                const currentType = typeSelect ? typeSelect.value : QUESTION_TYPES.MC;
                const rowTemplate = (currentType === QUESTION_TYPES.CHECKBOX) ? checkboxOptionRow : optionRow;
                
                const optionsList = this.previousElementSibling; 
                addOptionRow(qIndex, optionsList, rowTemplate);
            };
        }
    };

    // Adds a new option row to a question (Now accepts the rowTemplate function)
    const addOptionRow = (qIndex, optionsList, rowTemplate) => {
        const currentOptionsCount = optionsList.querySelectorAll('.option-row').length;
        if (currentOptionsCount >= 10) { 
            alert("A question cannot have more than 10 options.");
            return;
        }
        
        // Calculate the next index to ensure the option array is sequential
        const nextOIndex = currentOptionsCount;

        // Use the passed-in rowTemplate (radio or checkbox)
        optionsList.insertAdjacentHTML('beforeend', rowTemplate(qIndex, nextOIndex, {}));
    };


    // --- EVENT LISTENERS ---
    
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('questions-container');
        const addQuestionBtn = document.getElementById('add-question-btn');
        
        // 1. Initial Load: Populate existing questions and set starting index
        if (existingQuestions.length > 0) {
            existingQuestions.forEach((qData, i) => {
                container.insertAdjacentHTML('beforeend', questionTemplate(i, qData));
                renderAnswerFields(i, qData.type, qData);
                // The re-indexing logic below will fix any gaps caused by previous removals
            });
            // FIX: Set the global index counter to the correct starting point
            questionIndex = existingQuestions.length;
            
        } else {
             // If no existing questions (e.g., if user deleted them all and refreshed)
            container.insertAdjacentHTML('beforeend', questionTemplate(questionIndex));
            renderAnswerFields(questionIndex, QUESTION_TYPES.MC); 
            questionIndex++;
        }
        
        // Function to handle re-indexing dynamically after a question is removed
        const reIndexQuestions = () => {
            container.querySelectorAll('.question-card').forEach((qCard, i) => {
                const oldIndex = parseInt(qCard.getAttribute('data-index'));
                if (oldIndex !== i) {
                    qCard.setAttribute('data-index', i);

                    // A more robust way to update names: find all elements with old index in name and replace it
                    qCard.querySelectorAll('[name]').forEach(el => {
                        el.name = el.name.replace(`questions[${oldIndex}]`, `questions[${i}]`);
                    });
                    
                    // Update element IDs and data attributes
                    qCard.querySelector('.remove-question-btn').setAttribute('data-index', i);
                    const header = qCard.querySelector('.card-header h5');
                    if(header) header.textContent = `Question #${i + 1}`;
                }
            });
            // Reset the global counter
            questionIndex = container.querySelectorAll('.question-card').length;
        };


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
                     reIndexQuestions(); // Call the re-indexing function
                } else {
                    alert("A quiz must have at least one question.");
                }
            }
            
            // Remove Option
            if (e.target.classList.contains('remove-option-btn')) {
                const optionRow = e.target.closest('.option-row');
                const optionsList = optionRow.closest('.options-list');
                const typeSelect = optionRow.closest('.question-card').querySelector('.question-type-select');
                const type = typeSelect ? typeSelect.value : QUESTION_TYPES.MC;
                
                // Check minimum options for option-based types (MC, TF, CB)
                if (optionsList.querySelectorAll('.option-row').length > 2) {
                    optionRow.remove();
                    
                    // Ensure the correct-answer radio button stays selected if the checked one was deleted
                    if (type !== QUESTION_TYPES.CHECKBOX && !optionsList.querySelector('.correct-option-radio:checked')) {
                        const firstRadio = optionsList.querySelector('.correct-option-radio');
                        if (firstRadio) firstRadio.checked = true;
                    }
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
                // When changing type, render without previous answer data to avoid format issues
                renderAnswerFields(qIndex, type, {}); 
            }
        });
        
    });
</script>
@endsection