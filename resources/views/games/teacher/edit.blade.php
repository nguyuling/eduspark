@extends('layouts.app')

@section('content')
<div class="app">
  <main class="main">
    <div class="header">
      <div>
        <div class="title">Kemaskini Permainan: {{ $game->title }}</div>
        <div class="sub">Kemaskini butiran dan tetapan permainan</div>
      </div>
      <a href="{{ route('games.index') }}" class="btn-kembali">
        <i class="bi bi-arrow-left"></i>Kembali
      </a>
    </div>

    @if(session('success'))
      <div class="alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
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
    <form method="POST" action="{{ route('teacher.games.update', $game->id) }}" id="game-form">
      @csrf
      @method('PUT')

      <!-- Game Format Section -->
      <section class="panel" style="margin-bottom:20px; margin-top:10px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; border-bottom:2px solid #d4c5f9; padding-bottom:12px;">
          <h2 style="margin:0; font-size:18px; font-weight:700;">Maklumat Asas</h2>
        </div>

        <!-- Title -->
        <div style="margin-bottom: 20px;">
          <label for="title" style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 6px;">Tajuk Permainan <span style="color: var(--danger);">*</span></label>
          <input 
            type="text" 
            id="title" 
            name="title" 
            placeholder="Masukkan tajuk permainan"
            value="{{ old('title', $game->title) }}" 
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
            placeholder="Huraikan permainan anda..."
            style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 2px solid #d1d5db; background: transparent; color: inherit; font-size: 14px; outline: none; transition: border-color 0.2s ease, background 0.2s ease; resize: vertical; box-sizing: border-box;"
            onmouseover="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';"
            onmouseout="this.style.borderColor='#d1d5db'; this.style.background='transparent';"
            onfocus="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';"
            onblur="this.style.borderColor='#d1d5db'; this.style.background='transparent';"
          >{{ old('description', $game->description) }}</textarea>
          @error('description')<span style="color: var(--danger); font-size: 12px;">{{ $message }}</span>@enderror
        </div>

        <!-- Publish Checkbox -->
        <div style="display: flex; align-items: flex-end; padding-bottom: 2px;">
          <div style="display: flex; align-items: center; gap: 12px; padding: 11px 14px; background: rgba(106,77,247,0.05); border-radius: 8px; border: 2px solid #d1d5db; width: 100%;">
            <input 
              type="checkbox" 
              id="is_published" 
              name="is_published"
              value="1"
              {{ old('is_published', $game->is_published) ? 'checked' : '' }}
              style="width: 18px; height: 18px; cursor: pointer; flex-shrink: 0;"
            >
            <label for="is_published" style="margin: 0; cursor: pointer; font-weight: 500; font-size: 14px; white-space: nowrap;">Terbitkan Segera</label>
          </div>
        </div>
      </section>

      <!-- Classification Section -->
      <section class="panel" style="margin-bottom:20px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; border-bottom:2px solid #d4c5f9; padding-bottom:12px;">
          <h2 style="margin:0; font-size:18px; font-weight:700;">Pengelasan</h2>
        </div>

        <!-- Bottom Row: Category, Difficulty -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
          <!-- Category -->
          <div>
            <label for="category" style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 6px;">Kategori <span style="color: var(--danger);">*</span></label>
            <select 
              id="category" 
              name="category" 
              required
              style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 2px solid #d1d5db; background: transparent; color: inherit; font-size: 14px; outline: none; box-sizing: border-box; height: 44px; transition: border-color 0.2s ease, background 0.2s ease; cursor: pointer;"
              onmouseover="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';"
              onmouseout="this.style.borderColor='#d1d5db'; this.style.background='transparent';"
              onfocus="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';"
              onblur="this.style.borderColor='#d1d5db'; this.style.background='transparent';"
            >
              <option value="">-- Pilih Kategori --</option>
              <option value="Action" {{ old('category', $game->category) === 'Action' ? 'selected' : '' }}>Aksi</option>
              <option value="Casual" {{ old('category', $game->category) === 'Casual' ? 'selected' : '' }}>Santai</option>
              <option value="Puzzled" {{ old('category', $game->category) === 'Puzzled' ? 'selected' : '' }}>Teka-teki</option>
              <option value="Education" {{ old('category', $game->category) === 'Education' ? 'selected' : '' }}>Pendidikan</option>
              <option value="Others" {{ old('category', $game->category) === 'Others' ? 'selected' : '' }}>Lain-lain</option>
            </select>
            @error('category')<span style="color: var(--danger); font-size: 12px;">{{ $message }}</span>@enderror
          </div>

          <!-- Difficulty -->
          <div>
            <label for="difficulty" style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 6px;">Tahap Kesukaran <span style="color: var(--danger);">*</span></label>
            <select 
              id="difficulty" 
              name="difficulty" 
              required
              style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 2px solid #d1d5db; background: transparent; color: inherit; font-size: 14px; outline: none; box-sizing: border-box; height: 44px; transition: border-color 0.2s ease, background 0.2s ease; cursor: pointer;"
              onmouseover="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';"
              onmouseout="this.style.borderColor='#d1d5db'; this.style.background='transparent';"
              onfocus="this.style.borderColor='#9ca3af'; this.style.background='rgba(200, 200, 200, 0.08)';"
              onblur="this.style.borderColor='#d1d5db'; this.style.background='transparent';"
            >
              <option value="">-- Pilih Tahap Kesukaran --</option>
              <option value="easy" {{ old('difficulty', $game->difficulty) === 'easy' ? 'selected' : '' }}>Mudah</option>
              <option value="medium" {{ old('difficulty', $game->difficulty) === 'medium' ? 'selected' : '' }}>Sederhana</option>
              <option value="hard" {{ old('difficulty', $game->difficulty) === 'hard' ? 'selected' : '' }}>Sukar</option>
            </select>
            @error('difficulty')<span style="color: var(--danger); font-size: 12px;">{{ $message }}</span>@enderror
          </div>
        </div>
      </section>

      <!-- Action Buttons Row -->
      <div style="display:flex; gap:12px; justify-content:center; margin-top:40px; margin-bottom:40px; padding:0;">
        <button type="submit" class="btn-submit" style="display:inline-flex !important; align-items:center !important; gap:8px !important; padding:14px 26px !important; background:linear-gradient(90deg, #A855F7, #9333EA) !important; color:#fff !important; border:none !important; text-decoration:none !important; border-radius:8px !important; font-weight:600 !important; font-size:13px !important; cursor:pointer !important; transition:all 0.2s ease !important; box-shadow:0 2px 8px rgba(168, 85, 247, 0.3) !important;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(168, 85, 247, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(168, 85, 247, 0.3)'">
          <i class="bi bi-save"></i>Kemaskini Permainan
        </button>
      </div>
    </form>


  </main>
</div>
@endsection
