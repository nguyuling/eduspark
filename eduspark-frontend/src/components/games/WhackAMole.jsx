import React, { useState, useEffect, useCallback, useRef } from 'react';

const WhackAMole = () => {
  const [score, setScore] = useState(0);
  const [timeLeft, setTimeLeft] = useState(30);
  const [moles, setMoles] = useState(Array(9).fill(false));
  const [gameOver, setGameOver] = useState(false);
  const [currentMoles, setCurrentMoles] = useState([]); // Now support multiple moles
  const [gameSpeed, setGameSpeed] = useState(1200); // Starting speed in ms
  const [moleState, setMoleState] = useState({}); // Track state for each mole
  const [combo, setCombo] = useState(0);
  const [maxCombo, setMaxCombo] = useState(0);
  const [powerUp, setPowerUp] = useState(null); // Power-up types
  const [powerUpActive, setPowerUpActive] = useState(false);
  const [powerUpTimer, setPowerUpTimer] = useState(0);
  const [multiplier, setMultiplier] = useState(1); // Score multiplier
  const [bonusTime, setBonusTime] = useState(0); // Bonus time added
  const [showHammer, setShowHammer] = useState(false);
  const [hammerPosition, setHammerPosition] = useState({ x: 0, y: 0 });
  const [highScore, setHighScore] = useState(() => {
    return parseInt(localStorage.getItem('whackHighScore') || '0');
  });
  const [gameStarted, setGameStarted] = useState(false);
  
  const boardRef = useRef(null);

  // Select random mole holes to show moles
  const showRandomMoles = useCallback(() => {
    if (gameOver) return;
    
    // Clear current moles
    setCurrentMoles(prev => []);
    setMoleState({});
    
    // Determine how many moles to show based on difficulty
    const moleCount = Math.min(2 + Math.floor(score / 15), 4); // Increase mole count as score increases
    const holes = [];
    
    // Select random holes
    while (holes.length < moleCount) {
      const hole = Math.floor(Math.random() * 9);
      if (!holes.includes(hole)) {
        holes.push(hole);
      }
    }
    
    // Set the new moles
    setCurrentMoles(holes);
    
    // Set mole states
    const newState = {};
    holes.forEach(hole => {
      newState[hole] = 'popping';
    });
    setMoleState(newState);
    
    // Hide moles after a short time
    const hideTimeout = setTimeout(() => {
      setCurrentMoles([]);
      setMoleState({});
      setCombo(0);
    }, gameSpeed * 0.7);
    
    return () => clearTimeout(hideTimeout);
  }, [gameOver, gameSpeed, score]);

  // Game loop - show moles at intervals
  useEffect(() => {
    if (gameOver || !gameStarted) return;
    
    const moleInterval = setInterval(() => {
      showRandomMoles();
    }, gameSpeed);
    
    return () => clearInterval(moleInterval);
  }, [showRandomMoles, gameOver, gameSpeed, gameStarted]);

  // Timer countdown
  useEffect(() => {
    if (gameOver || timeLeft <= 0 || !gameStarted) return;
    
    const timer = setTimeout(() => {
      setTimeLeft(prev => {
        if (prev <= 1) {
          setGameOver(true);
          // Update high score if needed
          if (score > highScore) {
            setHighScore(score);
            localStorage.setItem('whackHighScore', score.toString());
          }
          return 0;
        }
        return prev - 1;
      });
    }, 1000);
    
    return () => clearTimeout(timer);
  }, [timeLeft, gameOver, bonusTime, score, highScore, gameStarted]);

  // Power-up generation
  useEffect(() => {
    if (gameOver || powerUpActive || !gameStarted) return;
    
    const powerUpInterval = setInterval(() => {
      if (Math.random() > 0.85) { // 15% chance to generate power-up
        const types = ['time', 'points', 'combo'];
        const randomType = types[Math.floor(Math.random() * types.length)];
        setPowerUp(randomType);
        
        // Hide power-up after 5 seconds
        setTimeout(() => {
          setPowerUp(null);
        }, 5000);
      }
    }, 10000); // Check every 10 seconds
    
    return () => clearInterval(powerUpInterval);
  }, [gameOver, powerUpActive, gameStarted]);

  // Power-up timer countdown
  useEffect(() => {
    if (powerUpActive && powerUpTimer > 0) {
      const timer = setTimeout(() => {
        setPowerUpTimer(prev => prev - 1);
      }, 1000);
      
      return () => clearTimeout(timer);
    } else if (powerUpActive && powerUpTimer <= 0) {
      // Deactivate power-up
      setPowerUpActive(false);
      setMultiplier(1);
    }
  }, [powerUpActive, powerUpTimer]);

  // Increase difficulty as score increases
  useEffect(() => {
    if (score > 0 && score % 10 === 0) {
      // Decrease game speed (make it faster) but not below 400ms
      setGameSpeed(prev => Math.max(400, prev - 50));
    }
  }, [score]);

  // Handle whacking a mole
  const whackMole = (index) => {
    if (gameOver || !currentMoles.includes(index)) return;
    
    // Calculate points
    let points = 10 * multiplier;
    if (powerUpActive && multiplier > 1) {
      points *= 1.5; // Bonus for active power-up
    }
    
    // Update score
    setScore(prev => prev + points);
    
    // Update combo
    const newCombo = combo + 1;
    setCombo(newCombo);
    if (newCombo > maxCombo) {
      setMaxCombo(newCombo);
    }
    
    // Update mole state
    setMoleState(prev => ({
      ...prev,
      [index]: 'whacked'
    }));
    
    // Remove the whacked mole
    setCurrentMoles(prev => prev.filter(hole => hole !== index));
    
    // Reset mole state after animation
    setTimeout(() => {
      setMoleState(prev => {
        const newState = { ...prev };
        delete newState[index];
        return newState;
      });
    }, 300);
  };

  // Handle hammer effect
  const handleWhack = (e, index) => {
    if (gameOver) return;
    
    // Calculate hammer position
    const rect = boardRef.current.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    
    setHammerPosition({ x, y });
    setShowHammer(true);
    
    // Hide hammer after animation
    setTimeout(() => {
      setShowHammer(false);
    }, 300);
    
    // Whack the mole
    whackMole(index);
  };

  // Handle power-up collection
  const collectPowerUp = () => {
    if (!powerUp) return;
    
    setPowerUpActive(true);
    setPowerUpTimer(10); // 10 seconds duration
    
    if (powerUp === 'time') {
      setTimeLeft(prev => prev + 5); // Add 5 seconds
      setBonusTime(5); // Visual indicator
      setTimeout(() => setBonusTime(0), 2000);
    } else if (powerUp === 'points') {
      setMultiplier(2); // Double points
    } else if (powerUp === 'combo') {
      setCombo(prev => prev + 5); // Add to combo
    }
    
    setPowerUp(null);
  };

  const resetGame = () => {
    setScore(0);
    setTimeLeft(30);
    setMoles(Array(9).fill(false));
    setGameOver(false);
    setCurrentMoles([]);
    setGameSpeed(1200);
    setMoleState({});
    setCombo(0);
    setMaxCombo(0);
    setPowerUp(null);
    setPowerUpActive(false);
    setPowerUpTimer(0);
    setMultiplier(1);
    setBonusTime(0);
  };

  const startGame = () => {
    setGameStarted(true);
    resetGame();
  };

  const returnToHome = () => {
    setGameStarted(false);
    setGameOver(false);
  };

  // Calculate time bar percentage
  const timePercentage = (timeLeft / 30) * 100;
  const timeColor = timeLeft > 15 ? '#4CAF50' : timeLeft > 5 ? '#FFC107' : '#F44336';

  return (
    <div style={{ 
      fontFamily: 'Arial, sans-serif', 
      textAlign: 'center', 
      marginTop: '20px',
      backgroundColor: '#1a1a2e',
      color: '#fff',
      minHeight: '100vh',
      padding: '20px'
    }}>
      {!gameStarted ? (
        <div style={{
          maxWidth: '600px',
          margin: '0 auto',
          padding: '30px',
          backgroundColor: '#0f3460',
          borderRadius: '15px',
          border: '2px solid #4ecca3',
          boxShadow: '0 0 20px rgba(78, 204, 163, 0.5)'
        }}>
          <h2 style={{ 
            fontSize: '2.5rem', 
            color: '#4ecca3',
            textShadow: '0 0 10px rgba(78, 204, 163, 0.7)',
            marginBottom: '20px'
          }}>🐹 Whack-a-Mole!</h2>
          
          <div style={{ 
            fontSize: '1.2rem', 
            marginBottom: '25px',
            lineHeight: '1.6',
            color: '#f1f1f1'
          }}>
            <p>Whack the moles as they pop up!</p>
            <p>Try to get the highest score possible in 30 seconds.</p>
          </div>
          
          <div style={{ 
            marginBottom: '25px',
            padding: '15px',
            backgroundColor: '#16213e',
            borderRadius: '10px'
          }}>
            <h4 style={{ color: '#4ecca3', marginBottom: '10px' }}>How to Play:</h4>
            <div style={{ textAlign: 'left', display: 'inline-block', width: '100%' }}>
              <p>• Click on moles as they pop up to whack them</p>
              <p>• Try to whack as many as possible in 30 seconds</p>
              <p>• Power-ups will appear randomly - click them for bonuses</p>
              <p>• The game gets harder as your score increases</p>
            </div>
          </div>
          
          <button 
            style={{
              padding: '15px 40px',
              fontSize: '1.2rem',
              backgroundColor: '#4CAF50',
              color: '#fff',
              border: 'none',
              borderRadius: '8px',
              cursor: 'pointer',
              fontWeight: 'bold',
              transition: 'all 0.3s',
              boxShadow: '0 0 10px rgba(76, 175, 80, 0.5)'
            }}
            onClick={startGame}
          >
            Start Game
          </button>
        </div>
      ) : (
        <>
          <h2 style={{ 
            fontSize: '2.5rem', 
            color: '#4ecca3',
            textShadow: '0 0 10px rgba(78, 204, 163, 0.7)',
            marginBottom: '20px'
          }}>🐹 Whack-a-Mole!</h2>
          
          <div style={{
            display: 'flex',
            justifyContent: 'space-around',
            flexWrap: 'wrap',
            marginBottom: '20px',
            padding: '10px',
            backgroundColor: '#16213e',
            borderRadius: '10px',
            border: '2px solid #4ecca3'
          }}>
            <div style={{ margin: '5px' }}>
              <span style={{ color: '#4ecca3', fontWeight: 'bold' }}>Score:</span>
              <span style={{ marginLeft: '10px', fontSize: '1.2rem' }}>{score}</span>
            </div>
            
            <div style={{ margin: '5px' }}>
              <span style={{ color: '#4ecca3', fontWeight: 'bold' }}>Time:</span>
              <div style={{ 
                width: '200px', 
                height: '20px', 
                background: 'rgba(0, 0, 0, 0.2)', 
                borderRadius: '10px', 
                margin: '5px 0',
                overflow: 'hidden'
              }}>
                <div 
                  style={{ 
                    height: '100%', 
                    width: `${timePercentage}%`, 
                    backgroundColor: timeColor,
                    transition: 'width 0.5s ease'
                  }}
                ></div>
              </div>
              <div style={{ fontSize: '1.2rem', fontWeight: 'bold' }}>{timeLeft}s</div>
            </div>
            
            {bonusTime > 0 && (
              <div style={{
                position: 'absolute',
                top: '-20px',
                right: '10px',
                background: '#FFD700',
                color: '#000',
                padding: '5px 10px',
                borderRadius: '20px',
                fontWeight: 'bold',
                animation: 'pulse 0.5s'
              }}>
                +{bonusTime}s!
              </div>
            )}
          </div>
          
          <div style={{
            margin: '15px 0',
            height: '50px',
            display: 'flex',
            justifyContent: 'center',
            alignItems: 'center'
          }}>
            {combo > 1 && (
              <div style={{
                fontSize: '1.8rem',
                color: '#FFD700',
                fontWeight: 'bold',
                textShadow: '0 0 10px rgba(255, 215, 0, 0.7)',
                animation: 'pulse 0.5s',
                margin: '0 10px'
              }}>
                COMBO x{combo}!
              </div>
            )}
            {powerUpActive && (
              <div style={{
                fontSize: '1.4rem',
                color: '#FF416C',
                fontWeight: 'bold',
                textShadow: '0 0 10px rgba(255, 65, 108, 0.7)',
                animation: 'pulse 0.5s',
                margin: '0 10px'
              }}>
                {multiplier > 1 ? `x${multiplier} POINTS!` : 'BONUS TIME!'}
              </div>
            )}
          </div>
          
          {powerUp && (
            <div 
              style={{
                position: 'absolute',
                top: '20px',
                right: '20px',
                background: 'rgba(255, 255, 255, 0.9)',
                borderRadius: '50px',
                padding: '10px 20px',
                display: 'flex',
                alignItems: 'center',
                gap: '10px',
                cursor: 'pointer',
                zIndex: 10,
                boxShadow: '0 4px 15px rgba(0, 0, 0, 0.2)',
                animation: 'bounce 1s infinite'
              }}
              onClick={collectPowerUp}
            >
              <div style={{ fontSize: '1.5rem' }}>
                {powerUp === 'time' && '⏱️'}
                {powerUp === 'points' && '💰'}
                {powerUp === 'combo' && '🔥'}
              </div>
              <div style={{ fontWeight: 'bold', color: '#333' }}>
                {powerUp === 'time' && 'BONUS TIME'}
                {powerUp === 'points' && 'SCORE MULTIPLIER'}
                {powerUp === 'combo' && 'COMBO BOOST'}
              </div>
            </div>
          )}
          
          <div style={{
            backgroundColor: '#0f3460',
            borderRadius: '10px',
            padding: '20px',
            border: '2px solid #4ecca3',
            maxWidth: '600px',
            margin: '0 auto',
            position: 'relative',
            overflow: 'hidden'
          }} ref={boardRef}>
            <div style={{ 
              color: '#f1f1f1', 
              marginBottom: '15px',
              fontSize: '1.1rem'
            }}>
              Whack the moles as fast as you can!
            </div>
            
            <div style={{
              display: 'grid',
              gridTemplateColumns: 'repeat(3, 1fr)',
              gap: '15px',
              width: '100%',
              margin: '20px 0'
            }}>
              {moles.map((hasMole, index) => (
                <div 
                  key={index}
                  style={{
                    aspectRatio: '1',
                    backgroundColor: currentMoles.includes(index) ? '#5D2906' : '#8B4513',
                    borderRadius: '50%',
                    position: 'relative',
                    overflow: 'hidden',
                    cursor: 'pointer',
                    boxShadow: '0 8px 15px rgba(0, 0, 0, 0.3)',
                    transition: 'transform 0.2s',
                    display: 'flex',
                    justifyContent: 'center',
                    alignItems: 'flex-end'
                  }}
                  onClick={(e) => handleWhack(e, index)}
                >
                  {currentMoles.includes(index) && (
                    <div style={{
                      position: 'absolute',
                      bottom: moleState[index] === 'whacked' ? '0%' : '0%',
                      left: '50%',
                      transform: 'translateX(-50%)',
                      width: '80%',
                      height: '80%',
                      backgroundColor: '#A9A9A9',
                      borderRadius: '50% 50% 40% 40%',
                      transition: 'bottom 0.3s ease',
                      animation: moleState[index] === 'whacked' ? 'whack 0.3s forwards' : 'none'
                    }}>
                      <div style={{
                        position: 'absolute',
                        top: '30%',
                        left: '50%',
                        transform: 'translateX(-50%)',
                        width: '60%',
                        textAlign: 'center'
                      }}>
                        <div style={{
                          display: 'flex',
                          justifyContent: 'space-around',
                          marginBottom: '10px'
                        }}>
                          <div style={{
                            width: '12px',
                            height: '12px',
                            backgroundColor: 'black',
                            borderRadius: '50%'
                          }}></div>
                          <div style={{
                            width: '12px',
                            height: '12px',
                            backgroundColor: 'black',
                            borderRadius: '50%'
                          }}></div>
                        </div>
                        <div style={{
                          width: '20px',
                          height: '12px',
                          backgroundColor: '#8B0000',
                          margin: '0 auto 10px',
                          borderRadius: '50%'
                        }}></div>
                        <div style={{
                          display: 'flex',
                          justifyContent: 'center'
                        }}>
                          <div style={{
                            width: '8px',
                            height: '2px',
                            backgroundColor: 'black',
                            margin: '0 2px'
                          }}></div>
                          <div style={{
                            width: '8px',
                            height: '2px',
                            backgroundColor: 'black',
                            margin: '0 2px'
                          }}></div>
                          <div style={{
                            width: '8px',
                            height: '2px',
                            backgroundColor: 'black',
                            margin: '0 2px'
                          }}></div>
                        </div>
                      </div>
                    </div>
                  )}
                </div>
              ))}
              
              {showHammer && (
                <div 
                  style={{
                    position: 'absolute',
                    zIndex: 20,
                    pointerEvents: 'none',
                    left: `${hammerPosition.x}px`,
                    top: `${hammerPosition.y}px`,
                    transform: 'translate(-50%, -50%)',
                    animation: 'whackHammer 0.3s ease-out'
                  }}
                >
                  <div style={{
                    width: '12px',
                    height: '80px',
                    background: 'linear-gradient(to right, #8B4513, #A0522D, #8B4513)',
                    borderRadius: '6px',
                    position: 'absolute',
                    top: '-40px',
                    left: '50%',
                    transform: 'translateX(-50%)',
                    boxShadow: '0 2px 5px rgba(0, 0, 0, 0.3)'
                  }}></div>
                  <div style={{
                    width: '60px',
                    height: '40px',
                    background: 'linear-gradient(135deg, #555, #333)',
                    borderRadius: '8px',
                    position: 'absolute',
                    top: '-80px',
                    left: '50%',
                    transform: 'translateX(-50%)',
                    boxShadow: '0 2px 8px rgba(0, 0, 0, 0.4)',
                    display: 'flex',
                    justifyContent: 'center',
                    alignItems: 'center'
                  }}>
                    <div style={{
                      width: '40px',
                      height: '20px',
                      background: '#FFD700',
                      borderRadius: '4px'
                    }}></div>
                  </div>
                </div>
              )}
            </div>

            {gameOver && (
              <div style={{
                position: 'fixed',
                top: 0,
                left: 0,
                width: '100%',
                height: '100%',
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                display: 'flex',
                justifyContent: 'center',
                alignItems: 'center',
                zIndex: 100
              }}>
                <div style={{
                  backgroundColor: '#0f3460',
                  padding: '30px',
                  borderRadius: '10px',
                  textAlign: 'center',
                  border: '2px solid #4ecca3',
                  maxWidth: '400px',
                  width: '80%'
                }}>
                  <h3 style={{ color: '#ff5252', fontSize: '2rem', marginBottom: '20px' }}>Game Over!</h3>
                  <p style={{ fontSize: '1.2rem', marginBottom: '15px' }}>Final Score: <span style={{ color: '#4ecca3', fontWeight: 'bold' }}>{score}</span></p>
                  <p style={{ fontSize: '1.2rem', marginBottom: '15px' }}>Max Combo: <span style={{ color: '#4ecca3', fontWeight: 'bold' }}>x{maxCombo}</span></p>
                  <p style={{ fontSize: '1.2rem', marginBottom: '20px' }}>High Score: <span style={{ color: '#4ecca3', fontWeight: 'bold' }}>{highScore}</span></p>
                  <div style={{ display: 'flex', justifyContent: 'center', gap: '15px' }}>
                    <button 
                      style={{
                        padding: '12px 25px',
                        backgroundColor: '#4CAF50',
                        color: '#fff',
                        border: 'none',
                        borderRadius: '5px',
                        cursor: 'pointer',
                        fontWeight: 'bold',
                        fontSize: '1.1rem',
                        transition: 'all 0.2s'
                      }}
                      onClick={resetGame}
                    >
                      Play Again
                    </button>
                    <button 
                      style={{
                        padding: '12px 25px',
                        backgroundColor: '#9C27B0',
                        color: '#fff',
                        border: 'none',
                        borderRadius: '5px',
                        cursor: 'pointer',
                        fontWeight: 'bold',
                        fontSize: '1.1rem',
                        transition: 'all 0.2s'
                      }}
                      onClick={returnToHome}
                    >
                      Home
                    </button>
                  </div>
                </div>
              </div>
            )}

            <div style={{ display: 'flex', justifyContent: 'center', gap: '15px', marginTop: '20px' }}>
              <button 
                style={{
                  padding: '10px 20px',
                  backgroundColor: '#9C27B0',
                  color: '#fff',
                  border: 'none',
                  borderRadius: '5px',
                  cursor: 'pointer',
                  fontWeight: 'bold',
                  fontSize: '1rem',
                  transition: 'all 0.2s'
                }}
                onClick={returnToHome}
              >
                Home
              </button>
            </div>
          </div>
        </>
      )}
      
      <style>{`
        @keyframes pulse {
          0% { transform: scale(1); }
          50% { transform: scale(1.2); }
          100% { transform: scale(1); }
        }
        
        @keyframes bounce {
          0%, 100% { transform: translateY(0); }
          50% { transform: translateY(-10px); }
        }
        
        @keyframes whack {
          0% { transform: translateX(-50%) rotate(0deg); }
          25% { transform: translateX(-50%) rotate(10deg); }
          50% { transform: translateX(-50%) rotate(-10deg); }
          75% { transform: translateX(-50%) rotate(5deg); }
          100% { transform: translateX(-50%) rotate(0deg); }
        }
        
        @keyframes whackHammer {
          0% { transform: translate(-50%, -50%) scale(1) rotate(0deg); }
          50% { transform: translate(-50%, -50%) scale(1.2) rotate(-10deg); }
          100% { transform: translate(-50%, -50%) scale(1) rotate(0deg); }
        }
        
        button:hover {
          transform: scale(1.05);
          box-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
        }
        
        button:disabled {
          opacity: 0.6;
          cursor: not-allowed;
          transform: none;
        }
      `}</style>
    </div>
  );
};

export default WhackAMole;