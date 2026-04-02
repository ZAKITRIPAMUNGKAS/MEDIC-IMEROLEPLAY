@extends('layouts.app')

@section('title', 'Ajukan Meeting Baru - Portal Medis')

@section('content')
    <div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
        <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700"></div>
        <div class="absolute inset-0 bg-black bg-opacity-20"></div>

        <div class="relative max-w-2xl w-full mx-auto text-white">
            <!-- Header -->
            <div class="glass-effect rounded-2xl elegant-shadow-lg p-6 md:p-8 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
                            <i class="fas fa-plus-circle mr-3 text-emerald-400"></i>Ajukan Meeting
                        </h1>
                        <p class="text-sky-200 text-lg">Buat pengajuan meeting baru untuk persetujuan admin</p>
                    </div>
                    <a href="{{ route('staff.meeting-requests.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-white/10 text-white rounded-lg hover:bg-white/20 transition-all duration-300 text-sm font-medium backdrop-blur-sm border border-white/20">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                </div>
            </div>

            <!-- Form -->
            <div class="glass-effect rounded-2xl elegant-shadow-lg p-6 md:p-8">
                @if(session('error'))
                    <div class="bg-red-500/15 border border-red-500/30 rounded-xl p-4 mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-400 mr-3"></i>
                            <p class="text-red-200 text-sm">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('staff.meeting-requests.store') }}" class="space-y-6">
                    @csrf

                    <!-- Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-3">
                            <i class="fas fa-calendar mr-2 text-sky-400"></i>Tanggal Meeting
                        </label>
                        <input type="date" name="requested_date" value="{{ old('requested_date', date('Y-m-d')) }}"
                            class="w-full bg-white/10 text-white border border-white/20 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 transition-all duration-300"
                            required>
                        @error('requested_date')
                            <p class="text-red-300 text-sm mt-2"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Time Range -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-3">
                                <i class="fas fa-clock mr-2 text-emerald-400"></i>Waktu Mulai
                            </label>
                            <input type="time" name="start_time" value="{{ old('start_time') }}" id="start_time"
                                class="w-full bg-white/10 text-white border border-white/20 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 transition-all duration-300"
                                required onchange="calculateMeetingDuration()">
                            @error('start_time')
                                <p class="text-red-300 text-sm mt-2"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-3">
                                <i class="fas fa-clock mr-2 text-orange-400"></i>Waktu Selesai
                            </label>
                            <input type="time" name="end_time" value="{{ old('end_time') }}" id="end_time"
                                class="w-full bg-white/10 text-white border border-white/20 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 transition-all duration-300"
                                required onchange="calculateMeetingDuration()">
                            @error('end_time')
                                <p class="text-red-300 text-sm mt-2"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Duration Preview -->
                    <div id="durationPreview" class="bg-sky-500/15 border border-sky-500/30 rounded-xl p-4 hidden">
                        <div class="flex items-center">
                            <i class="fas fa-hourglass-half text-sky-400 mr-3"></i>
                            <div>
                                <p class="text-sky-200 text-sm font-medium">Estimasi Durasi Meeting</p>
                                <p class="text-white text-lg font-bold" id="durationText">-</p>
                            </div>
                        </div>
                    </div>

                    <!-- Reason -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-3">
                            <i class="fas fa-comment-alt mr-2 text-purple-400"></i>Alasan Meeting
                        </label>
                        <textarea name="reason" rows="4" placeholder="Jelaskan tujuan dan keperluan meeting Anda..."
                            class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 transition-all duration-300 resize-none"
                            required minlength="10" maxlength="1000">{{ old('reason') }}</textarea>
                        <div class="flex justify-between mt-2">
                            @error('reason')
                                <p class="text-red-300 text-sm"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @else
                                <p class="text-gray-500 text-xs">Minimal 10 karakter</p>
                            @enderror
                            <p class="text-gray-500 text-xs"><span id="charCount">0</span>/1000</p>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="bg-blue-500/10 border border-blue-500/20 rounded-xl p-4">
                        <div class="flex">
                            <i class="fas fa-info-circle text-blue-400 mr-3 mt-0.5"></i>
                            <div class="text-blue-200 text-sm space-y-1">
                                <p><strong>Catatan:</strong></p>
                                <ul class="list-disc list-inside space-y-1 text-xs text-blue-300">
                                    <li>Pengajuan akan ditinjau oleh admin/manager</li>
                                    <li>Setelah disetujui, jam meeting otomatis ditambahkan ke laporan absensi</li>
                                    <li>Durasi maksimal 5 jam per pengajuan</li>
                                    <li>Satu pengajuan per tanggal (yang masih pending)</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="flex justify-end pt-4 border-t border-white/10">
                        <button type="submit"
                            class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl">
                            <i class="fas fa-paper-plane mr-2"></i>Kirim Pengajuan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function calculateMeetingDuration() {
            const start = document.getElementById('start_time').value;
            const end = document.getElementById('end_time').value;

            if (start && end) {
                const [startH, startM] = start.split(':').map(Number);
                const [endH, endM] = end.split(':').map(Number);

                let durationMinutes = (endH * 60 + endM) - (startH * 60 + startM);
                if (durationMinutes < 0) durationMinutes += 24 * 60;

                const hours = Math.floor(durationMinutes / 60);
                const minutes = durationMinutes % 60;

                let text = '';
                if (hours > 0 && minutes > 0) text = `${hours} jam ${minutes} menit`;
                else if (hours > 0) text = `${hours} jam`;
                else text = `${minutes} menit`;

                document.getElementById('durationText').textContent = text;
                document.getElementById('durationPreview').classList.remove('hidden');

                // Warn if too long
                if (durationMinutes > 300) {
                    document.getElementById('durationText').textContent = text + ' ⚠️ (Melebihi batas 5 jam!)';
                    document.getElementById('durationPreview').classList.remove('border-sky-500/30');
                    document.getElementById('durationPreview').classList.add('border-red-500/30');
                } else {
                    document.getElementById('durationPreview').classList.remove('border-red-500/30');
                    document.getElementById('durationPreview').classList.add('border-sky-500/30');
                }
            } else {
                document.getElementById('durationPreview').classList.add('hidden');
            }
        }

        // Character count for reason textarea
        const reasonTextarea = document.querySelector('textarea[name="reason"]');
        const charCount = document.getElementById('charCount');
        if (reasonTextarea) {
            charCount.textContent = reasonTextarea.value.length;
            reasonTextarea.addEventListener('input', function() {
                charCount.textContent = this.value.length;
            });
        }
    </script>
@endsection
