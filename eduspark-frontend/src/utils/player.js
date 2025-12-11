export const getPlayerId = () => {
  let playerId = localStorage.getItem('playerId');
  
  if (!playerId) {
    // Generate a simple player ID if none exists
    playerId = 'player_' + Math.random().toString(36).substr(2, 9);
    localStorage.setItem('playerId', playerId);
  }
  
  return playerId;
};
export const setPlayerId = (id) => {
  localStorage.setItem('playerId', id);
};