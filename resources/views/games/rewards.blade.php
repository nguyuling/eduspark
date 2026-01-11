@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
  <div class="mb-6 flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Ganjaran Saya</h1>
      <p class="text-gray-600 dark:text-gray-300 text-sm">Lihat semua ganjaran yang anda peroleh daripada permainan.</p>
    </div>
    <a href="{{ route('games.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-sm font-semibold">← Kembali ke Permainan</a>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
      <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">Jumlah Mata Dituntut</div>
      <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalPoints }}</div>
    </div>
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
      <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">Ganjaran Belum Dituntut</div>
      <div class="text-2xl font-bold text-amber-600">{{ $unclaimedCount }}</div>
    </div>
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
      <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">Jumlah Ganjaran</div>
      <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $rewards->count() }}</div>
    </div>
  </div>

  @if($rewards->isEmpty())
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 text-center text-gray-600 dark:text-gray-300">Tiada ganjaran lagi. Main permainan untuk dapatkan ganjaran!</div>
  @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      @foreach($rewards as $reward)
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 flex flex-col gap-2">
          <div class="text-3xl">{{ $reward->badge_icon ?? '🎖️' }}</div>
          <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $reward->reward_name }}</div>
          <div class="text-xs text-gray-600 dark:text-gray-300">{{ $reward->reward_description }}</div>
          <div class="text-xs font-semibold text-amber-700">+{{ $reward->points_awarded }} mata</div>
          <div class="text-xs text-gray-500">Permainan: {{ $reward->game->title ?? 'N/A' }}</div>
          <div class="flex items-center justify-between pt-2">
            <span class="text-xs font-semibold {{ $reward->is_claimed ? 'text-green-600' : 'text-amber-700' }}">{{ $reward->is_claimed ? 'Sudah dituntut' : 'Belum dituntut' }}</span>
            @if(!$reward->is_claimed)
              <form method="POST" action="{{ route('rewards.claim', $reward->id) }}">
                @csrf
                <button type="submit" class="px-3 py-1 text-xs font-semibold text-white bg-amber-600 rounded hover:bg-amber-700">Tuntut</button>
              </form>
            @else
              <span class="text-xs text-green-600 font-bold">✓</span>
            @endif
          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>
@endsection
