import React, { useState, useEffect } from 'react';
import { Link, useLocation } from 'react-router-dom';

const Header = () => {
  const location = useLocation();
  const [theme, setTheme] = useState('dark');
  const [userRole, setUserRole] = useState('student'); 
  const [userName, setUserName] = useState('Azila');

  useEffect(() => {
    // Load saved theme
    const savedTheme = localStorage.getItem('eduspark-theme') || 'dark';
    setTheme(savedTheme);
    document.body.className = savedTheme;
    
    // Load user role (in real app, get from API/auth)
    const savedRole = localStorage.getItem('eduspark-role') || 'student';
    setUserRole(savedRole);
    
    // Load user name
    const savedName = localStorage.getItem('eduspark-username') || 'Azila';
    setUserName(savedName);
    
    // Define CSS variables for both themes
    const root = document.documentElement;
    if (savedTheme === 'light') {
      root.style.setProperty('--accent', '#1D5DCD');
      root.style.setProperty('--accent-light', '#E63946');
      root.style.setProperty('--muted', '#666666');
      root.style.setProperty('--background', '#ffffff');
      root.style.setProperty('--card-bg', '#f8f9fa');
      root.style.setProperty('--border', 'rgba(0, 0, 0, 0.1)');
      root.style.setProperty('--text', '#333333');
      root.style.setProperty('--header-bg', 'rgba(0, 0, 0, 0.02)');
      root.style.setProperty('--input-bg', 'rgba(0, 0, 0, 0.02)');
    } else {
      root.style.setProperty('--accent', '#E63946');
      root.style.setProperty('--accent-light', '#1D5DCD');
      root.style.setProperty('--muted', '#cccccc');
      root.style.setProperty('--background', '#0d121a');
      root.style.setProperty('--card-bg', '#1a2232');
      root.style.setProperty('--border', 'rgba(255, 255, 255, 0.1)');
      root.style.setProperty('--text', '#ffffff');
      root.style.setProperty('--header-bg', 'rgba(255, 255, 255, 0.05)');
      root.style.setProperty('--input-bg', 'rgba(255, 255, 255, 0.05)');
    }
  }, []);

  const toggleTheme = () => {
    const newTheme = theme === 'dark' ? 'light' : 'dark';
    setTheme(newTheme);
    document.body.className = newTheme;
    localStorage.setItem('eduspark-theme', newTheme);
    
    // Update CSS variables
    const root = document.documentElement;
    if (newTheme === 'light') {
      root.style.setProperty('--accent', '#1D5DCD');
      root.style.setProperty('--accent-light', '#E63946');
      root.style.setProperty('--muted', '#666666');
      root.style.setProperty('--background', '#ffffff');
      root.style.setProperty('--card-bg', '#f8f9fa');
      root.style.setProperty('--border', 'rgba(0, 0, 0, 0.1)');
      root.style.setProperty('--text', '#333333');
      root.style.setProperty('--header-bg', 'rgba(0, 0, 0, 0.02)');
      root.style.setProperty('--input-bg', 'rgba(0, 0, 0, 0.02)');
    } else {
      root.style.setProperty('--accent', '#E63946');
      root.style.setProperty('--accent-light', '#1D5DCD');
      root.style.setProperty('--muted', '#cccccc');
      root.style.setProperty('--background', '#0d121a');
      root.style.setProperty('--card-bg', '#1a2232');
      root.style.setProperty('--border', 'rgba(255, 255, 255, 0.1)');
      root.style.setProperty('--text', '#ffffff');
      root.style.setProperty('--header-bg', 'rgba(255, 255, 255, 0.05)');
      root.style.setProperty('--input-bg', 'rgba(255, 255, 255, 0.05)');
    }
  };

  const toggleRole = () => {
    const newRole = userRole === 'student' ? 'teacher' : 'student';
    setUserRole(newRole);
    localStorage.setItem('eduspark-role', newRole);
    
    // Show notification
    alert(`Switched to ${newRole} view. You'll need to refresh or navigate to see changes.`);
  };

  // Navigation based on role
  const studentNavItems = [
    { path: '/', label: 'Dashboard', icon: '📊' },
    { path: '/games', label: 'Games', icon: '🎮' },
    { path: '/materials', label: 'Materials', icon: '📚' },
    { path: '/forum', label: 'Forum', icon: '💬' },
    { path: '/assessments', label: 'Assessments', icon: '📝' },
    { path: '/progress', label: 'Progress', icon: '📈' },
  ];

  const teacherNavItems = [
    { path: '/teacher', label: 'Dashboard', icon: '📊' },
    { path: '/teacher/games', label: 'Manage Games', icon: '🎮' },
    { path: '/teacher/materials', label: 'Manage Materials', icon: '📚' },
    { path: '/teacher/forum', label: 'Moderate Forum', icon: '💬' },
    { path: '/teacher/assessments', label: 'Create Assessments', icon: '📝' },
    { path: '/teacher/analytics', label: 'Analytics', icon: '📈' },
    { path: '/teacher/students', label: 'Student Management', icon: '👥' },
  ];

  const navItems = userRole === 'teacher' ? teacherNavItems : studentNavItems;

  return (
    <aside className="sidebar" style={{
      width: '240px',
      borderRadius: '16px',
      padding: '18px',
      display: 'flex',
      flexDirection: 'column',
      alignItems: 'center',
      gap: '12px',
      backdropFilter: 'blur(8px) saturate(120%)',
      background: theme === 'light' 
        ? 'linear-gradient(180deg, rgba(255,255,255,0.70), rgba(255,255,255,0.65))'
        : 'linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01))',
      border: theme === 'light' 
        ? '1px solid rgba(13,18,25,0.05)'
        : '1px solid rgba(255,255,255,0.03)',
      position: 'fixed',
      left: '20px',
      top: '20px',
      bottom: '20px',
      zIndex: 1000,
      color: theme === 'light' ? '#333333' : '#ffffff',
      overflowY: 'auto'
    }}>
      {/* Logo */}
      <div className="logo" style={{ 
        width: '100%', 
        height: 'auto', 
        marginBottom: '6px',
        display: 'flex',
        justifyContent: 'center',
        alignItems: 'center',
        padding: '10px 0',
        borderBottom: theme === 'light' 
          ? '1px solid rgba(0,0,0,0.05)' 
          : '1px solid rgba(255,255,255,0.05)'
      }}>
        <div style={{ position: 'relative', width: '110px' }}>
          {/* Fallback text if image fails */}
          <div style={{
            display: 'none',
            fontWeight: '700',
            fontSize: '20px',
            textAlign: 'center'
          }}>
            <span style={{ color: '#1D5DCD' }}>edu</span>
            <span style={{ color: '#E63946' }}>Spark</span>
          </div>
          
          {/* Logo Image */}
          <img 
            src="/eduSpark logo.png"
            alt="eduSpark Logo"
            onError={(e) => {
              // Show text if image fails
              e.target.style.display = 'none';
              e.target.previousElementSibling.style.display = 'block';
            }}
            style={{
              width: '100%',
              height: 'auto',
              maxWidth: '110px',
              objectFit: 'contain'
            }}
          />
        </div>
      </div>

      {/* User Profile & Role Badge */}
      <div style={{
        width: '100%',
        padding: '12px',
        backgroundColor: theme === 'light' 
          ? 'rgba(0,0,0,0.03)' 
          : 'rgba(255,255,255,0.05)',
        borderRadius: '12px',
        textAlign: 'center',
        marginBottom: '10px'
      }}>
        <div style={{
          fontSize: '14px',
          fontWeight: '600',
          marginBottom: '6px',
          color: theme === 'light' ? '#333' : '#fff',
          whiteSpace: 'nowrap',
          overflow: 'hidden',
          textOverflow: 'ellipsis'
        }}>
          {userName}
        </div>
        <div style={{
          display: 'flex',
          alignItems: 'center',
          justifyContent: 'center',
          gap: '6px'
        }}>
          <span style={{
            padding: '4px 10px',
            backgroundColor: userRole === 'teacher' 
              ? (theme === 'light' ? 'rgba(230, 57, 70, 0.1)' : 'rgba(230, 57, 70, 0.2)')
              : (theme === 'light' ? 'rgba(29, 93, 205, 0.1)' : 'rgba(29, 93, 205, 0.2)'),
            color: userRole === 'teacher' ? '#E63946' : '#1D5DCD',
            borderRadius: '20px',
            fontSize: '12px',
            fontWeight: '700',
            display: 'flex',
            alignItems: 'center',
            gap: '4px'
          }}>
            {userRole === 'teacher' ? '👨‍🏫' : '👨‍🎓'}
            {userRole === 'teacher' ? 'Teacher' : 'Student'}
          </span>
        </div>
      </div>

      {/* Navigation */}
      <nav className="nav" style={{ 
        width: '100%', 
        marginTop: '5px',
        flex: 1
      }}>
        {navItems.map((item) => {
          const isActive = location.pathname === item.path || 
                          (item.path !== '/' && location.pathname.startsWith(item.path));
          const accentColor = userRole === 'teacher' 
            ? (theme === 'light' ? '#E63946' : '#FF6B6B')
            : (theme === 'light' ? '#1D5DCD' : '#4DABF7');
          const mutedColor = theme === 'light' ? '#666666' : '#cccccc';
          
          return (
            <Link
              key={item.path}
              to={item.path}
              style={{
                display: 'flex',
                alignItems: 'center',
                gap: '10px',
                padding: '10px 12px',
                borderRadius: '12px',
                color: isActive ? accentColor : mutedColor,
                textDecoration: 'none',
                fontWeight: '600',
                margin: '6px 0',
                position: 'relative',
                background: isActive 
                  ? userRole === 'teacher'
                    ? theme === 'light'
                      ? 'linear-gradient(90deg, rgba(230, 57, 70, 0.16), rgba(230, 57, 70, 0.08))'
                      : 'linear-gradient(90deg, rgba(230, 57, 70, 0.16), rgba(230, 57, 70, 0.08))'
                    : theme === 'light'
                      ? 'linear-gradient(90deg, rgba(29, 93, 205, 0.16), rgba(29, 93, 205, 0.08))'
                      : 'linear-gradient(90deg, rgba(77, 171, 247, 0.16), rgba(77, 171, 247, 0.08))'
                  : 'transparent',
                boxShadow: isActive 
                  ? userRole === 'teacher'
                    ? '0 4px 12px rgba(230, 57, 70, 0.15)'
                    : '0 4px 12px rgba(29, 93, 205, 0.15)'
                  : 'none',
                transform: isActive ? 'translateY(-1px)' : 'none',
                transition: 'all 0.3s ease'
              }}
              onMouseOver={(e) => {
                if (!isActive) {
                  e.target.style.backgroundColor = theme === 'light' 
                    ? 'rgba(0,0,0,0.03)' 
                    : 'rgba(255,255,255,0.05)';
                  e.target.style.transform = 'translateY(-1px)';
                }
              }}
              onMouseOut={(e) => {
                if (!isActive) {
                  e.target.style.backgroundColor = 'transparent';
                  e.target.style.transform = 'translateY(0)';
                }
              }}
            >
              <span style={{ fontSize: '16px' }}>{item.icon}</span>
              <span style={{ fontSize: '14px' }}>{item.label}</span>
              {isActive && (
                <div style={{
                  position: 'absolute',
                  left: '0',
                  width: '4px',
                  height: 'calc(100% - 12px)',
                  background: accentColor,
                  borderRadius: '12px',
                  top: '6px'
                }} />
              )}
            </Link>
          );
        })}
      </nav>

      {/* Role Toggle Button */}
      <button
        onClick={toggleRole}
        style={{
          width: '100%',
          padding: '10px',
          backgroundColor: theme === 'light' 
            ? 'rgba(0,0,0,0.05)' 
            : 'rgba(255,255,255,0.08)',
          border: `1px solid ${theme === 'light' ? 'rgba(0,0,0,0.1)' : 'rgba(255,255,255,0.15)'}`,
          color: theme === 'light' ? '#555' : '#ddd',
          borderRadius: '10px',
          fontSize: '13px',
          fontWeight: '600',
          cursor: 'pointer',
          display: 'flex',
          alignItems: 'center',
          justifyContent: 'center',
          gap: '8px',
          marginTop: '10px',
          transition: 'all 0.3s ease'
        }}
        onMouseOver={(e) => {
          e.target.style.backgroundColor = theme === 'light' 
            ? 'rgba(0,0,0,0.08)' 
            : 'rgba(255,255,255,0.12)';
          e.target.style.transform = 'translateY(-1px)';
        }}
        onMouseOut={(e) => {
          e.target.style.backgroundColor = theme === 'light' 
            ? 'rgba(0,0,0,0.05)' 
            : 'rgba(255,255,255,0.08)';
          e.target.style.transform = 'translateY(0)';
        }}
      >
        <span style={{ fontSize: '16px' }}>
          {userRole === 'teacher' ? '👨‍🎓' : '👨‍🏫'}
        </span>
        Switch to {userRole === 'teacher' ? 'Student' : 'Teacher'}
      </button>

      {/* Theme Toggle */}
      <button
        onClick={toggleTheme}
        style={{
          marginTop: '8px',
          background: 'none',
          border: 'none',
          fontSize: '20px',
          cursor: 'pointer',
          padding: '10px',
          borderRadius: '50%',
          backgroundColor: theme === 'light' 
            ? 'rgba(11, 18, 32, 0.1)'
            : 'rgba(255, 255, 255, 0.05)',
          color: theme === 'light' ? '#333333' : '#ffffff',
          transition: 'all 0.3s ease',
          width: '44px',
          height: '44px',
          display: 'flex',
          alignItems: 'center',
          justifyContent: 'center'
        }}
        onMouseOver={(e) => {
          e.target.style.backgroundColor = theme === 'light' 
            ? 'rgba(11, 18, 32, 0.2)'
            : 'rgba(255, 255, 255, 0.1)';
          e.target.style.transform = 'scale(1.1)';
        }}
        onMouseOut={(e) => {
          e.target.style.backgroundColor = theme === 'light' 
            ? 'rgba(11, 18, 32, 0.1)'
            : 'rgba(255, 255, 255, 0.05)';
          e.target.style.transform = 'scale(1)';
        }}
      >
        {theme === 'dark' ? '🌙' : '☀️'}
      </button>

      {/* Footer Note */}
      <div style={{
        marginTop: '10px',
        fontSize: '11px',
        color: theme === 'light' ? '#888' : '#aaa',
        textAlign: 'center',
        padding: '5px'
      }}>
        v1.0 • {userRole === 'teacher' ? 'Teacher Mode' : 'Student Mode'}
      </div>
    </aside>
  );
};

export default Header;