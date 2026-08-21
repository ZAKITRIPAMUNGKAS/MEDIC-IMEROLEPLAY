@extends('layouts.app')

@section('title', $user->name . ' - Profil Anggota Portal Medis')

@section('content')
@php
    $managerEvaluations = $managerEvaluations ?? collect([]);
    $evaluationsAvg = $evaluationsAvg ?? 0;
    $evaluationsCount = $evaluationsCount ?? 0;
    $canSeeAll = $canSeeAll ?? false;
@endphp
<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8" x-data="{ activeTab: null }">
    <div class="max-w-6xl mx-auto space-y-8">
        
        <!-- Back Navigation & Quick Action -->
        <div class="flex items-center justify-between gap-4">
            <a href="{{ route('staff.members.index') }}" class="inline-flex items-center text-sky-300 hover:text-white text-sm font-medium transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Direktori
            </a>
            
            <a href="{{ route('staff.messages.index', ['user' => $user->id]) }}" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white text-xs font-bold rounded-xl shadow-lg transition-all transform hover:scale-105">
                <i class="fas fa-paper-plane mr-2"></i> Kirim Pesan (DM)
            </a>
        </div>

        <!-- Member Header Profile Card -->
        <div class="bg-white bg-opacity-10 backdrop-blur-md border border-white border-opacity-20 rounded-2xl p-6 sm:p-8 shadow-xl flex flex-col md:flex-row gap-6 items-center">
            
            <!-- Left Side: Profile Photo -->
            <div class="relative shrink-0">
                <img src="{{ $user->profile_image_url }}" 
                     onerror="{{ $user->profile_image_on_error }}"
                     alt="{{ $user->name }}" 
                     class="w-32 h-32 rounded-2xl object-cover border-2 border-white border-opacity-30 shadow-xl"
                />
                @if($user->isOnline())
                    <span class="absolute -bottom-1.5 -right-1.5 w-6 h-6 bg-emerald-400 border-4 border-slate-900 rounded-full shadow-[0_0_12px_rgba(52,211,153,0.8)]" title="Online"></span>
                @else
                    <span class="absolute -bottom-1.5 -right-1.5 w-6 h-6 bg-slate-500 border-4 border-slate-900 rounded-full" title="Offline"></span>
                @endif
            </div>

            <!-- Right Side: Details -->
            <div class="flex-1 text-center md:text-left space-y-3 min-w-0">
                <div class="flex flex-wrap items-center justify-center md:justify-start gap-2.5">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight truncate">
                        {{ $user->name }}
                    </h1>
                    
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase border {{ $user->hospital === 'roxwood' ? 'bg-emerald-500 bg-opacity-20 text-emerald-300 border-emerald-500 border-opacity-40' : 'bg-cyan-500 bg-opacity-20 text-cyan-300 border-cyan-500 border-opacity-40' }}">
                        <i class="fas fa-hospital mr-1"></i> {{ $user->hospital === 'roxwood' ? 'Roxwood Hospital' : 'Alta Hospital' }}
                    </span>
                    
                    @if($user->isClockedIn())
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-500 bg-opacity-20 text-amber-300 border border-amber-500 border-opacity-40 animate-pulse">
                            <i class="fas fa-clock mr-1"></i> ON DUTY
                        </span>
                    @endif
                </div>

                <!-- Roles & Attributes -->
                <div class="flex flex-wrap items-center justify-center md:justify-start gap-y-1.5 gap-x-4 text-xs sm:text-sm text-sky-200">
                    <div>
                        <i class="fas fa-id-card text-sky-400 mr-1.5"></i>
                        <span>Staff ID: <strong class="text-white">{{ $user->staff_id ?? '-' }}</strong></span>
                    </div>
                    <span class="hidden md:inline text-sky-400/50">•</span>
                    <div>
                        <i class="fas fa-envelope text-sky-400 mr-1.5"></i>
                        <span class="text-white">{{ $user->email }}</span>
                    </div>
                    <span class="hidden md:inline text-sky-400/50">•</span>
                    <div>
                        <i class="fas fa-fingerprint text-sky-400 mr-1.5"></i>
                        <span>Citizen ID: <strong class="text-white">{{ $user->citizen_id ?? '-' }}</strong></span>
                    </div>
                </div>

                <div class="pt-2">
                    @php
                        $level = $user->role->level ?? 0;
                        $badgeColor = 'bg-sky-500/20 text-sky-300 border-sky-500/30';
                        if ($level >= 4) {
                            $badgeColor = 'bg-rose-500/20 text-rose-300 border-rose-500/30';
                        } elseif ($level === 3) {
                            $badgeColor = 'bg-purple-500/20 text-purple-300 border-purple-500/30';
                        } elseif ($level === 2) {
                            $badgeColor = 'bg-indigo-500/20 text-indigo-300 border-indigo-500/30';
                        } elseif ($level === 1) {
                            $badgeColor = 'bg-blue-500/20 text-blue-300 border-blue-500/30';
                        }
                    @endphp
                    <span class="inline-flex items-center px-4 py-1.5 rounded-xl text-xs font-bold border shadow-md {{ $badgeColor }}">
                        <i class="fas fa-user-tag mr-2"></i>{{ $user->role->display_name ?? 'Staff' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Work Statistics & Interactive Cards (4 Widgets Grid) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Widget 1: Duty Hours (Info Card) -->
            <div class="bg-white bg-opacity-10 backdrop-blur-md border border-white border-opacity-20 rounded-2xl p-5 shadow-xl flex items-center gap-4">
                <div class="w-12 h-12 bg-sky-500 bg-opacity-20 border border-sky-400 border-opacity-30 text-sky-300 rounded-xl flex items-center justify-center text-xl shrink-0">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <span class="text-xs text-sky-200 block font-medium">Jam Duty (Akumulasi)</span>
                    <span class="text-base font-bold text-white tracking-wide mt-0.5 block">
                        {{ $stats['total_duty_formatted'] ?: '0 detik' }}
                    </span>
                </div>
            </div>

            <!-- Widget 2: Operation Records (Clickable Card) -->
            <button type="button" 
                    @click="activeTab = (activeTab === 'operations' ? null : 'operations')"
                    :class="activeTab === 'operations' ? 'bg-white bg-opacity-20 border-emerald-400 ring-2 ring-emerald-400/30' : 'bg-white bg-opacity-10 border-white/20 hover:border-emerald-400/50'"
                    class="w-full text-left rounded-2xl p-5 shadow-xl border backdrop-blur-md transition-all duration-300 group cursor-pointer relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 bg-emerald-500 bg-opacity-20 border border-emerald-400 border-opacity-30 text-emerald-300 group-hover:scale-110 transition-transform rounded-xl flex items-center justify-center text-lg shrink-0">
                            <i class="fas fa-procedures"></i>
                        </div>
                        <div>
                            <span class="text-xs text-sky-200 block font-medium">Tindakan Operasi</span>
                            <span class="text-base font-bold text-white tracking-wide mt-0.5 block">
                                {{ $stats['total_operations'] }} Rekam Medis
                            </span>
                        </div>
                    </div>
                    
                    <div class="text-sky-300 group-hover:text-emerald-300 transition-colors">
                        <template x-if="activeTab === 'operations'">
                            <i class="fas fa-chevron-up text-xs text-emerald-300"></i>
                        </template>
                        <template x-if="activeTab !== 'operations'">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </template>
                    </div>
                </div>
            </button>

            <!-- Widget 3: Letters Handled (Clickable Card) -->
            <button type="button" 
                    @click="activeTab = (activeTab === 'forms' ? null : 'forms')"
                    :class="activeTab === 'forms' ? 'bg-white bg-opacity-20 border-indigo-400 ring-2 ring-indigo-400/30' : 'bg-white bg-opacity-10 border-white/20 hover:border-indigo-400/50'"
                    class="w-full text-left rounded-2xl p-5 shadow-xl border backdrop-blur-md transition-all duration-300 group cursor-pointer relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 bg-indigo-500 bg-opacity-20 border border-indigo-400 border-opacity-30 text-indigo-300 group-hover:scale-110 transition-transform rounded-xl flex items-center justify-center text-lg shrink-0">
                            <i class="fas fa-file-signature"></i>
                        </div>
                        <div>
                            <span class="text-xs text-sky-200 block font-medium">Surat/Form Ditangani</span>
                            <span class="text-base font-bold text-white tracking-wide mt-0.5 block">
                                {{ $stats['total_forms_processed'] }} Dokumen
                            </span>
                        </div>
                    </div>

                    <div class="text-sky-300 group-hover:text-indigo-300 transition-colors">
                        <template x-if="activeTab === 'forms'">
                            <i class="fas fa-chevron-up text-xs text-indigo-300"></i>
                        </template>
                        <template x-if="activeTab !== 'forms'">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </template>
                    </div>
                </div>
            </button>

            <!-- Widget 4: Anonymous Manager Evaluations (Clickable Card) -->
            <button type="button" 
                    @click="activeTab = (activeTab === 'evaluations' ? null : 'evaluations')"
                    :class="activeTab === 'evaluations' ? 'bg-white bg-opacity-20 border-amber-400 ring-2 ring-amber-400/30' : 'bg-white bg-opacity-10 border-white/20 hover:border-amber-400/50'"
                    class="w-full text-left rounded-2xl p-5 shadow-xl border backdrop-blur-md transition-all duration-300 group cursor-pointer relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 bg-amber-500 bg-opacity-20 border border-amber-400 border-opacity-30 text-amber-300 group-hover:scale-110 transition-transform rounded-xl flex items-center justify-center text-lg shrink-0">
                            <i class="fas fa-star"></i>
                        </div>
                        <div>
                            <span class="text-xs text-sky-200 block font-medium">Evaluasi Staf (Anonim)</span>
                            <span class="text-base font-bold text-white tracking-wide mt-0.5 block">
                                {{ $evaluationsAvg > 0 ? number_format($evaluationsAvg, 1) . ' ⭐' : '0.0 ⭐' }}
                                <span class="text-xs text-amber-300 font-normal">({{ $evaluationsCount }})</span>
                            </span>
                        </div>
                    </div>

                    <div class="text-sky-300 group-hover:text-amber-300 transition-colors">
                        <template x-if="activeTab === 'evaluations'">
                            <i class="fas fa-chevron-up text-xs text-amber-300"></i>
                        </template>
                        <template x-if="activeTab !== 'evaluations'">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </template>
                    </div>
                </div>
            </button>
        </div>

        <!-- Detail Section 1: Rekam Operasi -->
        <div x-show="activeTab === 'operations'" 
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 -translate-y-2 scale-98"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="bg-white bg-opacity-10 backdrop-blur-md border border-white border-opacity-20 rounded-2xl shadow-xl p-6 sm:p-8 space-y-6">
            
            <div class="flex items-center justify-between border-b border-white border-opacity-10 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500 bg-opacity-20 text-emerald-300 border border-emerald-400/30 flex items-center justify-center">
                        <i class="fas fa-procedures text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white">Detail Rekam Operasi</h2>
                        <p class="text-xs text-sky-200">Daftar riwayat operasi medis yang ditangani oleh {{ $user->name }}</p>
                    </div>
                </div>
                
                <button type="button" @click="activeTab = null" class="px-3 py-1.5 rounded-xl bg-white bg-opacity-10 hover:bg-opacity-20 text-white text-xs font-semibold transition-all border border-white/20 flex items-center gap-1.5">
                    <i class="fas fa-times"></i> Tutup
                </button>
            </div>

            @if(count($operations) > 0)
                <div class="grid grid-cols-1 gap-4">
                    @foreach($operations as $item)
                        @php $op = $item['data']; @endphp
                        <div class="bg-black bg-opacity-20 border border-white border-opacity-10 hover:border-emerald-400/40 rounded-xl p-5 transition-all space-y-3">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-white/10 pb-3">
                                <div class="flex items-center gap-2.5">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500 bg-opacity-20 text-emerald-300 border border-emerald-500/30 uppercase">
                                        {{ $op->jenis_operasi }}
                                    </span>
                                    <h3 class="font-bold text-white text-base">
                                        {{ $item['title'] }}
                                    </h3>
                                </div>
                                <span class="text-xs text-sky-200 font-medium flex items-center gap-1.5">
                                    <i class="fas fa-calendar-alt text-emerald-400"></i>
                                    {{ $item['timestamp']->format('d M Y - H:i') }} ({{ $item['timestamp']->diffForHumans() }})
                                </span>
                            </div>

                            @if($canViewMedical)
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-xs">
                                    <div class="bg-white bg-opacity-5 p-3 rounded-lg border border-white/10">
                                        <span class="text-sky-200 block text-[11px] font-semibold mb-1">Nama Pasien:</span>
                                        <strong class="text-emerald-300 text-sm">{{ $op->nama_pasien }}</strong>
                                    </div>
                                    <div class="bg-white bg-opacity-5 p-3 rounded-lg border border-white/10">
                                        <span class="text-sky-200 block text-[11px] font-semibold mb-1">Diagnosa Medis:</span>
                                        <span class="text-white">{{ $op->diagnosa ?? '-' }}</span>
                                    </div>
                                    <div class="bg-white bg-opacity-5 p-3 rounded-lg border border-white/10 sm:col-span-2 lg:col-span-1">
                                        <span class="text-sky-200 block text-[11px] font-semibold mb-1">Peran Dalam Tim:</span>
                                        <span class="text-sky-300 font-medium">{{ $item['description'] }}</span>
                                    </div>
                                    @if($op->tindakan_operasi)
                                        <div class="bg-white bg-opacity-5 p-3 rounded-lg border border-white/10 sm:col-span-2 lg:col-span-3">
                                            <span class="text-sky-200 block text-[11px] font-semibold mb-1">Tindakan/Detail Operasi:</span>
                                            <span class="text-white">{{ $op->tindakan_operasi }}</span>
                                        </div>
                                    @endif
                                    @if($op->catatan)
                                        <div class="bg-white bg-opacity-5 p-3 rounded-lg border border-white/10 sm:col-span-2 lg:col-span-3">
                                            <span class="text-sky-200 block text-[11px] font-semibold mb-1">Catatan Medis Tambahan:</span>
                                            <span class="text-white italic">"{{ $op->catatan }}"</span>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="flex items-center gap-2 p-3 rounded-lg bg-rose-500 bg-opacity-20 border border-rose-500/30 text-rose-300 text-xs">
                                    <i class="fas fa-lock text-sm"></i>
                                    <span>Detail medis disensor (Memerlukan izin khusus melihat rekam medis pasien).</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-12 text-center text-sky-200 border border-dashed border-white/20 rounded-xl">
                    <i class="fas fa-procedures text-3xl mb-3 block text-sky-400"></i>
                    <p class="text-sm font-medium">Belum ada rekam operasi medis yang tercatat oleh anggota ini.</p>
                </div>
            @endif
        </div>

        <!-- Detail Section 2: Surat/Form Ditangani -->
        <div x-show="activeTab === 'forms'" 
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 -translate-y-2 scale-98"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="bg-white bg-opacity-10 backdrop-blur-md border border-white border-opacity-20 rounded-2xl shadow-xl p-6 sm:p-8 space-y-6">
            
            <div class="flex items-center justify-between border-b border-white border-opacity-10 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-500 bg-opacity-20 text-indigo-300 border border-indigo-400/30 flex items-center justify-center">
                        <i class="fas fa-file-signature text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white">Detail Penanganan Surat / Form Medis</h2>
                        <p class="text-xs text-sky-200">Daftar surat-surat dan dokumen medis yang telah diproses oleh {{ $user->name }}</p>
                    </div>
                </div>
                
                <button type="button" @click="activeTab = null" class="px-3 py-1.5 rounded-xl bg-white bg-opacity-10 hover:bg-opacity-20 text-white text-xs font-semibold transition-all border border-white/20 flex items-center gap-1.5">
                    <i class="fas fa-times"></i> Tutup
                </button>
            </div>

            @if(count($forms) > 0)
                <div class="grid grid-cols-1 gap-4">
                    @foreach($forms as $item)
                        @php $form = $item['data']; @endphp
                        <div class="bg-black bg-opacity-20 border border-white border-opacity-10 hover:border-indigo-400/40 rounded-xl p-5 transition-all space-y-3">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-white/10 pb-3">
                                <div class="flex items-center gap-2.5">
                                    @php
                                        $statusClass = 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30';
                                        if ($form->status === 'rejected') {
                                            $statusClass = 'bg-rose-500/20 text-rose-300 border-rose-500/30';
                                        } elseif ($form->status === 'cancelled') {
                                            $statusClass = 'bg-amber-500/20 text-amber-300 border-amber-500/30';
                                        }
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border {{ $statusClass }}">
                                        {{ $form->status }}
                                    </span>
                                    <h3 class="font-bold text-white text-base">
                                        {{ $item['title'] }}
                                    </h3>
                                </div>
                                <span class="text-xs text-sky-200 font-medium flex items-center gap-1.5">
                                    <i class="fas fa-calendar-alt text-indigo-400"></i>
                                    {{ $item['timestamp']->format('d M Y - H:i') }} ({{ $item['timestamp']->diffForHumans() }})
                                </span>
                            </div>

                            @if($canViewMedical)
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-xs">
                                    <div class="bg-white bg-opacity-5 p-3 rounded-lg border border-white/10">
                                        <span class="text-sky-200 block text-[11px] font-semibold mb-1">Nama Pemohon/Pasien:</span>
                                        <strong class="text-indigo-300 text-sm">{{ $form->character_name }}</strong>
                                    </div>
                                    <div class="bg-white bg-opacity-5 p-3 rounded-lg border border-white/10">
                                        <span class="text-sky-200 block text-[11px] font-semibold mb-1">Citizen ID Pemohon:</span>
                                        <span class="text-white font-medium">{{ $form->citizen_id ?? '-' }}</span>
                                    </div>
                                    <div class="bg-white bg-opacity-5 p-3 rounded-lg border border-white/10 sm:col-span-2 lg:col-span-1">
                                        <span class="text-sky-200 block text-[11px] font-semibold mb-1">Tindakan Staf:</span>
                                        <span class="text-white">{{ $item['description'] }}</span>
                                    </div>
                                    @if($form->notes)
                                        <div class="bg-white bg-opacity-5 p-3 rounded-lg border border-white/10 sm:col-span-2 lg:col-span-3">
                                            <span class="text-sky-200 block text-[11px] font-semibold mb-1">Catatan Pemrosesan:</span>
                                            <span class="text-white italic">"{{ $form->notes }}"</span>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="flex items-center gap-2 p-3 rounded-lg bg-rose-500 bg-opacity-20 border border-rose-500/30 text-rose-300 text-xs">
                                    <i class="fas fa-lock text-sm"></i>
                                    <span>Detail surat disensor (Memerlukan izin khusus melihat rekam medis pasien).</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-12 text-center text-sky-200 border border-dashed border-white/20 rounded-xl">
                    <i class="fas fa-file-signature text-3xl mb-3 block text-sky-400"></i>
                    <p class="text-sm font-medium">Belum ada surat atau form medis yang diproses oleh anggota ini.</p>
                </div>
            @endif
        </div>

        <!-- Detail Section 3: Hasil Ulasan & Evaluasi Anonim Staf -->
        <div x-show="activeTab === 'evaluations'" 
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 -translate-y-2 scale-98"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             class="bg-white bg-opacity-10 backdrop-blur-md border border-white border-opacity-20 rounded-2xl p-6 shadow-2xl space-y-6">
            
            <div class="flex items-center justify-between border-b border-white border-opacity-20 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500 bg-opacity-20 border border-amber-400 border-opacity-30 text-amber-300 flex items-center justify-center">
                        <i class="fas fa-star text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white">Hasil Ulasan & Evaluasi Anonim Staf</h2>
                        <p class="text-xs text-sky-200">Daftar ulasan & penilaian rating bintang dari anggota staf untuk {{ $user->name }}</p>
                    </div>
                </div>
                
                <button type="button" @click="activeTab = null" class="px-3 py-1.5 rounded-xl bg-white bg-opacity-10 hover:bg-opacity-20 text-white text-xs font-semibold transition-all border border-white/20 flex items-center gap-1.5">
                    <i class="fas fa-times"></i> Tutup
                </button>
            </div>

            @if(count($managerEvaluations) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($managerEvaluations as $review)
                        <div class="bg-black bg-opacity-20 border border-white border-opacity-10 rounded-xl p-5 space-y-3">
                            <div class="flex items-start justify-between gap-3 border-b border-white border-opacity-10 pb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-sky-500 to-cyan-600 text-white flex items-center justify-center text-xs shadow font-bold shrink-0 border border-white border-opacity-20">
                                        <i class="fas fa-user-secret"></i>
                                    </div>
                                    <div>
                                        <span class="text-xs font-extrabold text-amber-300 block flex items-center gap-1.5">
                                            🎭 Staf Medis (Anonim)
                                            @if($canSeeAll)
                                                <span class="px-1.5 py-0.5 rounded bg-amber-500 bg-opacity-20 border border-amber-400 border-opacity-40 text-amber-200 text-[9px] font-bold">
                                                    <i class="fas fa-eye mr-0.5"></i> Admin View
                                                </span>
                                            @endif
                                        </span>

                                        @if($canSeeAll && $review->evaluator)
                                            <div class="my-1 p-2 rounded-lg bg-amber-500 bg-opacity-10 border border-amber-400 border-opacity-30 text-[11px] text-amber-200 space-y-0.5">
                                                <div class="font-bold text-white flex items-center gap-1">
                                                    <i class="fas fa-id-card text-amber-400"></i> Nama Penilai: {{ $review->evaluator->name }}
                                                </div>
                                                <div class="text-[10px] text-amber-200 opacity-90">
                                                    Jabatan: {{ $review->evaluator->role?->name ?? '-' }} &middot; ID: {{ $review->evaluator->staff_id ?? '-' }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <span class="text-[10px] text-slate-300 font-medium bg-white bg-opacity-5 px-2 py-1 rounded-md">
                                    {{ $review->created_at->diffForHumans() }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-1 text-amber-400">
                                    @for($s = 1; $s <= 5; $s++)
                                        @if($s <= $review->rating)
                                            <i class="fas fa-star text-sm"></i>
                                        @else
                                            <i class="far fa-star text-slate-600 text-sm"></i>
                                        @endif
                                    @endfor
                                    <span class="ml-1 text-white font-bold">{{ number_format($review->rating, 1) }}</span>
                                </div>

                                <span class="px-2.5 py-0.5 rounded-full bg-sky-500 bg-opacity-20 border border-sky-400 border-opacity-30 text-sky-200 font-semibold text-[10px]">
                                    {{ $review->kategori }}
                                </span>
                            </div>

                            <div class="text-xs text-slate-200 leading-relaxed bg-white bg-opacity-5 p-3.5 rounded-lg border border-white border-opacity-5">
                                "{!! nl2br(e($review->komentar)) !!}"
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-12 text-center text-sky-200 border border-dashed border-white/20 rounded-xl">
                    <i class="fas fa-comment-slash text-3xl mb-3 block text-sky-400"></i>
                    <p class="text-sm font-medium">Belum ada ulasan evaluasi anonim untuk anggota ini.</p>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection

