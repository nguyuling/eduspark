@extends('layouts.app')

@section('content')
<div class="app">
    <main class="main">
        <div id="gameContainer" style="padding: 20px;">
            <!-- Game Header -->
            <div id="gameHeader" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <div>
                    <h1 style="margin: 0; font-size: 28px; font-weight: 700;">❓ Quiz Challenge</h1>
                    <p style="color: var(--muted); margin-top: 8px;">Cabaran kuiz pantas - Jawab soalan dengan betul sebanyak mungkin!</p>
                </div>
                <div style="display: flex; gap: 20px; align-items: center;">
                    <div style="text-align: center;">
                        <div style="color: var(--muted); font-size: 12px; font-weight: 600; text-transform: uppercase;">Skor</div>
                        <div id="scoreDisplay" style="font-size: 32px; font-weight: 700; color: var(--accent);">0</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="color: var(--muted); font-size: 12px; font-weight: 600; text-transform: uppercase;">Masa</div>
                        <div id="timerDisplay" style="font-size: 32px; font-weight: 700; color: #ef4444;">60</div>
                    </div>
                </div>
            </div>

            <!-- Game Area -->
            <div id="gameContent" style="display: none;">
                <div style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 40px; max-width: 600px; margin: 0 auto;">
                    <div id="questionNumber" style="color: var(--muted); font-size: 12px; font-weight: 600; margin-bottom: 12px;">Soalan 1 of 10</div>
                    <h2 id="questionText" style="font-size: 24px; font-weight: 700; margin-bottom: 30px; color: var(--text);">Soalan akan dipaparkan di sini</h2>
                    
                    <div id="answersContainer" style="display: grid; grid-template-columns: 1fr; gap: 12px;">
                        <!-- Answers will be inserted here -->
                    </div>
                </div>
            </div>

            <!-- Start Screen -->
            <div id="startScreen" style="text-align: center; padding: 60px 20px;">
                <div style="font-size: 64px; margin-bottom: 20px;">❓</div>
                <h2 style="font-size: 32px; font-weight: 700; margin-bottom: 12px;">Quiz Challenge</h2>
                <p style="color: var(--muted); font-size: 16px; margin-bottom: 30px; max-width: 500px; margin-left: auto; margin-right: auto;">
                    Anda akan mendapat 10 soalan kuiz yang berbeza. Jawab dengan betul sebanyak mungkin dalam 60 saat. Setiap jawapan yang betul memberikan 10 poin!
                </p>
                <button id="startBtn" style="padding: 14px 40px; background: linear-gradient(90deg, var(--accent), #9d4edd); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 700; cursor: pointer; transition: transform 0.2s ease;">
                    Mula Bermain ▶
                </button>
            </div>

            <!-- Game Over Screen -->
            <div id="gameOverScreen" style="display: none; text-align: center; padding: 60px 20px;">
                <div style="font-size: 64px; margin-bottom: 20px;">🎉</div>
                <h2 style="font-size: 32px; font-weight: 700; margin-bottom: 12px;">Permainan Tamat!</h2>
                <div style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 40px; max-width: 400px; margin: 20px auto; margin-bottom: 30px;">
                    <div style="margin-bottom: 20px;">
                        <div style="color: var(--muted); font-size: 12px; font-weight: 600; text-transform: uppercase; margin-bottom: 8px;">Skor Akhir</div>
                        <div id="finalScore" style="font-size: 48px; font-weight: 700; color: var(--accent);">0</div>
                    </div>
                    <div>
                        <div style="color: var(--muted); font-size: 12px; font-weight: 600; text-transform: uppercase; margin-bottom: 8px;">Soalan Dijawab dengan Betul</div>
                        <div id="correctAnswers" style="font-size: 28px; font-weight: 700; color: #22c55e;">0/10</div>
                    </div>
                </div>
                <div style="display: flex; gap: 12px; justify-content: center;">
                    <button id="playAgainBtn" style="padding: 12px 30px; background: var(--accent); color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer;">
                        Main Semula
                    </button>
                    <a href="/games" style="padding: 12px 30px; background: var(--border); color: var(--text); border: none; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; text-decoration: none; display: inline-block;">
                        Kembali ke Permainan
                    </a>
                </div>
            </div>
        </div>
    </main>
</div>

<style>
    .answer-btn {
        padding: 16px 20px;
        background: var(--card-bg);
        border: 2px solid var(--border);
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: left;
        color: var(--text);
    }

    .answer-btn:hover:not(:disabled) {
        border-color: var(--accent);
        background: rgba(106, 77, 247, 0.1);
    }

    .answer-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .answer-btn.correct {
        background: rgba(34, 197, 94, 0.2);
        border-color: #22c55e;
        color: #22c55e;
    }

    .answer-btn.incorrect {
        background: rgba(239, 68, 68, 0.2);
        border-color: #ef4444;
        color: #ef4444;
    }

    #startBtn:hover {
        transform: scale(1.05);
    }

    #startBtn:active {
        transform: scale(0.95);
    }
</style>

