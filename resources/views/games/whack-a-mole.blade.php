@extends('layouts.app')

@section('content')
<div class="app">
    <main class="main">
        <div id="gameContainer">
            <!-- Game Header -->
            <div id="gameHeader" class="header" style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <div class="title">Whack-a-Mole</div>
                    <div class="sub">Tumbuk tikus secepat mungkin untuk mendapat markah tertinggi!</div>
                </div>
                <div style="display: flex; gap: 40px; align-items: center;">
                    <div style="display: flex; gap: 40px; align-items: center;">
                        <div style="text-align: center;">
                            <div style="color: var(--muted); font-size: 12px; font-weight: 600; text-transform: uppercase; margin-bottom: 8px;">Skor</div>
                            <div id="scoreDisplay" style="font-size: 36px; font-weight: 700; color: var(--accent);">0</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="color: var(--muted); font-size: 12px; font-weight: 600; text-transform: uppercase; margin-bottom: 8px;">Masa</div>
                            <div id="timerDisplay" style="font-size: 36px; font-weight: 700; color: #ef4444;">30</div>
                        </div>
                    </div>
                    <a href="{{ route('games.index') }}" class="btn-kembali">
                        <i class="bi bi-arrow-left"></i>Kembali
                    </a>
                </div>
            </div>

            <!-- Game Area -->
            <div id="gameContent" style="display: none; margin-left: 40px; margin-right: 40px;">
                <section class="panel" style="margin-bottom: 20px;">
                    <div style="display: flex; justify-content: center; padding: 40px;">
                        <div id="gameBoard" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; max-width: 500px; width: 100%;">
                            <!-- Moles will be inserted here -->
                        </div>
                    </div>
                </section>
            </div>

            <!-- Start Screen -->
            <div id="startScreen" style="text-align: center; padding: 80px 40px; margin-left: 40px; margin-right: 40px;">
                <div style="font-size: 80px; margin-bottom: 20px;">🔨</div>
                <h2 style="font-size: 36px; font-weight: 700; margin-bottom: 16px;">Whack-a-Mole</h2>
                <p style="color: var(--muted); font-size: 16px; margin-bottom: 40px; max-width: 500px; margin-left: auto; margin-right: auto; line-height: 1.6;">
                    Tumbuk tikus yang muncul sebanyak mungkin dalam 30 saat! Setiap tumbukan yang tepat memberikan 10 poin.
                </p>
                <button id="startBtn" style="display: inline-flex; align-items: center; gap: 8px; padding: 14px 32px; background: linear-gradient(90deg, #A855F7, #9333EA); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 700; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 2px 8px rgba(168, 85, 247, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(168, 85, 247, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(168, 85, 247, 0.3)';">
                    <i class="bi bi-play-fill"></i> Mula Bermain
                </button>
            </div>

            <!-- Game Over Screen -->
            <div id="gameOverScreen" style="display: none; text-align: center; padding: 80px 40px; margin-left: 40px; margin-right: 40px;">
                <div style="font-size: 80px; margin-bottom: 20px;">🎉</div>
                <h2 style="font-size: 36px; font-weight: 700; margin-bottom: 32px;">Permainan Tamat!</h2>
                <section class="panel" style="max-width: 450px; margin: 0 auto 40px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; padding: 32px;">
                        <div>
                            <div style="color: var(--muted); font-size: 12px; font-weight: 600; text-transform: uppercase; margin-bottom: 8px;">Skor Akhir</div>
                            <div id="finalScore" style="font-size: 48px; font-weight: 700; color: var(--accent);">0</div>
                        </div>
                        <div>
                            <div style="color: var(--muted); font-size: 12px; font-weight: 600; text-transform: uppercase; margin-bottom: 8px;">Tumbukan Tepat</div>
                            <div id="moleHits" style="font-size: 48px; font-weight: 700; color: #22c55e;">0</div>
                        </div>
                    </div>
                </section>
                <div style="display: flex; gap: 12px; justify-content: center;">
                    <button id="playAgainBtn" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 28px; background: linear-gradient(90deg, #A855F7, #9333EA); color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; transition: all 0.2s ease;" onmouseover="this.style.transform='translateY(-2px)';" onmouseout="this.style.transform='translateY(0)';">
                        <i class="bi bi-arrow-clockwise"></i> Main Semula
                    </button>
                    <a href="/games" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 28px; background: transparent; color: var(--accent); border: 2px solid var(--accent); border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; text-decoration: none; transition: all 0.2s ease;" onmouseover="this.style.background='rgba(168, 85, 247, 0.1)';" onmouseout="this.style.background='transparent';">
                        <i class="bi bi-arrow-left"></i> Kembali ke Permainan
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
        font-size: 48px;
        transition: all 0.1s ease;
        user-select: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .mole-hole:hover:not(.empty) {
        transform: scale(1.08);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
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

    #startBtn:active {
        transform: scale(0.95) !important;
    }

    #playAgainBtn:active {
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
    let gameStartTime = 0;

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
        gameStartTime = Date.now();

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
        
        const gameEndTime = Date.now();
        const timeInSeconds = Math.floor((gameEndTime - gameStartTime) / 1000);
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("games.storeResult", 2) }}';
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        const scoreInput = document.createElement('input');
        scoreInput.type = 'hidden';
        scoreInput.name = 'score';
        scoreInput.value = score;
        form.appendChild(scoreInput);
        
        const timeInput = document.createElement('input');
        timeInput.type = 'hidden';
        timeInput.name = 'time_taken';
        timeInput.value = timeInSeconds;
        form.appendChild(timeInput);
        
        document.body.appendChild(form);
        form.submit();
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
