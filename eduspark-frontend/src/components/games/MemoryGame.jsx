import React, { useState, useEffect } from 'react';
import { progressService } from '../../services/progressService';
import GameSummary from './GameSummary';
import RewardsDisplay from './RewardsDisplay';
import Leaderboard from '../leaderboard/Leaderboard';

const MemoryGame = () => {
  const [cards, setCards] = useState([]);
  const [flipped, setFlipped] = useState([]);
  const [solved, setSolved] = useState([]);
  const [moves, setMoves] = useState(0);
  const [gameWon, setGameWon] = useState(false);
  const [difficulty, setDifficulty] = useState('easy');
  const [time, setTime] = useState(0);
  const [timerActive, setTimerActive] = useState(false);
  const [matches, setMatches] = useState(0);
  const [gameStarted, setGameStarted] = useState(false);
  const [showInstructions, setShowInstructions] = useState(false);
  const [gameProgress, setGameProgress] = useState(null);
  const [unlockedRewards, setUnlockedRewards] = useState([]);
  const [showSummary, setShowSummary] = useState(false);
  const [startTime, setStartTime] = useState(null);
  const [emojis, setEmojis] = useState([]);
  const [showLeaderboard, setShowLeaderboard] = useState(false);
  const [finalScore, setFinalScore] = useState(0);
  const [gameSummaryData, setGameSummaryData] = useState(null);
  const [leaderboardData, setLeaderboardData] = useState(null);
  const [isLoadingSummary, setIsLoadingSummary] = useState(false);

  // Different emoji sets for different themes
  const emojiThemes = {
    animals: ['🐶', '🐱', '🐭', '🐹', '🐰', '🦊', '🐻', '🐼', '🐨', '🐯', '🦁', '🐮', '🐷', '🐸', '🐵', '🐔', '🦄', '🦓', '🦝', '🦉'],
    food: ['🍎', '🍕', '🍔', '🍟', '🌭', '🍿', '🍦', '🍩', '🎂', '🍫', '🍭', '🍓', '🍉', '🍇', '🍒', '🍑', '🥝', '🥥', '🥦', '🥨'],
    nature: ['🌲', '🌵', '🌸', '🌻', '🌺', '🌿', '🍀', '🍁', '🍄', '🌾', '🌍', '🌙', '⭐', '🌈', '🔥', '💧', '🌋', '🌊', '☁️', '🌪️'],
    objects: ['🚗', '✈️', '🚀', '🚁', '🚢', '🚲', '🚡', '🚂', '🚜', '🚁', '🚤', '🛥️', '🚲', '🛴', '🛹', '🚗', '🛸', '🚁', '🚟', '🚂']
  };

  const difficultySettings = {
    easy: { pairs: 8, columns: 4, rows: 4, theme: 'animals' },
    medium: { pairs: 12, columns: 4, rows: 6, theme: 'food' },
    hard: { pairs: 16, columns: 4, rows: 8, theme: 'nature' },
    expert: { pairs: 20, columns: 4, rows: 10, theme: 'objects' }
  };

  // Get CSRF Token from Laravel
  const getCsrfToken = () => {
    const metaTag = document.querySelector('meta[name="csrf-token"]');
    return metaTag ? metaTag.content : '';
  };

  // ========== NEW: Save score to Laravel ==========
  const saveScoreToDatabase = async (score, status) => {
    try {
      let playerId = localStorage.getItem('memoryGamePlayerId');
      if (!playerId) {
        playerId = 'player_' + Math.random().toString(36).substr(2, 9);
        localStorage.setItem('memoryGamePlayerId', playerId);
      }
      const gameId = 3; // Memory Game ID
      const timeTaken = startTime ? Math.floor((Date.now() - startTime) / 1000) : time;
      
      console.log('Saving memory game score to database...', {
        user_id: playerId,
        game_id: gameId,
        score: score
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
          score: score,
          time_taken: timeTaken,
          game_stats: {
            status: status,
            moves: moves,
            pairs_matched: matches,
            difficulty: difficulty,
            efficiency: (difficultySettings[difficulty].pairs / Math.max(moves, 1)) * 100
          }
        })
      });
      
      console.log('Save response status:', response.status);
      
      if (!response.ok) {
        const errorText = await response.text();
        console.error('Failed to save score:', errorText);
        
        // Try fallback endpoint
        console.log('Trying fallback endpoint...');
        return await tryFallbackSave(gameId, playerId, score, timeTaken, status);
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
  const tryFallbackSave = async (gameId, playerId, score, timeTaken, status) => {
    try {
      const response = await fetch('/save-game-score', {
        method: 'POST',
        headers: { 
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          user_id: playerId,
          game_id: gameId,
          score: score,
          time_taken: timeTaken,
          game_stats: {
            status: status,
            moves: moves,
            pairs_matched: matches,
            difficulty: difficulty,
            efficiency: (difficultySettings[difficulty].pairs / Math.max(moves, 1)) * 100
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

  // ========== NEW: Get game summary from Laravel API ==========
  const loadGameSummary = async () => {
    setIsLoadingSummary(true);
    try {
      // First save the score
      const saveResult = await saveScoreToDatabase(finalScore, 'selesai');
      
      if (!saveResult || !saveResult.success) {
        console.warn('Score save may have failed, but continuing...');
      }
      
      // Then get game summary
      const gameId = 3;
      let playerId = localStorage.getItem('memoryGamePlayerId');
      if (!playerId) {
        playerId = 'player_' + Math.random().toString(36).substr(2, 9);
        localStorage.setItem('memoryGamePlayerId', playerId);
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
    const timeTaken = startTime ? Math.floor((Date.now() - startTime) / 1000) : time;
    const xpEarned = Math.floor(finalScore / 10);
    const coinsEarned = Math.floor(finalScore / 100);
    const accuracy = Math.min(100, Math.floor((matches / difficultySettings[difficulty].pairs) * 100));
    const efficiency = Math.min(100, Math.floor((difficultySettings[difficulty].pairs / Math.max(moves, 1)) * 100));
    
    const rewards = [
      {
        type: 'xp',
        name: 'Mata Pengalaman',
        description: 'Pengalaman asas bermain',
        amount: xpEarned,
        icon: '⭐'
      }
    ];
    
    if (coinsEarned > 0) {
      rewards.push({
        type: 'coins',
        name: 'Koin',
        description: 'Mata wang dalam permainan',
        amount: coinsEarned,
        icon: '🪙'
      });
    }
    
    if (efficiency >= 80) {
      rewards.push({
        type: 'achievement',
        name: 'Strategi Efisien',
        description: 'Ketepatan pergerakan melebihi 80%',
        badge: 'efficient',
        icon: '🎯'
      });
    }
    
    if (difficulty === 'hard' || difficulty === 'expert') {
      rewards.push({
        type: 'achievement',
        name: 'Pakar Ingatan',
        description: `Menyelesaikan tahap ${difficulty === 'hard' ? 'Sukar' : 'Pakar'}`,
        badge: difficulty,
        icon: '🏆'
      });
    }
    
    if (timeTaken < 60) {
      rewards.push({
        type: 'achievement',
        name: 'Cepat Tangkas',
        description: 'Selesai dalam masa kurang 1 minit',
        badge: 'fast',
        icon: '⚡'
      });
    }
    
    if (moves <= difficultySettings[difficulty].pairs) {
      rewards.push({
        type: 'achievement',
        name: 'Ingatan Fotografik',
        description: 'Lengkapkan dengan bilangan langkah minimum',
        badge: 'perfect',
        icon: '🧠'
      });
    }
    
    setGameSummaryData({
      score: finalScore,
      time_taken: timeTaken,
      rank: 1,
      total_players: 1,
      accuracy: accuracy,
      rewards: rewards,
      game_title: 'Padanan Ingatan',
      game_id: 3,
      user_name: 'Pemain',
      xp_earned: xpEarned,
      coins_earned: coinsEarned,
      moves: moves,
      matches: matches,
      efficiency: efficiency,
      difficulty: difficulty
    });
  };

  // ========== NEW: Load leaderboard data ==========
  const loadLeaderboard = async () => {
    try {
      const gameId = 3;
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
      const difficultyMultiplier = {
        easy: 1,
        medium: 1.5,
        hard: 2,
        expert: 3
      };
      
      const baseScore = finalScore * difficultyMultiplier[difficulty];
      
      setLeaderboardData({
        success: true,
        leaderboard: [
          { rank: 1, user_name: 'Ali', score: 2500, time_taken: 45, is_current_user: false },
          { rank: 2, user_name: 'Siti', score: 2200, time_taken: 60, is_current_user: false },
          { rank: 3, user_name: 'Ahmad', score: 2000, time_taken: 75, is_current_user: false },
          { rank: 4, user_name: 'Pemain', score: baseScore, time_taken: time, is_current_user: true },
          { rank: 5, user_name: 'Muthu', score: 1800, time_taken: 90, is_current_user: false }
        ],
        user_rank: 4,
        user_score: baseScore,
        user_time: time,
        total_players: 5,
        game_id: 3,
        game_title: 'Padanan Ingatan'
      });
    }
  };

  // ========== NEW: Collect rewards via Laravel API ==========
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
          game_id: 3,
          score: finalScore,
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
  const submitToLeaderboard = async (score) => {
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
          game_id: 'game3',
          score: score,
          time_taken: time,
          difficulty: difficulty
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

  // Game tracking function
  const startGameTracking = async (gameId) => {
    try {
      const response = await progressService.startGame(gameId);
      setGameProgress(response.data.progress);
    } catch (error) {
      console.error('Gagal memulakan penjejakan permainan:', error);
    }
  };

  // ========== UPDATED: Save game progress ==========
  const saveGameProgress = async () => {
    try {
      const timeSpent = startTime ? Math.floor((Date.now() - startTime) / 1000) : time;
      const score = getScore();
      setFinalScore(score);
      
      const progressData = {
        score: score,
        level: 1,
        time_spent: timeSpent,
        completed: true,
        progress_data: {
          total_moves: moves,
          pairs_count: difficultySettings[difficulty].pairs,
          time_taken_seconds: timeSpent,
          efficiency: (difficultySettings[difficulty].pairs / Math.max(moves, 1)) * 100
        }
      };

      const response = await progressService.saveProgress(3, progressData);
      setGameProgress(response.data.progress);
      
      // Load game summary from Laravel API
      await loadGameSummary();
      
      // Load leaderboard data
      await loadLeaderboard();
      
      if (response.data.rewards_unlocked && response.data.rewards_unlocked.length > 0) {
        setUnlockedRewards(response.data.rewards_unlocked);
      }
    } catch (error) {
      console.error('Gagal menyimpan kemajuan:', error);
    }
  };

  useEffect(() => {
    if (gameStarted) {
      resetGame();
    }
  }, [difficulty, gameStarted]);

  useEffect(() => {
    let interval = null;
    if (timerActive && !gameWon) {
      interval = setInterval(() => {
        setTime(time => time + 1);
      }, 1000);
    } else if (gameWon) {
      clearInterval(interval);
    }
    return () => clearInterval(interval);
  }, [timerActive, gameWon]);

  useEffect(() => {
    if (cards.length > 0 && solved.length > 0 && solved.length === cards.length) {
      setGameWon(true);
      setTimerActive(false);
      
      // Save progress and show summary
      setTimeout(() => {
        saveGameProgress();
      }, 500);
    }
  }, [solved, cards.length]);

  const resetGame = () => {
    const settings = difficultySettings[difficulty];
    const theme = emojiThemes[settings.theme || 'animals'];
    const selectedEmojis = theme.slice(0, settings.pairs);
    setEmojis(selectedEmojis);
    
    const gameCards = [...selectedEmojis, ...selectedEmojis]
      .sort(() => Math.random() - 0.5)
      .map((emoji, index) => ({
        id: index,
        emoji,
        flipped: false
      }));
    
    setCards(gameCards);
    setFlipped([]);
    setSolved([]);
    setMoves(0);
    setMatches(0);
    setTime(0);
    setGameWon(false);
    setTimerActive(true);
    setShowSummary(false);
    setShowLeaderboard(false);
    setUnlockedRewards([]);
    setStartTime(Date.now());
    setFinalScore(0);
    setGameSummaryData(null);
    setLeaderboardData(null);
    
    // Start game tracking — Game ID 3
    startGameTracking(3);
  };

  const handleCardClick = (id) => {
    if (flipped.length === 2 || solved.includes(id) || flipped.includes(id) || gameWon) return;

    const newFlipped = [...flipped, id];
    setFlipped(newFlipped);
    
    if (newFlipped.length === 1) {
      setTimerActive(true);
    }

    if (newFlipped.length === 2) {
      setMoves(moves + 1);
      
      const [first, second] = newFlipped;
      if (cards[first].emoji === cards[second].emoji) {
        const newSolved = [...solved, first, second];
        setSolved(newSolved);
        setMatches(matches + 1);
        
        setTimeout(() => {
          setFlipped([]);
        }, 500);
      } else {
        setTimeout(() => setFlipped([]), 1000);
      }
    }
  };

  const formatTime = (seconds) => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
  };

  const getScore = () => {
    if (!gameWon) return 0;
    
    const settings = difficultySettings[difficulty];
    const baseScore = settings.pairs * 100;
    const movePenalty = Math.max(0, moves - settings.pairs * 2) * 5;
    const timeBonus = Math.max(0, 300 - time) * 2;
    const difficultyBonus = {
      easy: 1,
      medium: 1.5,
      hard: 2,
      expert: 3
    }[difficulty] * 100;
    
    return Math.max(0, (baseScore - movePenalty + timeBonus) * difficultyBonus / 100);
  };

  const startGame = () => {
    setGameStarted(true);
  };

  // ========== NEW: Custom Game Summary Modal ==========
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
        }}>🎴 Permainan Tamat!</h2>
        
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
                  {formatTime(gameSummaryData.time_taken)}
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
                  Langkah
                </div>
                <div style={{ fontSize: '24px', fontWeight: 'bold', color: '#9C27B0' }}>
                  {moves}
                </div>
              </div>
            </div>
            
            {/* Additional Stats */}
            <div style={{
              marginTop: '15px',
              padding: '15px',
              background: 'rgba(255,255,255,0.05)',
              borderRadius: '10px'
            }}>
              <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: '10px' }}>
                <span>Tahap Kesukaran:</span>
                <span style={{ color: '#4ecca3', fontWeight: 'bold' }}>
                  {difficulty === 'easy' ? 'Mudah' : 
                   difficulty === 'medium' ? 'Sederhana' :
                   difficulty === 'hard' ? 'Sukar' : 'Pakar'}
                </span>
              </div>
              <div style={{ display: 'flex', justifyContent: 'space-between' }}>
                <span>Pasangan Dijumpai:</span>
                <span style={{ color: '#4ecca3', fontWeight: 'bold' }}>
                  {matches}/{difficultySettings[difficulty].pairs}
                </span>
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
                onClick={() => {
                  setGameStarted(false);
                  setGameWon(false);
                  setShowSummary(false);
                  setUnlockedRewards([]);
                }}
              >
                ← Pilih Permainan Baharu
              </button>
            </div>
          </>
        )}
      </div>
    );
  };

  // ========== NEW: Custom Leaderboard Modal ==========
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
              🏆 Papan Pemimpin Padanan Ingatan
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
                    Skor: {leaderboardData.user_score} mata | Masa: {formatTime(leaderboardData.user_time)}
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
                            {formatTime(entry.time_taken)}
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

  return (
    <div style={{ 
      fontFamily: 'Segoe UI, Arial, sans-serif', 
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
          borderRadius: '16px',
          border: '2px solid #4ecca3',
          boxShadow: '0 0 20px rgba(78, 204, 163, 0.5)'
        }}>
          <h2 style={{ 
            fontSize: '2.5rem', 
            color: '#4ecca3',
            textShadow: '0 0 10px rgba(78, 204, 163, 0.7)',
            marginBottom: '20px'
          }}>🎴 Padanan Ingatan</h2>
          
          <div style={{ 
            fontSize: '1.2rem', 
            marginBottom: '25px',
            lineHeight: '1.6',
            color: '#f1f1f1'
          }}>
            <p>Padankan pasangan emoji secepat mungkin!</p>
            <p>Pilih tahap kesukaran dan mulakan permainan.</p>
          </div>
          
          <div style={{ 
            marginBottom: '25px',
            padding: '15px',
            backgroundColor: '#16213e',
            borderRadius: '12px'
          }}>
            <h4 style={{ color: '#4ecca3', marginBottom: '12px' }}>Cara Bermain:</h4>
            <div style={{ textAlign: 'left', display: 'inline-block', width: '100%' }}>
              <p>• Klik pada kad untuk membalikkannya</p>
              <p>• Cari pasangan emoji yang sepadan</p>
              <p>• Cuba lengkapkan permainan dengan bilangan langkah minimum</p>
              <p>• Tahap lebih tinggi mempunyai lebih banyak pasangan</p>
            </div>
          </div>
          
          <div style={{ marginBottom: '20px' }}>
            <label style={{ display: 'block', marginBottom: '10px', fontSize: '1.2rem', color: '#4ecca3' }}>
              Pilih Tahap Kesukaran:
            </label>
            <select 
              value={difficulty} 
              onChange={(e) => setDifficulty(e.target.value)}
              style={{
                padding: '12px',
                fontSize: '1.1rem',
                borderRadius: '8px',
                border: 'none',
                backgroundColor: '#16213e',
                color: '#fff',
                width: '100%',
                maxWidth: '300px',
                cursor: 'pointer'
              }}
            >
              <option value="easy">Mudah (8 pasang)</option>
              <option value="medium">Sederhana (12 pasang)</option>
              <option value="hard">Sukar (16 pasang)</option>
              <option value="expert">Pakar (20 pasang)</option>
            </select>
          </div>
          
          <button 
            style={{
              padding: '15px 40px',
              fontSize: '1.2rem',
              backgroundColor: '#4CAF50',
              color: '#fff',
              border: 'none',
              borderRadius: '10px',
              cursor: 'pointer',
              fontWeight: 'bold',
              transition: 'all 0.3s',
              boxShadow: '0 0 12px rgba(76, 175, 80, 0.6)'
            }}
            onClick={startGame}
          >
            ▶ Mulakan Permainan
          </button>
        </div>
      ) : (
        <>
          <h2 style={{ 
            fontSize: '2.5rem', 
            color: '#4ecca3',
            textShadow: '0 0 10px rgba(78, 204, 163, 0.7)',
            marginBottom: '20px'
          }}>🎴 Padanan Ingatan</h2>
          
          <div style={{
            display: 'flex',
            justifyContent: 'space-around',
            flexWrap: 'wrap',
            marginBottom: '20px',
            padding: '12px',
            backgroundColor: '#16213e',
            borderRadius: '12px',
            border: '2px solid #4ecca3',
            fontSize: '1.1rem'
          }}>
            <div><strong>Masa:</strong> {formatTime(time)}</div>
            <div><strong>Langkah:</strong> {moves}</div>
            <div><strong>Padanan:</strong> {matches}/{difficultySettings[difficulty].pairs}</div>
          </div>
          
          <div style={{
            backgroundColor: '#0f3460',
            borderRadius: '12px',
            padding: '20px',
            border: '2px solid #4ecca3',
            maxWidth: '800px',
            margin: '0 auto'
          }}>
            <div style={{ 
              color: '#f1f1f1', 
              marginBottom: '15px',
              fontSize: '1.15rem',
              fontWeight: 500
            }}>
              Cari pasangan emoji yang sepadan!
            </div>
            
            <div 
              style={{
                display: 'grid',
                gridTemplateColumns: `repeat(${difficultySettings[difficulty].columns}, 1fr)`,
                gridTemplateRows: `repeat(${difficultySettings[difficulty].rows}, 1fr)`,
                gap: '10px',
                maxWidth: '600px',
                margin: '0 auto',
                height: `${difficultySettings[difficulty].rows * 90}px`
              }}
            >
              {cards.map((card, index) => (
                <div
                  key={card.id}
                  style={{
                    height: '80px',
                    cursor: 'pointer',
                    transition: 'all 0.3s',
                    perspective: '1000px',
                    display: 'flex',
                    justifyContent: 'center',
                    alignItems: 'center'
                  }}
                  onClick={() => handleCardClick(index)}
                >
                  <div
                    style={{
                      width: '100%',
                      height: '100%',
                      position: 'relative',
                      transformStyle: 'preserve-3d',
                      transition: 'transform 0.6s',
                      transform: `${(flipped.includes(index) || solved.includes(index)) ? 'rotateY(180deg)' : 'rotateY(0deg)'}`,
                      borderRadius: '8px',
                      boxShadow: '0 4px 8px rgba(0,0,0,0.3)'
                    }}
                  >
                    {/* Card Back */}
                    <div
                      style={{
                        position: 'absolute',
                        width: '100%',
                        height: '100%',
                        backfaceVisibility: 'hidden',
                        display: 'flex',
                        justifyContent: 'center',
                        alignItems: 'center',
                        backgroundColor: '#16213e',
                        color: '#4ecca3',
                        fontSize: '2rem',
                        borderRadius: '8px',
                        border: '2px solid #4ecca3'
                      }}
                    >
                      ?
                    </div>
                    
                    {/* Card Front */}
                    <div
                      style={{
                        position: 'absolute',
                        width: '100%',
                        height: '100%',
                        backfaceVisibility: 'hidden',
                        display: 'flex',
                        justifyContent: 'center',
                        alignItems: 'center',
                        backgroundColor: '#4ecca3',
                        color: '#000',
                        fontSize: '2rem',
                        borderRadius: '8px',
                        transform: 'rotateY(180deg)',
                        border: '2px solid #16213e'
                      }}
                    >
                      {card.emoji}
                    </div>
                  </div>
                </div>
              ))}
            </div>

            {/* NEW: Custom Game Summary Modal */}
            <GameSummaryModal />
            
            {/* NEW: Custom Leaderboard Modal */}
            <LeaderboardModal />

            {gameWon && !showSummary && (
              <div style={{
                marginTop: '25px',
                padding: '25px',
                backgroundColor: '#16213e',
                borderRadius: '12px',
                border: '2px solid #4CAF50',
                textAlign: 'center'
              }}>
                <h3 style={{ color: '#4CAF50', fontSize: '1.9rem', marginBottom: '12px', fontWeight: 600 }}>🎉 Tahniah! Anda Berjaya!</h3>
                <p style={{ fontSize: '1.2rem', margin: '8px 0' }}>Diselesaikan dalam <strong>{moves} langkah</strong></p>
                <p style={{ fontSize: '1.2rem', margin: '8px 0' }}>Masa: <strong>{formatTime(time)}</strong></p>
                <p style={{ fontSize: '1.3rem', margin: '12px 0', color: '#FFD700', fontWeight: 'bold' }}>
                  Markah: <strong>{finalScore}</strong>
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
                    <p style={{ fontSize: '1.1rem', marginTop: '10px' }}>Memuatkan ringkasan permainan...</p>
                  </div>
                )}
              </div>
            )}

            {!gameWon && (
              <div style={{ display: 'flex', justifyContent: 'center', gap: '15px', marginTop: '20px' }}>
                <button 
                  style={{
                    padding: '10px 22px',
                    backgroundColor: '#2196F3',
                    color: '#fff',
                    border: 'none',
                    borderRadius: '8px',
                    cursor: 'pointer',
                    fontWeight: 'bold',
                    fontSize: '1.05rem',
                    transition: 'all 0.2s'
                  }}
                  onClick={() => setShowInstructions(true)}
                >
                  📖 Panduan
                </button>
                <button 
                  style={{
                    padding: '10px 22px',
                    backgroundColor: '#9C27B0',
                    color: '#fff',
                    border: 'none',
                    borderRadius: '8px',
                    cursor: 'pointer',
                    fontWeight: 'bold',
                    fontSize: '1.05rem',
                    transition: 'all 0.2s'
                  }}
                  onClick={() => {
                    setGameStarted(false);
                    setGameWon(false);
                    setShowSummary(false);
                    setUnlockedRewards([]);
                  }}
                >
                  🔄 Permainan Baharu
                </button>
              </div>
            )}
          </div>
        </>
      )}
      
      {/* Instructions Modal */}
      {showInstructions && (
        <div style={{
          position: 'fixed',
          top: 0,
          left: 0,
          width: '100%',
          height: '100%',
          backgroundColor: 'rgba(0, 0, 0, 0.85)',
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
            maxWidth: '520px',
            width: '90%',
            color: '#fff'
          }}>
            <h3 style={{ color: '#4ecca3', fontSize: '1.9rem', marginBottom: '20px', fontWeight: 600 }}>📖 Panduan Permainan</h3>
            <div style={{ 
              textAlign: 'left', 
              marginBottom: '25px', 
              fontSize: '1.15rem',
              lineHeight: 1.7,
              color: '#e2e8f0'
            }}>
              <p>• Klik pada kad untuk membalikkannya</p>
              <p>• Cari dua kad dengan emoji yang sama</p>
              <p>• Jika sepadan, kad akan kekal terbuka</p>
              <p>• Jika tidak sepadan, kad akan tertutup semula selepas 1 saat</p>
              <p>• Lengkapkan semua pasangan untuk memenangi permainan</p>
              <p>• Semakin sedikit langkah & masa, semakin tinggi markah anda!</p>
            </div>
            <button 
              style={{
                padding: '12px 35px',
                backgroundColor: '#9C27B0',
                color: '#fff',
                border: 'none',
                borderRadius: '8px',
                cursor: 'pointer',
                fontWeight: 'bold',
                fontSize: '1.2rem',
                transition: 'all 0.2s',
                boxShadow: '0 3px 8px rgba(156, 39, 176, 0.5)'
              }}
              onClick={() => setShowInstructions(false)}
            >
              Tutup
            </button>
          </div>
        </div>
      )}
      
      <style>{`
        button:hover {
          transform: translateY(-2px);
          box-shadow: 0 4px 12px rgba(255, 255, 255, 0.3);
        }
        
        button:disabled {
          opacity: 0.6;
          cursor: not-allowed;
          transform: none;
        }
        
        @keyframes spin {
          0% { transform: rotate(0deg); }
          100% { transform: rotate(360deg); }
        }
      `}</style>
    </div>
  );
};

export default MemoryGame;