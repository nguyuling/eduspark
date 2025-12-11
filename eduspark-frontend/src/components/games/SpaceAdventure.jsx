import React, { useState, useEffect, useCallback, useRef } from 'react';
import { progressService } from '../../services/progressService';
import GameSummary from './GameSummary';
import RewardsDisplay from './RewardsDisplay';

const SpaceAdventure = () => {
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
  const [playerPower, setPlayerPower] = useState(1);
  const [powerUpTimer, setPowerUpTimer] = useState(0);
  const [showControls, setShowControls] = useState(false);
  const [gameStarted, setGameStarted] = useState(false);
  const [gameProgress, setGameProgress] = useState(null);
  const [unlockedRewards, setUnlockedRewards] = useState([]);
  const [showSummary, setShowSummary] = useState(false);
  
  const gameAreaRef = useRef(null);
  const lastTimeRef = useRef(0);
  const animationFrameRef = useRef();
  const keysPressed = useRef({});
  const shootCooldownRef = useRef(false);

  // Initialize stars
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

  // Move stars
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
      if (Math.random() > 0.7) {
        setEnemies(prev => [...prev, {
          id: Date.now(),
          x: Math.random() * 90,
          y: -5, // Start slightly off-screen
          health: 2,
          speed: 1.5,
          type: 'strong'
        }]);
      } else {
        setEnemies(prev => [...prev, {
          id: Date.now(),
          x: Math.random() * 90,
          y: -5,
          health: 1,
          speed: 1,
          type: 'normal'
        }]);
      }
    }, Math.max(500, 1000 - level * 50));

    return () => clearInterval(enemyInterval);
  }, [gameOver, isPaused, level, gameStarted]);

  // ✅ FIXED: Reliable collision detection (minimal, robust)
  const checkCollisions = useCallback(() => {
    // Hitbox sizes in % units (relative to screen width/height)
    const BULLET_W = 0.8;
    const BULLET_H = 1.5;
    const ENEMY_W = 3.5;
    const ENEMY_H = 3.5;
    const PLAYER_W = 4;
    const PLAYER_H = 4;

    // --- BULLET ↔ ENEMY ---
    setBullets(prevBullets => {
      const newBullets = [...prevBullets];
      const bulletsToRemove = [];
      let pointsToAdd = 0;
      const explosionsToAdd = [];

      setEnemies(prevEnemies => {
        const newEnemies = [...prevEnemies];
        const enemiesToRemove = [];

        for (let i = 0; i < newBullets.length; i++) {
          const b = newBullets[i];
          const bL = b.x - BULLET_W / 2;
          const bR = b.x + BULLET_W / 2;
          const bT = b.y;
          const bB = b.y + BULLET_H;

          for (let j = 0; j < newEnemies.length; j++) {
            const e = newEnemies[j];
            const eL = e.x;
            const eR = e.x + ENEMY_W;
            const eT = e.y;
            const eB = e.y + ENEMY_H;

            // ✅ Accurate overlap check
            if (bR > eL && bL < eR && bB > eT && bT < eB) {
              // Hit!
              newEnemies[j] = { ...e, health: e.health - 1 };
              bulletsToRemove.push(i);

              if (newEnemies[j].health <= 0) {
                enemiesToRemove.push(j);
                pointsToAdd += (e.type === 'strong' ? 20 : 10);
                explosionsToAdd.push({
                  id: Date.now() + Math.random(),
                  x: e.x + ENEMY_W / 2,
                  y: e.y + ENEMY_H / 2
                });
              }
              break; // One bullet, one enemy
            }
          }
        }

        // Remove enemies (reverse order)
        for (let i = enemiesToRemove.length - 1; i >= 0; i--) {
          newEnemies.splice(enemiesToRemove[i], 1);
        }

        // Update score
        if (pointsToAdd > 0) {
          setScore(prev => {
            const newScore = prev + pointsToAdd;
            if (newScore > highScore) {
              setHighScore(newScore);
              localStorage.setItem('spaceGameHighScore', newScore.toString());
            }
            return newScore;
          });
        }

        // Add explosions
        if (explosionsToAdd.length > 0) {
          setExplosions(prev => [...prev, ...explosionsToAdd.map(e => ({...e, size: 0}))]);
        }

        return newEnemies;
      });

      // Remove bullets (reverse order)
      for (let i = bulletsToRemove.length - 1; i >= 0; i--) {
        newBullets.splice(bulletsToRemove[i], 1);
      }

      return newBullets;
    });

    // --- PLAYER ↔ ENEMY ---
    setEnemies(prevEnemies => {
      const newEnemies = [...prevEnemies];
      const pL = playerPosition - PLAYER_W / 2;
      const pR = playerPosition + PLAYER_W / 2;
      const pT = 80;
      const pB = 80 + PLAYER_H;

      for (let i = newEnemies.length - 1; i >= 0; i--) {
        const e = newEnemies[i];
        const eL = e.x;
        const eR = e.x + ENEMY_W;
        const eT = e.y;
        const eB = e.y + ENEMY_H;

        if (pR > eL && pL < eR && pB > eT && pT < eB) {
          // Player hit
          setLives(prev => {
            const newLives = prev - 1;
            if (newLives <= 0) setGameOver(true);
            return newLives;
          });
          
          setExplosions(prev => [...prev, {
            id: Date.now() + Math.random(),
            x: playerPosition,
            y: 82,
            size: 0
          }]);
          
          newEnemies.splice(i, 1);
        }
      }
      return newEnemies;
    });
  }, [playerPosition, highScore]);

  // ✅ FIXED: Game loop runs collision every frame
  useEffect(() => {
    if (gameOver || isPaused || !gameStarted) return;

    const gameLoop = (timestamp) => {
      const deltaTime = timestamp - (lastTimeRef.current || timestamp);
      lastTimeRef.current = timestamp;

      // Player movement
      if (keysPressed.current['ArrowLeft']) {
        setPlayerPosition(prev => Math.max(2, Math.min(98, prev - 0.7 * (deltaTime / 16))));
      }
      if (keysPressed.current['ArrowRight']) {
        setPlayerPosition(prev => Math.max(2, Math.min(98, prev + 0.7 * (deltaTime / 16))));
      }

      // Move enemies
      setEnemies(prev => 
        prev.map(e => ({ ...e, y: e.y + e.speed * (deltaTime / 16) }))
          .filter(e => e.y < 110)
      );

      // Move bullets (faster & smoother)
      setBullets(prev => 
        prev.map(b => ({ ...b, y: b.y - 10 * (deltaTime / 16) }))
          .filter(b => b.y > -5)
      );

      // ✅ Critical: Check collisions every frame
      checkCollisions();

      animationFrameRef.current = requestAnimationFrame(gameLoop);
    };

    animationFrameRef.current = requestAnimationFrame(gameLoop);
    return () => {
      if (animationFrameRef.current) {
        cancelAnimationFrame(animationFrameRef.current);
      }
    };
  }, [gameOver, isPaused, gameStarted, checkCollisions]);

  // ✅ FIXED: Shoot with cooldown & clean ID
  const shoot = useCallback(() => {
    if (gameOver || isPaused || !gameStarted || shootCooldownRef.current) return;

    shootCooldownRef.current = true;
    setTimeout(() => { shootCooldownRef.current = false; }, 250);

    const newBullet = {
      id: Date.now() + Math.random(), // Avoid duplicate IDs
      x: playerPosition,
      y: 78
    };

    setBullets(prev => [...prev, newBullet]);
  }, [gameOver, isPaused, gameStarted, playerPosition]);

  // Tracking & progress
  useEffect(() => {
    if (gameStarted && !gameOver) {
      startGameTracking(1);
    }
  }, [gameStarted, gameOver]);

  const startGameTracking = async (gameId) => {
    try {
      const response = await progressService.startGame(gameId);
      setGameProgress(response.data.progress);
    } catch (error) {
      console.error('Gagal memulakan penjejakan permainan:', error);
    }
  };

  useEffect(() => {
    if (gameOver) {
      saveGameProgress();
      setShowSummary(true);
    }
  }, [gameOver]);

  const saveGameProgress = async () => {
    try {
      const progressData = {
        score: score,
        level: level,
        time_spent: 120,
        completed: true,
        progress_data: {
          enemies_defeated: 0, // optional: track actual count
          powerups_collected: powerUps.length,
          level_reached: level,
          lives_remaining: lives
        }
      };

      const response = await progressService.saveProgress(1, progressData);
      setGameProgress(response.data.progress);
      
      if (response.data.rewards_unlocked && response.data.rewards_unlocked.length > 0) {
        setUnlockedRewards(response.data.rewards_unlocked);
      }
    } catch (error) {
      console.error('Gagal menyimpan kemajuan:', error);
    }
  };

  // Keyboard controls
  useEffect(() => {
    const handleKeyDown = (e) => {
      keysPressed.current[e.key] = true;
      if (e.key === ' ') {
        e.preventDefault();
        shoot();
      } else if (e.key === 'p' || e.key === 'P') {
        setIsPaused(prev => !prev);
      } else if (e.key === 'c' || e.key === 'C') {
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
  }, [shoot]);

  // Utils
  const startGame = () => setGameStarted(true);
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
    setShowSummary(false);
    setUnlockedRewards([]);
  };
  const togglePause = () => !gameOver && gameStarted && setIsPaused(p => !p);
  const returnToHome = () => window.location.reload();

  // UI
  if (!gameStarted) {
    return (
      <div style={{ 
        fontFamily: 'Arial, sans-serif', 
        textAlign: 'center', 
        backgroundColor: '#1a1a2e',
        color: '#fff',
        minHeight: '100vh',
        padding: '20px'
      }}>
        <h2 style={{ 
          fontSize: '2.5rem', 
          color: '#4ecca3',
          marginBottom: '20px'
        }}>🚀 Pengembaraan Angkasa</h2>
        
        <div style={{
          maxWidth: '600px',
          margin: '0 auto',
          padding: '30px',
          backgroundColor: '#0f3460',
          borderRadius: '15px'
        }}>
          <h3>Selamat Datang ke Pengembaraan Angkasa!</h3>
          <p>Pertahankan kapal angkasa anda daripada penceroboh asing.</p>
          
          <div style={{
            textAlign: 'left',
            marginTop: '20px',
            padding: '15px',
            backgroundColor: 'rgba(255,255,255,0.1)',
            borderRadius: '8px'
          }}>
            <h4>Panduan Permainan:</h4>
            <ul style={{ textAlign: 'left', paddingLeft: '20px' }}>
              <li>Gunakan kekunci <strong>Anak Panah Kiri/Kanan</strong> untuk bergerak</li>
              <li>Tekan <strong>Spacebar</strong> untuk menembak</li>
              <li>Tekan <strong>P</strong> untuk menjeda permainan</li>
              <li>Elakkan musuh dan tembak mereka untuk mendapat mata</li>
            </ul>
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
              marginTop: '20px'
            }}
            onClick={startGame}
          >
            Mulakan Permainan
          </button>
        </div>
      </div>
    );
  }

  return (
    <div style={{ 
      fontFamily: 'Arial, sans-serif', 
      textAlign: 'center', 
      backgroundColor: '#1a1a2e',
      color: '#fff',
      minHeight: '100vh',
      padding: '20px'
    }}>
      <h2 style={{ 
        fontSize: '2.5rem', 
        color: '#4ecca3',
        marginBottom: '20px'
      }}>🚀 Pengembaraan Angkasa</h2>
      
      <div style={{
        display: 'flex',
        justifyContent: 'space-around',
        marginBottom: '20px',
        padding: '10px',
        backgroundColor: '#16213e',
        borderRadius: '10px'
      }}>
        <div>Markah: {score}</div>
        <div>Markah Tertinggi: {highScore}</div>
        <div>Nyawa: {lives}</div>
        <div>Tahap: {level}</div>
      </div>
      
      <div 
        style={{
          width: '100%',
          height: '400px',
          backgroundColor: '#0d1b2a',
          border: '2px solid #4ecca3',
          position: 'relative',
          overflow: 'hidden',
          margin: '0 auto'
        }}
      >
        {/* Stars */}
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
              borderRadius: '50%'
            }}
          />
        ))}
        
        {/* Player */}
        <div 
          style={{
            position: 'absolute',
            left: `${playerPosition}%`,
            bottom: '20px',
            fontSize: '2rem',
            transform: 'translateX(-50%)' // Center player
          }}
        >
          🚀
        </div>
        
        {/* Enemies */}
        {enemies.map(enemy => (
          <div 
            key={enemy.id}
            style={{
              position: 'absolute',
              left: `${enemy.x}%`,
              top: `${enemy.y}%`,
              fontSize: '1.5rem',
              transform: 'translateX(-50%)'
            }}
          >
            👾
          </div>
        ))}
        
        {/* Bullets — now centered and consistent with hitbox */}
        {bullets.map(bullet => (
          <div 
            key={bullet.id}
            style={{
              position: 'absolute',
              left: `${bullet.x}%`,
              top: `${bullet.y}%`,
              width: '4px',
              height: '8px',
              backgroundColor: '#4ecca3',
              borderRadius: '2px',
              transform: 'translateX(-50%)',
              boxShadow: '0 0 4px rgba(78, 204, 163, 0.6)'
            }}
          />
        ))}
        
        {/* Explosions */}
        {explosions.map(explosion => (
          <div 
            key={explosion.id}
            style={{
              position: 'absolute',
              left: `${explosion.x}%`,
              top: `${explosion.y}%`,
              width: '20px',
              height: '20px',
              background: 'radial-gradient(circle, #ff5e5e, transparent)',
              borderRadius: '50%',
              transform: 'translate(-50%, -50%)',
              animation: 'explode 0.5s forwards'
            }}
          />
        ))}
      </div>
      
      <div style={{ marginTop: '20px' }}>
        <button 
          onClick={() => setPlayerPosition(prev => Math.max(2, prev - 8))}
          style={{ margin: '0 10px', padding: '10px 20px' }}
        >
          Kiri
        </button>
        <button 
          onClick={shoot}
          style={{ margin: '0 10px', padding: '10px 20px', backgroundColor: '#4CAF50' }}
        >
          Tembak
        </button>
        <button 
          onClick={() => setPlayerPosition(prev => Math.min(98, prev + 8))}
          style={{ margin: '0 10px', padding: '10px 20px' }}
        >
          Kanan
        </button>
      </div>
      
      <div style={{ marginTop: '20px' }}>
        <button 
          onClick={togglePause}
          style={{ margin: '0 10px', padding: '10px 20px' }}
        >
          {isPaused ? 'Sambung' : 'Jeda'}
        </button>
        <button 
          onClick={resetGame}
          style={{ margin: '0 10px', padding: '10px 20px', backgroundColor: '#f44336', color: 'white' }}
        >
          Mulakan Semula
        </button>
      </div>

      {/* Game Over Modal */}
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
          zIndex: 1000
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
            <h3 style={{ color: '#ff5252', fontSize: '2rem', marginBottom: '20px' }}>Permainan Tamat!</h3>
            <p style={{ fontSize: '1.2rem', marginBottom: '15px' }}>Markah Akhir: <span style={{ color: '#4ecca3', fontWeight: 'bold' }}>{score}</span></p>
            <p style={{ fontSize: '1.2rem', marginBottom: '15px' }}>Tahap Dicapai: <span style={{ color: '#4ecca3', fontWeight: 'bold' }}>{level}</span></p>
            
            {showSummary && (
              <>
                <GameSummary progress={gameProgress} game={{ name: 'Pertahanan Kosmik' }} />
                {unlockedRewards.length > 0 && (
                  <div style={{ marginTop: '20px' }}>
                    <RewardsDisplay 
                      rewards={unlockedRewards}
                      onClaim={(reward) => console.log('Ganjaran dituntut:', reward)}
                    />
                  </div>
                )}
              </>
            )}
            
            <div style={{ display: 'flex', justifyContent: 'center', gap: '15px', marginTop: '20px' }}>
              <button 
                style={{
                  padding: '12px 25px',
                  backgroundColor: '#4CAF50',
                  color: '#fff',
                  border: 'none',
                  borderRadius: '5px',
                  cursor: 'pointer',
                  fontWeight: 'bold',
                  fontSize: '1.1rem'
                }}
                onClick={() => {
                  resetGame();
                  setShowSummary(false);
                  setUnlockedRewards([]);
                }}
              >
                Main Semula
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
                  fontSize: '1.1rem'
                }}
                onClick={returnToHome}
              >
                Halaman Utama
              </button>
            </div>
          </div>
        </div>
      )}
      
      {/* Controls Help */}
      {showControls && (
        <div style={{
          position: 'fixed',
          top: '50%',
          left: '50%',
          transform: 'translate(-50%, -50%)',
          backgroundColor: '#0f3460',
          padding: '20px',
          borderRadius: '10px',
          border: '2px solid #4ecca3',
          maxWidth: '400px',
          width: '80%',
          zIndex: 1001
        }}>
          <h3>Panduan Kawalan</h3>
          <ul style={{ textAlign: 'left' }}>
            <li><strong>Anak Panah Kiri/Kanan</strong>: Gerakkan kapal</li>
            <li><strong>Spacebar</strong>: Tembak peluru</li>
            <li><strong>P</strong>: Jeda/Sambung permainan</li>
            <li><strong>C</strong>: Tunjukkan/Sembunyikan panduan ini</li>
          </ul>
          <button 
            onClick={() => setShowControls(false)}
            style={{ marginTop: '15px', padding: '8px 16px' }}
          >
            Tutup
          </button>
        </div>
      )}

      {/* Explosion animation */}
      <style jsx>{`
        @keyframes explode {
          0% { width: 20px; height: 20px; opacity: 1; }
          100% { width: 50px; height: 50px; opacity: 0; }
        }
      `}</style>
    </div>
  );
};

export default SpaceAdventure;