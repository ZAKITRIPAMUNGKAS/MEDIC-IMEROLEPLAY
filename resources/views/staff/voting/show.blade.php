@extends('layouts.app')

@section('title', $voting->title . ' - Portal Medis iMe Roleplay')

@section('content')
<div class="min-h-screen bg-slate-950 text-slate-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto space-y-8">

        <!-- Navigation Back -->
        <div>
            <a href="{{ route('staff.voting.index') }}" class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-white transition-colors">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Voting
            </a>
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

        <!-- Main Banner -->
        <div class="relative overflow-hidden rounded-2xl bg-slate-900 border border-slate-800 p-6 sm:p-8 shadow-2xl space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <!-- Hospital Badge -->
                @if($voting->hospital === 'roxwood')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/10 border border-amber-500/30 text-amber-400">
                        <i class="fas fa-hospital-alt"></i> Roxwood Hospital
                    </span>
                @elseif($voting->hospital === 'alta')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-cyan-500/10 border border-cyan-500/30 text-cyan-400">
                        <i class="fas fa-hospital"></i> Alta Hospital
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-purple-500/10 border border-purple-500/30 text-purple-400">
                        <i class="fas fa-globe"></i> Terbuka Untuk Semua Staf
                    </span>
                @endif

                <!-- Status Badge -->
                @if($voting->status === 'active')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/10 border border-emerald-500/30 text-emerald-400">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span> Voting Berlangsung
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-800 text-slate-400 border border-slate-700">
                        <i class="fas fa-lock"></i> Sesi Ditutup
                    </span>
                @endif
            </div>

            <div>
                <h1 class="text-2xl sm:text-4xl font-extrabold text-white">
                    {{ $voting->title }}
                </h1>
                @if($voting->target_position)
                    <p class="text-indigo-400 font-semibold text-sm sm:text-base mt-1">
                        <i class="fas fa-briefcase mr-1"></i> Posisi Terpilih: {{ $voting->target_position }}
                    </p>
                @endif
            </div>

            @if($voting->description)
                <p class="text-slate-300 text-sm leading-relaxed border-t border-slate-800/80 pt-4">
                    {{ $voting->description }}
                </p>
            @endif

            <div class="flex flex-wrap items-center justify-between text-xs text-slate-400 pt-2 border-t border-slate-800/50 gap-4">
                <div class="flex items-center gap-4">
                    <span><i class="fas fa-users text-slate-500 mr-1"></i> Total Suara: <strong class="text-slate-200">{{ $voting->totalVotesCount() }}</strong></span>
                    <span><i class="fas fa-user-check text-slate-500 mr-1"></i> Status Anda: 
                        @if($hasVoted)
                            <strong class="text-emerald-400">Sudah Memilih</strong>
                        @else
                            <strong class="text-amber-400">Belum Memilih</strong>
                        @endif
                    </span>
                </div>
                <div>
                    <span><i class="fas fa-calendar-alt text-slate-500 mr-1"></i> Dibuat: {{ $voting->created_at->translatedFormat('d F Y H:i') }}</span>
                </div>
            </div>
        </div>

        <!-- Candidates Section Header -->
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-users text-indigo-400"></i> Daftar Kandidat Terdaftar
            </h2>
            <span class="text-xs text-slate-400">{{ $voting->candidates->count() }} Orang Kandidat</span>
        </div>

        <!-- Candidates List Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($voting->candidates as $candidate)
                @php
                    $isUserChoice = ($votedCandidateId === $candidate->id);
                    $votesCount = $candidate->votesCount();
                    $percentage = $candidate->percentageOfTotal();
                    $userObj = $candidate->user;
                @endphp
                <div class="relative rounded-2xl bg-slate-900 border {{ $isUserChoice ? 'border-emerald-500/80 ring-2 ring-emerald-500/20' : 'border-slate-800 hover:border-slate-700' }} p-6 flex flex-col justify-between transition-all duration-300 shadow-xl">
                    
                    @if($isUserChoice)
                        <div class="absolute top-4 right-4 bg-emerald-500 text-slate-950 font-extrabold text-xs px-3 py-1 rounded-full flex items-center gap-1 shadow-md">
                            <i class="fas fa-check-circle"></i> Pilihan Anda
                        </div>
                    @endif

                    <div class="space-y-4">
                        <!-- Candidate Header -->
                        <div class="flex items-start gap-4">
                            <div class="relative">
                                @if($candidate->photo || ($userObj && $userObj->profile_image))
                                    <img src="{{ asset('storage/' . ($candidate->photo ?? $userObj->profile_image)) }}" 
                                         alt="{{ $candidate->name }}" 
                                         class="w-16 h-16 rounded-2xl object-cover border-2 border-indigo-500/40 shadow-lg">
                                @else
                                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-600 to-purple-700 flex items-center justify-center text-white font-bold text-2xl shadow-lg border border-indigo-400/30">
                                        {{ strtoupper(substr($candidate->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1 pr-12">
                                <h3 class="text-lg font-bold text-white leading-tight">
                                    {{ $candidate->name }}
                                </h3>
                                
                                @if($userObj)
                                    <p class="text-xs text-indigo-400 font-medium mt-0.5">
                                        <i class="fas fa-id-badge mr-1"></i> {{ optional($userObj->role)->display_name ?? 'Staf Medis' }}
                                        @if($userObj->hospital)
                                            ({{ ucfirst($userObj->hospital) }})
                                        @endif
                                    </p>
                                @endif

                                @if($candidate->custom_role)
                                    <p class="text-xs text-slate-400 mt-0.5">
                                        {{ $candidate->custom_role }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- Vision & Mission -->
                        @if($candidate->vision_mission)
                            <div class="bg-slate-950/60 rounded-xl p-3.5 border border-slate-800/80 text-xs text-slate-300 space-y-1">
                                <span class="font-bold text-slate-400 block uppercase tracking-wider text-[10px]">Visi & Misi:</span>
                                <p class="whitespace-pre-line leading-relaxed text-slate-300">{!! nl2br(e($candidate->vision_mission)) !!}</p>
                            </div>
                        @endif

                        <!-- Live Percentage Progress Bar -->
                        <div class="space-y-1.5 pt-2 border-t border-slate-800/60">
                            <div class="flex justify-between items-center text-xs font-semibold">
                                <span class="text-slate-400">Perolehan Suara</span>
                                <span class="text-slate-200">{{ $votesCount }} Suara ({{ $percentage }}%)</span>
                            </div>
                            <div class="w-full bg-slate-950 h-3 rounded-full overflow-hidden p-0.5 border border-slate-800">
                                <div class="bg-gradient-to-r from-indigo-500 via-purple-500 to-emerald-400 h-full rounded-full transition-all duration-700 shadow-sm" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Vote Button Action -->
                    <div class="mt-6 pt-4 border-t border-slate-800/60">
                        @if($voting->status === 'active')
                            @if($hasVoted)
                                @if($isUserChoice)
                                    <button disabled class="w-full py-2.5 rounded-xl bg-emerald-500/20 text-emerald-300 font-bold text-sm border border-emerald-500/40 cursor-not-allowed">
                                        <i class="fas fa-check-circle mr-1"></i> Suara Telah Diberikan
                                    </button>
                                @else
                                    <button disabled class="w-full py-2.5 rounded-xl bg-slate-800 text-slate-500 font-medium text-sm cursor-not-allowed">
                                        Anda Memilih Kandidat Lain
                                    </button>
                                @endif
                            @else
                                <form action="{{ route('staff.voting.vote', $voting->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin memberikan suara kepada {{ addslashes($candidate->name) }}? Pilihan Anda tidak dapat diubah.');">
                                    @csrf
                                    <input type="hidden" name="candidate_id" value="{{ $candidate->id }}">
                                    <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold text-sm shadow-lg shadow-indigo-900/30 transition-all transform hover:-translate-y-0.5 active:translate-y-0">
                                        <i class="fas fa-vote-yea mr-1.5"></i> PILIH KANDIDAT INI
                                    </button>
                                </form>
                            @endif
                        @else
                            <button disabled class="w-full py-2.5 rounded-xl bg-slate-800 text-slate-500 font-medium text-sm cursor-not-allowed">
                                Sesi Voting Telah Ditutup
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
