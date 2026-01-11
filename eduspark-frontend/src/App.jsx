import React, { useState, useEffect } from 'react';
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import Header from './components/common/Header';
import GameList from './components/games/GameList';
import SpaceAdventure from './components/games/SpaceAdventure';
import WhackAMole from './components/games/WhackAMole'; 
import MemoryGame from './components/games/MemoryGame';
import MazeGame from './components/games/MazeGame';
import TeacherGames from './pages/teacher/TeacherGames';
import './App.css';

function App() {
  const [userRole, setUserRole] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    // Get user role from localStorage or API
    const role = localStorage.getItem('user_role') || 'student';
    setUserRole(role);
    setLoading(false);
  }, []);

  if (loading) return <div>Loading...</div>;
  return (
    <Router>
      <div className="App" style={{
        display: 'flex',
        minHeight: '100vh',
        width: '100%',
        margin: 0,
        padding: 0
      }}>
        <Header />
        <main style={{
          flex: 1,
          marginLeft: '280px', // Lebar sidebar
          padding: '20px',
          minHeight: '100vh',
          backgroundColor: 'transparent',
          color: 'var(--text-secondary)',
          width: 'calc(100% - 280px)'
        }}>
          <Routes>
            {/* Teacher Routes */}
            {userRole === 'teacher' && (
              <>
                <Route path="/teacher/games" element={<TeacherGames />} />
                <Route path="/" element={<Navigate to="/teacher/games" />} />
                <Route path="/games" element={<Navigate to="/teacher/games" />} />
              </>
            )}

            {/* Student Routes */}
            {userRole === 'student' && (
              <>
                {/* Laman Utama/Papan Pemuka - Papar Senarai Permainan */}
                <Route path="/" element={
                  <div>
                    <div style={{
                      background: 'linear-gradient(135deg, rgba(29, 93, 205, 0.1), rgba(230, 57, 70, 0.1))',
                      borderRadius: '16px',
                      padding: '30px',
                      marginBottom: '30px',
                      border: '1px solid rgba(255,255,255,0.1)'
                    }}>
                      <h1 style={{ 
                        color: 'var(--text-primary)', 
                        marginBottom: '10px',
                        fontSize: '2.5rem',
                        textShadow: '0 0 8px var(--accent-glow)'
                      }}>
                        Selamat Datang ke <span style={{ color: '#1D5DCD' }}>Edu</span>
                        <span style={{ color: '#E63946' }}>Spark</span>! 🚀
                      </h1>
                      <p style={{ fontSize: '1.1rem', opacity: 0.9, color: 'var(--text-secondary)' }}>
                        Mulakan perjalanan pembelajaran anda dengan permainan pendidikan interaktif
                      </p>
                    </div>
                    <h2 style={{ color: 'var(--text-primary)', marginBottom: '20px', textShadow: '0 0 6px var(--accent-glow)' }}>
                      🎮 Permainan Pilihan
                    </h2>
                    <GameList />
                  </div>
                } />
                
                {/* Laman Permainan - Juga paparkan Senarai Permainan */}
                <Route path="/games" element={
                  <div>
                    <h1 style={{ color: 'var(--text-primary)', marginBottom: '30px', fontSize: '2.5rem', textShadow: '0 0 8px var(--accent-glow)' }}>
                      🎮 Semua Permainan
                    </h1>
                    <GameList />
                  </div>
                } />
                
                {/* KOREKSI: Kembalikan laluan asal untuk permainan */}
                <Route path="/games/SpaceAdventure" element={<SpaceAdventure />} />
                <Route path="/games/WhackAMole" element={<WhackAMole />} />
                <Route path="/games/MemoryGame" element={<MemoryGame />} />
                <Route path="/games/MazeGame" element={<MazeGame />} />
              </>
            )}
            
            {/* Laman Sementara */}
            <Route path="/materials" element={
              <div style={{ padding: '40px', textAlign: 'center' }}>
                <h1 style={{ color: 'var(--text-primary)', textShadow: '0 0 6px var(--accent-glow)', marginBottom: '20px' }}>
                  📚 Bahan Pembelajaran
                </h1>
                <div style={{
                  background: 'rgba(255,255,255,0.05)',
                  padding: '40px',
                  borderRadius: '16px',
                  border: '1px solid rgba(255,255,255,0.1)'
                }}>
                  <p style={{ color: 'var(--text-secondary)', fontSize: '1.2rem' }}>
                    Bahan pembelajaran akan tiba tidak lama lagi...
                  </p>
                </div>
              </div>
            } />
            
            <Route path="/forum" element={
              <div style={{ padding: '40px', textAlign: 'center' }}>
                <h1 style={{ color: 'var(--text-primary)', textShadow: '0 0 6px var(--accent-glow)', marginBottom: '20px' }}>
                  💬 Forum Komuniti
                </h1>
                <div style={{
                  background: 'rgba(255,255,255,0.05)',
                  padding: '40px',
                  borderRadius: '16px',
                  border: '1px solid rgba(255,255,255,0.1)'
                }}>
                  <p style={{ color: 'var(--text-secondary)', fontSize: '1.2rem' }}>
                    Forum akan tiba tidak lama lagi...
                  </p>
                </div>
              </div>
            } />
            
            <Route path="/assessments" element={
              <div style={{ padding: '40px', textAlign: 'center' }}>
                <h1 style={{ color: 'var(--text-primary)', textShadow: '0 0 6px var(--accent-glow)', marginBottom: '20px' }}>
                  📝 Penilaian
                </h1>
                <div style={{
                  background: 'rgba(255,255,255,0.05)',
                  padding: '40px',
                  borderRadius: '16px',
                  border: '1px solid rgba(255,255,255,0.1)'
                }}>
                  <p style={{ color: 'var(--text-secondary)', fontSize: '1.2rem' }}>
                    Penilaian akan tiba tidak lama lagi...
                  </p>
                </div>
              </div>
            } />
          </Routes>
        </main>
      </div>
    </Router>
  );
}

export default App;