import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import 'bootstrap/dist/css/bootstrap.min.css';
import './App.css';

import Header from './components/common/Header';
import GameList from './components/games/GameList';
import SpaceGame from './components/games/SpaceGame';
import WhackAMole from './components/games/WhackAMole';
import MemoryGame from './components/games/MemoryGame';
import MazeGame from './components/games/MazeGame';

function App() {
  return (
    <Router>
      <div className="App">
        <Header />
        <Routes>
          <Route path="/" element={<GameList />} />
          <Route path="/game/space-game" element={<SpaceGame />} />
          <Route path="/game/whack-mole" element={<WhackAMole />} />
          <Route path="/game/memory-game" element={<MemoryGame />} />
          <Route path="/game/maze-game" element={<MazeGame />} />
        </Routes>
      </div>
    </Router>
  );
}

export default App;