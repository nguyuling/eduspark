<div class="game-summary">
    <h2>🎮 Permainan Tamat!</h2>
    
    <div class="score-display">
        <div class="score-circle">
            <span class="score">{{ $score }}</span>
            <span class="score-label">MATA</span>
        </div>
        
        <div class="score-details">
            <div class="detail-item">
                <span class="label">Kedudukan:</span>
                <span class="value">#{{ $rank }}</span>
            </div>
            <div class="detail-item">
                <span class="label">Masa:</span>
                <span class="value">{{ $time_taken }}s</span>
            </div>
            <div class="detail-item">
                <span class="label">Ketepatan:</span>
                <span class="value">{{ $accuracy }}%</span>
            </div>
        </div>
    </div>
    
    <!-- Rewards Section -->
    @if($rewards && count($rewards) > 0)
    <div class="rewards-section">
        <h3>🎁 Anugerah Diperolehi!</h3>
        <div class="rewards-grid">
            @foreach($rewards as $reward)
            <div class="reward-card">
                <div class="reward-icon">{{ $reward['icon'] }}</div>
                <div class="reward-info">
                    <h4>{{ $reward['name'] }}</h4>
                    <p>{{ $reward['description'] }}</p>
                    <span class="reward-xp">+{{ $reward['xp'] }} XP</span>
                </div>
            </div>
            @endforeach
        </div>
        
        <button class="btn-collect-rewards" onclick="collectRewards()">
            Kumpul Semua Anugerah
        </button>
    </div>
    @endif
    
    <!-- Action Buttons -->
    <div class="action-buttons">
        <button class="btn-main-again" onclick="restartGame()">
            Main Semula
        </button>
        <button class="btn-leaderboard" onclick="showLeaderboard({{ $game_id }})">
            Papan Pemimpin
        </button>
        <button class="btn-back" onclick="goBack()">
            Kembali ke Menu
        </button>
    </div>
</div>

<style>
.game-summary {
    text-align: center;
    padding: 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    color: white;
    max-width: 500px;
    margin: 0 auto;
}

.score-circle {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    background: white;
    color: #333;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    margin: 0 auto 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

.score-circle .score {
    font-size: 48px;
    font-weight: bold;
}

.score-circle .score-label {
    font-size: 14px;
    color: #666;
}

.score-details {
    display: flex;
    justify-content: space-around;
    margin: 20px 0;
}

.detail-item {
    text-align: center;
}

.detail-item .label {
    display: block;
    font-size: 12px;
    opacity: 0.8;
}

.detail-item .value {
    display: block;
    font-size: 18px;
    font-weight: bold;
}

.rewards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin: 20px 0;
}

.reward-card {
    background: rgba(255,255,255,0.1);
    border-radius: 10px;
    padding: 15px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.reward-icon {
    font-size: 30px;
}

.reward-info h4 {
    margin: 0;
    font-size: 16px;
}

.reward-info p {
    margin: 5px 0;
    font-size: 12px;
    opacity: 0.9;
}

.reward-xp {
    color: #FFD700;
    font-weight: bold;
}

.action-buttons {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 20px;
}

button {
    padding: 12px 24px;
    border: none;
    border-radius: 25px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-main-again {
    background: #4CAF50;
    color: white;
}

.btn-leaderboard {
    background: #2196F3;
    color: white;
}

.btn-back {
    background: #9C27B0;
    color: white;
}

.btn-collect-rewards {
    background: #FF9800;
    color: white;
    width: 100%;
    margin-top: 10px;
}

button:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}
</style>

<script>
function restartGame() {
    // Reload the game
    window.location.reload();
}

function showLeaderboard(gameId) {
    // Show leaderboard modal for this game
    fetch(`/api/games/${gameId}/leaderboard`)
        .then(response => response.json())
        .then(data => {
            // Show leaderboard modal with data
            displayLeaderboardModal(data);
        });
}

function goBack() {
    // Return to games list
    window.location.href = '/permainan';
}

function collectRewards() {
    fetch('/api/rewards/collect', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            game_id: {{ $game_id }},
            score: {{ $score }}
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Anugerah berjaya dikumpul!');
            // Update UI
            document.querySelector('.btn-collect-rewards').style.display = 'none';
        }
    });
}

function displayLeaderboardModal(leaderboardData) {
    // Create modal HTML
    const modalHTML = `
        <div class="leaderboard-modal">
            <div class="modal-content">
                <h2>🏆 Papan Pemimpin</h2>
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
                        ${leaderboardData.map((entry, index) => `
                            <tr class="${entry.is_current_user ? 'highlight' : ''}">
                                <td>#${index + 1}</td>
                                <td>${entry.user_name}</td>
                                <td>${entry.score}</td>
                                <td>${entry.time_taken}s</td>
                                <td>${new Date(entry.created_at).toLocaleDateString()}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
                <button onclick="closeModal()">Tutup</button>
            </div>
        </div>
    `;
    
    // Add to page
    document.body.insertAdjacentHTML('beforeend', modalHTML);
}

function closeModal() {
    const modal = document.querySelector('.leaderboard-modal');
    if (modal) {
        modal.remove();
    }
}
</script>