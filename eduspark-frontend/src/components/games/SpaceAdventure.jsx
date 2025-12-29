import React, { useState, useEffect, useCallback, useRef } from 'react';
import { progressService } from '../../services/progressService';
import GameSummary from './GameSummary';
import RewardsDisplay from './RewardsDisplay';
import Leaderboard from '../leaderboard/Leaderboard';

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
  const [showLeaderboard, setShowLeaderboard] = useState(false);
  const [enemiesDefeated, setEnemiesDefeated] = useState(0);
  const [gameSummaryData, setGameSummaryData] = useState(null);
  const [leaderboardData, setLeaderboardData] = useState(null);
  const [isLoadingSummary, setIsLoadingSummary] = useState(false);
  
  const gameAreaRef = useRef(null);
  const lastTimeRef = useRef(0);
  const animationFrameRef = useRef();
  const keysPressed = useRef({});
  const shootCooldownRef = useRef(false);
  const gameStartTimeRef = useRef(null);

  // Get CSRF Token from Laravel
  const getCsrfToken = () => {
    const metaTag = document.querySelector('meta[name="csrf-token"]');
    return metaTag ? metaTag.content : '';
  };

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
          y: -5,
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

  // ✅ FIXED: Reliable collision detection
  const checkCollisions = useCallback(() => {
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
      let enemiesDefeatedThisFrame = 0;

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

            if (bR > eL && bL < eR && bB > eT && bT < eB) {
              newEnemies[j] = { ...e, health: e.health - 1 };
              bulletsToRemove.push(i);

              if (newEnemies[j].health <= 0) {
                enemiesToRemove.push(j);
                enemiesDefeatedThisFrame++;
                pointsToAdd += (e.type === 'strong' ? 20 : 10);
                explosionsToAdd.push({
                  id: Date.now() + Math.random(),
                  x: e.x + ENEMY_W / 2,
                  y: e.y + ENEMY_H / 2
                });
              }
              break;
            }
          }
        }

        for (let i = enemiesToRemove.length - 1; i >= 0; i--) {
          newEnemies.splice(enemiesToRemove[i], 1);
        }

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

        if (enemiesDefeatedThisFrame > 0) {
          setEnemiesDefeated(prev => prev + enemiesDefeatedThisFrame);
        }

        if (explosionsToAdd.length > 0) {
          setExplosions(prev => [...prev, ...explosionsToAdd.map(e => ({...e, size: 0}))]);
        }

        return newEnemies;
      });

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

      if (keysPressed.current['ArrowLeft']) {
        setPlayerPosition(prev => Math.max(2, Math.min(98, prev - 0.7 * (deltaTime / 16))));
      }
      if (keysPressed.current['ArrowRight']) {
        setPlayerPosition(prev => Math.max(2, Math.min(98, prev + 0.7 * (deltaTime / 16))));
      }

      setEnemies(prev => 
        prev.map(e => ({ ...e, y: e.y + e.speed * (deltaTime / 16) }))
          .filter(e => e.y < 110)
      );

      setBullets(prev => 
        prev.map(b => ({ ...b, y: b.y - 10 * (deltaTime / 16) }))
          .filter(b => b.y > -5)
      );

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

  // ✅ FIXED: Shoot with cooldown
  const shoot = useCallback(() => {
    if (gameOver || isPaused || !gameStarted || shootCooldownRef.current) return;

    shootCooldownRef.current = true;
    setTimeout(() => { shootCooldownRef.current = false; }, 250);

    const newBullet = {
      id: Date.now() + Math.random(),
      x: playerPosition,
      y: 78
    };

    setBullets(prev => [...prev, newBullet]);
  }, [gameOver, isPaused, gameStarted, playerPosition]);

  // ========== FIXED: Save score to Laravel ==========
  const saveScoreToDatabase = async (finalScore, status) => {
    try {
      let playerId = localStorage.getItem('spaceGamePlayerId');
      if (!playerId) {
        playerId = 'player_' + Math.random().toString(36).substr(2, 9);
        localStorage.setItem('spaceGamePlayerId', playerId);
      }
      
      const gameId = 1;
      const timeTaken = gameStartTimeRef.current ? Math.floor((Date.now() - gameStartTimeRef.current) / 1000) : 0;
      
      console.log('Saving score to database...', {
        user_id: playerId,
        game_id: gameId,
        score: finalScore
      });
      
      // ✅ CORRECT ENDPOINT: Use Laravel API route
      const response = await fetch('/api/games/' + gameId + '/score', {
        method: 'POST',
        headers: { 
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': getCsrfToken()
        },
        body: JSON.stringify({
          user_id: playerId,
          game_id: gameId,
          score: finalScore,
          time_taken: timeTaken,
          game_stats: {
            status: status,
            level_reached: level,
            enemies_defeated: enemiesDefeated,
            lives_remaining: lives,
            powerups_collected: powerUps.length
          }
        })
      });
      
      console.log('Save response status:', response.status);
      
      if (!response.ok) {
        const errorText = await response.text();
        console.error('Failed to save score:', errorText);
        
        // Try fallback endpoint
        console.log('Trying fallback endpoint...');
        return await tryFallbackSave(gameId, playerId, finalScore, timeTaken, status);
      }
      
      const result = await response.json();
      console.log('Score saved successfully:', result);
      return result;
      
    } catch (error) {
      console.error('Error saving score:', error);
      return { success: false, message: error.message };
    }
  };

  // Fallback save method
  const tryFallbackSave = async (gameId, playerId, finalScore, timeTaken, status) => {
    try {
      const response = await fetch('/save-game-score', {
        method: 'POST',
        headers: { 
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          user_id: playerId,
          game_id: gameId,
          score: finalScore,
          time_taken: timeTaken,
          game_stats: {
            status: status,
            level_reached: level,
            enemies_defeated: enemiesDefeated,
            lives_remaining: lives,
            powerups_collected: powerUps.length
          }
        })
      });
      
      const result = await response.json();
      console.log('Fallback save result:', result);
      return result;
    } catch (error) {
      console.error('Fallback save also failed:', error);
      return { success: false, message: 'Both save methods failed' };
    }
  };

  // ========== FIXED: Get game summary from Laravel API ==========
  const loadGameSummary = async () => {
    setIsLoadingSummary(true);
    try {
      // First save the score
      const saveResult = await saveScoreToDatabase(score, 'selesai');
      
      if (!saveResult || !saveResult.success) {
        console.warn('Score save may have failed, but continuing...');
      }
      
      // Then get game summary
      const gameId = 1;
      let playerId = localStorage.getItem('spaceGamePlayerId');
      if (!playerId) {
        playerId = 'player_' + Math.random().toString(36).substr(2, 9);
        localStorage.setItem('spaceGamePlayerId', playerId);
      }
      
      console.log('Loading game summary for:', { gameId, playerId });
      
      // ✅ CORRECT ENDPOINT: Use the API route from web.php
      const url = `/api/game-summary/${gameId}?user_id=${playerId}`;
      console.log('Fetching from:', url);
      
      const response = await fetch(url, {
        credentials: 'include',
        headers: {
          'Accept': 'application/json'
        }
      });
      
      console.log('Summary response status:', response.status);
      
      if (!response.ok) {
        throw new Error(`HTTP ${response.status}: ${await response.text()}`);
      }
      
      const data = await response.json();
      console.log('Game summary data received:', data);
      
      if (data.success) {
        setGameSummaryData(data.summary);
      } else {
        console.warn('API returned success:false', data.message);
        // Create local summary as fallback
        createLocalSummary();
      }
    } catch (error) {
      console.error('Error loading game summary:', error);
      // Create local summary as fallback
      createLocalSummary();
    } finally {
      setIsLoadingSummary(false);
    }
  };

  // Create local summary when API fails
  const createLocalSummary = () => {
    const timeTaken = gameStartTimeRef.current ? Math.floor((Date.now() - gameStartTimeRef.current) / 1000) : 0;
    const xpEarned = Math.floor(score / 10);
    const coinsEarned = Math.floor(score / 100);
    
    setGameSummaryData({
      score: score,
      time_taken: timeTaken,
      rank: 1,
      total_players: 1,
      accuracy: Math.min(100, Math.floor((enemiesDefeated / (enemiesDefeated + 5)) * 100)),
      rewards: [
        {
          type: 'xp',
          name: 'Experience Points',
          description: 'Dasar pengalaman bermain',
          amount: xpEarned,
          icon: '⭐'
        },
        {
          type: 'coins',
          name: 'Koin',
          description: 'Mata wang dalam permainan',
          amount: coinsEarned,
          icon: '🪙'
        },
        ...(score >= 500 ? [{
          type: 'achievement',
          name: 'Pemain Cemerlang',
          description: 'Mencapai 500 mata',
          badge: 'bronze',
          icon: '🎯'
        }] : []),
        ...(score >= 800 ? [{
          type: 'achievement',
          name: 'Pemain Mahir',
          description: 'Mencapai 800 mata',
          badge: 'silver',
          icon: '⭐'
        }] : []),
        ...(score >= 1000 ? [{
          type: 'achievement',
          name: 'Master Pemain',
          description: 'Mencapai 1000 mata',
          badge: 'gold',
          icon: '🏆'
        }] : [])
      ].filter(reward => !(reward.type === 'achievement' && !reward.name)),
      game_title: 'Pertahanan Kosmik',
      game_id: 1,
      user_name: 'Pemain',
      xp_earned: xpEarned,
      coins_earned: coinsEarned
    });
  };

  // ========== FIXED: Load leaderboard data ==========
  const loadLeaderboard = async () => {
    try {
      const gameId = 1;
      console.log('Loading leaderboard for game:', gameId);
      
      // ✅ CORRECT ENDPOINT
      const response = await fetch(`/api/leaderboard/${gameId}`, {
        credentials: 'include',
        headers: {
          'Accept': 'application/json'
        }
      });
      
      console.log('Leaderboard response status:', response.status);
      
      if (!response.ok) {
        throw new Error(`HTTP ${response.status}`);
      }
      
      const data = await response.json();
      console.log('Leaderboard data received:', data);
      
      if (data.success) {
        setLeaderboardData(data);
      }
    } catch (error) {
      console.error('Error loading leaderboard:', error);
      // Create mock leaderboard for testing
      setLeaderboardData({
        success: true,
        leaderboard: [
          { rank: 1, user_name: 'Ali', score: 1200, time_taken: 180, is_current_user: false },
          { rank: 2, user_name: 'Siti', score: 1100, time_taken: 200, is_current_user: false },
          { rank: 3, user_name: 'Ahmad', score: 1050, time_taken: 220, is_current_user: false },
          { rank: 4, user_name: 'Pemain', score: score, time_taken: gameStartTimeRef.current ? Math.floor((Date.now() - gameStartTimeRef.current) / 1000) : 0, is_current_user: true },
          { rank: 5, user_name: 'Muthu', score: 900, time_taken: 250, is_current_user: false }
        ],
        user_rank: 4,
        user_score: score,
        user_time: gameStartTimeRef.current ? Math.floor((Date.now() - gameStartTimeRef.current) / 1000) : 0,
        total_players: 5,
        game_id: 1,
        game_title: 'Pertahanan Kosmik'
      });
    }
  };

  // ========== FIXED: Collect rewards via Laravel API ==========
  const collectRewards = async () => {
    try {
      const response = await fetch('/api/rewards/collect', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': getCsrfToken()
        },
        credentials: 'include',
        body: JSON.stringify({
          game_id: 1,
          score: score,
          score_id: gameSummaryData?.score_id || Date.now()
        })
      });
      
      if (!response.ok) {
        throw new Error(`HTTP ${response.status}`);
      }
      
      const data = await response.json();
      console.log('Rewards collection response:', data);
      
      if (data.success) {
        alert('🎉 Anugerah berjaya dikumpul!');
        if (gameSummaryData) {
          setGameSummaryData(prev => ({
            ...prev,
            rewards: []
          }));
        }
      } else {
        alert('Tiada anugerah untuk dikumpul.');
      }
    } catch (error) {
      console.error('Error collecting rewards:', error);
      alert('Gagal mengumpul anugerah. Anugerah anda masih selamat.');
    }
  };

  // Submit to leaderboard
  const submitToLeaderboard = async (finalScore) => {
    const userData = localStorage.getItem('user');
    let user = null;
    
    try {
      if (userData) {
        user = JSON.parse(userData);
      }
    } catch (e) {
      console.warn('Failed to parse user data');
    }
    
    if (!user || !user.id) {
      console.warn('User not authenticated — skipping leaderboard');
      return;
    }

    try {
      const timeTaken = gameStartTimeRef.current ? Math.floor((Date.now() - gameStartTimeRef.current) / 1000) : 0;
      
      const response = await fetch('/api/leaderboard', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': getCsrfToken()
        },
        body: JSON.stringify({
          user_id: user.id,
          username: user.name || 'Anonymous',
          class: user.class || 'Unknown',
          game_id: 'game1',
          score: finalScore,
          time_taken: timeTaken,
          level_reached: level,
          enemies_defeated: enemiesDefeated
        })
      });

      if (!response.ok) {
        const err = await response.json().catch(() => ({}));
        throw new Error(err.error || `HTTP ${response.status}`);
      }

      console.log('Score submitted to leaderboard');
    } catch (error) {
      console.error('Leaderboard submission failed:', error.message);
    }
  };

  // Tracking & progress
  useEffect(() => {
    if (gameStarted && !gameOver) {
      startGameTracking(1);
      gameStartTimeRef.current = Date.now();
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

  // ========== UPDATED: Game over effect ==========
  useEffect(() => {
    if (gameOver) {
      console.log('Game over! Starting post-game sequence...');
      
      // Save score and load summary
      const postGameSequence = async () => {
        await saveScoreToDatabase(score, 'selesai');
        await submitToLeaderboard(score);
        await saveGameProgress();
        await loadGameSummary();
        await loadLeaderboard();
        
        // Show summary after a short delay
        setTimeout(() => {
          setShowSummary(true);
        }, 800);
      };
      
      postGameSequence();
    }
  }, [gameOver]);

  const saveGameProgress = async () => {
    try {
      const timeSpent = gameStartTimeRef.current ? Math.floor((Date.now() - gameStartTimeRef.current) / 1000) : 0;
      
      const progressData = {
        score: score,
        level: level,
        time_spent: timeSpent,
        completed: true,
        progress_data: {
          enemies_defeated: enemiesDefeated,
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
  const startGame = () => {
    setGameStarted(true);
    gameStartTimeRef.current = Date.now();
    setEnemiesDefeated(0);
  };
  
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
    setShowLeaderboard(false);
    setUnlockedRewards([]);
    setEnemiesDefeated(0);
    setGameSummaryData(null);
    setLeaderboardData(null);
    gameStartTimeRef.current = Date.now();
  };
  
  const togglePause = () => !gameOver && gameStarted && setIsPaused(p => !p);
  const returnToHome = () => window.location.reload();

  // ========== Custom Game Summary Modal ==========
  const GameSummaryModal = () => {
    if (!gameSummaryData || !showSummary) return null;

    return (
      <div style={{
        position: 'fixed',
        top: '50%',
        left: '50%',
        transform: 'translate(-50%, -50%)',
        backgroundColor: '#0f3460',
        padding: '30px',
        borderRadius: '16px',
        border: '3px solid #4ecca3',
        maxWidth: '600px',
        width: '95%',
        zIndex: 2000,
        boxShadow: '0 0 30px rgba(78, 204, 163, 0.7)',
        color: 'white'
      }}>
        <h2 style={{ 
          fontSize: '2.2rem', 
          color: '#4ecca3',
          textAlign: 'center',
          marginBottom: '25px',
          textShadow: '0 0 8px rgba(78, 204, 163, 0.8)'
        }}>🎮 Permainan Tamat!</h2>
        
        {isLoadingSummary ? (
          <div style={{ textAlign: 'center', padding: '40px' }}>
            <div style={{
              width: '50px',
              height: '50px',
              border: '4px solid #4ecca3',
              borderTop: '4px solid transparent',
              borderRadius: '50%',
              animation: 'spin 1s linear infinite',
              margin: '0 auto'
            }} />
            <p style={{ fontSize: '1.2rem', marginTop: '20px' }}>Memuatkan ringkasan permainan...</p>
          </div>
        ) : (
          <>
            {/* Score Display */}
            <div style={{ 
              display: 'flex', 
              justifyContent: 'center', 
              alignItems: 'center',
              marginBottom: '25px'
            }}>
              <div style={{
                width: '180px',
                height: '180px',
                borderRadius: '50%',
                background: 'white',
                color: '#333',
                display: 'flex',
                flexDirection: 'column',
                justifyContent: 'center',
                alignItems: 'center',
                boxShadow: '0 15px 35px rgba(0,0,0,0.3)'
              }}>
                <span style={{ 
                  fontSize: '64px', 
                  fontWeight: 'bold',
                  color: '#4a5568'
                }}>
                  {gameSummaryData.score}
                </span>
                <span style={{ 
                  fontSize: '16px', 
                  color: '#718096',
                  textTransform: 'uppercase',
                  letterSpacing: '1px'
                }}>
                  MATA
                </span>
              </div>
            </div>
            
            {/* Score Details */}
            <div style={{
              display: 'grid',
              gridTemplateColumns: 'repeat(2, 1fr)',
              gap: '15px',
              margin: '25px 0'
            }}>
              <div style={{
                textAlign: 'center',
                padding: '15px',
                background: 'rgba(255,255,255,0.1)',
                borderRadius: '10px',
                backdropFilter: 'blur(10px)'
              }}>
                <div style={{ fontSize: '14px', opacity: '0.9', marginBottom: '5px' }}>
                  Kedudukan
                </div>
                <div style={{ fontSize: '24px', fontWeight: 'bold', color: '#FFD700' }}>
                  #{gameSummaryData.rank}
                </div>
              </div>
              
              <div style={{
                textAlign: 'center',
                padding: '15px',
                background: 'rgba(255,255,255,0.1)',
                borderRadius: '10px',
                backdropFilter: 'blur(10px)'
              }}>
                <div style={{ fontSize: '14px', opacity: '0.9', marginBottom: '5px' }}>
                  Masa
                </div>
                <div style={{ fontSize: '24px', fontWeight: 'bold', color: '#4ecca3' }}>
                  {gameSummaryData.time_taken}s
                </div>
              </div>
              
              <div style={{
                textAlign: 'center',
                padding: '15px',
                background: 'rgba(255,255,255,0.1)',
                borderRadius: '10px',
                backdropFilter: 'blur(10px)'
              }}>
                <div style={{ fontSize: '14px', opacity: '0.9', marginBottom: '5px' }}>
                  Ketepatan
                </div>
                <div style={{ fontSize: '24px', fontWeight: 'bold', color: '#2196F3' }}>
                  {gameSummaryData.accuracy}%
                </div>
              </div>
              
              <div style={{
                textAlign: 'center',
                padding: '15px',
                background: 'rgba(255,255,255,0.1)',
                borderRadius: '10px',
                backdropFilter: 'blur(10px)'
              }}>
                <div style={{ fontSize: '14px', opacity: '0.9', marginBottom: '5px' }}>
                  Pemain
                </div>
                <div style={{ fontSize: '24px', fontWeight: 'bold', color: '#9C27B0' }}>
                  {gameSummaryData.total_players}
                </div>
              </div>
            </div>
            
            {/* Rewards Section */}
            {gameSummaryData.rewards && gameSummaryData.rewards.length > 0 && (
              <div style={{ marginTop: '25px' }}>
                <h3 style={{ 
                  fontSize: '1.5rem', 
                  color: '#FFD700',
                  textAlign: 'center',
                  marginBottom: '15px'
                }}>
                  🎁 Anugerah Diperolehi!
                </h3>
                
                <div style={{
                  display: 'grid',
                  gridTemplateColumns: 'repeat(auto-fit, minmax(250px, 1fr))',
                  gap: '15px',
                  marginBottom: '20px'
                }}>
                  {gameSummaryData.rewards.map((reward, index) => (
                    <div 
                      key={index}
                      style={{
                        background: 'rgba(255,255,255,0.15)',
                        borderRadius: '12px',
                        padding: '15px',
                        display: 'flex',
                        alignItems: 'center',
                        gap: '15px',
                        transition: 'transform 0.3s'
                      }}
                    >
                      <div style={{ fontSize: '30px' }}>{reward.icon}</div>
                      <div style={{ textAlign: 'left', flex: 1 }}>
                        <h4 style={{ margin: '0 0 5px 0', fontSize: '16px' }}>{reward.name}</h4>
                        <p style={{ margin: '0 0 8px 0', fontSize: '14px', opacity: '0.9' }}>
                          {reward.description}
                        </p>
                        {reward.amount && (
                          <span style={{ color: '#FFD700', fontWeight: 'bold' }}>
                            +{reward.amount} {reward.type === 'xp' ? 'XP' : reward.type === 'coins' ? 'Koin' : ''}
                          </span>
                        )}
                      </div>
                    </div>
                  ))}
                </div>
                
                {gameSummaryData.rewards.length > 0 && (
                  <button 
                    onClick={collectRewards}
                    style={{
                      width: '100%',
                      padding: '15px',
                      background: 'linear-gradient(to right, #FF9800, #F57C00)',
                      color: 'white',
                      border: 'none',
                      borderRadius: '10px',
                      cursor: 'pointer',
                      fontWeight: 'bold',
                      fontSize: '1.1rem',
                      marginTop: '10px',
                      transition: 'all 0.3s'
                    }}
                  >
                    Kumpul Semua Anugerah
                  </button>
                )}
              </div>
            )}
            
            {/* XP & Coins Earned */}
            {(gameSummaryData.xp_earned > 0 || gameSummaryData.coins_earned > 0) && (
              <div style={{
                marginTop: '20px',
                padding: '15px',
                background: 'rgba(78, 204, 163, 0.1)',
                borderRadius: '10px',
                border: '1px solid rgba(78, 204, 163, 0.3)'
              }}>
                <div style={{ display: 'flex', justifyContent: 'space-around' }}>
                  {gameSummaryData.xp_earned > 0 && (
                    <div style={{ textAlign: 'center' }}>
                      <div style={{ fontSize: '24px', color: '#4ecca3' }}>+{gameSummaryData.xp_earned}</div>
                      <div style={{ fontSize: '14px', color: '#aaa' }}>XP</div>
                    </div>
                  )}
                  {gameSummaryData.coins_earned > 0 && (
                    <div style={{ textAlign: 'center' }}>
                      <div style={{ fontSize: '24px', color: '#FFD700' }}>+{gameSummaryData.coins_earned}</div>
                      <div style={{ fontSize: '14px', color: '#aaa' }}>Koin</div>
                    </div>
                  )}
                </div>
              </div>
            )}
            
            {/* Action Buttons */}
            <div style={{ 
              display: 'flex', 
              flexDirection: 'column', 
              gap: '12px', 
              marginTop: '30px' 
            }}>
              <button 
                style={{
                  padding: '15px 25px',
                  background: 'linear-gradient(to right, #4CAF50, #45a049)',
                  color: 'white',
                  border: 'none',
                  borderRadius: '10px',
                  cursor: 'pointer',
                  fontWeight: 'bold',
                  fontSize: '1.1rem',
                  transition: 'all 0.3s'
                }}
                onClick={() => {
                  resetGame();
                  setShowSummary(false);
                }}
              >
                🔄 Main Semula
              </button>
              
              <button 
                style={{
                  padding: '15px 25px',
                  background: 'linear-gradient(to right, #2196F3, #1976D2)',
                  color: 'white',
                  border: 'none',
                  borderRadius: '10px',
                  cursor: 'pointer',
                  fontWeight: 'bold',
                  fontSize: '1.1rem',
                  transition: 'all 0.3s'
                }}
                onClick={() => {
                  setShowSummary(false);
                  setShowLeaderboard(true);
                }}
              >
                🏆 Papan Pemimpin
              </button>
              
              <button 
                style={{
                  padding: '15px 25px',
                  background: 'linear-gradient(to right, #9C27B0, #7B1FA2)',
                  color: 'white',
                  border: 'none',
                  borderRadius: '10px',
                  cursor: 'pointer',
                  fontWeight: 'bold',
                  fontSize: '1.1rem',
                  transition: 'all 0.3s'
                }}
                onClick={returnToHome}
              >
                ← Kembali ke Menu
              </button>
            </div>
          </>
        )}
      </div>
    );
  };

  // ========== Custom Leaderboard Modal ==========
  const LeaderboardModal = () => {
    if (!showLeaderboard) return null;

    return (
      <div style={{
        position: 'fixed',
        top: 0,
        left: 0,
        width: '100%',
        height: '100%',
        backgroundColor: 'rgba(0, 0, 0, 0.95)',
        display: 'flex',
        justifyContent: 'center',
        alignItems: 'center',
        zIndex: 2000,
        padding: '20px'
      }}>
        <div style={{ 
          width: '95%', 
          maxWidth: '800px',
          maxHeight: '85vh',
          overflowY: 'auto',
          borderRadius: '16px',
          backgroundColor: '#0f3460',
          border: '3px solid #4ecca3',
          padding: '25px',
          color: 'white'
        }}>
          <div style={{ 
            display: 'flex', 
            justifyContent: 'space-between', 
            alignItems: 'center',
            marginBottom: '20px'
          }}>
            <h2 style={{ 
              fontSize: '2rem', 
              color: '#4ecca3',
              margin: 0
            }}>
              🏆 Papan Pemimpin
            </h2>
            <button 
              onClick={() => setShowLeaderboard(false)}
              style={{
                padding: '8px 16px',
                backgroundColor: '#f44336',
                color: 'white',
                border: 'none',
                borderRadius: '6px',
                cursor: 'pointer',
                fontWeight: 'bold'
              }}
            >
              ✕ Tutup
            </button>
          </div>
          
          {leaderboardData ? (
            <>
              {leaderboardData.user_rank && leaderboardData.user_rank > 10 && (
                <div style={{
                  background: '#e3f2fd',
                  padding: '15px',
                  borderRadius: '10px',
                  marginBottom: '20px',
                  textAlign: 'center',
                  color: '#1976D2'
                }}>
                  <h3 style={{ margin: '0 0 5px 0' }}>Kedudukan Anda: #{leaderboardData.user_rank}</h3>
                  <p style={{ margin: '0', fontSize: '0.9rem' }}>
                    Skor: {leaderboardData.user_score} mata | Masa: {leaderboardData.user_time}s
                  </p>
                </div>
              )}
              
              <div style={{ overflowX: 'auto' }}>
                <table style={{
                  width: '100%',
                  borderCollapse: 'collapse',
                  backgroundColor: 'rgba(255,255,255,0.05)',
                  borderRadius: '8px',
                  overflow: 'hidden'
                }}>
                  <thead>
                    <tr style={{ background: 'rgba(78, 204, 163, 0.2)' }}>
                      <th style={{ padding: '15px', textAlign: 'left', borderBottom: '2px solid #4ecca3' }}>
                        Kedudukan
                      </th>
                      <th style={{ padding: '15px', textAlign: 'left', borderBottom: '2px solid #4ecca3' }}>
                        Nama
                      </th>
                      <th style={{ padding: '15px', textAlign: 'left', borderBottom: '2px solid #4ecca3' }}>
                        Skor
                      </th>
                      <th style={{ padding: '15px', textAlign: 'left', borderBottom: '2px solid #4ecca3' }}>
                        Masa
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    {leaderboardData.leaderboard && leaderboardData.leaderboard.length > 0 ? (
                      leaderboardData.leaderboard.map((entry, index) => (
                        <tr 
                          key={index}
                          style={{
                            background: entry.is_current_user ? 'rgba(255, 215, 0, 0.15)' : 'transparent',
                            borderBottom: '1px solid rgba(255,255,255,0.1)'
                          }}
                        >
                          <td style={{ padding: '15px' }}>
                            <strong style={{ 
                              color: entry.rank <= 3 ? 
                                ['#FFD700', '#C0C0C0', '#CD7F32'][entry.rank-1] : '#666' 
                            }}>
                              {entry.rank <= 3 ? ['🥇', '🥈', '🥉'][entry.rank-1] : ''} #{entry.rank}
                            </strong>
                          </td>
                          <td style={{ padding: '15px' }}>
                            {entry.user_name} {entry.is_current_user ? ' (Anda)' : ''}
                          </td>
                          <td style={{ padding: '15px', fontWeight: 'bold' }}>
                            {entry.score}
                          </td>
                          <td style={{ padding: '15px' }}>
                            {entry.time_taken}s
                          </td>
                        </tr>
                      ))
                    ) : (
                      <tr>
                        <td colSpan="4" style={{ padding: '30px', textAlign: 'center', color: '#aaa' }}>
                          Tiada data papan pemimpin untuk permainan ini.
                        </td>
                      </tr>
                    )}
                  </tbody>
                </table>
              </div>
              
              <div style={{ marginTop: '20px', textAlign: 'center', color: '#aaa', fontSize: '0.9rem' }}>
                Jumlah Pemain: {leaderboardData.total_players || 0}
              </div>
            </>
          ) : (
            <div style={{ textAlign: 'center', padding: '40px' }}>
              <div style={{
                width: '40px',
                height: '40px',
                border: '3px solid #4ecca3',
                borderTop: '3px solid transparent',
                borderRadius: '50%',
                animation: 'spin 1s linear infinite',
                margin: '0 auto 20px'
              }} />
              <p>Memuatkan data papan pemimpin...</p>
            </div>
          )}
        </div>
      </div>
    );
  };

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
        }}>🚀 Pertahanan Kosmik</h2>
        
        <div style={{
          maxWidth: '600px',
          margin: '0 auto',
          padding: '30px',
          backgroundColor: '#0f3460',
          borderRadius: '15px',
          border: '2px solid #4ecca3',
          boxShadow: '0 0 20px rgba(78, 204, 163, 0.5)'
        }}>
          <h3 style={{ fontSize: '1.8rem', marginBottom: '15px', color: '#4ecca3' }}>Selamat Datang ke Pertahanan Kosmik!</h3>
          <p style={{ fontSize: '1.2rem', marginBottom: '20px' }}>Pertahankan kapal angkasa anda daripada penceroboh asing.</p>
          
          <div style={{
            textAlign: 'left',
            marginTop: '20px',
            padding: '15px',
            backgroundColor: 'rgba(255,255,255,0.1)',
            borderRadius: '8px',
            border: '1px solid rgba(78, 204, 163, 0.3)'
          }}>
            <h4 style={{ color: '#4ecca3', marginBottom: '10px' }}>Panduan Permainan:</h4>
            <ul style={{ textAlign: 'left', paddingLeft: '20px', fontSize: '1.1rem' }}>
              <li>Gunakan kekunci <strong>Anak Panah Kiri/Kanan</strong> untuk bergerak</li>
              <li>Tekan <strong>Spacebar</strong> untuk menembak</li>
              <li>Tekan <strong>P</strong> untuk menjeda permainan</li>
              <li>Tekan <strong>C</strong> untuk menunjukkan panduan</li>
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
              marginTop: '20px',
              fontWeight: 'bold',
              transition: 'all 0.3s',
              boxShadow: '0 0 12px rgba(76, 175, 80, 0.6)'
            }}
            onClick={startGame}
          >
            ▶ Mulakan Permainan
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
        marginBottom: '20px',
        textShadow: '0 0 8px rgba(78, 204, 163, 0.7)'
      }}>🚀 Pertahanan Kosmik</h2>
      
      <div style={{
        display: 'flex',
        justifyContent: 'space-around',
        flexWrap: 'wrap',
        marginBottom: '20px',
        padding: '12px',
        backgroundColor: '#16213e',
        borderRadius: '12px',
        border: '2px solid #4ecca3',
        fontSize: '1.1rem',
        gap: '15px'
      }}>
        <div><strong>Markah:</strong> <span style={{ color: '#FFD700' }}>{score}</span></div>
        <div><strong>Markah Tertinggi:</strong> <span style={{ color: '#4ecca3' }}>{highScore}</span></div>
        <div><strong>Nyawa:</strong> <span style={{ color: lives > 1 ? '#4CAF50' : '#FF5252' }}>{lives} ❤️</span></div>
        <div><strong>Tahap:</strong> <span style={{ color: '#9C27B0' }}>{level}</span></div>
        <div><strong>Musuh Ditumpaskan:</strong> <span style={{ color: '#2196F3' }}>{enemiesDefeated}</span></div>
      </div>
      
      <div 
        style={{
          width: '100%',
          height: '400px',
          backgroundColor: '#0d1b2a',
          border: '2px solid #4ecca3',
          position: 'relative',
          overflow: 'hidden',
          margin: '0 auto',
          borderRadius: '8px',
          boxShadow: '0 0 15px rgba(78, 204, 163, 0.4)'
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
              borderRadius: '50%',
              boxShadow: '0 0 3px rgba(255, 255, 255, 0.8)'
            }}
          />
        ))}
        
        {/* Player */}
        <div 
          style={{
            position: 'absolute',
            left: `${playerPosition}%`,
            bottom: '20px',
            fontSize: '2.5rem',
            transform: 'translateX(-50%)',
            filter: 'drop-shadow(0 0 5px rgba(78, 204, 163, 0.8))',
            zIndex: 10
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
              fontSize: '1.8rem',
              transform: 'translateX(-50%)',
              filter: enemy.type === 'strong' ? 'drop-shadow(0 0 3px #FF5252)' : 'drop-shadow(0 0 3px #4ecca3)',
              color: enemy.type === 'strong' ? '#FF5252' : '#4ecca3'
            }}
          >
            {enemy.type === 'strong' ? '👹' : '👾'}
          </div>
        ))}
        
        {/* Bullets */}
        {bullets.map(bullet => (
          <div 
            key={bullet.id}
            style={{
              position: 'absolute',
              left: `${bullet.x}%`,
              top: `${bullet.y}%`,
              width: '4px',
              height: '12px',
              backgroundColor: '#4ecca3',
              borderRadius: '2px',
              transform: 'translateX(-50%)',
              boxShadow: '0 0 8px rgba(78, 204, 163, 0.8)',
              zIndex: 5
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
              background: 'radial-gradient(circle, #FFD700, #FF5252, transparent)',
              borderRadius: '50%',
              transform: 'translate(-50%, -50%)',
              animation: 'explode 0.5s forwards',
              zIndex: 15
            }}
          />
        ))}
      </div>
      
      <div style={{ marginTop: '20px', display: 'flex', justifyContent: 'center', gap: '10px', flexWrap: 'wrap' }}>
        <button 
          onClick={() => setPlayerPosition(prev => Math.max(2, prev - 8))}
          style={{ 
            margin: '5px', 
            padding: '10px 20px', 
            backgroundColor: '#2196F3',
            color: 'white',
            border: 'none',
            borderRadius: '8px',
            cursor: 'pointer',
            fontWeight: 'bold',
            fontSize: '1.1rem'
          }}
        >
          ← Kiri
        </button>
        <button 
          onClick={shoot}
          style={{ 
            margin: '5px', 
            padding: '10px 20px', 
            backgroundColor: '#4CAF50',
            color: 'white',
            border: 'none',
            borderRadius: '8px',
            cursor: 'pointer',
            fontWeight: 'bold',
            fontSize: '1.1rem',
            boxShadow: '0 0 8px rgba(76, 175, 80, 0.6)'
          }}
        >
          🔫 Tembak
        </button>
        <button 
          onClick={() => setPlayerPosition(prev => Math.min(98, prev + 8))}
          style={{ 
            margin: '5px', 
            padding: '10px 20px', 
            backgroundColor: '#2196F3',
            color: 'white',
            border: 'none',
            borderRadius: '8px',
            cursor: 'pointer',
            fontWeight: 'bold',
            fontSize: '1.1rem'
          }}
        >
          Kanan →
        </button>
      </div>
      
      <div style={{ marginTop: '20px', display: 'flex', justifyContent: 'center', gap: '10px', flexWrap: 'wrap' }}>
        <button 
          onClick={togglePause}
          style={{ 
            margin: '5px', 
            padding: '10px 20px', 
            backgroundColor: isPaused ? '#4CAF50' : '#FF9800',
            color: 'white',
            border: 'none',
            borderRadius: '8px',
            cursor: 'pointer',
            fontWeight: 'bold',
            fontSize: '1.1rem'
          }}
        >
          {isPaused ? '▶ Sambung' : '⏸️ Jeda'}
        </button>
        <button 
          onClick={resetGame}
          style={{ 
            margin: '5px', 
            padding: '10px 20px', 
            backgroundColor: '#f44336',
            color: 'white',
            border: 'none',
            borderRadius: '8px',
            cursor: 'pointer',
            fontWeight: 'bold',
            fontSize: '1.1rem'
          }}
        >
          🔄 Mulakan Semula
        </button>
        <button 
          onClick={() => setShowControls(true)}
          style={{ 
            margin: '5px', 
            padding: '10px 20px', 
            backgroundColor: '#9C27B0',
            color: 'white',
            border: 'none',
            borderRadius: '8px',
            cursor: 'pointer',
            fontWeight: 'bold',
            fontSize: '1.1rem'
          }}
        >
          📖 Panduan
        </button>
      </div>

      {/* Game Over Modal */}
      {gameOver && !showSummary && (
        <div style={{
          position: 'fixed',
          top: 0,
          left: 0,
          width: '100%',
          height: '100%',
          backgroundColor: 'rgba(0, 0, 0, 0.9)',
          display: 'flex',
          justifyContent: 'center',
          alignItems: 'center',
          zIndex: 1000
        }}>
          <div style={{
            backgroundColor: '#0f3460',
            padding: '30px',
            borderRadius: '12px',
            textAlign: 'center',
            border: '2px solid #4ecca3',
            maxWidth: '500px',
            width: '90%',
            boxShadow: '0 0 25px rgba(78, 204, 163, 0.7)'
          }}>
            <h3 style={{ color: '#ff5252', fontSize: '2.2rem', marginBottom: '20px', textShadow: '0 0 6px rgba(255, 82, 82, 0.8)' }}>
              🛑 Permainan Tamat!
            </h3>
            <p style={{ fontSize: '1.3rem', marginBottom: '12px', color: '#FFD700' }}>
              Markah Akhir: <strong style={{ color: '#4ecca3', fontSize: '1.4rem' }}>{score}</strong>
            </p>
            <p style={{ fontSize: '1.1rem', marginBottom: '20px' }}>
              Memuatkan ringkasan permainan...
            </p>
            {isLoadingSummary && (
              <div style={{ margin: '20px 0' }}>
                <div style={{
                  width: '40px',
                  height: '40px',
                  border: '3px solid #4ecca3',
                  borderTop: '3px solid transparent',
                  borderRadius: '50%',
                  animation: 'spin 1s linear infinite',
                  margin: '0 auto'
                }} />
              </div>
            )}
          </div>
        </div>
      )}

      {/* Game Summary Modal */}
      <GameSummaryModal />
      
      {/* Leaderboard Modal */}
      <LeaderboardModal />

      {/* Controls Help */}
      {showControls && (
        <div style={{
          position: 'fixed',
          top: '50%',
          left: '50%',
          transform: 'translate(-50%, -50%)',
          backgroundColor: '#0f3460',
          padding: '25px',
          borderRadius: '12px',
          border: '2px solid #4ecca3',
          maxWidth: '400px',
          width: '90%',
          zIndex: 1001,
          boxShadow: '0 0 20px rgba(0, 0, 0, 0.8)'
        }}>
          <h3 style={{ color: '#4ecca3', fontSize: '1.8rem', marginBottom: '15px' }}>📖 Panduan Kawalan</h3>
          <ul style={{ textAlign: 'left', fontSize: '1.1rem', lineHeight: '1.6' }}>
            <li><strong>Anak Panah Kiri/Kanan</strong>: Gerakkan kapal</li>
            <li><strong>Spacebar</strong>: Tembak peluru</li>
            <li><strong>P</strong>: Jeda/Sambung permainan</li>
            <li><strong>C</strong>: Tunjukkan/Sembunyikan panduan ini</li>
          </ul>
          <button 
            onClick={() => setShowControls(false)}
            style={{ 
              marginTop: '20px', 
              padding: '10px 25px', 
              backgroundColor: '#9C27B0',
              color: 'white',
              border: 'none',
              borderRadius: '8px',
              cursor: 'pointer',
              fontWeight: 'bold',
              fontSize: '1.1rem'
            }}
          >
            Tutup
          </button>
        </div>
      )}

      {/* CSS Animations */}
      <style jsx>{`
        @keyframes explode {
          0% { width: 20px; height: 20px; opacity: 1; }
          100% { width: 50px; height: 50px; opacity: 0; }
        }
        
        @keyframes spin {
          0% { transform: rotate(0deg); }
          100% { transform: rotate(360deg); }
        }
        
        button:hover {
          transform: translateY(-2px);
          box-shadow: 0 4px 12px rgba(255, 255, 255, 0.2);
        }
        
        button:active {
          transform: translateY(0);
        }
      `}</style>
    </div>
  );
};

export default SpaceAdventure;