@extends('layouts.app')

@section('title', 'Pengajuan Meeting - Portal Medis')

@section('content')
    <div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
        <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700"></div>
        <div class="absolute inset-0 bg-black bg-opacity-20"></div>

        <div class="relative max-w-5xl w-full mx-auto text-white">
            <!-- Header -->
            <div class="glass-effect rounded-2xl elegant-shadow-lg p-6 md:p-8 mb-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
                            <i class="fas fa-calendar-check mr-3 text-sky-400"></i>Pengajuan Meeting
                        </h1>
                        <p class="text-sky-200 text-lg">Riwayat dan status pengajuan meeting Anda</p>
                    </div>
                    <a href="{{ route('staff.meeting-requests.create') }}"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl">
                        <i class="fas fa-plus mr-2"></i>Ajukan Meeting Baru
                    </a>
                </div>

                <!-- Stats -->
                <div class="flex flex-col sm:flex-row gap-4 mt-6">
                    <div class="flex-1 bg-gradient-to-br from-yellow-500/20 to-yellow-600/10 rounded-2xl p-5 border border-yellow-500/30 text-center shadow-lg backdrop-blur-sm transition-transform hover:-translate-y-1 duration-300">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-yellow-500/20 text-yellow-400 mb-3 shadow-inner">
                            <i class="fas fa-clock text-xl"></i>
                        </div>
                        <div class="text-3xl font-bold text-white mb-1">{{ $pendingCount }}</div>
                        <div class="text-yellow-300 text-xs font-bold uppercase tracking-wider">Menunggu</div>
                    </div>
                    <div class="flex-1 bg-gradient-to-br from-green-500/20 to-green-600/10 rounded-2xl p-5 border border-green-500/30 text-center shadow-lg backdrop-blur-sm transition-transform hover:-translate-y-1 duration-300">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-green-500/20 text-green-400 mb-3 shadow-inner">
                            <i class="fas fa-check text-xl"></i>
                        </div>
                        <div class="text-3xl font-bold text-white mb-1">{{ $approvedCount }}</div>
                        <div class="text-green-300 text-xs font-bold uppercase tracking-wider">Disetujui</div>
                    </div>
                    <div class="flex-1 bg-gradient-to-br from-red-500/20 to-red-600/10 rounded-2xl p-5 border border-red-500/30 text-center shadow-lg backdrop-blur-sm transition-transform hover:-translate-y-1 duration-300">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-red-500/20 text-red-400 mb-3 shadow-inner">
                            <i class="fas fa-times text-xl"></i>
                        </div>
                        <div class="text-3xl font-bold text-white mb-1">{{ $rejectedCount }}</div>
                        <div class="text-red-300 text-xs font-bold uppercase tracking-wider">Ditolak</div>
                    </div>
                </div>
            </div>

            <!-- Requests List -->
            <div class="glass-effect rounded-2xl elegant-shadow-lg p-6 md:p-8 relative overflow-hidden">
                <!-- Background decorative blob -->
                <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 rounded-full bg-sky-500/10 blur-3xl pointer-events-none"></div>
                
                @if($requests->isEmpty())
                    <div class="text-center py-16 relative z-10">
                        <div class="w-24 h-24 bg-gradient-to-br from-sky-400/20 to-sky-600/10 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner border border-sky-400/20">
                            <i class="fas fa-calendar-plus text-sky-400 text-4xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-3">Belum Ada Pengajuan</h3>
                        <p class="text-sky-200/80 max-w-md mx-auto mb-8 leading-relaxed">Anda belum memiliki riwayat pengajuan meeting. Buat pengajuan baru agar aktivitas Anda dapat dicatat oleh sistem.</p>
                        <a href="{{ route('staff.meeting-requests.create') }}"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-1">
                            <i class="fas fa-plus mr-2"></i>Buat Pengajuan Perdana
                        </a>
                    </div>
                @else
                    <div class="space-y-4 relative z-10">
                        @foreach($requests as $req)
                            @php
                                $badge = $req->getStatusBadge();
                                $colorMap = [
                                    'yellow' => ['bg' => 'bg-yellow-500/15', 'border' => 'border-yellow-500/30', 'text' => 'text-yellow-400'],
                                    'green' => ['bg' => 'bg-green-500/15', 'border' => 'border-green-500/30', 'text' => 'text-green-400'],
                                    'red' => ['bg' => 'bg-red-500/15', 'border' => 'border-red-500/30', 'text' => 'text-red-400'],
                                    'gray' => ['bg' => 'bg-gray-500/15', 'border' => 'border-gray-500/30', 'text' => 'text-gray-400'],
                                ];
                                $colors = $colorMap[$badge['color']] ?? $colorMap['gray'];
                            @endphp

                            <div class="bg-white/5 rounded-xl p-5 border border-white/10 hover:border-white/20 transition-all duration-200">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="{{ $colors['bg'] }} {{ $colors['border'] }} {{ $colors['text'] }} border px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center">
                                                <i class="fas {{ $badge['icon'] }} mr-1.5"></i>{{ $badge['label'] }}
                                            </span>
                                            <span class="text-gray-400 text-sm">
                                                #{{ $req->id }} — {{ $req->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-4 text-sm">
                                            <span class="text-white font-medium">
                                                <i class="fas fa-calendar text-sky-400 mr-1.5"></i>
                                                {{ $req->requested_date->format('d M Y') }}
                                            </span>
                                            <span class="text-gray-300">
                                                <i class="fas fa-clock text-sky-400 mr-1.5"></i>
                                                {{ Carbon\Carbon::parse($req->start_time)->format('H:i') }} — {{ Carbon\Carbon::parse($req->end_time)->format('H:i') }}
                                                ({{ $req->getFormattedDuration() }})
                                            </span>
                                        </div>
                                        <p class="text-gray-400 text-sm mt-2 line-clamp-2">
                                            <i class="fas fa-comment text-gray-500 mr-1.5"></i>{{ $req->reason }}
                                        </p>
                                    </div>
                                </div>

                                @if($req->review_notes)
                                    <div class="mt-3 pt-3 border-t border-white/10">
                                        <p class="text-sm text-gray-400">
                                            <i class="fas fa-user-check text-sky-400 mr-1.5"></i>
                                            <span class="text-white font-medium">{{ $req->reviewer->name ?? 'Admin' }}</span>:
                                            {{ $req->review_notes }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $requests->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
