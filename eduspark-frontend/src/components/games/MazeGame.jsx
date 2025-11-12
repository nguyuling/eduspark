import React, { useState, useEffect, useCallback } from 'react';

const MazeGame = () => {
  // Game state
  const [score, setScore] = useState(0);
  const [gameOver, setGameOver] = useState(false);
  const [characterPos, setCharacterPos] = useState({ x: 1, y: 1 });
  const [characterDirection, setCharacterDirection] = useState('down'); // 'up', 'down', 'left', 'right'
  const [showQuestion, setShowQuestion] = useState(false);
  const [currentQuestion, setCurrentQuestion] = useState(null);
  const [selectedAnswer, setSelectedAnswer] = useState(null);
  const [isCorrect, setIsCorrect] = useState(null);
  const [explanation, setExplanation] = useState('');
  const [gameStarted, setGameStarted] = useState(false);
  const [timeLeft, setTimeLeft] = useState(120);
  const [visitedQuestions, setVisitedQuestions] = useState(new Set());
  const [isMoving, setIsMoving] = useState(false);

  // Garden-themed maze with bushes (1 = bush, 0 = path)
  const [maze, setMaze] = useState([
    [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
    [1, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 1],
    [1, 0, 1, 0, 1, 0, 1, 1, 1, 1, 0, 1],
    [1, 0, 1, 0, 0, 0, 0, 0, 0, 1, 0, 1],
    [1, 0, 1, 1, 1, 1, 1, 1, 0, 1, 0, 1],
    [1, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 1],
    [1, 1, 1, 1, 1, 1, 1, 0, 1, 1, 0, 1],
    [1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1],
    [1, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
    [1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1],
    [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 1],
    [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1]
  ]);

  // Question positions in the garden
  const questionPositions = [
    { x: 3, y: 1 }, { x: 7, y: 2 }, { x: 2, y: 3 }, { x: 8, y: 4 },
    { x: 5, y: 5 }, { x: 1, y: 7 }, { x: 6, y: 7 }, { x: 9, y: 9 },
    { x: 4, y: 10 }, { x: 7, y: 10 }
  ];

  // Computer science questions pool
  const questionsPool = [
    {
      question: "What does CPU stand for?",
      options: [
        "Central Processing Unit",
        "Computer Processing Unit", 
        "Central Process Unit",
        "Central Processor Unit"
      ],
      correct: 0,
      explanation: "✅ CPU stands for Central Processing Unit - the brain of the computer!"
    },
    {
      question: "Which data structure follows LIFO principle?",
      options: [
        "Queue",
        "Stack",
        "Array",
        "Linked List"
      ],
      correct: 1,
      explanation: "✅ Stack follows LIFO (Last In, First Out) - like a stack of plates!"
    },
    {
      question: "What is the time complexity of binary search?",
      options: [
        "O(n)",
        "O(n²)",
        "O(log n)", 
        "O(1)"
      ],
      correct: 2,
      explanation: "✅ Binary search has O(log n) time complexity - it halves the search space each time!"
    },
    {
      question: "What does HTML stand for?",
      options: [
        "Hyper Text Markup Language",
        "High Tech Modern Language",
        "Hyper Transfer Markup Language", 
        "Home Tool Markup Language"
      ],
      correct: 0,
      explanation: "✅ HTML = Hyper Text Markup Language - the standard markup language for web pages!"
    },
    {
      question: "Which programming language is known for data science?",
      options: [
        "Python",
        "C++", 
        "Java",
        "Ruby"
      ],
      correct: 0,
      explanation: "✅ Python is widely used in data science for its powerful libraries like Pandas and NumPy!"
    },
    {
      question: "What does SQL stand for?",
      options: [
        "Structured Query Language",
        "Simple Question Language",
        "System Query Logic",
        "Structured Question Logic"
      ],
      correct: 0,
      explanation: "✅ SQL = Structured Query Language - used for managing and querying databases!"
    },
    {
      question: "Which of these is a NoSQL database?",
      options: [
        "MySQL",
        "MongoDB", 
        "PostgreSQL",
        "SQLite"
      ],
      correct: 1,
      explanation: "✅ MongoDB is a popular NoSQL database that uses documents instead of tables!"
    },
    {
      question: "What is the main purpose of CSS?",
      options: [
        "To structure web content",
        "To style web pages", 
        "To add interactivity",
        "To store data"
      ],
      correct: 1,
      explanation: "✅ CSS (Cascading Style Sheets) is used to style and layout web pages!"
    },
    {
      question: "Which protocol is used for emails?",
      options: [
        "HTTP",
        "FTP",
        "SMTP", 
        "TCP"
      ],
      correct: 2,
      explanation: "✅ SMTP (Simple Mail Transfer Protocol) is used for sending emails!"
    },
    {
      question: "What is JavaScript primarily used for?",
      options: [
        "Styling websites",
        "Database management", 
        "Web interactivity",
        "Server configuration"
      ],
      correct: 2,
      explanation: "✅ JavaScript adds interactivity and dynamic behavior to web pages!"
    }
  ];

  // Get random questions
  const getRandomQuestions = () => {
    const shuffled = [...questionsPool].sort(() => 0.5 - Math.random());
    return shuffled.slice(0, 6); // Return 6 random questions
  };

  const [availableQuestions, setAvailableQuestions] = useState([]);

  // Initialize game
  useEffect(() => {
    if (gameStarted) {
      setCharacterPos({ x: 1, y: 1 });
      setCharacterDirection('down');
      setScore(0);
      setTimeLeft(120);
      setGameOver(false);
      setVisitedQuestions(new Set());
      setAvailableQuestions(getRandomQuestions());
    }
  }, [gameStarted]);

  // Game timer
  useEffect(() => {
    if (gameStarted && timeLeft > 0 && !gameOver && !showQuestion) {
      const timer = setTimeout(() => setTimeLeft(timeLeft - 1), 1000);
      return () => clearTimeout(timer);
    } else if (timeLeft === 0 && !gameOver) {
      setGameOver(true);
    }
  }, [timeLeft, gameStarted, gameOver, showQuestion]);

  // Handle keyboard controls
  const handleKeyDown = useCallback((e) => {
    if (gameOver || showQuestion || !gameStarted || isMoving) return;

    if (!['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight'].includes(e.key)) {
      return;
    }

    e.preventDefault();

    const { x, y } = characterPos;
    let newX = x;
    let newY = y;
    let direction = characterDirection;

    switch (e.key) {
      case 'ArrowUp':
        newY = Math.max(0, y - 1);
        direction = 'up';
        break;
      case 'ArrowDown':
        newY = Math.min(11, y + 1);
        direction = 'down';
        break;
      case 'ArrowLeft':
        newX = Math.max(0, x - 1);
        direction = 'left';
        break;
      case 'ArrowRight':
        newX = Math.min(11, x + 1);
        direction = 'right';
        break;
      default:
        return;
    }

    // Check if the new position is valid (not a bush)
    if (maze[newY][newX] === 0) {
      setIsMoving(true);
      setCharacterDirection(direction);
      
      setTimeout(() => {
        setCharacterPos({ x: newX, y: newY });
        setIsMoving(false);
        
        // Check if position has a question
        const questionIndex = questionPositions.findIndex(pos => pos.x === newX && pos.y === newY);
        if (questionIndex !== -1 && !visitedQuestions.has(questionIndex) && availableQuestions.length > 0) {
          triggerQuestion(questionIndex);
        }
        
        // Check if reached the end
        if (newX === 10 && newY === 10) {
          setGameOver(true);
          setScore(prev => prev + 50);
        }
      }, 200);
    }
  }, [characterPos, gameOver, showQuestion, gameStarted, maze, visitedQuestions, isMoving, characterDirection, availableQuestions]);

  useEffect(() => {
    if (gameStarted && !gameOver) {
      window.addEventListener('keydown', handleKeyDown);
      return () => window.removeEventListener('keydown', handleKeyDown);
    }
  }, [gameStarted, gameOver, handleKeyDown]);

  // Trigger a question
  const triggerQuestion = (questionIndex) => {
    if (availableQuestions.length === 0) return;
    
    const randomQuestion = availableQuestions[Math.floor(Math.random() * availableQuestions.length)];
    setCurrentQuestion(randomQuestion);
    setShowQuestion(true);
    setSelectedAnswer(null);
    setIsCorrect(null);
    setVisitedQuestions(prev => new Set([...prev, questionIndex]));
    
    // Remove the used question
    setAvailableQuestions(prev => prev.filter(q => q !== randomQuestion));
  };

  // Handle answer selection
  const handleAnswer = (index) => {
    if (selectedAnswer !== null) return;
    
    setSelectedAnswer(index);
    const correct = index === currentQuestion.correct;
    setIsCorrect(correct);
    setExplanation(currentQuestion.explanation);
    
    if (correct) {
      setScore(prev => prev + 15); // Increased points for garden theme
    }
    
    setTimeout(() => {
      setShowQuestion(false);
    }, 2000);
  };

  const startGame = () => {
    setGameStarted(true);
  };

  const resetGame = () => {
    setGameStarted(false);
    setGameOver(false);
    setShowQuestion(false);
  };

  // Character emojis based on direction
  const getCharacterEmoji = () => {
    if (isMoving) {
      switch (characterDirection) {
        case 'up': return '👆';
        case 'down': return '👇';
        case 'left': return '👈';
        case 'right': return '👉';
        default: return '🧍';
      }
    }
    return '🧍';
  };

  // Render the garden maze
  const renderGardenMaze = () => {
    return maze.map((row, rowIndex) => (
      <div key={rowIndex} style={{ display: 'flex' }}>
        {row.map((cell, colIndex) => {
          const isBush = cell === 1;
          const isQuestion = questionPositions.some(pos => pos.x === colIndex && pos.y === rowIndex);
          const isVisitedQuestion = isQuestion && visitedQuestions.has(
            questionPositions.findIndex(pos => pos.x === colIndex && pos.y === rowIndex)
          );
          const isPlayer = characterPos.x === colIndex && characterPos.y === rowIndex;
          const isExit = colIndex === 10 && rowIndex === 10;

          let background = '';
          let content = '';
          let animation = '';

          if (isBush) {
            background = '#2E8B57'; // Bush green
            content = '🌿';
          } else {
            background = '#F0FFF0'; // Light garden green for paths
          }

          if (isPlayer) {
            content = getCharacterEmoji();
            animation = isMoving ? 'bounce 0.2s ease-in-out' : 'none';
          } else if (isExit) {
            background = '#FFD700'; // Golden exit
            content = '🏁';
          } else if (isQuestion && !isVisitedQuestion) {
            content = '❓';
            animation = 'pulse 2s infinite';
          } else if (isQuestion && isVisitedQuestion) {
            content = '✅';
          }

          // Add some random flowers on paths
          if (!isBush && !isPlayer && !isExit && !isQuestion && Math.random() < 0.1) {
            const flowers = ['🌼', '🌸', '🌺', '🌻'];
            content = flowers[Math.floor(Math.random() * flowers.length)];
          }

          return (
            <div
              key={`${rowIndex}-${colIndex}`}
              style={{
                width: '40px',
                height: '40px',
                display: 'flex',
                justifyContent: 'center',
                alignItems: 'center',
                border: '1px solid #90EE90',
                backgroundColor: background,
                fontSize: '20px',
                fontWeight: 'bold',
                transition: 'all 0.3s ease',
                animation: animation,
                borderRadius: isBush ? '8px' : '4px',
                boxShadow: isBush ? 'inset 0 2px 4px rgba(0,0,0,0.2)' : 'none',
                position: 'relative',
                overflow: 'hidden'
              }}
            >
              {content}
              {/* Add grass texture to paths */}
              {!isBush && (
                <div style={{
                  position: 'absolute',
                  top: 0,
                  left: 0,
                  right: 0,
                  bottom: 0,
                  background: 'linear-gradient(45deg, transparent 90%, #90EE90 90%)',
                  opacity: 0.3
                }} />
              )}
            </div>
          );
        })}
      </div>
    ));
  };

  if (!gameStarted) {
    return (
      <div style={{
        minHeight: '100vh',
        background: 'linear-gradient(135deg, #87CEEB 0%, #98FB98 100%)',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        padding: '20px',
        fontFamily: 'Arial, sans-serif'
      }}>
        <div style={{
          background: 'rgba(255, 255, 255, 0.95)',
          borderRadius: '20px',
          padding: '40px',
          maxWidth: '600px',
          width: '90%',
          textAlign: 'center',
          boxShadow: '0 20px 40px rgba(0,0,0,0.1)',
          border: '2px solid #90EE90'
        }}>
          <h1 style={{
            fontSize: '3rem',
            color: '#2E8B57',
            marginBottom: '10px',
            textShadow: '2px 2px 4px rgba(0,0,0,0.1)'
          }}>
            🌿 Garden Maze Adventure
          </h1>
          <p style={{
            fontSize: '1.2rem',
            color: '#556B2F',
            marginBottom: '30px'
          }}>
            Explore the beautiful garden maze, answer CS questions, and find your way to the golden flag!
          </p>

          <div style={{
            background: 'rgba(144, 238, 144, 0.2)',
            borderRadius: '15px',
            padding: '25px',
            marginBottom: '30px',
            textAlign: 'left',
            border: '1px solid #90EE90'
          }}>
            <h3 style={{ color: '#2E8B57', marginBottom: '15px' }}>🌼 How to Play:</h3>
            {[
              "Use arrow keys to move your character 🧍 through the garden",
              "Follow the light green paths between the bushes 🌿",
              "Discover and answer question marks ❓ hidden in the garden",
              "Each correct answer earns 15 points",
              "Find the golden flag 🏁 to complete your adventure!"
            ].map((item, index) => (
              <div key={index} style={{
                display: 'flex',
                alignItems: 'center',
                marginBottom: '12px',
                padding: '10px',
                background: 'rgba(255, 255, 255, 0.8)',
                borderRadius: '8px',
                border: '1px solid #E8F5E8'
              }}>
                <div style={{
                  width: '30px',
                  height: '30px',
                  background: 'linear-gradient(45deg, #87CEEB, #98FB98)',
                  borderRadius: '50%',
                  color: 'white',
                  display: 'flex',
                  alignItems: 'center',
                  justifyContent: 'center',
                  marginRight: '12px',
                  fontWeight: 'bold',
                  fontSize: '0.9rem'
                }}>
                  {index + 1}
                </div>
                <span style={{ color: '#556B2F', fontSize: '1rem' }}>{item}</span>
              </div>
            ))}
          </div>

          <button
            onClick={startGame}
            style={{
              background: 'linear-gradient(45deg, #87CEEB, #98FB98)',
              color: 'white',
              border: 'none',
              padding: '15px 40px',
              fontSize: '1.2rem',
              borderRadius: '25px',
              cursor: 'pointer',
              fontWeight: 'bold',
              transition: 'all 0.3s ease',
              boxShadow: '0 8px 20px rgba(135, 206, 235, 0.4)',
              textShadow: '1px 1px 2px rgba(0,0,0,0.2)'
            }}
            onMouseOver={(e) => {
              e.target.style.transform = 'scale(1.05)';
              e.target.style.boxShadow = '0 12px 25px rgba(135, 206, 235, 0.6)';
            }}
            onMouseOut={(e) => {
              e.target.style.transform = 'scale(1)';
              e.target.style.boxShadow = '0 8px 20px rgba(135, 206, 235, 0.4)';
            }}
          >
            🌸 Start Garden Adventure
          </button>
        </div>
      </div>
    );
  }

  return (
    <div style={{
      minHeight: '100vh',
      background: 'linear-gradient(135deg, #87CEEB 0%, #98FB98 100%)',
      padding: '20px',
      fontFamily: 'Arial, sans-serif'
    }}>
      {/* Header */}
      <div style={{
        background: 'rgba(255, 255, 255, 0.95)',
        borderRadius: '15px',
        padding: '20px',
        marginBottom: '20px',
        display: 'flex',
        justifyContent: 'space-between',
        alignItems: 'center',
        boxShadow: '0 8px 20px rgba(0,0,0,0.1)',
        border: '2px solid #90EE90'
      }}>
        <h1 style={{
          margin: 0,
          color: '#2E8B57',
          fontSize: '2rem',
          textShadow: '1px 1px 2px rgba(0,0,0,0.1)'
        }}>
          🌿 Garden Maze
        </h1>
        <div style={{ display: 'flex', gap: '30px' }}>
          <div style={{ textAlign: 'center' }}>
            <div style={{ color: '#556B2F', fontSize: '0.9rem', fontWeight: 'bold' }}>SCORE</div>
            <div style={{ fontSize: '1.8rem', fontWeight: 'bold', color: '#2E8B57' }}>{score}</div>
          </div>
          <div style={{ textAlign: 'center' }}>
            <div style={{ color: '#556B2F', fontSize: '0.9rem', fontWeight: 'bold' }}>TIME</div>
            <div style={{ 
              fontSize: '1.8rem', 
              fontWeight: 'bold', 
              color: timeLeft > 30 ? '#27ae60' : timeLeft > 10 ? '#f39c12' : '#e74c3c',
              animation: timeLeft < 30 ? 'pulse 1s infinite' : 'none'
            }}>
              {timeLeft}s
            </div>
          </div>
          <div style={{ textAlign: 'center' }}>
            <div style={{ color: '#556B2F', fontSize: '0.9rem', fontWeight: 'bold' }}>QUESTIONS</div>
            <div style={{ fontSize: '1.8rem', fontWeight: 'bold', color: '#2E8B57' }}>
              {visitedQuestions.size}/6
            </div>
          </div>
        </div>
      </div>

      {/* Maze Container */}
      <div style={{
        background: 'rgba(255, 255, 255, 0.9)',
        borderRadius: '20px',
        padding: '25px',
        boxShadow: '0 15px 30px rgba(0,0,0,0.1)',
        display: 'inline-block',
        border: '3px solid #90EE90'
      }}>
        <div style={{ 
          color: '#556B2F', 
          marginBottom: '20px',
          textAlign: 'center',
          fontSize: '1.1rem',
          fontWeight: 'bold'
        }}>
          🌸 Use arrow keys to explore • Find the golden flag 🏁
        </div>
        
        {renderGardenMaze()}

        {/* Garden Legend */}
        <div style={{ 
          display: 'grid', 
          gridTemplateColumns: 'repeat(3, 1fr)',
          gap: '15px', 
          marginTop: '25px',
          padding: '15px',
          background: 'rgba(144, 238, 144, 0.2)',
          borderRadius: '12px',
          border: '1px solid #90EE90'
        }}>
          {[
            { emoji: '🧍', label: 'You' },
            { emoji: '🏁', label: 'Finish' },
            { emoji: '❓', label: 'Question' },
            { emoji: '🌿', label: 'Bush' },
            { emoji: '✅', label: 'Answered' },
            { emoji: '🌸', label: 'Garden Path' }
          ].map((item, index) => (
            <div key={index} style={{ 
              display: 'flex', 
              alignItems: 'center', 
              gap: '8px',
              justifyContent: 'center'
            }}>
              <span style={{ fontSize: '1.3rem' }}>{item.emoji}</span>
              <span style={{ color: '#556B2F', fontSize: '0.9rem', fontWeight: '500' }}>{item.label}</span>
            </div>
          ))}
        </div>
      </div>

      {/* Question Modal */}
      {showQuestion && currentQuestion && (
        <div style={{
          position: 'fixed',
          top: 0,
          left: 0,
          width: '100%',
          height: '100%',
          background: 'rgba(46, 139, 87, 0.9)',
          display: 'flex',
          justifyContent: 'center',
          alignItems: 'center',
          zIndex: 1000
        }}>
          <div style={{
            background: 'linear-gradient(135deg, #FFF8DC, #F0FFF0)',
            borderRadius: '20px',
            padding: '30px',
            maxWidth: '500px',
            width: '90%',
            boxShadow: '0 20px 40px rgba(0,0,0,0.3)',
            border: '3px solid #90EE90'
          }}>
            <h3 style={{ 
              color: '#2E8B57', 
              marginBottom: '20px',
              textAlign: 'center',
              fontSize: '1.5rem'
            }}>
              💻 Garden Challenge
            </h3>
            
            <p style={{
              fontSize: '1.1rem',
              color: '#556B2F',
              marginBottom: '25px',
              lineHeight: '1.4',
              textAlign: 'center',
              fontWeight: '500'
            }}>
              {currentQuestion.question}
            </p>

            <div style={{ marginBottom: '20px' }}>
              {currentQuestion.options.map((option, index) => (
                <button
                  key={index}
                  onClick={() => handleAnswer(index)}
                  disabled={selectedAnswer !== null}
                  style={{
                    width: '100%',
                    padding: '15px',
                    marginBottom: '12px',
                    border: '2px solid #90EE90',
                    borderRadius: '12px',
                    background: selectedAnswer === null 
                      ? 'white'
                      : index === currentQuestion.correct
                        ? '#27ae60'
                        : index === selectedAnswer
                          ? '#e74c3c'
                          : 'white',
                    color: selectedAnswer === null ? '#2E8B57' : 'white',
                    cursor: selectedAnswer === null ? 'pointer' : 'default',
                    fontSize: '1rem',
                    textAlign: 'left',
                    transition: 'all 0.3s ease',
                    opacity: selectedAnswer !== null && index !== selectedAnswer && index !== currentQuestion.correct ? 0.6 : 1,
                    fontWeight: '500'
                  }}
                >
                  {option}
                </button>
              ))}
            </div>

            {selectedAnswer !== null && (
              <div style={{
                padding: '15px',
                background: isCorrect ? 'rgba(39, 174, 96, 0.1)' : 'rgba(231, 76, 60, 0.1)',
                border: `2px solid ${isCorrect ? '#27ae60' : '#e74c3c'}`,
                borderRadius: '12px',
                color: isCorrect ? '#27ae60' : '#e74c3c',
                textAlign: 'center',
                fontWeight: '500'
              }}>
                {explanation}
              </div>
            )}
          </div>
        </div>
      )}

      {/* Game Over Modal */}
      {gameOver && (
        <div style={{
          position: 'fixed',
          top: 0,
          left: 0,
          width: '100%',
          height: '100%',
          background: 'rgba(46, 139, 87, 0.95)',
          display: 'flex',
          justifyContent: 'center',
          alignItems: 'center',
          zIndex: 1000
        }}>
          <div style={{
            background: 'linear-gradient(135deg, #FFF8DC, #F0FFF0)',
            borderRadius: '20px',
            padding: '40px',
            textAlign: 'center',
            maxWidth: '450px',
            width: '90%',
            boxShadow: '0 20px 40px rgba(0,0,0,0.3)',
            border: '3px solid #90EE90'
          }}>
            <h2 style={{ 
              color: '#2E8B57', 
              marginBottom: '20px',
              fontSize: '2.2rem'
            }}>
              {score >= 60 ? '🏆 Garden Master!' : score >= 30 ? '🌼 Well Done!' : '🌸 Game Over'}
            </h2>
            
            <div style={{ fontSize: '4rem', marginBottom: '20px' }}>
              {score >= 60 ? '🎉' : score >= 30 ? '😊' : '🌿'}
            </div>
            
            <div style={{ 
              background: 'rgba(144, 238, 144, 0.2)', 
              borderRadius: '15px', 
              padding: '20px', 
              marginBottom: '25px',
              border: '2px solid #90EE90'
            }}>
              <p style={{ fontSize: '1.3rem', color: '#2E8B57', marginBottom: '8px' }}>
                Final Score: <strong style={{ color: '#556B2F' }}>{score}</strong>
              </p>
              <p style={{ fontSize: '1.1rem', color: '#2E8B57', marginBottom: '8px' }}>
                Time Left: <strong style={{ color: '#556B2F' }}>{timeLeft}s</strong>
              </p>
              <p style={{ fontSize: '1.1rem', color: '#2E8B57' }}>
                Questions Answered: <strong style={{ color: '#556B2F' }}>{visitedQuestions.size}/6</strong>
              </p>
            </div>

            <div style={{ display: 'flex', gap: '15px', justifyContent: 'center' }}>
              <button
                onClick={resetGame}
                style={{
                  background: 'linear-gradient(45deg, #87CEEB, #98FB98)',
                  color: 'white',
                  border: 'none',
                  padding: '12px 25px',
                  borderRadius: '25px',
                  cursor: 'pointer',
                  fontWeight: 'bold',
                  fontSize: '1rem',
                  transition: 'all 0.3s ease'
                }}
              >
                Play Again
              </button>
              <button
                onClick={() => setGameStarted(false)}
                style={{
                  background: '#A9A9A9',
                  color: 'white',
                  border: 'none',
                  padding: '12px 25px',
                  borderRadius: '25px',
                  cursor: 'pointer',
                  fontWeight: 'bold',
                  fontSize: '1rem',
                  transition: 'all 0.3s ease'
                }}
              >
                Main Menu
              </button>
            </div>
          </div>
        </div>
      )}

      <style>{`
        @keyframes bounce {
          0%, 100% { transform: scale(1); }
          50% { transform: scale(1.1); }
        }
        
        @keyframes pulse {
          0%, 100% { opacity: 1; }
          50% { opacity: 0.7; }
        }
        
        button:hover:not(:disabled) {
          transform: scale(1.05);
          box-shadow: 0 5px 15px rgba(144, 238, 144, 0.4);
        }
      `}</style>
    </div>
  );
};

export default MazeGame;