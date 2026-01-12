<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Sahkan Email — EduSpark</title>
  <link href="{{ asset('css/pages.css') }}" rel="stylesheet">

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
      line-height: 1.6;
    }

    .success-message {
      background: #e8f5e9;
      color: #2e7d32;
      padding: 12px;
      border-radius: 10px;
      margin-bottom: 16px;
      font-size: 14px;
      border-left: 3px solid #2e7d32;
    }

    body.dark .success-message {
      background: rgba(46,125,50,0.1);
      color: #66bb6a;
      border-left-color: #66bb6a;
    }

    .btn-resend {
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

    .btn-resend:hover {
      opacity: 0.9;
      transform: translateY(-2px);
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
    <h1>Sahkan Email Anda</h1>
    <p class="subtitle">Sebelum melanjutkan, sila periksa email anda untuk pautan pengesahan.</p>

    @if (session('resent'))
        <div class="success-message">
            ✓ Pautan pengesahan baru telah dihantar ke alamat email anda.
        </div>
    @endif

    <p style="font-size: 14px; line-height: 1.6; margin-bottom: 20px;">Jika anda tidak menerima email, sila klik butang di bawah untuk menghantar semula pautan pengesahan.</p>

    <form method="POST" action="{{ route('verification.resend') }}">
        @csrf
        <button type="submit" class="btn-resend">📧 Hantar Semula Pautan</button>
    </form>
  </div>

  <footer class="page-footer" style="margin-left: 0;">
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
