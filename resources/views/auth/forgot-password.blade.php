<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Lupa Kata Laluan — EduSpark</title>
  <link href="https://fonts.bunny.net/css?family=Inter" rel="stylesheet">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body.auth-page {
      font-family: Inter, system-ui, sans-serif;
    }

    .auth-form-group input,
    .auth-form-group select {
      box-sizing: border-box;
    }
  </style>
</head>

<body class="light auth-page">
  <main class="main">
    <div class="header">
      <div>
        <div class="title" style="text-align:center">Lupa Kata Laluan</div>
        <div class="sub" style="text-align:center">Masukkan alamat email anda dan kami akan menghantar pautan untuk tetapkan semula kata laluan.</div>
      </div>
    </div>

    <div class="auth-container" style="max-width:none;width:50%;margin:0;padding:30px 30px;">

    @if(session('status'))
      <div class="auth-success">✓ {{ session('status') }}</div>
    @endif

    @if($errors->any())
      <div class="auth-error">
        @foreach($errors->all() as $error)
          {{ $error }}<br>
        @endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
      @csrf

      <div class="auth-form-group">
        <label for="email">Alamat Email</label>
        <input 
          type="email" 
          id="email" 
          name="email" 
          required 
          placeholder="anda@contoh.com"
          value="{{ old('email') }}"
          autofocus
        />
      </div>

      <button type="submit" class="auth-btn">Hantar Pautan Tetapkan Semula</button>
    </form>
    </div>

    <div class="auth-links">
      <p><a href="{{ route('login') }}">Kembali ke Log Masuk</a></p>
    </div>
  </main>

  <script>
    // Apply dark theme based on system preference or localStorage
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const savedTheme = localStorage.getItem('theme');
    const theme = savedTheme || (prefersDark ? 'dark' : 'light');
    document.body.className = theme + ' auth-page';
  </script>

  <footer class="page-footer" style="margin-left: 0;">
    © 2025 EduSpark • Belajar • Bermain • Berkembang
  </footer>
</body>
</html>
