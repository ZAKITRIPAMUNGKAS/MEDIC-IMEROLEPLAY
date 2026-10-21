@extends('layouts.app')
@section('title', 'Ajukan Stase Baru — Portal Alta Hospital')
@section('content')
<div class="min-h-screen pt-20 pb-10" style="background: linear-gradient(180deg, #0c4a6e 0%, #075985 50%, #0369a1 100%);">
<div class="max-w-2xl mx-auto px-4">
    <div class="mb-6">
        <a href="{{ route('portal.stase.index') }}" class="text-white/50 hover:text-white text-sm flex items-center gap-1.5 mb-3 transition-colors">
            <i class="fas fa-arrow-left text-xs"></i> Kembali ke Daftar Stase
        </a>
        <h1 class="text-2xl font-bold text-white flex items-center gap-2">
            <i class="fas fa-graduation-cap text-blue-400"></i> Formulir Pengajuan Stase Medis
        </h1>
        <p class="text-white/60 text-sm mt-1">Pengajuan stase akan ditujukan ke Dokter Konsulen Pembimbing untuk evaluasi laporan, lalu diverifikasi oleh Divisi MSL untuk penerbitan sertifikat resmi ke profil Anda.</p>
    </div>

    @if($errors->any())
    <div class="mb-4 p-4 bg-red-500/20 border border-red-500/40 rounded-xl text-red-300 text-sm">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 sm:p-8 shadow-2xl">
        <form method="POST" action="{{ route('portal.stase.store') }}" class="space-y-5">
            @csrf

            {{-- Bidang Stase (Dropdown Stase yang Tersedia Saja) --}}
            <div>
                <label class="block text-sm text-white/80 font-medium mb-1.5">
                    Bidang Stase yang Tersedia *
                </label>
                <select name="stase_name" required class="w-full px-3.5 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400">
                    <option value="" class="bg-slate-900 text-white/50">— Pilih Bidang Stase yang Tersedia —</option>
                    @foreach($availableStases as $stase)
                    <option value="{{ $stase }}" class="bg-slate-900 text-white" {{ old('stase_name') == $stase ? 'selected' : '' }}>
                        {{ $stase }}
                    </option>
                    @endforeach
                </select>
                <p class="text-[11px] text-white/40 mt-1">Pilih modul/program stase kedokteran yang ingin Anda tempuh di Alta Hospital.</p>
            </div>

            {{-- Pilih Konsulen Pembimbing --}}
            <div>
                <label class="block text-sm text-white/80 font-medium mb-1.5">
                    Pilih Dokter Konsulen Pembimbing *
                </label>
                <select name="konsulen_id" required class="w-full px-3.5 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400">
                    <option value="" class="bg-slate-900 text-white/50">— Pilih Konsulen Pembimbing Resmi —</option>
                    @foreach($konsulenList as $k)
                    <option value="{{ $k->id }}" class="bg-slate-900 text-white" {{ old('konsulen_id') == $k->id ? 'selected' : '' }}>
                        {{ $k->name }} ({{ $k->staff_id }}) — {{ $k->role?->display_name }} {{ $k->is_konsulen ? '★ [Konsulen Resmi]' : '' }}
                    </option>
                    @endforeach
                </select>
                <p class="text-[11px] text-white/40 mt-1">Dokter Konsulen yang terdaftar akan mengevaluasi dan menginput laporan hasil bimbingan stase Anda.</p>
            </div>

            {{-- Catatan / Motivasi Stase --}}
            <div>
                <label class="block text-sm text-white/80 font-medium mb-1.5">Catatan / Motivasi Stase (Opsional)</label>
                <textarea name="notes" rows="3" maxlength="500" placeholder="Tuliskan catatan, tujuan pembelajaran, atau keterangan tambahan..."
                          class="w-full px-3.5 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white text-sm placeholder-white/30 focus:outline-none focus:border-blue-400 resize-none">{{ old('notes') }}</textarea>
            </div>

            {{-- Alur Proses --}}
            <div class="p-3.5 bg-blue-500/10 border border-blue-500/20 rounded-xl text-xs text-blue-200 space-y-1">
                <div class="font-semibold text-white flex items-center gap-1.5">
                    <i class="fas fa-info-circle text-blue-400"></i> Alur Penerbitan Sertifikat Stase:
                </div>
                <div class="text-[11px] text-blue-200/80 leading-relaxed">
                    1. Anggota mengajukan stase &rarr; 2. Dokter Konsulen terdaftar memeriksa dan menginput <strong>Laporan Hasil Bimbingan</strong> &rarr; 3. Divisi MSL memverifikasi laporan &rarr; 4. Sertifikat resmi ber-SVG otomatis terbit dan tampil di profil Anda.
                </div>
            </div>

            <div class="pt-2 flex gap-3">
                <button type="submit" class="flex-1 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-400 hover:to-indigo-500 text-white font-semibold rounded-xl shadow-lg transition-all text-sm flex items-center justify-center gap-2">
                    <i class="fas fa-paper-plane"></i> Kirim Pengajuan ke Dokter Konsulen
                </button>
                <a href="{{ route('portal.stase.index') }}" class="px-5 py-3 bg-white/10 hover:bg-white/15 text-white/70 font-medium rounded-xl transition-all text-sm">Batal</a>
            </div>
        </form>
    </div>
</div>
</div>
@endsection
