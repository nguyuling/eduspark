<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Daftar Akaun — EduSpark</title>
  <link href="https://fonts.bunny.net/css?family=Inter" rel="stylesheet">
  <link href="{{ asset('css/pages.css') }}" rel="stylesheet">
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
        <div class="title" style="text-align:center">Daftar Masuk</div>
        <div class="sub" style="text-align:center">Sertai EduSpark dan mulai pembelajaran anda</div>
      </div>
    </div>

    <div class="auth-container" style="max-width:none;width:50%;margin:0;padding:30px 30px;">

    {{-- Error Messages --}}
    @if($errors->any())
        <div class="auth-error">
            @foreach($errors->all() as $error)
              {{ $error }}<br>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="auth-form-group">
            <label for="name">Nama Penuh</label>
            <input type="text" id="name" name="name" required placeholder="contoh: Ahmad bin Ali" value="{{ old('name') }}">
        </div>

        <div class="auth-form-group">
            <label for="email">Alamat Email</label>
            <input type="email" id="email" name="email" required placeholder="anda@contoh.com" value="{{ old('email') }}">
        </div>

        <div class="auth-form-group">
            <label for="password">Kata Laluan</label>
            <input type="password" id="password" name="password" required minlength="6" placeholder="Minimum 6 aksara">
        </div>

        <div class="auth-form-group">
            <label for="password_confirmation">Sahkan Kata Laluan</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required minlength="6" placeholder="••••••••">
        </div>

        <div class="auth-form-group" id="role-group" style="display: none;">
            <label for="role">Saya adalah:</label>
            <select id="role" name="role">
                <option value="">-- Pilih Peranan --</option>
                <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Pelajar</option>
                <option value="teacher" {{ old('role') == 'teacher' ? 'selected' : '' }}>Guru</option>
            </select>
        </div>

        <div class="auth-form-group" id="district-group" style="display: none;">
            <label for="district">Daerah</label>
            <select id="district" name="district">
                <option value="">-- Pilih Daerah --</option>
                <option value="Pengerang" {{ old('district') == 'Pengerang' ? 'selected' : '' }}>Pengerang</option>
                <option value="Johor Bahru" {{ old('district') == 'Johor Bahru' ? 'selected' : '' }}>Johor Bahru</option>
                <option value="Kota Tinggi" {{ old('district') == 'Kota Tinggi' ? 'selected' : '' }}>Kota Tinggi</option>
                <option value="Mersing" {{ old('district') == 'Mersing' ? 'selected' : '' }}>Mersing</option>
                <option value="Batu Pahat" {{ old('district') == 'Batu Pahat' ? 'selected' : '' }}>Batu Pahat</option>
                <option value="Kluang" {{ old('district') == 'Kluang' ? 'selected' : '' }}>Kluang</option>
                <option value="Pontian" {{ old('district') == 'Pontian' ? 'selected' : '' }}>Pontian</option>
                <option value="Segamat" {{ old('district') == 'Segamat' ? 'selected' : '' }}>Segamat</option>
                <option value="Muar" {{ old('district') == 'Muar' ? 'selected' : '' }}>Muar</option>
            </select>
        </div>

        <div class="auth-form-group" id="school-group" style="display: none;">
            <label for="school_code">Sekolah</label>
            <select id="school_code" name="school_code">
                <option value="">-- Pilih Sekolah --</option>
            </select>
        </div>

        <div class="auth-form-group" id="phone-group" style="display: none;">
            <label for="phone">Nombor Telefon (Pilihan)</label>
            <input type="tel" id="phone" name="phone" placeholder="+60123456789" value="{{ old('phone') }}">
        </div>

        <button type="submit" id="submit-btn" class="auth-btn" disabled>Daftar Masuk</button>
    </form>
    </div>

    <div class="auth-links">
        <p>Sudah mempunyai akaun? <a href="{{ route('login') }}">Log masuk di sini</a></p>
    </div>
  </main>

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

    // Get form elements
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirmation');
    const roleSelect = document.getElementById('role');
    const districtSelect = document.getElementById('district');
    const schoolSelect = document.getElementById('school_code');
    const phoneInput = document.getElementById('phone');
    const submitBtn = document.getElementById('submit-btn');

    const roleGroup = document.getElementById('role-group');
    const districtGroup = document.getElementById('district-group');
    const schoolGroup = document.getElementById('school-group');
    const phoneGroup = document.getElementById('phone-group');

    // Check if a field has a value
    function isFieldFilled(field) {
      return field.value.trim() !== '';
    }

    // Update visibility of conditional fields and button state
    function updateFieldVisibility() {
      // Show role if all 4 basic fields are filled
      const basicFieldsFilled = isFieldFilled(nameInput) && 
                                 isFieldFilled(emailInput) && 
                                 isFieldFilled(passwordInput) && 
                                 isFieldFilled(passwordConfirmInput);
      
      if (basicFieldsFilled) {
        roleGroup.style.display = 'block';
        roleSelect.setAttribute('required', 'required');
      } else {
        roleGroup.style.display = 'none';
        roleSelect.removeAttribute('required');
        roleSelect.value = '';
        districtSelect.value = '';
        schoolSelect.value = '';
        districtGroup.style.display = 'none';
        schoolGroup.style.display = 'none';
        phoneGroup.style.display = 'none';
        submitBtn.disabled = true;
        return;
      }

      // Show district if role is selected
      if (isFieldFilled(roleSelect)) {
        districtGroup.style.display = 'block';
        districtSelect.setAttribute('required', 'required');
      } else {
        districtGroup.style.display = 'none';
        districtSelect.removeAttribute('required');
        districtSelect.value = '';
        schoolSelect.value = '';
        schoolGroup.style.display = 'none';
        phoneGroup.style.display = 'none';
        submitBtn.disabled = true;
        return;
      }

      // Show school if district is selected
      if (isFieldFilled(districtSelect)) {
        schoolGroup.style.display = 'block';
        schoolSelect.setAttribute('required', 'required');
      } else {
        schoolGroup.style.display = 'none';
        schoolSelect.removeAttribute('required');
        schoolSelect.value = '';
        phoneGroup.style.display = 'none';
        submitBtn.disabled = true;
        return;
      }

      // Show phone if school is selected and enable button
      if (isFieldFilled(schoolSelect)) {
        phoneGroup.style.display = 'block';
        submitBtn.disabled = false;
      } else {
        phoneGroup.style.display = 'none';
        submitBtn.disabled = true;
      }
    }

    // Handle district change
    function updateSchools() {
      const selectedDistrict = districtSelect.value;
      schoolSelect.innerHTML = '<option value="">-- Pilih Sekolah --</option>';

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

    // Add event listeners for field visibility
    nameInput.addEventListener('input', updateFieldVisibility);
    emailInput.addEventListener('input', updateFieldVisibility);
    passwordInput.addEventListener('input', updateFieldVisibility);
    passwordConfirmInput.addEventListener('input', updateFieldVisibility);
    roleSelect.addEventListener('change', updateFieldVisibility);
    districtSelect.addEventListener('change', function() {
      updateSchools();
      updateFieldVisibility();
    });
    schoolSelect.addEventListener('change', updateFieldVisibility);

    // Initialize visibility on page load (for old form values)
    updateFieldVisibility();

    // Apply dark theme based on system preference or localStorage
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const savedTheme = localStorage.getItem('theme');
    const theme = savedTheme || (prefersDark ? 'dark' : 'light');
    document.body.className = theme + ' auth-page';
  </script>
</body>
</html>