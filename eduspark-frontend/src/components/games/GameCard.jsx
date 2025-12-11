import React from 'react';
import { useNavigate } from 'react-router-dom';

const GameCard = ({ game }) => {
  const navigate = useNavigate();

  const handlePlayGame = () => {
    const routeMap = {
      'space-adventure': '/games/SpaceAdventure',
      'whack-mole': '/games/WhackAMole',
      'memory-game': '/games/MemoryGame',
      'maze-game': '/games/MazeGame'
    };
    navigate(routeMap[game.slug] || `/games/${game.slug}`);
  };

  const getDifficultyColor = (difficulty) => {
    switch (difficulty) {
      case 'easy': return '#2A9D8F'; // success green
      case 'medium': return '#F4C430'; // yellow
      case 'hard': return '#E63946'; // danger red
      default: return 'var(--muted)';
    }
  };

  const getTopicIcon = (topic) => {
    switch (topic) {
      case 'action': return '⚡';
      case 'casual': return '🎯';
      case 'puzzle': return '🧩';
      case 'education': return '📚';
      default: return '🎮';
    }
  };

  return (
    <div className="card fade-in-up" style={{
      padding: '24px',
      display: 'flex',
      flexDirection: 'column',
      position: 'relative',
      overflow: 'hidden'
    }}>
      {/* Game Header */}
      <div style={{ 
        display: 'flex', 
        justifyContent: 'space-between', 
        alignItems: 'center',
        marginBottom: '16px'
      }}>
        <div style={{ display: 'flex', alignItems: 'center', gap: '12px' }}>
          <div style={{
            width: '48px',
            height: '48px',
            borderRadius: '12px',
            background: 'linear-gradient(135deg, rgba(106,77,247,0.15), rgba(156,123,255,0.08))',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
            fontSize: '24px'
          }}>
            {getTopicIcon(game.topic)}
          </div>
          <div>
            <h3 style={{ 
              margin: 0, 
              fontWeight: '700', 
              fontSize: '18px',
              marginBottom: '4px'
            }}>
              {game.name}
            </h3>
            <div style={{ 
              fontSize: '13px', 
              color: 'var(--muted)',
              display: 'flex',
              alignItems: 'center',
              gap: '8px'
            }}>
              <span>{game.topic}</span>
              <span style={{
                display: 'inline-block',
                width: '4px',
                height: '4px',
                borderRadius: '50%',
                background: 'var(--muted)'
              }}></span>
              <span style={{
                color: getDifficultyColor(game.difficulty),
                fontWeight: '600'
              }}>
                {game.difficulty}
              </span>
            </div>
          </div>
        </div>
      </div>

      {/* Game Description */}
      <p style={{ 
        color: 'var(--muted)', 
        fontSize: '14px', 
        lineHeight: '1.6',
        marginBottom: '20px',
        flex: 1
      }}>
        {game.description}
      </p>

      {/* Game Footer */}
      <div style={{ 
        display: 'flex', 
        justifyContent: 'space-between', 
        alignItems: 'center',
        marginTop: 'auto'
      }}>
        <div style={{ display: 'flex', gap: '8px' }}>
          <span style={{
            padding: '6px 12px',
            borderRadius: '20px',
            fontSize: '12px',
            fontWeight: '600',
            background: 'rgba(106,77,247,0.1)',
            color: 'var(--accent)'
          }}>
            {game.game_type}
          </span>
          {game.score > 0 && (
            <span style={{
              padding: '6px 12px',
              borderRadius: '20px',
              fontSize: '12px',
              fontWeight: '600',
              background: 'rgba(42,157,143,0.1)',
              color: 'var(--success)'
            }}>
              Score: {game.score}
            </span>
          )}
        </div>
        
        <button
          onClick={handlePlayGame}
          className="btn-accent"
          style={{
            padding: '10px 24px',
            borderRadius: '12px',
            border: 'none',
            background: 'linear-gradient(90deg, var(--accent), var(--accent-2))',
            color: 'white',
            fontWeight: '600',
            cursor: 'pointer',
            fontSize: '14px',
            transition: 'all 0.2s ease'
          }}
          onMouseOver={(e) => {
            e.target.style.transform = 'translateY(-2px)';
            e.target.style.boxShadow = '0 10px 20px rgba(106,77,247,0.3)';
          }}
          onMouseOut={(e) => {
            e.target.style.transform = 'none';
            e.target.style.boxShadow = 'none';
          }}
        >
          Play Game →
        </button>
      </div>

      {/* Decorative Element */}
      <div style={{
        position: 'absolute',
        top: '0',
        right: '0',
        width: '60px',
        height: '60px',
        background: 'linear-gradient(135deg, rgba(106,77,247,0.05), rgba(156,123,255,0.02))',
        borderRadius: '0 14px 0 60px',
        zIndex: 0
      }}></div>
    </div>
  );
};

export default GameCard;