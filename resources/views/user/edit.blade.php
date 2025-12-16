@extends('layouts.app')

@section('content')

<div class="app">
  <main class="main">
    <div class="header">
      <div>
        <div class="title">Kemaskini Profil</div>
        <div class="sub">Kemaskini maklumat peribadi anda</div>
      </div>
        <a href="{{ route('profile.show') }}" class="btn-kembali" style="display:inline-block !important; padding:12px 24px !important; background:transparent !important; color:#6A4DF7 !important; border:2px solid #6A4DF7 !important; text-decoration:none !important; border-radius:8px !important; font-weight:700 !important; font-size:14px !important; margin-top:15px !important; transition:all 0.2s ease !important; cursor:pointer !important; line-height:1 !important; height:auto !important;" onmouseover="this.style.background='rgba(106,77,247,0.1)'" onmouseout="this.style.background='transparent'">
            <i class="bi bi-arrow-left" style="margin-right:6px;"></i>Kembali
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
            required
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