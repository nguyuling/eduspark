@extends('layouts.app')

@section('content')
<div class="app">
    <main class="main">
        <div id="gameContainer" style="padding: 20px;">
            <!-- Game Header -->
            <div id="gameHeader" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <div>
                    <h1 style="margin: 0; font-size: 28px; font-weight: 700;">🔨 Whack-a-Mole</h1>
                    <p style="color: var(--muted); margin-top: 8px;">Tumbuk tikus secepat mungkin untuk mendapat markah tertinggi!</p>
                </div>
                <div style="display: flex; gap: 20px; align-items: center;">
                    <div style="text-align: center;">
                        <div style="color: var(--muted); font-size: 12px; font-weight: 600; text-transform: uppercase;">Skor</div>
                        <div id="scoreDisplay" style="font-size: 32px; font-weight: 700; color: var(--accent);">0</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="color: var(--muted); font-size: 12px; font-weight: 600; text-transform: uppercase;">Masa</div>
                        <div id="timerDisplay" style="font-size: 32px; font-weight: 700; color: #ef4444;">30</div>
                    </div>
                </div>
            </div>

            <!-- Game Area -->
            <div id="gameContent" style="display: none;">
                <div style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 40px; max-width: 600px; margin: 0 auto;">
                    <div id="gameBoard" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; max-width: 400px; margin: 0 auto;">
                        <!-- Moles will be inserted here -->
                    </div>
                </div>
            </div>

            <!-- Start Screen -->
            <div id="startScreen" style="text-align: center; padding: 60px 20px;">
                <div style="font-size: 64px; margin-bottom: 20px;">🔨</div>
                <h2 style="font-size: 32px; font-weight: 700; margin-bottom: 12px;">Whack-a-Mole</h2>
                <p style="color: var(--muted); font-size: 16px; margin-bottom: 30px; max-width: 500px; margin-left: auto; margin-right: auto;">
                    Tumbuk tikus yang muncul sebanyak mungkin dalam 30 saat! Setiap tumbukan yang tepat memberikan 10 poin.
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
                        <div style="color: var(--muted); font-size: 12px; font-weight: 600; text-transform: uppercase; margin-bottom: 8px;">Tumbukan Tepat</div>
                        <div id="moleHits" style="font-size: 28px; font-weight: 700; color: #22c55e;">0</div>
                    </div>
                </div>
                <div style="display: flex; gap: 12px; justify-content: center;">
                    <button id="playAgainBtn" style="padding: 12px 30px; background: var(--accent); color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer;">
                        Main Semula
                    </button>
                    <a href="{{ route('games.index') }}" style="padding: 12px 30px; background: var(--border); color: var(--text); border: none; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; text-decoration: none; display: inline-block;">
                        Kembali ke Permainan
                    </a>
                </div>
            </div>
        </div>
    </main>
</div>

<style>
    .mole-hole {
        width: 100%;
        aspect-ratio: 1 / 1;
        background: linear-gradient(135deg, #8B6F47 0%, #A0826D 100%);
        border-radius: 50%;
        border: 4px solid #654321;
        position: relative;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
        transition: all 0.1s ease;
        user-select: none;
    }

    .mole-hole:hover:not(.empty) {
        transform: scale(1.05);
    }

    .mole-hole.empty {
        cursor: default;
    }

    .mole-hole.hit {
        animation: hitAnimation 0.3s ease;
    }

    @keyframes hitAnimation {
        0% { transform: scale(1); }
        50% { transform: scale(0.8); }
        100% { transform: scale(1); }
    }

    #startBtn:hover {
        transform: scale(1.05);
    }

    #startBtn:active {
        transform: scale(0.95);
    }
</style>

<script>
    // Game State
    let score = 0;
    let timeLeft = 30;
    let gameStarted = false;
    let gameActive = false;
    let moleHitCount = 0;
    let activeMole = null;

    const elements = {
        gameContainer: document.getElementById('gameContainer'),
        gameHeader: document.getElementById('gameHeader'),
        gameContent: document.getElementById('gameContent'),
        startScreen: document.getElementById('startScreen'),
        gameOverScreen: document.getElementById('gameOverScreen'),
        scoreDisplay: document.getElementById('scoreDisplay'),
        timerDisplay: document.getElementById('timerDisplay'),
        gameBoard: document.getElementById('gameBoard'),
        startBtn: document.getElementById('startBtn'),
        playAgainBtn: document.getElementById('playAgainBtn'),
        finalScore: document.getElementById('finalScore'),
        moleHits: document.getElementById('moleHits')
    };

    // Event Listeners
    elements.startBtn.addEventListener('click', startGame);
    elements.playAgainBtn.addEventListener('click', resetGame);

    function startGame() {
        gameStarted = true;
        gameActive = true;
        score = 0;
        timeLeft = 30;
        moleHitCount = 0;

        elements.startScreen.style.display = 'none';
        elements.gameContent.style.display = 'block';
        elements.gameOverScreen.style.display = 'none';
        elements.gameHeader.style.display = 'flex';

        elements.scoreDisplay.textContent = '0';
        elements.timerDisplay.textContent = '30';

        createBoard();
        startMoles();
        startTimer();
    }

    function createBoard() {
        elements.gameBoard.innerHTML = '';
        for (let i = 0; i < 9; i++) {
            const hole = document.createElement('div');
            hole.className = 'mole-hole empty';
            hole.id = `mole-${i}`;
            hole.textContent = '';
            hole.addEventListener('click', () => hitMole(hole));
            elements.gameBoard.appendChild(hole);
        }
    }

    function startMoles() {
        const moleInterval = setInterval(() => {
            if (!gameActive) {
                clearInterval(moleInterval);
                return;
            }

            if (activeMole) {
                activeMole.classList.remove('active');
                activeMole.textContent = '';
                activeMole.classList.add('empty');
            }

            const randomIndex = Math.floor(Math.random() * 9);
            activeMole = document.getElementById(`mole-${randomIndex}`);
            activeMole.textContent = '🐭';
            activeMole.classList.remove('empty');
        }, 600);
    }

    function hitMole(hole) {
        if (!gameActive || hole.classList.contains('empty')) return;

        hole.classList.add('hit');
        hole.textContent = '💥';
        score += 10;
        moleHitCount++;
        elements.scoreDisplay.textContent = score;

        setTimeout(() => {
            hole.textContent = '';
            hole.classList.remove('active', 'hit');
            hole.classList.add('empty');
        }, 300);
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
        elements.moleHits.textContent = moleHitCount;

        if (activeMole) {
            activeMole.textContent = '';
            activeMole.classList.add('empty');
        }
    }

    function resetGame() {
        timeLeft = 30;
        elements.timerDisplay.textContent = '30';
        elements.scoreDisplay.textContent = '0';
        elements.gameOverScreen.style.display = 'none';
        elements.startScreen.style.display = 'block';
    }
</script>
@endsection
