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
  const [showGameOverModal, setShowGameOverModal] = useState(false);
  const [gameSummaryData, setGameSummaryData] = useState(null);
  const [leaderboardData, setLeaderboardData] = useState(null);
  const [isLoadingSummary, setIsLoadingSummary] = useState(false);

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

  // Get CSRF Token from Laravel
  const getCsrfToken = () => {
    const metaTag = document.querySelector('meta[name="csrf-token"]');
    return metaTag ? metaTag.content : '';
  };

  // ========== NEW: Save score to Laravel ==========
  const saveScoreToDatabase = async (finalScore, status) => {
    try {
      let playerId = localStorage.getItem('mazeGamePlayerId');
      if (!playerId) {
        playerId = 'player_' + Math.random().toString(36).substr(2, 9);
        localStorage.setItem('mazeGamePlayerId', playerId);
      }
      
      const gameId = 4;
      const timeTaken = startTime ? Math.floor((Date.now() - startTime) / 1000) : 120 - timeLeft;
      
      console.log('Saving maze game score to database...', {
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
            questions_answered: questionsAnswered,
            correct_answers: correctAnswers,
            accuracy: questionsAnswered > 0 ? ((correctAnswers / questionsAnswered) * 100).toFixed(1) + '%' : '0%',
            maze_completed: characterPos.x === 14 && characterPos.y === 14
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
            questions_answered: questionsAnswered,
            correct_answers: correctAnswers,
            accuracy: questionsAnswered > 0 ? ((correctAnswers / questionsAnswered) * 100).toFixed(1) + '%' : '0%',
            maze_completed: characterPos.x === 14 && characterPos.y === 14
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
      const saveResult = await saveScoreToDatabase(score, 'selesai');
      
      if (!saveResult || !saveResult.success) {
        console.warn('Score save may have failed, but continuing...');
      }
      
      // Then get game summary
      const gameId = 4;
      let playerId = localStorage.getItem('mazeGamePlayerId');
      if (!playerId) {
        playerId = 'player_' + Math.random().toString(36).substr(2, 9);
        localStorage.setItem('mazeGamePlayerId', playerId);
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
    const timeTaken = startTime ? Math.floor((Date.now() - startTime) / 1000) : 120 - timeLeft;
    const xpEarned = Math.floor(score / 10);
    const coinsEarned = Math.floor(score / 100);
    const accuracy = questionsAnswered > 0 ? Math.floor((correctAnswers / questionsAnswered) * 100) : 0;
    const completionBonus = characterPos.x === 14 && characterPos.y === 14 ? 50 : 0;
    
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
    
    if (completionBonus > 0) {
      rewards.push({
        type: 'achievement',
        name: 'Penjelajah Labirin',
        description: 'Berjaya mencapai hujung labirin',
        badge: 'explorer',
        icon: '🏆'
      });
    }
    
    if (accuracy >= 80) {
      rewards.push({
        type: 'achievement',
        name: 'Ahli Java',
        description: 'Ketepatan jawapan melebihi 80%',
        badge: 'expert',
        icon: '🎯'
      });
    }
    
    if (questionsAnswered >= 8) {
      rewards.push({
        type: 'achievement',
        name: 'Pelajar Rajin',
        description: 'Menjawab 8 soalan atau lebih',
        badge: 'diligent',
        icon: '📚'
      });
    }
    
    if (timeLeft > 60) {
      rewards.push({
        type: 'achievement',
        name: 'Pantas Tangkas',
        description: 'Selesai dengan lebih 60 saat baki',
        badge: 'fast',
        icon: '⚡'
      });
    }
    
    setGameSummaryData({
      score: score + completionBonus,
      time_taken: timeTaken,
      rank: 1,
      total_players: 1,
      accuracy: accuracy,
      rewards: rewards,
      game_title: 'Labyrinth Java',
      game_id: 4,
      user_name: 'Pemain',
      xp_earned: xpEarned,
      coins_earned: coinsEarned,
      questions_answered: questionsAnswered,
      correct_answers: correctAnswers,
      maze_completed: characterPos.x === 14 && characterPos.y === 14,
      time_left: timeLeft
    });
  };

  // ========== NEW: Load leaderboard data ==========
  const loadLeaderboard = async () => {
    try {
      const gameId = 4;
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
      const finalScoreWithBonus = score + (characterPos.x === 14 && characterPos.y === 14 ? 50 : 0);
      
      setLeaderboardData({
        success: true,
        leaderboard: [
          { rank: 1, user_name: 'Ali', score: 300, time_taken: 45, is_current_user: false },
          { rank: 2, user_name: 'Siti', score: 280, time_taken: 60, is_current_user: false },
          { rank: 3, user_name: 'Ahmad', score: 250, time_taken: 75, is_current_user: false },
          { rank: 4, user_name: 'Pemain', score: finalScoreWithBonus, time_taken: 120 - timeLeft, is_current_user: true },
          { rank: 5, user_name: 'Muthu', score: 220, time_taken: 90, is_current_user: false }
        ],
        user_rank: 4,
        user_score: finalScoreWithBonus,
        user_time: 120 - timeLeft,
        total_players: 5,
        game_id: 4,
        game_title: 'Labyrinth Java'
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
          game_id: 4,
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
          'X-CSRF-TOKEN': getCsrfToken()
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
      setGameSummaryData(null);
      setLeaderboardData(null);
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
    
    // Save progress
    await saveGameProgress();
    
    // Load game summary and leaderboard
    await loadGameSummary();
    await loadLeaderboard();
    
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
    setGameSummaryData(null);
    setLeaderboardData(null);
  };

  // === Show Leaderboard Function ===
  const handleShowLeaderboard = () => {
    setShowGameOverModal(false);
    setShowLeaderboard(true);
  };

  // ========== NEW: Custom Game Summary Modal ==========
  const GameSummaryModal = () => {
    if (!gameSummaryData || !showGameOverModal) return null;

    return (
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
          padding: '30px',
          textAlign: 'center',
          maxWidth: '600px',
          width: '95%',
          boxShadow: '0 20px 50px rgba(0,0,0,0.1)',
          border: '3px solid #4ecca3',
          color: '#1E293B'
        }}>
          <h2 style={{ 
            fontSize: '2.2rem', 
            color: '#4ecca3',
            textAlign: 'center',
            marginBottom: '25px',
            textShadow: '0 0 8px rgba(78, 204, 163, 0.8)'
          }}>🧩 Permainan Tamat!</h2>
          
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
                    Soalan
                  </div>
                  <div style={{ fontSize: '24px', fontWeight: 'bold', color: '#9C27B0' }}>
                    {gameSummaryData.questions_answered}
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
                  <span>Status Labirin:</span>
                  <span style={{ color: gameSummaryData.maze_completed ? '#4CAF50' : '#FF5722', fontWeight: 'bold' }}>
                    {gameSummaryData.masa_completed ? 'Selesai' : 'Tidak Selesai'}
                  </span>
                </div>
                <div style={{ display: 'flex', justifyContent: 'space-between' }}>
                  <span>Jawapan Betul:</span>
                  <span style={{ color: '#4ecca3', fontWeight: 'bold' }}>
                    {gameSummaryData.correct_answers}/{gameSummaryData.questions_answered}
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
                    setShowGameOverModal(false);
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
                  onClick={handleShowLeaderboard}
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
                    setShowGameOverModal(false);
                  }}
                >
                  ← Kembali ke Menu
                </button>
              </div>
            </>
          )}
        </div>
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
              🏆 Papan Pemimpin Labirin Java
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

      {/* NEW: Game Summary Modal */}
      <GameSummaryModal />
      
      {/* NEW: Leaderboard Modal */}
      <LeaderboardModal />

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
        @keyframes spin {
          0% { transform: rotate(0deg); }
          100% { transform: rotate(360deg); }
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