@extends('layouts.app')

@section('content')

<div class="app">
  <main class="main">
    <div class="header">
      <div>
        <div class="title">Edit Bahan</div>
        <div class="sub">Kemaskini maklumat bahan pembelajaran</div>
      </div>
      <a href="{{ route('lesson.index') }}" class="btn-kembali" style="display:inline-block !important; margin-top:15px; padding:12px 24px !important; background:transparent !important; color:#6A4DF7 !important; border:2px solid #6A4DF7 !important; text-decoration:none !important; border-radius:8px !important; font-weight:700 !important; font-size:14px !important; transition:all 0.2s ease !important; cursor:pointer !important; line-height:1 !important; height:auto !important;" onmouseover="this.style.background='rgba(106,77,247,0.1)'" onmouseout="this.style.background='transparent'">
        <i class="bi bi-arrow-left" style="margin-right:6px;"></i>Kembali
      </a>
    </div>

    @if (session('error'))
      <div style="background:var(--danger);color:#fff;padding:12px 14px;border-radius:var(--card-radius);margin-bottom:20px;margin-left:40px;margin-right:40px;font-size:14px;">{{ session('error') }}</div>
    @endif

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

    <form method="POST" action="{{ route('lesson.update', $lesson->id) }}" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <section class="panel" style="margin-bottom:20px; margin-top:10px;">
        <h2 style="margin:0 0 20px 0; font-size:18px; font-weight:700; border-bottom:2px solid #d4c5f9; padding-bottom:12px;">Butiran Bahan</h2>

        <div style="margin-bottom: 20px;">
          <label for="title" style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 6px;">Tajuk <span style="color: var(--danger);">*</span></label>
          <input 
            type="text" 
            id="title" 
            name="title" 
            value="{{ old('title', $lesson->title) }}" 
            required
            style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 2px solid #d1d5db; background: transparent; color: inherit; font-size: 14px; outline: none; transition: border-color 0.2s ease, background 0.2s ease; box-sizing: border-box;" 
            onmouseover="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';"
            onmouseout="this.style.borderColor='#d1d5db'; this.style.background='transparent';"
            onfocus="this.style.borderColor='#6A4DF7'; this.style.background='rgba(200, 200, 200, 0.08)';"
            onblur="this.style.borderColor='#d1d5db'; this.style.background='transparent';"
          >
        </div>

        <div style="margin-bottom: 20px;">
          <label for="description" style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 6px;">Penerangan</label>
          <textarea 
            id="description" 
            name="description" 
            style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 2px solid #d1d5db; background: transparent; color: inherit; font-size: 14px; outline: none; transition: border-color 0.2s ease, background 0.2s ease; min-height: 100px; box-sizing: border-box; resize: vertical;" 
            onmouseover="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';"
            onmouseout="this.style.borderColor='#d1d5db'; this.style.background='transparent';"
            onfocus="this.style.borderColor='#6A4DF7'; this.style.background='rgba(200, 200, 200, 0.08)';"
            onblur="this.style.borderColor='#d1d5db'; this.style.background='transparent';"
          >{{ old('description', $lesson->description) }}</textarea>
        </div>

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
              <option value="4A" {{ old('class_group', $lesson->class_group) == '4A' ? 'selected' : '' }}>4A</option>
              <option value="4B" {{ old('class_group', $lesson->class_group) == '4B' ? 'selected' : '' }}>4B</option>
              <option value="4C" {{ old('class_group', $lesson->class_group) == '4C' ? 'selected' : '' }}>4C</option>
              <option value="5A" {{ old('class_group', $lesson->class_group) == '5A' ? 'selected' : '' }}>5A</option>
              <option value="5B" {{ old('class_group', $lesson->class_group) == '5B' ? 'selected' : '' }}>5B</option>
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
              <option value="class" {{ old('visibility', $lesson->visibility) == 'class' ? 'selected' : '' }}>Kelas Sahaja</option>
              <option value="public" {{ old('visibility', $lesson->visibility) == 'public' ? 'selected' : '' }}>Awam (Semua Pelajar)</option>
            </select>
          </div>
        </div>
      </section>

      <section class="panel" style="margin-bottom:20px;">
        <h2 style="margin:0 0 20px 0; font-size:18px; font-weight:700; border-bottom:2px solid #d4c5f9; padding-bottom:12px;">Fail</h2>

        @if($lesson->file_path)
        <div style="margin-bottom: 16px; padding: 12px; background: rgba(106,77,247,0.05); border-radius: 8px; border: 1px solid rgba(106,77,247,0.2);">
          <div style="font-weight: 600; font-size: 13px; color: var(--muted); margin-bottom: 4px;">Fail Semasa:</div>
          <div style="display: flex; gap: 8px; align-items: center;">
            <span style="background: rgba(106,77,247,0.1); color: var(--accent); padding: 4px 10px; border-radius: 4px; font-weight: 600; font-size: 12px;">
              {{ strtoupper($lesson->file_ext) }}
            </span>
            <span style="font-size: 13px;">{{ $lesson->file_name }}</span>
          </div>
        </div>
        @endif

        <div style="margin-bottom: 20px;">
          <label for="file" style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 6px;">
            Ganti Fail (Pilihan)
          </label>
          <input 
            type="file" 
            id="file" 
            name="file" 
            accept=".pdf,.docx,.pptx,.txt,.jpg,.png"
            style="width: 100%; padding: 32px 24px; border-radius: 8px; border: 2px solid #d1d5db; background: transparent; color: inherit; font-size: 14px; outline: none; transition: border-color 0.2s ease, background 0.2s ease; box-sizing: border-box; cursor: pointer; min-height: 120px;" 
            onmouseover="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';"
            onmouseout="this.style.borderColor='#d1d5db'; this.style.background='transparent';"
            onfocus="this.style.borderColor='#6A4DF7'; this.style.background='rgba(200, 200, 200, 0.08)';"
            onblur="this.style.borderColor='#d1d5db'; this.style.background='transparent';"
          >
          <div style="margin-top: 6px; color: var(--muted); font-size: 13px;">Biarkan kosong jika tidak mahu menukar fail</div>
        </div>
      </section>

      <section style="margin-left:40px; margin-right:40px; margin-bottom:20px; margin-top:40px; display:flex; gap:12px; align-items:center; justify-content:center;">
        <button type="submit" style="background: linear-gradient(90deg,var(--accent),var(--accent-2)); color:#fff; padding:12px 24px; border:none; border-radius:8px; font-weight:700; font-size:14px; cursor:pointer; transition:transform .2s ease, box-shadow .2s ease; box-shadow: 0 4px 12px rgba(106,77,247,0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(106,77,247,0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(106,77,247,0.3)';">
          <i class="bi bi-check-lg" style="margin-right:6px;"></i>Simpan Perubahan
        </button>
      </section>
    </form>
  </main>
</div>

@endsection