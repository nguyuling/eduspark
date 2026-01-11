// src/services/progressService.js
import { gameService } from './api';

export const progressService = {
  startGame: async (gameId) => {
    // Mock implementation - replace with actual API call
    return new Promise((resolve) => {
      setTimeout(() => {
        resolve({
          data: {
            progress: {
              id: Date.now(),
              gameId: gameId,
              startedAt: new Date().toISOString()
            }
          }
        });
      }, 300);
    });
  },
  
  saveProgress: async (gameId, data) => {
    // Mock implementation
    return new Promise((resolve) => {
      setTimeout(() => {
        resolve({
          data: {
            progress: {
              id: Date.now(),
              gameId: gameId,
              score: data.score,
              level: data.level,
              completed: data.completed
            },
            rewards_unlocked: data.score > 50 ? ['bronze_medal'] : []
          }
        });
      }, 300);
    });
  },

  claimReward: async (rewardId) => {
    try {
      const response = await fetch(`/api/rewards/${rewardId}/claim`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
      });
      return await response.json();
    } catch (error) {
      console.error('Claim reward error:', error);
      throw error;
    }
  }
};
