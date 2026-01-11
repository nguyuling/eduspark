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
      <h1 style={{ color: 'var(--accent)', marginBottom: '20px' }}>Papan Pemuka</h1>
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
          <h3>Selamat Datang ke EduSpark!</h3>
          <p>Mulakan perjalanan pembelajaran anda dengan permainan dan bahan interaktif.</p>
        </div>
        
        <div style={{
          background: 'rgba(255,255,255,0.05)',
          padding: '20px',
          borderRadius: '12px',
          border: '1px solid rgba(255,255,255,0.1)'
        }}>
          <h3>Aktiviti Terkini</h3>
          <p>Tiada aktiviti lagi. Mulakan bermain permainan!</p>
        </div>

        {/* Added more dashboard cards for better UX */}
        <div style={{
          background: 'rgba(255,255,255,0.05)',
          padding: '20px',
          borderRadius: '12px',
          border: '1px solid rgba(255,255,255,0.1)'
        }}>
          <h3>🏆 Pencapaian</h3>
          <p>Selesaikan permainan untuk membuka pencapaian istimewa!</p>
          <div style={{ marginTop: '10px', fontSize: '0.9rem', color: 'var(--success)' }}>
            • Penjelajah Baru (Siapkan 1 permainan)
            <br/>
            • Pakar Java (Jawab 10 soalan Java)
          </div>
        </div>

        <div style={{
          background: 'rgba(255,255,255,0.05)',
          padding: '20px',
          borderRadius: '12px',
          border: '1px solid rgba(255,255,255,0.1)'
        }}>
          <h3>🎯 Tips Pantas</h3>
          <p>Untuk permainan labirin:</p>
          <ul style={{ marginTop: '10px', paddingLeft: '20px', fontSize: '0.9rem' }}>
            <li>Gunakan kekunci anak panah untuk bergerak</li>
            <li>Jawab soalan untuk markah tambahan</li>
            <li>Capai penamat sebelum masa tamat</li>
          </ul>
        </div>
      </div>

      {/* Call to action section */}
      <div style={{
        marginTop: '40px',
        textAlign: 'center',
        background: 'linear-gradient(90deg, rgba(106, 77, 247, 0.1), rgba(156, 123, 255, 0.05))',
        padding: '30px',
        borderRadius: '16px',
        border: '1px solid rgba(106, 77, 247, 0.2)'
      }}>
        <h3 style={{ color: 'var(--accent)', marginBottom: '15px' }}>Bersedia untuk Bermain?</h3>
        <p style={{ marginBottom: '20px' }}>Pilih permainan dari menu "Permainan" untuk mula belajar!</p>
        <button
          onClick={() => window.location.href = '/games'}
          style={{
            background: 'var(--accent)',
            color: 'white',
            border: 'none',
            padding: '12px 24px',
            borderRadius: '8px',
            fontWeight: '600',
            cursor: 'pointer',
            fontSize: '1rem'
          }}
        >
          ⟶ Pergi ke Permainan
        </button>
      </div>
    </div>
  );
};

export default Dashboard;