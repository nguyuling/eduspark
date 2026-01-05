import { useState, useEffect, useCallback, useRef } from 'react';
import { progressService } from '../../services/progressService';
import GameSummary from './GameSummary';
import RewardsDisplay from './RewardsDisplay';
import Leaderboard from '../leaderboard/Leaderboard';
import './WhackAMole.css';

const WhackAMole = () => {
  const [markah, setMarkah] = useState(0);
  const [masaTinggal, setMasaTinggal] = useState(30);
  const [lubang, setLubang] = useState(Array(9).fill(false));
  const [permainanTamcat, setPermainanTamcat] = useState(false);
  const [tikusMuncul, setTikusMuncul] = useState([]);
  const [kelajuan, setKelajuan] = useState(1200);
  const [keadaanTikus, setKeadaanTikus] = useState({});
  const [kombo, setKombo] = useState(0);
  const [komboTertinggi, setKomboTertinggi] = useState(0);
  const [kuasa, setKuasa] = useState(null);
  const [kuasaAktif, setKuasaAktif] = useState(false);
  const [pengiraMasaKuasa, setPengiraMasaKuasa] = useState(0);
  const [pendarab, setPendarab] = useState(1);
  const [masaTambahan, setMasaTambahan] = useState(0);
  const [tunjukTukul, setTunjukTukul] = useState(false);
  const [posisiTukul, setPosisiTukul] = useState({ x: 0, y: 0 });
  const [markahTertinggi, setMarkahTertinggi] = useState(() => {
    return parseInt(localStorage.getItem('markahTertinggiTukul') || '0');
  });
  const [permainanDimulakan, setPermainanDimulakan] = useState(false);
  const [kemajuanPermainan, setKemajuanPermainan] = useState(null);
  const [ganjaranDibuka, setGanjaranDibuka] = useState([]);
  const [tunjukRingkasan, setTunjukRingkasan] = useState(false);
  const [tunjukLeaderboard, setTunjukLeaderboard] = useState(false);
  const [tikusDitumpaskan, setTikusDitumpaskan] = useState(0);
  const [ringkasanData, setRingkasanData] = useState(null);
  const [leaderboardData, setLeaderboardData] = useState(null);
  const [sedangMemuatRingkasan, setSedangMemuatRingkasan] = useState(false);
  
  const papanRef = useRef(null);
  const permulaanMasaRef = useRef(null);

  // Get CSRF Token from Laravel
  const dapatkanTokenCsrf = () => {
    const metaTag = document.querySelector('meta[name="csrf-token"]');
    return metaTag ? metaTag.content : '';
  };

  const munculkanTikusRawak = useCallback(() => {
    if (permainanTamcat) return;
    
    setTikusMuncul([]);
    setKeadaanTikus({});
    
    const bilanganTikus = Math.min(2 + Math.floor(markah / 15), 4);
    const lubangTerpilih = [];
    
    while (lubangTerpilih.length < bilanganTikus) {
      const lubang = Math.floor(Math.random() * 9);
      if (!lubangTerpilih.includes(lubang)) {
        lubangTerpilih.push(lubang);
      }
    }
    
    setTikusMuncul(lubangTerpilih);
    
    const keadaanBaru = {};
    lubangTerpilih.forEach(lubang => {
      keadaanBaru[lubang] = 'muncul';
    });
    setKeadaanTikus(keadaanBaru);
    
    const timeoutSembunyi = setTimeout(() => {
      setTikusMuncul([]);
      setKeadaanTikus({});
      setKombo(0);
    }, kelajuan * 0.7);
    
    return () => clearTimeout(timeoutSembunyi);
  }, [permainanTamcat, kelajuan, markah]);

  useEffect(() => {
    if (permainanTamcat || !permainanDimulakan) return;
    
    const intervalTikus = setInterval(() => {
      munculkanTikusRawak();
    }, kelajuan);
    
    return () => clearInterval(intervalTikus);
  }, [munculkanTikusRawak, permainanTamcat, kelajuan, permainanDimulakan]);

  useEffect(() => {
    if (permainanTamcat || masaTinggal <= 0 || !permainanDimulakan) return;
    
    const pengira = setTimeout(() => {
      setMasaTinggal(prev => {
        if (prev <= 1) {
          setPermainanTamcat(true);
          if (markah > markahTertinggi) {
            setMarkahTertinggi(markah);
            localStorage.setItem('markahTertinggiTukul', markah.toString());
          }
          return 0;
        }
        return prev - 1;
      });
    }, 1000);
    
    return () => clearTimeout(pengira);
  }, [masaTinggal, permainanTamcat, markah, markahTertinggi, permainanDimulakan]);

  useEffect(() => {
    if (permainanTamcat || kuasaAktif || !permainanDimulakan) return;
    
    const intervalKuasa = setInterval(() => {
      if (Math.random() > 0.85) {
        const jenisKuasa = ['masa', 'mata', 'kombo'];
        const kuasaRawak = jenisKuasa[Math.floor(Math.random() * jenisKuasa.length)];
        setKuasa(kuasaRawak);
        
        setTimeout(() => {
          setKuasa(null);
        }, 5000);
      }
    }, 10000);
    
    return () => clearInterval(intervalKuasa);
  }, [permainanTamcat, kuasaAktif, permainanDimulakan]);

  useEffect(() => {
    if (kuasaAktif && pengiraMasaKuasa > 0) {
      const pengira = setTimeout(() => {
        setPengiraMasaKuasa(prev => prev - 1);
      }, 1000);
      
      return () => clearTimeout(pengira);
    } else if (kuasaAktif && pengiraMasaKuasa <= 0) {
      setKuasaAktif(false);
      setPendarab(1);
    }
  }, [kuasaAktif, pengiraMasaKuasa]);

  useEffect(() => {
    if (markah > 0 && markah % 10 === 0) {
      setKelajuan(prev => Math.max(400, prev - 50));
    }
  }, [markah]);

  useEffect(() => {
    if (permainanDimulakan) {
      mulakanPenjejakanPermainan(2);
      permulaanMasaRef.current = Date.now();
    }
  }, [permainanDimulakan]);

  // ========== NEW: Save score to Laravel ==========
  const simpanMarkahKeDatabase = async (markahAkhir, status) => {
    try {
      let idPemain = localStorage.getItem('tukulGamePlayerId');
      if (!idPemain) {
        idPemain = 'player_' + Math.random().toString(36).substr(2, 9);
        localStorage.setItem('tukulGamePlayerId', idPemain);
      }
      
      const gameId = 2; // Whack-a-Mole Game ID
      const masaDiambil = permulaanMasaRef.current ? Math.floor((Date.now() - permulaanMasaRef.current) / 1000) : 0;
      
      console.log('Menyimpan markah ke pangkalan data...', {
        user_id: idPemain,
        game_id: gameId,
        score: markahAkhir
      });
      
      // ✅ CORRECT ENDPOINT: Use Laravel API route
      const response = await fetch('/api/games/' + gameId + '/score', {
        method: 'POST',
        headers: { 
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': dapatkanTokenCsrf()
        },
        body: JSON.stringify({
          user_id: idPemain,
          game_id: gameId,
          score: markahAkhir,
          time_taken: masaDiambil,
          game_stats: {
            status: status,
            tikus_ditumpaskan: tikusDitumpaskan,
            kombo_tertinggi: komboTertinggi,
            kelajuan_minimum: kelajuan,
            powerups_dikumpul: (kuasa ? 1 : 0)
          }
        })
      });
      
      console.log('Status respons simpan:', response.status);
      
      if (!response.ok) {
        const errorText = await response.text();
        console.error('Gagal menyimpan markah:', errorText);
        
        // Try fallback endpoint
        console.log('Mencuba endpoint sandaran...');
        return await cubaSimpanSandaran(gameId, idPemain, markahAkhir, masaDiambil, status);
      }
      
      const result = await response.json();
      console.log('Markah berjaya disimpan:', result);
      return result;
      
    } catch (error) {
      console.error('Ralat menyimpan markah:', error);
      return { success: false, message: error.message };
    }
  };

  // Fallback save method
  const cubaSimpanSandaran = async (gameId, idPemain, markahAkhir, masaDiambil, status) => {
    try {
      const response = await fetch('/save-game-score', {
        method: 'POST',
        headers: { 
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          user_id: idPemain,
          game_id: gameId,
          score: markahAkhir,
          time_taken: masaDiambil,
          game_stats: {
            status: status,
            tikus_ditumpaskan: tikusDitumpaskan,
            kombo_tertinggi: komboTertinggi,
            kelajuan_minimum: kelajuan,
            powerups_dikumpul: (kuasa ? 1 : 0)
          }
        })
      });
      
      const result = await response.json();
      console.log('Keputusan simpan sandaran:', result);
      return result;
    } catch (error) {
      console.error('Simpan sandaran juga gagal:', error);
      return { success: false, message: 'Kedua-dua kaedah simpan gagal' };
    }
  };

  // ========== NEW: Get game summary from Laravel API ==========
  const muatRingkasanPermainan = async () => {
    setSedangMemuatRingkasan(true);
    try {
      // First save the score
      const keputusanSimpan = await simpanMarkahKeDatabase(markah, 'selesai');
      
      if (!keputusanSimpan || !keputusanSimpan.success) {
        console.warn('Simpan markah mungkin gagal, tetapi diteruskan...');
      }
      
      // Then get game summary
      const gameId = 2;
      let idPemain = localStorage.getItem('tukulGamePlayerId');
      if (!idPemain) {
        idPemain = 'player_' + Math.random().toString(36).substr(2, 9);
        localStorage.setItem('tukulGamePlayerId', idPemain);
      }
      
      console.log('Memuat ringkasan permainan untuk:', { gameId, idPemain });
      
      // ✅ CORRECT ENDPOINT: Use the API route from web.php
      const url = `/api/game-summary/${gameId}?user_id=${idPemain}`;
      console.log('Mengambil dari:', url);
      
      const response = await fetch(url, {
        credentials: 'include',
        headers: {
          'Accept': 'application/json'
        }
      });
      
      console.log('Status respons ringkasan:', response.status);
      
      if (!response.ok) {
        throw new Error(`HTTP ${response.status}: ${await response.text()}`);
      }
      
      const data = await response.json();
      console.log('Data ringkasan permainan diterima:', data);
      
      if (data.success) {
        setRingkasanData(data.summary);
      } else {
        console.warn('API mengembalikan success:false', data.message);
        // Create local summary as fallback
        buatRingkasanTempatan();
      }
    } catch (error) {
      console.error('Ralat memuat ringkasan permainan:', error);
      // Create local summary as fallback
      buatRingkasanTempatan();
    } finally {
      setSedangMemuatRingkasan(false);
    }
  };

  // Create local summary when API fails
  const buatRingkasanTempatan = () => {
    const masaDiambil = permulaanMasaRef.current ? Math.floor((Date.now() - permulaanMasaRef.current) / 1000) : 0;
    const xpDiperolehi = Math.floor(markah / 10);
    const koinDiperolehi = Math.floor(markah / 100);
    const ketepatan = Math.min(100, Math.floor((tikusDitumpaskan / ((30 - masaTinggal) * 2)) * 100)) || 75;
    
    const ganjaran = [
      {
        type: 'xp',
        name: 'Mata Pengalaman',
        description: 'Pengalaman asas bermain',
        amount: xpDiperolehi,
        icon: '⭐'
      }
    ];
    
    if (koinDiperolehi > 0) {
      ganjaran.push({
        type: 'coins',
        name: 'Koin',
        description: 'Mata wang dalam permainan',
        amount: koinDiperolehi,
        icon: '🪙'
      });
    }
    
    if (komboTertinggi >= 5) {
      ganjaran.push({
        type: 'achievement',
        name: 'Raja Kombo',
        description: 'Mencapai kombo x5 atau lebih',
        badge: 'combo',
        icon: '🔥'
      });
    }
    
    if (tikusDitumpaskan >= 20) {
      ganjaran.push({
        type: 'achievement',
        name: 'Pemburu Tikus',
        description: 'Menumpaskan 20 tikus atau lebih',
        badge: 'hunter',
        icon: '🎯'
      });
    }
    
    if (markah >= 500) {
      ganjaran.push({
        type: 'achievement',
        name: 'Tukul Master',
        description: 'Mencapai 500 mata',
        badge: 'master',
        icon: '🏆'
      });
    }
    
    setRingkasanData({
      score: markah,
      time_taken: masaDiambil,
      rank: 1,
      total_players: 1,
      accuracy: ketepatan,
      rewards: ganjaran,
      game_title: 'Tumbuk Tikus',
      game_id: 2,
      user_name: 'Pemain',
      xp_earned: xpDiperolehi,
      coins_earned: koinDiperolehi
    });
  };

  // ========== NEW: Load leaderboard data ==========
  const muatLeaderboard = async () => {
    try {
      const gameId = 2;
      console.log('Memuat leaderboard untuk permainan:', gameId);
      
      // ✅ CORRECT ENDPOINT
      const response = await fetch(`/api/leaderboard/${gameId}`, {
        credentials: 'include',
        headers: {
          'Accept': 'application/json'
        }
      });
      
      console.log('Status respons leaderboard:', response.status);
      
      if (!response.ok) {
        throw new Error(`HTTP ${response.status}`);
      }
      
      const data = await response.json();
      console.log('Data leaderboard diterima:', data);
      
      if (data.success) {
        setLeaderboardData(data);
      }
    } catch (error) {
      console.error('Ralat memuat leaderboard:', error);
      // Create mock leaderboard for testing
      setLeaderboardData({
        success: true,
        leaderboard: [
          { rank: 1, user_name: 'Ali', score: 850, time_taken: 30, is_current_user: false },
          { rank: 2, user_name: 'Siti', score: 720, time_taken: 30, is_current_user: false },
          { rank: 3, user_name: 'Ahmad', score: 680, time_taken: 30, is_current_user: false },
          { rank: 4, user_name: 'Pemain', score: markah, time_taken: 30, is_current_user: true },
          { rank: 5, user_name: 'Muthu', score: 550, time_taken: 30, is_current_user: false }
        ],
        user_rank: 4,
        user_score: markah,
        user_time: 30,
        total_players: 5,
        game_id: 2,
        game_title: 'Tumbuk Tikus'
      });
    }
  };

  // ========== NEW: Collect rewards via Laravel API ==========
  const kumpulGanjaran = async () => {
    try {
      const response = await fetch('/api/rewards/collect', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': dapatkanTokenCsrf()
        },
        credentials: 'include',
        body: JSON.stringify({
          game_id: 2,
          score: markah,
          score_id: ringkasanData?.score_id || Date.now()
        })
      });
      
      if (!response.ok) {
        throw new Error(`HTTP ${response.status}`);
      }
      
      const data = await response.json();
      console.log('Respons pengumpulan ganjaran:', data);
      
      if (data.success) {
        alert('🎉 Ganjaran berjaya dikumpul!');
        if (ringkasanData) {
          setRingkasanData(prev => ({
            ...prev,
            rewards: []
          }));
        }
      } else {
        alert('Tiada ganjaran untuk dikumpul.');
      }
    } catch (error) {
      console.error('Ralat mengumpul ganjaran:', error);
      alert('Gagal mengumpul ganjaran. Ganjaran anda masih selamat.');
    }
  };

  // Submit to leaderboard
  const hantarKeLeaderboard = async (markahAkhir) => {
    const dataPengguna = localStorage.getItem('user');
    let pengguna = null;
    
    try {
      if (dataPengguna) {
        pengguna = JSON.parse(dataPengguna);
      }
    } catch (e) {
      console.warn('Gagal memproses data pengguna');
    }
    
    if (!pengguna || !pengguna.id) {
      console.warn('Pengguna tidak disahkan — melangkau leaderboard');
      return;
    }

    try {
      const masaDiambil = permulaanMasaRef.current ? Math.floor((Date.now() - permulaanMasaRef.current) / 1000) : 0;
      
      const response = await fetch('/api/leaderboard', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': dapatkanTokenCsrf()
        },
        body: JSON.stringify({
          user_id: pengguna.id,
          username: pengguna.name || 'Tanpa Nama',
          class: pengguna.class || 'Tidak Diketahui',
          game_id: 'game2',
          score: markahAkhir,
          time_taken: masaDiambil,
          kombo_tertinggi: komboTertinggi,
          tikus_ditumpaskan: tikusDitumpaskan
        })
      });

      if (!response.ok) {
        const err = await response.json().catch(() => ({}));
        throw new Error(err.error || `HTTP ${response.status}`);
      }

      console.log('Markah dihantar ke leaderboard');
    } catch (error) {
      console.error('Penghantaran ke leaderboard gagal:', error.message);
    }
  };

  const mulakanPenjejakanPermainan = async (idPermainan) => {
    try {
      const response = await progressService.startGame(idPermainan);
      setKemajuanPermainan(response.data.progress);
    } catch (error) {
      console.error('Gagal memulakan penjejakan permainan:', error);
    }
  };

  const ketukTikus = (indeks) => {
    if (permainanTamcat || !tikusMuncul.includes(indeks)) return;
    
    let mata = 10 * pendarab;
    if (kuasaAktif && pendarab > 1) {
      mata *= 1.5;
    }
    
    setMarkah(prev => prev + mata);
    setTikusDitumpaskan(prev => prev + 1);
    
    const komboBaru = kombo + 1;
    setKombo(komboBaru);
    if (komboBaru > komboTertinggi) {
      setKomboTertinggi(komboBaru);
    }
    
    setKeadaanTikus(prev => ({
      ...prev,
      [indeks]: 'diketuk'
    }));
    
    setTikusMuncul(prev => prev.filter(lubang => lubang !== indeks));
    
    setTimeout(() => {
      setKeadaanTikus(prev => {
        const keadaanBaru = { ...prev };
        delete keadaanBaru[indeks];
        return keadaanBaru;
      });
    }, 300);
  };

  // ========== UPDATED: Game over effect ==========
  useEffect(() => {
    if (permainanTamcat) {
      console.log('Permainan tamat! Memulakan urutan selepas permainan...');
      
      // Save score and load summary
      const urutanSelepasPermainan = async () => {
        await simpanMarkahKeDatabase(markah, 'selesai');
        await hantarKeLeaderboard(markah);
        await simpanKemajuanPermainan();
        await muatRingkasanPermainan();
        await muatLeaderboard();
        
        // Show summary after a short delay
        setTimeout(() => {
          setTunjukRingkasan(true);
        }, 800);
      };
      
      urutanSelepasPermainan();
    }
  }, [permainanTamcat]);

  const simpanKemajuanPermainan = async () => {
    try {
      const dataKemajuan = {
        score: markah,
        level: 1,
        time_spent: 30 - masaTinggal,
        completed: true,
        progress_data: {
          moles_whacked: markah / 10,
          max_combo: komboTertinggi,
          accuracy_percentage: ((markah / 10) / 30) * 100
        }
      };

      const response = await progressService.saveProgress(2, dataKemajuan);
      setKemajuanPermainan(response.data.progress);
      
      if (response.data.rewards_unlocked && response.data.rewards_unlocked.length > 0) {
        setGanjaranDibuka(response.data.rewards_unlocked);
      }
    } catch (error) {
      console.error('Gagal menyimpan kemajuan:', error);
    }
  };

  const kendalikanKetukan = (e, indeks) => {
    if (permainanTamcat) return;
    
    const rect = papanRef.current.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    
    setPosisiTukul({ x, y });
    setTunjukTukul(true);
    
    setTimeout(() => {
      setTunjukTukul(false);
    }, 300);
    
    ketukTikus(indeks);
  };

  const kumpulKuasa = () => {
    if (!kuasa) return;
    
    setKuasaAktif(true);
    setPengiraMasaKuasa(10);
    
    if (kuasa === 'masa') {
      setMasaTinggal(prev => prev + 5);
      setMasaTambahan(5);
      setTimeout(() => setMasaTambahan(0), 2000);
    } else if (kuasa === 'mata') {
      setPendarab(2);
    } else if (kuasa === 'kombo') {
      setKombo(prev => prev + 5);
    }
    
    setKuasa(null);
  };

  const setSemulaPermainan = () => {
    setMarkah(0);
    setMasaTinggal(30);
    setLubang(Array(9).fill(false));
    setPermainanTamcat(false);
    setTikusMuncul([]);
    setKelajuan(1200);
    setKeadaanTikus({});
    setKombo(0);
    setKomboTertinggi(0);
    setKuasa(null);
    setKuasaAktif(false);
    setPengiraMasaKuasa(0);
    setPendarab(1);
    setMasaTambahan(0);
    setTikusDitumpaskan(0);
    setTunjukRingkasan(false);
    setGanjaranDibuka([]);
    setTunjukLeaderboard(false);
    setRingkasanData(null);
    setLeaderboardData(null);
    permulaanMasaRef.current = Date.now();
  };

  const mulakanPermainan = () => {
    setPermainanDimulakan(true);
    setSemulaPermainan();
  };

  const kembaliKeLamanUtama = () => {
    setPermainanDimulakan(false);
    setPermainanTamcat(false);
  };

  // ========== NEW: Custom Game Summary Modal ==========
  const ModalRingkasanPermainan = () => {
    if (!ringkasanData || !tunjukRingkasan) return null;

    return (
      <div style={{
        position: 'fixed',
        top: '50%',
        left: '50%',
        transform: 'translate(-50%, -50%)',
        backgroundColor: '#0f3460',
        padding: '30px',
        borderRadius: '16px',
        border: '3px solid #4ecca3',
        maxWidth: '600px',
        width: '95%',
        zIndex: 2000,
        boxShadow: '0 0 30px rgba(78, 204, 163, 0.7)',
        color: 'white'
      }}>
        <h2 style={{ 
          fontSize: '2.2rem', 
          color: '#4ecca3',
          textAlign: 'center',
          marginBottom: '25px',
          textShadow: '0 0 8px rgba(78, 204, 163, 0.8)'
        }}>🎯 Permainan Tamat!</h2>
        
        {sedangMemuatRingkasan ? (
          <div style={{ textAlign: 'center', padding: '40px' }}>
            <div style={{
              width: '50px',
              height: '50px',
              border: '4px solid #4ecca3',
              borderTop: '4px solid transparent',
              borderRadius: '50%',
              animation: 'spin 1s linear infinite',
              margin: '0 auto'
            }} />
            <p style={{ fontSize: '1.2rem', marginTop: '20px' }}>Memuatkan ringkasan permainan...</p>
          </div>
        ) : (
          <>
            {/* Score Display */}
            <div style={{ 
              display: 'flex', 
              justifyContent: 'center', 
              alignItems: 'center',
              marginBottom: '25px'
            }}>
              <div style={{
                width: '180px',
                height: '180px',
                borderRadius: '50%',
                background: 'white',
                color: '#333',
                display: 'flex',
                flexDirection: 'column',
                justifyContent: 'center',
                alignItems: 'center',
                boxShadow: '0 15px 35px rgba(0,0,0,0.3)'
              }}>
                <span style={{ 
                  fontSize: '64px', 
                  fontWeight: 'bold',
                  color: '#4a5568'
                }}>
                  {ringkasanData.score}
                </span>
                <span style={{ 
                  fontSize: '16px', 
                  color: '#718096',
                  textTransform: 'uppercase',
                  letterSpacing: '1px'
                }}>
                  MATA
                </span>
              </div>
            </div>
            
            {/* Score Details */}
            <div style={{
              display: 'grid',
              gridTemplateColumns: 'repeat(2, 1fr)',
              gap: '15px',
              margin: '25px 0'
            }}>
              <div style={{
                textAlign: 'center',
                padding: '15px',
                background: 'rgba(255,255,255,0.1)',
                borderRadius: '10px',
                backdropFilter: 'blur(10px)'
              }}>
                <div style={{ fontSize: '14px', opacity: '0.9', marginBottom: '5px' }}>
                  Kedudukan
                </div>
                <div style={{ fontSize: '24px', fontWeight: 'bold', color: '#FFD700' }}>
                  #{ringkasanData.rank}
                </div>
              </div>
              
              <div style={{
                textAlign: 'center',
                padding: '15px',
                background: 'rgba(255,255,255,0.1)',
                borderRadius: '10px',
                backdropFilter: 'blur(10px)'
              }}>
                <div style={{ fontSize: '14px', opacity: '0.9', marginBottom: '5px' }}>
                  Kombo Tertinggi
                </div>
                <div style={{ fontSize: '24px', fontWeight: 'bold', color: '#4ecca3' }}>
                  x{komboTertinggi}
                </div>
              </div>
              
              <div style={{
                textAlign: 'center',
                padding: '15px',
                background: 'rgba(255,255,255,0.1)',
                borderRadius: '10px',
                backdropFilter: 'blur(10px)'
              }}>
                <div style={{ fontSize: '14px', opacity: '0.9', marginBottom: '5px' }}>
                  Ketepatan
                </div>
                <div style={{ fontSize: '24px', fontWeight: 'bold', color: '#2196F3' }}>
                  {ringkasanData.accuracy}%
                </div>
              </div>
              
              <div style={{
                textAlign: 'center',
                padding: '15px',
                background: 'rgba(255,255,255,0.1)',
                borderRadius: '10px',
                backdropFilter: 'blur(10px)'
              }}>
                <div style={{ fontSize: '14px', opacity: '0.9', marginBottom: '5px' }}>
                  Tikus Ditumpaskan
                </div>
                <div style={{ fontSize: '24px', fontWeight: 'bold', color: '#9C27B0' }}>
                  {tikusDitumpaskan}
                </div>
              </div>
            </div>
            
            {/* Rewards Section */}
            {ringkasanData.rewards && ringkasanData.rewards.length > 0 && (
              <div style={{ marginTop: '25px' }}>
                <h3 style={{ 
                  fontSize: '1.5rem', 
                  color: '#FFD700',
                  textAlign: 'center',
                  marginBottom: '15px'
                }}>
                  🎁 Ganjaran Diperolehi!
                </h3>
                
                <div style={{
                  display: 'grid',
                  gridTemplateColumns: 'repeat(auto-fit, minmax(250px, 1fr))',
                  gap: '15px',
                  marginBottom: '20px'
                }}>
                  {ringkasanData.rewards.map((ganjaran, index) => (
                    <div 
                      key={index}
                      style={{
                        background: 'rgba(255,255,255,0.15)',
                        borderRadius: '12px',
                        padding: '15px',
                        display: 'flex',
                        alignItems: 'center',
                        gap: '15px',
                        transition: 'transform 0.3s'
                      }}
                    >
                      <div style={{ fontSize: '30px' }}>{ganjaran.icon}</div>
                      <div style={{ textAlign: 'left', flex: 1 }}>
                        <h4 style={{ margin: '0 0 5px 0', fontSize: '16px' }}>{ganjaran.name}</h4>
                        <p style={{ margin: '0 0 8px 0', fontSize: '14px', opacity: '0.9' }}>
                          {ganjaran.description}
                        </p>
                        {ganjaran.amount && (
                          <span style={{ color: '#FFD700', fontWeight: 'bold' }}>
                            +{ganjaran.amount} {ganjaran.type === 'xp' ? 'XP' : ganjaran.type === 'coins' ? 'Koin' : ''}
                          </span>
                        )}
                      </div>
                    </div>
                  ))}
                </div>
                
                {ringkasanData.rewards.length > 0 && (
                  <button 
                    onClick={kumpulGanjaran}
                    style={{
                      width: '100%',
                      padding: '15px',
                      background: 'linear-gradient(to right, #FF9800, #F57C00)',
                      color: 'white',
                      border: 'none',
                      borderRadius: '10px',
                      cursor: 'pointer',
                      fontWeight: 'bold',
                      fontSize: '1.1rem',
                      marginTop: '10px',
                      transition: 'all 0.3s'
                    }}
                  >
                    Kumpul Semua Ganjaran
                  </button>
                )}
              </div>
            )}
            
            {/* XP & Coins Earned */}
            {(ringkasanData.xp_earned > 0 || ringkasanData.coins_earned > 0) && (
              <div style={{
                marginTop: '20px',
                padding: '15px',
                background: 'rgba(78, 204, 163, 0.1)',
                borderRadius: '10px',
                border: '1px solid rgba(78, 204, 163, 0.3)'
              }}>
                <div style={{ display: 'flex', justifyContent: 'space-around' }}>
                  {ringkasanData.xp_earned > 0 && (
                    <div style={{ textAlign: 'center' }}>
                      <div style={{ fontSize: '24px', color: '#4ecca3' }}>+{ringkasanData.xp_earned}</div>
                      <div style={{ fontSize: '14px', color: '#aaa' }}>XP</div>
                    </div>
                  )}
                  {ringkasanData.coins_earned > 0 && (
                    <div style={{ textAlign: 'center' }}>
                      <div style={{ fontSize: '24px', color: '#FFD700' }}>+{ringkasanData.coins_earned}</div>
                      <div style={{ fontSize: '14px', color: '#aaa' }}>Koin</div>
                    </div>
                  )}
                </div>
              </div>
            )}
            
            {/* Action Buttons */}
            <div style={{ 
              display: 'flex', 
              flexDirection: 'column', 
              gap: '12px', 
              marginTop: '30px' 
            }}>
              <button 
                style={{
                  padding: '15px 25px',
                  background: 'linear-gradient(to right, #4CAF50, #45a049)',
                  color: 'white',
                  border: 'none',
                  borderRadius: '10px',
                  cursor: 'pointer',
                  fontWeight: 'bold',
                  fontSize: '1.1rem',
                  transition: 'all 0.3s'
                }}
                onClick={() => {
                  setSemulaPermainan();
                  setTunjukRingkasan(false);
                }}
              >
                🔄 Main Semula
              </button>
              
              <button 
                style={{
                  padding: '15px 25px',
                  background: 'linear-gradient(to right, #2196F3, #1976D2)',
                  color: 'white',
                  border: 'none',
                  borderRadius: '10px',
                  cursor: 'pointer',
                  fontWeight: 'bold',
                  fontSize: '1.1rem',
                  transition: 'all 0.3s'
                }}
                onClick={() => {
                  setTunjukRingkasan(false);
                  setTunjukLeaderboard(true);
                }}
              >
                🏆 Papan Pemimpin
              </button>
              
              <button 
                style={{
                  padding: '15px 25px',
                  background: 'linear-gradient(to right, #9C27B0, #7B1FA2)',
                  color: 'white',
                  border: 'none',
                  borderRadius: '10px',
                  cursor: 'pointer',
                  fontWeight: 'bold',
                  fontSize: '1.1rem',
                  transition: 'all 0.3s'
                }}
                onClick={kembaliKeLamanUtama}
              >
                ← Kembali ke Menu
              </button>
            </div>
          </>
        )}
      </div>
    );
  };

  // ========== NEW: Custom Leaderboard Modal ==========
  const ModalLeaderboard = () => {
    if (!tunjukLeaderboard) return null;

    return (
      <div style={{
        position: 'fixed',
        top: 0,
        left: 0,
        width: '100%',
        height: '100%',
        backgroundColor: 'rgba(0, 0, 0, 0.95)',
        display: 'flex',
        justifyContent: 'center',
        alignItems: 'center',
        zIndex: 2000,
        padding: '20px'
      }}>
        <div style={{ 
          width: '95%', 
          maxWidth: '800px',
          maxHeight: '85vh',
          overflowY: 'auto',
          borderRadius: '16px',
          backgroundColor: '#0f3460',
          border: '3px solid #4ecca3',
          padding: '25px',
          color: 'white'
        }}>
          <div style={{ 
            display: 'flex', 
            justifyContent: 'space-between', 
            alignItems: 'center',
            marginBottom: '20px'
          }}>
            <h2 style={{ 
              fontSize: '2rem', 
              color: '#4ecca3',
              margin: 0
            }}>
              🏆 Papan Pemimpin Tumbuk Tikus
            </h2>
            <button 
              onClick={() => setTunjukLeaderboard(false)}
              style={{
                padding: '8px 16px',
                backgroundColor: '#f44336',
                color: 'white',
                border: 'none',
                borderRadius: '6px',
                cursor: 'pointer',
                fontWeight: 'bold'
              }}
            >
              ✕ Tutup
            </button>
          </div>
          
          {leaderboardData ? (
            <>
              {leaderboardData.user_rank && leaderboardData.user_rank > 10 && (
                <div style={{
                  background: '#e3f2fd',
                  padding: '15px',
                  borderRadius: '10px',
                  marginBottom: '20px',
                  textAlign: 'center',
                  color: '#1976D2'
                }}>
                  <h3 style={{ margin: '0 0 5px 0' }}>Kedudukan Anda: #{leaderboardData.user_rank}</h3>
                  <p style={{ margin: '0', fontSize: '0.9rem' }}>
                    Skor: {leaderboardData.user_score} mata
                  </p>
                </div>
              )}
              
              <div style={{ overflowX: 'auto' }}>
                <table style={{
                  width: '100%',
                  borderCollapse: 'collapse',
                  backgroundColor: 'rgba(255,255,255,0.05)',
                  borderRadius: '8px',
                  overflow: 'hidden'
                }}>
                  <thead>
                    <tr style={{ background: 'rgba(78, 204, 163, 0.2)' }}>
                      <th style={{ padding: '15px', textAlign: 'left', borderBottom: '2px solid #4ecca3' }}>
                        Kedudukan
                      </th>
                      <th style={{ padding: '15px', textAlign: 'left', borderBottom: '2px solid #4ecca3' }}>
                        Nama
                      </th>
                      <th style={{ padding: '15px', textAlign: 'left', borderBottom: '2px solid #4ecca3' }}>
                        Skor
                      </th>
                      <th style={{ padding: '15px', textAlign: 'left', borderBottom: '2px solid #4ecca3' }}>
                        Masa
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    {leaderboardData.leaderboard && leaderboardData.leaderboard.length > 0 ? (
                      leaderboardData.leaderboard.map((entry, index) => (
                        <tr 
                          key={index}
                          style={{
                            background: entry.is_current_user ? 'rgba(255, 215, 0, 0.15)' : 'transparent',
                            borderBottom: '1px solid rgba(255,255,255,0.1)'
                          }}
                        >
                          <td style={{ padding: '15px' }}>
                            <strong style={{ 
                              color: entry.rank <= 3 ? 
                                ['#FFD700', '#C0C0C0', '#CD7F32'][entry.rank-1] : '#666' 
                            }}>
                              {entry.rank <= 3 ? ['🥇', '🥈', '🥉'][entry.rank-1] : ''} #{entry.rank}
                            </strong>
                          </td>
                          <td style={{ padding: '15px' }}>
                            {entry.user_name} {entry.is_current_user ? ' (Anda)' : ''}
                          </td>
                          <td style={{ padding: '15px', fontWeight: 'bold' }}>
                            {entry.score}
                          </td>
                          <td style={{ padding: '15px' }}>
                            {entry.time_taken}s
                          </td>
                        </tr>
                      ))
                    ) : (
                      <tr>
                        <td colSpan="4" style={{ padding: '30px', textAlign: 'center', color: '#aaa' }}>
                          Tiada data papan pemimpin untuk permainan ini.
                        </td>
                      </tr>
                    )}
                  </tbody>
                </table>
              </div>
              
              <div style={{ marginTop: '20px', textAlign: 'center', color: '#aaa', fontSize: '0.9rem' }}>
                Jumlah Pemain: {leaderboardData.total_players || 0}
              </div>
            </>
          ) : (
            <div style={{ textAlign: 'center', padding: '40px' }}>
              <div style={{
                width: '40px',
                height: '40px',
                border: '3px solid #4ecca3',
                borderTop: '3px solid transparent',
                borderRadius: '50%',
                animation: 'spin 1s linear infinite',
                margin: '0 auto 20px'
              }} />
              <p>Memuatkan data papan pemimpin...</p>
            </div>
          )}
        </div>
      </div>
    );
  };

  const peratusanMasa = (masaTinggal / 30) * 100;
  const warnaMasa = masaTinggal > 15 ? '#4CAF50' : masaTinggal > 5 ? '#FFC107' : '#F44336';

  return (
    <div style={{ 
      fontFamily: 'Arial, sans-serif', 
      textAlign: 'center', 
      marginTop: '20px',
      backgroundColor: '#1a1a2e',
      color: '#fff',
      minHeight: '100vh',
      padding: '20px'
    }}>
      {!permainanDimulakan ? (
        <div style={{
          maxWidth: '600px',
          margin: '0 auto',
          padding: '30px',
          backgroundColor: '#0f3460',
          borderRadius: '15px',
          border: '2px solid #4ecca3',
          boxShadow: '0 0 20px rgba(78, 204, 163, 0.5)'
        }}>
          <h2 style={{ 
            fontSize: '2.5rem', 
            color: '#4ecca3',
            textShadow: '0 0 10px rgba(78, 204, 163, 0.7)',
            marginBottom: '20px'
          }}>🎯 Tumbuk Tikus!</h2>
          
          <div style={{ 
            fontSize: '1.2rem', 
            marginBottom: '25px',
            lineHeight: '1.6',
            color: '#f1f1f1'
          }}>
            <p>Tumbuk tikus yang muncul secepat mungkin!</p>
            <p>Cuba dapatkan markah tertinggi dalam masa 30 saat.</p>
          </div>
          
          <div style={{ 
            marginBottom: '25px',
            padding: '15px',
            backgroundColor: '#16213e',
            borderRadius: '10px'
          }}>
            <h4 style={{ color: '#4ecca3', marginBottom: '10px' }}>Cara Bermain:</h4>
            <div style={{ textAlign: 'left', display: 'inline-block', width: '100%' }}>
              <p>• Klik pada tikus yang muncul untuk menumbuknya</p>
              <p>• Cuba tumbuk sebanyak mungkin dalam masa 30 saat</p>
              <p>• Kuasa bonus akan muncul secara rawak - klik untuk manfaat</p>
              <p>• Permainan semakin sukar apabila markah meningkat</p>
            </div>
          </div>
          
          <button 
            style={{
              padding: '15px 40px',
              fontSize: '1.2rem',
              backgroundColor: '#4CAF50',
              color: '#fff',
              border: 'none',
              borderRadius: '8px',
              cursor: 'pointer',
              fontWeight: 'bold',
              transition: 'all 0.3s',
              boxShadow: '0 0 10px rgba(76, 175, 80, 0.5)'
            }}
            onClick={mulakanPermainan}
          >
            ▶ Mulakan Permainan
          </button>
        </div>
      ) : (
        <>
          <h2 style={{ 
            fontSize: '2.5rem', 
            color: '#4ecca3',
            textShadow: '0 0 10px rgba(78, 204, 163, 0.7)',
            marginBottom: '20px'
          }}>🎯 Tumbuk Tikus!</h2>
          
          <div style={{
            display: 'flex',
            justifyContent: 'space-around',
            flexWrap: 'wrap',
            marginBottom: '20px',
            padding: '10px',
            backgroundColor: '#16213e',
            borderRadius: '10px',
            border: '2px solid #4ecca3'
          }}>
            <div style={{ margin: '5px' }}>
              <span style={{ color: '#4ecca3', fontWeight: 'bold' }}>Markah:</span>
              <span style={{ marginLeft: '10px', fontSize: '1.2rem' }}>{markah}</span>
            </div>
            
            <div style={{ margin: '5px' }}>
              <span style={{ color: '#4ecca3', fontWeight: 'bold' }}>Masa:</span>
              <div style={{ 
                width: '200px', 
                height: '20px', 
                background: 'rgba(0, 0, 0, 0.2)', 
                borderRadius: '10px', 
                margin: '5px 0',
                overflow: 'hidden'
              }}>
                <div 
                  style={{ 
                    height: '100%', 
                    width: `${peratusanMasa}%`, 
                    backgroundColor: warnaMasa,
                    transition: 'width 0.5s ease'
                  }}
                ></div>
              </div>
              <div style={{ fontSize: '1.2rem', fontWeight: 'bold' }}>{masaTinggal}s</div>
            </div>
            
            <div style={{ margin: '5px' }}>
              <span style={{ color: '#4ecca3', fontWeight: 'bold' }}>Tikus:</span>
              <span style={{ marginLeft: '10px', fontSize: '1.2rem' }}>{tikusDitumpaskan}</span>
            </div>
            
            {masaTambahan > 0 && (
              <div style={{
                position: 'absolute',
                top: '-20px',
                right: '10px',
                background: '#FFD700',
                color: '#000',
                padding: '5px 10px',
                borderRadius: '20px',
                fontWeight: 'bold',
                animation: 'pulse 0.5s'
              }}>
                +{masaTambahan}s!
              </div>
            )}
          </div>
          
          <div style={{
            margin: '15px 0',
            height: '50px',
            display: 'flex',
            justifyContent: 'center',
            alignItems: 'center'
          }}>
            {kombo > 1 && (
              <div style={{
                fontSize: '1.8rem',
                color: '#FFD700',
                fontWeight: 'bold',
                textShadow: '0 0 10px rgba(255, 215, 0, 0.7)',
                animation: 'pulse 0.5s',
                margin: '0 10px'
              }}>
                KOMBO x{kombo}!
              </div>
            )}
            {kuasaAktif && (
              <div style={{
                fontSize: '1.4rem',
                color: '#FF416C',
                fontWeight: 'bold',
                textShadow: '0 0 10px rgba(255, 65, 108, 0.7)',
                animation: 'pulse 0.5s',
                margin: '0 10px'
              }}>
                {pendarab > 1 ? `x${pendarab} MATA!` : 'MASA TAMBAHAN!'}
              </div>
            )}
          </div>
          
          {kuasa && (
            <div 
              style={{
                position: 'absolute',
                top: '20px',
                right: '20px',
                background: 'rgba(255, 255, 255, 0.9)',
                borderRadius: '50px',
                padding: '10px 20px',
                display: 'flex',
                alignItems: 'center',
                gap: '10px',
                cursor: 'pointer',
                zIndex: 10,
                boxShadow: '0 4px 15px rgba(0, 0, 0, 0.2)',
                animation: 'bounce 1s infinite'
              }}
              onClick={kumpulKuasa}
            >
              <div style={{ fontSize: '1.5rem' }}>
                {kuasa === 'masa' && '⏱️'}
                {kuasa === 'mata' && '💰'}
                {kuasa === 'kombo' && '🔥'}
              </div>
              <div style={{ fontWeight: 'bold', color: '#333' }}>
                {kuasa === 'masa' && 'MASA TAMBAHAN'}
                {kuasa === 'mata' && 'PENDARAB MARKAH'}
                {kuasa === 'kombo' && 'BOOST KOMBO'}
              </div>
            </div>
          )}
          
          <div style={{
            backgroundColor: '#0f3460',
            borderRadius: '10px',
            padding: '20px',
            border: '2px solid #4ecca3',
            maxWidth: '600px',
            margin: '0 auto',
            position: 'relative',
            overflow: 'hidden'
          }} ref={papanRef}>
            <div style={{ 
              color: '#f1f1f1', 
              marginBottom: '15px',
              fontSize: '1.1rem'
            }}>
              Tumbuk tikus secepat mungkin!
            </div>
            
            <div style={{
              display: 'grid',
              gridTemplateColumns: 'repeat(3, 1fr)',
              gap: '15px',
              width: '100%',
              margin: '20px 0'
            }}>
              {lubang.map((adaTikus, indeks) => (
                <div 
                  key={indeks}
                  style={{
                    aspectRatio: '1',
                    backgroundColor: tikusMuncul.includes(indeks) ? '#5D2906' : '#8B4513',
                    borderRadius: '50%',
                    position: 'relative',
                    overflow: 'hidden',
                    cursor: 'pointer',
                    boxShadow: '0 8px 15px rgba(0, 0, 0, 0.3)',
                    transition: 'transform 0.2s',
                    display: 'flex',
                    justifyContent: 'center',
                    alignItems: 'flex-end'
                  }}
                  onClick={(e) => kendalikanKetukan(e, indeks)}
                >
                  {tikusMuncul.includes(indeks) && (
                    <div style={{
                      position: 'absolute',
                      bottom: keadaanTikus[indeks] === 'diketuk' ? '0%' : '0%',
                      left: '50%',
                      transform: 'translateX(-50%)',
                      width: '80%',
                      height: '80%',
                      backgroundColor: '#A9A9A9',
                      borderRadius: '50% 50% 40% 40%',
                      transition: 'bottom 0.3s ease',
                      animation: keadaanTikus[indeks] === 'diketuk' ? 'whack 0.3s forwards' : 'none'
                    }}>
                      <div style={{
                        position: 'absolute',
                        top: '30%',
                        left: '50%',
                        transform: 'translateX(-50%)',
                        width: '60%',
                        textAlign: 'center'
                      }}>
                        <div style={{
                          display: 'flex',
                          justifyContent: 'space-around',
                          marginBottom: '10px'
                        }}>
                          <div style={{
                            width: '12px',
                            height: '12px',
                            backgroundColor: 'black',
                            borderRadius: '50%'
                          }}></div>
                          <div style={{
                            width: '12px',
                            height: '12px',
                            backgroundColor: 'black',
                            borderRadius: '50%'
                          }}></div>
                        </div>
                        <div style={{
                          width: '20px',
                          height: '12px',
                          backgroundColor: '#8B0000',
                          margin: '0 auto 10px',
                          borderRadius: '50%'
                        }}></div>
                        <div style={{
                          display: 'flex',
                          justifyContent: 'center'
                        }}>
                          <div style={{
                            width: '8px',
                            height: '2px',
                            backgroundColor: 'black',
                            margin: '0 2px'
                          }}></div>
                          <div style={{
                            width: '8px',
                            height: '2px',
                            backgroundColor: 'black',
                            margin: '0 2px'
                          }}></div>
                          <div style={{
                            width: '8px',
                            height: '2px',
                            backgroundColor: 'black',
                            margin: '0 2px'
                          }}></div>
                        </div>
                      </div>
                    </div>
                  )}
                </div>
              ))}
              
              {tunjukTukul && (
                <div 
                  style={{
                    position: 'absolute',
                    zIndex: 20,
                    pointerEvents: 'none',
                    left: `${posisiTukul.x}px`,
                    top: `${posisiTukul.y}px`,
                    transform: 'translate(-50%, -50%)',
                    animation: 'whackHammer 0.3s ease-out'
                  }}
                >
                  <div style={{
                    width: '12px',
                    height: '80px',
                    background: 'linear-gradient(to right, #8B4513, #A0522D, #8B4513)',
                    borderRadius: '6px',
                    position: 'absolute',
                    top: '-40px',
                    left: '50%',
                    transform: 'translateX(-50%)',
                    boxShadow: '0 2px 5px rgba(0, 0, 0, 0.3)'
                  }}></div>
                  <div style={{
                    width: '60px',
                    height: '40px',
                    background: 'linear-gradient(135deg, #555, #333)',
                    borderRadius: '8px',
                    position: 'absolute',
                    top: '-80px',
                    left: '50%',
                    transform: 'translateX(-50%)',
                    boxShadow: '0 2px 8px rgba(0, 0, 0, 0.4)',
                    display: 'flex',
                    justifyContent: 'center',
                    alignItems: 'center'
                  }}>
                    <div style={{
                      width: '40px',
                      height: '20px',
                      background: '#FFD700',
                      borderRadius: '4px'
                    }}></div>
                  </div>
                </div>
              )}
            </div>

            {permainanTamcat && !tunjukRingkasan && (
              <div style={{
                position: 'fixed',
                top: 0,
                left: 0,
                width: '100%',
                height: '100%',
                backgroundColor: 'rgba(0, 0, 0, 0.9)',
                display: 'flex',
                justifyContent: 'center',
                alignItems: 'center',
                zIndex: 100
              }}>
                <div style={{
                  backgroundColor: '#0f3460',
                  padding: '30px',
                  borderRadius: '12px',
                  textAlign: 'center',
                  border: '2px solid #4ecca3',
                  maxWidth: '500px',
                  width: '90%',
                  boxShadow: '0 0 25px rgba(78, 204, 163, 0.7)'
                }}>
                  <h3 style={{ color: '#ff5252', fontSize: '2.2rem', marginBottom: '20px', textShadow: '0 0 6px rgba(255, 82, 82, 0.8)' }}>🛑 Permainan Tamat!</h3>
                  <p style={{ fontSize: '1.3rem', marginBottom: '12px', color: '#FFD700' }}>Markah Akhir: <strong style={{ color: '#4ecca3', fontSize: '1.4rem' }}>{markah}</strong></p>
                  <p style={{ fontSize: '1.2rem', marginBottom: '12px' }}>Kombo Tertinggi: <strong>x{komboTertinggi}</strong></p>
                  <p style={{ fontSize: '1.2rem', marginBottom: '12px' }}>Tikus Ditumpaskan: <strong>{tikusDitumpaskan}</strong></p>
                  <p style={{ fontSize: '1.2rem', marginBottom: '20px' }}>Markah Tertinggi: <strong>{markahTertinggi}</strong></p>
                  
                  {sedangMemuatRingkasan && (
                    <div style={{ margin: '20px 0' }}>
                      <div style={{
                        width: '40px',
                        height: '40px',
                        border: '3px solid #4ecca3',
                        borderTop: '3px solid transparent',
                        borderRadius: '50%',
                        animation: 'spin 1s linear infinite',
                        margin: '0 auto'
                      }} />
                    </div>
                  )}
                </div>
              </div>
            )}

            {/* NEW: Custom Game Summary Modal */}
            <ModalRingkasanPermainan />
            
            {/* NEW: Custom Leaderboard Modal */}
            <ModalLeaderboard />

            {!permainanTamcat && (
              <div style={{ display: 'flex', justifyContent: 'center', gap: '15px', marginTop: '20px' }}>
                <button 
                  style={{
                    padding: '10px 20px',
                    backgroundColor: '#9C27B0',
                    color: '#fff',
                    border: 'none',
                    borderRadius: '5px',
                    cursor: 'pointer',
                    fontWeight: 'bold',
                    fontSize: '1rem',
                    transition: 'all 0.2s'
                  }}
                  onClick={kembaliKeLamanUtama}
                >
                  🏠 Laman Utama
                </button>
              </div>
            )}
          </div>
        </>
      )}

      <style>{`
        @keyframes pulse {
          0% { transform: scale(1); }
          50% { transform: scale(1.2); }
          100% { transform: scale(1); }
        }
        
        @keyframes bounce {
          0%, 100% { transform: translateY(0); }
          50% { transform: translateY(-10px); }
        }
        
        @keyframes whack {
          0% { transform: translateX(-50%) rotate(0deg); }
          25% { transform: translateX(-50%) rotate(10deg); }
          50% { transform: translateX(-50%) rotate(-10deg); }
          75% { transform: translateX(-50%) rotate(5deg); }
          100% { transform: translateX(-50%) rotate(0deg); }
        }
        
        @keyframes whackHammer {
          0% { transform: translate(-50%, -50%) scale(1) rotate(0deg); }
          50% { transform: translate(-50%, -50%) scale(1.2) rotate(-10deg); }
          100% { transform: translate(-50%, -50%) scale(1) rotate(0deg); }
        }
        
        @keyframes spin {
          0% { transform: rotate(0deg); }
          100% { transform: rotate(360deg); }
        }
        
        button:hover {
          transform: scale(1.05);
          box-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
        }
        
        button:disabled {
          opacity: 0.6;
          cursor: not-allowed;
          transform: none;
        }
      `}</style>
    </div>
  );
};

export default WhackAMole;