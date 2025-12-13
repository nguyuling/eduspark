<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Log In — EduSpark</title>

  <style>
    :root {
      --bg-light: #f5f7ff;
      --bg-dark: #071026;
      --card-light: rgba(255, 255, 255, 0.9);
      --card-dark: #0f1724;
      --accent: #6A4DF7;
      --muted: #98a0b3;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Inter', sans-serif;
    }

    body {
      padding: 24px;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }

    body.light { background: var(--bg-light); color: #0b1220; }
    body.dark { background: var(--bg-dark); color: #e6eef8; }

    .container {
      max-width: 450px;
      width: 100%;
      background: var(--card-light);
      padding: 32px;
      border-radius: 14px;
      border: 1px solid rgba(11,18,32,0.06);
      text-align: center;
    }

    body.dark .container {
      background: var(--card-dark);
      border: 1px solid rgba(255,255,255,0.06);
    }

    h1 {
      font-size: 26px;
      margin-bottom: 8px;
    }

    .subtitle {
      font-size: 14px;
      color: var(--muted);
      margin-bottom: 20px;
    }

    .form-group {
      text-align: left;
      margin-bottom: 18px;
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
      padding: 11px 14px;
      font-size: 14px;
      border-radius: 8px;
      border: 2px solid #d1d5db;
      background: transparent;
      color: inherit;
      outline: none;
      transition: border-color 0.2s ease, background 0.2s ease;
    }

    body.dark input {
      color: white;
    }

    input:hover {
      border-color: #9ca3af;
      background: rgba(200, 200, 200, 0.08);
    }

    input:focus {
      outline: none;
      border-color: var(--accent);
      background: rgba(200, 200, 200, 0.08);
    }

    .btn-login {
      background: linear-gradient(90deg, var(--accent), #7c5dff);
      color: white;
      width: 100%;
      padding: 12px;
      font-size: 14px;
      font-weight: 700;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      margin-top: 10px;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      box-shadow: 0 4px 12px rgba(106,77,247,0.3);
    }

    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(106,77,247,0.4);
    }

    .links {
      margin-top: 20px;
      font-size: 0.95rem;
    }

    .links a {
      color: var(--accent);
      text-decoration: none;
      font-weight: 600;
    }

    .links a:hover {
      text-decoration: underline;
    }

    #errorMessage {
      margin-top: 14px;
      min-height: 20px;
      font-weight: 500;
      color: #d45a5a;
    }

    footer {
      margin-top: 25px;
      text-align: center;
      font-size: 12px;
      color: var(--muted);
    }
  </style>
</head>

<body class="light">
  <div class="container">
    <h1>Welcome Back!</h1>
    <p class="subtitle">Log in to continue your learning journey.</p>

    @if(session('success'))
      <div style="color: green; margin-bottom: 16px; font-weight: 500;">
        {{ session('success') }}
      </div>
    @endif

    @if($errors->any())
      <div style="color: #d45a5a; margin-bottom: 16px; font-weight: 500;">
        @foreach($errors->all() as $error)
          {{ $error }}<br>
        @endforeach
      </div>
    @endif

    <form id="loginForm">
      @csrf

      <div class="form-group">
        <label for="email">Email Address</label>
        <input 
          type="email" 
          id="email" 
          name="email" 
          required 
          placeholder="you@example.com"
          value="{{ old('email') }}"
        />
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input 
          type="password" 
          id="password" 
          name="password" 
          required 
          minlength="6"
          placeholder="••••••••"
        />
      </div>

      <button type="submit" class="btn-login">Log In</button>
    </form>

    <div class="links">
      <p>Don't have an account? <a href="{{ url('/register') }}">Register here</a></p>
    </div>

    <div id="errorMessage"></div>
  </div>

  <footer>
    © 2025 EduSpark • Powered by AplexTech
  </footer>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const form = document.getElementById('loginForm');
      const errorMessageEl = document.getElementById('errorMessage');

      if (!form) return;

      form.addEventListener('submit', async (e) => {
        e.preventDefault();
        errorMessageEl.textContent = '';

        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;

        if (!email || !password) {
          errorMessageEl.textContent = 'Please fill in all fields.';
          return;
        }

        try {
          const response = await fetch('{{ route("login.post") }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ email, password })
          });

        const data = await response.json();

          if (data.success) {
            window.location.href = data.redirect || '{{ route("home") }}'; 
          } else {
            errorMessageEl.textContent = data.message || 'Login failed. Please check your credentials.';
            document.getElementById('password').value = '';
            document.getElementById('password').focus();
          }
        } catch (err) {
          console.error('Login error:', err);
          errorMessageEl.textContent = '⚠️ Network error. Please check if the server is running.';
        }
      });
    });
  </script>
</body>
</html>
