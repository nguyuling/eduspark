@extends('layouts.app')

@section('content')

<div class="app">
  <!-- Main -->
  <main class="main">
    <div class="header">
      <div>
        <div class="title">Kemaskini Kuiz: {{ $quiz->title }}</div>
        <div class="sub">Ubah soalan dan tetapan kuiz anda</div>
      </div>
        <a href="{{ route('teacher.quizzes.index') }}" class="btn-kembali" onclick="return confirm('Perubahan anda tidak akan disimpan. Adakah anda pasti untuk meninggalkan halaman ini?');">
            <i class="bi bi-arrow-left"></i>Kembali
        </a>
    </div>

    @if (session('success'))
      <div class="alert-success">{{ session('success') }}</div>
    @endif

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
    <section class="panel" style="margin-bottom:20px; margin-top:10px;">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; border-bottom:2px solid #d4c5f9; padding-bottom:12px;">
          <h2 style="margin:0; font-size:18px; font-weight:700;">Format Kuiz</h2>

        </div>

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
      <section style="margin-left:5px; margin-top:40px; margin-bottom:20px;">
        <h2 style="font-size:18px; font-weight:700;">Soalan <span style="color: var(--danger);">*</span></h2>
      </section>

      <!-- Questions Container - Each question appears as its own section -->
      <div id="questions-container"></div>

      <!-- Add Question Button -->
      <section style="margin-left:5px; margin-bottom:40px;">
        <button type="button" id="add-question-btn" class="btn-add-question" style="display:inline-flex !important; align-items:center !important; gap:8px !important; padding:10px 18px !important; background:transparent !important; color:var(--accent) !important; border:2px solid var(--accent) !important; text-decoration:none !important; border-radius:8px !important; font-weight:600 !important; font-size:13px !important; cursor:pointer !important; transition:all 0.2s ease !important;" onmouseover="this.style.background='rgba(168, 85, 247, 0.1)'; this.style.borderColor='var(--accent)'" onmouseout="this.style.background='transparent'; this.style.borderColor='var(--accent)'">
          <i class="bi bi-plus-lg"></i>Tambah Soalan
        </button>
      </section>

      <!-- Action Buttons Row -->
      <div style="display:flex; gap:12px; justify-content:center; margin-top:40px; margin-bottom:40px; padding:0;">
        <button type="submit" class="btn-submit" style="display:inline-flex !important; align-items:center !important; gap:8px !important; padding:14px 26px !important; background:linear-gradient(90deg, #A855F7, #9333EA) !important; color:#fff !important; border:none !important; text-decoration:none !important; border-radius:8px !important; font-weight:600 !important; font-size:13px !important; cursor:pointer !important; transition:all 0.2s ease !important; box-shadow:0 2px 8px rgba(168, 85, 247, 0.3) !important;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(168, 85, 247, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(168, 85, 247, 0.3)'" onclick="return validateBeforeSubmit()">
          <i class="bi bi-save"></i>Simpan Kuiz
        </button>
    </form>
      </div>
  </main>
</div>

<script>
    // Define the question types based on the Question Model constants
    const QUESTION_TYPES = {
        MC: 'multiple_choice',
        SA: 'short_answer',
        TF: 'true_false',
        CHECKBOX: 'checkbox',
        CODING: 'coding'
    };
    
    // Global counter for question indices
    let questionIndex = 0;
    
    // PHP/Blade passes the existing quiz data to JavaScript
    const existingQuestions = @json($quiz->questions);

    // Template for a new question card
    const questionTemplate = (index) => `
        <section class="panel" style="margin-bottom:20px;" question-card data-index="${index}">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; padding-bottom:12px; border-bottom:2px solid #d4c5f9;">
                <h3 style="margin:0; font-size:16px; font-weight:700;">Soalan ${index + 1}</h3>
                <div style="display:flex; gap:8px; align-items:center;">
                    <button type="button" title="Pindah ke atas" style="background:transparent; color:var(--accent); border:2px solid var(--accent); padding:6px 12px; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer;" class="move-up-btn" data-index="${index}"><i class="bi bi-arrow-up"></i></button>
                    <button type="button" title="Pindah ke bawah" style="background:transparent; color:var(--accent); border:2px solid var(--accent); padding:6px 12px; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer;" class="move-down-btn" data-index="${index}"><i class="bi bi-arrow-down"></i></button>
                    <button type="button" style="background:transparent; color:var(--danger); border:2px solid var(--danger); padding:6px 12px; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer;" class="remove-question-btn" data-index="${index}"><i class="bi bi-trash"></i></button>
                </div>
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
                            <option value="${QUESTION_TYPES.CODING}">Koding</option>
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
    const shortAnswerTemplate = (index, correctAnswer = '') => `
        <div style="margin-top:12px;">
            <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: var(--success);">Jawapan Betul <span style="color: var(--danger);">*</span></label>
            <input type="text" name="questions[${index}][correct_answer]" value="${correctAnswer}" placeholder="Masukkan jawapan yang tepat" required style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 2px solid #d1d5db; background: transparent; color: inherit; font-size: 14px; outline: none; box-sizing: border-box; transition: border-color 0.2s ease, background 0.2s ease;" onmouseover="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';" onmouseout="this.style.borderColor='#d1d5db'; this.style.background='transparent';" onfocus="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';" onblur="this.style.borderColor='#d1d5db'; this.style.background='transparent';">
        </div>
    `;

    // Template for the Coding question input (Java only)
    const codingTemplate = (index) => {
        return `<div style="margin-top:12px;">
