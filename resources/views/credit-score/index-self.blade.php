@extends('layouts.app')
@section('title', 'Credit Score Saya')

@section('content')
<div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700"></div>
    <div class="absolute inset-0 bg-black bg-opacity-20"></div>

    <div class="relative max-w-2xl mx-auto text-white">

        {{-- Skor utama --}}
        @php
            $balance = $creditScore->balance;
            $scoreColor = $balance >= 85 ? 'from-green-400 to-emerald-500' : ($balance >= 80 ? 'from-yellow-400 to-amber-500' : 'from-red-400 to-rose-500');
            $scoreLabel = $balance >= 85 ? 'Sangat Baik' : ($balance >= 80 ? 'Baik' : 'Perlu Perhatian');
        @endphp

        <div class="glass-effect rounded-2xl p-8 mb-6 text-center">
            <div class="w-24 h-24 mx-auto rounded-full bg-gradient-to-br {{ $scoreColor }} flex items-center justify-center mb-4 shadow-xl">
                <span class="text-3xl font-black text-white">{{ $balance }}</span>
            </div>
            <h1 class="text-2xl font-bold text-white mb-1">Credit Score Anda</h1>
            <p class="text-sky-200 text-sm mb-3">{{ auth()->user()->name }} · {{ auth()->user()->role?->display_name }}</p>
            <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-sm font-bold bg-white/10 border border-white/20 text-white">
                <i class="fas fa-star text-amber-400"></i> {{ $scoreLabel }}
            </span>

            {{-- Info batas minimum --}}
            <div class="mt-5 grid grid-cols-2 gap-3 text-left">
                <div class="bg-white/5 rounded-xl p-3 border border-white/10">
                    <p class="text-gray-400 text-xs mb-1">Min. Trainee → Co-ass</p>
                    <div class="flex items-center justify-between">
                        <span class="text-white font-bold">80 poin</span>
                        @if($balance >= 80)
                            <i class="fas fa-check-circle text-green-400"></i>
                        @else
                            <i class="fas fa-times-circle text-red-400"></i>
                        @endif
                    </div>
                </div>
                <div class="bg-white/5 rounded-xl p-3 border border-white/10">
                    <p class="text-gray-400 text-xs mb-1">Min. Dokter Umum ke atas</p>
                    <div class="flex items-center justify-between">
                        <span class="text-white font-bold">85 poin</span>
                        @if($balance >= 85)
                            <i class="fas fa-check-circle text-green-400"></i>
                        @else
                            <i class="fas fa-times-circle text-red-400"></i>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Riwayat log --}}
        <div class="glass-effect rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-white/10 bg-white/5">
                <h2 class="text-sm font-bold text-white">Riwayat Transaksi</h2>
            </div>
            @if($logs->isEmpty())
                <div class="text-center py-10 text-gray-400 text-sm">Belum ada transaksi Credit Score.</div>
            @else
                <div class="divide-y divide-white/5">
                    @foreach($logs as $log)
                    <div class="px-5 py-3 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0
                                {{ $log->type === 'add' ? 'bg-green-500/20' : 'bg-red-500/20' }}">
                                <i class="fas fa-{{ $log->type === 'add' ? 'plus' : 'minus' }} text-xs
                                    {{ $log->type === 'add' ? 'text-green-400' : 'text-red-400' }}"></i>
                            </div>
                            <div>
                                <p class="text-white text-sm">{{ $log->reason }}</p>
                                <p class="text-gray-400 text-xs">
                                    Oleh {{ $log->issuedBy?->name ?? '-' }} ·
                                    {{ $log->created_at->translatedFormat('d M Y H:i') }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="font-bold {{ $log->type === 'add' ? 'text-green-400' : 'text-red-400' }}">
                                {{ $log->type === 'add' ? '+' : '' }}{{ $log->amount }}
                            </p>
                            <p class="text-gray-400 text-xs">Saldo: {{ $log->balance_after }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @if($logs->hasPages())
                <div class="px-5 py-3 border-t border-white/10 flex justify-end gap-1">
                    @if(!$logs->onFirstPage())<a href="{{ $logs->previousPageUrl() }}" class="px-3 py-1.5 bg-white/10 hover:bg-white/20 rounded-lg text-white text-xs">‹</a>@endif
                    @if($logs->hasMorePages())<a href="{{ $logs->nextPageUrl() }}" class="px-3 py-1.5 bg-white/10 hover:bg-white/20 rounded-lg text-white text-xs">›</a>@endif
                </div>
                @endif
            @endif
        </div>

    </div>
</div>
@endsection
