@extends('layouts.app')

@section('content')

<div class="app">
  <main class="main">
    <div class="header">
      <div>
        <div class="title">Kemaskini Profil</div>
        <div class="sub">Kemaskini maklumat peribadi anda</div>
      </div>
        <a href="{{ route('profile.show') }}" class="btn-kembali">
            <i class="bi bi-arrow-left"></i>Kembali
        </a>
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

    <section class="panel" style="margin-bottom:20px; margin-top:10px;">
      <div class="panel-header">
        <h3>Butiran Peribadi</h3>
      </div>

      <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('PUT')

        <div class="form-group">
          <label for="name">Nama Penuh*</label>
          <input
            type="text"
            id="name"
            name="name"
            value="{{ old('name', $user->name) }}"
            readonly
            style="background-color:#f9fafb; border-color:#d1d5db; color:#6b7280; cursor:not-allowed;"
          >
          @error('name')
            <div class="error-msg">{{ $message }}</div>
          @enderror

          
        </div>

        <div class="form-group">
          <label for="email">Alamat E-mel*</label>
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

        <div class="form-group">
          <label for="phone">No. Telefon*</label>
          <input
            type="text"
            id="phone"
            name="phone"
            value="{{ old('phone', $user->phone) }}"
            required
          >
          @error('phone')
            <div class="error-msg">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-group">
          <label for="role">Peranan*</label>
          <input
            type="text"
            id="role"
            name="role"
            value="{{ old('role', $user->role) }}"
            readonly
            style="background-color:#f9fafb; border-color:#d1d5db; color:#6b7280; cursor:not-allowed;"
          >
          @error('role')
            <div class="error-msg">{{ $message }}</div>
          @enderror
        </div>

        <hr style="margin:24px 0; border:none; border-top:1px solid #e5e7eb;">

        <div class="panel-header">
          <h3>Butiran Organisasi</h3>
        </div>

        <div class="form-group">
          <label for="school_name">Nama Sekolah</label>
          <input
            type="text"
            id="school_name"
            name="school_name"
            value="{{ old('school_name', $user->school_name) }}"
            placeholder="Contoh: SMK Pengerang Utama"
          >
          @error('school_name')
            <div class="error-msg">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-group">
          <label for="school_code">Kod Sekolah</label>
          <input
            type="text"
            id="school_code"
            name="school_code"
            value="{{ old('school_code', $user->school_code) }}"
            placeholder="Contoh: JEA3060"
          >
          @error('school_code')
            <div class="error-msg">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-group">
          <label for="district">Daerah</label>
          <input
            type="text"
            id="district"
            name="district"
            value="{{ old('district', $user->district) }}"
            placeholder="Contoh: Johor"
          >
          @error('district')
            <div class="error-msg">{{ $message }}</div>
          @enderror
        </div>

        <div class="panel-footer" style="display:flex;justify-content:center;">
          <button type="submit" class="btn btn-primary" style="padding:14px 26px">
            <i class="bi bi-check-lg"></i>
            Kemaskini
          </button>
        </div>
      </form>
    </section>
  </main>
</div>
@endsection