<!-- Full Code Input Section with Inline Checkboxes -->
<div style="margin-bottom:20px;">
<label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px;">Kod Penuh<span style="color: var(--danger);">*</span></label>
<div style="display: flex; gap: 0; border-radius: 8px; border: 2px solid #d1d5db; overflow: hidden; background: #f5f5f5;">
  <!-- Checkbox Column -->
  <div id="code-checkboxes-${index}" style="display: flex; flex-direction: column; background: linear-gradient(to bottom, #fafafa, #f5f5f5); border-right: 2px solid #e5e7eb; min-width: 40px; padding: 11px 4px; overflow-y: auto; font-size: 13px; line-height: 1.5; gap: 0px;">
  </div>
  
  <!-- Line Numbers Column -->
  <div id="code-lines-${index}" style="position: relative; background: #f9fafb; padding: 11px 8px; text-align: right; font-size: 13px; font-family: 'Courier New', monospace; color: #9ca3af; border-right: 2px solid #e5e7eb; line-height: 1.5; user-select: none; min-width: 40px;">1</div>
  
  <!-- Code Textarea -->
  <div style="flex: 1; position: relative; overflow: hidden;">
    <textarea name="questions[${index}][coding_full_code]" class="code-full-textarea" data-index="${index}" rows="1" placeholder="Masukkan kod Java lengkap di sini..." required style="width: 100%; padding: 11px 12px; border: none; background: transparent; color: inherit; font-size: 13px; font-family: 'Courier New', monospace; outline: none; resize: vertical; box-sizing: border-box; line-height: 1.5;" oninput="updateCodeLineNumbers(this, ${index})"></textarea>
  </div>
</div>
<div style="font-size:12px; color:#888; margin-top:8px; margin-bottom:12px;">
<i class="bi bi-info-circle"></i> Klik checkbox untuk untuk baris yang harus dijawab oleh pelajar | ENTER untuk baris seterusnya | TAB untuk indentasi
</div>
</div>

<!-- Hidden Lines Input -->
<input type="hidden" name="questions[${index}][hidden_line_numbers]" class="hidden-lines-input" data-index="${index}" value="">

