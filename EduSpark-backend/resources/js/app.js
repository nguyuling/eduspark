import './bootstrap';

export function getPlayerId() {
  let name = localStorage.getItem('playerName');
  if (!name) {
    name = prompt("🎮 Welcome! Enter your name to save scores:");
    if (!name) name = "Anonymous";
    localStorage.setItem('playerName', name);
  }
  return name; 
}
