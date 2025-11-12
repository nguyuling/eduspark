import React from 'react';
import GameCard from './GameCard';

const GameList = () => {
  const games = [
    {
      id: 1,
      name: '🚀 Space Defender',
      slug: 'space-game',
      description: 'Defend against alien invaders!',
      topic: 'action',
      game_type: 'arcade',
      difficulty: 'medium'
    },
    {
      id: 2,
      name: '🎯 Whack-a-Mole', 
      slug: 'whack-mole',  
      description: 'Whack the moles for high score!',
      topic: 'casual',
      game_type: 'arcade',
      difficulty: 'easy'
    },
    {
      id: 3,
      name: '🎴 Memory Match',
      slug: 'memory-game',
      description: 'Test your memory skills!',
      topic: 'puzzle',
      game_type: 'memory',
      difficulty: 'easy'
    },
    {
      id: 4,
      name: '🎮 Maze Quest',  
      slug: 'maze-game',     
      description: 'Navigate maze & answer CS questions!', 
      topic: 'education',     
      game_type: 'adventure', 
      difficulty: 'medium'
    }
  ];

  return (
    <div className="container mt-4">
      <h2 className="text-center mb-4">SPM Computer Science Games</h2>
      <div className="row">
        {games.map(game => (
          <div key={game.id} className="col-md-6 mb-4">
            <GameCard game={game} />
          </div>
        ))}
      </div>
    </div>
  );
};

export default GameList;