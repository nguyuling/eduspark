// components/Leaderboard/Leaderboard.js
import React, { useState, useEffect } from 'react';
import leaderboardService from '../../services/leaderboardService';
import TeacherResetPanel from './TeacherResetPanel';
import './Leaderboard.css';

const Leaderboard = () => {
  const [leaderboardData, setLeaderboardData] = useState([]);
  const [filteredData, setFilteredData] = useState([]);
  const [loading, setLoading] = useState(true);
  const [filters, setFilters] = useState({
    class: 'all',
    subject: 'all',
    timePeriod: 'all'
  });
  const [sortBy, setSortBy] = useState('score');
  const [availableClasses, setAvailableClasses] = useState([]);
  const [availableSubjects, setAvailableSubjects] = useState([]);
  const [userPosition, setUserPosition] = useState(null);
  const [userRole, setUserRole] = useState('student'); // Change to 'teacher' for testing teacher features
  const [error, setError] = useState(null);

  useEffect(() => {
    fetchLeaderboardData();
    fetchDropdownOptions();
    // fetchUserPosition(); // Uncomment when you have user authentication
  }, []);

  useEffect(() => {
    applyFiltersAndSort();
  }, [filters, sortBy, leaderboardData]);

  const fetchLeaderboardData = async () => {
    try {
      setLoading(true);
      setError(null);
      
      // Use the actual API call instead of mock data
      const response = await leaderboardService.getLeaderboard(filters);
      
      if (response.success) {
        // Transform API data to match frontend structure
        const transformedData = response.data.map(item => ({
          id: item.id,
          name: item.name,
          class: item.class,
          subject: item.subject,
          score: item.total_score,
          achievements: item.achievements_count,
          gamesPlayed: item.games_played,
          lastPlayed: item.last_played
        }));
        
        setLeaderboardData(transformedData);
        setFilteredData(transformedData);
      } else {
        setError('Gagal memuatkan data papan kedudukan');
      }
    } catch (error) {
      console.error('Error fetching leaderboard:', error);
      setError('Ralat sambungan. Sila cuba lagi.');
      
      // Fallback to mock data if API fails
      const mockData = [
        { id: 1, name: 'Ahmad bin Ali', class: '4A', subject: 'Sains Komputer', score: 245, achievements: 12, gamesPlayed: 8, lastPlayed: '2024-01-15' },
        { id: 2, name: 'Siti Nurhaliza', class: '5B', subject: 'Sains Komputer', score: 320, achievements: 18, gamesPlayed: 12, lastPlayed: '2024-01-14' },
        { id: 3, name: 'Raj Kumar', class: '4C', subject: 'Matematik', score: 180, achievements: 8, gamesPlayed: 5, lastPlayed: '2024-01-13' },
        { id: 4, name: 'Mei Ling', class: '5A', subject: 'Sains Komputer', score: 410, achievements: 22, gamesPlayed: 15, lastPlayed: '2024-01-15' },
        { id: 5, name: 'Ali bin Hassan', class: '4B', subject: 'Sains Komputer', score: 195, achievements: 10, gamesPlayed: 7, lastPlayed: '2024-01-12' },
        { id: 6, name: 'Priya Devi', class: '5C', subject: 'Matematik', score: 275, achievements: 14, gamesPlayed: 9, lastPlayed: '2024-01-14' },
        { id: 7, name: 'John Lim', class: '4A', subject: 'Sains Komputer', score: 360, achievements: 20, gamesPlayed: 11, lastPlayed: '2024-01-15' },
        { id: 8, name: 'Nurul Iman', class: '5B', subject: 'Sains Komputer', score: 290, achievements: 16, gamesPlayed: 10, lastPlayed: '2024-01-13' },
      ];
      
      setLeaderboardData(mockData);
      setFilteredData(mockData);
    } finally {
      setLoading(false);
    }
  };

  const fetchDropdownOptions = async () => {
    try {
      const classesResponse = await leaderboardService.getAvailableClasses();
      const subjectsResponse = await leaderboardService.getAvailableSubjects();
      
      if (classesResponse.success) {
        setAvailableClasses(classesResponse.data);
      } else {
        setAvailableClasses(['4A', '4B', '4C', '5A', '5B', '5C']); // Fallback
      }
      
      if (subjectsResponse.success) {
        setAvailableSubjects(subjectsResponse.data);
      } else {
        setAvailableSubjects(['Sains Komputer', 'Matematik', 'Bahasa Melayu', 'Bahasa Inggeris']); // Fallback
      }
    } catch (error) {
      console.error('Error fetching dropdown options:', error);
      // Set fallback values
      setAvailableClasses(['4A', '4B', '4C', '5A', '5B', '5C']);
      setAvailableSubjects(['Sains Komputer', 'Matematik', 'Bahasa Melayu', 'Bahasa Inggeris']);
    }
  };

  const fetchUserPosition = async () => {
    try {
      // Get current user ID - you need to implement this based on your auth system
      const userId = localStorage.getItem('userId') || '1'; // Replace with actual user ID
      const response = await leaderboardService.getUserPosition(userId, filters);
      
      if (response.success) {
        setUserPosition(response.data);
      }
    } catch (error) {
      console.error('Error fetching user position:', error);
    }
  };

  const applyFiltersAndSort = () => {
    let result = [...leaderboardData];

    // Apply time period filter client-side (since we're fetching all data and filtering locally)
    if (filters.timePeriod !== 'all') {
      const now = new Date();
      let startDate = new Date();
      
      switch(filters.timePeriod) {
        case 'today':
          startDate.setHours(0, 0, 0, 0);
          break;
        case 'week':
          startDate.setDate(startDate.getDate() - 7);
          break;
        case 'month':
          startDate.setMonth(startDate.getMonth() - 1);
          break;
        case 'year':
          startDate.setFullYear(startDate.getFullYear() - 1);
          break;
        default:
          break;
      }
      
      result = result.filter(item => {
        if (!item.lastPlayed) return true;
        return new Date(item.lastPlayed) >= startDate;
      });
    }

    // Apply sorting
    result.sort((a, b) => {
      if (sortBy === 'score') {
        return b.score - a.score;
      } else if (sortBy === 'achievements') {
        return b.achievements - a.achievements;
      } else if (sortBy === 'name') {
        return a.name.localeCompare(b.name);
      }
      return 0;
    });

    setFilteredData(result);
  };

  const handleFilterChange = async (filterType, value) => {
    const newFilters = {
      ...filters,
      [filterType]: value
    };
    
    setFilters(newFilters);
    
    // Refetch data with new filters (except timePeriod which we handle client-side)
    if (filterType === 'class' || filterType === 'subject') {
      try {
        setLoading(true);
        const response = await leaderboardService.getLeaderboard(newFilters);
        
        if (response.success) {
          const transformedData = response.data.map(item => ({
            id: item.id,
            name: item.name,
            class: item.class,
            subject: item.subject,
            score: item.total_score,
            achievements: item.achievements_count,
            gamesPlayed: item.games_played,
            lastPlayed: item.last_played
          }));
          
          setLeaderboardData(transformedData);
        }
      } catch (error) {
        console.error('Error fetching filtered data:', error);
      } finally {
        setLoading(false);
      }
    }
  };

  const handleSortChange = (type) => {
    setSortBy(type);
  };

  const resetFilters = () => {
    setFilters({
      class: 'all',
      subject: 'all',
      timePeriod: 'all'
    });
    setSortBy('score');
    fetchLeaderboardData();
  };

  const handleResetLeaderboard = async (classId, subjectId) => {
    try {
      const response = await leaderboardService.resetLeaderboard(classId, subjectId);
      
      if (response.success) {
        alert('Papan kedudukan telah diset semula!');
        // Refresh the leaderboard data
        fetchLeaderboardData();
      } else {
        alert('Gagal menyet semula papan kedudukan: ' + response.message);
      }
    } catch (error) {
      alert('Gagal menyet semula papan kedudukan: ' + error.message);
    }
  };

  const getRankColor = (index) => {
    if (index === 0) return '#FFD700'; // Gold
    if (index === 1) return '#C0C0C0'; // Silver
    if (index === 2) return '#CD7F32'; // Bronze
    return '#4ECDC4'; // Default teal
  };

  const getMedal = (index) => {
    if (index === 0) return '🥇';
    if (index === 1) return '🥈';
    if (index === 2) return '🥉';
    return `${index + 1}`;
  };

  const getSubjectBadgeClass = (subject) => {
    switch(subject) {
      case 'Sains Komputer':
        return 'cs-badge';
      case 'Matematik':
        return 'math-badge';
      case 'Bahasa Melayu':
        return 'bm-badge';
      case 'Bahasa Inggeris':
        return 'bi-badge';
      default:
        return 'cs-badge';
    }
  };

  if (loading) {
    return (
      <div className="leaderboard-container">
        <div className="loading-spinner">
          <div className="spinner"></div>
          <p>Memuatkan Kedudukan...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="leaderboard-container">
      <div className="leaderboard-header">
        <h1>📊 Papan Kedudukan</h1>
        <p className="subtitle">Bandingkan pencapaian anda dengan rakan-rakan</p>
      </div>

      {error && (
        <div className="error-message">
          <p>⚠️ {error}</p>
        </div>
      )}

      {/* Filters Section */}
      <div className="filters-section">
        <div className="filter-group">
          <label>Kelas:</label>
          <select 
            value={filters.class} 
            onChange={(e) => handleFilterChange('class', e.target.value)}
          >
            <option value="all">Semua Kelas</option>
            {availableClasses.map(cls => (
              <option key={cls} value={cls}>{cls}</option>
            ))}
          </select>
        </div>

        <div className="filter-group">
          <label>Subjek:</label>
          <select 
            value={filters.subject} 
            onChange={(e) => handleFilterChange('subject', e.target.value)}
          >
            <option value="all">Semua Subjek</option>
            {availableSubjects.map(subj => (
              <option key={subj} value={subj}>{subj}</option>
            ))}
          </select>
        </div>

        <div className="filter-group">
          <label>Tempoh:</label>
          <select 
            value={filters.timePeriod} 
            onChange={(e) => handleFilterChange('timePeriod', e.target.value)}
          >
            <option value="all">Semua Masa</option>
            <option value="today">Hari Ini</option>
            <option value="week">Minggu Ini</option>
            <option value="month">Bulan Ini</option>
            <option value="year">Tahun Ini</option>
          </select>
        </div>

        <button className="reset-btn" onClick={resetFilters}>
          ↺ Set Semula Penapis
        </button>
      </div>

      {/* Sorting Options */}
      <div className="sorting-section">
        <p>Susun mengikut:</p>
        <div className="sort-buttons">
          <button 
            className={`sort-btn ${sortBy === 'score' ? 'active' : ''}`}
            onClick={() => handleSortChange('score')}
          >
            ⭐ Markah Tertinggi
          </button>
          <button 
            className={`sort-btn ${sortBy === 'achievements' ? 'active' : ''}`}
            onClick={() => handleSortChange('achievements')}
          >
            🏆 Pencapaian
          </button>
          <button 
            className={`sort-btn ${sortBy === 'name' ? 'active' : ''}`}
            onClick={() => handleSortChange('name')}
          >
            🔤 Nama
          </button>
        </div>
      </div>

      {/* Leaderboard Table */}
      <div className="leaderboard-table-container">
        {filteredData.length === 0 ? (
          <div className="no-data">
            <p>Tiada data ditemui untuk penapis yang dipilih.</p>
            <button onClick={resetFilters} className="reset-data-btn">
              ↺ Lihat Semua Data
            </button>
          </div>
        ) : (
          <table className="leaderboard-table">
            <thead>
              <tr>
                <th style={{ width: '60px' }}>Kedudukan</th>
                <th style={{ width: '200px' }}>Nama Pelajar</th>
                <th style={{ width: '80px' }}>Kelas</th>
                <th style={{ width: '150px' }}>Subjek</th>
                <th style={{ width: '100px' }}>Markah</th>
                <th style={{ width: '100px' }}>Pencapaian</th>
                <th style={{ width: '100px' }}>Permainan</th>
                <th style={{ width: '120px' }}>Akhir Main</th>
              </tr>
            </thead>
            <tbody>
              {filteredData.map((student, index) => (
                <tr 
                  key={student.id}
                  className={index < 3 ? 'top-three' : ''}
                  style={index < 3 ? { 
                    backgroundColor: getRankColor(index) + '20',
                    borderLeft: `4px solid ${getRankColor(index)}`
                  } : {}}
                >
                  <td className="rank-cell">
                    <span className="rank-number" style={{ color: getRankColor(index) }}>
                      {getMedal(index)}
                    </span>
                  </td>
                  <td className="student-name">
                    <div className="avatar-placeholder">
                      {student.name.charAt(0)}
                    </div>
                    <div>
                      <strong>{student.name}</strong>
                      {index < 3 && (
                        <span className="top-badge">
                          {index === 0 ? '🏆 Juara' : index === 1 ? '🥈 Naib Juara' : '🥉 Ketiga'}
                        </span>
                      )}
                    </div>
                  </td>
                  <td>
                    <span className="class-badge">{student.class}</span>
                  </td>
                  <td>
                    <span className={`subject-badge ${getSubjectBadgeClass(student.subject)}`}>
                      {student.subject}
                    </span>
                  </td>
                  <td className="score-cell">
                    <strong style={{ color: '#F59E0B' }}>{student.score}</strong>
                    <div className="score-progress">
                      <div 
                        className="progress-bar" 
                        style={{ 
                          width: `${Math.min((student.score / 500) * 100, 100)}%`,
                          backgroundColor: index < 3 ? getRankColor(index) : '#4ECDC4'
                        }}
                      ></div>
                    </div>
                  </td>
                  <td className="achievements-cell">
                    <span className="achievement-count">{student.achievements}</span>
                  </td>
                  <td>
                    <span className="games-played">{student.gamesPlayed}</span>
                  </td>
                  <td>
                    <span className="last-played">
                      {student.lastPlayed ? 
                        new Date(student.lastPlayed).toLocaleDateString('ms-MY') : 
                        'Belum bermain'}
                    </span>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
      </div>

      {/* Teacher Reset Panel */}
      <TeacherResetPanel 
        userRole={userRole}
        onReset={handleResetLeaderboard}
      />

      {/* Legend */}
      <div className="legend">
        <div className="legend-item">
          <span className="legend-color" style={{ backgroundColor: '#FFD700' }}></span>
          <span>Kedudukan Pertama</span>
        </div>
        <div className="legend-item">
          <span className="legend-color" style={{ backgroundColor: '#C0C0C0' }}></span>
          <span>Kedudukan Kedua</span>
        </div>
        <div className="legend-item">
          <span className="legend-color" style={{ backgroundColor: '#CD7F32' }}></span>
          <span>Kedudukan Ketiga</span>
        </div>
        <div className="legend-item">
          <span className="legend-color" style={{ backgroundColor: '#4ECDC4' }}></span>
          <span>Pelajar Lain</span>
        </div>
      </div>

      {/* User's Position - Dynamic */}
      <div className="user-position-card">
        <h3>Kedudukan Anda</h3>
        {userPosition ? (
          <div className="user-info">
            <div className="user-avatar">
              <span>👤</span>
            </div>
            <div className="user-details">
              <h4>{userPosition.name}</h4>
              <p>Kelas: {userPosition.class || 'Tiada'} • Kedudukan: #{userPosition.rank}</p>
              <div className="user-stats">
                <div className="stat">
                  <span className="stat-label">Markah:</span>
                  <span className="stat-value">{userPosition.total_score}</span>
                </div>
                <div className="stat">
                  <span className="stat-label">Pencapaian:</span>
                  <span className="stat-value">0</span> {/* You need to fetch achievements separately */}
                </div>
                <div className="stat">
                  <span className="stat-label">Kedudukan:</span>
                  <span className="stat-value">#{userPosition.rank}</span>
                </div>
              </div>
            </div>
          </div>
        ) : (
          <div className="user-info">
            <div className="user-avatar">
              <span>👤</span>
            </div>
            <div className="user-details">
              <h4>Anda</h4>
              <p>Belum ada data prestasi</p>
              <p className="play-game-hint">Main permainan untuk muncul di papan kedudukan!</p>
            </div>
          </div>
        )}
        <div className="motivation-text">
          {userPosition && userPosition.total_score < 100 ? 'Teruskan berusaha!' : 
           userPosition && userPosition.total_score < 200 ? 'Bagus! Teruskan!' : 
           userPosition ? 'Cemerlang! Pertahankan pencapaian!' : 'Mula main untuk naik ranking!'}
        </div>
      </div>
    </div>
  );
};

export default Leaderboard;