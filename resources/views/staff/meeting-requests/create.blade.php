@extends('layouts.app')

@section('title', 'Ajukan Meeting Baru - Portal Medis')

@section('content')
    <div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
        <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700"></div>
        <div class="absolute inset-0 bg-black bg-opacity-20"></div>

        <div class="relative max-w-6xl w-full mx-auto text-white">
            <!-- Header -->
            <div class="glass-effect rounded-2xl elegant-shadow-lg p-6 md:p-8 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
                            <i class="fas fa-calendar-plus mr-3 text-emerald-400"></i>Ajukan Meeting
                        </h1>
                        <p class="text-sky-200 text-lg">Buat pengajuan meeting baru dengan bukti foto</p>
                    </div>
                    <a href="{{ route('staff.meeting-requests.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-white/10 text-white rounded-lg hover:bg-white/20 transition-all duration-300 text-sm font-medium backdrop-blur-sm border border-white/20">
                        <i class="fas fa-history mr-2"></i>Lihat Semua Riwayat
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Form Section -->
                <div class="lg:col-span-2">
                    <div class="glass-effect rounded-2xl elegant-shadow-lg p-6 md:p-8 h-full">
                        @if(session('error'))
                            <div class="bg-red-500/15 border border-red-500/30 rounded-xl p-4 mb-6">
                                <div class="flex items-center">
                                    <i class="fas fa-exclamation-circle text-red-400 mr-3"></i>
                                    <p class="text-red-200 text-sm">{{ session('error') }}</p>
                                </div>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('staff.meeting-requests.store') }}" enctype="multipart/form-data" class="space-y-6">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Date -->
                                <div class="md:col-span-2">
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

                            <!-- Photo Proof -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-3">
                                    <i class="fas fa-image mr-2 text-pink-400"></i>Bukti Foto Meeting <span class="text-red-400">*</span>
                                </label>
                                <div class="relative group">
                                    <input type="file" name="photo" id="photo" accept="image/*"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                        required onchange="previewImage(this)">
                                    <div class="w-full bg-white/10 border-2 border-dashed border-white/20 rounded-xl p-6 text-center group-hover:border-sky-400/50 transition-all duration-300" id="drop-zone">
                                        <div id="upload-placeholder">
                                            <i class="fas fa-cloud-upload-alt text-3xl text-sky-400 mb-2"></i>
                                            <p class="text-sm text-gray-300">Klik atau drag foto bukti meeting ke sini</p>
                                            <p class="text-xs text-gray-500 mt-1">JPG, PNG atau GIF (Maks. 4MB)</p>
                                        </div>
                                        <div id="image-preview" class="hidden">
                                            <img src="" alt="Preview" class="max-h-48 mx-auto rounded-lg shadow-lg">
                                            <p class="text-xs text-emerald-400 mt-2 font-medium"><i class="fas fa-check-circle mr-1"></i>Foto terpilih</p>
                                        </div>
                                    </div>
                                </div>
                                @error('photo')
                                    <p class="text-red-300 text-sm mt-2"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Reason -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-3">
                                    <i class="fas fa-comment-alt mr-2 text-purple-400"></i>Alasan Meeting
                                </label>
                                <textarea name="reason" rows="3" placeholder="Jelaskan tujuan dan keperluan meeting Anda..."
                                    class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 transition-all duration-300 resize-none"
                                    required minlength="10" maxlength="1000">{{ old('reason') }}</textarea>
                                <div class="flex justify-between mt-2">
                                    @error('reason')
                                        <p class="text-red-300 text-sm"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                    @else
                                        <p class="text-gray-500 text-xs text-blue-300/60">Tuliskan agenda atau hasil ringkas meeting</p>
                                    @enderror
                                    <p class="text-gray-500 text-xs"><span id="charCount">0</span>/1000</p>
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

                <!-- History Section -->
                <div class="lg:col-span-1">
                    <div class="glass-effect rounded-2xl elegant-shadow-lg p-6 md:p-8 h-full">
                        <h3 class="text-xl font-bold text-white mb-6 flex items-center">
                            <i class="fas fa-history mr-3 text-sky-400"></i>Riwayat Terakhir
                        </h3>

                        @if($recentRequests->isEmpty())
                            <div class="text-center py-10">
                                <div class="w-16 h-16 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-calendar-alt text-gray-500 text-xl"></i>
                                </div>
                                <p class="text-gray-400 text-sm">Belum ada pengajuan</p>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach($recentRequests as $req)
                                    @php
                                        $badge = $req->getStatusBadge();
                                        $colorMap = [
                                            'yellow' => ['bg' => 'bg-yellow-500/20', 'text' => 'text-yellow-300'],
                                            'green' => ['bg' => 'bg-green-500/20', 'text' => 'text-green-300'],
                                            'red' => ['bg' => 'bg-red-500/20', 'text' => 'text-red-300'],
                                        ];
                                        $colors = $colorMap[$badge['color']] ?? ['bg' => 'bg-gray-500/20', 'text' => 'text-gray-300'];
                                    @endphp
                                    <div class="bg-white/5 border border-white/10 rounded-xl p-4 hover:bg-white/10 transition-all duration-200">
                                        <div class="flex justify-between items-start mb-2">
                                            <span class="text-white font-bold text-sm">{{ $req->requested_date->format('d/m/Y') }}</span>
                                            <span class="{{ $colors['bg'] }} {{ $colors['text'] }} px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">
                                                {{ $badge['label'] }}
                                            </span>
                                        </div>
                                        <p class="text-gray-400 text-xs mb-2 italic line-clamp-1">"{{ $req->reason }}"</p>
                                        <div class="flex items-center text-[10px] text-sky-200/70">
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ Carbon\Carbon::parse($req->start_time)->format('H:i') }} - {{ Carbon\Carbon::parse($req->end_time)->format('H:i') }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-6 text-center">
                                <a href="{{ route('staff.meeting-requests.index') }}" class="text-sky-400 hover:text-sky-300 text-xs font-semibold underline underline-offset-4 decoration-sky-400/30">
                                    Lihat Semua Riwayat <i class="fas fa-chevron-right ml-1"></i>
                                </a>
                            </div>
                        @endif

                        <!-- Info Box -->
                        <div class="mt-8 bg-blue-500/10 border border-blue-500/20 rounded-xl p-4">
                            <div class="flex">
                                <i class="fas fa-info-circle text-blue-400 mr-2 mt-0.5 text-xs"></i>
                                <p class="text-blue-200 text-[11px] leading-relaxed">
                                    <strong>Penting:</strong> Pastikan foto bukti memperlihatkan minimal 3 peserta atau agenda meeting yang jelas.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
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
                    document.getElementById('durationPreview').classList.remove('border-sky-500/30 text-sky-200');
                    document.getElementById('durationPreview').classList.add('border-red-500/30 text-red-200');
                } else {
                    document.getElementById('durationPreview').classList.remove('border-red-500/30 text-red-200');
                    document.getElementById('durationPreview').classList.add('border-sky-500/30 text-sky-200');
                }
            } else {
                document.getElementById('durationPreview').classList.add('hidden');
            }
        }

        function previewImage(input) {
            const placeholder = document.getElementById('upload-placeholder');
            const previewContainer = document.getElementById('image-preview');
            const previewImg = previewContainer.querySelector('img');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    placeholder.classList.add('hidden');
                    previewContainer.classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
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
