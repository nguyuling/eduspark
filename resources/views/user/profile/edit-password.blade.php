<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Change Password — EduSpark</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg-light: #f5f7ff;
      --bg-dark: #071026;
      --card-light: rgba(255, 255, 255, 0.9);
      --card-dark: #0f1724;
      --accent: #6A4DF7;
      --accent-2: #9C7BFF;
      --muted: #98a0b3;
      --success: #2A9D8F;
      --danger: #E63946;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Inter', sans-serif;
      line-height: 1.5;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      padding: 24px;
    }
    body.light { background: var(--bg-light); color: #0b1220; }
    body.dark { background: var(--bg-dark); color: #e6eef8; }

    .container { max-width: 500px; margin: 0 auto; }
    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 28px;
    }
    .header h1 { font-weight: 700; font-size: 24px; }
    .header p { color: var(--muted); font-size: 14px; }

    .card {
      border-radius: 14px;
      padding: 28px;
      margin-bottom: 20px;
      text-align: center;
    }
    body.light .card {
      background: var(--card-light);
      border: 1px solid rgba(11,18,32,0.04);
    }
    body.dark .card {
      background: var(--card-dark);
      border: 1px solid rgba(255,255,255,0.03);
    }

    .form-group { margin-bottom: 20px; text-align: left; }
    label {
      display: block;
      font-weight: 600;
      font-size: 14px;
      margin-bottom: 8px;
      color: var(--muted);
    }
    input {
      width: 100%;
      padding: 12px 16px;
      border-radius: 10px;
      border: 1px solid rgba(152,160,179,0.3);
      background: transparent;
      font-size: 15px;
      color: inherit;
    }
    body.light input { background: white; }
    input:focus {
      outline: none;
      border-color: var(--accent);
      box-shadow: 0 0 0 3px rgba(106,77,247,0.15);
    }

    .btn {
      padding: 12px 24px;
      font-weight: 600;
      font-size: 15px;
      border-radius: 10px;
      border: none;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      width: 100%;
    }
    .btn-primary {
      background: linear-gradient(90deg, var(--accent), var(--accent-2));
      color: white;
    }
    .btn-secondary {
      background: rgba(152,160,179,0.1);
      color: var(--muted);
    }

    .alert {
      padding: 12px 16px;
      border-radius: 10px;
      font-weight: 500;
      margin-bottom: 20px;
    }
    .alert-success { background: rgba(42,157,143,0.15); color: var(--success); }
    .alert-error { background: rgba(230,57,70,0.15); color: var(--danger); }

    .security-note {
      font-size: 13px;
      color: var(--muted);
      margin-top: 6px;
      text-align: left;
    }

    footer {
      margin-top: auto;
      text-align: center;
      padding-top: 24px;
      font-size: 13px;
      color: var(--muted);
    }
  </style>
</head>
<body class="dark">
  <div class="container">
    <div class="header">
      <div>
        <h1>🔒 Change Password</h1>
        <p>Secure your account with a new password</p>
      </div>
      <button id="themeToggle" style="background:none;border:0;color:inherit;font-weight:600;cursor:pointer;">🌙</button>
    </div>

    @if(session('success'))
      <div class="alert alert-success">
        ✅ {{ session('success') }}
      </div>
    @endif

    @if($errors->any())
      <div class="alert alert-error">
        ❌ Please correct the errors below.
      </div>
    @endif

    <div class="card">
      <form method="POST" action="{{ route('profile.password.update') }}">
        @csrf

        <div class="form-group">
          <label for="current_password">Current Password</label>
          <input
            type="password"
            id="current_password"
            name="current_password"
            required
            autocomplete="current-password"
          >
          @error('current_password')
            <div class="security-note" style="color:var(--danger);">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-group">
          <label for="password">New Password</label>
          <input
            type="password"
            id="password"
            name="password"
            required
            minlength="6"
            autocomplete="new-password"
          >
          <div class="security-note">
            • At least 6 characters<br>
            • Should differ from your current password
          </div>
          @error('password')
            <div class="security-note" style="color:var(--danger);">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-group">
          <label for="password_confirmation">Confirm New Password</label>
          <input
            type="password"
            id="password_confirmation"
            name="password_confirmation"
            required
            autocomplete="new-password"
          >
          @error('password_confirmation')
            <div class="security-note" style="color:var(--danger);">{{ $message }}</div>
          @enderror
        </div>

        <button type="submit" class="btn btn-primary">
          🔐 Update Password
        </button>
      </form>

      <div style="margin-top:20px;">
        <a href="{{ route('profile') }}" class="btn btn-secondary">
          ← Back to Profile
        </a>
      </div>
    </div>
  </div>

  <footer>© 2025 EduSpark • Powered by AplexTech</footer>

  <script>
    const body = document.body;
    const toggle = document.getElementById('themeToggle');
    const applyTheme = mode => {
      body.className = mode;
      toggle.textContent = mode === 'dark' ? '🌙' : '☀️';
      localStorage.setItem('theme', mode);
    };
    applyTheme(localStorage.getItem('theme') || 'dark');
    toggle?.addEventListener('click', () => 
      applyTheme(body.classList.contains('dark') ? 'light' : 'dark')
    );
  </script>
</body>
</html>