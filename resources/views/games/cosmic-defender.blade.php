@extends('layouts.app')

@section('content')
<div class="app">
    <main class="main">
        <!-- Game Header -->
        <div id="gameHeader" class="header">
            <div>
                <div class="title">Cosmic Defender</div>
                <div class="sub">Pertahankan planet anda dari musuh kosmik! Gunakan panah kiri/kanan untuk bergerak dan SPACE untuk menembak.</div>
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

        <div id="gameContainer" style="padding: 20px;">

            <!-- Game Canvas -->
            <div id="gameCanvasWrapper" style="display: none; justify-content: center;">
                <section class="panel" style="width: 100%; max-width: 600px; padding: 20px;">
                    <canvas id="gameCanvas" style="max-width: 100%; height: auto; border-radius: 12px; background: white;"></canvas>
                </section>
            </div>

            <!-- Start Screen -->
            <div id="startScreen" style="text-align: center; padding: 80px 40px; min-height: 500px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                <section class="panel" style="width: 100%; max-width: 500px; padding: 40px;">
                    <div style="font-size: 80px; margin-bottom: 20px;">🚀</div>
                    <h2 style="font-size: 36px; font-weight: 700; margin-bottom: 12px;">Cosmic Defender</h2>
                    <p style="color: var(--muted); font-size: 16px; margin-bottom: 30px;">
                        Pertahankan planet anda dari musuh kosmik! Gunakan panah kiri/kanan untuk bergerak dan SPACE untuk menembak.
                    </p>
                    <button id="startBtn" style="padding: 14px 40px; background: linear-gradient(90deg, #A855F7, #9333EA); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 700; cursor: pointer; transition: transform 0.2s ease;">
                        Mula
                    </button>
                </section>
            </div>

            <!-- Game Over Screen -->
            <div id="gameOverScreen" style="display: none; text-align: center; padding: 80px 40px; min-height: 500px; flex-direction: column; align-items: center; justify-content: center;">
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
                    <button id="playAgainBtn" style="padding: 12px 30px; background: linear-gradient(90deg, #A855F7, #9333EA); color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer;">
                        Lihat Skor & Ganjaran
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
        const canvas = document.getElementById('gameCanvas');
        
        // Set canvas size with proper aspect ratio - larger for better visibility
        canvas.width = 600;
        canvas.height = 400;
        
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
        document.getElementById('gameCanvasWrapper').style.display = 'flex';
        document.getElementById('gameOverScreen').style.display = 'none';
        document.getElementById('gameHeader').style.display = 'flex';
        document.getElementById('scoreDisplay').textContent = '0';
        document.getElementById('livesDisplay').textContent = '3';

        resizeCanvas();
        gameLoop();
    }

    function gameLoop() {
        if (!gameActive) return;

        // Clear with white background
        ctx.fillStyle = 'white';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        
        // Add subtle grid pattern
        ctx.strokeStyle = 'rgba(200, 200, 200, 0.1)';
        ctx.lineWidth = 1;
        for (let i = 0; i < canvas.width; i += 40) {
            ctx.beginPath();
            ctx.moveTo(i, 0);
            ctx.lineTo(i, canvas.height);
            ctx.stroke();
        }
        for (let i = 0; i < canvas.height; i += 40) {
            ctx.beginPath();
            ctx.moveTo(0, i);
            ctx.lineTo(canvas.width, i);
            ctx.stroke();
        }

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
        
        // If wrapped in play mode, submit to game summary
        if (window.isPlayWrapperMode && window.submitGameScore) {
            // Hide all game elements
            const gameOverScreen = document.getElementById('gameOverScreen');
            const gameCanvasWrapper = document.getElementById('gameCanvasWrapper');
            const gameHeader = document.getElementById('gameHeader');
            
            if (gameOverScreen) gameOverScreen.style.display = 'none';
            if (gameCanvasWrapper) gameCanvasWrapper.style.display = 'none';
            if (gameHeader) gameHeader.style.display = 'none';
            
            window.submitGameScore(score, timeInSeconds);
            return;
        }
        
        // Standalone mode - show game over screen
        document.getElementById('gameOverScreen').style.display = 'flex';
        document.getElementById('gameCanvasWrapper').style.display = 'none';
        document.getElementById('finalScore').textContent = score;
    }

    document.getElementById('startBtn').addEventListener('click', startGame);
    document.getElementById('playAgainBtn').addEventListener('click', () => {
        const gameEndTime = Date.now();
        const timeInSeconds = Math.floor((gameEndTime - gameStartTime) / 1000);
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ isset($game) ? route("games.storeResult", $game->id) : route("games.storeResult", 1) }}';
        
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
    });

    window.addEventListener('resize', resizeCanvas);

    // Initialize
    resizeCanvas();
</script>
@endsection
