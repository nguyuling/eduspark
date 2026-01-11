<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ringkasan Permainan</title>
    <style>
        .game-summary {
            text-align: center;
            padding: 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            color: white;
            max-width: 600px;
            margin: 50px auto;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            position: relative;
        }

        .score-circle {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: white;
            color: #333;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin: 0 auto 30px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            border: 8px solid rgba(255,255,255,0.2);
        }

        .score-circle .score {
            font-size: 64px;
            font-weight: bold;
            color: #4a5568;
        }

        .score-circle .score-label {
            font-size: 16px;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 5px;
        }

        .score-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin: 30px 0;
        }

        .detail-item {
            text-align: center;
            padding: 15px;
            background: rgba(255,255,255,0.1);
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }

        .detail-item .label {
            display: block;
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 8px;
        }

        .detail-item .value {
            display: block;
            font-size: 24px;
            font-weight: bold;
        }

        .rewards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }

        .reward-card {
            background: rgba(255,255,255,0.15);
            border-radius: 15px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.3s;
            border-left: 5px solid #FF9800;
        }

        .reward-card:hover {
            transform: translateY(-5px);
            background: rgba(255,255,255,0.2);
        }

        .reward-icon {
            font-size: 40px;
            min-width: 50px;
            text-align: center;
        }

        .reward-info {
            text-align: left;
            flex: 1;
        }

        .reward-info h4 {
            margin: 0 0 8px 0;
            font-size: 18px;
            font-weight: 600;
        }

        .reward-info p {
            margin: 0 0 10px 0;
            font-size: 14px;
            opacity: 0.9;
            line-height: 1.4;
        }

        .reward-xp {
            color: #FFD700;
            font-weight: bold;
            font-size: 16px;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 30px;
        }

        button {
            padding: 18px 30px;
            border: none;
            border-radius: 12px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        button:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        button:active {
            transform: translateY(-2px);
        }

        .btn-main-again {
            background: linear-gradient(to right, #4CAF50, #45a049);
            color: white;
        }

        .btn-leaderboard {
            background: linear-gradient(to right, #2196F3, #1976D2);
            color: white;
        }

        .btn-back {
            background: linear-gradient(to right, #9C27B0, #7B1FA2);
            color: white;
        }

        .btn-collect-rewards {
            background: linear-gradient(to right, #FF9800, #F57C00);
            color: white;
            width: 100%;
            margin-top: 20px;
            padding: 15px;
        }

        /* Loading animation */
        .loading {
            text-align: center;
            padding: 40px;
        }

        .loading-spinner {
            border: 4px solid rgba(255,255,255,0.3);
            border-top: 4px solid #4ecca3;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Rank badge for top 3 */
        .rank-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            color: #333;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: bold;
            font-size: 1.1rem;
            box-shadow: 0 5px 15px rgba(255, 215, 0, 0.4);
        }

        /* Leaderboard modal styles */
        .leaderboard-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            padding: 20px;
        }

        .leaderboard-modal .modal-content {
            background: #0f3460;
            padding: 30px;
            border-radius: 16px;
            border: 3px solid #4ecca3;
            max-width: 800px;
            width: 95%;
            max-height: 80vh;
            overflow-y: auto;
            color: white;
        }

        .leaderboard-modal table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: rgba(255,255,255,0.05);
            border-radius: 8px;
            overflow: hidden;
        }

        .leaderboard-modal th {
            padding: 15px;
            text-align: left;
            background: rgba(78, 204, 163, 0.2);
            border-bottom: 2px solid #4ecca3;
        }

        .leaderboard-modal td {
            padding: 15px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .leaderboard-modal tr.highlight {
            background: rgba(255, 215, 0, 0.15);
        }

        .leaderboard-modal .rank-1 { color: #FFD700; }
        .leaderboard-modal .rank-2 { color: #C0C0C0; }
        .leaderboard-modal .rank-3 { color: #CD7F32; }

        @media (max-width: 768px) {
            .game-summary {
                margin: 20px;
                padding: 20px;
            }
            
            .score-circle {
                width: 150px;
                height: 150px;
            }
            
            .score-circle .score {
                font-size: 48px;
            }
            
            .score-details {
                grid-template-columns: 1fr;
            }
            
            .rewards-grid {
                grid-template-columns: 1fr;
            }
            
            button {
                padding: 15px;
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="game-summary">
        <h2>🎮 Permainan Tamat!</h2>
        
        <!-- Loading state initially -->
        <div id="summary-content">
            <div class="loading">
                <div class="loading-spinner"></div>
                <p>Memuatkan ringkasan permainan...</p>
            </div>
        </div>
    </div>

    <script>
        // Get game ID from URL or data attribute
        // Option 1: If game ID is passed in Blade
        const gameId = {{ $game_id ?? 'null' }};
        
        // Option 2: Get from URL parameter (e.g., /game-summary?game_id=1)
        const urlParams = new URLSearchParams(window.location.search);
        const urlGameId = urlParams.get('game_id');
        
        // Use either method
        const finalGameId = gameId || urlGameId || getGameIdFromPath();
        
        // Option 3: Extract from URL path
        function getGameIdFromPath() {
            const path = window.location.pathname;
            const match = path.match(/\/games\/(\d+)/);
            return match ? match[1] : null;
        }
        
        // Load game summary when page loads
        document.addEventListener('DOMContentLoaded', function() {
            if (finalGameId) {
                loadGameSummary(finalGameId);
            } else {
                showError('ID permainan tidak ditemukan. Sila kembali ke halaman permainan.');
            }
        });
        
        // Load game summary from Laravel API
        async function loadGameSummary(gameId) {
            try {
                const response = await fetch(`/api/games/${gameId}/summary`, {
                    credentials: 'include' // Include session/cookies
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    displayGameSummary(data.summary);
                } else {
                    throw new Error(data.message || 'Gagal memuatkan ringkasan');
                }
            } catch (error) {
                console.error('Error loading game summary:', error);
                showError('Gagal memuatkan ringkasan permainan. Sila cuba lagi.');
                
                // Fallback: Show basic info if we have score from URL
                const urlScore = urlParams.get('score');
                if (urlScore) {
                    displayFallbackSummary(urlScore, gameId);
                }
            }
        }
        
        // Display the game summary
        function displayGameSummary(summary) {
            const html = `
                <div class="score-display">
                    <div class="score-circle">
                        <span class="score">${summary.score}</span>
                        <span class="score-label">MATA</span>
                    </div>
                    
                    <div class="score-details">
                        <div class="detail-item">
                            <span class="label">Kedudukan:</span>
                            <span class="value">#${summary.rank}</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Masa:</span>
                            <span class="value">${summary.time_taken}s</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Ketepatan:</span>
                            <span class="value">${summary.accuracy}%</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Pemain:</span>
                            <span class="value">${summary.total_players}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Rewards Section -->
                ${summary.rewards && summary.rewards.length > 0 ? `
                <div class="rewards-section">
                    <h3>🎁 Anugerah Diperolehi!</h3>
                    <div class="rewards-grid">
                        ${summary.rewards.map(reward => `
                            <div class="reward-card">
                                <div class="reward-icon">${reward.icon}</div>
                                <div class="reward-info">
                                    <h4>${reward.name}</h4>
                                    <p>${reward.description}</p>
                                    <span class="reward-xp">+${reward.xp} XP</span>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                    
                    <button class="btn-collect-rewards" onclick="collectRewards(${summary.game_id}, ${summary.score})">
                        Kumpul Semua Anugerah
                    </button>
                </div>
                ` : ''}
                
                <!-- Action Buttons -->
                <div class="action-buttons">
                    <button class="btn-main-again" onclick="restartGame(${summary.game_id})">
                        🔄 Main Semula
                    </button>
                    <button class="btn-leaderboard" onclick="showLeaderboard(${summary.game_id})">
                        🏆 Papan Pemimpin
                    </button>
                    <button class="btn-back" onclick="goBack()">
                        ← Kembali ke Menu
                    </button>
                </div>
            `;
            
            document.getElementById('summary-content').innerHTML = html;
            
            // Add rank badge if in top 3
            if (summary.rank <= 3) {
                const rankEmojis = ['🥇', '🥈', '🥉'];
                const badge = document.createElement('div');
                badge.className = 'rank-badge';
                badge.innerHTML = `${rankEmojis[summary.rank - 1]} Kedudukan #${summary.rank}`;
                document.querySelector('.game-summary').appendChild(badge);
            }
        }
        
        // Fallback summary if API fails
        function displayFallbackSummary(score, gameId) {
            const html = `
                <div class="score-display">
                    <div class="score-circle">
                        <span class="score">${score}</span>
                        <span class="score-label">MATA</span>
                    </div>
                    
                    <p>Terima kasih telah bermain!</p>
                    
                    <div class="action-buttons">
                        <button class="btn-main-again" onclick="restartGame(${gameId})">
                            🔄 Main Semula
                        </button>
                        <button class="btn-back" onclick="goBack()">
                            ← Kembali ke Menu
                        </button>
                    </div>
                </div>
            `;
            
            document.getElementById('summary-content').innerHTML = html;
        }
        
        // Show error message
        function showError(message) {
            document.getElementById('summary-content').innerHTML = `
                <div style="text-align: center; padding: 30px;">
                    <p style="color: #ff6b6b; font-size: 1.1rem;">${message}</p>
                    <button class="btn-back" onclick="goBack()" style="margin-top: 20px;">
                        ← Kembali ke Menu
                    </button>
                </div>
            `;
        }
        
        // Updated functions
        function restartGame(gameId) {
            // Redirect to game URL
            window.location.href = `/games/${gameId}`;
        }

        function showLeaderboard(gameId) {
            fetch(`/api/games/${gameId}/leaderboard`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayLeaderboardModal(data);
                    } else {
                        alert('Gagal memuatkan papan pemimpin.');
                    }
                })
                .catch(error => {
                    console.error('Error loading leaderboard:', error);
                    alert('Ralat ketika memuatkan papan pemimpin.');
                });
        }

        function goBack() {
            window.location.href = '/permainan';
        }

        function collectRewards(gameId, score) {
            fetch('/api/rewards/collect', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    game_id: gameId,
                    score: score
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Anugerah berjaya dikumpul!');
                    document.querySelector('.btn-collect-rewards').style.display = 'none';
                } else {
                    alert('Gagal mengumpul anugerah: ' + (data.message || 'Sila cuba lagi.'));
                }
            })
            .catch(error => {
                console.error('Error collecting rewards:', error);
                alert('Ralat ketika mengumpul anugerah.');
            });
        }

        function displayLeaderboardModal(leaderboardData) {
            const modalHTML = `
                <div class="leaderboard-modal">
                    <div class="modal-content">
                        <h2>🏆 Papan Pemimpin</h2>
                        
                        ${leaderboardData.user_rank && leaderboardData.user_rank > 10 ? `
                        <div style="background: #e3f2fd; padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center; color: #1976D2;">
                            <h3 style="margin: 0 0 5px 0;">Kedudukan Anda: #${leaderboardData.user_rank}</h3>
                            <p style="margin: 0; font-size: 0.9rem;">Skor: ${leaderboardData.user_score} mata | Masa: ${leaderboardData.user_time}s</p>
                        </div>
                        ` : ''}
                        
                        <table>
                            <thead>
                                <tr>
                                    <th>Kedudukan</th>
                                    <th>Nama</th>
                                    <th>Skor</th>
                                    <th>Masa</th>
                                    <th>Tarikh</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${leaderboardData.leaderboard.map((entry, index) => `
                                    <tr class="${entry.is_current_user ? 'highlight' : ''}">
                                        <td class="rank-${index + 1}">
                                            ${index < 3 ? ['🥇', '🥈', '🥉'][index] : ''} #${index + 1}
                                        </td>
                                        <td>${entry.user_name} ${entry.is_current_user ? ' (Anda)' : ''}</td>
                                        <td>${entry.score}</td>
                                        <td>${entry.time_taken}s</td>
                                        <td>${new Date(entry.created_at).toLocaleDateString('ms-MY')}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                        <div style="text-align: center; margin-top: 20px;">
                            <button onclick="closeModal()" style="padding: 12px 30px; background: #666; color: white; border: none; border-radius: 8px; cursor: pointer;">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', modalHTML);
        }

        function closeModal() {
            const modal = document.querySelector('.leaderboard-modal');
            if (modal) {
                modal.remove();
            }
        }
    </script>
</body>
</html>