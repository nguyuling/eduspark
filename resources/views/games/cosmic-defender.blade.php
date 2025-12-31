@extends('layouts.app')

@section('content')
<div class="app">
    <main class="main">
        <div id="gameContainer" style="padding: 20px;">
            <!-- Game Header -->
            <div id="gameHeader" class="header">
                <div>
                    <div class="title">Cosmic Defender</div>
                </div>
                <div style="display: flex; gap: 60px; align-items: center;">
                    <div style="display: flex; gap: 20px; align-items: center;">
                        <div style="text-align: center;">
                            <div style="color: var(--muted); font-size: 12px; font-weight: 600; margin-bottom: 8px;">Skor</div>
                            <div id="scoreDisplay" style="font-size: 32px; font-weight: 700; color: var(--accent);">0</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="color: var(--muted); font-size: 12px; font-weight: 600; margin-bottom: 8px;">Nyawa</div>
                            <div id="livesDisplay" style="font-size: 32px; font-weight: 700; color: #ef4444;">3</div>
                        </div>
                    </div>
                    <a href="{{ route('games.index') }}" class="btn-kembali">
                        <i class="bi bi-arrow-left"></i>Kembali
                    </a>
                </div>
            </div>

            <!-- Game Canvas -->
            <canvas id="gameCanvas" style="display: none; flex: 1;"></canvas>

            <!-- Start Screen -->
            <div id="startScreen" style="text-align: center; padding: 80px 40px;">
                <div style="font-size: 80px; margin-bottom: 20px;">🚀</div>
                <h2 style="font-size: 36px; font-weight: 700; margin-bottom: 12px;">Cosmic Defender</h2>
                <p style="color: var(--muted); font-size: 16px; margin-bottom: 30px; max-width: 500px; margin-left: auto; margin-right: auto;">
                    Pertahankan planet anda dari musuh kosmik! Gunakan panah kiri/kanan untuk bergerak dan SPACE untuk menembak.
                </p>
                <button id="startBtn" style="padding: 14px 40px; background: linear-gradient(90deg, #1D5DCD, #E63946); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 700; cursor: pointer; transition: transform 0.2s ease;">
                    Mula Bermain ▶
                </button>
            </div>

            <!-- Game Over Screen -->
            <div id="gameOverScreen" style="display: none; text-align: center; padding: 80px 40px;">
                <div style="font-size: 80px; margin-bottom: 20px;">☠️</div>
                <h2 style="font-size: 32px; font-weight: 700; margin-bottom: 32px;">Permainan Tamat!</h2>
                <section class="panel" style="max-width: 450px; margin: 0 auto 40px;">
                    <div style="padding: 32px;">
                        <div style="margin-bottom: 15px;">
                            <div style="color: var(--muted); font-size: 12px; font-weight: 600; margin-bottom: 8px;\">Skor Akhir</div>
                            <div id="finalScore" style="font-size: 48px; font-weight: 700; color: var(--accent);\">0</div>
                        </div>
                    </div>
                </section>
                <div style="display: flex; gap: 12px; justify-content: center;\">
                    <button id="playAgainBtn" style="padding: 12px 30px; background: var(--accent); color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer;\">
                        Main Semula
                    </button>
                    <a href="/games" style="padding: 12px 30px; background: var(--border); color: var(--text); border: none; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; text-decoration: none; display: inline-block;\">
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    const canvas = document.getElementById('gameCanvas');
    const ctx = canvas.getContext('2d');

    let gameStarted = false;
    let gameActive = false;
    let score = 0;
    let lives = 3;
    let gameStartTime = 0; // Track when game starts

    // Player
    const player = {
        x: 0,
        y: 0,
        width: 40,
        height: 40,
        speed: 5,
        bullets: []
    };

    // Enemies
    let enemies = [];
    let explosions = [];

    // Input
    const keys = {};
    window.addEventListener('keydown', (e) => { keys[e.key] = true; });
    window.addEventListener('keyup', (e) => { keys[e.key] = false; });

    function resizeCanvas() {
        const gameContainer = document.getElementById('gameContainer');
        const header = document.getElementById('gameHeader');
        const canvas = document.getElementById('gameCanvas');
        
        // Get actual available height by calculating from gameContainer minus header
        const availableHeight = gameContainer.clientHeight - header.clientHeight;
        
        canvas.width = gameContainer.clientWidth;
        canvas.height = availableHeight;
        
        player.x = canvas.width / 2 - player.width / 2;
        player.y = canvas.height - 60;
    }

    function startGame() {
        gameStarted = true;
        gameActive = true;
        score = 0;
        lives = 3;
        enemies = [];
        player.bullets = [];
        gameStartTime = Date.now(); // Record start time

        document.getElementById('startScreen').style.display = 'none';
        document.getElementById('gameCanvas').style.display = 'block';
        document.getElementById('gameOverScreen').style.display = 'none';
        document.getElementById('gameHeader').style.display = 'flex';
        document.getElementById('scoreDisplay').textContent = '0';
        document.getElementById('livesDisplay').textContent = '3';

        resizeCanvas();
        gameLoop();
    }

    function gameLoop() {
        if (!gameActive) return;

        // Clear
        ctx.fillStyle = 'rgba(0, 20, 50, 0.1)';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // Update player
        if (keys['ArrowLeft'] || keys['a']) player.x = Math.max(0, player.x - player.speed);
        if (keys['ArrowRight'] || keys['d']) player.x = Math.min(canvas.width - player.width, player.x + player.speed);
        if (keys[' ']) {
            if (!player.lastShot || Date.now() - player.lastShot > 200) {
                player.bullets.push({
                    x: player.x + player.width / 2 - 5,
                    y: player.y,
                    width: 10,
                    height: 20,
                    speed: 7
                });
                player.lastShot = Date.now();
            }
        }

        // Update bullets
        player.bullets = player.bullets.filter(b => {
            b.y -= b.speed;
            ctx.fillStyle = '#22c55e';
            ctx.fillRect(b.x, b.y, b.width, b.height);
            return b.y > 0;
        });

        // Spawn enemies
        if (Math.random() > 0.98) {
            enemies.push({
                x: Math.random() * (canvas.width - 40),
                y: -40,
                width: 40,
                height: 40,
                speed: 2
            });
        }

        // Update enemies
        enemies = enemies.filter(e => {
            e.y += e.speed;

            // Draw enemy
            ctx.fillStyle = '#ef4444';
            ctx.font = '30px Arial';
            ctx.textAlign = 'center';
            ctx.fillText('👾', e.x + e.width / 2, e.y + e.height);

            // Check collision with bullets
            for (let i = player.bullets.length - 1; i >= 0; i--) {
                const b = player.bullets[i];
                if (b.x < e.x + e.width && b.x + b.width > e.x && b.y < e.y + e.height && b.y + b.height > e.y) {
                    player.bullets.splice(i, 1);
                    score += 10;
                    document.getElementById('scoreDisplay').textContent = score;
                    return false;
                }
            }

            // Check collision with player
            if (player.x < e.x + e.width && player.x + player.width > e.x && 
                player.y < e.y + e.height && player.y + player.height > e.y) {
                lives--;
                document.getElementById('livesDisplay').textContent = lives;
                if (lives <= 0) endGame();
                return false;
            }

            return e.y < canvas.height;
        });

        // Draw player
        ctx.font = '40px Arial';
        ctx.textAlign = 'center';
        ctx.fillText('🚀', player.x + player.width / 2, player.y + player.height);

        if (gameActive) requestAnimationFrame(gameLoop);
    }

    function endGame() {
        gameActive = false;
        
        // Calculate time taken (from game start to now)
        const gameEndTime = Date.now();
        const timeInSeconds = Math.floor((gameEndTime - gameStartTime) / 1000);
        
        // Submit score to server
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("games.storeResult", 1) }}'; // Game ID 1 for Cosmic Defender
        
        // CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        // Score
        const scoreInput = document.createElement('input');
        scoreInput.type = 'hidden';
        scoreInput.name = 'score';
        scoreInput.value = score;
        form.appendChild(scoreInput);
        
        // Time
        const timeInput = document.createElement('input');
        timeInput.type = 'hidden';
        timeInput.name = 'time_taken';
        timeInput.value = timeInSeconds;
        form.appendChild(timeInput);
        
        document.body.appendChild(form);
        form.submit();
    }

    document.getElementById('startBtn').addEventListener('click', startGame);
    document.getElementById('playAgainBtn').addEventListener('click', () => {
        document.getElementById('gameOverScreen').style.display = 'none';
        document.getElementById('startScreen').style.display = 'flex';
    });

    window.addEventListener('resize', resizeCanvas);

    // Initialize
    resizeCanvas();
</script>
@endsection
