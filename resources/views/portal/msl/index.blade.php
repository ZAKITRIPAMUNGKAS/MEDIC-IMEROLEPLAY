@extends('layouts.app')
@section('title', 'Sertifikat Visum MSL — Portal Alta Hospital')
@section('content')
<div class="min-h-screen pt-20 pb-10" style="background: linear-gradient(180deg, #0c4a6e 0%, #075985 50%, #0369a1 100%);">
<div class="max-w-6xl mx-auto px-4">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-stethoscope text-teal-400"></i> MSL — Sertifikat Visum
            </h1>
            <p class="text-white/60 text-sm mt-0.5">Kelola penerbitan, pencabutan (revoke), dan arsip sertifikat visum hidup &amp; visum mati anggota.</p>
        </div>
        <a href="{{ route('portal.msl.stase.index') }}" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-xl border border-white/20 text-sm flex items-center gap-2 transition-all">
            <i class="fas fa-book-medical text-teal-300"></i> Kelola Stase &amp; Konsulen
        </a>
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

    {{-- Form Terbitkan --}}
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 mb-6 shadow-xl">
        <h3 class="text-white font-semibold mb-4 flex items-center gap-2">
            <i class="fas fa-plus-circle text-teal-400"></i> Terbitkan Sertifikat Visum
        </h3>
        <form method="POST" action="{{ route('portal.msl.visum.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @csrf
            <div>
                <label class="block text-xs text-white/70 mb-1 font-medium">Anggota Medis *</label>
                <select name="user_id" required class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-teal-400">
                    <option value="">— Pilih Anggota —</option>
                    @foreach($staffList as $s)
                    <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->staff_id }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-white/70 mb-1 font-medium">Tipe Sertifikat Visum *</label>
                <select name="type" required class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-teal-400">
                    <option value="visum_alive">Sertifikat Visum Hidup</option>
                    <option value="visum_dead">Sertifikat Visum Mati</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-white/70 mb-1 font-medium">Judul Sertifikat *</label>
                <input type="text" name="title" required maxlength="255" placeholder="Contoh: Sertifikasi Kualifikasi Visum Hidup" class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm placeholder-white/30 focus:outline-none focus:border-teal-400">
            </div>
            <div>
                <label class="block text-xs text-white/70 mb-1 font-medium">Tanggal Terbit *</label>
                <input type="date" name="issue_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-teal-400">
            </div>
            <div class="col-span-1 sm:col-span-2">
                <label class="block text-xs text-white/70 mb-1 font-medium">Catatan / Keterangan Tambahan</label>
                <input type="text" name="notes" maxlength="500" placeholder="Keterangan kelayakan atau hasil pengujian visum..." class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm placeholder-white/30 focus:outline-none focus:border-teal-400">
            </div>
            <div class="col-span-1 sm:col-span-2 p-3 bg-teal-500/10 border border-teal-500/25 rounded-xl text-teal-200 text-xs flex items-start gap-2.5">
                <i class="fas fa-magic text-teal-400 mt-0.5 shrink-0"></i>
                <div class="leading-relaxed text-[11px]">
                    <strong class="text-white">Cetak Sertifikat Otomatis:</strong> Gambar sertifikat beresolusi tinggi otomatis di-generate menggunakan template resmi Alta Hospital (No. Registrasi, Stempel Resmi &amp; QR Code) dan langsung tampil di profil anggota serta direktori sertifikat.
                </div>
            </div>
            <div class="col-span-1 sm:col-span-2">
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-teal-500 to-cyan-600 text-white font-semibold rounded-xl text-sm hover:from-teal-400 hover:to-cyan-500 transition-all flex items-center gap-2 shadow-lg">
                    <i class="fas fa-magic"></i> Terbitkan &amp; Sinkron ke Profil Anggota
                </button>
            </div>
        </form>
    </div>

    {{-- Filter & Search --}}
    <div class="mb-4 flex flex-wrap gap-2 items-center justify-between">
        <form method="GET" class="flex flex-wrap gap-2 items-center">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau No. Pegawai..."
                   class="px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm placeholder-white/30 focus:outline-none focus:border-teal-400 w-60">
            <select name="type" class="px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none">
                <option value="">Semua Tipe</option>
                <option value="visum_alive" {{ request('type') === 'visum_alive' ? 'selected' : '' }}>Visum Hidup</option>
                <option value="visum_dead" {{ request('type') === 'visum_dead' ? 'selected' : '' }}>Visum Mati</option>
            </select>
            <select name="status" class="px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="revoked" {{ request('status') === 'revoked' ? 'selected' : '' }}>Dicabut (Revoked)</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-teal-500/20 hover:bg-teal-500/30 text-teal-300 rounded-xl border border-teal-500/30 text-sm font-medium">
                <i class="fas fa-search mr-1"></i> Filter
            </button>
            @if(request()->hasAny(['q', 'type', 'status']))
                <a href="{{ route('portal.msl.index') }}" class="px-3 py-2 bg-white/10 hover:bg-white/15 text-white/70 rounded-xl text-sm">Reset</a>
            @endif
        </form>
    </div>

    {{-- Tabel Sertifikat --}}
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden shadow-2xl">
        @if($certifications->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-white/40">
            <i class="fas fa-file-medical text-4xl mb-3"></i>
            <p class="text-sm">Belum ada sertifikat visum diterbitkan.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-white/10 text-xs text-white/50 uppercase tracking-wider">
                    <tr>
                        <th class="text-left px-5 py-3">Anggota</th>
                        <th class="text-left px-5 py-3">Tipe</th>
                        <th class="text-left px-5 py-3">Judul &amp; Nomor</th>
                        <th class="text-left px-5 py-3">Terbit</th>
                        <th class="text-left px-5 py-3">Status</th>
                        <th class="text-left px-5 py-3">Penerbit</th>
                        <th class="text-center px-5 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($certifications as $cert)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-5 py-3.5">
                            <div class="text-white font-medium">{{ $cert->user?->name }}</div>
                            <div class="text-white/40 text-xs">{{ $cert->user?->staff_id }}</div>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $cert->type === 'visum_alive' ? 'bg-teal-500/20 text-teal-300 border border-teal-500/30' : 'bg-slate-500/20 text-slate-300 border border-slate-500/30' }}">
                                {{ $cert->type === 'visum_alive' ? 'Visum Hidup' : 'Visum Mati' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="text-white/90 font-medium">{{ $cert->title }}</div>
                            <div class="text-sky-300/60 font-mono text-xs">{{ $cert->certificate_number ?? 'Auto-Generated' }}</div>
                        </td>
                        <td class="px-5 py-3.5 text-white/70">{{ $cert->issue_date?->format('d M Y') }}</td>
                        <td class="px-5 py-3.5">
                            @if($cert->status === 'active')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 flex items-center gap-1 w-fit">
                                    <i class="fas fa-check-circle text-[10px]"></i> Aktif
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-500/20 text-red-300 border border-red-500/30 flex items-center gap-1 w-fit">
                                    <i class="fas fa-ban text-[10px]"></i> Dicabut
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-white/60 text-xs">{{ $cert->issuedBy?->name ?? 'Alta Hospital' }}</td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-center gap-2">
                                {{-- Lihat Sertifikat --}}
                                <a href="{{ route('portal.cert.image', $cert->id) }}" target="_blank"
                                   class="px-2.5 py-1 bg-white/10 hover:bg-white/20 text-white rounded-lg text-xs font-medium border border-white/20 transition-all flex items-center gap-1"
                                   title="Lihat Gambar Sertifikat SVG">
                                    <i class="fas fa-eye text-teal-300"></i> Lihat
                                </a>

                                {{-- Tombol Revoke / Cabut Sertifikat --}}
                                @if($cert->status === 'active')
                                    <form method="POST" action="{{ route('portal.msl.revoke', $cert) }}" onsubmit="return confirm('Apakah Anda yakin ingin MENCABUT (revoke) sertifikat visum ini untuk {{ addslashes($cert->user?->name) }}?')">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 rounded-lg text-xs font-medium border border-rose-500/30 transition-all flex items-center gap-1" title="Cabut sertifikat (tidak aktif)">
                                            <i class="fas fa-ban"></i> Cabut
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('portal.msl.revoke', $cert) }}" onsubmit="return confirm('Aktifkan kembali sertifikat ini?')">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 rounded-lg text-xs font-medium border border-emerald-500/30 transition-all flex items-center gap-1" title="Aktifkan kembali sertifikat">
                                            <i class="fas fa-undo"></i> Aktifkan
                                        </button>
                                    </form>
                                @endif

                                {{-- Hapus --}}
                                <form method="POST" action="{{ route('portal.msl.destroy', $cert) }}" onsubmit="return confirm('Hapus permanen arsip sertifikat ini? Tindakan ini tidak dapat dibatalkan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-white/40 hover:text-red-400 transition-colors" title="Hapus permanen">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-white/10">{{ $certifications->links() }}</div>
        @endif
    </div>
</div>
</div>
@endsection
