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
      <div class="alert alert-success">
        <span style="font-size: 18px;">✓</span>
        <div>
          <div class="alert-title">Berjaya</div>
          {{ session('success') }}
        </div>
      </div>
    @endif

    @if($errors->any())
      <div class="alert alert-danger">
        <span style="font-size: 18px;">⚠️</span>
        <div>
          <div class="alert-title">Ralat</div>
          Sila betulkan ralat berikut sebelum menyimpan.
        </div>
      </div>
    @endif

    <section class="panel">
      <div class="panel-header">
        <h3>Maklumat Asas</h3>
      </div>

      <form method="POST" action="{{ route('teacher.games.update', $game->id) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
          <label for="title">Tajuk Permainan *</label>
          <input
            type="text"
            id="title"
            name="title"
            value="{{ old('title', $game->title) }}"
            required
            placeholder="Masukkan tajuk permainan"
          >
          @error('title')
            <div class="error-msg">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-group">
          <label for="description">Penerangan</label>
          <textarea
            id="description"
            name="description"
            class="form-input"
            placeholder="Huraikan permainan anda..."
          >{{ old('description', $game->description) }}</textarea>
          @error('description')
            <div class="error-msg">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-group">
          <label style="display: flex; align-items: center;">
            <input 
              type="checkbox" 
              id="is_published" 
              name="is_published" 
              value="1" 
              {{ old('is_published', $game->is_published) ? 'checked' : '' }}
            >
            <span style="margin-left: 8px; color: inherit;">Terbitkan Segera</span>
          </label>
        </div>
      </form>
    </section>

    <section class="panel">
      <div class="panel-header">
        <h3>Pengelasan</h3>
      </div>

      <form method="POST" action="{{ route('teacher.games.update', $game->id) }}">
        @csrf
        @method('PUT')

        <div class="info-grid">
          <div class="form-group">
            <label for="category">Kategori *</label>
            <input
              type="text"
              id="category"
              name="category"
              value="{{ old('category', $game->category) }}"
              required
              placeholder="Cth: Aksi, Teka-teki"
            >
            @error('category')
              <div class="error-msg">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-group">
            <label for="difficulty">Tahap Kesukaran *</label>
            <select id="difficulty" name="difficulty" required>
              <option value="">-- Pilih Tahap Kesukaran --</option>
              <option value="easy" {{ old('difficulty', $game->difficulty) === 'easy' ? 'selected' : '' }}>Mudah</option>
              <option value="medium" {{ old('difficulty', $game->difficulty) === 'medium' ? 'selected' : '' }}>Sederhana</option>
              <option value="hard" {{ old('difficulty', $game->difficulty) === 'hard' ? 'selected' : '' }}>Sukar</option>
            </select>
            @error('difficulty')
              <div class="error-msg">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-group">
            <label for="game_type">Jenis Permainan</label>
            <input
              type="text"
              id="game_type"
              name="game_type"
              value="{{ old('game_type', $game->game_type) }}"
              placeholder="Cth: Arkad, Pengembaraan"
            >
            @error('game_type')
              <div class="error-msg">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-group">
            <label for="topic">Topik</label>
            <input
              type="text"
              id="topic"
              name="topic"
              value="{{ old('topic', $game->topic) }}"
              placeholder="Cth: Pengaturcaraan, Matematik"
            >
            @error('topic')
              <div class="error-msg">{{ $message }}</div>
            @enderror
          </div>
        </div>
      </form>
    </section>


  </main>
</div>
@endsection
