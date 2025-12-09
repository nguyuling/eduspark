<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My Profile — EduSpark</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg-light: #f5f7ff;
      --bg-dark: #071026;
      --card-light: rgba(255, 255, 255, 0.9);
      --card-dark: #0f1724;
      --accent: #6A4DF7;
      --muted: #98a0b3;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Inter', sans-serif;
      overflow: hidden;
      height: 100vh;
    }

    .container {
      max-width: 600px;
      margin: 0 auto;
      display: flex;
      flex-direction: column;
      height: 100%;
    }

    /* ✅ Scrollable content with beautiful scrollbar */
    .scrollable-content {
      flex: 1;
      overflow-y: auto;
      padding: 24px;
    }

    /* 🔹 Sleek scrollbar */
    .scrollable-content::-webkit-scrollbar { width: 8px; }
    .scrollable-content::-webkit-scrollbar-track { background: transparent; }
    .scrollable-content::-webkit-scrollbar-thumb {
      background: rgba(106, 77, 247, 0.3);
      border-radius: 4px;
    }
    .scrollable-content::-webkit-scrollbar-thumb:hover {
      background: rgba(106, 77, 247, 0.5);
    }
    .scrollable-content {
      scrollbar-width: thin;
      scrollbar-color: rgba(106, 77, 247, 0.3) transparent;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 24px 24px 0;
    }

    h1 { font-size: 26px; }

    /* 🌓 Theme toggle */
    .theme-toggle {
      background: rgba(255,255,255,0.1);
      border: none;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      cursor: pointer;
      font-size: 18px;
      transition: 0.3s;
    }
    .theme-toggle:hover { opacity: 0.8; }

    .avatar-section {
      display: flex;
      flex-direction: column;
      align-items: center;
      margin-bottom: 28px;
    }

    .avatar {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      background: linear-gradient(135deg, #6A4DF7, #8A6FEF);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 40px;
      color: white;
      margin-bottom: 16px;
      position: relative;
      overflow: hidden;
      cursor: pointer;
    }

    .avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .avatar-overlay {
      position: absolute;
      inset: 0;
      background: rgba(0,0,0,0.5);
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0;
      transition: opacity 0.2s;
    }
    .avatar:hover .avatar-overlay { opacity: 1; }
    .avatar-overlay span {
      background: white;
      width: 32px;
      height: 32px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
    }

    #avatar-input { display: none; }

    .card {
      border-radius: 14px;
      padding: 20px;
      margin-bottom: 16px;
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
    }
    body.light .card { background: var(--card-light); border: 1px solid rgba(11,18,32,0.04); }
    body.dark .card { background: var(--card-dark); border: 1px solid rgba(255,255,255,0.03); }

    .label { font-size: 13px; color: var(--muted); margin-bottom: 4px; }
    .value { font-weight: 600; font-size: 16px; word-break: break-word; }

    .btn {
      padding: 8px 16px;
      border-radius: 8px;
      font-weight: 600;
      font-size: 14px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      text-decoration: none;
    }
    .btn-secondary {
      background: rgba(152,160,179,0.1);
      color: var(--muted);
      border: none;
    }
    .btn-edit { margin-left: auto; flex-shrink: 0; }

    footer {
      text-align: center;
      padding: 16px;
      font-size: 13px;
      color: var(--muted);
      border-top: 1px solid rgba(255,255,255,0.03);
    }
  </style>
</head>
<body class="dark">
  <div class="container">
    <div class="header">
      <h1>🧑 My Profile</h1>
      <!-- 🌓 Theme Toggle -->
      <button class="theme-toggle" id="theme-toggle" title="Switch theme">
        🌓
      </button>
    </div>

    <div class="scrollable-content">
      <div class="avatar-section">
        <div class="avatar" id="avatar-preview" title="Click to change photo">
          <span>{{ strtoupper(substr($user->name, 0, 1)) }}</span>
          <div class="avatar-overlay"><span>📷</span></div>
        </div>
        <input type="file" id="avatar-input" accept="image/*">
        <p style="font-size: 13px; color: var(--muted); margin-top: 4px;">Click avatar to upload</p>
      </div>

      <!-- Profile Cards -->
      @php
        $schoolNames = ['JEA3060' => 'SMK Pengerang Utama', 'JEA3061' => 'SMK Pengerang'];
        $schoolName = $schoolNames[$user->school_code] ?? $user->school_code;
        $roleName = $user->role === 'teacher' ? 'Teacher (Guru)' : 'Student (Pelajar)';
      @endphp

      <div class="card">
        <div><div class="label">Full Name</div><div class="value">{{ $user->name }}</div></div>
        <a href="{{ route('profile.edit') }}" class="btn btn-secondary btn-edit">✏️</a>
      </div>

      <div class="card">
        <div><div class="label">Email</div><div class="value">{{ $user->email }}</div></div>
        <a href="{{ route('profile.edit') }}" class="btn btn-secondary btn-edit">✏️</a>
      </div>

      <div class="card">
        <div><div class="label">Phone</div><div class="value">{{ $user->phone ?: '—' }}</div></div>
        <a href="{{ route('profile.edit') }}" class="btn btn-secondary btn-edit">✏️</a>
      </div>

      <div class="card">
        <div><div class="label">Role</div><div class="value">{{ $roleName }}</div></div>
      </div>

      <div class="card">
        <div><div class="label">District</div><div class="value">{{ $user->district }}</div></div>
      </div>

      <div class="card">
        <div><div class="label">School</div><div class="value">{{ $schoolName }}</div></div>
      </div>

      <div class="card">
        <div>
          <div class="label">User ID</div>
          <div class="value" style="font-family: monospace; background: rgba(106,77,247,0.15); padding: 4px 8px; border-radius: 4px;">
            {{ $user->user_id }}
          </div>
        </div>
        <button class="btn btn-secondary btn-edit" onclick="copyUserId()">📋</button>
      </div>

      <div class="card">
        <div><div class="label">Member Since</div><div class="value">{{ $user->created_at->format('j M Y') }}</div></div>
      </div>

      <div class="card">
        <div><div class="label">Password</div><div class="value">••••••••</div></div>
        <a href="{{ route('profile.password.edit') }}" class="btn btn-secondary btn-edit">🔐</a>
      </div>
    </div>

    <footer>© 2025 EduSpark • Powered by AplexTech</footer>
  </div>

  <script>
    // ==== 🌓 Theme Toggle ====
    const themeToggle = document.getElementById('theme-toggle');
    const body = document.body;

    // Load saved theme
    const savedTheme = localStorage.getItem('theme') || 'dark';
    body.className = savedTheme;
    themeToggle.textContent = savedTheme === 'dark' ? '🌙' : '☀️';

    themeToggle.addEventListener('click', () => {
      const newTheme = body.className === 'dark' ? 'light' : 'dark';
      body.className = newTheme;
      localStorage.setItem('theme', newTheme);
      themeToggle.textContent = newTheme === 'dark' ? '🌙' : '☀️';
    });

    // ==== 🖼️ Avatar Preview ====
    const avatarInput = document.getElementById('avatar-input');
    const avatarPreview = document.getElementById('avatar-preview');
    avatarPreview.addEventListener('click', () => avatarInput.click());
    avatarInput.addEventListener('change', e => {
      const file = e.target.files[0];
      if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = ev => {
          avatarPreview.innerHTML = `<img src="${ev.target.result}" alt="Avatar"><div class="avatar-overlay"><span>📷</span></div>`;
        };
        reader.readAsDataURL(file);
      }
    });

    // ==== 📋 Copy with Fallback ====
    function copyUserId() {
      const userId = `{{ $user->user_id }}`;
      const btn = event.target;

      // Try modern clipboard API first
      if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(userId).then(() => showCopyFeedback(btn, '✅ Copied!'));
      } else {
        // Fallback: create temporary input
        const tempInput = document.createElement('input');
        tempInput.value = userId;
        document.body.appendChild(tempInput);
        tempInput.select();
        try {
          const success = document.execCommand('copy');
          if (success) {
            showCopyFeedback(btn, '✅ Copied!');
          } else {
            throw new Error('execCommand failed');
          }
        } catch (err) {
          alert('⚠️ Copy failed. Please select and copy manually:\n' + userId);
        } finally {
          document.body.removeChild(tempInput);
        }
      }
    }

    function showCopyFeedback(btn, msg) {
      const original = btn.innerHTML;
      btn.innerHTML = msg;
      btn.style.opacity = '0.9';
      setTimeout(() => {
        btn.innerHTML = original;
        btn.style.opacity = '1';
      }, 1500);
    }
  </script>
</body>
</html>