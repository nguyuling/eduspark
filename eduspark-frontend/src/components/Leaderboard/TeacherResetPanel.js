// components/Leaderboard/TeacherResetPanel.js
import React, { useState } from 'react';
import './TeacherResetPanel.css';

const TeacherResetPanel = ({ userRole, onReset }) => {
  const [resetClass, setResetClass] = useState('');
  const [resetSubject, setResetSubject] = useState('');
  const [isConfirming, setIsConfirming] = useState(false);
  const [isResetting, setIsResetting] = useState(false);

  const handleReset = async () => {
    if (!isConfirming) {
      setIsConfirming(true);
      return;
    }

    setIsResetting(true);
    try {
      await onReset(resetClass || null, resetSubject || null);
      setResetClass('');
      setResetSubject('');
      setIsConfirming(false);
    } catch (error) {
      console.error('Reset failed:', error);
    } finally {
      setIsResetting(false);
    }
  };

  if (userRole !== 'teacher') {
    return null;
  }

  return (
    <div className="teacher-reset-panel">
      <h3>⚙️ Pengurusan Papan Kedudukan (Guru Sahaja)</h3>
      
      <div className="reset-controls">
        <div className="form-group">
          <label>Set Semula untuk Kelas:</label>
          <select 
            value={resetClass}
            onChange={(e) => setResetClass(e.target.value)}
            disabled={isResetting}
          >
            <option value="">Semua Kelas</option>
            <option value="4A">4A</option>
            <option value="4B">4B</option>
            <option value="4C">4C</option>
            <option value="5A">5A</option>
            <option value="5B">5B</option>
            <option value="5C">5C</option>
          </select>
        </div>

        <div className="form-group">
          <label>Set Semula untuk Subjek:</label>
          <select 
            value={resetSubject}
            onChange={(e) => setResetSubject(e.target.value)}
            disabled={isResetting}
          >
            <option value="">Semua Subjek</option>
            <option value="Sains Komputer">Sains Komputer</option>
            <option value="Matematik">Matematik</option>
            <option value="Bahasa Melayu">Bahasa Melayu</option>
            <option value="Bahasa Inggeris">Bahasa Inggeris</option>
          </select>
        </div>

        <button 
          className={`reset-btn ${isConfirming ? 'confirm' : ''}`}
          onClick={handleReset}
          disabled={isResetting}
        >
          {isResetting ? 'Menyet Semula...' : 
           isConfirming ? '⚠️ Klik lagi untuk sahkan' : 
           'Set Semula Papan Kedudukan'}
        </button>

        {isConfirming && (
          <button 
            className="cancel-btn"
            onClick={() => setIsConfirming(false)}
            disabled={isResetting}
          >
            Batal
          </button>
        )}
      </div>

      <div className="reset-info">
        <p><strong>Perhatian:</strong> Tindakan ini akan memadam semua data markah untuk:</p>
        <ul>
          <li>Kelas: {resetClass || 'Semua Kelas'}</li>
          <li>Subjek: {resetSubject || 'Semua Subjek'}</li>
        </ul>
        <p className="warning">⚠️ Tindakan ini tidak boleh dibuat asal!</p>
      </div>
    </div>
  );
};

export default TeacherResetPanel;