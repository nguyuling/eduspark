@extends('layouts.app')

@section('content')
<style>
.app { margin-left: 268px; padding: 28px; font-family: Inter, system-ui, sans-serif; }
.main { flex: 1; }

.page-header { margin-bottom: 28px; }
.page-header h1 { font-weight: 700; font-size: 28px; margin: 0 0 6px 0; }
.page-header .subtitle { color: var(--muted); font-size: 14px; }

.alert { padding: 14px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; display: flex; align-items: flex-start; gap: 12px; }
.alert-success { background: rgba(42, 157, 143, 0.1); border-left: 3px solid var(--success); color: var(--success); }
.alert-danger { background: rgba(230, 57, 70, 0.1); border-left: 3px solid var(--danger); color: var(--danger); }
.alert-title { font-weight: 700; margin-bottom: 6px; }

.panel { border-radius: var(--card-radius); padding: 20px; margin-bottom: 20px; background: transparent; border: 2px solid #d4c5f9; backdrop-filter: blur(6px); box-shadow: 0 2px 12px rgba(2, 6, 23, 0.18); transition: border-color .2s ease; }
body.light .panel { background: rgba(255, 255, 255, 0.96); }
body.dark .panel { background: #0f1724; }

.panel-header { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid rgba(255, 255, 255, 0.08); }
.panel-header h3 { font-weight: 700; font-size: 16px; margin: 0; }

.form-group { margin-bottom: 18px; }
label { font-size: 13px; color: var(--muted); font-weight: 600; display: block; margin-bottom: 6px; }
input[type="text"], input[type="email"], input[type="password"], textarea, select {
  width: 100%; padding: 11px 14px; border-radius: 8px; border: 2px solid #d1d5db;
  background: transparent; color: inherit; font-size: 14px; outline: none;
  transition: border-color .2s ease, background .2s ease; box-sizing: border-box;
}
input[type="text"]:hover, input[type="email"]:hover, input[type="password"]:hover {
  border-color: #9ca3af; background: rgba(200, 200, 200, 0.08);
}
input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus {
  border-color: #9ca3af; background: rgba(200, 200, 200, 0.08); box-shadow: 0 6px 20px rgba(106, 77, 247, 0.12);
}

.form-text { font-size: 12px; color: var(--muted); margin-top: 4px; }
.error-msg { color: var(--danger); font-size: 12px; margin-top: 4px; }

.panel-footer { display: flex; justify-content: space-between; align-items: center; padding-top: 16px; margin-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.08); }

.btn { cursor: pointer; padding: 8px 12px; border-radius: 10px; border: none; font-weight: 700; font-size: 14px; transition: transform .08s ease, box-shadow .12s ease, opacity .12s ease; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; }
.btn-primary { background: linear-gradient(90deg, var(--accent), var(--accent-2)); color: #fff; box-shadow: 0 6px 18px rgba(8, 12, 32, 0.25); }
.btn-primary:hover { transform: translateY(-3px); opacity: 0.98; }
.btn-secondary { background: transparent; color: inherit; border: 1px solid rgba(255, 255, 255, 0.06); }
.btn-secondary:hover { background: rgba(255, 255, 255, 0.05); }

@media (max-width: 920px) {
  .app { margin-left: 0; }
  .panel-footer { flex-direction: column; gap: 12px; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const themeToggle = document.getElementById('themeToggle');
  if(themeToggle) {
    themeToggle.addEventListener('click', function() {
      const isDark = document.body.classList.contains('dark');
      const newTheme = isDark ? 'light' : 'dark';
      document.body.classList.replace(isDark ? 'dark' : 'light', newTheme);
      localStorage.setItem('theme', newTheme);
      themeToggle.textContent = newTheme === 'dark' ? '🌙' : '☀️';
    });
    const isDark = document.body.classList.contains('dark');
    themeToggle.textContent = isDark ? '🌙' : '☀️';
  }
});
</script>

<div class="app">
  <main class="main">
    <div style="display:flex;justify-content:space-between;align-items:flex-start; margin-bottom:24px;">
      <div class="page-header">
        <h1>Edit Profile</h1>
        <div class="subtitle">Update your personal information</div>
      </div>
      <button id="themeToggle" style="background:none;border:0;color:inherit;font-weight:600;cursor:pointer;font-size:24px;">🌙</button>
    </div>

    @if(session('success'))
      <div class="alert alert-success">
        <span style="font-size: 18px;">✓</span>
        <div>
          <div class="alert-title">Success</div>
          {{ session('success') }}
        </div>
      </div>
    @endif

    @if($errors->any())
      <div class="alert alert-danger">
        <span style="font-size: 18px;">⚠️</span>
        <div>
          <div class="alert-title">Error</div>
          Please correct the following errors before saving.
        </div>
      </div>
    @endif

    <div class="panel">
      <div class="panel-header">
        <h3>Personal Details</h3>
      </div>

      <form method="POST" action="{{ route('profile.update') }}">
        @csrf

        <div class="form-group">
          <label for="name">Full Name</label>
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
          <label for="email">Email Address</label>
          <input
            type="email"
            id="email"
            name="email"
            value="{{ old('email', $user->email) }}"
            required
          >
          <div class="form-text">Your email is used for account verification.</div>
          @error('email')
            <div class="error-msg">{{ $message }}</div>
          @enderror
        </div>

        <div class="panel-footer">
          <a href="{{ route('profile.show') }}" class="btn btn-secondary">
            ← Back to Profile
          </a>
          <button type="submit" class="btn btn-primary">
            Save Changes
          </button>
        </div>
      </form>
    </div>

    <div class="panel" style="margin-top: 20px;">
      <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
          <h3 style="margin: 0 0 4px 0; font-weight: 700;">Password Security</h3>
          <p style="margin: 0; color: var(--muted); font-size: 13px;">Update your password to keep your account secure.</p>
        </div>
        <a href="{{ route('profile.password.edit') }}" class="btn btn-primary">
          Change Password
        </a>
      </div>
    </div>
  </main>
</div>
@endsection