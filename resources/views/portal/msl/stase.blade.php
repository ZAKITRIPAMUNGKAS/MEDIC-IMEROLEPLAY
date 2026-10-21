@extends('layouts.app')
@section('title', 'Kelola Stase & Konsulen — Divisi MSL')
@section('content')
<div class="min-h-screen pt-20 pb-10" style="background: linear-gradient(180deg, #0c4a6e 0%, #075985 50%, #0369a1 100%);">
<div class="max-w-6xl mx-auto px-4">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-book-medical text-teal-400"></i> MSL — Kelola Stase &amp; Konsulen
            </h1>
            <p class="text-white/60 text-sm mt-0.5">Daftarkan dokter konsulen stase resmi, tinjau laporan hasil bimbingan, dan terbitkan sertifikat stase resmi ke profil anggota.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('portal.msl.index') }}" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-xl border border-white/20 text-sm flex items-center gap-2 transition-all">
                <i class="fas fa-stethoscope text-teal-300"></i> Sertifikat Visum
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-emerald-500/20 border border-emerald-500/40 rounded-xl text-emerald-300 text-sm flex items-center gap-2">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 p-4 bg-red-500/20 border border-red-500/40 rounded-xl text-red-300 text-sm flex items-center gap-2">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mb-4 p-4 bg-red-500/20 border border-red-500/40 rounded-xl text-red-300 text-sm">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- SECTION 1: PENDAFTARAN & KELOLA KONSULEN STASE --}}
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 mb-6 shadow-xl">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-4 pb-3 border-b border-white/10">
            <div>
                <h3 class="text-white font-bold flex items-center gap-2 text-base">
                    <i class="fas fa-user-md text-teal-400"></i> Daftarkan Dokter Konsulen Stase Resmi
                </h3>
                <p class="text-white/50 text-xs mt-0.5">Dokter yang didaftarkan akan berwenang membimbing stase, menginput laporan evaluasi klinis, dan menyetujui pengajuan stase.</p>
            </div>
            <span class="px-3 py-1 bg-teal-500/20 text-teal-300 border border-teal-500/30 rounded-xl text-xs font-semibold">
                {{ $activeKonsulen->count() }} Konsulen Terdaftar
            </span>
        </div>

        {{-- Form Daftarkan Konsulen --}}
        <form method="POST" action="{{ route('portal.msl.konsulen.assign') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
            @csrf
            <div class="sm:col-span-2">
                <select name="user_id" required class="w-full px-3.5 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-teal-400">
                    <option value="" class="bg-slate-900 text-white/50">— Pilih Dokter / Staf Medis yang akan dijadikan Konsulen —</option>
                    @foreach($staffCandidates as $sc)
                    <option value="{{ $sc->id }}" class="bg-slate-900 text-white">
                        {{ $sc->name }} ({{ $sc->staff_id }}) — {{ $sc->role?->display_name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="w-full py-2.5 px-4 bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-400 hover:to-emerald-500 text-white text-sm font-semibold rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-user-plus"></i> Daftarkan Konsulen Stase
                </button>
            </div>
        </form>

        {{-- Daftar Konsulen Aktif --}}
        <div>
            <div class="text-xs font-bold uppercase tracking-wider text-white/50 mb-3 flex items-center gap-1.5">
                <i class="fas fa-clipboard-check text-teal-400"></i> Daftar Dokter Konsulen Aktif Saat Ini
            </div>
            @if($activeKonsulen->isEmpty())
                <div class="p-4 bg-white/5 rounded-xl text-white/40 text-xs italic text-center">
                    Belum ada dokter konsulen yang didaftarkan secara manual. Sistem saat ini fallback ke seluruh dokter spesialis &amp; manajer medis.
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($activeKonsulen as $ak)
                    <div class="p-3 bg-white/5 border border-white/10 rounded-xl flex items-center justify-between gap-2 hover:bg-white/10 transition-colors">
                        <div>
                            <div class="text-white font-semibold text-sm flex items-center gap-1.5">
                                <i class="fas fa-stethoscope text-teal-400 text-xs"></i> {{ $ak->name }}
                            </div>
                            <div class="text-white/40 text-xs">{{ $ak->staff_id }} &bull; {{ $ak->role?->display_name }}</div>
                        </div>
                        <form method="POST" action="{{ route('portal.msl.konsulen.revoke', $ak) }}" onsubmit="return confirm('Cabut status penugasan konsulen stase untuk {{ addslashes($ak->name) }}?')">
                            @csrf
                            <button type="submit" class="px-2 py-1 bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 rounded-lg text-xs font-medium border border-rose-500/30 transition-all flex items-center gap-1" title="Cabut Status Konsulen">
                                <i class="fas fa-user-minus"></i> Cabut
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
        <form method="GET" class="flex gap-2">
            <select name="status" class="px-3 py-1.5 bg-white/10 border border-white/20 rounded-xl text-white text-xs focus:outline-none">
                <option value="" class="bg-slate-900 text-white">Semua Status</option>
                <option value="approved_konsulen" class="bg-slate-900 text-white" {{ request('status') === 'approved_konsulen' ? 'selected' : '' }}>Laporan Konsulen Masuk (Siap Approve)</option>
                <option value="pending_konsulen" class="bg-slate-900 text-white" {{ request('status') === 'pending_konsulen' ? 'selected' : '' }}>Menunggu Konsulen</option>
                <option value="completed" class="bg-slate-900 text-white" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai / Lulus (Sertifikat Terbit)</option>
                <option value="rejected" class="bg-slate-900 text-white" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
            <button type="submit" class="px-3 py-1.5 bg-teal-500/20 hover:bg-teal-500/30 text-teal-300 rounded-xl border border-teal-500/30 text-xs font-semibold">
                Filter
            </button>
            @if(request('status'))
                <a href="{{ route('portal.msl.stase.index') }}" class="px-2.5 py-1.5 bg-white/10 hover:bg-white/15 text-white/70 rounded-xl text-xs">Reset</a>
            @endif
        </form>
    </div>

    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden shadow-2xl">
        @if($applications->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-white/40">
            <i class="fas fa-book-reader text-4xl mb-3"></i>
            <p class="text-sm">Tidak ada pengajuan stase yang ditemukan.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-white/10 text-xs text-white/50 uppercase tracking-wider">
                    <tr>
                        <th class="text-left px-5 py-3.5">Anggota</th>
                        <th class="text-left px-5 py-3.5">Bidang Stase</th>
                        <th class="text-left px-5 py-3.5">Konsulen Pembimbing</th>
                        <th class="text-left px-5 py-3.5">Laporan &amp; Evaluasi Konsulen</th>
                        <th class="text-left px-5 py-3.5">Status</th>
                        <th class="text-center px-5 py-3.5">Aksi MSL</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($applications as $app)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-5 py-3.5">
                            <div class="text-white font-medium">{{ $app->user?->name }}</div>
                            <div class="text-white/40 text-xs">{{ $app->user?->staff_id }} &bull; {{ $app->user?->role?->display_name }}</div>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="text-blue-300 font-semibold">{{ $app->stase_name }}</span>
                            <div class="text-white/40 text-[11px]">{{ $app->created_at->format('d M Y') }}</div>
                        </td>
                        <td class="px-5 py-3.5 text-white/80">
                            <div class="font-medium">{{ $app->konsulen?->name ?? '—' }}</div>
                            <div class="text-white/40 text-xs">{{ $app->konsulen?->staff_id }}</div>
                        </td>
                        <td class="px-5 py-3.5">
                            @if($app->konsulen_notes)
                                <div class="p-2 bg-black/20 border border-white/10 rounded-lg max-w-[240px]">
                                    <div class="text-[11px] text-white/90 line-clamp-2" title="{{ $app->konsulen_notes }}">
                                        <i class="fas fa-quote-left text-sky-400 text-[9px] mr-1"></i> {{ $app->konsulen_notes }}
                                    </div>
                                    <div class="text-[10px] text-sky-300 font-semibold mt-1 flex items-center justify-between">
                                        <span>Grade: {{ $app->grade ?: 'Lulus' }}</span>
                                        <span class="text-white/40 font-normal">{{ $app->konsulen_approved_at?->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                            @else
                                <span class="text-white/30 text-xs italic">Belum ada laporan dari konsulen</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                @if($app->status === 'approved_konsulen' || $app->status === 'pending_msl') bg-sky-500/20 text-sky-300 border border-sky-500/30
                                @elseif($app->status === 'pending_konsulen') bg-yellow-500/20 text-yellow-300 border border-yellow-500/30
                                @elseif($app->status === 'completed') bg-emerald-500/20 text-emerald-300 border border-emerald-500/30
                                @elseif($app->status === 'rejected') bg-red-500/20 text-red-300 border border-red-500/30
                                @else bg-blue-500/20 text-blue-300 border border-blue-500/30 @endif">
                                {{ $app->status_label }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-center gap-2">
                                {{-- Tombol Verifikasi & Terbitkan Sertifikat --}}
                                @if(in_array($app->status, ['approved_konsulen', 'pending_msl', 'pending_konsulen', 'approved']))
                                <form method="POST" action="{{ route('portal.msl.stase.approve', $app) }}" onsubmit="return confirm('Setujui stase {{ addslashes($app->stase_name) }} dan TERBITKAN SERTIFIKAT KELULUSAN RESMI untuk {{ addslashes($app->user?->name) }}?')">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white text-xs font-semibold rounded-lg shadow-md transition-all flex items-center gap-1.5">
                                        <i class="fas fa-award"></i> Approve &amp; Terbitkan Sertifikat
                                    </button>
                                </form>

                                {{-- Tombol Tolak --}}
                                <form method="POST" action="{{ route('portal.msl.stase.reject', $app) }}" onsubmit="return confirm('Tolak permohonan stase ini?')">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1.5 bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 text-xs font-semibold rounded-lg border border-rose-500/30 transition-all flex items-center gap-1">
                                        <i class="fas fa-times"></i> Tolak
                                    </button>
                                </form>
                                @endif

                                {{-- Jika Selesai: Tampilkan Tautan Gambar Sertifikat --}}
                                @if($app->status === 'completed')
                                    @if($app->certification_id)
                                        <a href="{{ route('portal.cert.image', $app->certification_id) }}" target="_blank"
                                           class="px-3 py-1.5 bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 border border-emerald-500/30 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition-all">
                                            <i class="fas fa-file-invoice text-teal-300"></i> Lihat Sertifikat (SVG)
                                        </a>
                                    @else
                                        <span class="text-xs text-emerald-300 font-medium">Lulus</span>
                                    @endif
                                @endif

                                @if($app->status === 'rejected')
                                    <span class="text-xs text-rose-300/70 italic">Ditolak</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-white/10">{{ $applications->links() }}</div>
        @endif
    </div>
</div>
</div>
@endsection
