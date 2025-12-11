import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import Header from './components/common/Header';
import GameList from './components/games/GameList';
import SpaceAdventure from './components/games/SpaceAdventure';
import WhackAMole from './components/games/WhackAMole'; 
import MemoryGame from './components/games/MemoryGame';
import MazeGame from './components/games/MazeGame';
import './App.css';

function App() {
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
          marginLeft: '280px', // Sidebar width
          padding: '20px',
          minHeight: '100vh',
          backgroundColor: 'transparent', // Changed from var(--background)
          color: 'var(--text-secondary)',
          width: 'calc(100% - 280px)'
        }}>
          <Routes>
            {/* Home/Dashboard - Shows GameList */}
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
                    Welcome to <span style={{ color: '#1D5DCD' }}>Edu</span>
                    <span style={{ color: '#E63946' }}>Spark</span>! 🚀
                  </h1>
                  <p style={{ fontSize: '1.1rem', opacity: 0.9, color: 'var(--text-secondary)' }}>
                    Start your learning journey with interactive educational games
                  </p>
                </div>
                <h2 style={{ color: 'var(--text-primary)', marginBottom: '20px', textShadow: '0 0 6px var(--accent-glow)' }}>Featured Games</h2>
                <GameList />
              </div>
            } />
            
            {/* Games Page - Also shows GameList */}
            <Route path="/games" element={
              <div>
                <h1 style={{ color: 'var(--text-primary)', marginBottom: '30px', fontSize: '2.5rem', textShadow: '0 0 8px var(--accent-glow)' }}>
                  🎮 All Games
                </h1>
                <GameList />
              </div>
            } />
            
            {/* Individual Game Routes */}
            <Route path="/games/SpaceAdventure" element={<SpaceAdventure />} />
            <Route path="/games/WhackAMole" element={<WhackAMole />} />
            <Route path="/games/MemoryGame" element={<MemoryGame />} />
            <Route path="/games/MazeGame" element={<MazeGame />} />
            
            {/* Placeholder Pages */}
            <Route path="/materials" element={
              <div>
                <h1 style={{ color: 'var(--text-primary)', textShadow: '0 0 6px var(--accent-glow)' }}>📚 Learning Materials</h1>
                <p style={{ color: 'var(--text-secondary)' }}>Study materials coming soon...</p>
              </div>
            } />
            
            <Route path="/forum" element={
              <div>
                <h1 style={{ color: 'var(--text-primary)', textShadow: '0 0 6px var(--accent-glow)' }}>💬 Community Forum</h1>
                <p style={{ color: 'var(--text-secondary)' }}>Forum coming soon...</p>
              </div>
            } />
            
            <Route path="/assessments" element={
              <div>
                <h1 style={{ color: 'var(--text-primary)', textShadow: '0 0 6px var(--accent-glow)' }}>📝 Assessments</h1>
                <p style={{ color: 'var(--text-secondary)' }}>Assessments coming soon...</p>
              </div>
            } />
          </Routes>
        </main>
      </div>
    </Router>
  );
}

export default App;