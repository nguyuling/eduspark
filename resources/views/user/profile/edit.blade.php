<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Edit Profile — EduSpark</title>
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

    .container { max-width: 600px; margin: 0 auto; width: 100%; }
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
      padding: 24px;
      margin-bottom: 20px;
      transition: transform 0.2s;
    }
    .card:hover { transform: translateY(-4px); }
    body.light .card {
      background: var(--card-light);
      border: 1px solid rgba(11,18,32,0.04);
    }
    body.dark .card {
      background: var(--card-dark);
      border: 1px solid rgba(255,255,255,0.03);
    }

    .form-group { margin-bottom: 20px; }
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
      gap: 6px;
    }
    .btn-primary {
      background: linear-gradient(90deg, var(--accent), var(--accent-2));
      color: white;
    }
    .btn-secondary {
      background: rgba(152,160,179,0.1);
      color: var(--muted);
    }
    .btn-link {
      background: none;
      color: var(--accent);
      padding: 0;
      font-weight: 600;
      text-decoration: none;
    }
    .btn-link:hover { text-decoration: underline; }

    .alert {
      padding: 12px 16px;
      border-radius: 10px;
      font-weight: 500;
      margin-bottom: 20px;
    }
    .alert-success { background: rgba(42,157,143,0.15); color: var(--success); }
    .alert-error { background: rgba(230,57,70,0.15); color: var(--danger); }

    .section-title {
      font-weight: 700;
      font-size: 18px;
      margin-bottom: 16px;
      color: inherit;
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
        <h1>🧑 My Profile</h1>
        <p>Manage your personal information</p>
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

    <!-- Name & Email Section -->
    <div class="card">
      <h2 class="section-title">👤 Personal Information</h2>
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
          @error('name')<div style="color:var(--danger);font-size:13px;margin-top:4px;">{{ $message }}</div>@enderror
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
          <div style="font-size:13px;color:var(--muted);margin-top:4px;">
            We’ll send a confirmation email after update.
          </div>
          @error('email')<div style="color:var(--danger);font-size:13px;margin-top:4px;">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="btn btn-primary">
          💾 Save Changes
        </button>
      </form>
    </div>

    <!-- Password Section (Link Only) -->
    <div class="card">
      <h2 class="section-title">🔒 Security</h2>
      <p style="margin-bottom:16px;">
        Keep your account secure by updating your password regularly.
      </p>
      <a href="{{ route('profile.password.edit') }}" class="btn btn-link">
        ➕ Change Password
      </a>
    </div>

    <a href="{{ url('/') }}" class="btn btn-secondary" style="display:inline-flex;">
      ← Back to Dashboard
    </a>
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