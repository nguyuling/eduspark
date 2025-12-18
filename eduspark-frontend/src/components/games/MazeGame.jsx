import React, { useState, useEffect, useCallback } from 'react';
import { progressService } from '../../services/progressService';
import GameSummary from './GameSummary';
import RewardsDisplay from './RewardsDisplay';
import Leaderboard from '../leaderboard/Leaderboard';

const MazeGame = () => {
  // === State Variables ===
  const [score, setScore] = useState(0);
  const [gameOver, setGameOver] = useState(false);
  const [characterPos, setCharacterPos] = useState({ x: 1, y: 1 });
  const [characterDirection, setCharacterDirection] = useState('down');
  const [showQuestion, setShowQuestion] = useState(false);
  const [currentQuestion, setCurrentQuestion] = useState(null);
  const [selectedAnswer, setSelectedAnswer] = useState(null);
  const [isCorrect, setIsCorrect] = useState(null);
  const [explanation, setExplanation] = useState('');
  const [gameStarted, setGameStarted] = useState(false);
  const [timeLeft, setTimeLeft] = useState(120);
  const [visitedQuestions, setVisitedQuestions] = useState(new Set());
  const [isMoving, setIsMoving] = useState(false);
  const [gameProgress, setGameProgress] = useState(null);
  const [unlockedRewards, setUnlockedRewards] = useState([]);
  const [showSummary, setShowSummary] = useState(false);
  const [startTime, setStartTime] = useState(null);
  const [questionsAnswered, setQuestionsAnswered] = useState(0);
  const [correctAnswers, setCorrectAnswers] = useState(0);
  const [showLeaderboard, setShowLeaderboard] = useState(false);
  const [showGameOverModal, setShowGameOverModal] = useState(false); // NEW STATE

  // === Save Score to Database ===
  const saveScoreToDatabase = async (finalScore, status) => {
    try {
      let playerId = localStorage.getItem('mazeGamePlayerId');
      if (!playerId) {
        playerId = 'player_' + Math.random().toString(36).substr(2, 9);
        localStorage.setItem('mazeGamePlayerId', playerId);
      }
      const gameId = 4;
      const response = await fetch('/api/save-game-score', {
        method: 'POST',
        headers: { 
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          user_id: playerId,
          game_id: gameId,
          score: finalScore,
          time_taken: startTime ? Math.floor((Date.now() - startTime) / 1000) : 120 - timeLeft,
          game_stats: {
            status: status,
            questions_answered: questionsAnswered,
            correct_answers: correctAnswers,
            accuracy: questionsAnswered > 0 ? ((correctAnswers / questionsAnswered) * 100).toFixed(1) + '%' : '0%'
          }
        })
      });
      if (!response.ok) {
        const errorText = await response.text();
        throw new Error(`HTTP ${response.status}: ${errorText}`);
      }
      const result = await response.json();
      console.log('📊 Score save result:', result);
      return result;
    } catch (error) {
      console.error('❌ Failed to save score:', error);
      return null;
    }
  };

  // === Submit Score to Leaderboard ===
  const submitToLeaderboard = async (finalScore) => {
    const userData = localStorage.getItem('user');
    let user = null;
    
    try {
      if (userData) {
        user = JSON.parse(userData);
      }
    } catch (e) {
      console.warn('Failed to parse user data from localStorage');
    }
    
    if (!user || !user.id) {
      console.warn('⚠️ User not authenticated — skipping leaderboard submission');
      return;
    }

    try {
      const response = await fetch('/api/leaderboard', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': `Bearer ${user.token || ''}`
        },
        body: JSON.stringify({
          user_id: user.id,
          username: user.name || 'Anonymous',
          class: user.class || 'Unknown',
          game_id: 'game4',
          score: finalScore,
          time_taken: startTime ? Math.floor((Date.now() - startTime) / 1000) : 120 - timeLeft
        })
      });

      if (!response.ok) {
        const err = await response.json().catch(() => ({}));
        throw new Error(err.error || `HTTP ${response.status}`);
      }

      console.log('✅ Score submitted to leaderboard');
    } catch (error) {
      console.error('❌ Leaderboard submission failed:', error.message);
    }
  };

  // === Progress Tracking ===
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
      const timeSpent = startTime ? Math.floor((Date.now() - startTime) / 1000) : 120 - timeLeft;
      const progressData = {
        score: score,
        level: 1,
        time_spent: timeSpent,
        completed: true,
        progress: {
          questions_answered: questionsAnswered,
          correct_answers: correctAnswers,
          accuracy_percentage: questionsAnswered > 0 ? (correctAnswers / questionsAnswered) * 100 : 0,
          maze_completed: characterPos.x === 14 && characterPos.y === 14
        }
      };
      const response = await progressService.saveProgress(4, progressData);
      setGameProgress(response.data.progress);
      if (response.data.rewards_unlocked?.length > 0) {
        setUnlockedRewards(response.data.rewards_unlocked);
      }
    } catch (error) {
      console.error('Gagal menyimpan kemajuan:', error);
    }
  };

  // === Maze Layout ===
  const [maze] = useState([
    [1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1],
    [1,0,0,0,0,0,1,0,0,0,0,0,0,0,0,1],
    [1,0,1,1,1,0,1,0,1,1,1,1,1,1,0,1],
    [1,0,1,0,0,0,0,0,0,0,0,0,0,0,0,1],
    [1,0,1,0,1,1,1,1,1,1,1,1,1,1,0,1],
    [1,0,0,0,0,0,0,0,0,0,0,0,1,0,0,1],
    [1,1,1,1,1,1,1,1,1,1,1,0,1,0,1,1],
    [1,0,0,0,0,0,0,0,0,0,1,0,1,0,0,1],
    [1,0,1,1,1,1,1,0,1,0,1,0,1,1,0,1],
    [1,0,1,0,0,0,1,0,1,0,0,0,0,0,0,1],
    [1,0,1,0,1,0,1,0,1,1,1,1,1,1,0,1],
    [1,0,0,0,1,0,0,0,0,0,0,0,0,0,0,1],
    [1,1,1,0,1,1,1,1,1,1,1,1,1,1,0,1],
    [1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1],
    [1,0,1,1,1,1,1,1,1,1,1,1,1,1,0,1],
    [1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1]
  ]);

  const questionPositions = [
    { x: 5, y: 1 }, { x: 2, y: 3 }, { x: 10, y: 3 },
    { x: 4, y: 5 }, { x: 12, y: 5 }, { x: 1, y: 7 },
    { x: 9, y: 7 }, { x: 3, y: 9 }, { x: 11, y: 9 },
    { x: 6, y: 11 }, { x: 13, y: 13 }, { x: 8, y: 13 }
  ];

  // === BEGINNER JAVA QUESTIONS (Bahasa Melayu) ===
  const questionsPool = [
    {
      question: "Apakah bahasa pengaturcaraan Java?",
      options: [
        "Bahasa mesin",
        "Bahasa pengaturcaraan berorientasi objek",
        "Bahasa pangkalan data",
        "Bahasa markup"
      ],
      correct: 1,
      explanation: "✅ Java adalah bahasa pengaturcaraan berorientasi objek yang popular untuk membina aplikasi mudah alih, desktop dan web."
    },
    {
      question: "Apakah fungsi utama 'public static void main(String[] args)' dalam Java?",
      options: [
        "Menyatakan pemboleh ubah",
        "Titik permulaan program Java",
        "Mencetak output ke skrin",
        "Mengimport pakej"
      ],
      correct: 1,
      explanation: "✅ 'main' method adalah titik masuk utama (entry point) untuk program Java. Semua program Java bermula di sini."
    },
    {
      question: "Apakah output bagi kod berikut?\n```java\nint x = 5;\nint y = 2;\nSystem.out.println(x + y);\n```",
      options: [
        "7",
        "52",
        "10",
        "5+2"
      ],
      correct: 0,
      explanation: "✅ Operator '+' menambah nilai: 5 + 2 = 7."
    },
    {
      question: "Apakah jenis data untuk menyimpan nombor bulat dalam Java?",
      options: [
        "String",
        "boolean",
        "int",
        "double"
      ],
      correct: 2,
      explanation: "✅ 'int' digunakan untuk menyimpan nombor bulat seperti 1, 2, 3, -10, 100, dsb."
    },
    {
      question: "Bagaimanakah anda mengisytiharkan pemboleh ubah dalam Java?",
      options: [
        "variable x = 5;",
        "int x = 5;",
        "x = 5;",
        "declare x = 5;"
      ],
      correct: 1,
      explanation: "✅ Sintaks yang betul: <jenis_data> <nama_pembolehubah> = <nilai>; Contoh: int umur = 20;"
    },
    {
      question: "Apakah fungsi 'System.out.println()' dalam Java?",
      options: [
        "Membaca input pengguna",
        "Menyimpan data ke fail",
        "Mencetak teks ke konsol",
        "Mengira matematik"
      ],
      correct: 2,
      explanation: "✅ 'System.out.println()' mencetak output ke konsol (command prompt/terminal) dan menambah baris baru."
    },
    {
      question: "Apakah operator yang digunakan untuk perbandingan 'sama dengan' dalam Java?",
      options: [
        "=",
        "==",
        "===",
        "equals"
      ],
      correct: 1,
      explanation: "✅ Operator '==' membandingkan sama ada dua nilai adalah sama. Contoh: if (x == 5) { ... }"
    },
    {
      question: "Apakah kitaran hidup program Java?",
      options: [
        "Kompilasi → Interpretasi → Pelaksanaan",
        "Tulis → Debug → Hantar",
        "Design → Code → Test",
        "Plan → Code → Deploy"
      ],
      correct: 0,
      explanation: "✅ Java kod dikompilasi ke bytecode (.class), kemudian diinterpretasi oleh JVM untuk dilaksanakan."
    },
    {
      question: "Apakah maksud 'OOP' dalam Java?",
      options: [
        "Object-Oriented Programming",
        "Online Operation Protocol",
        "Output Optimization Process",
        "Object Operation Platform"
      ],
      correct: 0,
      explanation: "✅ OOP = Object-Oriented Programming. Java adalah bahasa berorientasi objek yang menggunakan konsep class dan object."
    },
    {
      question: "Apakah fungsi kata kunci 'class' dalam Java?",
      options: [
        "Mengisytiharkan pemboleh ubah",
        "Membuat gelung (loop)",
        "Mentakrifkan templat untuk objek",
        "Mengimport perpustakaan"
      ],
      correct: 2,
      explanation: "✅ 'class' adalah templat atau blueprint untuk mencipta objek. Ia mengandungi data (fields) dan methods."
    },
    {
      question: "Bagaimanakah anda membuat komen satu baris dalam Java?",
      options: [
        "/* komen */",
        "// komen",
        "# komen",
        "-- komen"
      ],
      correct: 1,
      explanation: "✅ '//' digunakan untuk komen satu baris. Contoh: // Ini adalah komen"
    },
    {
      question: "Apakah fungsi 'if' statement dalam Java?",
      options: [
        "Membuat gelung",
        "Membuat keputusan bersyarat",
        "Mengisytiharkan class",
        "Mencetak output"
      ],
      correct: 1,
      explanation: "✅ 'if' statement membuat keputusan berdasarkan syarat. Jika syarat benar, kod dalam blok if akan dilaksanakan."
    }
  ];

  const [availableQuestions, setAvailableQuestions] = useState([]);

  // === Lifecycle Effects ===
  useEffect(() => {
    if (gameStarted) {
      setCharacterPos({ x: 1, y: 1 });
      setCharacterDirection('down');
      setScore(0);
      setTimeLeft(120);
      setGameOver(false);
      setShowGameOverModal(false);
      setVisitedQuestions(new Set());
      setAvailableQuestions([...questionsPool].sort(() => 0.5 - Math.random()).slice(0, 10));
      setQuestionsAnswered(0);
      setCorrectAnswers(0);
      setStartTime(Date.now());
    }
  }, [gameStarted]);

  useEffect(() => {
    if (gameStarted && timeLeft > 0 && !gameOver && !showQuestion) {
      const timer = setTimeout(() => setTimeLeft(timeLeft - 1), 1000);
      return () => clearTimeout(timer);
    } else if (timeLeft === 0 && !gameOver) {
      handleGameOver('gagal');
    }
  }, [timeLeft, gameStarted, gameOver, showQuestion, score]);

  // === Game Over Handler ===
  const handleGameOver = async (status) => {
    setGameOver(true);
    
    // Save score and progress
    await saveScoreToDatabase(score, status);
    await submitToLeaderboard(score);
    await saveGameProgress();
    
    // Show game over modal after a short delay
    setTimeout(() => {
      setShowGameOverModal(true);
      setShowSummary(true);
    }, 500);
  };

  // === Movement Handler ===
  const handleKeyDown = useCallback((e) => {
    if (gameOver || showQuestion || !gameStarted || isMoving) return;
    if (!['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight'].includes(e.key)) return;

    e.preventDefault();
    const { x, y } = characterPos;
    let newX = x, newY = y, direction = characterDirection;

    switch (e.key) {
      case 'ArrowUp': newY = Math.max(0, y - 1); direction = 'up'; break;
      case 'ArrowDown': newY = Math.min(15, y + 1); direction = 'down'; break;
      case 'ArrowLeft': newX = Math.max(0, x - 1); direction = 'left'; break;
      case 'ArrowRight': newX = Math.min(15, x + 1); direction = 'right'; break;
      default: return;
    }

    if (maze[newY][newX] === 0) {
      setIsMoving(true);
      setCharacterDirection(direction);
      
      setTimeout(() => {
        setCharacterPos({ x: newX, y: newY });
        setIsMoving(false);
        
        const qIndex = questionPositions.findIndex(pos => pos.x === newX && pos.y === newY);
        if (qIndex !== -1 && !visitedQuestions.has(qIndex) && availableQuestions.length > 0) {
          triggerQuestion(qIndex);
        }
        
        if (newX === 14 && newY === 14) {
          const finalScore = score + 50;
          setScore(finalScore);
          handleGameOver('selesai');
        }
      }, 200);
    }
  }, [characterPos, gameOver, showQuestion, gameStarted, maze, visitedQuestions, isMoving, characterDirection, availableQuestions, score]);

  useEffect(() => {
    if (gameStarted && !gameOver) {
      window.addEventListener('keydown', handleKeyDown);
      return () => window.removeEventListener('keydown', handleKeyDown);
    }
  }, [gameStarted, gameOver, handleKeyDown]);

  // === Question Handler ===
  const triggerQuestion = (questionIndex) => {
    if (availableQuestions.length === 0) return;
    const randomIndex = Math.floor(Math.random() * availableQuestions.length);
    const q = availableQuestions[randomIndex];
    setCurrentQuestion(q);
    setShowQuestion(true);
    setSelectedAnswer(null);
    setIsCorrect(null);
    setExplanation('');
    setVisitedQuestions(prev => new Set([...prev, questionIndex]));
  };

  const handleAnswer = (index) => {
    if (selectedAnswer !== null) return;
    setSelectedAnswer(index);
    setQuestionsAnswered(prev => prev + 1);
    const correct = index === currentQuestion.correct;
    setIsCorrect(correct);
    setExplanation(currentQuestion.explanation);
    if (correct) {
      setCorrectAnswers(prev => prev + 1);
      setScore(prev => prev + 15);
    }
  };

  const startGame = () => {
    setGameStarted(true);
    startGameTracking(4);
  };

  const resetGame = () => {
    setGameStarted(false);
    setGameOver(false);
    setShowGameOverModal(false);
    setShowQuestion(false);
    setShowSummary(false);
    setShowLeaderboard(false);
    setUnlockedRewards([]);
    setScore(0);
    setCharacterPos({ x: 1, y: 1 });
    setCharacterDirection('down');
    setTimeLeft(120);
    setVisitedQuestions(new Set());
    setQuestionsAnswered(0);
    setCorrectAnswers(0);
    setStartTime(null);
  };

  // === Show Leaderboard Function ===
  const handleShowLeaderboard = () => {
    setShowGameOverModal(false);
    setShowLeaderboard(true);
  };

  // === Render Maze ===
  const renderMaze = () => {
    return maze.map((row, rowIndex) => (
      <div key={rowIndex} style={{ display: 'flex' }}>
        {row.map((cell, colIndex) => {
          const isWall = cell === 1;
          const isPlayer = characterPos.x === colIndex && characterPos.y === rowIndex;
          const isExit = colIndex === 14 && rowIndex === 14;

          const qIndex = questionPositions.findIndex(pos => pos.x === colIndex && pos.y === rowIndex);
          const isQuestionTile = qIndex !== -1 && cell === 0;
          const isVisitedQuestion = isQuestionTile && visitedQuestions.has(qIndex);

          let backgroundColor = isWall ? '#86C8BC' : '#F1F5F9';
          let content = '';
          let color = '#1E293B';
          let fontSize = '14px';

          if (isPlayer) {
            content = '🧑';
            color = '#FF6B6B';
            fontSize = '18px';
          } else if (isExit) {
            content = '🏁';
            color = '#66BB6A';
          } else if (isQuestionTile && !isVisitedQuestion) {
            content = '?';
            color = '#FFA726';
          } else if (isQuestionTile && isVisitedQuestion) {
            content = '✓';
            color = '#66BB6A';
          }

          return (
            <div
              key={`${rowIndex}-${colIndex}`}
              style={{
                width: '28px',
                height: '28px',
                display: 'flex',
                justifyContent: 'center',
                alignItems: 'center',
                backgroundColor,
                color,
                fontSize,
                fontWeight: 'bold',
                border: isWall ? 'none' : '1px solid #E2E8F0',
                borderRadius: '4px',
                position: 'relative',
                transition: 'all 0.2s ease',
                transform: isPlayer
                  ? (characterDirection === 'up' ? 'rotate(0deg)' :
                     characterDirection === 'right' ? 'rotate(90deg)' :
                     characterDirection === 'down' ? 'rotate(180deg)' :
                     'rotate(270deg)')
                  : 'none',
                animation: isQuestionTile && !isVisitedQuestion 
                  ? 'pulseAmber 2s infinite' 
                  : isPlayer ? 'glowCoral 2s infinite' : 'none',
                boxShadow: isPlayer 
                  ? '0 0 8px rgba(255, 107, 107, 0.5)' 
                  : isExit 
                    ? '0 0 8px rgba(102, 187, 106, 0.4)'
                    : 'none'
              }}
            >
              {content}
            </div>
          );
        })}
      </div>
    ));
  };

  // === START SCREEN ===
  if (!gameStarted) {
    return (
      <div style={{
        minHeight: '100vh',
        background: 'linear-gradient(135deg, #F8FAFC, #E2E8F0)',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        padding: '20px',
        fontFamily: '"Segoe UI", system-ui, sans-serif'
      }}>
        <div style={{
          background: '#FFFFFF',
          borderRadius: '20px',
          padding: '40px',
          maxWidth: '550px',
          width: '90%',
          textAlign: 'center',
          boxShadow: '0 12px 30px rgba(66, 153, 225, 0.15)',
          border: '1px solid #E2E8F0'
        }}>
          <h1 style={{
            fontSize: '2.4rem',
            color: '#1E293B',
            marginBottom: '8px',
            fontWeight: '800',
            letterSpacing: '-0.5px'
          }}>
            🌟 Labyrinth Java
          </h1>
          <div style={{ 
            color: '#64748B', 
            fontSize: '1.2rem', 
            marginBottom: '24px',
            fontWeight: '500'
          }}>
            Pembelajaran Pengaturcaraan Java Asas
          </div>

          <div style={{
            background: '#F8FAFC',
            borderRadius: '16px',
            padding: '24px',
            marginBottom: '28px',
            textAlign: 'left',
            border: '1px solid #E2E8F0'
          }}>
            <h3 style={{ 
              color: '#334155', 
              marginBottom: '16px', 
              fontWeight: '700',
              display: 'flex',
              alignItems: 'center',
              gap: '8px'
            }}>
              📚 Cara Bermain
            </h3>
            {[
              "Gunakan kekunci anak panah (↑ ↓ ← →) untuk bergerak",
              "Elakkan dinding (kotak hijau muda)",
              "Jawab soalan Java (?) untuk dapatkan markah",
              "Setiap jawapan betul: +15 markah",
              "Capai bendera (🏁) sebelum masa tamat!"
            ].map((item, i) => (
              <div key={i} style={{ 
                display: 'flex', 
                marginBottom: '12px',
                color: '#475569',
                fontSize: '1.05rem',
                lineHeight: 1.5
              }}>
                <span style={{ 
                  marginRight: '12px', 
                  color: '#4ECDC4',
                  fontWeight: 'bold',
                  fontSize: '1.2rem'
                }}>•</span>
                {item}
              </div>
            ))}
          </div>

          <button
            onClick={startGame}
            style={{
              background: 'linear-gradient(90deg, #4ECDC4, #44A08D)',
              color: 'white',
              border: 'none',
              padding: '16px 40px',
              fontSize: '1.15rem',
              borderRadius: '14px',
              cursor: 'pointer',
              fontWeight: '700',
              transition: 'all 0.3s ease',
              boxShadow: '0 6px 16px rgba(68, 160, 141, 0.4)',
              display: 'flex',
              alignItems: 'center',
              justifyContent: 'center',
              gap: '10px',
              margin: '0 auto'
            }}
          >
            ▶ Mula Permainan
          </button>
        </div>

        <style jsx>{`
          @keyframes pulseAmber {
            0%, 100% { opacity: 1; text-shadow: 0 0 6px rgba(255, 167, 38, 0.6); }
            50% { opacity: 0.8; text-shadow: 0 0 12px rgba(255, 167, 38, 0.9); }
          }
          @keyframes glowCoral {
            0%, 100% { box-shadow: 0 0 6px rgba(255, 107, 107, 0.5); }
            50% { box-shadow: 0 0 12px rgba(255, 107, 107, 0.8); }
          }
        `}</style>
      </div>
    );
  }

  // === GAME SCREEN ===
  return (
    <div style={{
      minHeight: '100vh',
      background: 'linear-gradient(135deg, #F8FAFC, #E2E8F0)',
      padding: '20px',
      fontFamily: '"Segoe UI", system-ui, sans-serif',
      color: '#1E293B'
    }}>
      {/* Header */}
      <div style={{
        background: '#FFFFFF',
        borderRadius: '16px',
        padding: '18px 28px',
        marginBottom: '24px',
        display: 'flex',
        justifyContent: 'space-between',
        alignItems: 'center',
        boxShadow: '0 4px 16px rgba(0,0,0,0.05)',
        border: '1px solid #E2E8F0'
      }}>
        <h1 style={{ 
          margin: 0,
          color: '#1E293B',
          fontSize: '1.8rem',
          fontWeight: 700
        }}>
          🌟 Labyrinth Java
        </h1>
        <div style={{ 
          display: 'flex', 
          gap: '28px',
          fontSize: '1rem',
          fontWeight: '600'
        }}>
          <div style={{ textAlign: 'center' }}>
            <div style={{ color: '#64748B', fontWeight: 500 }}>MARKAH</div>
            <div style={{ 
              fontSize: '1.6rem', 
              fontWeight: 800, 
              color: '#F59E0B'
            }}>{score}</div>
          </div>
          <div style={{ textAlign: 'center' }}>
            <div style={{ color: '#64748B', fontWeight: 500 }}>MASA</div>
            <div style={{ 
              fontSize: '1.6rem', 
              fontWeight: 800, 
              color: timeLeft > 30 ? '#10B981' : timeLeft > 10 ? '#F59E0B' : '#EF4444'
            }}>
              {timeLeft}s
            </div>
          </div>
          <div style={{ textAlign: 'center' }}>
            <div style={{ color: '#64748B', fontWeight: 500 }}>SOALAN</div>
            <div style={{ 
              fontSize: '1.6rem', 
              fontWeight: 800, 
              color: '#4ECDC4'
            }}>
              {visitedQuestions.size}/{availableQuestions.length}
            </div>
          </div>
        </div>
      </div>

      {/* Maze */}
      <div style={{
        background: '#FFFFFF',
        borderRadius: '20px',
        padding: '28px',
        display: 'inline-block',
        boxShadow: '0 8px 24px rgba(0,0,0,0.06)',
        border: '1px solid #E2E8F0'
      }}>
        <div style={{ 
          color: '#475569', 
          marginBottom: '22px',
          textAlign: 'center',
          fontSize: '1.1rem',
          fontWeight: 600
        }}>
          Gunakan kekunci anak panah untuk bergerak • Capai 🏁
        </div>
        
        {renderMaze()}

        <div style={{ 
          display: 'grid', 
          gridTemplateColumns: 'repeat(3, 1fr)',
          gap: '14px', 
          marginTop: '24px',
          padding: '16px',
          background: '#F8FAFC',
          borderRadius: '14px',
          border: '1px solid #E2E8F0',
          fontSize: '0.95rem',
          fontWeight: '600'
        }}>
          {[
            { symbol: '🧑', label: 'Pemain', color: '#FF6B6B' },
            { symbol: '🏁', label: 'Penamat', color: '#66BB6A' },
            { symbol: '?', label: 'Soalan', color: '#FFA726' },
            { symbol: '✓', label: 'Selesai', color: '#66BB6A' },
            { symbol: '█', label: 'Dinding', color: '#86C8BC' },
            { symbol: ' ', label: 'Laluan', color: '#F1F5F9' }
          ].map((item, i) => (
            <div key={i} style={{ 
              display: 'flex', 
              alignItems: 'center', 
              gap: '8px',
              justifyContent: 'center',
              color: '#334155'
            }}>
              <span style={{ color: item.color, fontSize: '1.2rem' }}>{item.symbol}</span>
              <span>{item.label}</span>
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
          background: 'rgba(248, 250, 252, 0.85)',
          display: 'flex',
          justifyContent: 'center',
          alignItems: 'center',
          zIndex: 1000
        }}>
          <div style={{
            background: '#FFFFFF',
            borderRadius: '20px',
            padding: '32px',
            maxWidth: '540px',
            width: '92%',
            boxShadow: '0 16px 40px rgba(0,0,0,0.12)',
            border: '1px solid #E2E8F0',
            color: '#1E293B'
          }}>
            <h3 style={{ 
              color: '#4ECDC4', 
              marginBottom: '20px',
              textAlign: 'center',
              fontSize: '1.5rem',
              fontWeight: 700
            }}>
              💡 Soalan Java Asas
            </h3>
            
            <p style={{
              fontSize: '1.15rem',
              lineHeight: 1.6,
              marginBottom: '24px',
              whiteSpace: 'pre-line',
              fontWeight: 500,
              color: '#334155'
            }}>
              {currentQuestion.question}
            </p>

            <div style={{ marginBottom: '24px' }}>
              {currentQuestion.options.map((option, index) => (
                <button
                  key={index}
                  onClick={() => handleAnswer(index)}
                  disabled={selectedAnswer !== null}
                  style={{
                    width: '100%',
                    padding: '15px',
                    marginBottom: '12px',
                    border: '2px solid #CBD5E1',
                    borderRadius: '12px',
                    background: selectedAnswer === null 
                      ? '#F8FAFC'
                      : index === currentQuestion.correct
                        ? '#DCFCE7'
                        : index === selectedAnswer
                          ? '#FEE2E2'
                          : '#F8FAFC',
                    color: selectedAnswer === null ? '#1E293B' : 'inherit',
                    cursor: selectedAnswer === null ? 'pointer' : 'default',
                    fontSize: '1.05rem',
                    textAlign: 'left',
                    transition: 'all 0.2s ease',
                    fontWeight: '600'
                  }}
                >
                  {String.fromCharCode(65 + index)}. {option}
                </button>
              ))}
            </div>

            {selectedAnswer !== null && (
              <div style={{
                padding: '18px',
                background: isCorrect 
                  ? '#DCFCE7' 
                  : '#FEE2E2',
                border: `2px solid ${isCorrect ? '#66BB6A' : '#EF5350'}`,
                borderRadius: '14px',
                color: isCorrect ? '#166534' : '#991B1B',
                fontWeight: 600,
                whiteSpace: 'pre-line',
                lineHeight: 1.5,
                fontSize: '1.05rem'
              }}>
                {explanation}
                
                <button
                  onClick={() => {
                    setShowQuestion(false);
                    setAvailableQuestions(prev => prev.filter(q => q !== currentQuestion));
                  }}
                  style={{
                    marginTop: '18px',
                    background: 'linear-gradient(90deg, #4ECDC4, #44A08D)',
                    color: 'white',
                    border: 'none',
                    padding: '12px 28px',
                    borderRadius: '12px',
                    fontWeight: '700',
                    cursor: 'pointer',
                    display: 'block',
                    margin: '0 auto',
                    boxShadow: '0 4px 10px rgba(68, 160, 141, 0.3)',
                    fontSize: '1.05rem'
                  }}
                >
                  ▶ Teruskan Permainan
                </button>
              </div>
            )}
          </div>
        </div>
      )}

      {/* Game Over Modal - SEPARATE from Leaderboard Modal */}
      {showGameOverModal && (
        <div style={{
          position: 'fixed',
          top: 0,
          left: 0,
          width: '100%',
          height: '100%',
          background: 'rgba(248, 250, 252, 0.95)',
          display: 'flex',
          justifyContent: 'center',
          alignItems: 'center',
          zIndex: 1000
        }}>
          <div style={{
            background: '#FFFFFF',
            borderRadius: '20px',
            padding: '40px',
            textAlign: 'center',
            maxWidth: '520px',
            width: '90%',
            boxShadow: '0 20px 50px rgba(0,0,0,0.1)',
            border: '1px solid #E2E8F0',
            color: '#1E293B'
          }}>
            <h2 style={{ 
              color: score >= 60 ? '#166534' : score >= 30 ? '#854D0E' : '#991B1B',
              marginBottom: '16px',
              fontSize: '2.2rem',
              fontWeight: 800
            }}>
              {score >= 60 ? '🎉 Tahniah!' : score >= 30 ? '👏 Bagus!' : '💪 Cuba Lagi!'}
            </h2>
            
            <div style={{ 
              fontSize: '3.8rem', 
              marginBottom: '20px',
              color: score >= 60 ? '#166534' : score >= 30 ? '#854D0E' : '#991B1B'
            }}>
              {score >= 60 ? '🏆' : score >= 30 ? '👍' : '📚'}
            </div>
            
            <div style={{ 
              background: '#F8FAFC', 
              borderRadius: '16px', 
              padding: '20px', 
              marginBottom: '24px',
              border: '1px solid #E2E8F0'
            }}>
              <p style={{ fontSize: '1.25rem', marginBottom: '8px', fontWeight: 600 }}>
                Markah Akhir: <strong style={{ color: '#F59E0B' }}>{score}</strong>
              </p>
              <p style={{ fontSize: '1.15rem', marginBottom: '6px' }}>
                Masa Baki: <strong>{timeLeft}s</strong>
              </p>
              <p style={{ fontSize: '1.15rem' }}>
                Soalan: <strong>{visitedQuestions.size}/{availableQuestions.length}</strong>
              </p>
            </div>

            {showSummary && (
              <div style={{ marginTop: '20px', marginBottom: '24px' }}>
                <GameSummary 
                  progress={gameProgress} 
                  game={{ name: 'Labyrinth Java' }} 
                />
                
                {unlockedRewards.length > 0 && (
                  <RewardsDisplay 
                    rewards={unlockedRewards}
                    onClaim={(reward) => {
                      console.log('Ganjaran dituntut:', reward);
                    }}
                  />
                )}
              </div>
            )}

            <div style={{ 
              display: 'flex',
              flexDirection: 'column',
              gap: '12px',
              marginTop: '20px'
            }}>
              <button
                onClick={() => {
                  // Play Again - reset everything
                  setGameStarted(true);
                  setGameOver(false);
                  setShowGameOverModal(false);
                  setShowQuestion(false);
                  setShowSummary(false);
                  setUnlockedRewards([]);
                  setScore(0);
                  setCharacterPos({ x: 1, y: 1 });
                  setCharacterDirection('down');
                  setTimeLeft(120);
                  setVisitedQuestions(new Set());
                  setAvailableQuestions([...questionsPool].sort(() => 0.5 - Math.random()).slice(0, 10));
                  setQuestionsAnswered(0);
                  setCorrectAnswers(0);
                  setStartTime(Date.now());
                }}
                style={{
                  background: 'linear-gradient(90deg, #4ECDC4, #44A08D)',
                  color: 'white',
                  border: 'none',
                  padding: '14px',
                  borderRadius: '14px',
                  cursor: 'pointer',
                  fontWeight: '700',
                  fontSize: '1.1rem',
                  boxShadow: '0 4px 12px rgba(68, 160, 141, 0.3)'
                }}
              >
                ▶ Main Semula
              </button>

              <button
                onClick={handleShowLeaderboard}
                style={{
                  background: 'linear-gradient(90deg, #F59E0B, #F97316)',
                  color: 'white',
                  border: 'none',
                  padding: '14px',
                  borderRadius: '14px',
                  cursor: 'pointer',
                  fontWeight: '700',
                  fontSize: '1.1rem',
                  boxShadow: '0 4px 12px rgba(245, 158, 11, 0.3)'
                }}
              >
                📊 Lihat Kedudukan
              </button>

              <button
                onClick={resetGame}
                style={{
                  background: 'white',
                  color: '#4ECDC4',
                  border: '2px solid #4ECDC4',
                  padding: '14px',
                  borderRadius: '14px',
                  cursor: 'pointer',
                  fontWeight: '700',
                  fontSize: '1.1rem',
                  transition: 'all 0.2s ease'
                }}
                onMouseEnter={e => e.target.style.background = '#F0FDFA'}
                onMouseLeave={e => e.target.style.background = 'white'}
              >
                ◀ Kembali ke Menu
              </button>
            </div>
          </div>
        </div>
      )}

      {/* LEADERBOARD MODAL - SEPARATE from Game Over Modal */}
      {showLeaderboard && (
        <div style={{
          position: 'fixed',
          top: 0,
          left: 0,
          width: '100%',
          height: '100%',
          background: 'rgba(0, 0, 0, 0.7)',
          display: 'flex',
          justifyContent: 'center',
          alignItems: 'center',
          zIndex: 2000
        }}>
          <div style={{ 
            width: '95%', 
            maxWidth: '900px',
            maxHeight: '85vh',
            overflowY: 'auto',
            borderRadius: '16px',
            background: '#FFFFFF',
            padding: '20px'
          }}>
            <div style={{
              display: 'flex',
              justifyContent: 'space-between',
              alignItems: 'center',
              marginBottom: '20px'
            }}>
              <h2 style={{ margin: 0, color: '#1E293B' }}>🏆 Kedudukan Pemain</h2>
              <button
                onClick={() => setShowLeaderboard(false)}
                style={{
                  background: '#EF4444',
                  color: 'white',
                  border: 'none',
                  padding: '8px 16px',
                  borderRadius: '8px',
                  cursor: 'pointer',
                  fontWeight: '600'
                }}
              >
                Tutup
              </button>
            </div>
            <Leaderboard 
              gameId="game4" 
              onClose={() => setShowLeaderboard(false)} 
            />
          </div>
        </div>
      )}

      {/* Animations */}
      <style jsx global>{`
        @keyframes pulseAmber {
          0%, 100% { opacity: 1; text-shadow: 0 0 6px rgba(255, 167, 38, 0.6); }
          50% { opacity: 0.8; text-shadow: 0 0 12px rgba(255, 167, 38, 0.9); }
        }
        @keyframes glowCoral {
          0%, 100% { box-shadow: 0 0 6px rgba(255, 107, 107, 0.5); }
          50% { box-shadow: 0 0 12px rgba(255, 107, 107, 0.8); }
        }
        body {
          margin: 0;
          overflow-x: hidden;
          background: linear-gradient(135deg, #F8FAFC, #E2E8F0);
        }
      `}</style>
    </div>
  );
};

export default MazeGame;