import React, { useState, useEffect } from 'react';

const Leaderboard = ({ gameId = 'game4', onClose }) => {
  // Get user from localStorage instead of UserContext
  const [user, setUser] = useState(() => {
    try {
      const userData = localStorage.getItem('user');
      return userData ? JSON.parse(userData) : { role: 'student', class: '', id: 0 };
    } catch {
      return { role: 'student', class: '', id: 0 };
    }
  });

  const [entries, setEntries] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  // Filters
  const [filters, setFilters] = useState({
    game_id: gameId,
    class: user.role === 'teacher' ? '' : user.class,
    period: 'all',
  });

  // ✅ Fetch leaderboard
  const loadLeaderboard = async () => {
    setLoading(true);
    setError(null);
    try {
      const query = new URLSearchParams(filters).toString();
      const res = await fetch(`/api/leaderboard?${query}`);

      if (!res.ok) {
        const errData = await res.json().catch(() => ({}));
        throw new Error(errData.error || `HTTP ${res.status}`);
      }

      const data = await res.json();
      setEntries(data || []);
    } catch (err) {
      setError('Gagal memuatkan: ' + err.message);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    loadLeaderboard();
  }, [filters]);

  const handleFilterChange = (e) => {
    const { name, value } = e.target;
    setFilters((prev) => ({ ...prev, [name]: value }));
  };

  // ✅ Reset leaderboard
  const handleReset = async () => {
    if (
      !window.confirm(
        '⚠️ Set semula kedudukan untuk penapis terpilih?\nTindakan ini tidak boleh dibatalkan.'
      )
    )
      return;

    try {
      const query = new URLSearchParams({
        game_id: filters.game_id,
        class: filters.class || undefined,
      }).toString();

      const token = localStorage.getItem('token') || localStorage.getItem('auth_token');

      const headers = {
        Accept: 'application/json',
      };

      if (token) {
        headers['Authorization'] = `Bearer ${token}`;
      }

      const res = await fetch(`/api/leaderboard?${query}`, {
        method: 'DELETE',
        headers,
      });

      const result = await res.json();

      if (res.ok) {
        alert(`✅ ${result.message}`);
        loadLeaderboard();
      } else {
        throw new Error(result.error || result.message || 'Gagal set semula');
      }
    } catch (err) {
      alert('❌ ' + err.message);
    }
  };

  // Find current user's entry
  const myEntry = entries.find((e) => parseInt(e.user_id) === user.id);
  const myRank = myEntry ? myEntry.rank : null;

  return (
    <div style={styles.container}>
      <div style={styles.header}>
        <h2 style={styles.title}>🏆 Kedudukan Pemain</h2>
        {onClose && (
          <button onClick={onClose} style={styles.closeBtn}>
            ×
          </button>
        )}
      </div>

      {/* Filters */}
      <div style={styles.filters}>
        <select
          name="game_id"
          value={filters.game_id}
          onChange={handleFilterChange}
          style={styles.select}
        >
          <option value="1">Permainan 1</option>
          <option value="2">Permainan 2</option>
          <option value="3">Permainan 3</option>
          <option value="4">Permainan 4</option>
        </select>

        {user.role === 'teacher' && (
          <input
            name="class"
            placeholder="Kelas (cth: 4A)"
            value={filters.class}
            onChange={handleFilterChange}
            style={styles.input}
          />
        )}

        <select
          name="period"
          value={filters.period}
          onChange={handleFilterChange}
          style={styles.select}
        >
          <option value="all">Semua Masa</option>
          <option value="today">Hari Ini</option>
          <option value="week">Minggu Ini</option>
          <option value="month">Bulan Ini</option>
        </select>

        <button 
          onClick={loadLeaderboard} 
          style={styles.btnPrimary}
          onMouseEnter={(e) => e.target.style.backgroundColor = styles.btnPrimaryHover.backgroundColor}
          onMouseLeave={(e) => e.target.style.backgroundColor = styles.btnPrimary.backgroundColor}
        >
          Guna
        </button>
        {user.role === 'teacher' && (
          <button 
            onClick={handleReset} 
            style={styles.btnDanger}
            onMouseEnter={(e) => e.target.style.backgroundColor = styles.btnDangerHover.backgroundColor}
            onMouseLeave={(e) => e.target.style.backgroundColor = styles.btnDanger.backgroundColor}
          >
            🗑️ Set Semula
          </button>
        )}
      </div>

      {/* Self Highlight */}
      {myRank && (
        <div style={styles.myRank}>
          🎯 Anda berada di kedudukan <strong>#{myRank}</strong> dengan {myEntry.score} markah!
        </div>
      )}

      {/* Table */}
      {loading ? (
        <div style={styles.center}>
          <div style={styles.spinner}></div>
          <p>Memuatkan kedudukan...</p>
        </div>
      ) : error ? (
        <div style={styles.center}>
          <p style={{ color: '#d32f2f' }}>❌ {error}</p>
          <button onClick={loadLeaderboard} style={styles.retryBtn}>
            Cuba Lagi
          </button>
        </div>
      ) : entries.length === 0 ? (
        <div style={styles.center}>
          <p>Tiada rekod dijumpai untuk penapis ini.</p>
        </div>
      ) : (
        <div style={styles.tableWrap}>
          <table style={styles.table}>
            <thead>
              <tr>
                <th style={styles.th}>#</th>
                <th style={styles.th}>Nama</th>
                <th style={styles.th}>Kelas</th>
                <th style={styles.th}>Markah</th>
                <th style={styles.th}>Tarikh</th>
              </tr>
            </thead>
            <tbody>
              {entries.map((entry, idx) => (
                <tr
                  key={entry.id || `${entry.user_id}-${idx}`}
                  style={{
                    ...styles.tr,
                    ...(parseInt(entry.user_id) === user.id
                      ? styles.highlight
                      : {}),
                  }}
                  onMouseEnter={(e) => e.currentTarget.style.backgroundColor = styles.trHover.backgroundColor}
                  onMouseLeave={(e) => e.currentTarget.style.backgroundColor = parseInt(entry.user_id) === user.id ? styles.highlight.backgroundColor : 'transparent'}
                >
                  <td style={styles.td}>{entry.rank}</td>
                  <td style={styles.td}>{entry.username}</td>
                  <td style={styles.td}>{entry.class}</td>
                  <td style={{ ...styles.td, fontWeight: 'bold' }}>
                    {entry.score}
                  </td>
                  <td style={styles.td}>
                    {new Date(entry.timestamp || entry.created_at || Date.now()).toLocaleDateString('ms-MY', {
                      day: '2-digit',
                      month: 'short',
                      year: '2-digit',
                    })}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}

      {/* Legend */}
      <div style={styles.legend}>
        <span style={{ color: '#4a3a96', fontWeight: '600' }}>💡 Petua:</span> Baris berwarna biru menunjukkan kedudukan anda
      </div>
    </div>
  );
};

// ✅ Enhanced styling — Bahasa Melayu version
const styles = {
  container: {
    maxWidth: '900px',
    margin: '0 auto',
    backgroundColor: '#f9f7fe',
    borderRadius: '16px',
    fontFamily: '"Segoe UI", system-ui, sans-serif',
  },
  header: {
    display: 'flex',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: '1.5rem',
    padding: '1rem',
  },
  title: {
    margin: 0,
    color: '#4a3a96',
    fontWeight: '700',
    fontSize: '1.8rem',
  },
  closeBtn: {
    background: 'none',
    border: 'none',
    fontSize: '2rem',
    cursor: 'pointer',
    color: '#aaa',
    width: '40px',
    height: '40px',
    display: 'flex',
    justifyContent: 'center',
    alignItems: 'center',
    borderRadius: '50%',
    transition: 'background 0.2s',
  },
  filters: {
    display: 'flex',
    gap: '0.8rem',
    flexWrap: 'wrap',
    padding: '1.2rem',
    backgroundColor: '#fff',
    borderRadius: '12px',
    marginBottom: '1.5rem',
    boxShadow: '0 2px 6px rgba(0,0,0,0.05)',
  },
  select: {
    padding: '0.6rem 1rem',
    borderRadius: '8px',
    border: '1px solid #ddd',
    fontSize: '1rem',
    backgroundColor: 'white',
    minWidth: '140px',
    cursor: 'pointer',
  },
  input: {
    padding: '0.6rem 1rem',
    borderRadius: '8px',
    border: '1px solid #ddd',
    fontSize: '1rem',
    minWidth: '120px',
  },
  btnPrimary: {
    padding: '0.6rem 1.2rem',
    backgroundColor: '#4ECDC4',
    color: 'white',
    border: 'none',
    borderRadius: '8px',
    fontWeight: '600',
    cursor: 'pointer',
    transition: 'background 0.2s',
  },
  btnPrimaryHover: {
    backgroundColor: '#44A08D',
  },
  btnDanger: {
    padding: '0.6rem 1.2rem',
    backgroundColor: '#FF6B6B',
    color: 'white',
    border: 'none',
    borderRadius: '8px',
    fontWeight: '600',
    cursor: 'pointer',
    transition: 'background 0.2s',
  },
  btnDangerHover: {
    backgroundColor: '#EF5350',
  },
  myRank: {
    backgroundColor: '#E6F7FF',
    padding: '1rem',
    borderRadius: '12px',
    textAlign: 'center',
    fontWeight: '600',
    color: '#2C6ED5',
    marginBottom: '1.2rem',
    borderLeft: '4px solid #4ECDC4',
    fontSize: '1.1rem',
  },
  center: {
    textAlign: 'center',
    padding: '3rem',
    color: '#666',
  },
  spinner: {
    width: '40px',
    height: '40px',
    border: '3px solid #f3f3f3',
    borderTop: '3px solid #4ECDC4',
    borderRadius: '50%',
    animation: 'spin 1s linear infinite',
    margin: '0 auto 1rem',
  },
  retryBtn: {
    marginTop: '1rem',
    padding: '0.5rem 1.2rem',
    backgroundColor: '#4a3a96',
    color: 'white',
    border: 'none',
    borderRadius: '6px',
    cursor: 'pointer',
  },
  tableWrap: {
    overflowX: 'auto',
    borderRadius: '8px',
    boxShadow: '0 2px 12px rgba(0,0,0,0.08)',
    marginBottom: '1.5rem',
  },
  table: {
    width: '100%',
    borderCollapse: 'collapse',
    backgroundColor: '#fff',
    minWidth: '600px',
  },
  th: {
    backgroundColor: '#4ECDC4',
    color: 'white',
    padding: '1rem',
    textAlign: 'left',
    fontSize: '0.95rem',
    fontWeight: '600',
  },
  tr: {
    borderBottom: '1px solid #eee',
    transition: 'background 0.2s',
  },
  trHover: {
    backgroundColor: '#F8FAFC',
  },
  td: {
    padding: '0.8rem 1rem',
    fontSize: '0.95rem',
    color: '#334155',
  },
  highlight: {
    backgroundColor: '#F0FDFA !important',
    boxShadow: 'inset 4px 0 #4ECDC4',
    fontWeight: '600',
  },
  legend: {
    textAlign: 'center',
    fontSize: '0.9rem',
    color: '#64748B',
    marginTop: '1rem',
    padding: '0.5rem',
    fontStyle: 'italic',
  },
};

// Add CSS animation for spinner
if (typeof document !== 'undefined') {
  const style = document.createElement('style');
  style.textContent = `
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  `;
  document.head.appendChild(style);
}

export default Leaderboard;