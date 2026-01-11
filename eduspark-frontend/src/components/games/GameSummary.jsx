import React from 'react';

const GameSummary = ({ progress, game }) => {
  if (!progress) return null;

  const calculateStars = () => {
    if (!progress.stars) {
      // Calculate stars based on score
      const score = progress.score || 0;
      if (score >= 80) return 3;
      if (score >= 50) return 2;
      return 1;
    }
    return progress.stars;
  };

  const stars = calculateStars();

  return (
    <div className="card fade-in-up" style={{ 
      padding: '25px',
      marginTop: '20px',
      background: 'linear-gradient(135deg, rgba(106,77,247,0.1), rgba(156,123,255,0.05))',
      border: '1px solid rgba(106,77,247,0.2)'
    }}>
      <h3 style={{ 
        marginBottom: '20px', 
        color: 'var(--accent)',
        display: 'flex',
        alignItems: 'center',
        gap: '10px'
      }}>
        📊 {game?.name || 'Game'} Summary
      </h3>
      
      <div style={{ 
        display: 'grid', 
        gridTemplateColumns: 'repeat(auto-fit, minmax(180px, 1fr))', 
        gap: '15px',
        marginBottom: '20px'
      }}>
        <div className="card" style={{ 
          padding: '15px',
          background: 'rgba(255,255,255,0.05)'
        }}>
          <div className="label" style={{ 
            fontSize: '13px', 
            color: 'var(--muted)',
            marginBottom: '5px'
          }}>
            Score
          </div>
          <div className="value" style={{ 
            fontSize: '28px', 
            fontWeight: 'bold', 
            color: 'var(--accent)',
            textShadow: '0 2px 4px rgba(106,77,247,0.3)'
          }}>
            {progress.score || 0}
          </div>
        </div>

        <div className="card" style={{ 
          padding: '15px',
          background: 'rgba(255,255,255,0.05)'
        }}>
          <div className="label" style={{ 
            fontSize: '13px', 
            color: 'var(--muted)',
            marginBottom: '5px'
          }}>
            Stars
          </div>
          <div className="value" style={{ 
            fontSize: '28px', 
            fontWeight: 'bold',
            color: '#FFD700'
          }}>
            {'⭐'.repeat(stars)}
          </div>
        </div>

        <div className="card" style={{ 
          padding: '15px',
          background: 'rgba(255,255,255,0.05)'
        }}>
          <div className="label" style={{ 
            fontSize: '13px', 
            color: 'var(--muted)',
            marginBottom: '5px'
          }}>
            Time
          </div>
          <div className="value" style={{ 
            fontSize: '24px', 
            fontWeight: 'bold', 
            color: 'var(--success)'
          }}>
            {Math.round((progress.time_spent || 0) / 60)}min
          </div>
        </div>

        <div className="card" style={{ 
          padding: '15px',
          background: 'rgba(255,255,255,0.05)'
        }}>
          <div className="label" style={{ 
            fontSize: '13px', 
            color: 'var(--muted)',
            marginBottom: '5px'
          }}>
            Attempts
          </div>
          <div className="value" style={{ 
            fontSize: '28px', 
            fontWeight: 'bold', 
            color: 'var(--yellow)'
          }}>
            {progress.attempts || 1}
          </div>
        </div>
      </div>

      {/* Performance Bar */}
      <div style={{ marginTop: '15px' }}>
        <div style={{ 
          display: 'flex', 
          justifyContent: 'space-between', 
          marginBottom: '8px' 
        }}>
          <span style={{ 
            fontSize: '14px', 
            color: 'var(--muted)',
            fontWeight: '600'
          }}>
            Performance
          </span>
          <span style={{ 
            fontSize: '14px', 
            fontWeight: 'bold',
            color: stars === 3 ? 'var(--success)' : 
                   stars === 2 ? 'var(--yellow)' : 'var(--danger)'
          }}>
            {stars === 3 ? 'Excellent' : 
             stars === 2 ? 'Good' : 'Keep Practicing'}
          </span>
        </div>
        <div style={{
          height: '12px',
          background: 'rgba(255,255,255,0.1)',
          borderRadius: '6px',
          overflow: 'hidden',
          marginBottom: '15px'
        }}>
          <div style={{
            height: '100%',
            width: `${(stars / 3) * 100}%`,
            background: stars === 3 ? 'linear-gradient(90deg, var(--success), #4ECDC4)' :
                       stars === 2 ? 'linear-gradient(90deg, var(--yellow), #FFD166)' :
                       'linear-gradient(90deg, var(--danger), #FF6B6B)',
            borderRadius: '6px',
            transition: 'width 0.8s ease'
          }}></div>
        </div>
      </div>

      {/* Completion Status */}
      {progress.completed && (
        <div style={{
          padding: '12px',
          background: 'rgba(42,157,143,0.1)',
          border: '1px solid rgba(42,157,143,0.3)',
          borderRadius: '8px',
          textAlign: 'center',
          marginTop: '10px'
        }}>
          <span style={{ 
            color: 'var(--success)', 
            fontWeight: 'bold',
            fontSize: '14px'
          }}>
            ✅ Game Completed Successfully!
          </span>
        </div>
      )}
    </div>
  );
};

export default GameSummary;