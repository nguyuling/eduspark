import React, { useState, useEffect } from 'react';
import { progressService } from '../../services/progressService';
import GameSummary from './GameSummary';
import RewardsDisplay from './RewardsDisplay';

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

  // Game tracking function
  const startGameTracking = async (gameId) => {
    try {
      const response = await progressService.startGame(gameId);
      setGameProgress(response.data.progress);
    } catch (error) {
      console.error('Gagal memulakan penjejakan permainan:', error);
    }
  };

  const saveGameProgress = async () => {
    try {
      const timeSpent = startTime ? Math.floor((Date.now() - startTime) / 1000) : time;
      
      const progressData = {
        score: getScore(),
        level: 1,
        time_spent: timeSpent,
        completed: true,
        progress_data: {  // FIXED: Changed from progress_ to progress_data
          total_moves: moves,
          pairs_count: difficultySettings[difficulty].pairs,
          time_taken_seconds: timeSpent,
          efficiency: (difficultySettings[difficulty].pairs / Math.max(moves, 1)) * 100
        }
      };

      const response = await progressService.saveProgress(3, progressData);
      setGameProgress(response.data.progress);
      
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
      saveGameProgress();
      setShowSummary(true);
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
    setUnlockedRewards([]);
    setStartTime(Date.now());
    
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
    
    return Math.max(0, baseScore - movePenalty + timeBonus);
  };

  const startGame = () => {
    setGameStarted(true);
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

            {gameWon && (
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
                <p style={{ fontSize: '1.3rem', margin: '12px 0', color: '#FFD700', fontWeight: 'bold' }}>Markah: <strong>{getScore()}</strong></p>
                
                {showSummary && (
                  <>
                    <GameSummary progress={gameProgress} game={{ name: 'Padanan Ingatan' }} />
                    
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
                      padding: '12px 28px',
                      backgroundColor: '#4CAF50',
                      color: '#fff',
                      border: 'none',
                      borderRadius: '8px',
                      cursor: 'pointer',
                      fontWeight: 'bold',
                      fontSize: '1.1rem',
                      transition: 'all 0.2s',
                      boxShadow: '0 2px 6px rgba(76, 175, 80, 0.4)'
                    }}
                    onClick={resetGame}
                  >
                    Main Semula
                  </button>
                  <button 
                    style={{
                      padding: '12px 28px',
                      backgroundColor: '#9C27B0',
                      color: '#fff',
                      border: 'none',
                      borderRadius: '8px',
                      cursor: 'pointer',
                      fontWeight: 'bold',
                      fontSize: '1.1rem',
                      transition: 'all 0.2s',
                      boxShadow: '0 2px 6px rgba(156, 39, 176, 0.4)'
                    }}
                    onClick={() => {
                      setGameStarted(false);
                      setGameWon(false);
                      setShowSummary(false);
                      setUnlockedRewards([]);
                    }}
                  >
                    Permainan Baharu
                  </button>
                </div>
              </div>
            )}

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
          </div>
        </>
      )}
      
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
      `}</style>
    </div>
  );
};

export default MemoryGame;