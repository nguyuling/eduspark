@extends('layouts.app')

@section('content')
<style>
    #mazeCanvas {
        display: block;
        margin: 0 auto;
        background: linear-gradient(135deg, #86efac, #6ee7a0);
        border: 4px solid #15803d;
        border-radius: 12px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    }
</style>

<div class="app">
    <main class="main">
        <!-- Game Header -->
        <div id="gameHeader" class="header">
            <div>
                <div class="title">Maze Quest</div>
                <div class="sub">Navigasi taman sambil menjawab soalan Java!</div>
            </div>
            <div style="display: flex; gap: 60px; align-items: center;">
                <div style="display: flex; gap: 20px; align-items: center;">
                    <div style="text-align: center;">
                        <div style="color: var(--muted); font-size: 12px; font-weight: 600; margin-bottom: 8px;">Skor Betul</div>
                        <div id="correctDisplay" style="font-size: 32px; font-weight: 700; color: #10b981;">0</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="color: var(--muted); font-size: 12px; font-weight: 600; margin-bottom: 8px;">Markah</div>
                        <div id="scoreDisplay" style="font-size: 32px; font-weight: 700; color: #f59e0b;">0</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="color: var(--muted); font-size: 12px; font-weight: 600; margin-bottom: 8px;">Masa</div>
                        <div id="timerDisplay" style="font-size: 32px; font-weight: 700; color: #ef4444;">0</div>
                    </div>
                </div>
                <a href="{{ route('games.index') }}" class="btn-kembali">
                    <i class="bi bi-arrow-left"></i>Kembali
                </a>
            </div>
        </div>

        <div id="gameContainer" style="padding: 20px;">

            <!-- Game Area -->
            <div id="gameContent" style="display: none;">
                <div style="max-width: 950px; margin: 0 auto;">
                    <canvas id="mazeCanvas" width="900" height="900"></canvas>
                    <div style="margin-top: 20px; text-align: center; background: #f3f4f6; padding: 20px; border-radius: 12px; border: 3px solid #d1d5db;">
                        <p style="font-size: 18px; font-weight: 700; color: #1f2937; margin: 0;">
                            🎮 Gunakan <span style="background: #3b82f6; color: white; padding: 4px 12px; border-radius: 6px; font-weight: 900;">↑ ↓ ← →</span> atau 
                            <span style="background: #10b981; color: white; padding: 4px 12px; border-radius: 6px; font-weight: 900;">W A S D</span> untuk bergerak
                        </p>
                        <p style="font-size: 16px; color: #6b7280; margin: 10px 0 0 0;">
                            🌺 Kumpul semua bunga untuk jawab soalan Java! Target: <span id="flowerTarget" style="font-weight: 900; color: #ec4899;">0/15</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Start Screen -->
            <div id="startScreen" style="text-align: center; padding: 80px 40px; min-height: 500px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                <section class="panel" style="width: 100%; max-width: 500px; padding: 40px;">
                    <div style="font-size: 80px; margin-bottom: 20px;">🌳</div>
                    <h2 style="font-size: 36px; font-weight: 700; margin-bottom: 12px;">Maze Quest</h2>
                    <p style="color: var(--muted); font-size: 16px; margin-bottom: 30px;">
                        Kumpulkan 15 bunga di taman labirin sambil menjawab soalan Java. Setiap bunga yang dikumpul memberikan satu soalan dan 100 mata untuk jawapan yang betul.
                    </p>
                    <button id="startBtn" style="padding: 16px 40px; background: linear-gradient(90deg, #A855F7, #9333EA); color: white; border: none; border-radius: 12px; font-size: 18px; font-weight: 700; cursor: pointer; box-shadow: 0 8px 16px rgba(0,0,0,0.2); transition: transform 0.2s; position: relative; z-index: 1;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                        Mula
                    </button>
                </section>
            </div>

            <!-- Game Over Screen -->
            <div id="gameOverScreen" style="display: none; text-align: center; padding: 80px 40px; min-height: 500px; flex-direction: column; align-items: center; justify-content: center;">
                <div>
                    <div style="font-size: 100px; margin-bottom: 30px;">🎉</div>
                    <h2 style="font-size: 48px; font-weight: 900; margin-bottom: 20px; color: #1f2937;">Tahniah!</h2>
                    <div style="background: #dbeafe; border: 4px solid #3b82f6; border-radius: 16px; padding: 40px; max-width: 500px; margin: 0 auto 40px auto;">
                        <div style="margin-bottom: 30px;">
                            <div style="color: var(--muted); font-size: 12px; font-weight: 600; margin-bottom: 8px;">Skor Akhir</div>
                            <div id="finalScore" style="font-size: 48px; font-weight: 700; color: #f59e0b;">0</div>
                        </div>
                        <div>
                            <div style="color: var(--muted); font-size: 12px; font-weight: 600; margin-bottom: 8px;">Soalan Dijawab dengan Betul</div>
                            <div id="finalCorrect" style="font-size: 28px; font-weight: 700; color: #10b981;">0/15</div>
                        </div>
                    </div>
                    <button id="mulaBeramainBtn" style="padding: 20px 60px; background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; border-radius: 12px; font-size: 20px; font-weight: 900; cursor: pointer; box-shadow: 0 8px 16px rgba(0,0,0,0.2); transition: transform 0.2s;">
                        � Lihat Skor & Ganjaran
                    </button>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Question Modal -->
