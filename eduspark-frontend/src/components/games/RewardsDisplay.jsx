import React, { useState } from 'react';
import { progressService } from '../../services/progressService';

const RewardsDisplay = ({ rewards, onClaim }) => {
  const [claimed, setClaimed] = useState([]);

  const handleClaim = async (reward) => {
    try {
      // FIX: Check if claimReward method exists before calling
      if (progressService && typeof progressService.claimReward === 'function') {
        await progressService.claimReward(reward.id);
      } else {
        console.warn('progressService.claimReward is not available');
        // Fallback: just mark as claimed locally
      }
      
      setClaimed([...claimed, reward.id]);
      
      // Call the parent callback if provided
      if (onClaim && typeof onClaim === 'function') {
        onClaim(reward);
      }
    } catch (error) {
      console.error('Failed to claim reward:', error);
    }
  };

  // FIX: Return null if no rewards or rewards is not an array
  if (!rewards || !Array.isArray(rewards) || rewards.length === 0) {
    return null;
  }

  return (
    <div className="card fade-in-up" style={{ 
      padding: '25px',
      marginTop: '20px',
      background: 'linear-gradient(135deg, rgba(244,196,48,0.1), rgba(106,77,247,0.05))',
      border: '1px solid rgba(244,196,48,0.2)'
    }}>
      <h3 style={{ 
        marginBottom: '20px', 
        color: 'var(--yellow)',
        display: 'flex',
        alignItems: 'center',
        gap: '10px'
      }}>
        🎁 Rewards Earned!
      </h3>
      
      <div style={{ 
        display: 'grid', 
        gridTemplateColumns: 'repeat(auto-fit, minmax(180px, 1fr))', 
        gap: '20px' 
      }}>
        {/* FIX: Added proper key prop using reward.id or index as fallback */}
        {rewards.map((reward, index) => (
          <div 
            key={reward.id || `reward-${index}`} 
            className="card" 
            style={{ 
              padding: '20px',
              textAlign: 'center',
              background: claimed.includes(reward.id) 
                ? 'rgba(42,157,143,0.1)' 
                : 'rgba(255,255,255,0.05)',
              border: claimed.includes(reward.id) 
                ? '2px solid var(--success)' 
                : '1px solid rgba(106,77,247,0.2)',
              position: 'relative',
              overflow: 'hidden'
            }}
          >
            {/* Shiny effect for unclaimed rewards */}
            {!claimed.includes(reward.id) && (
              <div style={{
                position: 'absolute',
                top: 0,
                left: 0,
                right: 0,
                height: '3px',
                background: 'linear-gradient(90deg, var(--yellow), var(--accent))',
                animation: 'shine 2s infinite'
              }}></div>
            )}
            
            <div style={{ 
              fontSize: '3rem', 
              marginBottom: '15px',
              filter: claimed.includes(reward.id) ? 'grayscale(0.3)' : 'none'
            }}>
              {reward.icon || '🏆'}
            </div>
            
            <h4 style={{ 
              margin: '10px 0', 
              fontSize: '16px',
              fontWeight: 'bold',
              color: claimed.includes(reward.id) ? 'var(--success)' : 'var(--accent)'
            }}>
              {reward.name || `Reward ${index + 1}`}
            </h4>
            
            <p style={{ 
              fontSize: '13px', 
              color: 'var(--muted)', 
              marginBottom: '15px',
              lineHeight: '1.4'
            }}>
              {reward.description || 'Congratulations on earning this reward!'}
            </p>
            
            {!claimed.includes(reward.id) ? (
              <button
                onClick={() => handleClaim(reward)}
                style={{
                  padding: '10px 20px',
                  background: 'linear-gradient(90deg, var(--yellow), var(--accent))',
                  color: 'white',
                  border: 'none',
                  borderRadius: '25px',
                  cursor: 'pointer',
                  fontSize: '13px',
                  fontWeight: 'bold',
                  transition: 'all 0.3s ease',
                  boxShadow: '0 4px 15px rgba(244,196,48,0.3)'
                }}
                onMouseOver={(e) => {
                  e.target.style.transform = 'translateY(-2px)';
                  e.target.style.boxShadow = '0 6px 20px rgba(244,196,48,0.4)';
                }}
                onMouseOut={(e) => {
                  e.target.style.transform = 'none';
                  e.target.style.boxShadow = '0 4px 15px rgba(244,196,48,0.3)';
                }}
              >
                Claim Reward
              </button>
            ) : (
              <div style={{ 
                display: 'flex', 
                alignItems: 'center', 
                justifyContent: 'center',
                gap: '8px'
              }}>
                <span style={{ 
                  color: 'var(--success)', 
                  fontSize: '13px',
                  fontWeight: 'bold'
                }}>
                  ✓ Claimed
                </span>
              </div>
            )}
          </div>
        ))}
      </div>

      <style>{`
        @keyframes shine {
          0% { transform: translateX(-100%); }
          100% { transform: translateX(100%); }
        }
      `}</style>
    </div>
  );
};

export default RewardsDisplay;