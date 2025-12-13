@extends('layouts.app')

@section('content')

<div class="app">
  <main class="main">
    <div style="display:flex;justify-content:space-between;align-items:flex-start; margin-bottom:24px; margin-top:40px; margin-left:40px; margin-right:40px;">
      <div class="page-header">
        <h1>Tukar Kata Laluan</h1>
        <div class="subtitle">Kemaskini kata laluan anda untuk menjaga keamanan akaun</div>
      </div>
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

        <div class="panel-footer">
          <a href="{{ route('profile.show') }}" class="btn btn-secondary">
            ← Kembali ke Profil
          </a>
          <button type="submit" class="btn btn-danger">
            Simpan Kata Laluan Baru
          </button>
        </div>
      </form>
    </div>
  </main>
</div>
@endsection