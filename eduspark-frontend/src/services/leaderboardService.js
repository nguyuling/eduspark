// services/leaderboardService.js
class LeaderboardService {
  constructor() {
    this.baseURL = '/api/leaderboard';
  }

  async getLeaderboard(filters = {}) {
    try {
      const queryParams = new URLSearchParams(filters).toString();
      const response = await fetch(`${this.baseURL}?${queryParams}`);
      
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      
      return await response.json();
    } catch (error) {
      console.error('Error fetching leaderboard:', error);
      throw error;
    }
  }

  async updateScore(userId, gameId, score) {
    try {
      const response = await fetch(`${this.baseURL}/update-score`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({
          user_id: userId,
          game_id: gameId,
          score: score,
          timestamp: new Date().toISOString()
        })
      });
      
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      
      return await response.json();
    } catch (error) {
      console.error('Error updating score:', error);
      throw error;
    }
  }

  async getUserPosition(userId, filters = {}) {
    try {
      const queryParams = new URLSearchParams(filters).toString();
      const response = await fetch(`${this.baseURL}/user/${userId}?${queryParams}`);
      
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      
      return await response.json();
    } catch (error) {
      console.error('Error fetching user position:', error);
      throw error;
    }
  }

  async resetLeaderboard(classId = null, subjectId = null) {
    try {
      const response = await fetch(`${this.baseURL}/reset`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({
          class_id: classId,
          subject_id: subjectId,
          reset_by: 'teacher'
        })
      });
      
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      
      return await response.json();
    } catch (error) {
      console.error('Error resetting leaderboard:', error);
      throw error;
    }
  }

  async getAvailableClasses() {
    try {
      const response = await fetch(`${this.baseURL}/available-classes`);
      return await response.json();
    } catch (error) {
      console.error('Error fetching classes:', error);
      return { success: false, data: [] };
    }
  }

  async getAvailableSubjects() {
    try {
      const response = await fetch(`${this.baseURL}/available-subjects`);
      return await response.json();
    } catch (error) {
      console.error('Error fetching subjects:', error);
      return { success: false, data: [] };
    }
  }
}

export default new LeaderboardService();