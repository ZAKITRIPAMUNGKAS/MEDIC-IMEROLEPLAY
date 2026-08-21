@extends('layouts.app')

@section('title', 'Penilaian & Evaluasi Manajer (Anonim) - iMe Roleplay')

@section('content')
<div class="min-h-screen bg-slate-900 text-white pt-6 pb-16 px-4 sm:px-6 lg:px-8 font-sans">
    <div class="max-w-7xl mx-auto space-y-8">

        <!-- HEADER BANNER (DEFAULT SKY BLUE MEDICAL THEME) -->
        <div class="relative rounded-3xl p-6 sm:p-8 border border-white/20 shadow-2xl overflow-hidden" style="background: linear-gradient(135deg, #0c4a6e 0%, #075985 50%, #0284c7 100%);">
            <div class="absolute -right-10 -bottom-10 opacity-10 text-9xl text-white pointer-events-none">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="relative z-10 space-y-3">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/15 backdrop-blur-md border border-white/25 text-sky-200 text-xs font-bold uppercase tracking-wider shadow-sm">
                    <i class="fas fa-user-secret text-amber-300"></i> 100% Rahasia & Anonim &middot; Tema Default Medis
                </div>
                <h1 class="text-2xl sm:text-4xl font-black text-white tracking-tight">
                    Penilaian & Evaluasi Manajer
                </h1>
                <p class="text-sm text-sky-100/90 max-w-2xl leading-relaxed">
                    Wadah evaluasi bagi seluruh anggota staf medic untuk memberikan nilai bintang (1 - 5) dan masukan konstruktif bagi jajaran <strong class="text-white">Manajer & Staff Manajer</strong>. Identitas penilai <strong class="text-amber-300">dijamin 100% disembunyikan (Anonim)</strong> demi kebebasan berpendapat.
                </p>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-500/20 border border-emerald-500/40 rounded-2xl text-emerald-200 text-sm font-semibold flex items-center gap-3 shadow-lg">
                <i class="fas fa-check-circle text-xl text-emerald-400"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-red-500/20 border border-red-500/40 rounded-2xl text-red-200 text-sm font-semibold flex items-center gap-3 shadow-lg">
                <i class="fas fa-exclamation-triangle text-xl text-red-400"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- TOP BAR & NEW EVALUATION BUTTON -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white/10 backdrop-blur-md p-5 rounded-2xl border border-white/15">
            <div>
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fas fa-star text-amber-300"></i> Evaluasi Kinerja Manajer EMS
                </h2>
                <p class="text-xs text-sky-200/80">Pilih manajer di bawah ini untuk melihat ulasan atau memberikan penilaian bintang.</p>
            </div>
            <button onclick="openEvaluationModal()" class="px-6 py-3 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white font-bold rounded-xl text-xs transition-all shadow-lg shadow-sky-500/25 flex items-center justify-center gap-2 shrink-0">
                <i class="fas fa-pen text-amber-300"></i> Beri Penilaian Baru
            </button>
        </div>

        <!-- SECTION 1: EMS ALTA HOSPITAL MANAGERS -->
        <div class="space-y-4">
            <div class="flex items-center justify-between border-b border-white/15 pb-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-sky-500/30 border border-sky-400/40 text-sky-300 flex items-center justify-center text-base font-bold shadow-inner">
                        <i class="fas fa-hospital"></i>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-white">Manajer & Staff Manajer — EMS Alta Hospital</h2>
                        <p class="text-xs text-sky-300/80">Jajaran Manajemen Medis Utama EMS Alta Hospital</p>
                    </div>
                </div>
                <span class="px-3 py-1 bg-sky-500/20 border border-sky-400/30 rounded-full text-sky-200 text-xs font-bold">
                    {{ $altaManagers->count() }} Manajer
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse($altaManagers as $manager)
                    @php
                        $avg = round($manager->avg_rating ?? 0, 1);
                        $count = $manager->reviews_count ?? 0;
                    @endphp
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-5 border border-white/15 hover:border-sky-400/50 transition-all duration-300 flex flex-col justify-between space-y-4 group">
                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-slate-800 border-2 border-sky-400/50 overflow-hidden shrink-0 flex items-center justify-center text-xl font-black text-sky-300 shadow-md">
                                @if($manager->avatar)
                                    <img src="{{ asset('storage/' . $manager->avatar) }}" alt="{{ $manager->name }}" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr($manager->name, 0, 2)) }}
                                @endif
                            </div>

                            <div class="space-y-1 min-w-0 flex-1">
                                <span class="px-2 py-0.5 rounded-md bg-sky-500/25 border border-sky-400/40 text-sky-200 font-bold text-[10px] uppercase tracking-wider">
                                    {{ $manager->role?->name ?? 'Manajer' }}
                                </span>
                                <h3 class="text-base font-bold text-white truncate group-hover:text-sky-300 transition-colors" title="{{ $manager->name }}">
                                    {{ $manager->name }}
                                </h3>
                                <p class="text-xs text-sky-200/70 truncate">ID: {{ $manager->staff_id ?? '-' }} &middot; Alta Hospital</p>
                            </div>
                        </div>

                        <!-- Rating & Review Count -->
                        <div class="pt-3 border-t border-white/10 flex items-center justify-between text-xs">
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

                            <span class="text-slate-300 text-[11px] font-semibold bg-white/5 px-2 py-0.5 rounded-md">
                                {{ $count }} Evaluasi
                            </span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-2 pt-1">
                            <button onclick="openEvaluationModal({{ $manager->id }}, '{{ addslashes($manager->name) }}')" class="w-full py-2 bg-sky-500/20 hover:bg-sky-500/40 text-sky-200 font-bold rounded-xl text-xs border border-sky-400/30 transition-all text-center flex items-center justify-center gap-1.5">
                                <i class="fas fa-pen text-amber-300"></i> Beri Penilaian
                            </button>
                            <a href="{{ route('staff.manager-evaluations.index', ['manager_id' => $manager->id]) }}" class="px-3 py-2 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-xl text-xs border border-white/15 transition-all" title="Lihat Ulasan">
                                <i class="fas fa-comments"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white/5 rounded-2xl p-8 text-center text-slate-400 border border-white/10">
                        <i class="fas fa-users-slash text-3xl text-slate-600 mb-2"></i>
                        <p>Belum ada data Manajer Alta Hospital yang terdaftar.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- SECTION 2: ROXWOOD HOSPITAL (RH) MANAGERS -->
        <div class="space-y-4 pt-6">
            <div class="flex items-center justify-between border-b border-white/15 pb-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-purple-500/30 border border-purple-400/40 text-purple-300 flex items-center justify-center text-base font-bold shadow-inner">
                        <i class="fas fa-clinic-medical"></i>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-white">Manajer & Staff Manajer — Roxwood Hospital (RH)</h2>
                        <p class="text-xs text-purple-200/80">Jajaran Manajemen Medis Roxwood Hospital</p>
                    </div>
                </div>
                <span class="px-3 py-1 bg-purple-500/20 border border-purple-400/30 rounded-full text-purple-200 text-xs font-bold">
                    {{ $roxwoodManagers->count() }} Manajer
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse($roxwoodManagers as $manager)
                    @php
                        $avg = round($manager->avg_rating ?? 0, 1);
                        $count = $manager->reviews_count ?? 0;
                    @endphp
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-5 border border-white/15 hover:border-purple-400/50 transition-all duration-300 flex flex-col justify-between space-y-4 group">
                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-slate-800 border-2 border-purple-400/50 overflow-hidden shrink-0 flex items-center justify-center text-xl font-black text-purple-300 shadow-md">
                                @if($manager->avatar)
                                    <img src="{{ asset('storage/' . $manager->avatar) }}" alt="{{ $manager->name }}" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr($manager->name, 0, 2)) }}
                                @endif
                            </div>

                            <div class="space-y-1 min-w-0 flex-1">
                                <span class="px-2 py-0.5 rounded-md bg-purple-500/25 border border-purple-400/40 text-purple-200 font-bold text-[10px] uppercase tracking-wider">
                                    {{ $manager->role?->name ?? 'Manajer RH' }}
                                </span>
                                <h3 class="text-base font-bold text-white truncate group-hover:text-purple-300 transition-colors" title="{{ $manager->name }}">
                                    {{ $manager->name }}
                                </h3>
                                <p class="text-xs text-purple-200/70 truncate">ID: {{ $manager->staff_id ?? '-' }} &middot; Roxwood Hospital</p>
                            </div>
                        </div>

                        <!-- Rating & Review Count -->
                        <div class="pt-3 border-t border-white/10 flex items-center justify-between text-xs">
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

                            <span class="text-slate-300 text-[11px] font-semibold bg-white/5 px-2 py-0.5 rounded-md">
                                {{ $count }} Evaluasi
                            </span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-2 pt-1">
                            <button onclick="openEvaluationModal({{ $manager->id }}, '{{ addslashes($manager->name) }}')" class="w-full py-2 bg-purple-500/20 hover:bg-purple-500/40 text-purple-200 font-bold rounded-xl text-xs border border-purple-400/30 transition-all text-center flex items-center justify-center gap-1.5">
                                <i class="fas fa-pen text-amber-300"></i> Beri Penilaian
                            </button>
                            <a href="{{ route('staff.manager-evaluations.index', ['manager_id' => $manager->id]) }}" class="px-3 py-2 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-xl text-xs border border-white/15 transition-all" title="Lihat Ulasan">
                                <i class="fas fa-comments"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white/5 rounded-2xl p-8 text-center text-slate-400 border border-white/10">
                        <i class="fas fa-users-slash text-3xl text-slate-600 mb-2"></i>
                        <p>Belum ada data Manajer Roxwood Hospital yang terdaftar.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- REVIEWS & COMMENTS FEED SECTION -->
        <div class="space-y-4 pt-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-white/15 pb-3">
                <div>
                    <h2 class="text-lg font-bold text-white flex items-center gap-2">
                        <i class="fas fa-comments text-sky-400"></i> Ulasan & Evaluasi Anonim Staf
                    </h2>
                    @if($selectedManager)
                        <p class="text-xs text-sky-300">Menampilkan ulasan evaluasi untuk: <strong>{{ $selectedManager->name }}</strong></p>
                    @endif
                </div>

                @if($selectedManagerId)
                    <a href="{{ route('staff.manager-evaluations.index') }}" class="text-xs font-bold text-sky-300 hover:underline flex items-center gap-1 bg-white/10 px-3 py-1.5 rounded-lg border border-white/15">
                        <i class="fas fa-filter"></i> Tampilkan Semua Ulasan
                    </a>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($reviews as $review)
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-5 border border-white/10 space-y-3 relative overflow-hidden">
                        <!-- Top Row: Anonymous Avatar & Manager Target -->
                        <div class="flex items-start justify-between gap-3 border-b border-white/10 pb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-sky-500 to-blue-600 text-white flex items-center justify-center text-sm shadow-md font-bold shrink-0 border border-white/20">
                                    <i class="fas fa-user-secret"></i>
                                </div>
                                <div>
                                    <span class="text-xs font-extrabold text-amber-300 block flex items-center gap-1">
                                        🎭 Staf Medis (Anonim)
                                    </span>
                                    <span class="text-[11px] text-slate-300 block">
                                        Menilai: <strong class="text-white">{{ $review->manager?->name ?? 'Manajer' }}</strong>
                                        <span class="text-[10px] text-sky-300">({{ $review->manager?->role?->name ?? 'Manajer' }})</span>
                                    </span>
                                </div>
                            </div>

                            <span class="px-2 py-1 bg-white/5 border border-white/10 rounded-lg text-[10px] font-semibold text-slate-300">
                                {{ $review->created_at->diffForHumans() }}
                            </span>
                        </div>

                        <!-- Rating Stars & Category -->
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

                            <span class="px-2.5 py-0.5 rounded-full bg-sky-500/20 border border-sky-400/30 text-sky-200 font-semibold text-[10px]">
                                {{ $review->kategori }}
                            </span>
                        </div>

                        <!-- Comment Content -->
                        <div class="text-xs text-slate-200 leading-relaxed bg-white/5 p-3 rounded-xl border border-white/5">
                            "{!! nl2br(e($review->komentar)) !!}"
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white/5 rounded-2xl p-8 text-center text-slate-400 border border-white/10">
                        <i class="fas fa-comment-slash text-3xl text-slate-600 mb-2"></i>
                        <p>Belum ada masukan evaluasi anonim yang dikirimkan.</p>
                    </div>
                @endforelse
            </div>

            <div class="pt-2">
                {{ $reviews->links() }}
            </div>
        </div>

    </div>
