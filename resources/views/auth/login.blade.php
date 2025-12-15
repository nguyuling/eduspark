<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Log Masuk — EduSpark</title>
  <link href="{{ asset('css/pages.css') }}" rel="stylesheet">
</head>

<body class="light auth-page">
  <div class="auth-container">
    <h1>Selamat Datang Kembali</h1>
    <p class="auth-subtitle">Log masuk ke akaun EduSpark anda untuk meneruskan pembelajaran.</p>

    @if(session('success'))
      <div class="auth-success">✓ {{ session('success') }}</div>
    @endif

    @if($errors->any())
      <div class="auth-error">
        @foreach($errors->all() as $error)
          {{ $error }}<br>
        @endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
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
        />
      </div>

      <div class="auth-form-group">
        <label for="password">Kata Laluan</label>
        <input 
          type="password" 
          id="password" 
          name="password" 
          required 
          minlength="6"
          placeholder="••••••••"
        />
      </div>

      <button type="submit" class="auth-btn">🔐 Log Masuk</button>
    </form>

    <div class="auth-links">
      <p>Tidak mempunyai akaun? <a href="{{ route('register') }}">Daftar di sini</a></p>
      <p><a href="{{ route('password.request') }}">Lupa kata laluan?</a></p>
    </div>
  </div>

  <footer class="auth-footer">
    © 2025 EduSpark • Belajar • Bermain • Berkembang
  </footer>

  <script>
    // Apply dark theme based on system preference or localStorage
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const savedTheme = localStorage.getItem('theme');
    const theme = savedTheme || (prefersDark ? 'dark' : 'light');
    document.body.className = theme + ' auth-page';
  </script>
</body>
</html>
