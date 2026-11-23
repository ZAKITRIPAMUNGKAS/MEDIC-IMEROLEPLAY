@extends('layouts.app')

@section('title', 'Edit Jadwal Dokter & Spesialisasi - Panel Admin')

@section('content')
<div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700"></div>
    <div class="absolute inset-0 bg-black bg-opacity-20"></div>

    <div class="relative max-w-3xl w-full mx-auto text-white">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center">
                <a href="{{ route('admin.doctor-schedules.index') }}" class="mr-4 text-sky-400 hover:text-sky-300 transition-colors">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white">Edit Jadwal Dokter</h1>
                    <p class="text-sky-300 text-sm sm:text-base">Perbarui informasi jadwal praktek dan spesialisasi dokter</p>
                </div>
            </div>
        </div>

        @if($errors->any())
        <div class="mb-6 px-6 py-4 bg-red-500/20 border border-red-500/30 text-red-300 rounded-xl">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="glass-effect rounded-2xl elegant-shadow-lg p-6 sm:p-8">
            <form method="POST" action="{{ route('admin.doctor-schedules.update', $doctorSchedule) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Doctor Name -->
                <div>
                    <label for="doctor_name" class="block text-sm font-semibold text-gray-200 mb-2">Nama Dokter</label>
                    <div class="relative">
                        <i class="fas fa-user-md absolute left-4 top-1/2 -translate-y-1/2 text-sky-400 pointer-events-none text-sm"></i>
                        <input type="text"
                               name="doctor_name"
                               id="doctor_name"
                               list="doctor_suggestions"
                               value="{{ old('doctor_name', $doctorSchedule->doctor_name) }}"
                               placeholder="Ketik nama dokter..."
                               autocomplete="off"
                               required
                               class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-lg pl-12 pr-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 text-sm transition-all">
                        <datalist id="doctor_suggestions">
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->name }}" data-hospital="{{ $doctor->isRoxwood() ? 'roxwood' : 'alta' }}">
                                    {{ $doctor->name }} ({{ $doctor->role->display_name ?? $doctor->role->name }})
                                </option>
                            @endforeach
                        </datalist>
                    </div>
                    <p class="text-[11px] text-sky-400/70 mt-1.5"><i class="fas fa-info-circle mr-1"></i>Nama dokter dapat diketik atau dipilih dari daftar.</p>
                </div>

                <!-- Spesialisasi / Poliklinik -->
                <div>
                    <label for="poli" class="block text-sm font-semibold text-gray-200 mb-2">
                        Spesialisasi / Poliklinik (Poli)
                    </label>
                    <div class="relative">
                        <i class="fas fa-stethoscope absolute left-4 top-1/2 -translate-y-1/2 text-sky-400 pointer-events-none text-sm"></i>
                        <input type="text"
                               name="poli"
                               id="poli"
                               list="poli_suggestions"
                               value="{{ old('poli', $doctorSchedule->poli) }}"
                               placeholder="Ketik atau pilih nama spesialisasi dokter..."
                               autocomplete="off"
                               required
                               class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-lg pl-12 pr-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 text-sm transition-all">
                        <datalist id="poli_suggestions">
                            @foreach($poliList as $pli)
                                <option value="{{ $pli }}">{{ $pli }}</option>
                            @endforeach
                        </datalist>
                    </div>

                    <!-- Quick Click Badges for Specialties -->
                    <div class="mt-3">
                        <div class="text-[11px] text-sky-300 font-semibold mb-2 flex items-center justify-between">
                            <span><i class="fas fa-magic mr-1"></i>Pilih Cepat Spesialisasi:</span>
                            <span class="text-sky-400/70 text-[10px]">Klik salah satu untuk mengganti</span>
                        </div>
                        <div class="flex flex-wrap gap-1.5 max-h-40 overflow-y-auto p-2.5 rounded-xl bg-black/25 border border-white/10 custom-scrollbar">
                            @php
                                $quickBadges = [
                                    'Spesialis Bedah (Sp.B)',
                                    'Spesialis Penyakit Dalam (Sp.PD)',
                                    'Spesialis Anak (Sp.A)',
                                    'Spesialis Kandungan (Sp.OG)',
                                    'Spesialis Jantung (Sp.JP)',
                                    'Spesialis Saraf (Sp.S)',
                                    'Spesialis Mata (Sp.M)',
                                    'Spesialis THT-KL',
                                    'Spesialis Kulit & Kelamin (Sp.DV)',
                                    'Spesialis Ortopedi (Sp.OT)',
                                    'Spesialis Jiwa / Psikiatri (Sp.KJ)',
                                    'Spesialis Paru (Sp.P)',
                                    'Spesialis Anestesiologi (Sp.An)',
                                    'Spesialis Radiologi (Sp.Rad)',
                                    'Spesialis Bedah Saraf (Sp.BS)',
                                    'Spesialis Urologi (Sp.U)',
                                    'Spesialis Onkologi',
                                    'Spesialis Bedah Mulut',
                                    'Poli Umum',
                                ];
                            @endphp
                            @foreach($quickBadges as $badge)
                                <button type="button"
                                        onclick="selectPoli('🩺 {{ $badge }}')"
                                        class="px-2.5 py-1 text-xs rounded-lg bg-sky-500/10 hover:bg-sky-500/30 text-sky-200 border border-sky-400/20 hover:border-sky-400/60 transition-all cursor-pointer">
                                    🩺 {{ $badge }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Days (Multiple Selection) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-200 mb-3">Hari Praktek</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @foreach($days as $day)
                            <label class="flex items-center p-3 rounded-xl border border-white/10 bg-white/5 hover:bg-white/10 cursor-pointer transition-all has-[:checked]:bg-sky-500/20 has-[:checked]:border-sky-500/50">
                                <input type="checkbox" name="day[]" value="{{ $day }}" @checked(in_array($day, old('day', $doctorSchedule->day ?? [])))
                                       class="w-4 h-4 rounded border-white/20 bg-white/10 text-sky-500 focus:ring-sky-500 focus:ring-offset-0">
                                <span class="ml-2 text-sm text-gray-200 font-medium">{{ $day }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Hours -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="start_time" class="block text-sm font-semibold text-gray-200 mb-2">Jam Mulai Praktek</label>
                        <div class="relative">
                            <i class="far fa-clock absolute left-4 top-1/2 -translate-y-1/2 text-sky-400 pointer-events-none"></i>
                            <input type="time" name="start_time" id="start_time" value="{{ old('start_time', \Carbon\Carbon::parse($doctorSchedule->start_time)->format('H:i')) }}" required
                                   class="w-full bg-white/10 text-white border border-white/20 rounded-lg pl-12 pr-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 text-sm transition-all">
                        </div>
                    </div>
                    <div>
                        <label for="end_time" class="block text-sm font-semibold text-gray-200 mb-2">Jam Selesai Praktek</label>
                        <div class="relative">
                            <i class="far fa-clock absolute left-4 top-1/2 -translate-y-1/2 text-sky-400 pointer-events-none"></i>
                            <input type="time" name="end_time" id="end_time" value="{{ old('end_time', \Carbon\Carbon::parse($doctorSchedule->end_time)->format('H:i')) }}" required
                                   class="w-full bg-white/10 text-white border border-white/20 rounded-lg pl-12 pr-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 text-sm transition-all">
                        </div>
                    </div>
                </div>

                <!-- Hospital & Status -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2">
                    <div>
                        <label class="block text-sm font-semibold text-gray-200 mb-3">Rumah Sakit</label>
                        <div class="flex gap-4">
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="hospital" value="alta" @checked(old('hospital', $doctorSchedule->hospital) == 'alta') class="sr-only peer">
                                <div class="flex items-center justify-center p-3 rounded-xl border border-white/10 bg-white/5 hover:bg-white/10 transition-all peer-checked:bg-sky-500/40 peer-checked:border-sky-400 peer-checked:text-white peer-checked:shadow-lg peer-checked:shadow-sky-500/20">
                                    <i class="fas fa-hospital mr-2 text-cyan-300"></i>
                                    <span class="text-sm font-medium">Alta Hospital</span>
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="hospital" value="roxwood" @checked(old('hospital', $doctorSchedule->hospital) == 'roxwood') class="sr-only peer">
                                <div class="flex items-center justify-center p-3 rounded-xl border border-white/10 bg-white/5 hover:bg-white/10 transition-all peer-checked:bg-purple-500/40 peer-checked:border-purple-400 peer-checked:text-white peer-checked:shadow-lg peer-checked:shadow-purple-500/20">
                                    <i class="fas fa-hospital-alt mr-2 text-amber-300"></i>
                                    <span class="text-sm font-medium">Roxwood Hospital</span>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-200 mb-3">Status Publikasi</label>
                        <div class="flex items-center h-[50px]">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $doctorSchedule->is_active)) class="sr-only peer">
                                <div class="w-11 h-6 bg-white/10 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                                <span class="ml-3 text-sm font-medium text-gray-200">Tampilkan di Portal Jadwal Publik</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-semibold text-gray-200 mb-2">Catatan Praktek (Opsional)</label>
                    <textarea name="notes" id="notes" rows="3"
                              class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 text-sm transition-all"
                              placeholder="Misal: Perjanjian wajib H-1, kuota 10 pasien per hari...">{{ old('notes', $doctorSchedule->notes) }}</textarea>
                </div>

                <!-- Form Actions -->
                <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-white/10">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white rounded-xl font-bold transition-all duration-300 transform hover:scale-[1.01] shadow-xl">
                        <i class="fas fa-save mr-2"></i><span>Perbarui Jadwal Dokter</span>
                    </button>
                    <a href="{{ route('admin.doctor-schedules.index') }}" class="inline-flex items-center justify-center px-8 py-4 bg-white/5 hover:bg-white/10 border border-white/20 text-white rounded-xl font-bold transition-all duration-300">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    function selectPoli(poliValue) {
        const input = document.getElementById('poli');
        if (input) {
            input.value = poliValue;
            input.focus();
        }
    }
</script>
@endsection

@endsection
