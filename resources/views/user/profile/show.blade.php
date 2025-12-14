@extends('layouts.app')

@section('content')

<div class="app">
  <main class="main" style="flex:1;">
    <div class="header">
      <div>
        <div class="title" style="font-weight:700;font-size:28px;">Profil</div>
        <div class="sub" style="color:var(--muted);font-size:13px;">Lihat dan urus maklumat akaun anda</div>
      </div>
      <form method="POST" action="{{ route('logout') }}" style="display: inline;">
        @csrf
        <button type="submit" class="btn btn-danger" style="display:inline-block !important; padding:12px 24px !important; background:transparent !important; color:#E63946 !important; border:2px solid #E63946 !important; text-decoration:none !important; border-radius:8px !important; font-weight:700 !important; font-size:14px !important; margin-top:15px !important; transition:all 0.2s ease !important; cursor:pointer !important; line-height:1 !important; height:auto !important;" onmouseover="this.style.background='rgba(230,57,70,0.15)'" onmouseout="this.style.background='transparent'">
          <i class="bi bi-box-arrow-right" style="margin-right:6px;"></i>Daftar Keluar
        </button>
      </form>
    </div>

    <!-- Personal Information -->
    <section class="panel">
      <div class="panel-header" style="display:flex;justify-content:space-between;align-items:center;">
        <h3>Maklumat Peribadi</h3>
        <a href="{{ route('profile.edit') }}" style="display:inline-flex; align-items:center; justify-content:center; background:transparent; border:none; color:var(--accent); padding:0; font-size:24px; transition:opacity .2s ease; text-decoration:none; cursor:pointer;" onmouseover="this.style.opacity='0.7';" onmouseout="this.style.opacity='1';" title="Kemaskini">
            <i class="bi bi-pencil-square"></i>
        </a>
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
    </section>

    <!-- Organization Details -->
    <section class="panel">
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
    <section class="panel">
      <div class="panel-header" style="display:flex;justify-content:space-between;align-items:center;">
        <h3>Sekuriti Akaun</h3>
        <a href="{{ route('profile.password.edit') }}" style="display:inline-flex; align-items:center; justify-content:center; background:transparent; border:none; color:var(--accent); padding:0; font-size:24px; transition:opacity .2s ease; text-decoration:none; cursor:pointer;" onmouseover="this.style.opacity='0.7';" onmouseout="this.style.opacity='1';" title="Kemaskini">
            <i class="bi bi-pencil-square"></i>
        </a>
      </div>
      <p style="margin: 0; color: var(--muted);">Kata laluan telah ditetapkan dan diamankan.</p>
    </section>


  </main>
</div>
@endsection