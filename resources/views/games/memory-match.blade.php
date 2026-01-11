@extends('layouts.app')

@section('content')
<div class="app">
    <main class="main">
        <!-- Game Header -->
        <div id="gameHeader" class="header">
            <div>
                <div class="title">Memory Match</div>
                <div class="sub">Padankan kad yang sama untuk menang!</div>
            </div>
            <div style="display: flex; gap: 60px; align-items: center;">
                <div style="text-align: center;">
                    <div style="color: var(--muted); font-size: 12px; font-weight: 600; margin-bottom: 8px;">Markah</div>
                    <div id="moveCount" style="font-size: 32px; font-weight: 700; color: var(--accent);">0</div>
                </div>
                <a href="{{ route('games.index') }}" class="btn-kembali">
                    <i class="bi bi-arrow-left"></i>Kembali
                </a>
            </div>
        </div>

        <div id="gameContainer" style="padding: 20px;">

            <!-- Game Area -->
            <div id="gameContent" style="display: none;">
                <div style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 40px; max-width: 600px; margin: 0 auto;">
                    <div id="gameBoard" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; max-width: 400px; margin: 0 auto;">
                        <!-- Cards will be inserted here -->
                    </div>
                </div>
            </div>

            <!-- Start Screen -->
            <div id="startScreen" style="text-align: center; padding: 80px 40px; min-height: 500px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                <section class="panel" style="width: 100%; max-width: 500px; padding: 40px;">
                    <div style="font-size: 80px; margin-bottom: 20px;">🎴</div>
                    <h2 style="font-size: 36px; font-weight: 700; margin-bottom: 12px;">Memory Match</h2>
                    <p style="color: var(--muted); font-size: 16px; margin-bottom: 30px;">
                        Padankan semua pasangan kad dalam bilangan pergerakan yang paling sedikit! Pilih 2 kad pada satu masa untuk mendedahkannya.
                    </p>
                    <button id="startBtn" style="padding: 14px 40px; background: linear-gradient(90deg, #A855F7, #9333EA); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 700; cursor: pointer; transition: transform 0.2s ease;">
                        Mula
                    </button>
                </section>
            </div>

            <!-- Game Over Screen -->
            <div id="gameOverScreen" style="display: none; text-align: center; padding: 80px 40px; min-height: 500px; flex-direction: column; align-items: center; justify-content: center;">
                <div style="font-size: 80px; margin-bottom: 20px;">🎉</div>
                <h2 style="font-size: 32px; font-weight: 700; margin-bottom: 32px;">Anda Menang!</h2>
                <section class="panel" style="max-width: 450px; margin: 0 auto 40px;">
                    <div style="padding: 32px;">
                        <div>
                            <div style="color: var(--muted); font-size: 12px; font-weight: 600; margin-bottom: 8px;">Jumlah Pergerakan</div>
                            <div id="finalMoves" style="font-size: 48px; font-weight: 700; color: var(--accent);">0</div>
                        </div>
                    </div>
                </section>
                <div style="display: flex; gap: 12px; justify-content: center;">
                    <button id="playAgainBtn" style="padding: 12px 30px; background: var(--accent); color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer;">
                        Lihat Skor & Ganjaran
                    </button>
                </div>
            </div>
        </div>
    </main>
</div>

<style>
    .card {
        width: 100%;
        aspect-ratio: 1 / 1;
        background: linear-gradient(135deg, var(--accent), #9d4edd);
        border-radius: 8px;
        border: 2px solid var(--accent);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        cursor: pointer;
        transition: all 0.3s ease;
        user-select: none;
        position: relative;
    }

    .card:hover:not(.matched) {
        transform: scale(1.05);
    }

    .card.flipped {
        background: var(--card-bg);
        border-color: var(--border);
    }

    .card.matched {
        opacity: 0.5;
        cursor: default;
    }

    #startBtn:hover {
        transform: scale(1.05);
    }

    #startBtn:active {
        transform: scale(0.95);
    }
</style>

