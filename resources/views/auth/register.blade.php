<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Create Account — EduSpark</title>

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

    input, select {
      width: 100%;
      padding: 12px;
      font-size: 15px;
      border-radius: 10px;
      border: 1px solid rgba(11,18,32,0.15);
      background: white;
    }

    body.dark input, body.dark select {
      background: #0d1525;
      color: white;
      border: 1px solid rgba(255,255,255,0.15);
    }

    input:focus, select:focus {
      outline: none;
      border-color: var(--accent);
      box-shadow: 0 0 0 2px rgba(106,77,247,0.25);
    }

    .btn-register {
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

    .btn-register:hover {
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

    .logo {
      text-align: center;
      margin-bottom: 24px;
      font-size: 28px;
    }
  </style>
</head>

<body class="light">

  <div class="container">
    <h1>Create Your Account</h1>
    <p class="subtitle">Join EduSpark and start learning</p>

    {{-- Error Messages --}}
    @if($errors->any())
        <div class="error-message">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="/register">
        @csrf

        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" required placeholder="e.g. Ahmad bin Ali" value="{{ old('name') }}">
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required placeholder="you@example.com" value="{{ old('email') }}">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required minlength="6" placeholder="At least 6 characters">
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required minlength="6" placeholder="••••••••">
        </div>

        <div class="form-group">
            <label for="role">Role</label>
            <select id="role" name="role" required>
                <option value="">-- Select Role --</option>
                <option value="student">Student</option>
                <option value="teacher">Teacher</option>
            </select>
        </div>

        <div class="form-group">
            <label for="district">District</label>
            <select id="district" name="district" required>
                <option value="">-- Select District --</option>
                <option value="Pengerang" {{ old('district')=='Pengerang' ? 'selected' : '' }}>Pengerang</option>
                <option value="Johor Bahru" {{ old('district')=='Johor Bahru' ? 'selected' : '' }}>Johor Bahru</option>
                <option value="Kota Tinggi" {{ old('district')=='Kota Tinggi' ? 'selected' : '' }}>Kota Tinggi</option>
                <option value="Mersing" {{ old('district')=='Mersing' ? 'selected' : '' }}>Mersing</option>
                <option value="Batu Pahat" {{ old('district')=='Batu Pahat' ? 'selected' : '' }}>Batu Pahat</option>
                <option value="Kluang" {{ old('district')=='Kluang' ? 'selected' : '' }}>Kluang</option>
                <option value="Pontian" {{ old('district')=='Pontian' ? 'selected' : '' }}>Pontian</option>
                <option value="Segamat" {{ old('district')=='Segamat' ? 'selected' : '' }}>Segamat</option>
                <option value="Muar" {{ old('district')=='Muar' ? 'selected' : '' }}>Muar</option>
            </select>
        </div>

        <div class="form-group">
            <label for="school_code">School</label>
            <select id="school_code" name="school_code" required>
                <option value="">-- Select School --</option>
                <!-- Options populated by JavaScript -->
            </select>
        </div>

        <div class="form-group">
            <label for="phone">Phone Number (Optional)</label>
            <input type="tel" id="phone" name="phone" placeholder="+60123456789" value="{{ old('phone') }}">
        </div>

        <button type="submit" class="btn-register">✨ Create Account</button>
    </form>

    <div class="links">
        <p>Already have an account? <a href="{{ route('login') }}">Log in</a></p>
    </div>
  </div>

  <footer>
    © 2025 EduSpark • Learn • Play • Grow
  </footer>

  <script>
    // School data by district
    const schoolsByDistrict = {
      'Pengerang': [
        { code: 'JPG0001', name: 'SMK Pengerang Utama' },
        { code: 'JPG0002', name: 'SMK Pengerang' },
        { code: 'JPG1001', name: 'SK Pengerang' },
        { code: 'JPG1002', name: 'SK Sungai Rengit' },
      ],
      'Johor Bahru': [
        { code: 'JJB0001', name: 'SMK Taman Universiti' },
        { code: 'JJB0002', name: 'SMK Dato\' Onn' },
        { code: 'JJB1001', name: 'SK Taman Universiti 2' },
        { code: 'JJB1002', name: 'SK Seri Permai' },
      ],
      'Kota Tinggi': [
        { code: 'JKT0001', name: 'SMK Kota Tinggi' },
        { code: 'JKT1001', name: 'SK Kota Tinggi' },
      ],
      'Mersing': [
        { code: 'JMS0001', name: 'SMK Mersing' },
        { code: 'JMS1001', name: 'SK Mersing' },
      ],
      'Batu Pahat': [
        { code: 'JBP0001', name: 'SMK Dato\' Bentara Luar' },
        { code: 'JBP1001', name: 'SK Batu Pahat' },
      ],
      'Kluang': [
        { code: 'JKG0001', name: 'SMK Kluang' },
        { code: 'JKG1001', name: 'SK Kluang' },
      ],
      'Pontian': [
        { code: 'JPT0001', name: 'SMK Pontian' },
        { code: 'JPT1001', name: 'SK Pontian' },
      ],
      'Segamat': [
        { code: 'JSG0001', name: 'SMK Segamat' },
        { code: 'JSG1001', name: 'SK Segamat' },
      ],
      'Muar': [
        { code: 'JMR0001', name: 'SMK Muar' },
        { code: 'JMR1001', name: 'SK Muar' },
      ],
    };

    // Handle district change
    const districtSelect = document.getElementById('district');
    const schoolSelect = document.getElementById('school_code');

    function updateSchools() {
      const selectedDistrict = districtSelect.value;
      schoolSelect.innerHTML = '<option value="">-- Select School --</option>';

      if (selectedDistrict && schoolsByDistrict[selectedDistrict]) {
        const schools = schoolsByDistrict[selectedDistrict];
        schools.forEach(school => {
          const option = document.createElement('option');
          option.value = school.code;
          option.textContent = school.name;
          schoolSelect.appendChild(option);
        });
      }
    }

    districtSelect.addEventListener('change', updateSchools);

    // Apply dark theme based on system preference or localStorage
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const savedTheme = localStorage.getItem('theme');
    const theme = savedTheme || (prefersDark ? 'dark' : 'light');
    document.body.className = theme;
  </script>
</body>
</html>
