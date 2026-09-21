@extends('layouts.app')

@section('title', 'Formulir Pengajuan Resign — Portal Alta Hospital')

@section('content')
<div class="min-h-screen pt-20 pb-10" style="background: linear-gradient(135deg, #0b1329 0%, #0c2461 50%, #0b1329 100%);">
    <div class="max-w-2xl mx-auto px-4">

        <div class="mb-6">
            <a href="{{ route('portal.resignation.index') }}" class="text-white/50 hover:text-white text-sm flex items-center gap-1.5 mb-3 transition-colors">
                <i class="fas fa-arrow-left text-xs"></i> Kembali
            </a>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-file-signature text-orange-400"></i> Formulir Pengajuan Resign
            </h1>
            <p class="text-white/50 text-sm mt-1">Isi formulir ini dengan jujur. Pengajuan akan dikirim ke divisi PND.</p>
        </div>

        {{-- Warning --}}
        <div class="mb-5 p-4 bg-red-500/15 border border-red-500/30 rounded-xl text-red-300 text-sm flex items-start gap-3">
            <i class="fas fa-exclamation-triangle text-red-400 mt-0.5"></i>
            <div>
                <strong class="block font-semibold mb-0.5">Perhatian!</strong>
                Setelah pengajuan disetujui PND, Divisi IE akan menghitung denda resign berdasarkan jabatan Anda.
                Akun akan dinonaktifkan setelah denda dilunasi.
            </div>
        </div>

        @if($errors->any())
        <div class="mb-4 p-4 bg-red-500/20 border border-red-500/40 rounded-xl text-red-300 text-sm">
            <ul class="space-y-1 list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 shadow-2xl space-y-5">
            {{-- Info Pemohon --}}
            <div class="grid grid-cols-2 gap-4 pb-4 border-b border-white/10 text-sm">
                <div><span class="text-white/40 text-xs uppercase block mb-0.5">Nama</span><span class="text-white font-semibold">{{ $user->name }}</span></div>
                <div><span class="text-white/40 text-xs uppercase block mb-0.5">Jabatan</span><span class="text-white/80">{{ $position }}</span></div>
                <div><span class="text-white/40 text-xs uppercase block mb-0.5">Jabatan Manajerial</span><span class="text-white/80">{{ $managerialPosition }}</span></div>
                <div><span class="text-white/40 text-xs uppercase block mb-0.5">Batch</span><span class="text-white/80">{{ $batch }}</span></div>
            </div>

            <form method="POST" action="{{ route('portal.resignation.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="reason_ic" class="block text-sm text-white/70 font-medium mb-1.5">
                        Alasan Resign (In-Character / IC) <span class="text-rose-400">*</span>
                    </label>
                    <textarea id="reason_ic" name="reason_ic" rows="4" required maxlength="2000"
                              placeholder="Alasan pengunduran diri dalam konteks roleplay..."
                              class="w-full px-3 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white text-sm placeholder-white/30 focus:outline-none focus:border-orange-400 focus:ring-1 focus:ring-orange-400 transition-all resize-none">{{ old('reason_ic') }}</textarea>
                </div>

                <div>
                    <label for="reason_ooc" class="block text-sm text-white/70 font-medium mb-1.5">
                        Alasan Resign (Out-of-Character / OOC) <span class="text-rose-400">*</span>
                    </label>
                    <textarea id="reason_ooc" name="reason_ooc" rows="4" required maxlength="2000"
                              placeholder="Alasan pengunduran diri yang sebenarnya..."
                              class="w-full px-3 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white text-sm placeholder-white/30 focus:outline-none focus:border-orange-400 focus:ring-1 focus:ring-orange-400 transition-all resize-none">{{ old('reason_ooc') }}</textarea>
                </div>

                <div class="pt-2 flex gap-3">
                    <button type="submit"
                            onclick="return confirm('Yakin ingin mengajukan resign? Proses ini tidak bisa dibatalkan setelah disetujui PND.')"
                            class="flex-1 py-3 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-400 hover:to-red-500 text-white font-semibold rounded-xl shadow-lg transition-all duration-200 text-sm">
                        <i class="fas fa-paper-plane mr-1.5"></i> Kirim Pengajuan Resign
                    </button>
                    <a href="{{ route('portal.resignation.index') }}"
                       class="px-5 py-3 bg-white/10 hover:bg-white/15 text-white/70 font-medium rounded-xl transition-all text-sm">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
