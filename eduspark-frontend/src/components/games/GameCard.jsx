import React from 'react';
import { useNavigate } from 'react-router-dom';

const GameCard = ({ game }) => {
  const navigate = useNavigate();

  const handlePlayGame = () => {
    navigate(`/game/${game.slug}`);
  };

  return (
    <div className="card h-100">
      <div className="card-body">
        <h5 className="card-title">{game.name}</h5>
        <p className="card-text">{game.description}</p>
        <div className="mb-2">
          <span className="badge bg-primary me-2">{game.topic}</span>
          <span className="badge bg-dark">{game.difficulty}</span>
        </div>
      </div>
      <div className="card-footer">
        <button 
          className="btn btn-primary w-100"
          onClick={handlePlayGame}
        >
          Play Game
        </button>
      </div>
    </div>
  );
};

export default GameCard;