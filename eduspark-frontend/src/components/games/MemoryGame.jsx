import React, { useState, useEffect } from 'react';

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

  // Different emoji sets for different themes
  const emojiThemes = {
    animals: ['🐶', '🐱', '🐭', '🐹', '🐰', '🦊', '🐻', '🐼', '🐨', '🐯', '🦁', '🐮', '🐷', '🐸', '🐵', '🐔', '🦄', '🦓', '🦝', '🦉'],
    food: ['🍎', '🍕', '🍔', '🍟', '🌭', '🍿', '🍦', '🍩', '🎂', '🍫', '🍭', '🍓', '🍉', '🍇', '🍒', '🍑', '🥝', '🥥', '🥦', '🥨'],
    nature: ['🌲', '🌵', '🌸', '🌻', '🌺', '🌿', '🍀', '🍁', '🍄', '🌾', '🌍', '🌙', '⭐', '🌈', '🔥', '💧', '🌋', '🌊', '☁️', '🌪️'],
    objects: ['🚗', '✈️', '🚀', '🚁', '🚢', '🚲', '🚡', '🚂', '🚜', '🚁', '🚤', '🛥️', '🚲', '🛴', '🛹', '🚗', '🛸', '🚁', '🚟', '🚂']
  };

  const difficultySettings = {
    easy: { pairs: 8, columns: 4, rows: 4 },
    medium: { pairs: 12, columns: 4, rows: 6 },
    hard: { pairs: 16, columns: 4, rows: 8 },
    expert: { pairs: 20, columns: 4, rows: 10 }
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

  const resetGame = () => {
    const settings = difficultySettings[difficulty];
    const theme = emojiThemes[difficultySettings[difficulty].theme || 'animals'];
    const selectedEmojis = theme.slice(0, settings.pairs);
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
  };

  const handleCardClick = (id) => {
    if (flipped.length === 2 || solved.includes(id) || flipped.includes(id) || gameWon) return;

    const newFlipped = [...flipped, id];
    setFlipped(newFlipped);
    
    if (newFlipped.length === 1) {
      setTimerActive(true); // Start timer on first move
    }

    if (newFlipped.length === 2) {
      setMoves(moves + 1);
      
      const [first, second] = newFlipped;
      if (cards[first].emoji === cards[second].emoji) {
        setSolved([...solved, first, second]);
        setMatches(matches + 1);
        
        setTimeout(() => {
          setFlipped([]);
          
          // Check if game is won
          if (solved.length + 2 === cards.length) {
            setGameWon(true);
            setTimerActive(false);
          }
        }, 500);
      } else {
        setTimeout(() => setFlipped([]), 1000);
      }
    } else {
      setFlipped(newFlipped);
    }
  };

  const formatTime = (seconds) => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
  };

  const getScore = () => {
    if (!gameWon) return 0;
    
    // Calculate score based on moves and time
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
          }}>🎴 Memory Match</h2>
          
          <div style={{ 
            fontSize: '1.2rem', 
            marginBottom: '25px',
            lineHeight: '1.6',
            color: '#f1f1f1'
          }}>
            <p>Match pairs of emojis as fast as you can!</p>
            <p>Select a difficulty level to start playing.</p>
          </div>
          
          <div style={{ 
            marginBottom: '25px',
            padding: '15px',
            backgroundColor: '#16213e',
            borderRadius: '10px'
          }}>
            <h4 style={{ color: '#4ecca3', marginBottom: '10px' }}>How to Play:</h4>
            <div style={{ textAlign: 'left', display: 'inline-block', width: '100%' }}>
              <p>• Click on cards to flip them over</p>
              <p>• Find matching pairs of emojis</p>
              <p>• Try to complete the game in as few moves as possible</p>
              <p>• Higher difficulties have more pairs to match</p>
            </div>
          </div>
          
          <div style={{ marginBottom: '20px' }}>
            <label style={{ display: 'block', marginBottom: '10px', fontSize: '1.2rem', color: '#4ecca3' }}>
              Select Difficulty:
            </label>
            <select 
              value={difficulty} 
              onChange={(e) => setDifficulty(e.target.value)}
              style={{
                padding: '10px',
                fontSize: '1rem',
                borderRadius: '5px',
                border: 'none',
                backgroundColor: '#16213e',
                color: '#fff',
                width: '100%',
                maxWidth: '300px'
              }}
            >
              <option value="easy">Easy (8 pairs)</option>
              <option value="medium">Medium (12 pairs)</option>
              <option value="hard">Hard (16 pairs)</option>
              <option value="expert">Expert (20 pairs)</option>
            </select>
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
          }}>🎴 Memory Match</h2>
          
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
              <span style={{ color: '#4ecca3', fontWeight: 'bold' }}>Time:</span>
              <span style={{ marginLeft: '10px', fontSize: '1.2rem' }}>{formatTime(time)}</span>
            </div>
            <div style={{ margin: '5px' }}>
              <span style={{ color: '#4ecca3', fontWeight: 'bold' }}>Moves:</span>
              <span style={{ marginLeft: '10px', fontSize: '1.2rem' }}>{moves}</span>
            </div>
            <div style={{ margin: '5px' }}>
              <span style={{ color: '#4ecca3', fontWeight: 'bold' }}>Matches:</span>
              <span style={{ marginLeft: '10px', fontSize: '1.2rem' }}>{matches}/{difficultySettings[difficulty].pairs}</span>
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
              Find matching pairs of emojis!
            </div>
            
            <div 
              style={{
                display: 'grid',
                gridTemplateColumns: `repeat(${difficultySettings[difficulty].columns}, 1fr)`,
                gridTemplateRows: `repeat(${difficultySettings[difficulty].rows}, 1fr)`,
                gap: '10px',
                maxWidth: '600px',
                margin: '0 auto',
                height: `${difficultySettings[difficulty].rows * 90}px` // Adjust height based on rows
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
                marginTop: '20px',
                padding: '20px',
                backgroundColor: '#16213e',
                borderRadius: '10px',
                border: '2px solid #4CAF50'
              }}>
                <h3 style={{ color: '#4CAF50', fontSize: '1.8rem', marginBottom: '10px' }}>🎉 Congratulations! You Won!</h3>
                <p style={{ fontSize: '1.2rem', margin: '10px 0' }}>Time: {formatTime(time)}</p>
                <p style={{ fontSize: '1.2rem', margin: '10px 0' }}>Moves: {moves}</p>
                <p style={{ fontSize: '1.2rem', margin: '10px 0', color: '#4CAF50', fontWeight: 'bold' }}>Score: {getScore()}</p>
                
                <div style={{ display: 'flex', justifyContent: 'center', gap: '15px', marginTop: '15px' }}>
                  <button 
                    style={{
                      padding: '12px 25px',
                      backgroundColor: '#4CAF50',
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
                      fontSize: '1rem',
                      transition: 'all 0.2s'
                    }}
                    onClick={() => {
                      setGameStarted(false);
                      setGameWon(false);
                    }}
                  >
                    New Game
                  </button>
                </div>
              </div>
            )}

            <div style={{ display: 'flex', justifyContent: 'center', gap: '15px', marginTop: '20px' }}>
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
                onClick={() => setShowInstructions(true)}
              >
                Instructions
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
                onClick={() => {
                  setGameStarted(false);
                  setGameWon(false);
                }}
              >
                New Game
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
            <h3 style={{ color: '#4ecca3', fontSize: '1.8rem', marginBottom: '20px' }}>Game Instructions</h3>
            <div style={{ textAlign: 'left', marginBottom: '20px' }}>
              <p style={{ margin: '10px 0', fontSize: '1.1rem' }}>• Click on a card to flip it over</p>
              <p style={{ margin: '10px 0', fontSize: '1.1rem' }}>• Find matching pairs of emojis</p>
              <p style={{ margin: '10px 0', fontSize: '1.1rem' }}>• If two cards match, they stay face up</p>
              <p style={{ margin: '10px 0', fontSize: '1.1rem' }}>• If they don't match, they flip back over</p>
              <p style={{ margin: '10px 0', fontSize: '1.1rem' }}>• Complete the game by matching all pairs</p>
              <p style={{ margin: '10px 0', fontSize: '1.1rem' }}>• Try to finish in as few moves as possible</p>
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
              onClick={() => setShowInstructions(false)}
            >
              Close
            </button>
          </div>
        </div>
      )}
      
      <style>{`
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

export default MemoryGame;