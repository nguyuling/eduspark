import React, { useState, useEffect } from 'react';
import { gameService } from '../../services/api'; 
import GameCard from './GameCard';

const GameList = () => {
  const [games, setGames] = useState([]);
  const [loading, setLoading] = useState(true);
  const [stats, setStats] = useState({
    totalGames: 0,
    completedGames: 0,
    averageScore: 0
  });

  useEffect(() => {
    loadGames();
    // Anda boleh muat stat sebenar dari API kemudian
    setStats({
      totalGames: 4,
      completedGames: 0,
      averageScore: 0
    });
  }, []);

  const loadGames = async () => {
    try {
      // Jika anda mempunyai API backend
      // const response = await gameService.getAllGames();
      // setGames(response.data);
      
      // Untuk sekarang, gunakan permainan contoh yang sepadan dengan gaya rakan sepasukan anda
      const sampleGames = [
        {
          id: 1,
          name: '🚀 Pertahanan Kosmik',
          slug: 'space-adventure',
          description: 'Pertempuran angkasa epik dengan kuasa tambahan!',
          topic: 'aksi',
          game_type: 'arcade',
          difficulty: 'sederhana',
          score: 0
        },
        {
          id: 2,
          name: '🎯 Tumbuk Tikus',
          slug: 'whack-mole',
          description: 'Tumbuk tikus untuk markah tinggi!',
          topic: 'santai',
          game_type: 'arcade',
          difficulty: 'mudah',
          score: 0
        },
        {
          id: 3,
          name: '🎴 Padanan Ingatan',
          slug: 'memory-game',
          description: 'Uji kemahiran ingatan anda!',
          topic: 'teka-teki',
          game_type: 'memory',
          difficulty: 'mudah',
          score: 0
        },
        {
          id: 4,
          name: '🌿 Labyrinth Java',
          slug: 'maze-game',
          description: 'Navigasi labirin & jawab soalan pengaturcaraan Java!',
          topic: 'pendidikan',
          game_type: 'pengembaraan',
          difficulty: 'sederhana',
          score: 0
        }
      ];
      setGames(sampleGames);
    } catch (error) {
      console.error('Ralat memuatkan permainan:', error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="panel card fade-in-up" style={{ 
        padding: '40px', 
        textAlign: 'center',
        marginLeft: '280px',
        marginRight: '20px',
        marginTop: '20px'
      }}>
        <div className="label" style={{ color: 'var(--muted)' }}>Memuatkan permainan...</div>
      </div>
    );
  }

  return (
    <div className="main-content" style={{ 
      marginLeft: '280px', 
      padding: '28px',
      animation: 'fadeInUp 0.5s ease'
    }}>
      {/* Header */}
      <div className="header" style={{
        display: 'flex',
        justifyContent: 'space-between',
        alignItems: 'center',
        marginBottom: '28px'
      }}>
        <div>
          <div className="title" style={{ 
            fontWeight: '700', 
            fontSize: '24px',
            marginBottom: '4px'
          }}>
            Permainan
          </div>
          <div className="sub" style={{ 
            color: 'var(--muted)', 
            fontSize: '14px' 
          }}>
            Permainan pembelajaran interaktif untuk Sains Komputer SPM
          </div>
        </div>
      </div>

      {/* Kad Statistik */}
      <div className="cards" style={{
        display: 'grid',
        gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))',
        gap: '16px',
        marginBottom: '28px'
      }}>
        <div className="card" style={{ padding: '20px' }}>
          <div className="label" style={{ 
            fontSize: '13px', 
            color: 'var(--muted)', 
            fontWeight: '600' 
          }}>
            Jumlah Permainan
          </div>
          <div className="value" style={{ 
            fontWeight: '700', 
            fontSize: '28px', 
            marginTop: '8px' 
          }}>
            <span className="badge-pill" style={{
              background: 'linear-gradient(90deg, var(--accent), var(--accent-2))'
            }}>
              {stats.totalGames}
            </span>
          </div>
        </div>

        <div className="card" style={{ padding: '20px' }}>
          <div className="label" style={{ 
            fontSize: '13px', 
            color: 'var(--muted)', 
            fontWeight: '600' 
          }}>
            Selesai
          </div>
          <div className="value" style={{ 
            fontWeight: '700', 
            fontSize: '28px', 
            marginTop: '8px' 
          }}>
            <span className="badge-pill" style={{
              background: 'linear-gradient(90deg, var(--success), #4ECDC4)'
            }}>
              {stats.completedGames}
            </span>
          </div>
        </div>

        <div className="card" style={{ padding: '20px' }}>
          <div className="label" style={{ 
            fontSize: '13px', 
            color: 'var(--muted)', 
            fontWeight: '600' 
          }}>
            Purata Markah
          </div>
          <div className="value" style={{ 
            fontWeight: '700', 
            fontSize: '28px', 
            marginTop: '8px' 
          }}>
            <span className="badge-pill" style={{
              background: 'linear-gradient(90deg, var(--yellow), var(--accent))'
            }}>
              {stats.averageScore}%
            </span>
          </div>
        </div>
      </div>

      {/* Grid Permainan */}
      <div style={{ 
        display: 'grid',
        gridTemplateColumns: 'repeat(auto-fill, minmax(300px, 1fr))',
        gap: '20px'
      }}>
        {games.map(game => (
          <GameCard key={game.id} game={game} />
        ))}
      </div>
    </div>
  );
};

export default GameList;