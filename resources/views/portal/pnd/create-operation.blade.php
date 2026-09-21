@extends('layouts.app')

@section('title', 'Buat Pengajuan Operasi — Portal Alta Hospital')

@section('content')
<div class="min-h-screen pt-20 pb-10" style="background: linear-gradient(135deg, #0b1329 0%, #0c2461 50%, #0b1329 100%);">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">

        {{-- Header --}}
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('portal.pnd.my-operations') }}" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 border border-white/10 flex items-center justify-center text-white/70 hover:text-white transition-all">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-white tracking-wide">Pengajuan Jadwal Operasi</h1>
                <p class="text-white/50 text-sm mt-0.5">Isi data pasien dan tim operasi untuk diverifikasi oleh Divisi PND</p>
            </div>
        </div>

        @if($errors->any())
        <div class="mb-6 p-4 bg-rose-500/20 border border-rose-500/40 rounded-xl text-rose-300 text-sm">
            <div class="font-semibold mb-1 flex items-center gap-2">
                <i class="fas fa-exclamation-circle"></i> Terdapat kesalahan pengisian:
            </div>
            <ul class="list-disc list-inside space-y-1 text-xs">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Form Card --}}
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 sm:p-8 shadow-2xl">
            <form action="{{ route('portal.pnd.operation.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- Jenis Operasi & Nama Pasien --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-white/70 uppercase mb-2">
                            Jenis Operasi <span class="text-rose-400">*</span>
                        </label>
                        <select name="jenis_operasi" required class="w-full px-3.5 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-emerald-400">
                            <option value="" class="bg-gray-800 text-white">-- Pilih Jenis Operasi --</option>
                            <option value="Operasi Kecil (Minor)" {{ old('jenis_operasi') === 'Operasi Kecil (Minor)' ? 'selected' : '' }} class="bg-gray-800 text-white">Operasi Kecil (Minor)</option>
                            <option value="Operasi Sedang" {{ old('jenis_operasi') === 'Operasi Sedang' ? 'selected' : '' }} class="bg-gray-800 text-white">Operasi Sedang</option>
                            <option value="Operasi Besar (Mayor)" {{ old('jenis_operasi') === 'Operasi Besar (Mayor)' ? 'selected' : '' }} class="bg-gray-800 text-white">Operasi Besar (Mayor)</option>
                            <option value="Operasi Khusus / Cito" {{ old('jenis_operasi') === 'Operasi Khusus / Cito' ? 'selected' : '' }} class="bg-gray-800 text-white">Operasi Khusus / Cito</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-white/70 uppercase mb-2">
                            Nama Pasien <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" name="patient_name" value="{{ old('patient_name') }}" required placeholder="Contoh: Bpk. John Doe"
                               class="w-full px-3.5 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white text-sm placeholder-white/30 focus:outline-none focus:border-emerald-400">
                    </div>
                </div>

                {{-- Diagnosis Medis --}}
                <div>
                    <label class="block text-xs font-semibold text-white/70 uppercase mb-2">
                        Diagnosis Medis Pra-Bedah <span class="text-rose-400">*</span>
                    </label>
                    <textarea name="diagnosis" rows="2" required placeholder="Diagnosis klinis pasien..."
                              class="w-full px-3.5 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white text-sm placeholder-white/30 focus:outline-none focus:border-emerald-400">{{ old('diagnosis') }}</textarea>
                </div>

                {{-- Rencana Prosedur / Tindakan --}}
                <div>
                    <label class="block text-xs font-semibold text-white/70 uppercase mb-2">
                        Rencana Prosedur / Tindakan Bedah <span class="text-rose-400">*</span>
                    </label>
                    <textarea name="planned_procedure" rows="2" required placeholder="Rencana tindakan pembedahan yang akan dilakukan..."
                              class="w-full px-3.5 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white text-sm placeholder-white/30 focus:outline-none focus:border-emerald-400">{{ old('planned_procedure') }}</textarea>
                </div>

                {{-- Jadwal & DPJP --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-white/70 uppercase mb-2">
                            Waktu / Jadwal Operasi
                        </label>
                        <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}"
                               class="w-full px-3.5 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-emerald-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-white/70 uppercase mb-2">
                            Dokter DPJP / Konsulen
                        </label>
                        <select name="dpjp_id" class="w-full px-3.5 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-emerald-400">
                            <option value="" class="bg-gray-800 text-white">-- Pilih Dokter DPJP --</option>
                            @foreach($doctors as $doc)
                                <option value="{{ $doc->id }}" {{ old('dpjp_id') == $doc->id ? 'selected' : '' }} class="bg-gray-800 text-white">
                                    {{ $doc->name }} ({{ $doc->role?->display_name ?? 'Dokter' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Asisten Operasi (Opsional) --}}
                <div>
                    <label class="block text-xs font-semibold text-white/70 uppercase mb-2">
                        Asisten Bedah / Paramedis (Opsional)
                    </label>
                    <div class="max-h-40 overflow-y-auto p-3 bg-white/5 border border-white/15 rounded-xl space-y-2">
                        @forelse($doctors as $doc)
                            <label class="flex items-center gap-2.5 text-xs text-white/80 hover:text-white cursor-pointer select-none">
                                <input type="checkbox" name="assistant_ids[]" value="{{ $doc->id }}"
                                       {{ is_array(old('assistant_ids')) && in_array($doc->id, old('assistant_ids')) ? 'checked' : '' }}
                                       class="rounded bg-white/10 border-white/20 text-emerald-500 focus:ring-0 focus:ring-offset-0">
                                <span>{{ $doc->name }} <span class="text-white/40">({{ $doc->role?->display_name ?? 'Dokter' }})</span></span>
                            </label>
                        @empty
                            <p class="text-xs text-white/40 italic">Tidak ada staf dokter tersedia.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Catatan Tambahan --}}
                <div>
                    <label class="block text-xs font-semibold text-white/70 uppercase mb-2">
                        Catatan Khusus (Opsional)
                    </label>
                    <textarea name="notes" rows="2" placeholder="Catatan khusus, kebutuhan ruangan OK, dll..."
                              class="w-full px-3.5 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white text-sm placeholder-white/30 focus:outline-none focus:border-emerald-400">{{ old('notes') }}</textarea>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-white/10">
                    <a href="{{ route('portal.pnd.my-operations') }}" class="px-5 py-2.5 bg-white/10 text-white/70 hover:text-white rounded-xl text-sm font-semibold transition-all">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-xl text-sm font-semibold shadow-lg shadow-emerald-900/30 transition-all flex items-center gap-2">
                        <i class="fas fa-paper-plane text-xs"></i> Ajukan Operasi ke PND
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
