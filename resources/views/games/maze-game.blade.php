@extends('layouts.app')

@section('content')
<div class="app">
    <main class="main">
        <div id="gameContainer" style="padding: 20px;">
            <!-- Game Header -->
            <div id="gameHeader" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <div>
                    <h1 style="margin: 0; font-size: 28px; font-weight: 700;">🗺️ Maze Quest</h1>
                    <p style="color: var(--muted); margin-top: 8px;">Navigasi labirin sambil menjawab soalan!</p>
                </div>
                <div style="display: flex; gap: 20px; align-items: center;">
                    <div style="text-align: center;">
                        <div style="color: var(--muted); font-size: 12px; font-weight: 600; text-transform: uppercase;">Aras</div>
                        <div id="levelDisplay" style="font-size: 32px; font-weight: 700; color: var(--accent);">1</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="color: var(--muted); font-size: 12px; font-weight: 600; text-transform: uppercase;">Skor</div>
                        <div id="scoreDisplay" style="font-size: 32px; font-weight: 700; color: #22c55e;">0</div>
                    </div>
                </div>
            </div>

            <!-- Game Area -->
            <div id="gameContent" style="display: none;">
                <div style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 20px; max-width: 600px; margin: 0 auto;">
                    <canvas id="mazeCanvas" width="500" height="500" style="border: 2px solid var(--border); border-radius: 8px; display: block; margin: 0 auto; background: white;"></canvas>
                    <p style="text-align: center; color: var(--muted); font-size: 14px; margin-top: 15px;">Gunakan panah atau WASD untuk bergerak. Capai EXIT untuk menang!</p>
                </div>
            </div>

            <!-- Start Screen -->
            <div id="startScreen" style="text-align: center; padding: 60px 20px;">
                <div style="font-size: 64px; margin-bottom: 20px;">🗺️</div>
                <h2 style="font-size: 32px; font-weight: 700; margin-bottom: 12px;">Maze Quest</h2>
                <p style="color: var(--muted); font-size: 16px; margin-bottom: 30px; max-width: 500px; margin-left: auto; margin-right: auto;">
                    Navigasi melalui labirin dan capai pintu keluar! Gunakan panah atau WASD untuk bergerak.
                </p>
                <button id="startBtn" style="padding: 14px 40px; background: linear-gradient(90deg, var(--accent), #9d4edd); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 700; cursor: pointer; transition: transform 0.2s ease;">
                    Mula Bermain ▶
                </button>
            </div>

            <!-- Game Over Screen -->
            <div id="gameOverScreen" style="display: none; text-align: center; padding: 60px 20px;">
                <div style="font-size: 64px; margin-bottom: 20px;">🎉</div>
                <h2 style="font-size: 32px; font-weight: 700; margin-bottom: 12px;">Anda Berjaya!</h2>
                <div style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 40px; max-width: 400px; margin: 20px auto; margin-bottom: 30px;">
                    <div style="margin-bottom: 20px;">
                        <div style="color: var(--muted); font-size: 12px; font-weight: 600; text-transform: uppercase; margin-bottom: 8px;">Skor</div>
                        <div id="finalScore" style="font-size: 48px; font-weight: 700; color: var(--accent);">0</div>
                    </div>
                    <div>
                        <div style="color: var(--muted); font-size: 12px; font-weight: 600; text-transform: uppercase; margin-bottom: 8px;">Aras Selesai</div>
                        <div id="finalLevel" style="font-size: 28px; font-weight: 700; color: #22c55e;">1</div>
                    </div>
                </div>
                <div style="display: flex; gap: 12px; justify-content: center;">
                    <button id="nextLevelBtn" style="padding: 12px 30px; background: var(--accent); color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer;">
                        Aras Seterusnya
                    </button>
                    <a href="/games" style="padding: 12px 30px; background: var(--border); color: var(--text); border: none; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; text-decoration: none; display: inline-block;">
                        Kembali ke Permainan
                    </a>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    const canvas = document.getElementById('mazeCanvas');
    const ctx = canvas.getContext('2d');

    let level = 1;
    let score = 0;
    let gameStarted = false;
    let gameActive = false;
    let gameStartTime = 0;

    const player = {
        x: 20,
        y: 20,
        size: 10
    };

    const exitPos = {
        x: 460,
        y: 460,
        size: 20
    };

    const keys = {};
    window.addEventListener('keydown', (e) => { keys[e.key] = true; });
    window.addEventListener('keyup', (e) => { keys[e.key] = false; });

    // Simple maze walls (coordinates)
    let walls = [];

    function generateMaze(difficulty) {
        walls = [];
        const gridSize = 40 + difficulty * 10;

        // Add perimeter
        for (let i = 0; i < 500; i += gridSize) {
            walls.push({ x: 0, y: i, w: 500, h: 10 });
            walls.push({ x: i, y: 0, w: 10, h: 500 });
        }

        // Add random internal walls based on difficulty
        for (let i = 0; i < 4 + difficulty * 2; i++) {
            const x = Math.random() * 400 + 50;
            const y = Math.random() * 400 + 50;
            const vertical = Math.random() > 0.5;
            walls.push({
                x: x,
                y: y,
                w: vertical ? 10 : 150,
                h: vertical ? 150 : 10
            });
        }
    }

    function drawMaze() {
        ctx.fillStyle = '#f0f0f0';
        ctx.fillRect(0, 0, 500, 500);

        // Draw walls
        ctx.fillStyle = '#333';
        walls.forEach(wall => {
            ctx.fillRect(wall.x, wall.y, wall.w, wall.h);
        });

        // Draw exit
        ctx.fillStyle = '#22c55e';
        ctx.font = '20px Arial';
        ctx.textAlign = 'center';
        ctx.fillText('EXIT', exitPos.x, exitPos.y + 6);

        // Draw player
        ctx.fillStyle = '#1D5DCD';
        ctx.beginPath();
        ctx.arc(player.x, player.y, player.size, 0, Math.PI * 2);
        ctx.fill();
    }

    function checkCollision(x, y) {
        for (let wall of walls) {
            if (x > wall.x && x < wall.x + wall.w && y > wall.y && y < wall.y + wall.h) {
                return true;
            }
        }
        return false;
    }

    function update() {
        const speed = 3;
        let newX = player.x;
        let newY = player.y;

        if (keys['ArrowLeft'] || keys['a']) newX -= speed;
        if (keys['ArrowRight'] || keys['d']) newX += speed;
        if (keys['ArrowUp'] || keys['w']) newY -= speed;
        if (keys['ArrowDown'] || keys['s']) newY += speed;

        // Check collision
        if (!checkCollision(newX, newY)) {
            player.x = newX;
            player.y = newY;
        }

        // Check win condition
        const dx = player.x - exitPos.x;
        const dy = player.y - exitPos.y;
        if (Math.sqrt(dx * dx + dy * dy) < 30) {
            winLevel();
        }
    }

    function gameLoop() {
        if (!gameActive) return;

        update();
        drawMaze();

        requestAnimationFrame(gameLoop);
    }

    function startGame() {
        gameStarted = true;
        gameActive = true;
        level = 1;
        score = 0;
        player.x = 20;
        player.y = 20;
        gameStartTime = Date.now();

        document.getElementById('startScreen').style.display = 'none';
        document.getElementById('gameContent').style.display = 'block';
        document.getElementById('gameOverScreen').style.display = 'none';
        document.getElementById('gameHeader').style.display = 'flex';
        document.getElementById('levelDisplay').textContent = '1';
        document.getElementById('scoreDisplay').textContent = '0';

        generateMaze(level);
        gameLoop();
    }

    function winLevel() {
        gameActive = false;
        score += (level * 100);
        
        const gameEndTime = Date.now();
        const timeInSeconds = Math.floor((gameEndTime - gameStartTime) / 1000);
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("games.storeResult", 4) }}';
        
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

    document.getElementById('startBtn').addEventListener('click', startGame);
    document.getElementById('nextLevelBtn').addEventListener('click', () => {
        level++;
        player.x = 20;
        player.y = 20;
        gameActive = true;

        document.getElementById('gameOverScreen').style.display = 'none';
        document.getElementById('gameContent').style.display = 'block';
        document.getElementById('gameHeader').style.display = 'flex';
        document.getElementById('levelDisplay').textContent = level;
        document.getElementById('scoreDisplay').textContent = score;

        generateMaze(level);
        gameLoop();
    });
</script>
@endsection