<script>
    // Card pairs (emojis)
    const cardPairs = ['🐭', '🐭', '🐰', '🐰', '🐻', '🐻', '🦊', '🦊', '🐼', '🐼', '🦁', '🦁', '🐸', '🐸', '🦕', '🦕'];

    // Game State
    let moves = 0;
    let gameStarted = false;
    let gameActive = false;
    let flippedCards = [];
    let matchedPairs = 0;
    let gameStartTime = 0;

    const elements = {
        gameContainer: document.getElementById('gameContainer'),
        gameHeader: document.getElementById('gameHeader'),
        gameContent: document.getElementById('gameContent'),
        startScreen: document.getElementById('startScreen'),
        gameOverScreen: document.getElementById('gameOverScreen'),
        moveCount: document.getElementById('moveCount'),
        gameBoard: document.getElementById('gameBoard'),
        startBtn: document.getElementById('startBtn'),
        playAgainBtn: document.getElementById('playAgainBtn'),
        finalMoves: document.getElementById('finalMoves')
    };

    // Event Listeners
    elements.startBtn.addEventListener('click', startGame);
    elements.playAgainBtn.addEventListener('click', () => {
        const gameEndTime = Date.now();
        const timeInSeconds = Math.floor((gameEndTime - gameStartTime) / 1000);
        
        const calculatedScore = Math.max(0, 1000 - (moves * 50));
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ isset($game) ? route("games.storeResult", $game->id) : route("games.storeResult", 3) }}';
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        const scoreInput = document.createElement('input');
        scoreInput.type = 'hidden';
        scoreInput.name = 'score';
        scoreInput.value = calculatedScore;
        form.appendChild(scoreInput);
        
        const timeInput = document.createElement('input');
        timeInput.type = 'hidden';
        timeInput.name = 'time_taken';
        timeInput.value = timeInSeconds;
        form.appendChild(timeInput);
        
        document.body.appendChild(form);
        form.submit();
    });

    function startGame() {
        gameStarted = true;
        gameActive = true;
        moves = 0;
        matchedPairs = 0;
        flippedCards = [];
        gameStartTime = Date.now();

        elements.startScreen.style.display = 'none';
        elements.gameContent.style.display = 'block';
        elements.gameOverScreen.style.display = 'none';
        elements.gameHeader.style.display = 'flex';

        elements.moveCount.textContent = '0';

        createBoard();
    }

    function createBoard() {
        elements.gameBoard.innerHTML = '';
        const shuffled = shuffleArray([...cardPairs]);

        shuffled.forEach((card, index) => {
            const cardEl = document.createElement('div');
            cardEl.className = 'card';
            cardEl.textContent = '?';
            cardEl.dataset.value = card;
            cardEl.dataset.index = index;
            cardEl.addEventListener('click', () => flipCard(cardEl));
            elements.gameBoard.appendChild(cardEl);
        });
    }

    function flipCard(cardEl) {
        if (!gameActive || cardEl.classList.contains('flipped') || cardEl.classList.contains('matched')) return;
        if (flippedCards.length >= 2) return;

        cardEl.classList.add('flipped');
        cardEl.textContent = cardEl.dataset.value;
        flippedCards.push(cardEl);

        if (flippedCards.length === 2) {
            moves++;
            elements.moveCount.textContent = moves;
            checkMatch();
        }
    }

    function checkMatch() {
        gameActive = false;
        const [card1, card2] = flippedCards;

        setTimeout(() => {
            if (card1.dataset.value === card2.dataset.value) {
                card1.classList.add('matched');
                card2.classList.add('matched');
                matchedPairs++;

                if (matchedPairs === cardPairs.length / 2) {
                    endGame();
                } else {
                    flippedCards = [];
                    gameActive = true;
                }
            } else {
                card1.classList.remove('flipped');
                card2.classList.remove('flipped');
                card1.textContent = '?';
                card2.textContent = '?';
                flippedCards = [];
                gameActive = true;
            }
        }, 1000);
    }

    function endGame() {
        gameStarted = false;
        gameActive = false;
        
        const gameEndTime = Date.now();
        const timeInSeconds = Math.floor((gameEndTime - gameStartTime) / 1000);
        
        // Calculate score based on moves (fewer moves = higher score)
        const calculatedScore = Math.max(0, 1000 - (moves * 50));
        
        // If wrapped in play mode, submit to game summary
        if (window.isPlayWrapperMode && window.submitGameScore) {
            // Hide all game elements
            const gameContent = document.getElementById('gameContent');
            const gameOverScreen = document.getElementById('gameOverScreen');
            const gameHeader = document.getElementById('gameHeader');
            
            if (gameContent) gameContent.style.display = 'none';
            if (gameOverScreen) gameOverScreen.style.display = 'none';
            if (gameHeader) gameHeader.style.display = 'none';
            
            window.submitGameScore(calculatedScore, timeInSeconds);
            return;
        }
        
        // Standalone mode - show game over screen
        document.getElementById('gameOverScreen').style.display = 'flex';
        document.getElementById('gameContent').style.display = 'none';
        document.getElementById('finalMoves').textContent = moves;
    }

    function resetGame() {
        elements.gameOverScreen.style.display = 'none';
        elements.startScreen.style.display = 'block';
    }

    function shuffleArray(array) {
        for (let i = array.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [array[i], array[j]] = [array[j], array[i]];
        }
        return array;
    }
</script>
@endsection
