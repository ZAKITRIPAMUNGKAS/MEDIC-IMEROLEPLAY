@extends('layouts.app')
@section('title', 'Kelola Stase & Konsulen — Divisi MSL')
@section('content')
<div class="min-h-screen pt-20 pb-12" style="background: linear-gradient(180deg, #0c4a6e 0%, #075985 50%, #0369a1 100%);">
<div class="max-w-7xl mx-auto px-4 sm:px-6">
    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2.5">
                <i class="fas fa-book-medical text-teal-400"></i> MSL — Kelola Stase &amp; Konsulen
            </h1>
            <p class="text-white/60 text-sm mt-0.5">Daftarkan dokter konsulen stase resmi, tinjau laporan bimbingan klinis, dan terbitkan sertifikat stase resmi ke profil anggota.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('portal.msl.index') }}" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-xl border border-white/20 text-sm flex items-center gap-2 transition-all">
                <i class="fas fa-stethoscope text-teal-300"></i> Sertifikat Visum
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-emerald-500/20 border border-emerald-500/40 rounded-xl text-emerald-300 text-sm flex items-center gap-2 shadow-lg">
        <i class="fas fa-check-circle text-base"></i> {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 p-4 bg-red-500/20 border border-red-500/40 rounded-xl text-red-300 text-sm flex items-center gap-2 shadow-lg">
        <i class="fas fa-exclamation-circle text-base"></i> {{ session('error') }}
    </div>
    @endif

    @if(isset($errors) && $errors->any())
    <div class="mb-4 p-4 bg-red-500/20 border border-red-500/40 rounded-xl text-red-300 text-sm shadow-lg">
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- SECTION 1: PENDAFTARAN & KELOLA KONSULEN STASE --}}
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 mb-8 shadow-xl">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-4 pb-3 border-b border-white/10">
            <div>
                <h3 class="text-white font-bold flex items-center gap-2 text-base">
                    <i class="fas fa-user-md text-teal-400"></i> Daftarkan Dokter Konsulen Stase Resmi
                </h3>
                <p class="text-white/50 text-xs mt-0.5">Dokter yang didaftarkan akan berwenang membimbing stase, menginput laporan evaluasi klinis, dan menyetujui pengajuan stase.</p>
            </div>
            <span class="px-3 py-1 bg-teal-500/20 text-teal-300 border border-teal-500/30 rounded-xl text-xs font-semibold flex items-center gap-1.5">
                <i class="fas fa-shield-alt text-[10px]"></i> {{ $activeKonsulen->count() }} Konsulen Terdaftar
            </span>
        </div>

        {{-- Form Daftarkan Konsulen (Typable / Searchable with Autocomplete) --}}
        <form method="POST" action="{{ route('portal.msl.konsulen.assign') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6 items-start">
            @csrf
            <div class="sm:col-span-2">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-teal-400/80">
                        <i class="fas fa-search text-xs"></i>
                    </div>
                    <input type="text"
                           name="doctor_search"
                           id="doctor_search_input"
                           list="doctorCandidatesList"
                           autocomplete="off"
                           placeholder="Ketik nama dokter atau NIP / Staff ID..."
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-900/60 border border-white/20 rounded-xl text-white text-sm placeholder-white/40 focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition-all"
                           required>
                    <input type="hidden" name="user_id" id="doctor_user_id_hidden">
                    <datalist id="doctorCandidatesList">
                        @foreach($staffCandidates as $sc)
                        <option value="{{ $sc->name }} ({{ $sc->staff_id }})" data-id="{{ $sc->id }}">
                            {{ $sc->doctor_title }}
                        </option>
                        @endforeach
                    </datalist>
                </div>
                <p class="text-white/40 text-[11px] mt-1.5 flex items-center gap-1.5 pl-1">
                    <i class="fas fa-keyboard text-teal-400/70"></i>
                    <span>Nama dokter dapat diketik bebas atau dipilih dari daftar saran pencarian yang tersedia.</span>
                </p>
            </div>
            <div>
                <button type="submit" class="w-full py-2.5 px-4 bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-400 hover:to-emerald-500 text-white text-sm font-semibold rounded-xl shadow-lg transition-all flex items-center justify-center gap-2 h-[42px]">
                    <i class="fas fa-user-plus"></i> Daftarkan Konsulen
                </button>
            </div>
        </form>

        {{-- Daftar Konsulen Aktif --}}
        <div>
            <div class="text-xs font-bold uppercase tracking-wider text-white/50 mb-3 flex items-center gap-1.5">
                <i class="fas fa-clipboard-check text-teal-400"></i> Daftar Dokter Konsulen Aktif Saat Ini
            </div>
            @if($activeKonsulen->isEmpty())
                <div class="p-4 bg-white/5 rounded-xl text-white/40 text-xs italic text-center border border-white/5">
                    Belum ada dokter konsulen yang didaftarkan secara manual. Sistem saat ini fallback ke seluruh dokter spesialis &amp; manajer medis.
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($activeKonsulen as $ak)
                    <div class="p-3 bg-white/5 border border-white/10 rounded-xl flex items-center justify-between gap-2 hover:bg-white/10 transition-colors">
                        <div class="min-w-0">
                            <div class="text-white font-semibold text-sm flex items-center gap-1.5 truncate">
                                <i class="fas fa-stethoscope text-teal-400 text-xs shrink-0"></i>
                                <span class="truncate">{{ $ak->name }}</span>
                            </div>
                            <div class="text-white/40 text-xs truncate mt-0.5">
                                <span class="font-mono text-[11px]">{{ $ak->staff_id }}</span>
                                <span class="text-white/20">&bull;</span>
                                <span class="text-teal-300/80">{{ $ak->doctor_title }}</span>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('portal.msl.konsulen.revoke', $ak) }}" onsubmit="return confirm('Cabut status penugasan konsulen stase untuk {{ addslashes($ak->name) }}?')">
                            @csrf
                            <button type="submit" class="px-2.5 py-1 bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 rounded-lg text-xs font-medium border border-rose-500/30 transition-all flex items-center gap-1 whitespace-nowrap" title="Cabut Status Konsulen">
                                <i class="fas fa-user-minus text-[11px]"></i> Cabut
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- SECTION 2: VERIFIKASI PENGAJUAN STASE & TERBITKAN SERTIFIKAT --}}
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <div>
            <h2 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="fas fa-graduation-cap text-teal-400"></i> Antrean Pengajuan Stase &amp; Verifikasi Sertifikat
            </h2>
            <p class="text-white/50 text-xs">Periksa laporan yang diinput dokter konsulen, lalu approve untuk menerbitkan sertifikat stase resmi ke profil anggota.</p>
        </div>

        {{-- Filter Status --}}
        <form method="GET" class="flex items-center gap-2">
            <select name="status" class="px-3 py-1.5 bg-slate-900/60 border border-white/20 rounded-xl text-white text-xs focus:outline-none focus:border-teal-400">
                <option value="" class="bg-slate-900 text-white">Semua Status</option>
                <option value="approved_konsulen" class="bg-slate-900 text-white" {{ request('status') === 'approved_konsulen' ? 'selected' : '' }}>Laporan Konsulen Masuk (Siap Approve)</option>
                <option value="pending_konsulen" class="bg-slate-900 text-white" {{ request('status') === 'pending_konsulen' ? 'selected' : '' }}>Menunggu Konsulen</option>
                <option value="completed" class="bg-slate-900 text-white" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai / Lulus (Sertifikat Terbit)</option>
                <option value="rejected" class="bg-slate-900 text-white" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
            <button type="submit" class="px-3 py-1.5 bg-teal-500/20 hover:bg-teal-500/30 text-teal-300 rounded-xl border border-teal-500/30 text-xs font-semibold transition-all">
                Filter
            </button>
            @if(request('status'))
                <a href="{{ route('portal.msl.stase.index') }}" class="px-2.5 py-1.5 bg-white/10 hover:bg-white/15 text-white/70 rounded-xl text-xs transition-all">Reset</a>
            @endif
        </form>
    </div>

    {{-- Tabel Pengajuan Stase --}}
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden shadow-2xl mb-8">
        @if($applications->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-white/40">
            <i class="fas fa-book-reader text-4xl mb-3 text-white/30"></i>
            <p class="text-sm">Tidak ada pengajuan stase yang ditemukan.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-white/10 text-xs text-white/50 uppercase tracking-wider bg-white/[0.02]">
                    <tr>
                        <th class="text-left px-5 py-3.5 whitespace-nowrap">Anggota</th>
                        <th class="text-left px-5 py-3.5 whitespace-nowrap">Bidang Stase</th>
                        <th class="text-left px-5 py-3.5 whitespace-nowrap">Konsulen Pembimbing</th>
                        <th class="text-left px-5 py-3.5 whitespace-nowrap">Laporan &amp; Evaluasi Konsulen</th>
                        <th class="text-left px-5 py-3.5 whitespace-nowrap">Status</th>
                        <th class="text-center px-5 py-3.5 whitespace-nowrap">Aksi MSL</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($applications as $app)
                    <tr class="hover:bg-white/5 transition-colors">
                        {{-- 1. Anggota --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-teal-500 to-sky-600 flex items-center justify-center text-white text-xs font-bold uppercase shadow shrink-0">
                                    {{ substr($app->user?->name ?? 'U', 0, 2) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="text-white font-medium text-sm leading-tight truncate">{{ $app->user?->name ?? '—' }}</div>
                                    <div class="text-white/40 text-xs flex items-center gap-1.5 mt-0.5 truncate">
                                        <span class="font-mono text-[11px]">{{ $app->user?->staff_id ?? '-' }}</span>
                                        @if(!empty($app->user?->role?->display_name))
                                            <span class="text-white/20">&bull;</span>
                                            <span class="text-teal-300/80">{{ $app->user->role->display_name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- 2. Bidang Stase --}}
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-1.5">
                                <i class="fas fa-stethoscope text-sky-400 text-xs"></i>
                                <span class="text-sky-200 font-semibold text-sm">{{ ucwords(str_replace('_', ' ', $app->stase_name)) }}</span>
                            </div>
                            <div class="text-white/40 text-[11px] flex items-center gap-1 mt-0.5">
                                <i class="far fa-calendar-alt text-[10px]"></i>
                                <span>Diajukan: {{ $app->created_at->format('d M Y') }}</span>
                            </div>
                        </td>

                        {{-- 3. Konsulen Pembimbing --}}
                        <td class="px-5 py-4">
                            @if($app->konsulen)
                                <div class="text-white font-medium text-sm leading-tight flex items-center gap-1.5">
                                    <i class="fas fa-user-md text-teal-400 text-xs shrink-0"></i>
                                    <span>{{ $app->konsulen->name }}</span>
                                </div>
                                <div class="text-white/40 text-xs flex items-center gap-1.5 mt-0.5">
                                    <span class="font-mono text-[11px]">{{ $app->konsulen->staff_id }}</span>
                                    <span class="text-white/20">&bull;</span>
                                    <span class="text-teal-300/80">{{ $app->konsulen->doctor_title }}</span>
                                </div>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-white/5 border border-white/10 rounded-lg text-white/40 text-xs italic">
                                    <i class="fas fa-clock text-[10px] text-amber-400/70"></i> Belum ditugaskan
                                </span>
                            @endif
                        </td>

                        {{-- 4. Laporan & Evaluasi Konsulen --}}
                        <td class="px-5 py-4">
                            @if($app->konsulen_notes)
                                <div class="p-2.5 bg-black/25 border border-white/10 rounded-xl min-w-[210px] max-w-[280px]">
                                    <div class="flex items-center justify-between gap-2 mb-1 pb-1 border-b border-white/5 text-[11px]">
                                        <span class="px-1.5 py-0.5 bg-emerald-500/20 text-emerald-300 font-bold rounded border border-emerald-500/30 text-[10px]">
                                            Grade: {{ $app->grade ?: 'Lulus' }}
                                        </span>
                                        <span class="text-white/40 text-[10px] flex items-center gap-1">
                                            <i class="far fa-clock text-[9px]"></i> {{ $app->konsulen_approved_at?->format('d/m/Y') }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-white/80 line-clamp-2 leading-relaxed" title="{{ $app->konsulen_notes }}">
                                        "{{ $app->konsulen_notes }}"
                                    </div>
                                </div>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white/5 border border-white/10 rounded-lg text-white/40 text-xs">
                                    <i class="fas fa-hourglass-start text-[10px] text-amber-400/80"></i>
                                    <span>Menunggu laporan</span>
                                </span>
                            @endif
                        </td>

                        {{-- 5. Status --}}
                        <td class="px-5 py-4 whitespace-nowrap">
                            @if($app->status === 'approved_konsulen' || $app->status === 'pending_msl')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-sky-500/15 text-sky-300 border border-sky-500/30 whitespace-nowrap">
                                    <span class="w-1.5 h-1.5 rounded-full bg-sky-400 animate-pulse"></span> Laporan Masuk
                                </span>
                            @elseif($app->status === 'pending_konsulen')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-500/15 text-amber-300 border border-amber-500/30 whitespace-nowrap">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Menunggu Konsulen
                                </span>
                            @elseif($app->status === 'completed')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 whitespace-nowrap">
                                    <i class="fas fa-check-circle text-[11px]"></i> Sertifikat Terbit
                                </span>
                            @elseif($app->status === 'rejected')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-500/15 text-rose-300 border border-rose-500/30 whitespace-nowrap">
                                    <i class="fas fa-times-circle text-[11px]"></i> Ditolak
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-500/15 text-blue-300 border border-blue-500/30 whitespace-nowrap">
                                    {{ $app->status_label }}
                                </span>
                            @endif
                        </td>

                        {{-- 6. Aksi MSL --}}
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="flex items-center justify-center gap-2">
                                @if($app->status === 'approved_konsulen' || $app->status === 'pending_msl')
                                    {{-- Laporan Konsulen Sudah Siap: Tombol Utama Hijau Menyala --}}
                                    <form method="POST" action="{{ route('portal.msl.stase.approve', $app) }}" onsubmit="return confirm('Setujui kelulusan stase {{ addslashes($app->stase_name) }} dan TERBITKAN SERTIFIKAT RESMI ke profil {{ addslashes($app->user?->name) }}?')">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white text-xs font-semibold rounded-xl shadow-md transition-all flex items-center gap-1.5">
                                            <i class="fas fa-award"></i> Approve &amp; Terbitkan Sertifikat
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('portal.msl.stase.reject', $app) }}" onsubmit="return confirm('Tolak permohonan stase ini?')">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1.5 bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 text-xs font-semibold rounded-xl border border-rose-500/30 transition-all flex items-center gap-1">
                                            <i class="fas fa-times"></i> Tolak
                                        </button>
                                    </form>
                                @elseif($app->status === 'pending_konsulen')
                                    {{-- Masih Menunggu Konsulen: Tombol Bypass Sekunder yang Rapi --}}
                                    <form method="POST" action="{{ route('portal.msl.stase.approve', $app) }}" onsubmit="return confirm('Konsulen belum mengirim laporan. Anda yakin ingin APPROVE LANGSUNG dan menerbitkan sertifikat stase untuk {{ addslashes($app->user?->name) }}?')">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1.5 bg-teal-500/20 hover:bg-teal-500/30 text-teal-300 text-xs font-semibold rounded-xl border border-teal-500/30 transition-all flex items-center gap-1" title="Setujui langsung tanpa menunggu laporan konsulen">
                                            <i class="fas fa-check-double text-[11px]"></i> Approve Langsung
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('portal.msl.stase.reject', $app) }}" onsubmit="return confirm('Tolak permohonan stase ini?')">
                                        @csrf
                                        <button type="submit" class="px-2 py-1.5 bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 text-xs font-semibold rounded-xl border border-rose-500/30 transition-all flex items-center gap-1">
                                            <i class="fas fa-times"></i> Tolak
                                        </button>
                                    </form>
                                @elseif($app->status === 'completed')
                                    @if($app->certification_id)
                                        <a href="{{ route('portal.cert.image', $app->certification_id) }}" target="_blank"
                                           class="px-3 py-1.5 bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 border border-emerald-500/40 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition-all shadow-sm">
                                            <i class="fas fa-file-invoice text-teal-300"></i> Lihat Sertifikat (SVG)
                                        </a>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-xs text-emerald-300 font-medium">
                                            <i class="fas fa-check"></i> Selesai (Lulus)
                                        </span>
                                    @endif
                                @elseif($app->status === 'rejected')
                                    <span class="inline-flex items-center gap-1 text-xs text-rose-300/70 italic">
                                        <i class="fas fa-ban text-[10px]"></i> Ditolak
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3.5 border-t border-white/10 bg-white/[0.02]">{{ $applications->links() }}</div>
        @endif
    </div>
</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('doctor_search_input');
    const hiddenIdInput = document.getElementById('doctor_user_id_hidden');
    const datalist = document.getElementById('doctorCandidatesList');

    if (searchInput && hiddenIdInput && datalist) {
        searchInput.addEventListener('input', function () {
            const val = this.value.trim();
            const opts = datalist.querySelectorAll('option');
            let matchedId = '';

            opts.forEach(function (opt) {
                if (opt.value === val) {
                    matchedId = opt.getAttribute('data-id') || '';
                }
            });

            hiddenIdInput.value = matchedId;
        });
    }
});
</script>
@endsection
