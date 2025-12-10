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

    input[readonly] {
      opacity: 0.85;
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
    
    .btn-register:disabled {
        background: #ccc;
        cursor: not-allowed;
        transform: none;
    }

    .btn-register:hover:not(:disabled) {
      opacity: 0.9;
      transform: translateY(-2px);
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

    /* Scrollable select for state (if needed later) */
    select.scrollable {
      max-height: 120px;
      overflow-y: auto;
    }

    /* Hide sections by default */
    .conditional-section {
      display: none;
    }
  </style>
</head>

<body class="light">

  <div class="container">
    <h1>Create Your Account</h1>
    <p class="subtitle">Join EduSpark as a student or teacher in Johor.</p>

    {{-- Success & Error Messages --}}
    @if(session('success'))
        <div style="color: green; margin-bottom: 16px;">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div style="color: red; margin-bottom: 16px;">
            <ul style="padding-left: 20px; margin: 0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('register.post') }}" method="POST" id="registration-form">
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
            <label for="role">I am a:</label>
            <select id="role" name="role" required>
                <option value="">-- Select Role --</option>
                <option value="student" {{ old('role')=='student' ? 'selected' : '' }}>Student</option>
                <option value="teacher" {{ old('role')=='teacher' ? 'selected' : '' }}>Teacher</option>
            </select>
        </div>

        <!-- State: Johor (auto, fixed) -->
        <div class="form-group conditional-section" id="state-section">
            <label for="state">State</label>
            <input type="text" id="state" name="state" value="Johor" readonly>
        </div>

        <!-- District -->
        <div class="form-group conditional-section" id="district-section">
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

        <!-- School -->
        <div class="form-group conditional-section" id="school-section">
            <label for="school_code">School</label>
            <select id="school_code" name="school_code" required>
                <option value="">-- Select School --</option>
                <!-- Options populated by JS -->
            </select>
        </div>

        <!-- Phone (optional) -->
        <div class="form-group conditional-section" id="phone-section">
            <label for="phone">Phone Number (optional)</label>
            <input 
                type="tel" 
                id="phone" 
                name="phone" 
                placeholder="+6012-345 6789"
                value="{{ old('phone') }}"
                pattern="^[\+]?[0-9\s\-\(\)]{7,}$"
                title="e.g. +60123456789"
            >
        </div>

        <button type="submit" class="btn-register" id="submit-btn" disabled>✨ Create Account</button>
    </form>

    <div class="links">
        <p>Already have an account? <a href="{{ url('/login') }}">Log in</a></p>
    </div>
  </div>

  <footer>
    © 2025 EduSpark • Learn • Play • Grow
  </footer>

  <script>
    // Sample school data: district → [ { code, name } ]
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
      'Batu Pahat': [
        { code: 'JBP0001', name: 'SMK Dato\' Bentara Luar' },
        { code: 'JBP1001', name: 'SK Batu Pahat' },
      ],
      // Add more districts as needed
    };

    // DOM Elements
    const form = document.getElementById('registration-form');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const roleSelect = document.getElementById('role');
    const stateSection = document.getElementById('state-section');
    const districtSection = document.getElementById('district-section');
    const schoolSection = document.getElementById('school-section');
    const phoneSection = document.getElementById('phone-section');
    const districtSelect = document.getElementById('district');
    const schoolSelect = document.getElementById('school_code');
    const submitBtn = document.getElementById('submit-btn');

    // --- Core Logic ---

    // 1. Function to check if all required fields are filled
    function checkFormValidity() {
        const requiredFields = [nameInput, emailInput, passwordInput, roleSelect, districtSelect, schoolSelect];
        
        // Check if all required fields have a non-empty value
        const allRequiredFilled = requiredFields.every(field => field.value.trim() !== '');

        // Also check if the role, district, and school sections are visible (implies the flow has reached this point)
        const conditionalFieldsVisible = roleSelect.value !== '' && districtSelect.value !== '' && schoolSelect.value !== '';
        
        // Enable button only if all required fields are filled AND conditional fields are selected
        submitBtn.disabled = !(allRequiredFilled && conditionalFieldsVisible);
    }

    // 2. Attach checkFormValidity to all required input fields
    [nameInput, emailInput, passwordInput, roleSelect, districtSelect, schoolSelect].forEach(field => {
        field.addEventListener('input', checkFormValidity);
        field.addEventListener('change', checkFormValidity);
    });

    // 3. Show sections progressively (and call checkFormValidity after changes)
    roleSelect.addEventListener('change', function() {
      if (this.value) {
        stateSection.style.display = 'block';
        districtSection.style.display = 'block';
        schoolSection.style.display = 'none';
        phoneSection.style.display = 'none';
        districtSelect.value = '';
        schoolSelect.innerHTML = '<option value="">-- Select District First --</option>';
      } else {
        stateSection.style.display = 'none';
        districtSection.style.display = 'none';
        schoolSection.style.display = 'none';
        phoneSection.style.display = 'none';
      }
      checkFormValidity();
    });

    districtSelect.addEventListener('change', function() {
      const district = this.value;
      schoolSelect.innerHTML = '<option value="">-- Select School --</option>';
      
      if (district && schoolsByDistrict[district]) {
        schoolsByDistrict[district].forEach(school => {
          const opt = document.createElement('option');
          opt.value = school.code;
          opt.textContent = school.name;
          // Retain old value if applicable (for Laravel validation errors)
          if (school.code === "{{ old('school_code') }}") opt.selected = true;
          schoolSelect.appendChild(opt);
        });
        schoolSection.style.display = 'block';
        phoneSection.style.display = 'block'; // Show phone after district is selected, school select is visible
      } else {
        schoolSection.style.display = 'none';
        phoneSection.style.display = 'none';
      }
      checkFormValidity();
    });

    schoolSelect.addEventListener('change', function() {
        // phoneSection is already visible after district selection, but we check validity.
        checkFormValidity();
    });


    // 4. Re-apply old selections on page load (for validation errors)
    document.addEventListener('DOMContentLoaded', function() {
      const oldRole = "{{ old('role') }}";
      const oldDistrict = "{{ old('district') }}";
      const oldSchool = "{{ old('school_code') }}";

      if (oldRole) {
        roleSelect.value = oldRole;
        stateSection.style.display = 'block';
        districtSection.style.display = 'block';
      }

      if (oldDistrict && schoolsByDistrict[oldDistrict]) {
        districtSelect.value = oldDistrict;
        // Trigger district change manually to populate schools
        const event = new Event('change');
        districtSelect.dispatchEvent(event);
        
        // Then select school if exists
        if (oldSchool) {
          // Use setTimeout to ensure the options are fully rendered before selecting
          setTimeout(() => {
            schoolSelect.value = oldSchool;
            // Trigger change on school select too
            schoolSelect.dispatchEvent(new Event('change'));
          }, 50);
        }
      } else if (oldDistrict) {
        districtSelect.value = oldDistrict;
        schoolSection.style.display = 'none';
      }
      
      // Final check on load to ensure button state is correct if all fields were submitted successfully
      checkFormValidity();
    });
  </script>
</body>
</html>