@extends('layouts.app')
@section('title', 'Ajukan Stase Baru — Portal Alta Hospital')
@section('content')
<div class="min-h-screen pt-20 pb-10" style="background: linear-gradient(135deg, #0b1329 0%, #0c2461 50%, #0b1329 100%);">
<div class="max-w-2xl mx-auto px-4">
    <div class="mb-6">
        <a href="{{ route('portal.stase.index') }}" class="text-white/50 hover:text-white text-sm flex items-center gap-1.5 mb-3 transition-colors"><i class="fas fa-arrow-left text-xs"></i> Kembali</a>
        <h1 class="text-2xl font-bold text-white flex items-center gap-2"><i class="fas fa-graduation-cap text-blue-400"></i> Formulir Pengajuan Stase</h1>
        <p class="text-white/50 text-sm mt-1">Pengajuan akan dikirim ke konsulen yang dipilih, kemudian disetujui oleh MSL.</p>
    </div>
    @if($errors->any())
    <div class="mb-4 p-4 bg-red-500/20 border border-red-500/40 rounded-xl text-red-300 text-sm"><ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 shadow-2xl">
        <form method="POST" action="{{ route('portal.stase.store') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm text-white/70 font-medium mb-1.5">Pilih Konsulen (DPJP/Dokter Spesialis) *</label>
                <select name="konsulen_id" required class="w-full px-3 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400">
                    <option value="">— Pilih Konsulen Pembimbing —</option>
                    @foreach($konsulenList as $k)
                    <option value="{{ $k->id }}" {{ old('konsulen_id') == $k->id ? 'selected' : '' }}>
                        {{ $k->name }} — {{ $k->role?->display_name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm text-white/70 font-medium mb-1.5">Nama / Bidang Stase *</label>
                <input type="text" name="stase_name" value="{{ old('stase_name') }}" required maxlength="255"
                       placeholder="Contoh: Stase IGD, Stase Bedah, dll."
                       class="w-full px-3 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white text-sm placeholder-white/30 focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400">
            </div>
            <div>
                <label class="block text-sm text-white/70 font-medium mb-1.5">Departemen / Bangsal</label>
                <input type="text" name="department" value="{{ old('department') }}" maxlength="255"
                       placeholder="Departemen atau bangsal stase (opsional)"
                       class="w-full px-3 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white text-sm placeholder-white/30 focus:outline-none focus:border-blue-400">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-white/70 font-medium mb-1.5">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}"
                           class="w-full px-3 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-sm text-white/70 font-medium mb-1.5">Tanggal Selesai (Est.)</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}"
                           class="w-full px-3 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-blue-400">
                </div>
            </div>
            <div>
                <label class="block text-sm text-white/70 font-medium mb-1.5">Catatan / Motivasi Stase</label>
                <textarea name="notes" rows="3" maxlength="500" placeholder="Tuliskan motivasi atau tujuan mengikuti stase ini..."
                          class="w-full px-3 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white text-sm placeholder-white/30 focus:outline-none focus:border-blue-400 resize-none">{{ old('notes') }}</textarea>
            </div>
            <div class="pt-2 flex gap-3">
                <button type="submit" class="flex-1 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-400 hover:to-indigo-500 text-white font-semibold rounded-xl shadow-lg transition-all text-sm">
                    <i class="fas fa-paper-plane mr-1.5"></i> Kirim ke Konsulen
                </button>
                <a href="{{ route('portal.stase.index') }}" class="px-5 py-3 bg-white/10 hover:bg-white/15 text-white/70 font-medium rounded-xl transition-all text-sm">Batal</a>
            </div>
        </form>
    </div>
</div>
</div>
@endsection