<div id="questionModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 24px; padding: 48px; max-width: 700px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,0.5); border: 6px solid #3b82f6;">
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="font-size: 80px; margin-bottom: 15px;">🌺</div>
            <h2 style="font-size: 32px; font-weight: 900; color: #1f2937; margin-bottom: 10px;">Soalan Java</h2>
            <p style="font-size: 18px; color: #6b7280; font-weight: 700;">Bunga <span id="questionNumber">1</span> daripada 15</p>
        </div>
        
        <div style="background: #f3f4f6; border-radius: 12px; padding: 24px; margin-bottom: 30px; border: 3px solid #d1d5db;">
            <p id="questionText" style="font-size: 22px; font-weight: 700; color: #1f2937; line-height: 1.6; margin: 0;"></p>
        </div>
        
        <div id="answersContainer" style="display: grid; gap: 15px;">
            <!-- Answers will be inserted here -->
        </div>
    </div>
</div>

<!-- Result Modal -->
<div id="resultModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 24px; padding: 48px; max-width: 500px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,0.5); text-align: center; border: 6px solid #10b981;">
        <div id="resultIcon" style="font-size: 100px; margin-bottom: 20px;"></div>
        <h2 id="resultTitle" style="font-size: 36px; font-weight: 900; margin-bottom: 15px;"></h2>
        <p id="resultMessage" style="font-size: 20px; color: #6b7280; font-weight: 700; margin-bottom: 30px;"></p>
        <button id="continueBtn" style="padding: 16px 48px; background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; border: none; border-radius: 12px; font-size: 20px; font-weight: 900; cursor: pointer; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
            Teruskan ➡️
        </button>
    </div>
</div>

