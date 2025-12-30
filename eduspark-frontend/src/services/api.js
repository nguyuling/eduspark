// src/services/api.js

const API_BASE_URL = "http://localhost:3000/api";

// Mock game data
const mockGames = [
  {
    id: 1,
    title: "Space Adventure",
    description: "Defend your spaceship from alien invaders in this exciting space shooter game.",
    category: "Action",
    difficulty: "Medium",
    imageUrl: "https://images.unsplash.com/photo-1446776653964-20c1d3a81b06?w=400",
    url: "/games/SpaceAdventure",
    ratings: 4.5
  },
  {
    id: 2,
    title: "Whack A Mole",
    description: "Test your reflexes by whacking moles as they pop up from their holes.",
    category: "Arcade",
    difficulty: "Easy",
    imageUrl: "https://images.unsplash.com/photo-1511512578047-dfb367046420?w=400",
    url: "/games/WhackAMole",
    ratings: 4.2
  },
  {
    id: 3,
    title: "Memory Match",
    description: "Challenge your memory by matching pairs of cards in this brain-training game.",
    category: "Puzzle",
    difficulty: "Easy",
    imageUrl: "https://images.unsplash.com/photo-1593305841991-05c297ba4575?w-400",
    url: "/games/MemoryMatch",
    ratings: 4.7
  }
];

// Game service - NOTE: variable name must match import
export const gameService = {
  getAllGames: async () => {
    return new Promise((resolve) => {
      setTimeout(() => {
        resolve(mockGames);
      }, 500);
    });
  },
  
  getGameById: async (id) => {
    const game = mockGames.find(game => game.id === id);
    return new Promise((resolve, reject) => {
      setTimeout(() => {
        if (game) {
          resolve(game);
        } else {
          reject(new Error('Game not found'));
        }
      }, 300);
    });
  }
};