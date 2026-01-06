import React, { useState, useEffect } from 'react';
import axios from 'axios';

const TeacherGames = () => {
  const [games, setGames] = useState([]);
  const [loading, setLoading] = useState(true);
  const [editingGame, setEditingGame] = useState(null);
  const [showAddModal, setShowAddModal] = useState(false);

  useEffect(() => {
    fetchTeacherGames();
  }, []);

  const fetchTeacherGames = async () => {
    try {
      const response = await axios.get('/api/teacher/games', {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
        }
      });
      setGames(response.data);
      setLoading(false);
    } catch (error) {
      console.error('Error fetching games:', error);
      setLoading(false);
    }
  };

  const handleEdit = (game) => {
    setEditingGame(game);
  };

  const handleDelete = async (id) => {
    if (window.confirm('Are you sure you want to delete this game?')) {
      try {
        await axios.delete(`/api/teacher/games/${id}`, {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
          }
        });
        setGames(games.filter(game => game.id !== id));
      } catch (error) {
        console.error('Error deleting game:', error);
        alert('Failed to delete game');
      }
    }
  };

  const handleSave = async (updatedGame) => {
    try {
      await axios.put(`/api/teacher/games/${updatedGame.id}`, updatedGame, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
        }
      });
      setGames(games.map(game => 
        game.id === updatedGame.id ? updatedGame : game
      ));
      setEditingGame(null);
    } catch (error) {
      console.error('Error saving game:', error);
      alert('Failed to save game');
    }
  };

  return (
    <div style={{ 
      padding: '30px',
      marginLeft: '280px', // Account for sidebar
      minHeight: '100vh',
      backgroundColor: 'var(--background)',
      color: 'var(--text)'
    }}>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '30px' }}>
        <div>
          <h1 style={{ fontSize: '28px', fontWeight: '700', margin: '0' }}>Manage Games</h1>
          <p style={{ color: 'var(--muted)', marginTop: '8px' }}>Create, edit, and manage educational games</p>
        </div>
        <button 
          onClick={() => setShowAddModal(true)}
          style={{
            padding: '12px 24px',
            backgroundColor: 'var(--accent)',
            color: 'white',
            border: 'none',
            borderRadius: '12px',
            fontWeight: '600',
            cursor: 'pointer',
            fontSize: '16px',
            display: 'flex',
            alignItems: 'center',
            gap: '8px'
          }}
        >
          <span>+</span> Add New Game
        </button>
      </div>

      {/* Stats Cards */}
      <div style={{ 
        display: 'grid', 
        gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))', 
        gap: '20px', 
        marginBottom: '30px' 
      }}>
        <div style={{
          backgroundColor: 'var(--card-bg)',
          borderRadius: '16px',
          padding: '20px',
          border: '1px solid var(--border)'
        }}>
          <div style={{ color: 'var(--muted)', fontSize: '14px', fontWeight: '600' }}>Total Games</div>
          <div style={{ fontSize: '32px', fontWeight: '700', marginTop: '8px' }}>{games.length}</div>
        </div>
        <div style={{
          backgroundColor: 'var(--card-bg)',
          borderRadius: '16px',
          padding: '20px',
          border: '1px solid var(--border)'
        }}>
          <div style={{ color: 'var(--muted)', fontSize: '14px', fontWeight: '600' }}>Active Games</div>
          <div style={{ fontSize: '32px', fontWeight: '700', marginTop: '8px' }}>
            {games.filter(g => g.status === 'active').length}
          </div>
        </div>
        <div style={{
          backgroundColor: 'var(--card-bg)',
          borderRadius: '16px',
          padding: '20px',
          border: '1px solid var(--border)'
        }}>
          <div style={{ color: 'var(--muted)', fontSize: '14px', fontWeight: '600' }}>Avg. Score</div>
          <div style={{ fontSize: '32px', fontWeight: '700', marginTop: '8px' }}>
            {Math.round(games.reduce((acc, game) => acc + game.score, 0) / games.length)} pts
          </div>
        </div>
      </div>

      {/* Games Table */}
      <div style={{
        backgroundColor: 'var(--card-bg)',
        borderRadius: '16px',
        overflow: 'hidden',
        border: '1px solid var(--border)'
      }}>
        <table style={{ width: '100%', borderCollapse: 'collapse' }}>
          <thead>
            <tr style={{ 
              backgroundColor: 'var(--header-bg)',
              borderBottom: '1px solid var(--border)'
            }}>
              <th style={{ padding: '16px 20px', textAlign: 'left', fontWeight: '600' }}>Game Title</th>
              <th style={{ padding: '16px 20px', textAlign: 'left', fontWeight: '600' }}>Category</th>
              <th style={{ padding: '16px 20px', textAlign: 'left', fontWeight: '600' }}>Difficulty</th>
              <th style={{ padding: '16px 20px', textAlign: 'left', fontWeight: '600' }}>Status</th>
              <th style={{ padding: '16px 20px', textAlign: 'left', fontWeight: '600' }}>Avg Score</th>
              <th style={{ padding: '16px 20px', textAlign: 'left', fontWeight: '600' }}>Actions</th>
            </tr>
          </thead>
          <tbody>
            {games.map(game => (
              <tr key={game.id} style={{ 
                borderBottom: '1px solid var(--border)',
                '&:last-child': { borderBottom: 'none' }
              }}>
                <td style={{ padding: '16px 20px' }}>{game.title}</td>
                <td style={{ padding: '16px 20px' }}>
                  <span style={{
                    padding: '4px 12px',
                    backgroundColor: game.category === 'Action' ? 'rgba(29, 93, 205, 0.1)' : 
                                    game.category === 'Casual' ? 'rgba(46, 204, 113, 0.1)' : 
                                    'rgba(155, 89, 182, 0.1)',
                    color: game.category === 'Action' ? '#1D5DCD' : 
                           game.category === 'Casual' ? '#2ecc71' : 
                           '#9b59b6',
                    borderRadius: '20px',
                    fontSize: '14px',
                    fontWeight: '600'
                  }}>
                    {game.category}
                  </span>
                </td>
                <td style={{ padding: '16px 20px' }}>
                  <span style={{
                    padding: '4px 12px',
                    backgroundColor: game.difficulty === 'Easy' ? 'rgba(46, 204, 113, 0.1)' : 
                                    game.difficulty === 'Medium' ? 'rgba(243, 156, 18, 0.1)' : 
                                    'rgba(231, 76, 60, 0.1)',
                    color: game.difficulty === 'Easy' ? '#2ecc71' : 
                           game.difficulty === 'Medium' ? '#f39c12' : 
                           '#e74c3c',
                    borderRadius: '20px',
                    fontSize: '14px',
                    fontWeight: '600'
                  }}>
                    {game.difficulty}
                  </span>
                </td>
                <td style={{ padding: '16px 20px' }}>
                  <span style={{
                    padding: '4px 12px',
                    backgroundColor: game.status === 'active' ? 'rgba(46, 204, 113, 0.1)' : 'rgba(149, 165, 166, 0.1)',
                    color: game.status === 'active' ? '#2ecc71' : '#95a5a6',
                    borderRadius: '20px',
                    fontSize: '14px',
                    fontWeight: '600'
                  }}>
                    {game.status}
                  </span>
                </td>
                <td style={{ padding: '16px 20px', fontWeight: '600' }}>{game.score} pts</td>
                <td style={{ padding: '16px 20px' }}>
                  <div style={{ display: 'flex', gap: '8px' }}>
                    <button 
                      onClick={() => handleEdit(game)}
                      style={{
                        padding: '8px 16px',
                        backgroundColor: 'rgba(52, 152, 219, 0.1)',
                        color: '#3498db',
                        border: 'none',
                        borderRadius: '8px',
                        cursor: 'pointer',
                        fontSize: '14px',
                        fontWeight: '600'
                      }}
                    >
                      Edit
                    </button>
                    <button 
                      onClick={() => handleDelete(game.id)}
                      style={{
                        padding: '8px 16px',
                        backgroundColor: 'rgba(231, 76, 60, 0.1)',
                        color: '#e74c3c',
                        border: 'none',
                        borderRadius: '8px',
                        cursor: 'pointer',
                        fontSize: '14px',
                        fontWeight: '600'
                      }}
                    >
                      Delete
                    </button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {/* Edit Modal */}
      {editingGame && (
        <div style={{
          position: 'fixed',
          top: 0,
          left: 0,
          right: 0,
          bottom: 0,
          backgroundColor: 'rgba(0, 0, 0, 0.5)',
          display: 'flex',
          alignItems: 'center',
          justifyContent: 'center',
          zIndex: 1000
        }}>
          <div style={{
            backgroundColor: 'var(--card-bg)',
            borderRadius: '16px',
            padding: '30px',
            width: '90%',
            maxWidth: '500px'
          }}>
            <h2 style={{ marginBottom: '20px' }}>Edit Game</h2>
            <form onSubmit={(e) => {
              e.preventDefault();
              const formData = new FormData(e.target);
              handleSave({
                ...editingGame,
                title: formData.get('title'),
                category: formData.get('category'),
                difficulty: formData.get('difficulty'),
                status: formData.get('status')
              });
            }}>
              <div style={{ marginBottom: '15px' }}>
                <label style={{ display: 'block', marginBottom: '5px', fontWeight: '600' }}>Game Title</label>
                <input 
                  name="title"
                  defaultValue={editingGame.title}
                  style={{
                    width: '100%',
                    padding: '10px',
                    borderRadius: '8px',
                    border: '1px solid var(--border)',
                    backgroundColor: 'var(--input-bg)',
                    color: 'var(--text)'
                  }}
                />
              </div>
              {/* Add more fields... */}
              <div style={{ display: 'flex', gap: '10px', justifyContent: 'flex-end', marginTop: '20px' }}>
                <button 
                  type="button"
                  onClick={() => setEditingGame(null)}
                  style={{
                    padding: '10px 20px',
                    backgroundColor: 'transparent',
                    color: 'var(--text)',
                    border: '1px solid var(--border)',
                    borderRadius: '8px',
                    cursor: 'pointer'
                  }}
                >
                  Cancel
                </button>
                <button 
                  type="submit"
                  style={{
                    padding: '10px 20px',
                    backgroundColor: 'var(--accent)',
                    color: 'white',
                    border: 'none',
                    borderRadius: '8px',
                    cursor: 'pointer'
                  }}
                >
                  Save Changes
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};

export default TeacherGames;