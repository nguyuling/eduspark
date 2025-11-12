import React, { useState, useEffect, useCallback, useRef } from 'react';

const SpaceGame = () => {
  const [score, setScore] = useState(0);
  const [highScore, setHighScore] = useState(() => {
    return parseInt(localStorage.getItem('spaceGameHighScore') || '0');
  });
  const [playerPosition, setPlayerPosition] = useState(50);
  const [bullets, setBullets] = useState([]);
  const [enemies, setEnemies] = useState([]);
  const [explosions, setExplosions] = useState([]);
  const [stars, setStars] = useState([]);
  const [powerUps, setPowerUps] = useState([]);
  const [lives, setLives] = useState(3);
  const [gameOver, setGameOver] = useState(false);
  const [isPaused, setIsPaused] = useState(false);
  const [level, setLevel] = useState(1);
  const [playerPower, setPlayerPower] = useState(1); // 1: normal, 2: double shot, 3: laser
  const [powerUpTimer, setPowerUpTimer] = useState(0);
  const [showControls, setShowControls] = useState(false);
  const [gameStarted, setGameStarted] = useState(false);
  
  const gameAreaRef = useRef(null);
  const lastTimeRef = useRef(0);
  const animationFrameRef = useRef();
  const keysPressed = useRef({});

  // Initialize stars for background
  useEffect(() => {
    const newStars = [];
    for (let i = 0; i < 100; i++) {
      newStars.push({
        id: i,
        x: Math.random() * 100,
        y: Math.random() * 100,
        size: Math.random() * 2 + 1,
        speed: Math.random() * 0.5 + 0.1
      });
    }
    setStars(newStars);
  }, []);

  // Move stars for parallax effect
  useEffect(() => {
    if (gameOver || isPaused || !gameStarted) return;
    
    const moveStars = () => {
      setStars(prev => prev.map(star => ({
        ...star,
        y: star.y >= 100 ? 0 : star.y + star.speed
      })));
    };
    
    const starInterval = setInterval(moveStars, 100);
    return () => clearInterval(starInterval);
  }, [gameOver, isPaused, gameStarted]);

  // Create enemies
  useEffect(() => {
    if (gameOver || isPaused || !gameStarted) return;
    
    const enemyInterval = setInterval(() => {
      if (Math.random() > 0.7) { // 30% chance to spawn a stronger enemy
        setEnemies(prev => [...prev, {
          id: Date.now(),
          x: Math.random() * 90,
          y: 0,
          health: 2,
          speed: 1.5,
          type: 'strong'
        }]);
      } else {
        setEnemies(prev => [...prev, {
          id: Date.now(),
          x: Math.random() * 90,
          y: 0,
          health: 1,
          speed: 1,
          type: 'normal'
        }]);
      }
    }, Math.max(500, 1000 - level * 50)); // Increase spawn rate with level

    return () => clearInterval(enemyInterval);
  }, [gameOver, isPaused, level, gameStarted]);

  // Create power-ups
  useEffect(() => {
    if (gameOver || isPaused || !gameStarted) return;
    
    const powerUpInterval = setInterval(() => {
      if (Math.random() > 0.9) { // 10% chance to spawn power-up
        setPowerUps(prev => [...prev, {
          id: Date.now(),
          x: Math.random() * 90,
          y: 0,
          type: Math.random() > 0.5 ? 'health' : 'power'
        }]);
      }
    }, 10000); // Every 10 seconds

    return () => clearInterval(powerUpInterval);
  }, [gameOver, isPaused, gameStarted]);

  // Game loop
  useEffect(() => {
    if (gameOver || isPaused || !gameStarted) return;
    
    const gameLoop = (timestamp) => {
      const deltaTime = timestamp - (lastTimeRef.current || timestamp);
      lastTimeRef.current = timestamp;
      
      // Handle continuous key presses
      if (keysPressed.current['ArrowLeft']) {
        setPlayerPosition(prev => Math.max(0, Math.min(90, prev - 0.5)));
      }
      if (keysPressed.current['ArrowRight']) {
        setPlayerPosition(prev => Math.max(0, Math.min(90, prev + 0.5)));
      }
      
      // Move enemies
      setEnemies(prev => prev.map(enemy => ({
        ...enemy,
        y: enemy.y + enemy.speed * (deltaTime / 16)
      })).filter(enemy => enemy.y < 100));
      
      // Move bullets
      setBullets(prev => prev.map(bullet => ({
        ...bullet,
        y: bullet.y - 8 * (deltaTime / 16)
      })).filter(bullet => bullet.y > 0));
      
      // Move power-ups
      setPowerUps(prev => prev.map(powerUp => ({
        ...powerUp,
        y: powerUp.y + 1 * (deltaTime / 16)
      })).filter(powerUp => powerUp.y < 100));
      
      // Update power-up timer
      if (playerPower > 1) {
        setPowerUpTimer(prev => {
          if (prev <= 0) {
            setPlayerPower(1);
            return 0;
          }
          return prev - deltaTime;
        });
      }
      
      animationFrameRef.current = requestAnimationFrame(gameLoop);
    };
    
    animationFrameRef.current = requestAnimationFrame(gameLoop);
    
    return () => {
      if (animationFrameRef.current) {
        cancelAnimationFrame(animationFrameRef.current);
      }
    };
  }, [gameOver, isPaused, playerPower, gameStarted]);

  // Collision detection
  useEffect(() => {
    if (gameOver || isPaused || !gameStarted) return;
    
    // Bullet-enemy collisions
    bullets.forEach(bullet => {
      enemies.forEach(enemy => {
        const distance = Math.sqrt(
          Math.pow(bullet.x - enemy.x, 2) + Math.pow(bullet.y - enemy.y, 2)
        );
        
        if (distance < 5) {
          // Create explosion
          setExplosions(prev => [...prev, {
            id: Date.now(),
            x: enemy.x,
            y: enemy.y,
            size: 20,
            life: 300 // ms
          }]);
          
          // Remove bullet
          setBullets(prev => prev.filter(b => b.id !== bullet.id));
          
          // Damage enemy
          setEnemies(prev => {
            const updated = prev.map(e => {
              if (e.id === enemy.id) {
                const newHealth = e.health - 1;
                if (newHealth <= 0) {
                  // Enemy destroyed
                  setScore(s => s + (e.type === 'strong' ? 20 : 10));
                  return null;
                }
                return {...e, health: newHealth};
              }
              return e;
            }).filter(Boolean);
            
            return updated;
          });
        }
      });
    });
    
    // Player-enemy collisions
    enemies.forEach(enemy => {
      const distance = Math.sqrt(
        Math.pow(playerPosition - enemy.x, 2) + Math.pow(80 - enemy.y, 2)
      );
      
      if (distance < 8) {
        // Create explosion
        setExplosions(prev => [...prev, {
          id: Date.now(),
          x: enemy.x,
          y: enemy.y,
          size: 30,
          life: 500
        }]);
        
        // Remove enemy
        setEnemies(prev => prev.filter(e => e.id !== enemy.id));
        
        // Lose life
        setLives(prev => {
          const newLives = prev - 1;
          if (newLives <= 0) {
            setGameOver(true);
            if (score > highScore) {
              setHighScore(score);
              localStorage.setItem('spaceGameHighScore', score.toString());
            }
          }
          return newLives;
        });
      }
    });
    
    // Player-powerUp collisions
    powerUps.forEach(powerUp => {
      const distance = Math.sqrt(
        Math.pow(playerPosition - powerUp.x, 2) + Math.pow(80 - powerUp.y, 2)
      );
      
      if (distance < 6) {
        // Collect power-up
        setPowerUps(prev => prev.filter(p => p.id !== powerUp.id));
        
        if (powerUp.type === 'health') {
          setLives(prev => Math.min(3, prev + 1));
        } else if (powerUp.type === 'power') {
          setPlayerPower(2);
          setPowerUpTimer(10000); // 10 seconds
        }
      }
    });
    
    // Update explosions
    setExplosions(prev => prev.map(exp => ({
      ...exp,
      life: exp.life - 16,
      size: exp.size + 0.5
    })).filter(exp => exp.life > 0));
  }, [bullets, enemies, powerUps, playerPosition, gameOver, isPaused, score, highScore, gameStarted]);

  // Level progression
  useEffect(() => {
    if (score >= level * 100) {
      setLevel(prev => prev + 1);
    }
  }, [score, level]);

  const shoot = () => {
    if (gameOver || isPaused || !gameStarted) return;
    
    const newBullet = {
      id: Date.now(),
      x: playerPosition,
      y: 80,
      type: playerPower
    };
    
    if (playerPower === 2) { // Double shot
      setBullets(prev => [...prev, 
        {...newBullet, id: Date.now(), x: playerPosition - 2},
        {...newBullet, id: Date.now() + 1, x: playerPosition + 2}
      ]);
    } else {
      setBullets(prev => [...prev, newBullet]);
    }
  };

  // Keyboard event handlers
  useEffect(() => {
    const handleKeyDown = (e) => {
      keysPressed.current[e.key] = true;
      
      if (e.key === ' ') {
        e.preventDefault(); // Prevent page scroll
        shoot();
      }
      if (e.key === 'p' || e.key === 'P') {
        setIsPaused(prev => !prev);
      }
      if (e.key === 'c' || e.key === 'C') {
        setShowControls(prev => !prev);
      }
    };

    const handleKeyUp = (e) => {
      keysPressed.current[e.key] = false;
    };

    window.addEventListener('keydown', handleKeyDown);
    window.addEventListener('keyup', handleKeyUp);

    return () => {
      window.removeEventListener('keydown', handleKeyDown);
      window.removeEventListener('keyup', handleKeyUp);
    };
  }, [gameOver, isPaused, gameStarted, shoot]);

  const resetGame = () => {
    setScore(0);
    setPlayerPosition(50);
    setBullets([]);
    setEnemies([]);
    setExplosions([]);
    setPowerUps([]);
    setLives(3);
    setGameOver(false);
    setLevel(1);
    setPlayerPower(1);
    setPowerUpTimer(0);
    setIsPaused(false);
    setGameStarted(true);
  };

  const togglePause = () => {
    if (!gameOver && gameStarted) {
      setIsPaused(prev => !prev);
    }
  };

  const startGame = () => {
    setGameStarted(true);
    resetGame();
  };

  const returnToHome = () => {
    // In a real app, this would navigate to the homepage
    window.location.reload(); // For this example, we'll just reload
  };

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
          }}>🚀 Space Defender</h2>
          
          <div style={{ 
            fontSize: '1.2rem', 
            marginBottom: '25px',
            lineHeight: '1.6',
            color: '#f1f1f1'
          }}>
            <p>Defend your ship against alien invaders!</p>
            <p>Collect power-ups to enhance your weapons and gain extra lives.</p>
          </div>
          
          <div style={{ 
            marginBottom: '25px',
            padding: '15px',
            backgroundColor: '#16213e',
            borderRadius: '10px'
          }}>
            <h4 style={{ color: '#4ecca3', marginBottom: '10px' }}>How to Play:</h4>
            <div style={{ textAlign: 'left', display: 'inline-block', width: '100%' }}>
              <p>← → Arrow Keys: Move your ship</p>
              <p>Spacebar: Shoot lasers</p>
              <p>P: Pause/Resume game</p>
              <p>C: Show/hide controls</p>
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
          }}>🚀 Space Defender</h2>
          
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
              <span style={{ color: '#4ecca3', fontWeight: 'bold' }}>High Score:</span>
              <span style={{ marginLeft: '10px', fontSize: '1.2rem' }}>{highScore}</span>
            </div>
            <div style={{ margin: '5px' }}>
              <span style={{ color: '#4ecca3', fontWeight: 'bold' }}>Level:</span>
              <span style={{ marginLeft: '10px', fontSize: '1.2rem' }}>{level}</span>
            </div>
            <div style={{ margin: '5px' }}>
              <span style={{ color: '#4ecca3', fontWeight: 'bold' }}>Lives:</span>
              <div style={{ display: 'inline-block', marginLeft: '10px' }}>
                {[...Array(3)].map((_, i) => (
                  <span 
                    key={i} 
                    style={{ 
                      fontSize: '1.5rem', 
                      color: i < lives ? '#ff5252' : '#444',
                      margin: '0 2px'
                    }}
                  >❤️</span>
                ))}
              </div>
            </div>
          </div>
          
          <div style={{
            backgroundColor: '#0f3460',
            borderRadius: '10px',
            padding: '20px',
            border: '2px solid #4ecca3',
            maxWidth: '800px',
            margin: '0 auto'
          }}>
            <div style={{ 
              color: '#f1f1f1', 
              marginBottom: '15px',
              fontSize: '1.1rem'
            }}>
              Use ← → arrows to move, Space to shoot! | P to pause | C for controls
            </div>
            
            <div 
              ref={gameAreaRef}
              style={{
                position: 'relative',
                width: '100%',
                height: '400px',
                backgroundColor: '#0d1b2a',
                border: '2px solid #4ecca3',
                overflow: 'hidden',
                borderRadius: '8px',
                margin: '0 auto'
              }}
            >
              {/* Stars background */}
              {stars.map(star => (
                <div 
                  key={star.id}
                  style={{
                    position: 'absolute',
                    left: `${star.x}%`,
                    top: `${star.y}%`,
                    width: `${star.size}px`,
                    height: `${star.size}px`,
                    backgroundColor: '#fff',
                    borderRadius: '50%',
                    boxShadow: '0 0 5px #fff',
                  }}
                />
              ))}
              
              {/* Player */}
              <div 
                style={{
                  position: 'absolute',
                  left: `${playerPosition}%`,
                  bottom: '20px',
                  transform: 'translateX(-50%)',
                  transition: 'left 0.1s',
                }}
              >
                <div style={{
                  fontSize: '2rem',
                  filter: playerPower > 1 ? 'drop-shadow(0 0 5px #4ecca3)' : 'none'
                }}>🚀</div>
                {playerPower > 1 && (
                  <div style={{
                    position: 'absolute',
                    top: '-20px',
                    left: '50%',
                    transform: 'translateX(-50%)',
                    fontSize: '1.2rem',
                    animation: 'pulse 1s infinite'
                  }}>
                    ⚡
                  </div>
                )}
              </div>

              {/* Bullets */}
              {bullets.map(bullet => (
                <div 
                  key={bullet.id}
                  style={{
                    position: 'absolute',
                    left: `${bullet.x}%`,
                    top: `${bullet.y}%`,
                    width: '5px',
                    height: '15px',
                    backgroundColor: bullet.type > 1 ? '#4ecca3' : '#ff0',
                    borderRadius: '2px',
                    boxShadow: bullet.type > 1 ? '0 0 8px #4ecca3' : '0 0 5px #ff0',
                  }}
                />
              ))}

              {/* Enemies */}
              {enemies.map(enemy => (
                <div 
                  key={enemy.id}
                  style={{
                    position: 'absolute',
                    left: `${enemy.x}%`,
                    top: `${enemy.y}%`,
                    width: '30px',
                    height: '30px',
                    transform: 'translateX(-50%)',
                    display: 'flex',
                    justifyContent: 'center',
                    alignItems: 'center',
                    fontSize: enemy.type === 'strong' ? '1.8rem' : '1.5rem',
                  }}
                >
                  {enemy.type === 'strong' ? '👾' : '👽'}
                  {enemy.health > 1 && (
                    <div style={{
                      position: 'absolute',
                      bottom: '-10px',
                      left: '0',
                      width: '100%',
                      height: '4px',
                      backgroundColor: '#444',
                      borderRadius: '2px',
                      overflow: 'hidden'
                    }}>
                      <div 
                        style={{ 
                          height: '100%', 
                          backgroundColor: '#ff5252',
                          width: `${(enemy.health / 2) * 100}%`,
                          transition: 'width 0.3s'
                        }} 
                      />
                    </div>
                  )}
                </div>
              ))}

              {/* Power-ups */}
              {powerUps.map(powerUp => (
                <div 
                  key={powerUp.id}
                  style={{
                    position: 'absolute',
                    left: `${powerUp.x}%`,
                    top: `${powerUp.y}%`,
                    width: '25px',
                    height: '25px',
                    transform: 'translateX(-50%)',
                    display: 'flex',
                    justifyContent: 'center',
                    alignItems: 'center',
                    fontSize: '1.5rem',
                    animation: 'spin 2s linear infinite',
                  }}
                >
                  {powerUp.type === 'health' ? '❤️' : '⚡'}
                </div>
              ))}

              {/* Explosions */}
              {explosions.map(explosion => (
                <div 
                  key={explosion.id}
                  style={{
                    position: 'absolute',
                    left: `${explosion.x}%`,
                    top: `${explosion.y}%`,
                    width: `${explosion.size}px`,
                    height: `${explosion.size}px`,
                    transform: 'translate(-50%, -50%)',
                    borderRadius: '50%',
                    background: `radial-gradient(circle, #ff9800 0%, #ff5722 70%, transparent 100%)`,
                    opacity: explosion.life / 500,
                    pointerEvents: 'none',
                  }}
                />
              ))}
            </div>

            <div style={{
              display: 'flex',
              justifyContent: 'center',
              gap: '10px',
              marginTop: '20px'
            }}>
              <button 
                style={{
                  padding: '10px 20px',
                  backgroundColor: '#ff9800',
                  color: '#000',
                  border: 'none',
                  borderRadius: '5px',
                  cursor: 'pointer',
                  fontWeight: 'bold',
                  fontSize: '1rem',
                  transition: 'all 0.2s'
                }}
                onClick={() => setPlayerPosition(prev => Math.max(0, Math.min(90, prev - 10)))}
                disabled={gameOver || isPaused}
              >
                ← Left
              </button>
              <button 
                style={{
                  padding: '10px 20px',
                  backgroundColor: '#4CAF50',
                  color: '#fff',
                  border: 'none',
                  borderRadius: '5px',
                  cursor: 'pointer',
                  fontWeight: 'bold',
                  fontSize: '1rem',
                  transition: 'all 0.2s'
                }}
                onClick={shoot}
                disabled={gameOver || isPaused}
              >
                🔥 Shoot
              </button>
              <button 
                style={{
                  padding: '10px 20px',
                  backgroundColor: '#ff9800',
                  color: '#000',
                  border: 'none',
                  borderRadius: '5px',
                  cursor: 'pointer',
                  fontWeight: 'bold',
                  fontSize: '1rem',
                  transition: 'all 0.2s'
                }}
                onClick={() => setPlayerPosition(prev => Math.max(0, Math.min(90, prev + 10)))}
                disabled={gameOver || isPaused}
              >
                Right →
              </button>
            </div>
            
            <div style={{
              display: 'flex',
              justifyContent: 'center',
              gap: '15px',
              marginTop: '20px'
            }}>
              <button 
                style={{
                  padding: '10px 20px',
                  backgroundColor: '#2196F3',
                  color: '#fff',
                  border: 'none',
                  borderRadius: '5px',
                  cursor: 'pointer',
                  fontWeight: 'bold',
                  fontSize: '1rem',
                  transition: 'all 0.2s'
                }}
                onClick={togglePause}
                disabled={gameOver}
              >
                {isPaused ? '▶️ Resume' : '⏸️ Pause'}
              </button>
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
                onClick={resetGame}
              >
                🔄 Restart
              </button>
            </div>
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
                <p style={{ fontSize: '1.2rem', marginBottom: '15px' }}>Your Score: <span style={{ color: '#4ecca3', fontWeight: 'bold' }}>{score}</span></p>
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
          
          {isPaused && !gameOver && (
            <div style={{
              position: 'fixed',
              top: 0,
              left: 0,
              width: '100%',
              height: '100%',
              backgroundColor: 'rgba(0, 0, 0, 0.7)',
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
                <h3 style={{ color: '#4ecca3', fontSize: '2rem', marginBottom: '20px' }}>Game Paused</h3>
                <button 
                  style={{
                    padding: '12px 30px',
                    backgroundColor: '#2196F3',
                    color: '#fff',
                    border: 'none',
                    borderRadius: '5px',
                    cursor: 'pointer',
                    fontWeight: 'bold',
                    fontSize: '1.1rem',
                    transition: 'all 0.2s'
                  }}
                  onClick={togglePause}
                >
                  Resume Game
                </button>
              </div>
            </div>
          )}
          
          {showControls && (
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
                maxWidth: '500px',
                width: '80%'
              }}>
                <h3 style={{ color: '#4ecca3', fontSize: '1.8rem', marginBottom: '20px' }}>Game Controls</h3>
                <div style={{ textAlign: 'left', marginBottom: '20px' }}>
                  <p style={{ margin: '10px 0', fontSize: '1.1rem' }}>← → Arrow Keys: Move your ship</p>
                  <p style={{ margin: '10px 0', fontSize: '1.1rem' }}>Spacebar: Shoot lasers</p>
                  <p style={{ margin: '10px 0', fontSize: '1.1rem' }}>P: Pause/Resume game</p>
                  <p style={{ margin: '10px 0', fontSize: '1.1rem' }}>C: Show/hide controls</p>
                </div>
                <button 
                  style={{
                    padding: '12px 30px',
                    backgroundColor: '#9C27B0',
                    color: '#fff',
                    border: 'none',
                    borderRadius: '5px',
                    cursor: 'pointer',
                    fontWeight: 'bold',
                    fontSize: '1.1rem',
                    transition: 'all 0.2s'
                  }}
                  onClick={() => setShowControls(false)}
                >
                  Close
                </button>
              </div>
            </div>
          )}
          
          <div style={{
            marginTop: '20px',
            padding: '15px',
            backgroundColor: '#16213e',
            borderRadius: '10px',
            border: '2px solid #4ecca3'
          }}>
            <h5 style={{ color: '#4ecca3', marginBottom: '15px' }}>Power-ups:</h5>
            <div style={{ display: 'flex', justifyContent: 'center', gap: '30px' }}>
              <div style={{ textAlign: 'center' }}>
                <div style={{ fontSize: '2rem', marginBottom: '5px' }}>❤️</div>
                <p>Health - Gain an extra life</p>
              </div>
              <div style={{ textAlign: 'center' }}>
                <div style={{ fontSize: '2rem', marginBottom: '5px' }}>⚡</div>
                <p>Power Shot - Double shot for 10 seconds</p>
              </div>
            </div>
          </div>
        </>
      )}
      
      <style>{`
        @keyframes spin {
          0% { transform: translateX(-50%) rotate(0deg); }
          100% { transform: translateX(-50%) rotate(360deg); }
        }
        
        @keyframes pulse {
          0% { transform: translateX(-50%) scale(1); }
          50% { transform: translateX(-50%) scale(1.2); }
          100% { transform: translateX(-50%) scale(1); }
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

export default SpaceGame;