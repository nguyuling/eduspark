@extends('layouts.app')

@section('content')

<div class="app">
  <main class="main">
    <div style="display:flex;justify-content:space-between;align-items:flex-start; margin-bottom:24px; margin-left:40px; margin-right:40px; margin-top:40px;">
      <div class="page-header">
        <h1>Kemaskini Profil</h1>
        <div class="subtitle">Kemaskini maklumat peribadi anda</div>
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success" style="margin-left:40px; margin-right:40px;">
        <span style="font-size: 18px;">✓</span>
        <div>
          <div class="alert-title">Success</div>
          {{ session('success') }}
        </div>
      </div>
    @endif

    @if($errors->any())
      <div class="alert alert-danger" style="margin-left:40px; margin-right:40px;">
        <span style="font-size: 18px;">⚠️</span>
        <div>
          <div class="alert-title">Error</div>
          Please correct the following errors before saving.
        </div>
      </div>
    @endif

    <div class="panel panel-spaced">
      <div class="panel-header">
        <h3>Butiran Peribadi</h3>
      </div>

      <form method="POST" action="{{ route('profile.update') }}">
        @csrf

        <div class="form-group">
          <label for="name">Nama Penuh</label>
          <input
            type="text"
            id="name"
            name="name"
            value="{{ old('name', $user->name) }}"
            required
          >
          @error('name')
            <div class="error-msg">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-group">
          <label for="email">Alamat E-mel</label>
          <input
            type="email"
            id="email"
            name="email"
            value="{{ old('email', $user->email) }}"
            required
          >
          <div class="form-text">E-mel anda digunakan untuk pengesahan akaun.</div>
          @error('email')
            <div class="error-msg">{{ $message }}</div>
          @enderror
        </div>

        <div class="panel-footer">
          <a href="{{ route('profile.show') }}" class="btn btn-secondary">
            ← Kembali ke Profil
          </a>
          <button type="submit" class="btn btn-primary">
            Simpan Perubahan
          </button>
        </div>
      </form>
    </div>

    <div class="panel panel-spaced">
      <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
          <h3 style="margin: 0 0 4px 0; font-weight: 700;">Keselamatan Kata Laluan</h3>
          <p style="margin: 0; color: var(--muted); font-size: 13px;">Kemaskini kata laluan anda untuk menjaga keamanan akaun.</p>
        </div>
        <a href="{{ route('profile.password.edit') }}" class="btn btn-primary">
          Tukar Kata Laluan
        </a>
      </div>
    </div>
  </main>
</div>
@endsection