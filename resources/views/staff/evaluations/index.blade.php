@extends('layouts.app')

@section('title', 'Penilaian & Evaluasi Manajer - Portal Medis iMe Roleplay')

@section('content')
<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-8">

        {{-- Header Section (Matching Default Staff Members Theme) --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl sm:text-4xl font-bold text-white mb-2">
                    <i class="fas fa-star text-amber-400 mr-3"></i>Penilaian & Evaluasi Manajer
                </h1>
                <p class="text-sky-200 text-sm sm:text-base">
                    Wadah resmi evaluasi kinerja jajaran Manajer & Staff Manajer dengan jaminan <strong class="text-amber-300">100% Anonim</strong>.
                </p>
            </div>
            
            <button onclick="openEvaluationModal()" class="px-6 py-3 rounded-xl bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white font-bold text-sm shadow-lg shadow-sky-500/20 transition-all flex items-center gap-2">
                <i class="fas fa-pen text-amber-300"></i> Beri Penilaian Baru
            </button>
        </div>

        {{-- Alert Notification --}}
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-500 bg-opacity-20 border border-emerald-500 border-opacity-40 text-emerald-200 text-sm font-semibold flex items-center gap-3">
                <i class="fas fa-check-circle text-emerald-400 text-xl"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-xl bg-red-500 bg-opacity-20 border border-red-500 border-opacity-40 text-red-200 text-sm font-semibold flex items-center gap-3">
                <i class="fas fa-exclamation-triangle text-red-400 text-xl"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Access Badge Bar -->
        <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-2xl border border-white border-opacity-20 p-4 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2 text-sky-200">
                <i class="fas fa-user-shield text-amber-300 text-base"></i>
                <span>Status Identitas: <strong class="text-amber-300">🎭 Staf Medis (Anonim)</strong> &middot; Nama & ID Anda disembunyikan.</span>
            </div>
            
            <div class="flex items-center gap-2">
                @if($canSeeAll)
                    <span class="px-3 py-1 bg-gradient-to-r from-amber-500 to-orange-500 text-white text-[11px] font-bold rounded-lg shadow">
                        <i class="fas fa-crown mr-1"></i> Akses Administrator (Semua Instansi)
                    </span>
                @elseif(auth()->user()->isRoxwood())
                    <span class="px-3 py-1 bg-emerald-600 text-white text-[11px] font-bold rounded-lg shadow">
                        <i class="fas fa-hospital-alt mr-1 text-emerald-200"></i> Instansi: Roxwood Hospital
                    </span>
                @else
                    <span class="px-3 py-1 bg-cyan-600 text-white text-[11px] font-bold rounded-lg shadow">
                        <i class="fas fa-hospital mr-1 text-cyan-200"></i> Instansi: Alta Hospital
                    </span>
                @endif
            </div>
        </div>

        <!-- SEARCH BAR FORM -->
        <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-2xl border border-white border-opacity-20 p-4 sm:p-5">
            <form action="{{ route('staff.manager-evaluations.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3">
                <div class="relative flex-1 w-full">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-sky-300/70 text-sm"></i>
                    <input type="text" 
                           name="search" 
                           value="{{ $search ?? '' }}" 
                           placeholder="Cari manajer berdasarkan Nama, Staff ID, atau Jabatan..." 
                           class="w-full pl-11 pr-4 py-3 rounded-xl bg-slate-950/60 border border-white/20 text-white placeholder-sky-200/50 text-xs sm:text-sm font-medium focus:outline-none focus:ring-2 focus:ring-sky-400 transition-all">
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white text-xs font-bold rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-search"></i> Cari Manajer
                    </button>

                    @if(!empty($search))
                        <a href="{{ route('staff.manager-evaluations.index') }}" class="px-4 py-3 bg-white/10 hover:bg-white/20 text-white text-xs font-semibold rounded-xl border border-white/20 transition-all flex items-center gap-1.5 whitespace-nowrap">
                            <i class="fas fa-times"></i> Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- SECTION 1: ALTA HOSPITAL MANAGERS -->
        @if($altaManagers->count() > 0 || ($canSeeAll || auth()->user()->isAlta()))
        <div class="space-y-4">
            <div class="flex items-center justify-between border-b border-white border-opacity-20 pb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-cyan-500 bg-opacity-20 border border-cyan-400 border-opacity-30 text-cyan-300 flex items-center justify-center text-lg font-bold">
                        <i class="fas fa-hospital"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white">Alta Hospital — Manajer & Staff Manajer</h2>
                        <p class="text-xs text-sky-200">Jajaran Manajemen Medis EMS Alta Hospital</p>
                    </div>
                </div>
                <span class="px-3 py-1 bg-cyan-500 bg-opacity-20 border border-cyan-400 border-opacity-30 rounded-full text-cyan-200 text-xs font-bold">
                    {{ $altaManagers->count() }} Manajer
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($altaManagers as $manager)
                    @php
                        $avg = round($manager->avg_rating ?? 0, 1);
                        $count = $manager->reviews_count ?? 0;
                    @endphp
                    <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-2xl p-6 border border-white border-opacity-20 hover:border-sky-400 transition-all duration-300 flex flex-col justify-between space-y-4 group">
                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-slate-800 border-2 border-sky-400 border-opacity-50 overflow-hidden shrink-0 flex items-center justify-center text-xl font-black text-sky-300 shadow-md">
                                @if($manager->avatar)
                                    <img src="{{ asset('storage/' . $manager->avatar) }}" alt="{{ $manager->name }}" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr($manager->name, 0, 2)) }}
                                @endif
                            </div>

                            <div class="space-y-1 min-w-0 flex-1">
                                <span class="px-2.5 py-0.5 rounded-md bg-cyan-600 text-white font-bold text-[10px] uppercase tracking-wider">
                                    {{ $manager->role?->name ?? 'Manajer' }}
                                </span>
                                <h3 class="text-base font-bold text-white truncate group-hover:text-sky-300 transition-colors" title="{{ $manager->name }}">
                                    {{ $manager->name }}
                                </h3>
                                <p class="text-xs text-sky-200 truncate">ID: {{ $manager->staff_id ?? '-' }} &middot; Alta Hospital</p>
                            </div>
                        </div>

                        <!-- Rating & Review Count -->
                        <div class="pt-3 border-t border-white border-opacity-10 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-1.5">
                                <div class="flex text-amber-400 text-sm">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($avg))
                                            <i class="fas fa-star"></i>
                                        @elseif($i - 0.5 <= $avg)
                                            <i class="fas fa-star-half-alt"></i>
                                        @else
                                            <i class="far fa-star text-slate-600"></i>
                                        @endif
                                    @endfor
                                </div>
                                <span class="font-extrabold text-white text-sm">{{ $avg > 0 ? number_format($avg, 1) : '0.0' }}</span>
                                <span class="text-slate-400 text-[11px]">/ 5.0</span>
                            </div>

                            <span class="text-sky-200 text-[11px] font-semibold bg-white bg-opacity-10 px-2.5 py-1 rounded-lg">
                                {{ $count }} Evaluasi
                            </span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-2 pt-1">
                            <button onclick="openEvaluationModal({{ $manager->id }}, '{{ addslashes($manager->name) }}', '{{ addslashes($manager->role?->name ?? "Manajer") }}', 'alta')" class="w-full py-2.5 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white font-bold rounded-xl text-xs transition-all text-center flex items-center justify-center gap-1.5 shadow">
                                <i class="fas fa-pen text-amber-300"></i> Beri Penilaian
                            </button>
                            <a href="{{ route('staff.members.show', $manager->id) }}" class="px-3.5 py-2.5 bg-white bg-opacity-10 hover:bg-opacity-20 text-white font-semibold rounded-xl text-xs border border-white border-opacity-20 transition-all" title="Lihat Profil">
                                <i class="fas fa-user-circle"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white bg-opacity-5 rounded-2xl p-8 text-center text-slate-400 border border-white border-opacity-10">
                        <i class="fas fa-users-slash text-3xl text-slate-600 mb-2"></i>
                        <p>Belum ada data Manajer Alta Hospital yang terdaftar.</p>
                    </div>
                @endforelse
            </div>
        </div>
        @endif

        <!-- SECTION 2: ROXWOOD HOSPITAL (RH) MANAGERS -->
        @if($roxwoodManagers->count() > 0 || ($canSeeAll || auth()->user()->isRoxwood()))
        <div class="space-y-4 pt-6">
            <div class="flex items-center justify-between border-b border-white border-opacity-20 pb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500 bg-opacity-20 border border-emerald-400 border-opacity-30 text-emerald-300 flex items-center justify-center text-lg font-bold">
                        <i class="fas fa-hospital-alt"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white">Roxwood Hospital (RH) — Manajer & Staff Manajer</h2>
                        <p class="text-xs text-emerald-200">Jajaran Manajemen Medis Roxwood Hospital</p>
                    </div>
                </div>
                <span class="px-3 py-1 bg-emerald-500 bg-opacity-20 border border-emerald-400 border-opacity-30 rounded-full text-emerald-200 text-xs font-bold">
                    {{ $roxwoodManagers->count() }} Manajer
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($roxwoodManagers as $manager)
                    @php
                        $avg = round($manager->avg_rating ?? 0, 1);
                        $count = $manager->reviews_count ?? 0;
                    @endphp
                    <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-2xl p-6 border border-white border-opacity-20 hover:border-emerald-400 transition-all duration-300 flex flex-col justify-between space-y-4 group">
                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-slate-800 border-2 border-emerald-400 border-opacity-50 overflow-hidden shrink-0 flex items-center justify-center text-xl font-black text-emerald-300 shadow-md">
                                @if($manager->avatar)
                                    <img src="{{ asset('storage/' . $manager->avatar) }}" alt="{{ $manager->name }}" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr($manager->name, 0, 2)) }}
                                @endif
                            </div>

                            <div class="space-y-1 min-w-0 flex-1">
                                <span class="px-2.5 py-0.5 rounded-md bg-emerald-600 text-white font-bold text-[10px] uppercase tracking-wider">
                                    {{ $manager->role?->name ?? 'Manajer RH' }}
                                </span>
                                <h3 class="text-base font-bold text-white truncate group-hover:text-emerald-300 transition-colors" title="{{ $manager->name }}">
                                    {{ $manager->name }}
                                </h3>
                                <p class="text-xs text-emerald-200 truncate">ID: {{ $manager->staff_id ?? '-' }} &middot; Roxwood Hospital</p>
                            </div>
                        </div>

                        <!-- Rating & Review Count -->
                        <div class="pt-3 border-t border-white border-opacity-10 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-1.5">
                                <div class="flex text-amber-400 text-sm">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($avg))
                                            <i class="fas fa-star"></i>
                                        @elseif($i - 0.5 <= $avg)
                                            <i class="fas fa-star-half-alt"></i>
                                        @else
                                            <i class="far fa-star text-slate-600"></i>
                                        @endif
                                    @endfor
                                </div>
                                <span class="font-extrabold text-white text-sm">{{ $avg > 0 ? number_format($avg, 1) : '0.0' }}</span>
                                <span class="text-slate-400 text-[11px]">/ 5.0</span>
                            </div>

                            <span class="text-emerald-200 text-[11px] font-semibold bg-white bg-opacity-10 px-2.5 py-1 rounded-lg">
                                {{ $count }} Evaluasi
                            </span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-2 pt-1">
                            <button onclick="openEvaluationModal({{ $manager->id }}, '{{ addslashes($manager->name) }}', '{{ addslashes($manager->role?->name ?? "Manajer") }}', 'roxwood')" class="w-full py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold rounded-xl text-xs transition-all text-center flex items-center justify-center gap-1.5 shadow">
                                <i class="fas fa-pen text-amber-300"></i> Beri Penilaian
                            </button>
                            <a href="{{ route('staff.members.show', $manager->id) }}" class="px-3.5 py-2.5 bg-white bg-opacity-10 hover:bg-opacity-20 text-white font-semibold rounded-xl text-xs border border-white border-opacity-20 transition-all" title="Lihat Profil">
                                <i class="fas fa-user-circle"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white bg-opacity-5 rounded-2xl p-8 text-center text-slate-400 border border-white border-opacity-10">
                        <i class="fas fa-users-slash text-3xl text-slate-600 mb-2"></i>
                        <p>Belum ada data Manajer Roxwood Hospital yang terdaftar.</p>
                    </div>
                @endforelse
            </div>
        </div>
        @endif

    </div>
