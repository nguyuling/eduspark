@extends('layouts.app')

@section('content')

<div class="app">
  <!-- Main -->
  <main class="main">
    <div class="header">
      <div>
        <div class="title">Cipta Kuiz Baru</div>
        <div class="sub">Sediakan kuiz anda dengan soalan dan pilihan</div>
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

    <!-- Quiz Format Section -->
    <section class="panel" style="margin-left:40px; margin-right:40px; margin-bottom:20px;">
      <h2 style="margin:0 0 20px 0; font-size:18px; font-weight:700; border-bottom:2px solid #d4c5f9; padding-bottom:12px;">Format Kuiz</h2>

      <form method="POST" action="{{ route('teacher.quizzes.store') }}" id="quiz-form">
        @csrf

        <!-- Title -->
        <div style="margin-bottom: 20px;">
          <label for="title" style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 6px;">Tajuk Kuiz <span style="color: var(--danger);">*</span></label>
          <input 
            type="text" 
            id="title" 
            name="title" 
            placeholder="Contoh: Penilaian Bab 1"
            value="{{ old('title') }}" 
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
          >{{ old('description') }}</textarea>
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
              value="{{ old('due_at') }}"
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
              value="{{ old('max_attempts', 1) }}" 
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
                {{ old('is_published') ? 'checked' : '' }}
                style="width: 18px; height: 18px; cursor: pointer; flex-shrink: 0;"
              >
              <label for="is_published" style="margin: 0; cursor: pointer; font-weight: 500; font-size: 14px; white-space: nowrap;">Terbitkan Segera</label>
            </div>
          </div>
        </div>

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
      </form>

    <!-- Action Buttons - Outside all containers -->
    <div style="display:flex; gap:12px; justify-content:center; margin-top:60px; margin-bottom:40px; padding:0;">
      <button form="quiz-form" type="submit" style="display:inline-block; padding:12px 24px; background:linear-gradient(90deg,var(--accent),var(--accent-2)); color:#fff; text-decoration:none; border-radius:8px; font-weight:700; font-size:14px; transition:transform .2s ease, box-shadow .2s ease; box-shadow: 0 4px 12px rgba(106,77,247,0.3); border:none; cursor:pointer;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(106,77,247,0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(106,77,247,0.3)';">
        Simpan Kuiz
      </button>
      <a href="{{ route('teacher.quizzes.index') }}" style="display:inline-block; padding:12px 24px; background:transparent; color:var(--accent); border:2px solid var(--accent); text-decoration:none; border-radius:8px; font-weight:700; font-size:14px; transition:all .2s ease;" onmouseover="this.style.background='rgba(106,77,247,0.1)';" onmouseout="this.style.background='transparent';">
        Batal
      </a>
    </div>
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
        <section class="panel" style="margin-left:40px; margin-right:40px; margin-bottom:20px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; padding-bottom:12px; border-bottom:2px solid #d4c5f9;">
                <h3 style="margin:0; font-size:16px; font-weight:700;">Soalan ${index + 1}</h3>
                <button type="button" style="background:transparent; color:var(--danger); border:2px solid var(--danger); padding:6px 12px; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer;" class="remove-question-btn" data-index="${index}">Buang</button>
            </div>
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px;">
                <!-- Left: Teks Soalan -->
                <div>
                    <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px;">Teks Soalan <span style="color: var(--danger);">*</span></label>
                    <textarea name="questions[${index}][question_text]" rows="5" placeholder="Masukkan teks soalan di sini..." required style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 2px solid #d1d5db; background: transparent; color: inherit; font-size: 14px; outline: none; resize: vertical; box-sizing: border-box; transition: border-color 0.2s ease, background 0.2s ease;" onmouseover="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';" onmouseout="this.style.borderColor='#d1d5db'; this.style.background='transparent';" onfocus="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';" onblur="this.style.borderColor='#d1d5db'; this.style.background='transparent';"></textarea>
                </div>
                <!-- Right: Markah and Jenis -->
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div>
                        <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px;">Markah <span style="color: var(--danger);">*</span></label>
                        <input type="number" name="questions[${index}][points]" value="1" min="1" required style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 2px solid #d1d5db; background: transparent; color: inherit; font-size: 14px; outline: none; box-sizing: border-box; height: 42px; transition: border-color 0.2s ease, background 0.2s ease;" onmouseover="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';" onmouseout="this.style.borderColor='#d1d5db'; this.style.background='transparent';" onfocus="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';" onblur="this.style.borderColor='#d1d5db'; this.style.background='transparent';"/>
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px;">Jenis Soalan <span style="color: var(--danger);">*</span></label>
                        <select name="questions[${index}][type]" class="question-type-select" data-index="${index}" required style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 2px solid #d1d5db; background: transparent; color: inherit; font-size: 14px; outline: none; box-sizing: border-box; height: 42px; transition: border-color 0.2s ease, background 0.2s ease; cursor: pointer;" onmouseover="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';" onmouseout="this.style.borderColor='#d1d5db'; this.style.background='transparent';" onfocus="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';" onblur="this.style.borderColor='#d1d5db'; this.style.background='transparent';">
                            <option value="${QUESTION_TYPES.MC}">Pilihan Berganda</option>
                            <option value="${QUESTION_TYPES.CHECKBOX}">Kotak Semak</option>
                            <option value="${QUESTION_TYPES.SA}">Jawapan Pendek</option>
                            <option value="${QUESTION_TYPES.TF}">Benar/Salah</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="answers-container" id="answers-container-${index}" style="margin-top:20px;">
                ${optionTemplate(index, QUESTION_TYPES.MC)} 
            </div>
        </section>
    `;

    // Template for the Short Answer input (Only one text box)
    const shortAnswerTemplate = (index) => `
        <div style="margin-top:12px;">
            <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: var(--success);">Jawapan Betul <span style="color: var(--danger);">*</span></label>
            <input type="text" name="questions[${index}][correct_answer]" placeholder="Masukkan jawapan yang tepat" required style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 2px solid #d1d5db; background: transparent; color: inherit; font-size: 14px; outline: none; box-sizing: border-box; transition: border-color 0.2s ease, background 0.2s ease;" onmouseover="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';" onmouseout="this.style.borderColor='#d1d5db'; this.style.background='transparent';" onfocus="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';" onblur="this.style.borderColor='#d1d5db'; this.style.background='transparent';">
        </div>
    `;

    // Template for Options container (used by MC, TF, and CHECKBOX)
    const optionTemplate = (qIndex, type) => {
        const rowTemplate = (type === QUESTION_TYPES.CHECKBOX) ? checkboxOptionRow : optionRow;

        return `
            <h6 style="margin:0 0 12px 0; font-weight:600; font-size:13px;">Pilihan & Jawapan Betul <span style="color: var(--danger);">*</span></h6>
            <div class="options-list" data-q-index="${qIndex}" style="display:flex; flex-direction:column; gap:8px;">
                ${rowTemplate(qIndex, 0)}
                ${rowTemplate(qIndex, 1)}
            </div>
            <button type="button" style="display:inline-block; padding:8px 14px; background:transparent; color:var(--accent); border:1px solid var(--accent); text-decoration:none; border-radius:6px; font-weight:600; font-size:12px; cursor:pointer; margin-top:12px;" class="add-option-btn" data-q-index="${qIndex}">
                + Tambah Pilihan
            </button>
        `;
    };
    
    // Template for a single option row (Radio button for MC/TF)
    const optionRow = (qIndex, oIndex) => `
        <div style="display:flex; gap:8px; align-items:flex-start;" class="option-row" data-o-index="${oIndex}">
            <input class="form-check-input mt-0 correct-option-radio" type="radio" 
                   name="questions[${qIndex}][correct_answer]" 
                   value="" 
                   data-option-text-target 
                   ${oIndex === 0 ? 'checked' : ''} required style="width:18px; height:18px; margin-top:11px; cursor:pointer;">
            <input type="text" 
                   name="questions[${qIndex}][options][]" 
                   class="option-text-input" 
                   placeholder="Teks Pilihan" 
                   required
                   oninput="updateRadioValue(this)"
                   style="flex:1; padding: 11px 14px; border-radius: 8px; border: 2px solid #d1d5db; background: transparent; color: inherit; font-size: 14px; outline: none; box-sizing: border-box; transition: border-color 0.2s ease, background 0.2s ease;" onmouseover="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';" onmouseout="this.style.borderColor='#d1d5db'; this.style.background='transparent';" onfocus="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';" onblur="this.style.borderColor='#d1d5db'; this.style.background='transparent';">
            <button type="button" style="background:transparent; color:var(--danger); border:1px solid var(--danger); padding:8px 10px; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer;" class="remove-option-btn" data-o-index="${oIndex}">✕</button>
        </div>
    `;

    // Template for a single option row (Checkbox)
    const checkboxOptionRow = (qIndex, oIndex) => `
        <div style="display:flex; gap:8px; align-items:flex-start;" class="option-row" data-o-index="${oIndex}">
            <input class="form-check-input mt-0 correct-option-checkbox" type="checkbox" 
                   name="questions[${qIndex}][correct_answers][]" 
                   value="" 
                   data-option-text-target
                   style="width:18px; height:18px; margin-top:11px; cursor:pointer;">
            <input type="text" 
                   name="questions[${qIndex}][options][]" 
                   class="option-text-input" 
                   placeholder="Teks Pilihan" 
                   required
                   oninput="updateCheckboxValue(this)"
                   style="flex:1; padding: 11px 14px; border-radius: 8px; border: 2px solid #d1d5db; background: transparent; color: inherit; font-size: 14px; outline: none; box-sizing: border-box; transition: border-color 0.2s ease, background 0.2s ease;" onmouseover="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';" onmouseout="this.style.borderColor='#d1d5db'; this.style.background='transparent';" onfocus="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';" onblur="this.style.borderColor='#d1d5db'; this.style.background='transparent';">
            <button type="button" style="background:transparent; color:var(--danger); border:1px solid var(--danger); padding:8px 10px; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer;" class="remove-option-btn" data-o-index="${oIndex}">✕</button>
        </div>
    `;

    // Function to dynamically update the radio button's value when the option text changes
    function updateRadioValue(inputElement) {
        const optionText = inputElement.value;
        const radio = inputElement.closest('div').querySelector('[data-option-text-target]');
        if (radio && radio.type === 'radio') {
            radio.value = optionText;
        }
    }

    // Function to dynamically update the checkbox button's value when the option text changes
    function updateCheckboxValue(inputElement) {
        const optionText = inputElement.value;
        const checkbox = inputElement.closest('div').querySelector('[data-option-text-target]');
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
                    <div style="display:flex; gap:8px; align-items:flex-start;" class="option-row" data-o-index="0">
                        <input class="form-check-input mt-0 correct-option-radio" type="radio" 
                               name="questions[${qIndex}][correct_answer]" value="True" checked required style="width:18px; height:18px; margin-top:11px; cursor:pointer;">
                        <input type="text" name="questions[${qIndex}][options][]" class="option-text-input" value="Benar" readonly style="flex:1; padding: 11px 14px; border-radius: 8px; border: 1px solid var(--control-border); background: var(--input-bg); color: inherit; font-size: 14px; outline: none; box-sizing: border-box;" oninput="updateRadioValue(this)">
                        <button type="button" style="background:transparent; color:var(--muted); border:1px solid var(--muted); padding:8px 10px; border-radius:6px; font-size:12px; font-weight:600; cursor:not-allowed; opacity:0.5;" disabled>✕</button>
                    </div>
                    <div style="display:flex; gap:8px; align-items:flex-start;" class="option-row" data-o-index="1">
                        <input class="form-check-input mt-0 correct-option-radio" type="radio" 
                               name="questions[${qIndex}][correct_answer]" value="False" required style="width:18px; height:18px; margin-top:11px; cursor:pointer;">
                        <input type="text" name="questions[${qIndex}][options][]" class="option-text-input" value="Salah" readonly style="flex:1; padding: 11px 14px; border-radius: 8px; border: 1px solid var(--control-border); background: var(--input-bg); color: inherit; font-size: 14px; outline: none; box-sizing: border-box;" oninput="updateRadioValue(this)">
                        <button type="button" style="background:transparent; color:var(--muted); border:1px solid var(--muted); padding:8px 10px; border-radius:6px; font-size:12px; font-weight:600; cursor:not-allowed; opacity:0.5;" disabled>✕</button>
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
            alert("Soalan tidak boleh mempunyai lebih daripada 10 pilihan.");
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
                const card = e.target.closest('[question-card]');
                const questionCards = container.querySelectorAll('[question-card]');
                if (questionCards.length > 1) {
                     // Get the index of the card being removed
                     const removedIndex = parseInt(card.getAttribute('data-index'));
                     card.remove();
                     
                     // Re-index all cards that came after the removed one
                     container.querySelectorAll('[question-card]').forEach((qCard, i) => {
                         const currentCardIndex = parseInt(qCard.getAttribute('data-index'));
                         
                         // Only process cards that need re-indexing
                         if(currentCardIndex > removedIndex) {
                             const newIndex = currentCardIndex - 1;
                             
                             // Update attribute
                             qCard.setAttribute('data-index', newIndex);
                             
                             // Update display number
                             qCard.querySelector('h5').textContent = `Soalan #${newIndex + 1}`;
                             
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
                    alert("Kuiz mesti mempunyai sekurang-kurangnya satu soalan.");
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
                    alert("Soalan Pilihan Berganda/Benar-Salah/Kotak Semak mesti mempunyai sekurang-kurangnya dua pilihan.");
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