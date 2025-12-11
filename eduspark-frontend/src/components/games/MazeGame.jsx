import React, { useState, useEffect, useCallback } from 'react';
import { progressService } from '../../services/progressService';
import GameSummary from './GameSummary';
import RewardsDisplay from './RewardsDisplay';

const MazeGame = () => {
  // === All state hooks remain exactly the same ===
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

  // Database & Progress — unchanged
  const saveScoreToDatabase = async (finalScore, status) => {
    try {
      let playerId = localStorage.getItem('mazeGamePlayerId');
      if (!playerId) {
        playerId = 'player_' + Math.random().toString(36).substr(2, 9);
        localStorage.setItem('mazeGamePlayerId', playerId);
      }
      const gameId = 4;
      const response = await fetch('/save-game-score', {
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
    } catch (error) {
      console.error('❌ Failed to save score:', error);
    }
  };

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
        progress_data: {
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

  // Maze & Questions — updated for 16×16
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

  const questionsPool = [
    {
      question: "Apakah maksud 'pemboleh ubah' dalam pengaturcaraan?",
      options: [
        "Nilai yang sentiasa tetap",
        "Simpanan data yang boleh berubah nilainya",
        "Arahan untuk mengira",
        "Struktur data jenis senarai"
      ],
      correct: 1,
      explanation: "✅ Pemboleh ubah ialah lokasi ingatan yang menyimpan data yang nilainya boleh diubah semasa perlaksanaan atur cara."
    },
    {
      question: "Struktur kawalan manakah yang digunakan untuk membuat keputusan dalam atur cara?",
      options: [
        "for",
        "while",
        "if...else",
        "function"
      ],
      correct: 2,
      explanation: "✅ Struktur kawalan 'if...else' digunakan untuk membuat keputusan berdasarkan syarat (boolean)."
    },
    {
      question: "Apakah output bagi kod Python berikut?\n```x = 5\ny = 2\nprint(x // y)```",
      options: [
        "2.5",
        "2",
        "3",
        "10"
      ],
      correct: 1,
      explanation: "✅ Operator '//' adalah pembahagian integer (floor division). 5 // 2 = 2."
    },
    {
      question: "Apakah fungsi utama 'function' dalam pengaturcaraan?",
      options: [
        "Menyimpan data kekal",
        "Mengelakkan pengulangan kod",
        "Mencetak output ke skrin",
        "Menghubungkan ke pangkalan data"
      ],
      correct: 1,
      explanation: "✅ Fungsi membolehkan kod ditulis sekali dan diguna semula — meningkatkan kerekaan modular dan boleh selenggara."
    },
    {
      question: "Jenis data manakah yang sesuai untuk menyimpan 'True' atau 'False'?",
      options: [
        "integer",
        "string",
        "float",
        "boolean"
      ],
      correct: 3,
      explanation: "✅ Jenis data 'boolean' hanya mempunyai dua nilai: True atau False."
    },
    {
      question: "Apakah maksud 'komputasi awan'?",
      options: [
        "Penggunaan komputer riba untuk sambungan internet",
        "Perkhidmatan komputing yang disediakan melalui internet",
        "Sistem operasi berbasis awan",
        "Peranti storan fizikal berbentuk awan"
      ],
      correct: 1,
      explanation: "✅ Komputasi awan merujuk kepada perkhidmatan seperti penyimpanan, pemprosesan, dan aplikasi yang disampaikan melalui internet."
    },
    {
      question: "Antara berikut, yang manakah BUKAN model perkhidmatan komputasi awan?",
      options: [
        "IaaS",
        "PaaS",
        "SaaS",
        "CaaS"
      ],
      correct: 3,
      explanation: "✅ Model utama ialah IaaS (Infrastructure), PaaS (Platform), dan SaaS (Software)."
    },
    {
      question: "Apakah ciri utama 'komputasi selari'?",
      options: [
        "Satu tugas dilaksanakan oleh satu pemproses sahaja",
        "Beberapa tugas dilaksanakan serentak oleh pelbagai pemproses",
        "Atur cara dilaksanakan secara turutan",
        "Tiada keperluan untuk koordinasi"
      ],
      correct: 1,
      explanation: "✅ Komputasi selari melibatkan pelbagai pemproses/tetulang melaksanakan bahagian tugas secara serentak."
    },
    {
      question: "Apakah kelebihan utama komputasi awan kepada pengguna?",
      options: [
        "Kos permulaan yang tinggi",
        "Keperluan peranti keras berkuasa tinggi",
        "Akses dari mana-mana lokasi dengan internet",
        "Keselamatan data lebih rendah"
      ],
      correct: 2,
      explanation: "✅ Pengguna boleh mengakses perkhidmatan awan dari mana-mana peranti dengan sambungan internet."
    },
    {
      question: "Apakah maksud 'scalability' dalam komputasi awan?",
      options: [
        "Keupayaan sistem untuk mengecilkan sumber",
        "Keupayaan sistem untuk menyesuaikan sumber mengikut permintaan",
        "Kepantasan rangkaian sahaja",
        "Jumlah data yang boleh disimpan"
      ],
      correct: 1,
      explanation: "✅ 'Scalability' bermaksud sistem boleh dikembangkan atau dikecilkan mengikut keperluan."
    },
    {
      question: "Apakah output bagi kod Python ini?\n```for i in range(3):\n    print(i, end=' ')\n```",
      options: [
        "0 1 2",
        "1 2 3",
        "0 1 2 3",
        "3"
      ],
      correct: 0,
      explanation: "✅ `range(3)` menghasilkan 0, 1, 2."
    },
    {
      question: "Apakah fungsi 'cloud storage'?",
      options: [
        "Menyimpan data pada cakera keras fizikal di rumah",
        "Menyimpan data pada pelayan jauh melalui internet",
        "Memproses data tanpa internet",
        "Mengedit dokumen secara manual"
      ],
      correct: 1,
      explanation: "✅ Cloud storage (contoh: Google Drive) menyimpan data di pelayan jauh."
    }
  ];

  const [availableQuestions, setAvailableQuestions] = useState([]);

  // Game Lifecycle — unchanged logic
  useEffect(() => {
    if (gameStarted) {
      setCharacterPos({ x: 1, y: 1 });
      setCharacterDirection('down');
      setScore(0);
      setTimeLeft(120);
      setGameOver(false);
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
      setGameOver(true);
      saveScoreToDatabase(score, 'gagal');
    }
  }, [timeLeft, gameStarted, gameOver, showQuestion, score]);

  useEffect(() => {
    if (gameOver) {
      saveGameProgress();
      setShowSummary(true);
    }
  }, [gameOver]);

  // Movement — unchanged
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
          setGameOver(true);
          saveScoreToDatabase(finalScore, 'selesai');
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

  // Questions — updated: no auto-close
  const triggerQuestion = (questionIndex) => {
    if (availableQuestions.length === 0) return;
    const q = availableQuestions[Math.floor(Math.random() * availableQuestions.length)];
    setCurrentQuestion(q);
    setShowQuestion(true);
    setSelectedAnswer(null);
    setIsCorrect(null);
    setExplanation(currentQuestion?.explanation || '');
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
    setShowQuestion(false);
    setShowSummary(false);
    setUnlockedRewards([]);
    // Reset all game state variables
    setScore(0);
    setCharacterPos({ x: 1, y: 1 });
    setCharacterDirection('down');
    setTimeLeft(120);
    setVisitedQuestions(new Set());
    setQuestionsAnswered(0);
    setCorrectAnswers(0);
    setStartTime(null);
  };

  // ✅ NEW: Bright & Fun Maze Renderer
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

          // 🎨 Bright, fun colors!
          let backgroundColor = isWall 
            ? '#86C8BC' // Soft teal wall
            : '#F1F5F9'; // Light path

          let content = '';
          let color = '#1E293B'; // Dark text for contrast
          let fontWeight = 'bold';
          let fontSize = '14px';

          if (isPlayer) {
            content = '🧑'; // Friendly character
            color = '#FF6B6B'; // Coral
            fontSize = '18px';
          } else if (isExit) {
            content = '🏁'; // Finish flag (more universal)
            color = '#66BB6A'; // Success green
          } else if (isQuestionTile && !isVisitedQuestion) {
            content = '?';
            color = '#FFA726'; // Amber — warm & fun
          } else if (isQuestionTile && isVisitedQuestion) {
            content = '✓';
            color = '#66BB6A'; // Green check
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
                fontWeight,
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

  // ——— UI: START SCREEN (Bright & Inviting) ———
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
            🌟 Laluan Soalan
          </h1>
          <div style={{ 
            color: '#64748B', 
            fontSize: '1.2rem', 
            marginBottom: '24px',
            fontWeight: '500'
          }}>
            Sains Komputer Tingkatan 4 & 5
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
              "gunakan kekunci anak panah (↑ ↓ ← →) untuk bergerak",
              "elakkan dinding (kotak hijau muda)",
              "jawab soalan (?) untuk dapatkan markah",
              "setiap jawapan betul: +15 markah",
              "capai bendera (🏁) sebelum masa tamat!"
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

  // ——— UI: GAME SCREEN (Clean & Fun) ———
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
          🌟 Laluan Soalan
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

      {/* Maze Container */}
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

        {/* Legend — colorful & clear */}
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
              💡 Cabaran Sains Komputer
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

      {/* Game Over Modal */}
      {gameOver && (
        <div style={{
          position: 'fixed',
          top: 0,
          left: 0,
          width: '100%',
          height: '100%',
          background: 'rgba(248, 250, 252, 0.9)',
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
                  game={{ name: 'Laluan Soalan' }} 
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

            {/* ✅ Three clear options — all stay on MazeGame */}
            <div style={{ 
              display: 'flex',
              flexDirection: 'column',
              gap: '12px',
              marginTop: '20px'
            }}>
              {/* 1. Main Semula — restart game immediately */}
              <button
                onClick={() => {
                  // Reset game state
                  setGameOver(false);
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

              {/* 2. Kembali ke Menu Permainan — goes back to START SCREEN */}
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
                ◀ Kembali ke Menu Permainan
              </button>
            </div>
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