<!-- Preview Section (Student View) -->
<div style="margin-top:20px;">
<label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 8px;">Pandangan Pelajar</label>
<div id="code-preview-${index}" style="position: relative; background: #f5f5f5; border-radius: 8px; border: 2px solid #d1d5db; overflow: hidden; padding:0; min-height:100px; display: flex;">
<div id="preview-lines-${index}" style="flex-shrink: 0; width: 40px; background: #e8e8e8; padding: 8px 0; text-align: right; font-size: 12px; font-family: 'Courier New', monospace; color: #888; border-right: 1px solid #d1d5db; line-height: 1.5; user-select: none; padding-right: 6px;"></div>
<div id="preview-code-${index}" style="flex: 1; padding: 8px 8px; font-family:'Courier New', monospace; font-size:12px; line-height:1.5; color:inherit; white-space: pre-wrap; word-wrap: break-word; overflow-x: auto;">Masukkan kod di atas</div>
</div>
</div>
</div>`;
    };

    // Function to update line numbers and refresh checkboxes
    function updateCodeLineNumbers(textarea, index) {
        const lines = textarea.value.split('\n');
        
        const lineNumbersDiv = document.getElementById(`code-lines-${index}`);
        const checkboxesDiv = document.getElementById(`code-checkboxes-${index}`);
        
        // Auto-expand textarea based on content, starting from minimum single line
        textarea.style.height = 'auto';
        const minHeight = parseInt(window.getComputedStyle(textarea).lineHeight) * 1 + parseInt(window.getComputedStyle(textarea).paddingTop) + parseInt(window.getComputedStyle(textarea).paddingBottom);
        textarea.style.height = Math.max(textarea.scrollHeight, minHeight) + 'px';
        
        // Update line numbers
        let lineNumbers = '';
        for (let i = 1; i <= Math.max(lines.length, 1); i++) {
            lineNumbers += i + '<br>';
        }
        lineNumbersDiv.innerHTML = lineNumbers;
        
        // Update checkboxes column
        updateCheckboxesColumn(index, lines);
        
        // Update preview
        updateCodePreview(index, lines);
    }

    // Function to render checkboxes in the checkbox column
    function updateCheckboxesColumn(index, lines) {
        const checkboxesDiv = document.getElementById(`code-checkboxes-${index}`);
        const hiddenInput = document.querySelector(`.hidden-lines-input[data-index="${index}"]`);
        const currentHidden = hiddenInput.value ? hiddenInput.value.split(',').map(Number) : [];
        
        let html = '';
        lines.forEach((line, i) => {
            const lineNum = i + 1;
            const isHidden = currentHidden.includes(lineNum);
            
            html += `<div class="checkbox-wrapper" data-line="${lineNum}" style="
                display: flex;
                align-items: center;
                justify-content: center;
                height: 17px;
                margin: 1px;
                cursor: pointer;
                transition: all 0.2s ease;
            " onmouseover="this.style.background='${isHidden ? 'rgba(168, 85, 247, 0.1)' : 'rgba(0, 0, 0, 0.03)'}';" onmouseout="this.style.background='transparent';">
                <input 
                    type="checkbox" 
                    class="code-line-checkbox" 
                    data-line="${lineNum}" 
                    ${isHidden ? 'checked' : ''} 
                    style="
                        cursor: pointer;
                        width: 10px;
                        height: 10px;
                        margin: 2px;
                        accent-color: #A855F7;
                    "
                >
            </div>`;
        });
        
        checkboxesDiv.innerHTML = html;
        
        // Add event listeners to checkboxes
        checkboxesDiv.querySelectorAll('.code-line-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', () => updateHiddenLines(index));
        });
    }

    // Function to update hidden lines input and preview
    function updateHiddenLines(index) {
        const checkboxesDiv = document.getElementById(`code-checkboxes-${index}`);
        const hiddenInput = document.querySelector(`.hidden-lines-input[data-index="${index}"]`);
        const checkboxes = checkboxesDiv.querySelectorAll('.code-line-checkbox:checked');
        
        const hiddenLines = Array.from(checkboxes).map(cb => {
            return cb.getAttribute('data-line');
        });
        
        hiddenInput.value = hiddenLines.join(',');
        
        // Auto-calculate points: 1 point per hidden line
        const pointsInput = document.querySelector(`input[name="questions[${index}][points]"]`);
        if (pointsInput) {
            pointsInput.value = Math.max(1, hiddenLines.length); // Minimum 1 point
        }
        
        // Update preview
        const textarea = document.querySelector(`.code-full-textarea[data-index="${index}"]`);
        const lines = textarea.value.split('\n');
        updateCodePreview(index, lines);
    }

    // Function to update code preview (showing what students will see)
    function updateCodePreview(index, lines) {
        const hiddenInput = document.querySelector(`.hidden-lines-input[data-index="${index}"]`);
        const currentHidden = hiddenInput.value ? hiddenInput.value.split(',').map(Number) : [];
        
        const previewDiv = document.getElementById(`preview-code-${index}`);
        const previewLines = document.getElementById(`preview-lines-${index}`);
        
        if (lines.length === 0) {
            previewDiv.textContent = 'Masukkan kod di atas';
            previewLines.innerHTML = '';
            return;
        }
        
        let previewHtml = '';
        let lineNumbersHtml = '';
        let lineNum = 1;
        
        lines.forEach((line, i) => {
            if (currentHidden.includes(i + 1)) {
                previewHtml += '<div style="height: 1.5em; display: flex; align-items: center;"><span style="background-color: #ffee8c; color: #000; padding: 2px 4px; border-radius: 2px;">[___BARIS DISEMBUNYIKAN___]</span></div>';
            } else {
                previewHtml += '<div style="height: 1.5em; display: flex; align-items: center;">' + line.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</div>';
            }
            lineNumbersHtml += '<div style="height: 1.5em; display: flex; align-items: center; justify-content: flex-end;">' + lineNum + '</div>';
            lineNum++;
        });
        
        previewDiv.innerHTML = previewHtml;
        previewDiv.style.display = 'flex';
        previewDiv.style.flexDirection = 'column';
        previewLines.innerHTML = lineNumbersHtml;
        previewLines.style.display = 'flex';
        previewLines.style.flexDirection = 'column';
    }

    // Function to handle tab key for 4 spaces
    function handleTabKey(event, index) {
        const textarea = event.target;
        if (event.code === 'Tab') {
            event.preventDefault();
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            
            // Insert 4 spaces at cursor position
            textarea.value = textarea.value.substring(0, start) + '    ' + textarea.value.substring(end);
            textarea.selectionStart = textarea.selectionEnd = start + 4;
            updateCodeLineNumbers(textarea, index);
        }
    }


    // Template for Options container (used by MC, TF, and CHECKBOX)
    // Accepts an optional options array to render all options
    const optionTemplate = (qIndex, type, options = null) => {
        const rowTemplate = (type === QUESTION_TYPES.CHECKBOX) ? checkboxOptionRow : optionRow;
        let optionsHtml = '';
        if (Array.isArray(options) && options.length > 0) {
            for (let i = 0; i < options.length; i++) {
                optionsHtml += rowTemplate(qIndex, i);
            }
        } else {
            // Show 4 options by default for MC and CHECKBOX types, 2 for others
            const defaultOptionCount = (type === QUESTION_TYPES.MC || type === QUESTION_TYPES.CHECKBOX) ? 4 : 2;
            for (let i = 0; i < defaultOptionCount; i++) {
                optionsHtml += rowTemplate(qIndex, i);
            }
        }
        return `
            <h6 style="margin:0 0 12px 0; font-weight:600; font-size:13px;">Pilihan & Jawapan Betul <span style="color: var(--danger);">*</span></h6>
            <div class="options-list" data-q-index="${qIndex}" style="display:flex; flex-direction:column; gap:8px;">
                ${optionsHtml}
            </div>
            <button type="button" style="display:inline-block; padding:8px 14px; background:transparent; color:var(--accent); border:1px solid var(--accent); text-decoration:none; border-radius:6px; font-weight:600; font-size:12px; cursor:pointer; margin-top:12px;" class="add-option-btn" data-q-index="${qIndex}">
                + Tambah Pilihan
            </button>
        `;
    };
    
    // Template for a single option row (Radio button for MC/TF)
    const optionRow = (qIndex, oIndex) => `
        <div style="display:flex; gap:8px; align-items:center;" class="option-row" data-o-index="${oIndex}">
            <input class="form-check-input mt-0 correct-option-radio" type="radio" 
                   name="questions[${qIndex}][correct_answer]" 
                   value="" 
                   data-option-text-target 
                   ${oIndex === 0 ? 'checked' : ''} required style="width:18px; height:18px; cursor:pointer; flex-shrink:0;">
            <input type="text" 
                   name="questions[${qIndex}][options][]" 
                   class="option-text-input" 
                   placeholder="Teks Pilihan" 
                   required
                   oninput="updateRadioValue(this)"
                   style="flex:1; padding: 11px 14px; border-radius: 8px; border: 2px solid #d1d5db; background: transparent; color: inherit; font-size: 14px; outline: none; box-sizing: border-box; height: 42px; transition: border-color 0.2s ease, background 0.2s ease;" onmouseover="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';" onmouseout="this.style.borderColor='#d1d5db'; this.style.background='transparent';" onfocus="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';" onblur="this.style.borderColor='#d1d5db'; this.style.background='transparent';">
            <button type="button" style="background:transparent; color:var(--danger); border:1px solid var(--danger); border-radius:6px; font-size:14px; font-weight:600; cursor:pointer; width:42px; height:42px; padding:0; display:flex; align-items:center; justify-content:center; flex-shrink:0;" class="remove-option-btn" data-o-index="${oIndex}">✕</button>
        </div>
    `;

    // Template for a single option row (Checkbox)
    const checkboxOptionRow = (qIndex, oIndex) => `
        <div style="display:flex; gap:8px; align-items:center;" class="option-row" data-o-index="${oIndex}">
            <input class="form-check-input mt-0 correct-option-checkbox" type="checkbox" 
                   name="questions[${qIndex}][correct_answers][]" 
                   value="" 
                   data-option-text-target
                   style="width:18px; height:18px; cursor:pointer; flex-shrink:0;">
            <input type="text" 
                   name="questions[${qIndex}][options][]" 
                   class="option-text-input" 
                   placeholder="Teks Pilihan" 
                   required
                   oninput="updateCheckboxValue(this)"
                   style="flex:1; padding: 11px 14px; border-radius: 8px; border: 2px solid #d1d5db; background: transparent; color: inherit; font-size: 14px; outline: none; box-sizing: border-box; height: 42px; transition: border-color 0.2s ease, background 0.2s ease;" onmouseover="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';" onmouseout="this.style.borderColor='#d1d5db'; this.style.background='transparent';" onfocus="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';" onblur="this.style.borderColor='#d1d5db'; this.style.background='transparent';">
            <button type="button" style="background:transparent; color:var(--danger); border:1px solid var(--danger); border-radius:6px; font-size:14px; font-weight:600; cursor:pointer; width:42px; height:42px; padding:0; display:flex; align-items:center; justify-content:center; flex-shrink:0;" class="remove-option-btn" data-o-index="${oIndex}">✕</button>
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

    // Renders the correct answer fields based on the selected type
    const renderAnswerFields = (qIndex, type, options = null, correctAnswer = '') => {
        const container = document.getElementById(`answers-container-${qIndex}`);
        container.innerHTML = '';
        const rowTemplate = (type === QUESTION_TYPES.CHECKBOX) ? checkboxOptionRow : optionRow;
        if (type === QUESTION_TYPES.SA) {
            container.innerHTML = shortAnswerTemplate(qIndex, correctAnswer);
        } else if (type === QUESTION_TYPES.CODING) {
            container.innerHTML = codingTemplate(qIndex);
        } else if (type === QUESTION_TYPES.MC || type === QUESTION_TYPES.TF || type === QUESTION_TYPES.CHECKBOX) {
            container.innerHTML = optionTemplate(qIndex, type, options);
            // Note: Do NOT bind onclick here as it's handled by event delegation below
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
                container.querySelector('.add-option-btn').style.display = 'none';
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

    // Validate quiz title and all questions before allowing a new one to be added
    const validateAllQuestions = () => {
        const container = document.getElementById('questions-container');
        const questionCards = container.querySelectorAll('[question-card]');
        
        // First, check if quiz title is filled
        const titleInput = document.getElementById('title');
        const titleValue = titleInput ? titleInput.value.trim() : '';
        
        if (!titleValue) {
            alert('Sila isi medan "Tajuk Kuiz" terlebih dahulu sebelum menambah soalan baru.');
            return false;
        }
        
        if (questionCards.length === 0) return true; // No questions yet, allow adding
        
        // Validate ALL questions (not just the last one)
        for (let i = 0; i < questionCards.length; i++) {
            const card = questionCards[i];
            const questionNum = i + 1;
            
            // Check if question text is not empty
            const textArea = card.querySelector('textarea[name*="question_text"]');
            const questionText = textArea ? textArea.value.trim() : '';
            
            if (!questionText) {
                alert(`Teks soalan untuk Soalan ${questionNum} tidak boleh kosong.`);
                return false;
            }
            
            const typeSelect = card.querySelector('.question-type-select');
            const type = typeSelect ? typeSelect.value : QUESTION_TYPES.MC;
            
            // Validation for Checkbox questions
            if (type === QUESTION_TYPES.CHECKBOX) {
                const checkboxes = card.querySelectorAll('input[type="checkbox"][name*="correct_answers"]');
                const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
                
                if (checkedCount < 2) {
                    alert(`Soalan ${questionNum} (Kotak Semak): Sekurang-kurangnya 2 jawapan yang betul mesti dipilih.`);
                    return false;
                }
            }
            
            // Validation for Coding questions
            else if (type === QUESTION_TYPES.CODING) {
                const hiddenLinesInput = card.querySelector('.hidden-lines-input');
                const hiddenLines = hiddenLinesInput ? hiddenLinesInput.value.trim() : '';
                
                if (!hiddenLines) {
                    alert(`Soalan ${questionNum} (Koding): Sekurang-kurangnya 1 baris mesti dipilih sebagai baris tersembunyi.`);
                    return false;
                }
            }
            
            // Validation for Short Answer questions
            else if (type === QUESTION_TYPES.SA) {
                const correctAnswerInput = card.querySelector('input[name*="correct_answer"]');
                const correctAnswer = correctAnswerInput ? correctAnswerInput.value.trim() : '';
                
                if (!correctAnswer) {
                    alert(`Soalan ${questionNum} (Jawapan Pendek): Medan jawapan yang betul tidak boleh kosong.`);
                    return false;
                }
            }
            
            // Validation for MC and TF - check that options have text
            else if (type === QUESTION_TYPES.MC || type === QUESTION_TYPES.TF) {
                const optionInputs = card.querySelectorAll('input[name*="options"][]');
                let hasEmptyOption = false;
                
                optionInputs.forEach(input => {
                    if (!input.value.trim()) {
                        hasEmptyOption = true;
                    }
                });
                
                if (hasEmptyOption) {
                    alert(`Soalan ${questionNum}: Semua pilihan mesti mempunyai teks. Sila isi semua medan pilihan.`);
                    return false;
                }
            }
        }
        
        return true;
    };

    // Validate the previous question before allowing a new one to be added
    const validatePreviousQuestion = () => {
        const container = document.getElementById('questions-container');
        const questionCards = container.querySelectorAll('[question-card]');
        
        if (questionCards.length === 0) return true; // No questions yet, allow adding
        
        // Get the last (previous) question
        const lastCard = questionCards[questionCards.length - 1];
        const questionNum = questionCards.length;
        
        // First, check if question text is not empty
        const textArea = lastCard.querySelector('textarea[name*="question_text"]');
        const questionText = textArea ? textArea.value.trim() : '';
        
        if (!questionText) {
            alert(`Teks soalan untuk Soalan ${questionNum} tidak boleh kosong.`);
            return false;
        }
        
        const typeSelect = lastCard.querySelector('.question-type-select');
        const type = typeSelect ? typeSelect.value : QUESTION_TYPES.MC;
        
        // Validation for Checkbox questions
        if (type === QUESTION_TYPES.CHECKBOX) {
            const checkboxes = lastCard.querySelectorAll('input[type="checkbox"][name*="correct_answers"]');
            const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
            
            if (checkedCount < 2) {
                alert(`Sekurang-kurangnya 2 jawapan yang betul mesti dipilih bagi soalan jenis Kotak Semak.`);
                return false;
            }
        }
        
        // Validation for Coding questions
        else if (type === QUESTION_TYPES.CODING) {
            const hiddenLinesInput = lastCard.querySelector('.hidden-lines-input');
            const hiddenLines = hiddenLinesInput ? hiddenLinesInput.value.trim() : '';
            
            if (!hiddenLines) {
                alert(`Sekurang-kurangnya 1 baris mesti dipilih sebagai baris tersembunyi bagi soalan jenis Koding.`);
                return false;
            }
        }
        
        // Validation for Short Answer questions
        else if (type === QUESTION_TYPES.SA) {
            const correctAnswerInput = lastCard.querySelector('input[name*="correct_answer"]');
            const correctAnswer = correctAnswerInput ? correctAnswerInput.value.trim() : '';
            
            if (!correctAnswer) {
                alert(`Medan jawapan yang betul tidak boleh kosong bagi soalan jenis Jawapan Pendek.`);
                return false;
            }
        }
        
        return true;
    };

    // Function to update question numbers after moving
    function updateQuestionNumbers() {
        const container = document.getElementById('questions-container');
        const questionCards = container.querySelectorAll('[question-card]');
        
        questionCards.forEach((card, index) => {
            const oldIndex = parseInt(card.getAttribute('data-index'));
            const newIndex = index;
            
            // CRITICAL: Preserve all form field values BEFORE innerHTML replacement
            const fieldValues = {};
            card.querySelectorAll('input, textarea, select').forEach(field => {
                if (field.type === 'checkbox' || field.type === 'radio') {
                    fieldValues[field.name] = {
                        value: field.value,
                        checked: field.checked,
                        type: field.type
                    };
                } else {
                    fieldValues[field.name] = field.value;
                }
            });
            
            // Update question number display
            const titleElement = card.querySelector('h3');
            if (titleElement) {
                titleElement.textContent = `Soalan ${newIndex + 1}`;
            }
            
            // Update all field names from questions[oldIndex] to questions[newIndex]
            card.setAttribute('data-index', newIndex);
            card.innerHTML = card.innerHTML
                .replace(new RegExp(`questions\\[${oldIndex}\\]`, 'g'), `questions[${newIndex}]`)
                .replace(new RegExp(`code-checkboxes-${oldIndex}`, 'g'), `code-checkboxes-${newIndex}`)
                .replace(new RegExp(`code-lines-${oldIndex}`, 'g'), `code-lines-${newIndex}`)
                .replace(new RegExp(`preview-lines-${oldIndex}`, 'g'), `preview-lines-${newIndex}`)
                .replace(new RegExp(`preview-code-${oldIndex}`, 'g'), `preview-code-${newIndex}`)
                .replace(new RegExp(`code-preview-${oldIndex}`, 'g'), `code-preview-${newIndex}`)
                .replace(new RegExp(`answers-container-${oldIndex}`, 'g'), `answers-container-${newIndex}`)
                .replace(new RegExp(`data-index="${oldIndex}"`, 'g'), `data-index="${newIndex}"`);
            
            // CRITICAL: Restore all form field values and reattach listeners
            const newFieldNames = {};
            card.querySelectorAll('input, textarea, select').forEach(field => {
                // Build map of new field names to elements
                newFieldNames[field.name] = field;
            });
            
            // Restore values using old names mapped to new elements
            for (let oldName in fieldValues) {
                const newName = oldName.replace(new RegExp(`questions\\[${oldIndex}\\]`), `questions[${newIndex}]`);
                if (newFieldNames[newName]) {
                    const field = newFieldNames[newName];
                    if (field.type === 'checkbox' || field.type === 'radio') {
                        field.checked = fieldValues[oldName].checked;
                        field.value = fieldValues[oldName].value;
                    } else {
                        field.value = fieldValues[oldName];
                    }
                }
            }
            
            // Re-attach event listeners for type change select
            const typeSelect = card.querySelector(`.question-type-select[data-index="${newIndex}"]`);
            if (typeSelect) {
                // Remove all existing listeners by cloning and replacing
                const newTypeSelect = typeSelect.cloneNode(true);
                typeSelect.parentNode.replaceChild(newTypeSelect, typeSelect);
                
                // Attach new listener
                newTypeSelect.addEventListener('change', function() {
                    const qIndex = this.getAttribute('data-index');
                    const type = this.value;
                    renderAnswerFields(qIndex, type);
                    
                    // Handle points field based on question type
                    const pointsInput = container.querySelector(`input[name="questions[${qIndex}][points]"]`);
                    if (pointsInput) {
                        if (type === QUESTION_TYPES.CODING) {
                            pointsInput.readOnly = true;
                            pointsInput.style.opacity = '0.6';
                            pointsInput.style.cursor = 'not-allowed';
                        } else {
                            pointsInput.readOnly = false;
                            pointsInput.style.opacity = '1';
                            pointsInput.style.cursor = 'auto';
                        }
                    }
                });
            }
            
            // Re-attach event listeners for code textarea if it's a coding question
            const fullCodeTextarea = card.querySelector(`.code-full-textarea[data-index="${newIndex}"]`);
            if (fullCodeTextarea) {
                const newCodeTextarea = fullCodeTextarea.cloneNode(true);
                fullCodeTextarea.parentNode.replaceChild(newCodeTextarea, fullCodeTextarea);
                
                newCodeTextarea.addEventListener('input', function() {
                    updateCodeLineNumbers(this, newIndex);
                });
                newCodeTextarea.addEventListener('keydown', function(e) {
                    handleTabKey(e, newIndex);
                });
            }
            
            // Update button data attributes
            const removeBtn = card.querySelector('.remove-question-btn');
            const moveUpBtn = card.querySelector('.move-up-btn');
            const moveDownBtn = card.querySelector('.move-down-btn');
            if (removeBtn) removeBtn.setAttribute('data-index', newIndex);
            if (moveUpBtn) moveUpBtn.setAttribute('data-index', newIndex);
            if (moveDownBtn) moveDownBtn.setAttribute('data-index', newIndex);
        });
    }

    // --- EVENT LISTENERS & INITIALIZATION ---
    
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('questions-container');
        const addQuestionBtn = document.getElementById('add-question-btn');
        
        // 1. Initial Load: Populate existing questions and set starting index
        if (existingQuestions.length > 0) {
            existingQuestions.forEach((qData, i) => {
                container.insertAdjacentHTML('beforeend', questionTemplate(i));
                // For MC, Checkbox, pass options array to render all rows
                let options = null;
                if ((qData.type === QUESTION_TYPES.MC || qData.type === QUESTION_TYPES.CHECKBOX) && Array.isArray(qData.options)) {
                    options = qData.options;
                }
                // For Short Answer, pass the correct_answer
                let correctAnswer = '';
                if (qData.type === QUESTION_TYPES.SA) {
                    correctAnswer = qData.correct_answer || '';
                }
                renderAnswerFields(i, qData.type, options, correctAnswer);
                // Populate existing data into the form inputs
                const card = container.querySelector(`[question-card][data-index="${i}"]`);
                if (!card) {
                    console.error(`Could not find card with data-index="${i}"`);
                    return;
                }
                // Set question text
                const textArea = card.querySelector('textarea[name*="question_text"]');
                if (textArea) textArea.value = qData.question_text || '';
                // Set points
                const pointsInput = card.querySelector('input[name*="points"]');
                if (pointsInput) pointsInput.value = qData.points || 1;
                // Set type select
                const typeSelect = card.querySelector('select[name*="type"]');
                if (typeSelect) typeSelect.value = qData.type || QUESTION_TYPES.MC;
                // Populate options/answers
                if (qData.type === QUESTION_TYPES.SA) {
                    // Short answer is now populated via template with correctAnswer parameter
                } else if (qData.type === QUESTION_TYPES.CODING) {
                    // Handle Coding question
                    const codeTextarea = card.querySelector('textarea[name*="coding_full_code"]');
                    if (codeTextarea) {
                        // Set hidden line numbers FIRST before updating code display
                        const hiddenLinesInput = card.querySelector('.hidden-lines-input');
                        if (hiddenLinesInput && qData.hidden_line_numbers) {
                            hiddenLinesInput.value = qData.hidden_line_numbers;
                        }
                        // Now set code and update display (which will use the hidden lines)
                        codeTextarea.value = qData.coding_full_code || '';
                        updateCodeLineNumbers(codeTextarea, i);
                        // Add event listeners for coding textarea
                        codeTextarea.addEventListener('input', function() {
                            updateCodeLineNumbers(this, i);
                        });
                        codeTextarea.addEventListener('keydown', function(e) {
                            handleTabKey(e, i);
                        });
                    }
                } else if (qData.type === QUESTION_TYPES.TF) {
                    // Handle True/False specific logic
                    const radios = card.querySelectorAll('input[type="radio"]');
                    qData.options?.forEach((option, oIdx) => {
                        if (option.option_text === 'Benar' && option.is_correct) {
                            radios[0].checked = true;
                        } else if (option.option_text === 'Salah' && option.is_correct) {
                            radios[1].checked = true;
                        }
                    });
                } else if (qData.type === QUESTION_TYPES.MC || qData.type === QUESTION_TYPES.CHECKBOX) {
                    // Handle MC/Checkbox
                    const optionInputs = card.querySelectorAll('input[name*="options"]');
                    let optIndex = 0;
                    qData.options?.forEach((option, oIdx) => {
                        if (optIndex < optionInputs.length) {
                            optionInputs[optIndex].value = option.option_text || '';
                            optIndex++;
                        }
                    });
                    // Set correct answers
                    if (qData.type === QUESTION_TYPES.CHECKBOX) {
                        const checkboxes = card.querySelectorAll('input[type="checkbox"][name*="correct_answer"]');
                        checkboxes.forEach((cb, cbIdx) => {
                            cb.value = qData.options?.[cbIdx]?.option_text || '';
                            cb.checked = qData.options?.[cbIdx]?.is_correct || false;
                        });
                    } else {
                        const radios = card.querySelectorAll('input[type="radio"][name*="correct_answer"]');
                        radios.forEach((radio, rIdx) => {
                            radio.value = qData.options?.[rIdx]?.option_text || '';
                            radio.checked = qData.options?.[rIdx]?.is_correct || false;
                        });
                    }
                }
            });
            questionIndex = existingQuestions.length;
        } else {
            // If no existing questions, create one empty one
            container.insertAdjacentHTML('beforeend', questionTemplate(questionIndex));
            renderAnswerFields(questionIndex, QUESTION_TYPES.MC); 
            questionIndex++;
        }
        
        // Setup type change listeners for all existing questions
        document.querySelectorAll('.question-type-select').forEach(select => {
            select.addEventListener('change', function() {
                const qIndex = this.getAttribute('data-index');
                const type = this.value;
                renderAnswerFields(qIndex, type);
            });
        });

        // 2. Add Question Button
        addQuestionBtn.addEventListener('click', function() {
            // Validate all questions and quiz title before adding a new one
            if (!validateAllQuestions()) {
                return;
            }
            
            container.insertAdjacentHTML('beforeend', questionTemplate(questionIndex));
            renderAnswerFields(questionIndex, QUESTION_TYPES.MC);
            
            // Setup event listener for the new question's type select
            const newTypeSelect = container.querySelector(`.question-type-select[data-index="${questionIndex}"]`);
            if (newTypeSelect) {
                newTypeSelect.addEventListener('change', function() {
                    const qIndex = this.getAttribute('data-index');
                    const type = this.value;
                    renderAnswerFields(qIndex, type);
                });
            }
            
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
                             qCard.querySelector('h3').textContent = `Soalan #${newIndex + 1}`;
                             
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

            // Move Question Up
            if (e.target.closest('.move-up-btn')) {
                const card = e.target.closest('[question-card]');
                const previousCard = card.previousElementSibling;
                if (previousCard && previousCard.hasAttribute('question-card')) {
                    card.parentNode.insertBefore(card, previousCard);
                    updateQuestionNumbers();
                }
            }

            // Move Question Down
            if (e.target.closest('.move-down-btn')) {
                const card = e.target.closest('[question-card]');
                const nextCard = card.nextElementSibling;
                if (nextCard && nextCard.hasAttribute('question-card')) {
                    card.parentNode.insertBefore(nextCard, card);
                    updateQuestionNumbers();
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

        // 5. Delegation for Change Events (Only handles dynamically added questions now)
        container.addEventListener('change', function(e) {
            if (e.target.classList.contains('question-type-select')) {
                const qIndex = e.target.getAttribute('data-index');
                const type = e.target.value;
                renderAnswerFields(qIndex, type);
            }
        });
        
        // Validate that quiz title is filled before allowing question text input
        container.addEventListener('focus', function(e) {
            if (e.target.tagName === 'TEXTAREA' && e.target.name.includes('question_text')) {
                const titleInput = document.getElementById('title');
                const titleValue = titleInput ? titleInput.value.trim() : '';
                
                if (!titleValue) {
                    alert('Sila isi medan "Tajuk Kuiz" terlebih dahulu sebelum menulis teks soalan.');
                    e.target.blur();
                }
            }
        }, true);
        
    });

    // Validation function to sync all radio/checkbox values with their option text before submission
    function validateBeforeSubmit() {
        const container = document.getElementById('questions-container');
        
        // STEP 1: Sync all radio buttons and checkboxes with their option text values
        container.querySelectorAll('[question-card]').forEach(card => {
            const qIndex = card.getAttribute('data-index');
            const typeSelect = card.querySelector('.question-type-select');
            const type = typeSelect ? typeSelect.value : QUESTION_TYPES.MC;
            
            // For multiple choice and true/false (radio buttons)
            if (type === QUESTION_TYPES.MC || type === QUESTION_TYPES.TF) {
                const radios = card.querySelectorAll('input[type="radio"][name*="correct_answer"]');
                const optionInputs = card.querySelectorAll('input[name*="options"]');
                
                radios.forEach((radio, index) => {
                    if (optionInputs[index]) {
                        radio.value = optionInputs[index].value;
                    }
                });
            }
            // For checkbox questions
            else if (type === QUESTION_TYPES.CHECKBOX) {
                const checkboxes = card.querySelectorAll('input[type="checkbox"][name*="correct_answer"]');
                const optionInputs = card.querySelectorAll('input[name*="options"]');
                
                checkboxes.forEach((checkbox, index) => {
                    if (optionInputs[index]) {
                        checkbox.value = optionInputs[index].value;
                    }
                });
            }
        });
        
        // STEP 2: Validate checkbox questions have at least 2 correct answers
        let hasInvalidCheckboxQuestion = false;
        
        container.querySelectorAll('[question-card]').forEach(card => {
            const typeSelect = card.querySelector('.question-type-select');
            const type = typeSelect ? typeSelect.value : QUESTION_TYPES.MC;
            
            if (type === QUESTION_TYPES.CHECKBOX) {
                const checkboxes = card.querySelectorAll('input[type="checkbox"][name*="correct_answers"]');
                const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
                
                if (checkedCount < 2) {
                    hasInvalidCheckboxQuestion = true;
                    const questionNum = card.getAttribute('data-index') + 1;
                    console.warn(`Soalan ${questionNum} (Kotak Semak) mempunyai kurang daripada 2 jawapan yang betul yang dipilih`);
                }
            }
        });
        
        if (hasInvalidCheckboxQuestion) {
            alert('Untuk soalan jenis "Kotak Semak", sekurang-kurangnya 2 jawapan yang betul mesti dipilih. Sila semak jawapan yang betul untuk setiap soalan Kotak Semak.');
            return false;
        }
        
        return true;
    }
</script>
@endsection