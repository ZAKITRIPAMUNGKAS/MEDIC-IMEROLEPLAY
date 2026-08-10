@extends('layouts.app')

@section('title', 'Fitur Voting & Pemilihan - Portal Medis iMe Roleplay')

@section('content')
<div class="min-h-screen bg-slate-950 text-slate-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-8">
        
        <!-- Header Banner -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 border border-slate-800 p-6 sm:p-8 shadow-2xl">
            <div class="absolute -right-10 -top-10 w-48 h-48 bg-indigo-500/10 rounded-full blur-3xl"></div>
            <div class="absolute right-1/3 -bottom-10 w-48 h-48 bg-amber-500/10 rounded-full blur-3xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-xs font-semibold uppercase tracking-wider mb-3">
                        <i class="fas fa-check-circle"></i> Demokrasi Staf Medis
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
                        Pemilihan & Voting Internal
                    </h1>
                    <p class="mt-2 text-slate-400 text-sm sm:text-base max-w-2xl">
                        Suarakan aspirasi Anda untuk menentukan kepemimpinan & struktur divisi di Roxwood dan Alta Hospital.
                    </p>
                </div>
                
                @if(auth()->user() && (auth()->user()->hasPermission('manage_users') || auth()->user()->isAdmin()))
                <a href="{{ route('admin.voting.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-medium text-sm shadow-lg shadow-indigo-900/30 transition-all duration-200">
                    <i class="fas fa-tasks"></i> Kelola Voting (Admin)
                </a>
                @endif
            </div>
        </div>

        <!-- Alert Notification -->
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm flex items-center gap-3">
                <i class="fas fa-check-circle text-emerald-400 text-lg"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-sm flex items-center gap-3">
                <i class="fas fa-exclamation-triangle text-rose-400 text-lg"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Filter Tabs (Alta vs Roxwood) -->
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-800 pb-4">
            <div class="flex items-center gap-2 bg-slate-900 p-1.5 rounded-xl border border-slate-800">
                <a href="{{ route('staff.voting.index', ['hospital' => 'all']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $hospital === 'all' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                   <i class="fas fa-globe mr-1.5"></i> Semua
                </a>
                <a href="{{ route('staff.voting.index', ['hospital' => 'alta']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $hospital === 'alta' ? 'bg-cyan-600 text-white shadow-md shadow-cyan-900/30' : 'text-slate-400 hover:text-white' }}">
                   <i class="fas fa-hospital mr-1.5 text-cyan-300"></i> Alta Hospital
                   <span class="ml-2 px-1.5 py-0.5 text-xs rounded-full bg-slate-800 text-slate-300">{{ $altaCount }}</span>
                </a>
                <a href="{{ route('staff.voting.index', ['hospital' => 'roxwood']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $hospital === 'roxwood' ? 'bg-amber-600 text-white shadow-md shadow-amber-900/30' : 'text-slate-400 hover:text-white' }}">
                   <i class="fas fa-hospital-user mr-1.5 text-amber-300"></i> Roxwood Hospital
                   <span class="ml-2 px-1.5 py-0.5 text-xs rounded-full bg-slate-800 text-slate-300">{{ $roxwoodCount }}</span>
                </a>
            </div>

            <div class="text-xs text-slate-400 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>{{ $activeCount }} Voting Sedang Berlangsung</span>
            </div>
        </div>

        <!-- Voting Cards Grid -->
        @if($votings->isEmpty())
            <div class="rounded-2xl bg-slate-900/50 border border-slate-800/80 p-12 text-center">
                <div class="w-16 h-16 bg-slate-800 rounded-full flex items-center justify-center mx-auto text-slate-500 mb-4 text-2xl">
                    <i class="fas fa-box-archive"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-300">Belum Ada Sesi Voting</h3>
                <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">Saat ini belum ada sesi voting yang dipublikasikan untuk kategori ini.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($votings as $voting)
                    @php
                        $userVoted = $voting->hasUserVoted(auth()->id());
                        $totalVotes = $voting->totalVotesCount();
                    @endphp
                    <div class="group relative rounded-2xl bg-slate-900/90 border border-slate-800/90 hover:border-slate-700 transition-all duration-300 flex flex-col justify-between overflow-hidden shadow-lg hover:shadow-2xl">
                        
                        <!-- Top Accent line according to hospital -->
                        @if($voting->hospital === 'roxwood')
                            <div class="h-1.5 w-full bg-gradient-to-r from-amber-500 to-amber-600"></div>
                        @elseif($voting->hospital === 'alta')
                            <div class="h-1.5 w-full bg-gradient-to-r from-cyan-500 to-blue-600"></div>
                        @else
                            <div class="h-1.5 w-full bg-gradient-to-r from-indigo-500 to-purple-600"></div>
                        @endif

                        <div class="p-6 space-y-4">
                            <!-- Badges Header -->
                            <div class="flex items-center justify-between gap-2">
                                <!-- Hospital Badge -->
                                @if($voting->hospital === 'roxwood')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-500/10 border border-amber-500/30 text-amber-400">
                                        <i class="fas fa-hospital-alt"></i> Roxwood Hospital
                                    </span>
                                @elseif($voting->hospital === 'alta')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-cyan-500/10 border border-cyan-500/30 text-cyan-400">
                                        <i class="fas fa-hospital"></i> Alta Hospital
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-500/10 border border-purple-500/30 text-purple-400">
                                        <i class="fas fa-globe"></i> Semua Staf
                                    </span>
                                @endif

                                <!-- Status Badge -->
                                @if($voting->status === 'active')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-800 text-slate-400">
                                        <i class="fas fa-lock text-xs"></i> Ditutup
                                    </span>
                                @endif
                            </div>

                            <!-- Title & Position -->
                            <div>
                                <h2 class="text-xl font-bold text-white group-hover:text-indigo-400 transition-colors">
                                    {{ $voting->title }}
                                </h2>
                                @if($voting->target_position)
                                    <div class="mt-1 text-xs font-semibold text-slate-400 flex items-center gap-1">
                                        <i class="fas fa-briefcase text-slate-500"></i> Posisi: <span class="text-slate-200">{{ $voting->target_position }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Description -->
                            @if($voting->description)
                                <p class="text-slate-400 text-sm line-clamp-2">
                                    {{ $voting->description }}
                                </p>
                            @endif

                            <!-- Meta Stats -->
                            <div class="pt-2 grid grid-cols-2 gap-2 text-xs border-t border-slate-800/60 text-slate-400">
                                <div>
                                    <span class="block text-slate-500">Kandidat</span>
                                    <span class="font-bold text-slate-200 text-sm">{{ $voting->candidates->count() }} Akun</span>
                                </div>
                                <div>
                                    <span class="block text-slate-500">Total Suara</span>
                                    <span class="font-bold text-slate-200 text-sm">{{ $totalVotes }} Suara</span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer Action -->
                        <div class="p-6 pt-0">
                            @if($userVoted)
                                <a href="{{ route('staff.voting.show', $voting->id) }}" class="w-full py-2.5 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-emerald-400 font-semibold text-sm flex items-center justify-center gap-2 border border-emerald-500/20 transition-all">
                                    <i class="fas fa-check-circle"></i> Sudah Memberi Suara
                                </a>
                            @elseif($voting->status === 'active')
                                <a href="{{ route('staff.voting.show', $voting->id) }}" class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-500 hover:to-blue-500 text-white font-semibold text-sm flex items-center justify-center gap-2 shadow-lg shadow-indigo-900/30 transition-all">
                                    <i class="fas fa-vote-yea"></i> Pilih Kandidat Sekarang
                                </a>
                            @else
                                <a href="{{ route('staff.voting.show', $voting->id) }}" class="w-full py-2.5 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-sm flex items-center justify-center gap-2 transition-all">
                                    <i class="fas fa-chart-pie"></i> Lihat Hasil Voting
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $votings->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
