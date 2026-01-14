@extends('layouts.app')

@section('content')

<div class="app">
  <main class="main">
    <div class="header">
      <div>
        <div class="title">{{ $lesson->title }}</div>
        <div class="sub">Butiran Bahan Pembelajaran</div>
      </div>
      <a href="{{ route('lesson.index') }}" class="btn-kembali" style="display:inline-block !important; margin-top:15px; padding:12px 24px !important; background:transparent !important; color:#6A4DF7 !important; border:2px solid #6A4DF7 !important; text-decoration:none !important; border-radius:8px !important; font-weight:700 !important; font-size:14px !important; transition:all 0.2s ease !important; cursor:pointer !important; line-height:1 !important; height:auto !important;" onmouseover="this.style.background='rgba(106,77,247,0.1)'" onmouseout="this.style.background='transparent'">
        <i class="bi bi-arrow-left" style="margin-right:6px;"></i>Kembali
      </a>
    </div>

    <!-- Lesson Details -->
    <section class="panel" style="margin-bottom:20px; margin-top:10px;">
      <h2 style="margin:0 0 20px 0; font-size:18px; font-weight:700; border-bottom:2px solid #d4c5f9; padding-bottom:12px;">Maklumat Bahan</h2>

      @if (session('success'))
        <div class="alert-success">{{ session('success') }}</div>
      @endif

      @if (session('error'))
        <div style="background:var(--danger);color:#fff;padding:12px 14px;border-radius:var(--card-radius);margin-bottom:20px;font-size:14px;">{{ session('error') }}</div>
      @endif

      <div style="margin-bottom: 20px;">
        <div style="font-weight: 600; font-size: 14px; color: var(--muted); margin-bottom: 6px;">Tajuk</div>
        <div style="font-size: 16px; font-weight: 600;">{{ $lesson->title }}</div>
      </div>

      <div style="margin-bottom: 20px;">
        <div style="font-weight: 600; font-size: 14px; color: var(--muted); margin-bottom: 6px;">Penerangan</div>
        <div style="font-size: 14px; line-height: 1.6;">{{ $lesson->description ?? 'Tiada penerangan' }}</div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
        <div>
          <div style="font-weight: 600; font-size: 14px; color: var(--muted); margin-bottom: 6px;">Kelas</div>
          <div style="font-size: 14px; font-weight: 600;">{{ $lesson->class_group }}</div>
        </div>
        <div>
          <div style="font-weight: 600; font-size: 14px; color: var(--muted); margin-bottom: 6px;">Keterlihatan</div>
          <div style="font-size: 14px; font-weight: 600;">
            @if($lesson->visibility === 'class')
              <span style="background:rgba(106,77,247,0.1); color:var(--accent); padding:4px 10px; border-radius:4px; font-size:12px;">
                <i class="bi bi-lock-fill" style="margin-right:4px;"></i>Kelas Sahaja
              </span>
            @else
              <span style="background:rgba(34,197,94,0.1); color:#22c55e; padding:4px 10px; border-radius:4px; font-size:12px;">
                <i class="bi bi-globe" style="margin-right:4px;"></i>Awam
              </span>
            @endif
          </div>
        </div>
        <div>
          <div style="font-weight: 600; font-size: 14px; color: var(--muted); margin-bottom: 6px;">Dicipta</div>
          <div style="font-size: 14px;">{{ $lesson->created_at->format('d M Y, h:i A') }}</div>
        </div>
      </div>

      @if($lesson->uploader)
      <div style="margin-bottom: 20px;">
        <div style="font-weight: 600; font-size: 14px; color: var(--muted); margin-bottom: 6px;">Dimuat Naik Oleh</div>
        <div style="font-size: 14px;">
          {{ $lesson->uploader->name ?? 'Tidak Diketahui' }}
          @if($lesson->uploaded_by === Auth::id())
            <span style="background:rgba(106,77,247,0.1); color:var(--accent); padding:4px 10px; border-radius:4px; font-size:12px; margin-left:8px;">
              <i class="bi bi-person-check" style="margin-right:4px;"></i>Anda
            </span>
          @endif
        </div>
      </div>
      @endif

      @if($lesson->file_path)
      <div style="margin-bottom: 20px; padding-top: 0;">
        <div style="font-weight: 600; font-size: 14px; color: var(--muted); margin-bottom: 12px;">Fail Dilampirkan</div>
        <div style="display: flex; gap: 12px; align-items: center; background: rgba(106,77,247,0.05); padding: 16px; border-radius: 8px; border: 1px solid rgba(106,77,247,0.2);">
          <span style="background: linear-gradient(90deg,var(--accent),var(--accent-2)); color:#fff; padding: 10px 16px; border-radius: 6px; font-weight: 700; font-size: 14px; min-width: 60px; text-align: center;">
            {{ strtoupper($lesson->file_ext) }}
          </span>
          <div style="flex: 1;">
            <div style="font-size: 14px; font-weight: 600; margin-bottom: 2px;">{{ $lesson->file_name }}</div>
            <div style="font-size: 12px; color: var(--muted);">{{ $lesson->created_at->diffForHumans() }}</div>
          </div>
        </div>
      </div>

      <div style="display: flex; gap: 12px; margin-top: 12px; flex-wrap: wrap; justify-content: center;">
        <a href="{{ route('lesson.download', $lesson->id) }}" download style="display:inline-flex; align-items:center; padding:12px 24px; background:linear-gradient(90deg,var(--accent),var(--accent-2)); color:#fff; text-decoration:none; border-radius:8px; font-weight:700; font-size:14px; transition:transform .2s ease, box-shadow .2s ease; box-shadow: 0 4px 12px rgba(106,77,247,0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(106,77,247,0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(106,77,247,0.3)';">
          <i class="bi bi-download" style="margin-right:8px; font-size:16px;"></i>Muat Turun
        </a>
      </div>
      @else
      <div style="text-align: center; padding: 40px 24px; color: var(--muted); background: rgba(200,200,200,0.05); border-radius: 8px; margin-top: 20px;">
        <i class="bi bi-file-earmark-x" style="font-size: 48px; opacity: 0.3; display: block; margin-bottom: 12px;"></i>
        <div style="font-size: 14px;">Tiada fail dilampirkan</div>
      </div>
      @endif
    </section>

    <!-- PDF Preview -->
    @if($lesson->file_path && $lesson->file_ext === 'pdf')
      <section class="panel" style="margin-bottom:20px;">
        <h2 style="margin:0 0 20px 0; font-size:18px; font-weight:700; border-bottom:2px solid #d4c5f9; padding-bottom:12px;">Paparan PDF</h2>
        <div style="margin-top:20px;">
          <iframe src="{{ route('lesson.preview-file', $lesson->id) }}" style="width:100%; height:600px;" frameborder="0"></iframe>
        </div>
      </section>
    @endif
@endsection