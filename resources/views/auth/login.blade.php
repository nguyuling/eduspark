<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Log Masuk — EduSpark</title>
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
        <div class="title" style="text-align:center">Selamat Kembali</div>
        <div class="sub" style="text-align:center">Log masuk ke akaun EduSpark anda untuk meneruskan pembelajaran.</div>
      </div>
    </div>

    <div class="auth-container" style="max-width:none;width:50%;margin:0;padding:30px 30px;">

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

    <form method="POST" action="{{ route('login') }}" novalidate>
      @csrf

      <div class="auth-form-group">
        <label for="email">Alamat Email*</label>
        <input 
          type="email" 
          id="email" 
          name="email" 
          required 
          placeholder="anda@contoh.com"
          value="{{ old('email') }}"
          autocomplete="email"
        />
        @error('email')
          <span style="color: red; font-size: 12px;">{{ $message }}</span>
        @enderror
      </div>

      <div class="auth-form-group">
        <label for="password">Kata Laluan*</label>
        <input 
          type="password" 
          id="password" 
          name="password" 
          required 
          minlength="6"
          placeholder="••••••••"
          autocomplete="current-password"
        />
        @error('password')
          <span style="color: red; font-size: 12px;">{{ $message }}</span>
        @enderror
      </div>

      <button type="submit" id="submit-btn" class="auth-btn">Log Masuk</button>
    </form>
    </div>

    <div class="auth-links">
      <p>Tidak mempunyai akaun? <a href="{{ route('register') }}">Daftar di sini</a></p>
      <p><a href="{{ route('password.request') }}">Lupa kata laluan?</a></p>
    </div>
  </main>

  <script>
    // Get form elements
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const submitBtn = document.getElementById('submit-btn');

    // Check if both fields are filled
    function updateButtonState() {
      const emailFilled = emailInput.value.trim() !== '';
      const passwordFilled = passwordInput.value.trim() !== '';
      
      if (emailFilled && passwordFilled) {
        submitBtn.disabled = false;
      } else {
        submitBtn.disabled = true;
      }
    }

    // Add event listeners
    emailInput.addEventListener('input', updateButtonState);
    passwordInput.addEventListener('input', updateButtonState);

    // Initialize on page load (for old form values)
    updateButtonState();

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
