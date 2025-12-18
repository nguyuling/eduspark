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
  
  const papanRef = useRef(null);
  const permulaanMasaRef = useRef(null);

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

  // Save score to database
  const simpanMarkahKeDatabase = async (markahAkhir, status) => {
    try {
      let idPemain = localStorage.getItem('tukulGamePlayerId');
      if (!idPemain) {
        idPemain = 'player_' + Math.random().toString(36).substr(2, 9);
        localStorage.setItem('tukulGamePlayerId', idPemain);
      }
      
      const gameId = 2; // Whack-a-Mole Game ID
      const masaDiambil = permulaanMasaRef.current ? Math.floor((Date.now() - permulaanMasaRef.current) / 1000) : 0;
      
      const response = await fetch('/api/save-game-score', {
        method: 'POST',
        headers: { 
          'Content-Type': 'application/json',
          'Accept': 'application/json'
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
      
      if (!response.ok) {
        const errorText = await response.text();
        console.error('Gagal menyimpan markah:', errorText);
        return null;
      }
      const result = await response.json();
      console.log('Markah disimpan:', result);
      return result;
    } catch (error) {
      console.error('Ralat menyimpan markah:', error);
      return null;
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
          'Authorization': `Bearer ${pengguna.token || ''}`
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

  useEffect(() => {
    if (permainanTamcat) {
      // Save score and submit to leaderboard
      simpanMarkahKeDatabase(markah, 'selesai');
      hantarKeLeaderboard(markah);
      
      // Save progress
      simpanKemajuanPermainan();
      
      // Show summary after a short delay
      setTimeout(() => {
        setTunjukRingkasan(true);
      }, 500);
    }
  }, [permainanTamcat, markah]);

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

            {permainanTamcat && (
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
                  
                  {tunjukRingkasan && (
                    <>
                      <GameSummary progress={kemajuanPermainan} game={{ name: 'Tumbuk Tikus' }} />
                      
                      {ganjaranDibuka.length > 0 && (
                        <RewardsDisplay 
                          rewards={ganjaranDibuka}
                          onClaim={(ganjaran) => console.log('Ganjaran dituntut:', ganjaran)}
                        />
                      )}
                      
                      <div style={{ display: 'flex', justifyContent: 'center', gap: '15px', marginTop: '25px', flexWrap: 'wrap' }}>
                        <button 
                          style={{
                            padding: '12px 25px',
                            backgroundColor: '#4CAF50',
                            color: '#fff',
                            border: 'none',
                            borderRadius: '8px',
                            cursor: 'pointer',
                            fontWeight: 'bold',
                            fontSize: '1.1rem',
                            transition: 'all 0.2s',
                            boxShadow: '0 2px 6px rgba(76, 175, 80, 0.4)'
                          }}
                          onClick={() => {
                            setSemulaPermainan();
                            setTunjukRingkasan(false);
                            setGanjaranDibuka([]);
                          }}
                        >
                          🔄 Main Semula
                        </button>
                        <button 
                          style={{
                            padding: '12px 25px',
                            backgroundColor: '#FF9800',
                            color: '#fff',
                            border: 'none',
                            borderRadius: '8px',
                            cursor: 'pointer',
                            fontWeight: 'bold',
                            fontSize: '1.1rem',
                            transition: 'all 0.2s',
                            boxShadow: '0 2px 6px rgba(255, 152, 0, 0.4)'
                          }}
                          onClick={() => setTunjukLeaderboard(true)}
                        >
                          📊 Lihat Kedudukan
                        </button>
                        <button 
                          style={{
                            padding: '12px 25px',
                            backgroundColor: '#9C27B0',
                            color: '#fff',
                            border: 'none',
                            borderRadius: '8px',
                            cursor: 'pointer',
                            fontWeight: 'bold',
                            fontSize: '1.1rem',
                            transition: 'all 0.2s',
                            boxShadow: '0 2px 6px rgba(156, 39, 176, 0.4)'
                          }}
                          onClick={kembaliKeLamanUtama}
                        >
                          🏠 Laman Utama
                        </button>
                      </div>
                    </>
                  )}
                </div>
              </div>
            )}

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

      {/* Leaderboard Modal */}
      {tunjukLeaderboard && (
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
            maxWidth: '900px',
            maxHeight: '85vh',
            overflowY: 'auto',
            borderRadius: '16px'
          }}>
            <Leaderboard 
              gameId="game2" 
              onClose={() => setTunjukLeaderboard(false)} 
            />
          </div>
        </div>
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