</div>

<!-- EVALUATION SUBMISSION MODAL -->
<div id="evaluationModal" class="fixed inset-0 z-[99999] hidden bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-white/20 rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-6 shadow-2xl relative text-white">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-white/10 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-sky-500/20 text-sky-300 border border-sky-400/30 flex items-center justify-center text-lg font-bold">
                    <i class="fas fa-star text-amber-300"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white">Beri Evaluasi Manajer</h3>
                    <p class="text-xs text-amber-300 font-semibold flex items-center gap-1">
                        <i class="fas fa-lock"></i> Identitas Anda Dijamin Anonim
                    </p>
                </div>
            </div>
            <button onclick="closeEvaluationModal()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 flex items-center justify-center transition-all">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Form -->
        <form action="{{ route('staff.manager-evaluations.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Target Manager Selection -->
            <div>
                <label class="block text-xs font-semibold text-sky-200 mb-1.5">Pilih Manajer / Staff Manajer <span class="text-red-400">*</span></label>
                <select name="manager_id" id="modalManagerId" class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/15 text-sm text-white focus:outline-none focus:border-sky-400 transition-all font-medium" required>
                    <option value="" class="bg-slate-900 text-white">-- Pilih Manajer --</option>
                    <optgroup label="🏥 EMS Alta Hospital" class="bg-slate-900 text-sky-300 font-bold">
                        @foreach($altaManagers as $m)
                            <option value="{{ $m->id }}" class="bg-slate-900 text-white">{{ $m->name }} ({{ $m->role?->name ?? 'Manajer' }})</option>
                        @endforeach
                    </optgroup>
                    <optgroup label="🏩 Roxwood Hospital (RH)" class="bg-slate-900 text-purple-300 font-bold">
                        @foreach($roxwoodManagers as $m)
                            <option value="{{ $m->id }}" class="bg-slate-900 text-white">{{ $m->name }} ({{ $m->role?->name ?? 'Manajer RH' }})</option>
                        @endforeach
                    </optgroup>
                </select>
            </div>

            <!-- STAR RATING WIDGET -->
            <div>
                <label class="block text-xs font-semibold text-sky-200 mb-1.5">Rating Penilaian (1 - 5 Bintang) <span class="text-red-400">*</span></label>
                <input type="hidden" name="rating" id="ratingInput" value="5" required>
                <div class="flex items-center gap-2 p-3 bg-white/5 rounded-xl border border-white/10 justify-center">
                    @for($b = 1; $b <= 5; $b++)
                        <button type="button" onclick="setStarRating({{ $b }})" class="star-btn text-2xl text-amber-400 hover:scale-125 transition-transform" data-star="{{ $b }}">
                            <i class="fas fa-star"></i>
                        </button>
                    @endfor
                    <span id="starRatingLabel" class="ml-2 font-bold text-amber-300 text-sm">5.0 / 5.0 (Sangat Baik)</span>
                </div>
            </div>

            <!-- Category -->
            <div>
                <label class="block text-xs font-semibold text-sky-200 mb-1.5">Kategori Penilaian <span class="text-red-400">*</span></label>
                <select name="kategori" class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/15 text-sm text-white focus:outline-none focus:border-sky-400 transition-all font-medium" required>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" class="bg-slate-900 text-white">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Comment -->
            <div>
                <label class="block text-xs font-semibold text-sky-200 mb-1.5">Komentar & Masukan Evaluasi <span class="text-red-400">*</span></label>
                <textarea name="komentar" rows="4" class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/15 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-sky-400 transition-all font-medium" placeholder="Tuliskan masukan konstruktif, pujian, maupun saran perbaikan untuk manajer bersangkutan..." required></textarea>
            </div>

            <!-- Notice -->
            <div class="p-3 bg-sky-500/10 border border-sky-400/20 rounded-xl text-[11px] text-sky-200/90 leading-relaxed flex items-start gap-2">
                <i class="fas fa-shield-alt text-sky-300 text-sm shrink-0 mt-0.5"></i>
                <span>Sistem secara otomatis menyembunyikan nama dan identitas Anda. Komentar akan dipublikasikan atas nama <strong>🎭 Staf Medis (Anonim)</strong>.</span>
            </div>

            <!-- Submit Button -->
            <div class="pt-2 flex justify-end gap-3">
                <button type="button" onclick="closeEvaluationModal()" class="px-5 py-2.5 bg-white/10 hover:bg-white/20 text-slate-300 font-bold rounded-xl text-xs transition-all">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white font-bold rounded-xl text-xs transition-all shadow-lg shadow-sky-500/20 flex items-center gap-2">
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

function openEvaluationModal(managerId = null, managerName = '') {
    if (managerId) {
        document.getElementById('modalManagerId').value = managerId;
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
