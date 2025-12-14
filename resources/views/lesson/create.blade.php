@extends('layouts.app')

@section('content')

<div class="app">
  <!-- Main -->
  <main class="main">
    <div class="header">
      <div>
        <div class="title">Cipta Bahan Baru</div>
        <div class="sub">Tambah bahan pembelajaran baru untuk kelas anda</div>
      </div>
        <a href="{{ route('lesson.index') }}" class="btn-kembali" style="display:inline-block !important; padding:12px 24px !important; background:transparent !important; color:#6A4DF7 !important; border:2px solid #6A4DF7 !important; text-decoration:none !important; border-radius:8px !important; font-weight:700 !important; font-size:14px !important; margin-top:15px !important; transition:all 0.2s ease !important; cursor:pointer !important; line-height:1 !important; height:auto !important;" onmouseover="this.style.background='rgba(106,77,247,0.1)'" onmouseout="this.style.background='transparent'">
            <i class="bi bi-arrow-left" style="margin-right:6px;"></i>Kembali
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

    <!-- Main Form -->
    <form method="POST" action="{{ route('lesson.store') }}" enctype="multipart/form-data" id="lesson-form">
      @csrf

      <!-- Lesson Details Section -->
      <section class="panel" style="margin-left:40px; margin-right:40px; margin-bottom:20px; margin-top:20px;">
        <h2 style="margin:0 0 20px 0; font-size:18px; font-weight:700; border-bottom:2px solid #d4c5f9; padding-bottom:12px;">Butiran Bahan</h2>

        <!-- Title -->
        <div style="margin-bottom: 20px;">
          <label for="title" style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 6px;">Tajuk <span style="color: var(--danger);">*</span></label>
          <input 
            type="text" 
            id="title" 
            name="title" 
            placeholder="Contoh: Pengenalan kepada Algebra"
            value="{{ old('title') }}" 
            required
            style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 2px solid #d1d5db; background: transparent; color: inherit; font-size: 14px; outline: none; transition: border-color 0.2s ease, background 0.2s ease; box-sizing: border-box;" 
            onmouseover="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';"
            onmouseout="this.style.borderColor='#d1d5db'; this.style.background='transparent';"
            onfocus="this.style.borderColor='#6A4DF7'; this.style.background='rgba(200, 200, 200, 0.08)';"
            onblur="this.style.borderColor='#d1d5db'; this.style.background='transparent';"
          >
        </div>

        <!-- Description -->
        <div style="margin-bottom: 20px;">
          <label for="description" style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 6px;">Penerangan</label>
          <textarea 
            id="description" 
            name="description" 
            placeholder="Terangkan secara ringkas kandungan bahan pembelajaran ini..."
            style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 2px solid #d1d5db; background: transparent; color: inherit; font-size: 14px; outline: none; transition: border-color 0.2s ease, background 0.2s ease; min-height: 100px; box-sizing: border-box; resize: vertical;" 
            onmouseover="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';"
            onmouseout="this.style.borderColor='#d1d5db'; this.style.background='transparent';"
            onfocus="this.style.borderColor='#6A4DF7'; this.style.background='rgba(200, 200, 200, 0.08)';"
            onblur="this.style.borderColor='#d1d5db'; this.style.background='transparent';"
          >{{ old('description') }}</textarea>
        </div>

        <!-- Class Group and Visibility in same row -->
        <div style="display: flex; gap: 20px; margin-bottom: 20px;">
          <div style="flex: 1;">
            <label for="class_group" style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 6px;">Kelas <span style="color: var(--danger);">*</span></label>
            <select 
              id="class_group" 
              name="class_group" 
              required
              style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 2px solid #d1d5db; background: transparent; color: inherit; font-size: 14px; outline: none; transition: border-color 0.2s ease, background 0.2s ease; box-sizing: border-box; height: 44px;" 
              onmouseover="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';"
              onmouseout="this.style.borderColor='#d1d5db'; this.style.background='transparent';"
              onfocus="this.style.borderColor='#6A4DF7'; this.style.background='rgba(200, 200, 200, 0.08)';"
              onblur="this.style.borderColor='#d1d5db'; this.style.background='transparent';"
            >
              <option value="">-- Pilih Kelas --</option>
              <option value="4A" {{ old('class_group') == '4A' ? 'selected' : '' }}>4A</option>
              <option value="4B" {{ old('class_group') == '4B' ? 'selected' : '' }}>4B</option>
              <option value="4C" {{ old('class_group') == '4C' ? 'selected' : '' }}>4C</option>
              <option value="5A" {{ old('class_group') == '5A' ? 'selected' : '' }}>5A</option>
              <option value="5B" {{ old('class_group') == '5B' ? 'selected' : '' }}>5B</option>
            </select>
          </div>

          <div style="flex: 1;">
            <label for="visibility" style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 6px;">Keterlihatan <span style="color: var(--danger);">*</span></label>
            <select 
              id="visibility" 
              name="visibility" 
              required
              style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 2px solid #d1d5db; background: transparent; color: inherit; font-size: 14px; outline: none; transition: border-color 0.2s ease, background 0.2s ease; box-sizing: border-box; height: 44px;" 
              onmouseover="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';"
              onmouseout="this.style.borderColor='#d1d5db'; this.style.background='transparent';"
              onfocus="this.style.borderColor='#6A4DF7'; this.style.background='rgba(200, 200, 200, 0.08)';"
              onblur="this.style.borderColor='#d1d5db'; this.style.background='transparent';"
            >
              <option value="">-- Pilih Keterlihatan --</option>
              <option value="class" {{ old('visibility') == 'class' ? 'selected' : '' }}>Kelas Sahaja</option>
              <option value="public" {{ old('visibility') == 'public' ? 'selected' : '' }}>Awam (Semua Pelajar)</option>
            </select>
          </div>
        </div>
      </section>

      <!-- File Upload Section -->
      <section class="panel" style="margin-left:40px; margin-right:40px; margin-bottom:20px;">
        <h2 style="margin:0 0 20px 0; font-size:18px; font-weight:700; border-bottom:2px solid #d4c5f9; padding-bottom:12px;">Muat Naik Fail</h2>

        <!-- File Upload -->
        <div style="margin-bottom: 20px;">
          <label for="file" style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 6px;">Fail (Pilihan)</label>
          <input 
            type="file" 
            id="file" 
            name="file" 
            accept=".pdf,.docx,.pptx,.txt,.jpg,.png"
            style="width: 100%; padding: 32px 24px; border-radius: 8px; border: 2px solid #d1d5db; background: transparent; color: inherit; font-size: 14px; outline: none; transition: border-color 0.2s ease, background 0.2s ease; box-sizing: border-box; cursor: pointer; min-height: 120px; display: flex; align-items: center;" 
            onmouseover="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';"
            onmouseout="this.style.borderColor='#d1d5db'; this.style.background='transparent';"
            onfocus="this.style.borderColor='#6A4DF7'; this.style.background='rgba(200, 200, 200, 0.08)';"
            onblur="this.style.borderColor='#d1d5db'; this.style.background='transparent';"
          >
          <div style="margin-top: 6px; color: var(--muted); font-size: 13px;">Disokong: PDF, DOCX, PPTX, TXT, JPG, PNG (Saiz maksimum: 10MB)</div>
        </div>
      </section>

      <!-- Action Buttons -->
      <section style="margin-left:40px; margin-right:40px; margin-bottom:20px; margin-top:40px; display:flex; gap:12px; align-items:center; justify-content:center;">
        <button type="submit" style="background: linear-gradient(90deg,var(--accent),var(--accent-2)); color:#fff; padding:12px 24px; border:none; border-radius:8px; font-weight:700; font-size:14px; cursor:pointer; transition:transform .2s ease, box-shadow .2s ease; box-shadow: 0 4px 12px rgba(106,77,247,0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(106,77,247,0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(106,77,247,0.3)';">
          Cipta Bahan
        </button>
      </section>
    </form>
  </main>
</div>

@endsection
