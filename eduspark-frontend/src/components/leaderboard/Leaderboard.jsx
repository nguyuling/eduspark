import React, { useState, useEffect } from 'react';

const Leaderboard = ({ gameId = 'game4', onClose }) => {
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

  // Game mapping with actual names
  const gameOptions = [
    { id: '1', name: '🚀 Pertahanan Kosmik' },
    { id: '2', name: '🎯 Tumbuk Tikus' },
    { id: '3', name: '🎴 Padanan Ingatan' },
    { id: '4', name: '🌿 Labyrinth Java' }
  ];

  const [filters, setFilters] = useState({
    game_id: gameId,
    class: user.role === 'teacher' ? '' : user.class,
    period: 'all',
  });

  // ✅ Generate unique mock data (no duplicate players)
  const generateUniqueMockData = () => {
    const players = [
      { id: 101, name: 'Ali Ahmad', class: '4A' },
      { id: 102, name: 'Siti Sarah', class: '4B' },
      { id: 103, name: 'Ahmad Firdaus', class: '5A' },
      { id: 104, name: 'Nurul Huda', class: '5B' },
      { id: 105, name: 'Muhammad Amir', class: '4A' },
      { id: 106, name: 'Fatimah Zara', class: '4B' },
      { id: 107, name: 'Hakim Hassan', class: '5A' },
      { id: 108, name: 'Aina Sofea', class: '5B' },
      { id: 109, name: 'Danish Irfan', class: '4A' },
      { id: 110, name: 'Zara Qistina', class: '4B' },
      { id: 111, name: 'Ariff Danish', class: '5A' },
      { id: 112, name: 'Maisarah', class: '5B' },
      { id: 113, name: 'Fikri Haikal', class: '4A' },
      { id: 114, name: 'Nur Aisyah', class: '4B' },
      { id: 115, name: 'Adam Rayyan', class: '5A' }
    ];

    // Generate highest score for each player
    const uniqueEntries = players.map(player => {
      // Different games have different score ranges
      const gameScores = {
        '1': { min: 1500, max: 5000 }, // Space Adventure - high scores
        '2': { min: 800, max: 2500 },  // Whack-a-Mole
        '3': { min: 500, max: 1500 },  // Memory Match
        '4': { min: 30, max: 100 }     // Maze Game
      };
      
      const range = gameScores[filters.game_id] || { min: 50, max: 100 };
      const score = Math.floor(Math.random() * (range.max - range.min + 1)) + range.min;
      
      // Recent date for top players, older for others
      const daysAgo = Math.floor(Math.random() * 30);
      
      return {
        id: player.id,
        user_id: player.id,
        username: player.name,
        class: player.class,
        score: score,
        timestamp: new Date(Date.now() - daysAgo * 24 * 60 * 60 * 1000).toISOString(),
        game_id: filters.game_id
      };
    });

    // Sort by score (highest first)
    const sortedEntries = uniqueEntries.sort((a, b) => b.score - a.score);
    
    // Add rank numbers
    return sortedEntries.map((entry, idx) => ({
      ...entry,
      rank: idx + 1
    }));
  };

  // ✅ Fetch leaderboard or use mock data
  const loadLeaderboard = async () => {
    setLoading(true);
    setError(null);
    
    try {
      // Try real API first
      const query = new URLSearchParams(filters).toString();
      const res = await fetch(`/api/leaderboard?${query}`);
      
      if (!res.ok) {
        // If API fails, use mock data
        console.warn('API gagal, menggunakan data contoh...');
        const mockData = generateUniqueMockData();
        
        // Ensure current user is included
        const userInData = mockData.find(e => parseInt(e.user_id) === user.id);
        if (user.id > 0 && !userInData) {
          const range = filters.game_id === '4' ? { min: 40, max: 80 } : { min: 100, max: 500 };
          const userScore = Math.floor(Math.random() * (range.max - range.min + 1)) + range.min;
          const userEntry = {
            id: user.id,
            user_id: user.id,
            username: user.name || 'Anda',
            class: user.class || '4A',
            score: userScore,
            rank: Math.floor(Math.random() * 10) + 1, // Random rank 1-10
            timestamp: new Date().toISOString(),
            game_id: filters.game_id
          };
          mockData.push(userEntry);
          // Re-sort with user
          mockData.sort((a, b) => b.score - a.score).forEach((entry, idx) => {
            entry.rank = idx + 1;
          });
        }
        
        setEntries(mockData);
      } else {
        const data = await res.json();
        
        // Ensure API data has unique players (highest score only)
        if (data && data.length > 0) {
          // Group by user_id and keep highest score
          const uniqueData = Object.values(
            data.reduce((acc, entry) => {
              const userId = entry.user_id || entry.userId;
              if (!acc[userId] || entry.score > acc[userId].score) {
                acc[userId] = entry;
              }
              return acc;
            }, {})
          );
          
          // Sort and rank
          const sorted = uniqueData.sort((a, b) => b.score - a.score);
          const rankedData = sorted.map((entry, idx) => ({
            ...entry,
            rank: idx + 1
          }));
          
          setEntries(rankedData);
        } else {
          setEntries(data || []);
        }
      }
    } catch (err) {
      console.warn('Ralat API, menggunakan data contoh:', err.message);
      // Use mock data on error
      const mockData = generateUniqueMockData();
      setEntries(mockData);
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

  // ✅ Reset leaderboard (mock for now)
  const handleReset = async () => {
    if (
      !window.confirm(
        '⚠️ Set semula kedudukan untuk penapis terpilih?\nTindakan ini tidak boleh dibatalkan.'
      )
    )
      return;

    try {
      // For demo, just reload with empty data
      alert('✅ Fungsi set semula dalam mod demo. Untuk produksi, sambungkan ke API backend.');
      loadLeaderboard();
    } catch (err) {
      alert('❌ ' + err.message);
    }
  };

  // Find current user's entry
  const myEntry = entries.find((e) => parseInt(e.user_id) === user.id);
  const myRank = myEntry ? myEntry.rank : null;

  // Get current game name
  const currentGame = gameOptions.find(g => g.id === filters.game_id) || gameOptions[3];

  // Calculate statistics
  const totalPlayers = entries.length;
  const averageScore = entries.length > 0 
    ? Math.round(entries.reduce((sum, entry) => sum + entry.score, 0) / entries.length)
    : 0;
  const topScore = entries.length > 0 ? entries[0].score : 0;

  return (
    <div style={styles.container}>
      <div style={styles.header}>
        <div>
          <h2 style={styles.title}>🏆 Kedudukan Pemain</h2>
          <p style={{ color: '#64748B', fontSize: '0.9rem', marginTop: '4px' }}>
            Permainan: <strong>{currentGame.name}</strong>
          </p>
        </div>
        {onClose && (
          <button onClick={onClose} style={styles.closeBtn}>
            ×
          </button>
        )}
      </div>

      {/* Quick Stats */}
      {entries.length > 0 && (
        <div style={styles.statsBar}>
          <div style={styles.statItem}>
            <div style={styles.statLabel}>Jumlah Pemain</div>
            <div style={styles.statValue}>{totalPlayers}</div>
          </div>
          <div style={styles.statItem}>
            <div style={styles.statLabel}>Purata Markah</div>
            <div style={styles.statValue}>{averageScore}</div>
          </div>
          <div style={styles.statItem}>
            <div style={styles.statLabel}>Markah Tertinggi</div>
            <div style={{...styles.statValue, color: '#10B981'}}>{topScore}</div>
          </div>
        </div>
      )}

      {/* Filters */}
      <div style={styles.filters}>
        <select
          name="game_id"
          value={filters.game_id}
          onChange={handleFilterChange}
          style={styles.select}
        >
          {gameOptions.map(game => (
            <option key={game.id} value={game.id}>{game.name}</option>
          ))}
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

      {/* Demo Mode Notice */}
      <div style={styles.demoNotice}>
        <span style={{ fontWeight: '600', color: '#F59E0B' }}>💡 Mod Demo:</span> Setiap pemain muncul sekali sahaja dengan markah tertinggi mereka.
      </div>

      {/* Self Highlight */}
      {myRank && (
        <div style={styles.myRank}>
          🎯 Anda berada di kedudukan <strong>#{myRank}</strong> dengan <strong>{myEntry.score}</strong> markah!
          {myRank <= 3 && <span style={{ marginLeft: '10px', color: '#F59E0B' }}>🏅</span>}
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
          <button onClick={loadLeaderboard} style={styles.retryBtn}>
            Muatkan Data Contoh
          </button>
        </div>
      ) : (
        <div style={styles.tableWrap}>
          <div style={{ 
            display: 'flex', 
            justifyContent: 'space-between', 
            alignItems: 'center',
            marginBottom: '12px',
            padding: '0 8px'
          }}>
            <div style={{ fontSize: '0.9rem', color: '#64748B', fontWeight: '600' }}>
              Menunjukkan {entries.length} pemain unik
            </div>
            <div style={{ fontSize: '0.85rem', color: '#94A3B8' }}>
              Kemaskini: {new Date().toLocaleTimeString('ms-MY', { hour: '2-digit', minute: '2-digit' })}
            </div>
          </div>
          
          <table style={styles.table}>
            <thead>
              <tr>
                <th style={styles.th}>#</th>
                <th style={styles.th}>Nama</th>
                <th style={styles.th}>Kelas</th>
                <th style={styles.th}>Markah</th>
                <th style={styles.th}>Tarikh Terakhir</th>
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
                  <td style={styles.td}>
                    <span style={{
                      display: 'inline-block',
                      width: '28px',
                      height: '28px',
                      borderRadius: '50%',
                      background: entry.rank === 1 ? 'linear-gradient(135deg, #FFD700, #FFA500)' : 
                                 entry.rank === 2 ? 'linear-gradient(135deg, #C0C0C0, #A0A0A0)' : 
                                 entry.rank === 3 ? 'linear-gradient(135deg, #CD7F32, #A0522D)' : '#F1F5F9',
                      color: entry.rank <= 3 ? 'white' : '#334155',
                      textAlign: 'center',
                      lineHeight: '28px',
                      fontWeight: 'bold',
                      fontSize: '0.85rem',
                      boxShadow: entry.rank <= 3 ? '0 2px 4px rgba(0,0,0,0.2)' : 'none'
                    }}>
                      {entry.rank}
                    </span>
                  </td>
                  <td style={{...styles.td, fontWeight: parseInt(entry.user_id) === user.id ? '700' : '500'}}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                      {entry.rank === 1 && '👑 '}
                      {entry.username}
                      {parseInt(entry.user_id) === user.id && 
                        <span style={{
                          fontSize: '0.75rem',
                          backgroundColor: '#4ECDC4',
                          color: 'white',
                          padding: '2px 6px',
                          borderRadius: '4px',
                          marginLeft: '6px'
                        }}>
                          Anda
                        </span>
                      }
                    </div>
                  </td>
                  <td style={styles.td}>{entry.class}</td>
                  <td style={{ 
                    ...styles.td, 
                    fontWeight: 'bold', 
                    color: entry.score >= 90 ? '#10B981' : 
                           entry.score >= 70 ? '#F59E0B' : 
                           entry.score >= 50 ? '#3B82F6' : '#EF4444',
                    fontSize: entry.score >= 90 ? '1.1rem' : '1rem'
                  }}>
                    {entry.score}
                    {entry.score >= 90 && <span style={{ marginLeft: '4px', fontSize: '0.8rem' }}>🔥</span>}
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
        <span style={{ color: '#4a3a96', fontWeight: '600' }}>💡 Info:</span> Setiap pemain muncul sekali dengan markah tertinggi • 👑 Juara • 🔥 Markah cemerlang
      </div>
    </div>
  );
};

const styles = {
  container: {
    maxWidth: '900px',
    margin: '0 auto',
    backgroundColor: '#f9f7fe',
    borderRadius: '16px',
    fontFamily: '"Segoe UI", system-ui, sans-serif',
    padding: '0 0 20px 0',
    boxShadow: '0 4px 20px rgba(0,0,0,0.08)',
  },
  header: {
    display: 'flex',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: '1rem',
    padding: '1.5rem 1.5rem 1rem 1.5rem',
    borderBottom: '1px solid #E2E8F0',
  },
  title: {
    margin: 0,
    color: '#4a3a96',
    fontWeight: '700',
    fontSize: '1.8rem',
  },
  statsBar: {
    display: 'flex',
    justifyContent: 'space-around',
    padding: '1rem 1.5rem',
    backgroundColor: '#FFFFFF',
    margin: '0 1.5rem 1rem 1.5rem',
    borderRadius: '12px',
    border: '1px solid #E2E8F0',
    boxShadow: '0 2px 8px rgba(0,0,0,0.04)',
  },
  statItem: {
    textAlign: 'center',
  },
  statLabel: {
    fontSize: '0.8rem',
    color: '#64748B',
    fontWeight: '600',
    marginBottom: '4px',
  },
  statValue: {
    fontSize: '1.5rem',
    fontWeight: '700',
    color: '#4a3a96',
  },
  closeBtn: {
    background: 'none',
    border: 'none',
    fontSize: '2rem',
    cursor: 'pointer',
    color: '#94A3B8',
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
    padding: '1.2rem 1.5rem',
    backgroundColor: '#fff',
    marginBottom: '1rem',
    borderTop: '1px solid #E2E8F0',
    borderBottom: '1px solid #E2E8F0',
  },
  select: {
    padding: '0.6rem 1rem',
    borderRadius: '8px',
    border: '1px solid #ddd',
    fontSize: '1rem',
    backgroundColor: 'white',
    minWidth: '180px',
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
  demoNotice: {
    backgroundColor: '#FFFBEB',
    padding: '0.8rem 1.5rem',
    borderRadius: '8px',
    margin: '0 1.5rem 1rem 1.5rem',
    borderLeft: '4px solid #F59E0B',
    fontSize: '0.9rem',
    color: '#92400E',
  },
  myRank: {
    backgroundColor: '#E6F7FF',
    padding: '1rem 1.5rem',
    borderRadius: '12px',
    textAlign: 'center',
    fontWeight: '600',
    color: '#2C6ED5',
    margin: '0 1.5rem 1.2rem 1.5rem',
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
    margin: '0 1.5rem 1.5rem 1.5rem',
    boxShadow: '0 2px 12px rgba(0,0,0,0.08)',
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