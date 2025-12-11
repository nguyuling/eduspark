// src/pages/Dashboard.jsx
import React from 'react';

const Dashboard = () => {
  return (
    <div style={{ 
      background: 'var(--background)', 
      minHeight: '100vh',
      padding: '20px',
      color: 'var(--muted)'
    }}>
      <h1 style={{ color: 'var(--accent)', marginBottom: '20px' }}>Dashboard</h1>
      <div style={{
        display: 'grid',
        gridTemplateColumns: 'repeat(auto-fit, minmax(300px, 1fr))',
        gap: '20px'
      }}>
        <div style={{
          background: 'rgba(255,255,255,0.05)',
          padding: '20px',
          borderRadius: '12px',
          border: '1px solid rgba(255,255,255,0.1)'
        }}>
          <h3>Welcome to EduSpark!</h3>
          <p>Start your learning journey with interactive games and materials.</p>
        </div>
        
        <div style={{
          background: 'rgba(255,255,255,0.05)',
          padding: '20px',
          borderRadius: '12px',
          border: '1px solid rgba(255,255,255,0.1)'
        }}>
          <h3>Recent Activity</h3>
          <p>No activity yet. Start playing games!</p>
        </div>
      </div>
    </div>
  );
};

export default Dashboard;