<script>
    // Quiz Data
    const quizzes = [
        {
            question: "Apakah output bagi kod berikut? print(2 ** 3)",
            answers: ["6", "8", "2", "3"],
            correct: 1
        },
        {
            question: "Pemboleh ubah dalam Python adalah...",
            answers: ["Rak penyimpanan data", "Fungsi", "Loop", "Pemindahan data"],
            correct: 0
        },
        {
            question: "Simbol apakah yang digunakan untuk ulasan baris tunggal dalam Python?",
            answers: ["//", "#", "/*", "-->"],
            correct: 1
        },
        {
            question: "Fungsi mana yang digunakan untuk mendapatkan panjang senarai?",
            answers: ["size()", "width()", "len()", "length()"],
            correct: 2
        },
        {
            question: "Apakah keluaran str(123)?",
            answers: ["123 sebagai integer", "123 sebagai string", "Ralat", "None"],
            correct: 1
        },
        {
            question: "Loop manakah yang berjalan sehingga syarat palsu?",
            answers: ["for loop", "while loop", "if loop", "range loop"],
            correct: 1
        },
        {
            question: "Fungsi apakah yang digunakan untuk membaca input pengguna?",
            answers: ["read()", "input()", "get()", "scan()"],
            correct: 1
        },
        {
            question: "Indeks pertama dalam senarai Python adalah...",
            answers: ["1", "0", "-1", "null"],
            correct: 1
        },
        {
            question: "Apakah jenis data bool dalam Python?",
            answers: ["Nombor", "Boolean benar/palsu", "String", "Senarai"],
            correct: 1
        },
        {
            question: "Operator logik yang manakah mengembalikan True jika kedua-duanya True?",
            answers: ["or", "and", "not", "xor"],
            correct: 1
        }
    ];

    // Game State
    let currentQuestion = 0;
    let score = 0;
    let correctCount = 0;
    let timeLeft = 60;
    let gameStarted = false;
    let gameActive = false;
    let selectedAnswer = null;

    const elements = {
        gameContainer: document.getElementById('gameContainer'),
        gameHeader: document.getElementById('gameHeader'),
        gameContent: document.getElementById('gameContent'),
        startScreen: document.getElementById('startScreen'),
        gameOverScreen: document.getElementById('gameOverScreen'),
        scoreDisplay: document.getElementById('scoreDisplay'),
        timerDisplay: document.getElementById('timerDisplay'),
        questionNumber: document.getElementById('questionNumber'),
        questionText: document.getElementById('questionText'),
        answersContainer: document.getElementById('answersContainer'),
        startBtn: document.getElementById('startBtn'),
        playAgainBtn: document.getElementById('playAgainBtn'),
        finalScore: document.getElementById('finalScore'),
        correctAnswers: document.getElementById('correctAnswers')
    };

    // Event Listeners
    elements.startBtn.addEventListener('click', startGame);
    elements.playAgainBtn.addEventListener('click', resetGame);

    function startGame() {
        gameStarted = true;
        gameActive = true;
        currentQuestion = 0;
        score = 0;
        correctCount = 0;
        timeLeft = 60;
        selectedAnswer = null;

        elements.startScreen.style.display = 'none';
        elements.gameContent.style.display = 'block';
        elements.gameOverScreen.style.display = 'none';
        elements.gameHeader.style.display = 'flex';

        loadQuestion();
        startTimer();
    }

    function loadQuestion() {
        if (currentQuestion >= quizzes.length || timeLeft <= 0) {
            endGame();
            return;
        }

        const quiz = quizzes[currentQuestion];
        elements.questionNumber.textContent = `Soalan ${currentQuestion + 1} of ${quizzes.length}`;
        elements.questionText.textContent = quiz.question;
        
        elements.answersContainer.innerHTML = '';
        quiz.answers.forEach((answer, index) => {
            const btn = document.createElement('button');
            btn.className = 'answer-btn';
            btn.textContent = answer;
            btn.disabled = !gameActive;
            btn.addEventListener('click', () => selectAnswer(index, quiz.correct));
            elements.answersContainer.appendChild(btn);
        });
    }

    function selectAnswer(index, correctIndex) {
        if (!gameActive) return;

        gameActive = false;
        const buttons = document.querySelectorAll('.answer-btn');
        
        if (index === correctIndex) {
            buttons[index].classList.add('correct');
            score += 10;
            correctCount++;
            elements.scoreDisplay.textContent = score;
        } else {
            buttons[index].classList.add('incorrect');
            buttons[correctIndex].classList.add('correct');
        }

        buttons.forEach(btn => btn.disabled = true);

        setTimeout(() => {
            currentQuestion++;
            gameActive = true;
            loadQuestion();
        }, 1500);
    }

    function startTimer() {
        const timerInterval = setInterval(() => {
            if (!gameStarted) {
                clearInterval(timerInterval);
                return;
            }

            timeLeft--;
            elements.timerDisplay.textContent = timeLeft;

            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                endGame();
            }
        }, 1000);
    }

    function endGame() {
        gameStarted = false;
        gameActive = false;
        elements.gameContent.style.display = 'none';
        elements.gameHeader.style.display = 'none';
        elements.gameOverScreen.style.display = 'block';
        elements.finalScore.textContent = score;
        elements.correctAnswers.textContent = `${correctCount}/${quizzes.length}`;
    }

    function resetGame() {
        timeLeft = 60;
        elements.timerDisplay.textContent = '60';
        elements.scoreDisplay.textContent = '0';
        elements.gameOverScreen.style.display = 'none';
        elements.startScreen.style.display = 'block';
    }
</script>
@endsection