</div>

<!-- EVALUATION SUBMISSION MODAL (CLEANED UP & NEAT) -->
<div id="evaluationModal" class="fixed inset-0 z-[99999] hidden bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-white border-opacity-20 rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-5 shadow-2xl relative text-white">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-white border-opacity-10 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-sky-500 bg-opacity-20 text-sky-300 border border-sky-400 border-opacity-30 flex items-center justify-center text-lg font-bold">
                    <i class="fas fa-star text-amber-300"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white">Beri Evaluasi Manajer</h3>
                    <p class="text-xs text-amber-300 font-semibold flex items-center gap-1">
                        <i class="fas fa-lock"></i> Identitas Anda Dijamin Anonim
                    </p>
                </div>
            </div>
            <button onclick="closeEvaluationModal()" class="w-8 h-8 rounded-xl bg-white bg-opacity-10 hover:bg-opacity-20 text-slate-300 flex items-center justify-center transition-all">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Form -->
        <form action="{{ route('staff.manager-evaluations.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Hidden Target Manager ID -->
            <input type="hidden" name="manager_id" id="modalManagerId" required>

            <!-- Target Manager Info Card (Replaces Select Dropdown) -->
            <div id="modalTargetManagerBox" class="p-3.5 bg-sky-500/10 border border-sky-400/30 rounded-2xl flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-slate-800 border border-sky-400/40 flex items-center justify-center text-sky-300 font-bold text-base shrink-0">
                    <i class="fas fa-user-md"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <span class="text-[10px] uppercase font-bold text-sky-300 tracking-wider block" id="modalTargetRole">Staff Manager</span>
                    <h4 class="text-sm sm:text-base font-bold text-white truncate" id="modalTargetName">Nama Manajer</h4>
                </div>
            </div>

            <!-- Fallback Select Container (Only shown if opened without target) -->
            <div id="modalManagerSelectContainer" class="hidden">
                <label class="block text-xs font-semibold text-sky-200 mb-1.5">Pilih Manajer / Staff Manajer <span class="text-red-400">*</span></label>
                <select id="modalManagerSelect" class="w-full px-4 py-3 rounded-xl bg-white bg-opacity-10 border border-white border-opacity-20 text-sm text-white focus:outline-none focus:border-sky-400 transition-all font-medium" onchange="document.getElementById('modalManagerId').value = this.value">
                    <option value="" class="bg-slate-900 text-white">-- Pilih Manajer --</option>
                    @if($altaManagers->count() > 0)
                        <optgroup label="🏥 Alta Hospital" class="bg-slate-900 text-cyan-300 font-bold">
                            @foreach($altaManagers as $m)
                                <option value="{{ $m->id }}" class="bg-slate-900 text-white">{{ $m->name }} ({{ $m->role?->name ?? 'Manajer' }})</option>
                            @endforeach
                        </optgroup>
                    @endif
                    @if($roxwoodManagers->count() > 0)
                        <optgroup label="🏥 Roxwood Hospital" class="bg-slate-900 text-emerald-300 font-bold">
                            @foreach($roxwoodManagers as $m)
                                <option value="{{ $m->id }}" class="bg-slate-900 text-white">{{ $m->name }} ({{ $m->role?->name ?? 'Manajer RH' }})</option>
                            @endforeach
                        </optgroup>
                    @endif
                </select>
            </div>

            <!-- STAR RATING WIDGET -->
            <div>
                <label class="block text-xs font-semibold text-sky-200 mb-1.5">Rating Penilaian (1 - 5 Bintang) <span class="text-red-400">*</span></label>
                <input type="hidden" name="rating" id="ratingInput" value="5" required>
                <div class="flex items-center gap-2 p-3 bg-white bg-opacity-5 rounded-xl border border-white border-opacity-10 justify-center">
                    @for($b = 1; $b <= 5; $b++)
                        <button type="button" onclick="setStarRating({{ $b }})" class="star-btn text-2xl text-amber-400 hover:scale-125 transition-transform" data-star="{{ $b }}">
                            <i class="fas fa-star"></i>
                        </button>
                    @endfor
                    <span id="starRatingLabel" class="ml-2 font-bold text-amber-300 text-sm">5.0 / 5.0 (Sangat Baik)</span>
                </div>
            </div>

            <!-- Multi Category Selection Checkboxes -->
            <div>
                <label class="block text-xs font-semibold text-sky-200 mb-2">
                    Kategori Penilaian <span class="text-xs text-amber-300 font-normal">(Bisa pilih lebih dari 1)</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 bg-white bg-opacity-5 p-3 rounded-xl border border-white border-opacity-10 max-h-48 overflow-y-auto">
                    @foreach($categories as $index => $cat)
                        <label class="flex items-center gap-2.5 p-2 rounded-lg bg-white bg-opacity-5 hover:bg-opacity-10 border border-white border-opacity-10 cursor-pointer transition-all text-xs text-sky-100 font-medium">
                            <input type="checkbox" name="kategori[]" value="{{ $cat }}" class="w-4 h-4 rounded text-sky-500 focus:ring-sky-400 bg-slate-900 border-white/20" {{ $index === 0 ? 'checked' : '' }}>
                            <span>{{ $cat }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Comment Input Textarea (Refined & Tidy) -->
            <div>
                <label class="block text-xs font-semibold text-sky-200 mb-1.5">Komentar & Masukan Evaluasi <span class="text-red-400">*</span></label>
                <textarea name="komentar" rows="3" class="w-full px-4 py-3 rounded-xl bg-slate-950/60 border border-white/20 text-sm text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:border-transparent transition-all font-medium" placeholder="Tuliskan masukan konstruktif, pujian, maupun saran perbaikan..." required></textarea>
            </div>

            <!-- Notice -->
            <div class="p-3 bg-sky-500 bg-opacity-10 border border-sky-400 border-opacity-20 rounded-xl text-[11px] text-sky-200 leading-relaxed flex items-start gap-2">
                <i class="fas fa-shield-alt text-sky-300 text-sm shrink-0 mt-0.5"></i>
                <span>Sistem secara otomatis menyembunyikan nama dan identitas Anda. Komentar akan dipublikasikan atas nama <strong>🎭 Staf Medis (Anonim)</strong>.</span>
            </div>

            <!-- Submit Button -->
            <div class="pt-2 flex justify-end gap-3">
                <button type="button" onclick="closeEvaluationModal()" class="px-5 py-2.5 bg-white bg-opacity-10 hover:bg-opacity-20 text-slate-300 font-bold rounded-xl text-xs transition-all">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white font-bold rounded-xl text-xs transition-all shadow-lg shadow-sky-500/20 flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i> Kirim Evaluasi Anonim
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentSelectedRating = 5;

const ratingLabels = {
    1: '1.0 / 5.0 (Sangat Kurang)',
    2: '2.0 / 5.0 (Kurang)',
    3: '3.0 / 5.0 (Cukup)',
    4: '4.0 / 5.0 (Baik)',
    5: '5.0 / 5.0 (Sangat Baik)'
};

function openEvaluationModal(managerId = null, managerName = '', managerRole = '', managerHospital = '') {
    const hiddenIdInput = document.getElementById('modalManagerId');
    const targetBox = document.getElementById('modalTargetManagerBox');
    const selectBox = document.getElementById('modalManagerSelectContainer');
    
    if (managerId) {
        hiddenIdInput.value = managerId;
        if (targetBox) {
            targetBox.classList.remove('hidden');
            document.getElementById('modalTargetName').textContent = managerName;
            document.getElementById('modalTargetRole').textContent = managerRole + ' · ' + (managerHospital === 'roxwood' ? 'Roxwood Hospital' : 'Alta Hospital');
        }
        if (selectBox) selectBox.classList.add('hidden');
    } else {
        if (selectBox) {
            selectBox.classList.remove('hidden');
            const selEl = document.getElementById('modalManagerSelect');
            if (selEl && selEl.options.length > 1) {
                selEl.selectedIndex = 1;
                hiddenIdInput.value = selEl.value;
            }
        }
        if (targetBox) targetBox.classList.add('hidden');
    }

    setStarRating(5);
    document.getElementById('evaluationModal').classList.remove('hidden');
}

function closeEvaluationModal() {
    document.getElementById('evaluationModal').classList.add('hidden');
}

function setStarRating(rating) {
    currentSelectedRating = rating;
    document.getElementById('ratingInput').value = rating;
    document.getElementById('starRatingLabel').textContent = ratingLabels[rating] || `${rating}.0 / 5.0`;

    document.querySelectorAll('.star-btn').forEach((btn, index) => {
        const starNum = index + 1;
        const icon = btn.querySelector('i');
        if (starNum <= rating) {
            icon.className = 'fas fa-star text-amber-400';
        } else {
            icon.className = 'far fa-star text-slate-600';
        }
    });
}
</script>
@endpush
