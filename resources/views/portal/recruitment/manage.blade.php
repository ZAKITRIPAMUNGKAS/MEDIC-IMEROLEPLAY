@extends('layouts.app')

@section('title', 'Kelola Recruitment Medis - Portal Alta Hospital')

@section('content')
<div class="min-h-screen bg-slate-900 text-slate-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Top Header & Breadcrumb -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-white/10 pb-5">
            <div>
                <div class="flex items-center gap-2 text-xs text-amber-400 font-semibold uppercase tracking-wider mb-1">
                    <i class="fas fa-hospital-user"></i> Divisi IE & PND Alta Hospital
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                    Manajemen Rekrutmen Calon Medis
                </h1>
                <p class="text-xs text-slate-400 mt-0.5">
                    Kontrol pembukaan periode rekrutmen dan verifikasi berkas pendaftaran calon paramedic IME Medical Center.
                </p>
            </div>

            <!-- Recruitment Status Toggle Button & Candidate Cleanup Option -->
            <div class="flex flex-wrap items-center gap-2.5">
                @if($currentPeriod)
                    <div class="flex items-center gap-2 bg-emerald-500/10 border border-emerald-500/30 px-3.5 py-2 rounded-xl text-emerald-300 text-xs font-bold">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>STATUS: DIBUKA ({{ $currentPeriod->batch_name }})</span>
                    </div>
                    <form method="POST" action="{{ route('portal.recruitment.toggle') }}" onsubmit="return confirm('Apakah Anda yakin ingin MENUTUP pendaftaran rekrutmen sekarang?')">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-bold transition shadow-lg flex items-center gap-1.5">
                            <i class="fas fa-power-off"></i> Tutup Pendaftaran
                        </button>
                    </form>
                @else
                    <div class="flex items-center gap-2 bg-rose-500/10 border border-rose-500/30 px-3.5 py-2 rounded-xl text-rose-300 text-xs font-bold">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-400"></span>
                        <span>STATUS: DITUTUP</span>
                    </div>
                    <button onclick="document.getElementById('openBatchModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition shadow-lg flex items-center gap-1.5">
                        <i class="fas fa-bullhorn"></i> Buka Pendaftaran
                    </button>
                    @if($stats['total'] > 0)
                    <button type="button" onclick="document.getElementById('clearApplicantsModal').classList.remove('hidden')" class="px-3.5 py-2 bg-rose-600/20 hover:bg-rose-600/30 border border-rose-500/30 text-rose-300 hover:text-white rounded-xl text-xs font-bold transition shadow-lg flex items-center gap-1.5">
                        <i class="fas fa-trash-alt"></i> Bersihkan Pendaftar
                    </button>
                    @endif
                @endif
                @if($currentPeriod && $stats['total'] > 0)
                    <button type="button" onclick="document.getElementById('clearApplicantsModal').classList.remove('hidden')" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 border border-white/10 text-slate-300 hover:text-rose-400 rounded-xl text-xs font-bold transition flex items-center gap-1.5" title="Opsi Pembersihan Data Pelamar">
                        <i class="fas fa-trash-alt"></i> Bersihkan Data
                    </button>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-xs sm:text-sm flex items-center gap-2">
                <i class="fas fa-check-circle text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('info'))
            <div class="p-4 rounded-xl bg-sky-500/20 border border-sky-500/30 text-sky-300 text-xs sm:text-sm flex items-center gap-2">
                <i class="fas fa-info-circle text-base"></i>
                <span>{{ session('info') }}</span>
            </div>
        @endif

        <!-- Quick Metrics -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <a href="{{ route('portal.recruitment.index') }}" class="p-4 rounded-2xl bg-white/5 border border-white/10 hover:border-white/20 transition group">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Pelamar</div>
                <div class="text-2xl font-black text-white mt-1 group-hover:text-amber-400 transition">{{ $stats['total'] }}</div>
            </a>
            <a href="{{ route('portal.recruitment.index', ['status' => 'pending']) }}" class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 hover:border-amber-500/40 transition group">
                <div class="text-[11px] font-bold text-amber-300 uppercase tracking-wider">Menunggu Review</div>
                <div class="text-2xl font-black text-amber-400 mt-1">{{ $stats['pending'] }}</div>
            </a>
            <a href="{{ route('portal.recruitment.index', ['status' => 'reviewed']) }}" class="p-4 rounded-2xl bg-blue-500/10 border border-blue-500/20 hover:border-blue-500/40 transition group">
                <div class="text-[11px] font-bold text-blue-300 uppercase tracking-wider">Lolos Berkas</div>
                <div class="text-2xl font-black text-blue-400 mt-1">{{ $stats['reviewed'] }}</div>
            </a>
            <a href="{{ route('portal.recruitment.index', ['status' => 'interview']) }}" class="p-4 rounded-2xl bg-purple-500/10 border border-purple-500/20 hover:border-purple-500/40 transition group">
                <div class="text-[11px] font-bold text-purple-300 uppercase tracking-wider">Tahap Interview</div>
                <div class="text-2xl font-black text-purple-400 mt-1">{{ $stats['interview'] }}</div>
            </a>
            <a href="{{ route('portal.recruitment.index', ['status' => 'accepted']) }}" class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 hover:border-emerald-500/40 transition group">
                <div class="text-[11px] font-bold text-emerald-300 uppercase tracking-wider">Diterima</div>
                <div class="text-2xl font-black text-emerald-400 mt-1">{{ $stats['accepted'] }}</div>
            </a>
            <a href="{{ route('portal.recruitment.index', ['status' => 'rejected']) }}" class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 hover:border-rose-500/40 transition group">
                <div class="text-[11px] font-bold text-rose-300 uppercase tracking-wider">Ditolak</div>
                <div class="text-2xl font-black text-rose-400 mt-1">{{ $stats['rejected'] }}</div>
            </a>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-white/5 border border-white/10 rounded-2xl p-4 flex flex-col sm:flex-row items-center justify-between gap-3">
            <form method="GET" action="{{ route('portal.recruitment.index') }}" class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto">
                <div class="relative flex-1 sm:w-64">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama IC / CID / Discord..." class="w-full pl-9 pr-3 py-2 rounded-xl bg-slate-800 border border-white/10 text-white text-xs placeholder-slate-400 focus:border-amber-400 outline-none">
                </div>
                <select name="status" class="px-3 py-2 rounded-xl bg-slate-800 border border-white/10 text-white text-xs focus:border-amber-400 outline-none" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Review</option>
                    <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>Lolos Berkas</option>
                    <option value="interview" {{ request('status') === 'interview' ? 'selected' : '' }}>Tahap Interview</option>
                    <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Diterima</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 rounded-xl text-xs font-bold transition">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('portal.recruitment.index') }}" class="text-xs text-slate-400 hover:text-white underline">Reset</a>
                @endif
            </form>

            <div class="text-xs text-slate-400">
                Menampilkan <strong>{{ $applications->count() }}</strong> dari <strong>{{ $applications->total() }}</strong> berkas
            </div>
        </div>

        <!-- Table of Applications -->
        <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-white/5 border-b border-white/10 text-slate-400 uppercase tracking-wider text-[10px]">
                            <th class="py-3.5 px-4 font-semibold">Pelamar (IC)</th>
                            <th class="py-3.5 px-4 font-semibold">CID</th>
                            <th class="py-3.5 px-4 font-semibold">Jenis Kelamin</th>
                            <th class="py-3.5 px-4 font-semibold">Pengalaman Medis</th>
                            <th class="py-3.5 px-4 font-semibold">Jam / Hari Online</th>
                            <th class="py-3.5 px-4 font-semibold">Status</th>
                            <th class="py-3.5 px-4 font-semibold">Tanggal Kirim</th>
                            <th class="py-3.5 px-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($applications as $app)
                        <tr class="hover:bg-white/[0.02] transition">
                            <td class="py-3 px-4">
                                <div class="font-bold text-white text-sm">{{ $app->ic_name }}</div>
                                <div class="text-[11px] text-slate-400">{{ $app->discord_username ? 'Discord: ' . $app->discord_username : 'Tanpa Discord' }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-mono text-amber-300 font-bold">#{{ $app->cid }}</span>
                            </td>
                            <td class="py-3 px-4 text-slate-300">{{ $app->gender }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold {{ $app->has_medical_exp === 'Ada' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-slate-700 text-slate-300' }}">
                                    {{ $app->has_medical_exp }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-300 text-[11px]">
                                <div>{{ is_array($app->online_hours) ? implode(', ', $app->online_hours) : '-' }}</div>
                                <div class="text-slate-400">{{ is_array($app->online_days) ? implode(', ', $app->online_days) : '-' }}</div>
                            </td>
                            <td class="py-3 px-4">
                                {!! $app->status_badge !!}
                            </td>
                            <td class="py-3 px-4 text-slate-400 text-[11px]">
                                {{ $app->created_at?->translatedFormat('d M Y, H:i') ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5 justify-end">
                                    <a href="{{ route('portal.recruitment.show', $app) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition">
                                        <i class="fas fa-eye"></i> Tinjau Berkas
                                    </a>
                                    <form method="POST" action="{{ route('portal.recruitment.destroy', $app) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus berkas pendaftaran {{ addslashes($app->ic_name) }} (#{{ $app->cid }}) secara permanen?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/25 border border-rose-500/30 text-rose-300 hover:text-rose-200 transition" title="Hapus Berkas Pendaftar">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-slate-400">
                                <i class="fas fa-inbox text-3xl mb-2 block opacity-40"></i>
                                Belum ada berkas pendaftaran calon medis yang masuk.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($applications->hasPages())
                <div class="p-4 border-t border-white/10">
                    {{ $applications->links() }}
                </div>
            @endif
        </div>

    </div>
</div>

<!-- Modal Buka Pendaftaran -->
<div id="openBatchModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-[99999] flex items-center justify-center p-4 hidden">
    <div class="bg-slate-900 border border-white/20 rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-white/10 pb-3">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <i class="fas fa-bullhorn text-emerald-400"></i> Buka Pendaftaran Rekrutmen Baru
            </h3>
            <button onclick="document.getElementById('openBatchModal').classList.add('hidden')" class="text-slate-400 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('portal.recruitment.toggle') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Nama Batch / Periode *</label>
                <input type="text" name="batch_name" value="Batch {{ now()->translatedFormat('F Y') }}" placeholder="Contoh: Batch September 2026" class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-white/10 text-white text-xs focus:border-emerald-400 outline-none" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Catatan / Keterangan Khusus (Opsional)</label>
                <textarea name="notes" rows="3" placeholder="Informasi tambahan untuk tim PND & IE..." class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-white/10 text-white text-xs focus:border-emerald-400 outline-none"></textarea>
            </div>
            <div class="p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-xs">
                ℹ️ Membuka pendaftaran akan otomatis mengaktifkan <strong>Banner Pengumuman Resmi</strong> di halaman utama website dan mengaktifkan formulir pendaftaran online.
            </div>
            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" onclick="document.getElementById('openBatchModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs font-semibold hover:bg-slate-700 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition shadow-lg">
                    Ya, Buka Pendaftaran Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Pembersihan Data Pendaftar EMS -->
<div id="clearApplicantsModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-[99999] flex items-center justify-center p-4 hidden">
    <div class="bg-slate-900 border border-rose-500/30 rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-white/10 pb-3">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <i class="fas fa-trash-alt text-rose-400"></i> Pembersihan Data Pendaftaran Calon Medis
            </h3>
            <button onclick="document.getElementById('clearApplicantsModal').classList.add('hidden')" class="text-slate-400 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs leading-relaxed space-y-1">
            <div class="font-bold flex items-center gap-1.5 text-rose-200">
                <i class="fas fa-exclamation-triangle"></i> Perhatian: Aksi ini bersifat permanen!
            </div>
            <p>
                Fitur ini membersihkan nama-nama pelamar EMS dan menghapus file berkas pendukung fisik (KTP, SKB, Surat Bebas Narkoba/Sehat, dan Surat Psikologi) dari server penyimpanan agar siap untuk batch rekrutmen berikutnya.
            </p>
        </div>

        <form method="POST" action="{{ route('portal.recruitment.clear') }}" class="space-y-4" onsubmit="return confirm('Apakah Anda benar-benar yakin ingin membersihkan data pendaftaran yang dipilih?')">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-2">Pilih Lingkup Pembersihan</label>
                <div class="space-y-2">
                    <label class="flex items-start gap-2.5 p-3 rounded-xl bg-slate-800/80 border border-white/10 hover:border-white/20 cursor-pointer transition">
                        <input type="radio" name="scope" value="all" checked class="mt-0.5 text-rose-500 focus:ring-rose-500">
                        <div>
                            <div class="text-xs font-bold text-white">Hapus Seluruh Data Pendaftar (Reset Total)</div>
                            <div class="text-[11px] text-slate-400">Membersihkan seluruh {{ $stats['total'] }} berkas pelamar baik yang menunggu, lolos, maupun ditolak.</div>
                        </div>
                    </label>
                    <label class="flex items-start gap-2.5 p-3 rounded-xl bg-slate-800/80 border border-white/10 hover:border-white/20 cursor-pointer transition">
                        <input type="radio" name="scope" value="rejected" class="mt-0.5 text-rose-500 focus:ring-rose-500">
                        <div>
                            <div class="text-xs font-bold text-white">Hanya Pelamar Ditolak ({{ $stats['rejected'] }} Berkas)</div>
                            <div class="text-[11px] text-slate-400">Hanya membersihkan pelamar yang status berkasnya ditolak.</div>
                        </div>
                    </label>
                    <label class="flex items-start gap-2.5 p-3 rounded-xl bg-slate-800/80 border border-white/10 hover:border-white/20 cursor-pointer transition">
                        <input type="radio" name="scope" value="without_interview" class="mt-0.5 text-rose-500 focus:ring-rose-500">
                        <div>
                            <div class="text-xs font-bold text-white">Hapus Yang Belum / Batal Interview (Pending, Lolos Berkas & Ditolak)</div>
                            <div class="text-[11px] text-slate-400">Menyimpan data calon yang sudah masuk ke tahap Interview atau Diterima.</div>
                        </div>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" onclick="document.getElementById('clearApplicantsModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs font-semibold hover:bg-slate-700 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold transition shadow-lg flex items-center gap-1.5">
                    <i class="fas fa-trash-alt"></i> Bersihkan Sekarang
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
