@extends('layouts.app')

@section('content')
<style>
.app { margin-left: 268px; padding: 28px; font-family: Inter, system-ui, sans-serif; }
.main { flex: 1; }

.page-header { margin-bottom: 28px; }
.page-header h1 { font-weight: 700; font-size: 28px; margin: 0 0 6px 0; }
.page-header .subtitle { color: var(--muted); font-size: 14px; }

.panel { border-radius: var(--card-radius); padding: 20px; animation: fadeInUp .4s ease; margin-bottom: 20px; background: transparent; border: 2px solid #d4c5f9; backdrop-filter: blur(6px); box-shadow: 0 2px 12px rgba(2, 6, 23, 0.18); transition: border-color .2s ease, transform .12s ease, box-shadow .12s ease; }
body.light .panel { background: rgba(255, 255, 255, 0.96); }
body.dark .panel { background: #0f1724; }

.panel:hover { border-color: var(--accent); transform: translateY(-2px); box-shadow: 0 4px 20px rgba(106, 77, 247, 0.2); }

.panel-header { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding-bottom: 0; border-bottom: none; }
.panel-header h3 { font-weight: 700; font-size: 18px; margin: 0; }

.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
.info-item { display: flex; flex-direction: column; }
.info-label { color: var(--muted); font-size: 12px; font-weight: 600; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
.info-value { font-weight: 700; font-size: 15px; }

.panel-footer { display: flex; justify-content: space-between; align-items: center; padding-top: 16px; margin-top: 16px; border-top: 1px solid rgba(255, 255, 255, 0.08); }

.btn { cursor: pointer; padding: 8px 12px; border-radius: 10px; border: none; font-weight: 700; font-size: 14px; transition: transform .08s ease, box-shadow .12s ease, opacity .12s ease; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; }
.btn-primary { background: linear-gradient(90deg, var(--accent), var(--accent-2)); color: #fff; box-shadow: 0 6px 18px rgba(8, 12, 32, 0.25); }
.btn-primary:hover { transform: translateY(-3px); opacity: 0.98; }
.btn-secondary { background: transparent; color: inherit; border: 1px solid rgba(255, 255, 255, 0.06); }
.btn-secondary:hover { background: rgba(255, 255, 255, 0.05); }
.btn-danger { background: var(--danger); color: #fff; box-shadow: none; }
.btn-danger:hover { opacity: 0.9; }

.badge { display: inline-block; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 600; background: rgba(106, 77, 247, 0.15); color: var(--accent); }

@keyframes fadeInUp { from{opacity:0; transform:translateY(10px);} to{opacity:1; transform:none;} }

@media (max-width: 920px) {
  .app { margin-left: 0; }
  .info-grid { grid-template-columns: 1fr; }
}
</style>

<div class="app">
  <main class="main" style="flex:1;">
    <div class="header" style="display:flex;justify-content:space-between;align-items:flex-start; margin-bottom:40px; margin-top:40px; margin-left:40px; margin-right:40px;">
      <div>
        <div class="title" style="font-weight:700;font-size:28px;">Profil Saya</div>
        <div class="sub" style="color:var(--muted);font-size:13px;">Lihat dan urus maklumat akaun anda</div>
      </div>
    </div>

    <!-- Personal Information -->
    <section class="panel" style="margin-left:40px; margin-right:40px; margin-top:20px; margin-bottom:20px;">
      <div class="panel-header" style="margin:0 0 20px 0;">
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
        <a href="{{ route('profile.edit') }}" style="cursor: pointer; padding:12px 20px; border-radius:10px; border:none; background: linear-gradient(90deg, var(--accent), var(--accent-2)); color:#fff; font-weight:700; font-size:15px; transition: transform .08s ease, box-shadow .12s ease, opacity .12s ease; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; box-shadow: 0 6px 18px rgba(8, 12, 32, 0.25);">
          Edit Profil
        </a>
      </div>
    </section>

    <!-- Organization Details -->
    <section class="panel" style="margin-left:40px; margin-right:40px; margin-top:20px; margin-bottom:20px;">
      <div class="panel-header" style="margin:0 0 20px 0;">
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
    <section class="panel" style="margin-left:40px; margin-right:40px; margin-top:20px; margin-bottom:20px;">
      <div class="panel-header" style="margin:0 0 20px 0;">
        <h3>Keamanan Akaun</h3>
      </div>
      <p style="margin: 0 0 16px 0; color: var(--muted);">Kata laluan telah ditetapkan dan diamankan.</p>
      <div style="display: flex; justify-content: flex-end;">
        <a href="{{ route('profile.password.edit') }}" style="cursor: pointer; padding:12px 20px; border-radius:10px; border:none; background: var(--danger); color:#fff; font-weight:700; font-size:15px; transition: transform .08s ease, opacity .12s ease; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; box-shadow: none;">
          Ubah Kata Laluan
        </a>
      </div>
    </section>
  </main>
</div>
@endsection