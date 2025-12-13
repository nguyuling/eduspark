@extends('layouts.app')

@section('content')

<div class="app">
  <main class="main" style="flex:1;">
    <div class="header" style="display:flex;justify-content:space-between;align-items:flex-start; margin-bottom:40px; margin-top:40px; margin-left:40px; margin-right:40px;">
      <div>
        <div class="title" style="font-weight:700;font-size:28px;">Profil</div>
        <div class="sub" style="color:var(--muted);font-size:13px;">Lihat dan urus maklumat akaun anda</div>
      </div>
    </div>

    <!-- Personal Information -->
    <section class="panel panel-spaced">
      <div class="panel-header">
        <h3>Maklumat Peribadi</h3>
      </div>
      <div class="info-grid">
        <div class="info-item">
          <div class="info-label">Nama Penuh</div>
          <div class="info-value">{{ $user->name }}</div>
        </div>
        <div class="info-item">
          <div class="info-label">E-mel</div>
          <div class="info-value">{{ $user->email }}</div>
        </div>
        <div class="info-item">
          <div class="info-label">Telefon</div>
          <div class="info-value">{{ $user->phone ?: '—' }}</div>
        </div>
        <div class="info-item">
          <div class="info-label">Peranan</div>
          <div class="info-value">{{ $user->role === 'teacher' ? 'Guru' : 'Pelajar' }}</div>
        </div>
      </div>
      <div class="panel-footer">
        <div></div>
        <a href="{{ route('profile.edit') }}" class="btn btn-primary">
          Kemaskini Profil
        </a>
      </div>
    </section>

    <!-- Organization Details -->
    <section class="panel panel-spaced">
      <div class="panel-header">
        <h3>Butiran Organisasi</h3>
      </div>
      @php
        $schoolNames = ['JEA3060' => 'SMK Pengerang Utama', 'JEA3061' => 'SMK Pengerang'];
        $schoolName = $schoolNames[$user->school_code] ?? $user->school_code;
      @endphp
      <div class="info-grid">
        <div class="info-item">
          <div class="info-label">Daerah</div>
          <div class="info-value">{{ $user->district }}</div>
        </div>
        <div class="info-item">
          <div class="info-label">Sekolah</div>
          <div class="info-value">{{ $schoolName }}</div>
        </div>
        <div class="info-item">
          <div class="info-label">Ahli Sejak</div>
          <div class="info-value">{{ $user->created_at->format('j M Y') }}</div>
        </div>
        <div class="info-item">
          <div class="info-label">ID Pengguna</div>
          <div class="info-value"><span class="badge">{{ $user->user_id }}</span></div>
        </div>
      </div>
    </section>

    <!-- Account Security -->
    <section class="panel panel-spaced">
      <div class="panel-header">
        <h3>Sekuriti Akaun</h3>
      </div>
      <p style="margin: 0 0 16px 0; color: var(--muted);">Kata laluan telah ditetapkan dan diamankan.</p>
      <div style="display: flex; justify-content: flex-end;">
        <a href="{{ route('profile.password.edit') }}" class="btn btn-danger">
          Kemaskini Kata Laluan
        </a>
      </div>
    </section>

    <!-- Logout -->
    <section class="panel panel-spaced">
      <div class="panel-header">
        <h3>Keluar</h3>
      </div>
      <p style="margin: 0 0 16px 0; color: var(--muted);">Keluar dari akaun anda dengan selamat.</p>
      <div style="display: flex; justify-content: flex-end;">
        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
          @csrf
          <button type="submit" class="btn btn-danger">
            Keluar
          </button>
        </form>
      </div>
    </section>
  </main>
</div>
@endsection