@extends('layouts.app')

@section('content')
<div class="app">
    <main class="main" style="overflow: hidden; margin: 0; padding: 0;">
        <div id="gameContainer" style="padding: 0; width: 100%; min-height: calc(100vh - 70px); display: flex; flex-direction: column; background: #000;">
            <!-- Game Header -->
            <div id="gameHeader" style="display: flex; justify-content: space-between; align-items: center; padding: 20px; background: rgba(0,0,0,0.5); border-bottom: 1px solid rgba(255,255,255,0.1); z-index: 10; height: auto;">
                <div style="color: white;">
                    <h1 style="margin: 0; font-size: 24px; font-weight: 700;">🚀 Cosmic Defender</h1>
                </div>
                <div style="display: flex; gap: 20px; align-items: center;">
                    <div style="text-align: center; color: white;">
                        <div style="color: rgba(255,255,255,0.7); font-size: 11px; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Skor</div>
                        <div id="scoreDisplay" style="font-size: 24px; font-weight: 700; color: #22c55e;">0</div>
                    </div>
                    <div style="text-align: center; color: white;">
                        <div style="color: rgba(255,255,255,0.7); font-size: 11px; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Nyawa</div>
                        <div id="livesDisplay" style="font-size: 24px; font-weight: 700; color: #ef4444;">3</div>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div style="background: rgba(74, 222, 128, 0.1); border-bottom: 1px solid rgba(74, 222, 128, 0.3); padding: 12px 20px; text-align: center; color: #4ade80; font-size: 13px;">
                <strong>📖 How to Play:</strong> Use <strong>← →</strong> or <strong>A/D</strong> to move • Press <strong>SPACE</strong> to shoot • Destroy 👾 enemies • Don't let them hit you!
            </div>

            <!-- Game Canvas -->
            <canvas id="gameCanvas" style="flex: 1; display: none !important; width: 100%; background: linear-gradient(to bottom, #000428, #004e92); cursor: none;"></canvas>

            <!-- Start Screen -->
            <div id="startScreen" style="display: flex; align-items: center; justify-content: center; flex: 1; text-align: center; color: white;">
                <div style="max-width: 500px;">
                    <div style="font-size: 80px; margin-bottom: 20px;">🚀</div>
                    <h2 style="font-size: 36px; font-weight: 700; margin-bottom: 12px;">Cosmic Defender</h2>
                    <p style="color: rgba(255,255,255,0.8); font-size: 16px; margin-bottom: 30px;">
                        Pertahankan planet anda dari musuh kosmik! Gunakan panah kiri/kanan untuk bergerak dan SPACE untuk menembak.
                    </p>
                    <button id="startBtn" style="padding: 14px 40px; background: linear-gradient(90deg, #1D5DCD, #E63946); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 700; cursor: pointer; transition: transform 0.2s ease;">
                        Mula Bermain ▶
                    </button>
                </div>
            </div>

            <!-- Game Over Screen -->
            <div id="gameOverScreen" style="display: none; flex: 1; align-items: center; justify-content: center; text-align: center; color: white; background: rgba(0,0,0,0.9);">
                <div style="max-width: 400px;">
                    <div style="font-size: 64px; margin-bottom: 20px;">☠️</div>
                    <h2 style="font-size: 32px; font-weight: 700; margin-bottom: 20px;">Permainan Tamat!</h2>
                    <div style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 12px; padding: 30px; margin-bottom: 30px;">
                        <div style="margin-bottom: 15px;">
                            <div style="color: rgba(255,255,255,0.7); font-size: 12px; font-weight: 600; text-transform: uppercase; margin-bottom: 8px;">Skor Akhir</div>
                            <div id="finalScore" style="font-size: 40px; font-weight: 700; color: #22c55e;">0</div>
                        </div>
                    </div>
                    <div style="display: flex; gap: 12px; justify-content: center;">
                        <button id="playAgainBtn" style="padding: 12px 30px; background: #1D5DCD; color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer;">
                            Main Semula
                        </button>
                        <a href="/games" style="padding: 12px 30px; background: rgba(255,255,255,0.1); color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; text-decoration: none; display: inline-block;">
                            Kembali
                        </a>
                    </div>
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
        const headerHeight = header.offsetHeight;
        
        canvas.width = gameContainer.offsetWidth;
        canvas.height = gameContainer.offsetHeight - headerHeight;
        
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
        document.getElementById('gameCanvas').style.display = 'none';
        document.getElementById('gameHeader').style.display = 'none';
        document.getElementById('gameOverScreen').style.display = 'flex';
        document.getElementById('finalScore').textContent = score;
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
