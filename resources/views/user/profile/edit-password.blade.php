@extends('layouts.app')

@section('content')

<div class="app">
  <main class="main">
    <div style="display:flex;justify-content:space-between;align-items:flex-start; margin-bottom:24px; margin-top:40px; margin-left:40px; margin-right:40px;">
      <div class="page-header">
        <h1>Tukar Kata Laluan</h1>
        <div class="subtitle">Kemaskini kata laluan anda untuk menjaga keamanan akaun</div>
      </div>
        <a href="{{ route('profile.show') }}" class="btn-kembali" style="display:inline-block !important; padding:12px 24px !important; background:transparent !important; color:#6A4DF7 !important; border:2px solid #6A4DF7 !important; text-decoration:none !important; border-radius:8px !important; font-weight:700 !important; font-size:14px !important; margin-top:15px !important; transition:all 0.2s ease !important; cursor:pointer !important; line-height:1 !important; height:auto !important;" onmouseover="this.style.background='rgba(106,77,247,0.1)'" onmouseout="this.style.background='transparent'">
            <i class="bi bi-arrow-left" style="margin-right:6px;"></i>Kembali
        </a>
    </div>

    @if(session('success'))
      <div class="alert alert-success" style="margin-left:40px; margin-right:40px;">
        <span style="font-size: 18px;">✓</span>
        <div>
          <div class="alert-title">Berjaya</div>
          {{ session('success') }}
        </div>
      </div>
    @endif

    @if($errors->any())
      <div class="alert alert-danger" style="margin-left:40px; margin-right:40px;">
        <span style="font-size: 18px;">⚠️</span>
        <div>
          <div class="alert-title">Ralat</div>
          Terdapat masalah mengemaskini kata laluan anda. Sila semak medan di bawah.
        </div>
      </div>
    @endif

    <div class="panel panel-spaced">
      <div class="panel-header">
        <h3>Kemaskini Kata Laluan</h3>
      </div>

      <form method="POST" action="{{ route('profile.password.update') }}">
        @csrf
        @method('PUT')

        <div class="form-group">
          <label for="current_password">Kata Laluan Semasa</label>
          <input
            type="password"
            id="current_password"
            name="current_password"
            class="form-input"
            required
            autocomplete="current-password"
          >
          @error('current_password')
            <div class="error-msg">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-group">
          <label for="password">Kata Laluan Baru</label>
          <input
            type="password"
            id="password"
            name="password"
            class="form-input"
            required
            minlength="6"
            autocomplete="new-password"
          >
          <div class="form-text">Kata laluan mesti sekurang-kurangnya 6 aksara.</div>
          @error('password')
            <div class="error-msg">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-group">
          <label for="password_confirmation">Sahkan Kata Laluan Baru</label>
          <input
            type="password"
            id="password_confirmation"
            class="form-input"
            name="password_confirmation"
            required
            autocomplete="new-password"
          >
          @error('password_confirmation')
            <div class="error-msg">{{ $message }}</div>
          @enderror
        </div>

        <div class="panel-footer" style="display:flex;justify-content:center;">
          <button type="submit" class="btn btn-danger" style="display:inline-block !important; padding:12px 24px !important; background:linear-gradient(135deg, #E63946, #c92a2a) !important; color:#ffffff !important; border:none !important; text-decoration:none !important; border-radius:8px !important; font-weight:700 !important; font-size:14px !important; transition:all 0.2s ease !important; cursor:pointer !important; line-height:1 !important; height:auto !important;" onmouseover="this.style.boxShadow='0 4px 12px rgba(230,57,70,0.4)'" onmouseout="this.style.boxShadow='none'">
            Simpan Kata Laluan Baru
          </button>
        </div>
      </form>
    </div>
  </main>
</div>
@endsection