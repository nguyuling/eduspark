@extends('layouts.app')

@section('content')
<div class="app">
    <main class="main">
        <div id="gameContainer" style="padding: 20px;">
            <!-- Game Header -->
            <div id="gameHeader" class="header">
                <div>
                    <div class="title">Memory Match</div>
                    <div class="sub">Padankan kad yang sama untuk menang!</div>
                </div>
                <div style="display: flex; gap: 40px; align-items: center;">
                    <div style="text-align: center;">
                        <div style="color: var(--muted); font-size: 12px; font-weight: 600; text-transform: uppercase;">Pergerakan</div>
                        <div id="moveCount" style="font-size: 32px; font-weight: 700; color: var(--accent);">0</div>
                    </div>
                    <a href="{{ route('games.index') }}" class="btn-kembali">
                        <i class="bi bi-arrow-left"></i>Kembali
                    </a>
                </div>
            </div>

            <!-- Game Area -->
            <div id="gameContent" style="display: none;">
                <div style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 40px; max-width: 600px; margin: 0 auto;">
                    <div id="gameBoard" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; max-width: 400px; margin: 0 auto;">
                        <!-- Cards will be inserted here -->
                    </div>
                </div>
            </div>

            <!-- Start Screen -->
            <div id="startScreen" style="text-align: center; padding: 60px 20px;">
                <div style="font-size: 64px; margin-bottom: 20px;">🎴</div>
                <h2 style="font-size: 32px; font-weight: 700; margin-bottom: 12px;">Memory Match</h2>
                <p style="color: var(--muted); font-size: 16px; margin-bottom: 30px; max-width: 500px; margin-left: auto; margin-right: auto;">
                    Padankan semua pasangan kad dalam bilangan pergerakan yang paling sedikit! Pilih 2 kad pada satu masa untuk mendedahkannya.
                </p>
                <button id="startBtn" style="padding: 14px 40px; background: linear-gradient(90deg, var(--accent), #9d4edd); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 700; cursor: pointer; transition: transform 0.2s ease;">
                    Mula Bermain ▶
                </button>
            </div>

            <!-- Game Over Screen -->
            <div id="gameOverScreen" style="display: none; text-align: center; padding: 60px 20px;">
                <div style="font-size: 64px; margin-bottom: 20px;">🎉</div>
                <h2 style="font-size: 32px; font-weight: 700; margin-bottom: 12px;">Anda Menang!</h2>
                <div style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 40px; max-width: 400px; margin: 20px auto; margin-bottom: 30px;">
                    <div style="margin-bottom: 20px;">
                        <div style="color: var(--muted); font-size: 12px; font-weight: 600; text-transform: uppercase; margin-bottom: 8px;">Jumlah Pergerakan</div>
                        <div id="finalMoves" style="font-size: 48px; font-weight: 700; color: var(--accent);">0</div>
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
    elements.playAgainBtn.addEventListener('click', resetGame);

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
        
        const calculatedScore = Math.max(0, 1000 - (moves * 50));
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("games.storeResult", 3) }}';
        
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
