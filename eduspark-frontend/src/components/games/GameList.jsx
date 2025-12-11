import React, { useState, useEffect } from 'react';
import { gameService } from "/src/services/api";
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
    // You can load actual stats from your API later
    setStats({
      totalGames: 4,
      completedGames: 0,
      averageScore: 0
    });
  }, []);

  const loadGames = async () => {
    try {
      // If you have backend API
      // const response = await gameService.getAllGames();
      // setGames(response.data);
      
      // For now, use hardcoded games matching your teammate's style
      const sampleGames = [
        {
          id: 1,
          name: '🚀 Cosmic Defender',
          slug: 'space-adventure',
          description: 'Epic space battle with power-ups!',
          topic: 'action',
          game_type: 'arcade',
          difficulty: 'medium',
          score: 0
        },
        {
          id: 2,
          name: '🎯 Whack-a-Mole',
          slug: 'whack-mole',
          description: 'Whack the moles for high score!',
          topic: 'casual',
          game_type: 'arcade',
          difficulty: 'easy',
          score: 0
        },
        {
          id: 3,
          name: '🎴 Memory Match',
          slug: 'memory-game',
          description: 'Test your memory skills!',
          topic: 'puzzle',
          game_type: 'memory',
          difficulty: 'easy',
          score: 0
        },
        {
          id: 4,
          name: '🌿 Garden Maze',
          slug: 'maze-game',
          description: 'Navigate maze & answer CS questions!',
          topic: 'education',
          game_type: 'adventure',
          difficulty: 'medium',
          score: 0
        }
      ];
      setGames(sampleGames);
    } catch (error) {
      console.error('Error loading games:', error);
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
        <div className="label" style={{ color: 'var(--muted)' }}>Loading games...</div>
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
            Games
          </div>
          <div className="sub" style={{ 
            color: 'var(--muted)', 
            fontSize: '14px' 
          }}>
            Interactive learning games for SPM Computer Science
          </div>
        </div>
      </div>

      {/* Stats Cards */}
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
            Total Games
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
            Completed
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
            Avg Score
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

      {/* Games Grid */}
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