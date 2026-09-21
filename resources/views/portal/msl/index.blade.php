@extends('layouts.app')
@section('title', 'Sertifikat Visum MSL — Portal Alta Hospital')
@section('content')
<div class="min-h-screen pt-20 pb-10" style="background: linear-gradient(135deg, #0b1329 0%, #0c2461 50%, #0b1329 100%);">
<div class="max-w-6xl mx-auto px-4">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2"><i class="fas fa-stethoscope text-teal-400"></i> MSL — Sertifikat Visum</h1>
            <p class="text-white/50 text-sm mt-0.5">Kelola sertifikat visum hidup dan visum mati anggota</p>
        </div>
    </div>
    @if(session('success'))
    <div class="mb-4 p-4 bg-emerald-500/20 border border-emerald-500/40 rounded-xl text-emerald-300 text-sm flex items-center gap-2"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    {{-- Form Terbitkan --}}
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 mb-6">
        <h3 class="text-white font-semibold mb-4 flex items-center gap-2"><i class="fas fa-plus-circle text-teal-400"></i> Terbitkan Sertifikat Visum</h3>
        <form method="POST" action="{{ route('portal.msl.visum.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @csrf
            <div>
                <label class="block text-xs text-white/50 mb-1">Anggota *</label>
                <select name="user_id" required class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-teal-400">
                    <option value="">— Pilih Anggota —</option>
                    @foreach($staffList as $s)<option value="{{ $s->id }}">{{ $s->name }} ({{ $s->staff_id }})</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-white/50 mb-1">Tipe Sertifikat *</label>
                <select name="type" required class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-teal-400">
                    <option value="visum_alive">Sertifikat Visum Hidup</option>
                    <option value="visum_dead">Sertifikat Visum Mati</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-white/50 mb-1">Judul *</label>
                <input type="text" name="title" required maxlength="255" placeholder="Judul sertifikat" class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm placeholder-white/30 focus:outline-none focus:border-teal-400">
            </div>
            <div>
                <label class="block text-xs text-white/50 mb-1">Tanggal Terbit *</label>
                <input type="date" name="issue_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-teal-400">
            </div>
            <div class="col-span-1 sm:col-span-2 p-3 bg-teal-500/10 border border-teal-500/25 rounded-xl text-teal-200 text-xs flex items-start gap-2.5">
                <i class="fas fa-magic text-teal-400 mt-0.5 shrink-0"></i>
                <div class="leading-relaxed text-[11px]">
                    <strong class="text-white">Cetak Sertifikat Otomatis:</strong> Berkas foto sertifikat visum beresolusi tinggi otomatis di-generate oleh sistem menggunakan template resmi Alta Hospital dengan Nomor Registrasi, Cap Stempel Resmi &amp; Tanda Tangan Digital yang langsung tampil di profil anggota.
                </div>
            </div>
            <div class="col-span-1 sm:col-span-2">
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-teal-500 to-cyan-600 text-white font-semibold rounded-xl text-sm hover:from-teal-400 hover:to-cyan-500 transition-all flex items-center gap-2">
                    <i class="fas fa-magic"></i> Terbitkan &amp; Generate Foto ke Profil
                </button>
            </div>
        </form>
    </div>
    {{-- Tabel Sertifikat --}}
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden">
        @if($certifications->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-white/40"><i class="fas fa-file-medical text-4xl mb-3"></i><p class="text-sm">Belum ada sertifikat visum diterbitkan.</p></div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-white/10 text-xs text-white/50 uppercase tracking-wider">
                    <tr><th class="text-left px-5 py-3">Anggota</th><th class="text-left px-5 py-3">Tipe</th><th class="text-left px-5 py-3">Judul</th><th class="text-left px-5 py-3">Terbit</th><th class="text-left px-5 py-3">Status</th><th class="text-left px-5 py-3">Diterbitkan Oleh</th></tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($certifications as $cert)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-5 py-3.5"><div class="text-white font-medium">{{ $cert->user?->name }}</div><div class="text-white/40 text-xs">{{ $cert->user?->staff_id }}</div></td>
                        <td class="px-5 py-3.5"><span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $cert->type === 'visum_alive' ? 'bg-teal-500/20 text-teal-300 border border-teal-500/30' : 'bg-slate-500/20 text-slate-300 border border-slate-500/30' }}">{{ $cert->type === 'visum_alive' ? 'Visum Hidup' : 'Visum Mati' }}</span></td>
                        <td class="px-5 py-3.5 text-white/80">{{ $cert->title }}</td>
                        <td class="px-5 py-3.5 text-white/70">{{ $cert->issue_date?->format('d M Y') }}</td>
                        <td class="px-5 py-3.5"><span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $cert->status === 'active' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-red-500/20 text-red-300 border border-red-500/30' }}">{{ ucfirst($cert->status) }}</span></td>
                        <td class="px-5 py-3.5 text-white/60 text-xs">{{ $cert->issuedBy?->name ?? '—' }}</td>
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
