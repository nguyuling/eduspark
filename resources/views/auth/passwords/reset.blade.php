<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Tetapkan Semula Kata Laluan — EduSpark</title>

  <style>
    :root {
      --bg-light: #f5f7ff;
      --bg-dark: #071026;
      --card-light: rgba(255, 255, 255, 0.9);
      --card-dark: #0f1724;
      --accent: #6A4DF7;
      --muted: #98a0b3;
    }

    * { box-sizing: border-box; }

    body {
      font-family: 'Inter', sans-serif;
      padding: 24px;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      margin: 0;
    }

    body.light { background: var(--bg-light); color: #0b1220; }
    body.dark { background: var(--bg-dark); color: #e6eef8; }

    .container {
      max-width: 500px;
      width: 100%;
      background: var(--card-light);
      padding: 32px;
      border-radius: 14px;
      border: 1px solid rgba(11,18,32,0.06);
      box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }

    body.dark .container {
      background: var(--card-dark);
      border: 1px solid rgba(255,255,255,0.06);
    }

    h1 {
      margin-bottom: 8px;
      font-size: 26px;
    }

    .subtitle {
      font-size: 14px;
      color: var(--muted);
      margin-bottom: 20px;
    }

    .form-group {
      margin-bottom: 18px;
      width: 100%;
    }

    label {
      font-size: 14px;
      font-weight: 600;
      color: var(--muted);
      display: block;
      margin-bottom: 6px;
    }

    input {
      width: 100%;
      padding: 12px;
      font-size: 15px;
      border-radius: 10px;
      border: 1px solid rgba(11,18,32,0.15);
      background: white;
    }

    body.dark input {
      background: #0d1525;
      color: white;
      border: 1px solid rgba(255,255,255,0.15);
    }

    input:focus {
      outline: none;
      border-color: var(--accent);
      box-shadow: 0 0 0 2px rgba(106,77,247,0.25);
    }

    .btn-submit {
      background: var(--accent);
      color: white;
      width: 100%;
      padding: 14px;
      border-radius: 10px;
      font-size: 16px;
      font-weight: 600;
      border: none;
      cursor: pointer;
      margin-top: 10px;
      transition: 0.2s;
    }

    .btn-submit:hover {
      opacity: 0.9;
      transform: translateY(-2px);
    }

    .error-message {
      background: #fee;
      color: #c33;
      padding: 12px;
      border-radius: 10px;
      margin-bottom: 16px;
      font-size: 14px;
      border-left: 3px solid #c33;
    }

    body.dark .error-message {
      background: rgba(206,51,51,0.1);
      color: #ff6b6b;
      border-left-color: #ff6b6b;
    }

    .links {
      margin-top: 16px;
      font-size: 14px;
      text-align: center;
    }

    .links a {
      color: var(--accent);
      text-decoration: none;
      font-weight: 600;
    }

    .links a:hover {
      text-decoration: underline;
    }

    footer {
      margin-top: 20px;
      text-align: center;
      font-size: 12px;
      color: var(--muted);
    }
  </style>
</head>

<body class="light">

  <div class="container">
    <h1>Tetapkan Semula Kata Laluan</h1>
    <p class="subtitle">Masukkan maklumat baru anda untuk tetapkan semula kata laluan.</p>

    @if($errors->any())
        <div class="error-message">
            @foreach($errors->all() as $error)
                {{ $error }}<br>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="form-group">
            <label for="email">Alamat Email</label>
            <input type="email" id="email" name="email" required placeholder="anda@contoh.com" value="{{ $email ?? old('email') }}" autofocus>
        </div>

        <div class="form-group">
            <label for="password">Kata Laluan Baru</label>
            <input type="password" id="password" name="password" required minlength="8" placeholder="Minimum 8 aksara">
        </div>

        <div class="form-group">
            <label for="password_confirmation">Sahkan Kata Laluan</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" placeholder="••••••••">
        </div>

        <button type="submit" class="btn-submit">🔐 Tetapkan Semula Kata Laluan</button>
    </form>

    <div class="links">
        <p><a href="{{ route('login') }}">← Kembali ke Log Masuk</a></p>
    </div>
  </div>

  <footer>
    © 2025 EduSpark • Belajar • Bermain • Berkembang
  </footer>

  <script>
    // Apply dark theme based on system preference or localStorage
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const savedTheme = localStorage.getItem('theme');
    const theme = savedTheme || (prefersDark ? 'dark' : 'light');
    document.body.className = theme;
  </script>
</body>
</html>