<script>
    const canvas = document.getElementById('mazeCanvas');
    const ctx = canvas.getContext('2d');

    let score = 0;
    let correctAnswers = 0;
    let gameStarted = false;
    let gameActive = false;
    let gameStartTime = 0;
    let currentTime = 0;
    let timerInterval;
    let flowersCollected = 0;

    const maze = {
        cellSize: 45,
        cols: 15,
        rows: 15,
        grid: [],
        player: { x: 1, y: 1 },
        flowers: [],
        startTime: Date.now(),

        initialize: function() {
            // Create grid filled with walls
            for (let y = 0; y < this.rows; y++) {
                this.grid[y] = [];
                for (let x = 0; x < this.cols; x++) {
                    this.grid[y][x] = 1; // 1 = wall
                }
            }
            
            // Create maze using recursive backtracking
            this.createPaths(1, 1);
            
            // Create multiple paths
            this.createMultiplePaths();
            
            // Ensure start is clear
            this.grid[this.player.y][this.player.x] = 0;
            
            // Place 15 flowers on path cells
            this.placeFlowers();
        },

        createPaths: function(x, y) {
            const directions = [
                [0, -2], // up
                [2, 0],  // right
                [0, 2],  // down
                [-2, 0]  // left
            ];
            
            // Shuffle directions
            directions.sort(() => Math.random() - 0.5);
            
            for (let [dx, dy] of directions) {
                const newX = x + dx;
                const newY = y + dy;
                
                if (newX > 0 && newX < this.cols - 1 && 
                    newY > 0 && newY < this.rows - 1 && 
                    this.grid[newY][newX] === 1) {
                    
                    // Carve path
                    this.grid[newY][newX] = 0;
                    this.grid[y + dy/2][x + dx/2] = 0;
                    
                    // Recursively continue
                    this.createPaths(newX, newY);
                }
            }
        },

        createMultiplePaths: function() {
            // Open additional walls to create multiple routes
            const additionalPaths = [
                [2, 2], [4, 2], [6, 2], [8, 2], [10, 2], [12, 2],
                [2, 4], [4, 4], [6, 4], [8, 4], [10, 4], [12, 4],
                [2, 6], [4, 6], [6, 6], [8, 6], [10, 6], [12, 6],
                [2, 8], [4, 8], [6, 8], [8, 8], [10, 8], [12, 8],
                [2, 10], [4, 10], [6, 10], [8, 10], [10, 10], [12, 10],
                [2, 12], [4, 12], [6, 12], [8, 12], [10, 12], [12, 12]
            ];
            
            additionalPaths.forEach(([x, y]) => {
                if (x > 0 && x < this.cols && y > 0 && y < this.rows) {
                    this.grid[y][x] = 0;
                }
            });
        },

        placeFlowers: function() {
            this.flowers = [];
            let placed = 0;
            
            for (let y = 1; y < this.rows - 1 && placed < 15; y++) {
                for (let x = 1; x < this.cols - 1 && placed < 15; x++) {
                    if (this.grid[y][x] === 0 && Math.random() < 0.15) {
                        this.flowers.push({ x: x, y: y, collected: false });
                        placed++;
                    }
                }
            }
        },

        draw: function() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            // Draw cells
            for (let y = 0; y < this.rows; y++) {
                for (let x = 0; x < this.cols; x++) {
                    const px = x * this.cellSize;
                    const py = y * this.cellSize;
                    
                    if (this.grid[y][x] === 1) {
                        // Wall (hedge)
                        ctx.fillStyle = '#166534';
                        ctx.fillRect(px, py, this.cellSize, this.cellSize);
                        ctx.strokeStyle = '#14532d';
                        ctx.lineWidth = 1;
                        ctx.strokeRect(px, py, this.cellSize, this.cellSize);
                    } else {
                        // Path
                        ctx.fillStyle = '#f0fdf4';
                        ctx.fillRect(px, py, this.cellSize, this.cellSize);
                        ctx.strokeStyle = '#d1d5db';
                        ctx.lineWidth = 0.5;
                        ctx.strokeRect(px, py, this.cellSize, this.cellSize);
                    }
                }
            }
            
            // Draw flowers
            this.flowers.forEach(flower => {
                if (!flower.collected) {
                    const px = flower.x * this.cellSize + this.cellSize / 2;
                    const py = flower.y * this.cellSize + this.cellSize / 2;
                    
                    // Flower petals
                    ctx.fillStyle = '#ec4899';
                    for (let i = 0; i < 5; i++) {
                        const angle = (i * 2 * Math.PI) / 5;
                        const petalX = px + Math.cos(angle) * 10;
                        const petalY = py + Math.sin(angle) * 10;
                        ctx.beginPath();
                        ctx.arc(petalX, petalY, 6, 0, Math.PI * 2);
                        ctx.fill();
                    }
                    
                    // Flower center
                    ctx.fillStyle = '#fbbf24';
                    ctx.beginPath();
                    ctx.arc(px, py, 5, 0, Math.PI * 2);
                    ctx.fill();
                }
            });
            
            // Draw player (butterfly)
            const px = this.player.x * this.cellSize + this.cellSize / 2;
            const py = this.player.y * this.cellSize + this.cellSize / 2;
            
            ctx.fillStyle = '#3b82f6';
            ctx.beginPath();
            ctx.arc(px - 4, py, 6, 0, Math.PI * 2);
            ctx.fill();
            ctx.beginPath();
            ctx.arc(px + 4, py, 6, 0, Math.PI * 2);
            ctx.fill();
            
            ctx.fillStyle = '#1e40af';
            ctx.fillRect(px - 2, py - 6, 4, 12);
        },

        move: function(dx, dy) {
            const newX = this.player.x + dx;
            const newY = this.player.y + dy;
            
            if (newX >= 0 && newX < this.cols && 
                newY >= 0 && newY < this.rows && 
                this.grid[newY][newX] === 0) {
                
                this.player.x = newX;
                this.player.y = newY;
                
                // Check for flower collection
                this.checkFlowerCollection();
                
                return true;
            }
            return false;
        },

        checkFlowerCollection: function() {
            this.flowers.forEach((flower, index) => {
                if (!flower.collected && flower.x === this.player.x && flower.y === this.player.y) {
                    flower.collected = true;
                    flowersCollected++;
                    gameActive = false;
                    showQuestion(index);
                    updateFlowerCount();
                }
            });
        }
    };

    const javaQuestions = [
        { question: "Apakah keyword yang digunakan untuk mencipta kelas dalam Java?", answers: ["class", "Class", "object", "public"], correct: 0 },
        { question: "Apakah output dari: System.out.println(5 + 3 + \"2\");", answers: ["82", "10", "532", "Error"], correct: 0 },
        { question: "Apakah kaedah utama (main method) yang betul dalam Java?", answers: ["public static void main(String[] args)", "static public void main(String args)", "public void main(String[] args)", "void main(String args[])"], correct: 0 },
        { question: "Apakah perbezaan antara '==' dan 'equals()' dalam Java?", answers: ["== membanding rujukan, equals() membanding nilai", "Sama sahaja", "== lebih pantas", "equals() untuk integer sahaja"], correct: 0 },
        { question: "Apakah nilai default untuk boolean dalam Java?", answers: ["false", "true", "0", "null"], correct: 0 },
        { question: "Apakah keyword untuk mewarisi kelas dalam Java?", answers: ["extends", "implements", "inherits", "super"], correct: 0 },
        { question: "Apakah output dari: int x = 10; System.out.println(++x);", answers: ["11", "10", "9", "Error"], correct: 0 },
        { question: "Apakah jenis data yang sesuai untuk menyimpan nombor perpuluhan dalam Java?", answers: ["double", "int", "float", "decimal"], correct: 0 },
        { question: "Apakah maksud 'static' dalam Java?", answers: ["Boleh diakses tanpa membuat objek", "Tidak boleh diubah", "Private sahaja", "Final value"], correct: 0 },
        { question: "Apakah keyword untuk membuat interface dalam Java?", answers: ["interface", "abstract", "class", "implements"], correct: 0 },
        { question: "Apakah modifier yang betul untuk variable yang tidak boleh diubah?", answers: ["final", "const", "static", "private"], correct: 0 },
        { question: "Apakah output dari: System.out.println(10 / 3);", answers: ["3", "3.33", "3.3333", "Error"], correct: 0 },
        { question: "Apakah perbezaan antara ArrayList dan LinkedList?", answers: ["ArrayList guna array, LinkedList guna nodes", "Sama sahaja", "ArrayList lebih lambat", "LinkedList tidak boleh add()"], correct: 0 },
        { question: "Apakah keyword untuk menangkap exception dalam Java?", answers: ["try-catch", "throw-catch", "error-handle", "exception-catch"], correct: 0 },
        { question: "Apakah maksud polymorphism dalam Java?", answers: ["Satu objek boleh ambil banyak bentuk", "Banyak kelas dalam satu file", "Satu method sahaja", "Tiada inheritance"], correct: 0 }
    ];

    window.addEventListener('keydown', (e) => {
        if (!gameActive) return;
        
        let moved = false;
        switch(e.key.toLowerCase()) {
            case 'arrowup':
            case 'w':
                moved = maze.move(0, -1);
                break;
            case 'arrowdown':
            case 's':
                moved = maze.move(0, 1);
                break;
            case 'arrowleft':
            case 'a':
                moved = maze.move(-1, 0);
                break;
            case 'arrowright':
            case 'd':
                moved = maze.move(1, 0);
                break;
        }
        
        if (moved) {
            maze.draw();
        }
    });

    function showQuestion(flowerIndex) {
        const questionIndex = flowerIndex % javaQuestions.length;
        const question = javaQuestions[questionIndex];
        
        document.getElementById('questionNumber').textContent = flowersCollected;
        document.getElementById('questionText').textContent = question.question;
        
        const container = document.getElementById('answersContainer');
        container.innerHTML = '';
        
        question.answers.forEach((answer, index) => {
            const btn = document.createElement('button');
            btn.textContent = answer;
            btn.style.cssText = `padding: 20px; background: linear-gradient(135deg, #f3f4f6, #e5e7eb); border: 4px solid #d1d5db; border-radius: 12px; font-size: 18px; font-weight: 700; cursor: pointer; color: #1f2937; transition: all 0.2s;`;
            btn.onmouseover = () => {
                btn.style.background = 'linear-gradient(135deg, #dbeafe, #bfdbfe)';
                btn.style.borderColor = '#3b82f6';
            };
            btn.onmouseout = () => {
                btn.style.background = 'linear-gradient(135deg, #f3f4f6, #e5e7eb)';
                btn.style.borderColor = '#d1d5db';
            };
            btn.onclick = () => checkAnswer(index, question.correct);
            container.appendChild(btn);
        });
        
        document.getElementById('questionModal').style.display = 'flex';
    }

    function checkAnswer(selected, correct) {
        document.getElementById('questionModal').style.display = 'none';
        
        const isCorrect = selected === correct;
        
        if (isCorrect) {
            correctAnswers++;
            score += 100;
            document.getElementById('resultIcon').textContent = '✅';
            document.getElementById('resultTitle').textContent = 'Betul!';
            document.getElementById('resultTitle').style.color = '#10b981';
            document.getElementById('resultMessage').textContent = 'Tahniah! Anda dapat +100 mata!';
        } else {
            document.getElementById('resultIcon').textContent = '❌';
            document.getElementById('resultTitle').textContent = 'Salah!';
            document.getElementById('resultTitle').style.color = '#ef4444';
            document.getElementById('resultMessage').textContent = 'Cuba lagi pada bunga seterusnya!';
        }
        
        document.getElementById('correctDisplay').textContent = correctAnswers;
        document.getElementById('scoreDisplay').textContent = score;
        document.getElementById('resultModal').style.display = 'flex';
    }

    document.getElementById('continueBtn').addEventListener('click', () => {
        document.getElementById('resultModal').style.display = 'none';
        
        if (flowersCollected >= 15) {
            endGame();
        } else {
            gameActive = true;
            maze.draw();
        }
    });

    function endGame() {
        clearInterval(timerInterval);
        gameActive = false;
        
        const timeInSeconds = currentTime; // Total time taken
        
        console.log('endGame called, isPlayWrapperMode:', window.isPlayWrapperMode);
        
        // If wrapped in play mode, submit to game summary
        if (window.isPlayWrapperMode && window.submitGameScore) {
            console.log('Calling submitGameScore with:', score, timeInSeconds);
            // Hide all game elements
            const gameContent = document.getElementById('gameContent');
            const gameOverScreen = document.getElementById('gameOverScreen');
            const gameHeader = document.getElementById('gameHeader');
            
            if (gameContent) gameContent.style.display = 'none';
            if (gameOverScreen) gameOverScreen.style.display = 'none';
            if (gameHeader) gameHeader.style.display = 'none';
            
            window.submitGameScore(score, timeInSeconds);
            return;
        }
        
        console.log('Showing game over screen (standalone mode)');
        // Standalone mode - show game over screen
        document.getElementById('gameContent').style.display = 'none';
        document.getElementById('gameOverScreen').style.display = 'flex';
        document.getElementById('finalScore').textContent = score;
        document.getElementById('finalCorrect').textContent = `${correctAnswers}/15`;
    }
    
    function submitGameResult() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ isset($game) ? route("games.storeResult", $game->id) : route("games.storeResult", 4) }}';
        
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
        timeInput.value = currentTime;
        form.appendChild(timeInput);
        
        document.body.appendChild(form);
        form.submit();
    }

    function updateFlowerCount() {
        document.getElementById('flowerTarget').textContent = `${flowersCollected}/15`;
    }

    function startTimer() {
        timerInterval = setInterval(() => {
            currentTime++;
            document.getElementById('timerDisplay').textContent = currentTime;
        }, 1000);
    }

    function startGame() {
        try {
            console.log('Starting game...');
            gameStarted = true;
            gameActive = true;
            score = 0;
            correctAnswers = 0;
            flowersCollected = 0;
            currentTime = 0;
            gameStartTime = Date.now();

            console.log('Hiding start screen...');
            document.getElementById('startScreen').style.display = 'none';
            document.getElementById('gameContent').style.display = 'block';
            document.getElementById('scoreDisplay').textContent = '0';
            document.getElementById('correctDisplay').textContent = '0';
            document.getElementById('timerDisplay').textContent = '0';
            
            console.log('Initializing maze...');
            maze.initialize();
            console.log('Maze initialized, updating flower count...');
            updateFlowerCount();
            console.log('Starting timer...');
            startTimer();
            console.log('Drawing maze...');
            maze.draw();
            console.log('Game started successfully!');
        } catch (error) {
            console.error('Error starting game:', error);
            alert('Error starting game: ' + error.message);
        }
    }

    document.getElementById('startBtn').addEventListener('click', startGame);
    // Fallback handler - also add onclick attribute
    document.getElementById('startBtn').onclick = function(e) {
        e.preventDefault();
        e.stopPropagation();
        startGame();
        return false;
    };
    
    document.getElementById('mulaBeramainBtn').addEventListener('click', submitGameResult);
</script>
@endsection
