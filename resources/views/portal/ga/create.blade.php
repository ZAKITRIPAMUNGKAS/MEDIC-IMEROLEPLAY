@extends('layouts.app')

@section('title', 'Terbitkan Sertifikat Kendaraan GA — Portal Alta Hospital')

@section('content')
<div class="min-h-screen pt-20 pb-10" style="background: linear-gradient(135deg, #0b1329 0%, #0c2461 50%, #0b1329 100%);">
    <div class="max-w-2xl mx-auto px-4">
        <div class="mb-6">
            <a href="{{ route('portal.ga.index') }}" class="text-white/50 hover:text-white text-sm flex items-center gap-1.5 mb-3 transition-colors">
                <i class="fas fa-arrow-left text-xs"></i> Kembali
            </a>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-certificate text-amber-400"></i> Terbitkan Sertifikat Kendaraan
            </h1>
        </div>

        @if($errors->any())
        <div class="mb-4 p-4 bg-red-500/20 border border-red-500/40 rounded-xl text-red-300 text-sm">
            <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 shadow-2xl">
            <form method="POST" action="{{ route('portal.ga.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm text-white/70 font-medium mb-1.5">Pilih Anggota <span class="text-rose-400">*</span></label>
                    <select name="user_id" required class="w-full px-3 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400">
                        <option value="">— Pilih Anggota —</option>
                        @foreach($staffList as $staff)
                        <option value="{{ $staff->id }}" {{ old('user_id') == $staff->id ? 'selected' : '' }}>
                            {{ $staff->name }} ({{ $staff->staff_id }}) — {{ $staff->role?->display_name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm text-white/70 font-medium mb-1.5">Tipe Sertifikat <span class="text-rose-400">*</span></label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-3 p-3 bg-white/10 border border-white/20 rounded-xl cursor-pointer hover:bg-amber-500/10 hover:border-amber-500/40 transition-all has-[:checked]:bg-amber-500/15 has-[:checked]:border-amber-400">
                            <input type="radio" name="type" value="vehicle_land" {{ old('type','vehicle_land') === 'vehicle_land' ? 'checked' : '' }} class="accent-amber-400">
                            <span class="text-white text-sm"><i class="fas fa-car text-amber-400 mr-1.5"></i> Kendaraan Darat</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 bg-white/10 border border-white/20 rounded-xl cursor-pointer hover:bg-sky-500/10 hover:border-sky-500/40 transition-all has-[:checked]:bg-sky-500/15 has-[:checked]:border-sky-400">
                            <input type="radio" name="type" value="vehicle_heli" {{ old('type') === 'vehicle_heli' ? 'checked' : '' }} class="accent-sky-400">
                            <span class="text-white text-sm"><i class="fas fa-helicopter text-sky-400 mr-1.5"></i> Helikopter</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm text-white/70 font-medium mb-1.5">Judul Sertifikat <span class="text-rose-400">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required maxlength="255"
                           placeholder="Contoh: SIM Kendaraan Darurat Darat"
                           class="w-full px-3 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white text-sm placeholder-white/30 focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-white/70 font-medium mb-1.5">Nomor Sertifikat</label>
                        <input type="text" name="certificate_number" value="{{ old('certificate_number') }}" maxlength="100"
                               placeholder="GA-XXXX-XXXX"
                               class="w-full px-3 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white text-sm placeholder-white/30 focus:outline-none focus:border-amber-400">
                    </div>
                    <div>
                        <label class="block text-sm text-white/70 font-medium mb-1.5">Tanggal Terbit <span class="text-rose-400">*</span></label>
                        <input type="date" name="issue_date" value="{{ old('issue_date', date('Y-m-d')) }}" required
                               class="w-full px-3 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-amber-400">
                    </div>
                </div>

                <div>
                    <label class="block text-sm text-white/70 font-medium mb-1.5">Catatan</label>
                    <textarea name="notes" rows="2" maxlength="500" placeholder="Catatan tambahan (opsional)..."
                              class="w-full px-3 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white text-sm placeholder-white/30 focus:outline-none focus:border-amber-400 resize-none">{{ old('notes') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm text-white/70 font-medium mb-1.5">Upload Sertifikat (PDF/Gambar, max 5MB)</label>
                    <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png"
                           class="w-full px-3 py-2.5 bg-white/10 border border-white/20 border-dashed rounded-xl text-white/70 text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-amber-500/20 file:text-amber-300 hover:file:bg-amber-500/30">
                </div>

                <div class="pt-2 flex gap-3">
                    <button type="submit"
                            class="flex-1 py-3 bg-gradient-to-r from-amber-500 to-yellow-600 hover:from-amber-400 hover:to-yellow-500 text-white font-semibold rounded-xl shadow-lg transition-all duration-200 text-sm">
                        <i class="fas fa-certificate mr-1.5"></i> Terbitkan Sertifikat
                    </button>
                    <a href="{{ route('portal.ga.index') }}" class="px-5 py-3 bg-white/10 hover:bg-white/15 text-white/70 font-medium rounded-xl transition-all text-sm">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
