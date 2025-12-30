@extends('layouts.app')

@section('title', 'Form ' . ($formTypes[$type] ?? 'Layanan Medis') . ' - Portal Medis MPK-BA')

@section('content')
    <div class="relative min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900"></div>
        <div class="absolute inset-0 bg-black bg-opacity-30"></div>

        <div class="relative max-w-4xl w-full mx-auto">
            <div class="glass-effect rounded-2xl elegant-shadow-lg p-4 sm:p-6 md:p-8 lg:p-12">
                <div class="text-center mb-6 sm:mb-8 md:mb-10">
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-2">
                        {{ $formTypes[$type] ?? 'Formulir Layanan' }}
                    </h1>
                    <p class="text-blue-100 text-sm sm:text-base font-medium">Silakan lengkapi informasi di bawah ini dengan
                        cermat.</p>
                </div>

                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-500/20 border border-red-400/50 rounded-xl backdrop-blur-sm animate-fade-in-up"
                        id="errorAlert">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-red-500/30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-red-300 text-lg"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-red-200 font-bold mb-1">Tidak Dapat Mengirim Form</h4>
                                <p class="text-red-100 text-sm leading-relaxed">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                    <script>
                        // Scroll to top and show alert when error alert is shown
                        (function () {
                            const errorAlert = document.getElementById('errorAlert');
                            if (errorAlert) {
                                // Scroll to top immediately
                                window.scrollTo({ top: 0, behavior: 'smooth' });
                                errorAlert.scrollIntoView({ behavior: 'smooth', block: 'start' });
                                // Show browser alert  for better visibility
                                setTimeout(function () {
                                    alert('{{ addslashes(session('error')) }}');
                                }, 100);
                            }
                        })();
                    </script>
                @endif

                @if(session('success'))
                    <div
                        class="mb-6 p-4 bg-green-500/20 border border-green-400/50 rounded-xl backdrop-blur-sm animate-fade-in-up">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-green-500/30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-300 text-lg"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-green-200 font-bold mb-1">Berhasil</h4>
                                <p class="text-green-100 text-sm leading-relaxed">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('public.form.submit') }}" id="medicalForm"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="form_type" value="{{ $type }}">

                    <div class="border-b border-white/10 pb-6 mb-8">
                        <h3 class="text-xl font-semibold text-white mb-6">Informasi Data Diri</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            <div>
                                <label for="hospital" class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                    Rumah Sakit <span class="text-red-400">*</span>
                                </label>
                                <select id="hospital" name="hospital"
                                    class="form-select @error('hospital') border-red-500 @enderror" required>
                                    <option value="">-- Pilih Rumah Sakit --</option>
                                    <option value="alta" @if(old('hospital') == 'alta') selected @endif
                                        class="bg-slate-900 text-white font-bold">Alta Hospital</option>
                                    <option value="roxwood" @if(old('hospital') == 'roxwood') selected @endif
                                        class="bg-slate-900 text-white font-bold">Roxwood Hospital</option>
                                </select>
                                @error('hospital') <p class="form-error">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="character_name"
                                    class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                    Nama Lengkap <span class="text-red-400">*</span>
                                </label>
                                <input type="text" id="character_name" name="character_name"
                                    value="{{ old('character_name') }}"
                                    class="form-input @error('character_name') border-red-500 @enderror"
                                    placeholder="Masukkan Nama Lengkap" required>
                                @error('character_name') <p class="form-error">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="birth_date" class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                    Tanggal Lahir <span class="text-red-400">*</span>
                                </label>
                                <input type="date" id="birth_date" name="form_data[birth_date]"
                                    value="{{ old('form_data.birth_date') }}"
                                    class="form-input @error('form_data.birth_date') border-red-500 @enderror" required>
                                @error('form_data.birth_date') <p class="form-error">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="gender" class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                    Gender <span class="text-red-400">*</span>
                                </label>
                                <select id="gender" name="form_data[gender]"
                                    class="form-select @error('form_data.gender') border-red-500 @enderror" required>
                                    <option value="" class="bg-slate-900 text-white font-bold">Pilih Gender</option>
                                    <option value="Laki-laki" @if(old('form_data.gender') == 'Laki-laki') selected @endif
                                        class="bg-slate-900 text-white font-bold">Laki-laki</option>
                                    <option value="Perempuan" @if(old('form_data.gender') == 'Perempuan') selected @endif
                                        class="bg-slate-900 text-white font-bold">Perempuan</option>
                                </select>
                                @error('form_data.gender') <p class="form-error">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="age" class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                    Umur <span class="text-red-400">*</span>
                                </label>
                                <input type="number" id="age" name="form_data[age]" value="{{ old('form_data.age') }}"
                                    class="form-input @error('form_data.age') border-red-500 @enderror"
                                    placeholder="Contoh: 25" required>
                                @error('form_data.age') <p class="form-error">{{ $message }}</p> @enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label for="occupation" class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                    Pekerjaan <span class="text-red-400">*</span>
                                </label>
                                <input type="text" id="occupation" name="form_data[occupation]"
                                    value="{{ old('form_data.occupation') }}"
                                    class="form-input @error('form_data.occupation') border-red-500 @enderror"
                                    placeholder="Contoh: Pengusaha" required>
                                @error('form_data.occupation') <p class="form-error">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="citizen_id"
                                    class="block text-sm font-medium text-white mb-2 font-bold text-lg">Citizen ID</label>
                                <input type="text" id="citizen_id" name="citizen_id" value="{{ old('citizen_id') }}"
                                    class="form-input" placeholder="Contoh: 123456">
                            </div>
                            <div>
                                <label for="phone_number"
                                    class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                    No HP (IC) <span class="text-red-400">*</span>
                                </label>
                                <input type="text" id="phone_number" name="form_data[phone_number]"
                                    value="{{ old('form_data.phone_number') }}"
                                    class="form-input @error('form_data.phone_number') border-red-500 @enderror"
                                    placeholder="Contoh: 08123456789" required>
                                @error('form_data.phone_number') <p class="form-error">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-8">
                        <h3 class="text-xl font-bold text-white mb-6">
                            @if($type === 'pendaftaran_karakter')
                                Kronologi CK & Penyebab Kematian
                            @else
                                Detail Kebutuhan
                            @endif
                        </h3>
                        <div class="space-y-6">

                            @if($type === 'surat_kesehatan')
                                <div>
                                    <label for="purpose_sk"
                                        class="block text-sm font-medium text-white mb-2 font-bold text-lg">Keperluan</label>
                                    <select id="purpose_sk" name="form_data[purpose]" class="form-select">
                                        <option value="Pemeriksaan Rutin" class="bg-slate-900 text-white font-bold">Pemeriksaan
                                            Rutin</option>
                                        <option value="Lampiran Pembuatan Lisensi" class="bg-slate-900 text-white font-bold">
                                            Lampiran Pembuatan Lisensi</option>
                                        <option value="Lampiran Mendaftar Pekerjaan" class="bg-slate-900 text-white font-bold">
                                            Lampiran Mendaftar Pekerjaan</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="doctor_name_sk"
                                        class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                        Nama Dokter <span class="text-red-400">*</span>
                                    </label>
                                    <select id="doctor_name_sk" name="form_data[doctor_name]"
                                        class="form-select @error('form_data.doctor_name') border-red-500 @enderror" required>
                                        <option value="">-- Pilih Dokter --</option>
                                        @foreach($doctors as $doctor)
                                            <option value="{{ $doctor->name }}"
                                                data-hospital="{{ $doctor->hospital ?? ($doctor->isRoxwood() ? 'roxwood' : 'alta') }}"
                                                @if(old('form_data.doctor_name') == $doctor->name) selected @endif
                                                class="bg-slate-900 text-white font-bold">
                                                {{ $doctor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('form_data.doctor_name') <p class="form-error">{{ $message }}</p> @enderror
                                </div>

                            @elseif($type === 'operasi_plastik')
                                <div>
                                    <label for="purpose_op"
                                        class="block text-sm font-medium text-white mb-2 font-bold text-lg">Jenis Operasi
                                        Plastik</label>
                                    <select id="purpose_op" name="form_data[purpose]" class="form-select">
                                        <option value="Rekontruksi Wajah" class="bg-slate-900 text-white font-bold">Rekontruksi
                                            Wajah</option>
                                        <option value="Suntik Kulit" class="bg-slate-900 text-white font-bold">Suntik Kulit
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label for="doctor_name_op"
                                        class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                        Nama Dokter <span class="text-red-400">*</span>
                                    </label>
                                    <select id="doctor_name_op" name="form_data[doctor_name]"
                                        class="form-select @error('form_data.doctor_name') border-red-500 @enderror" required>
                                        <option value="">-- Pilih Dokter --</option>
                                        @foreach($doctors as $doctor)
                                            <option value="{{ $doctor->name }}"
                                                data-hospital="{{ $doctor->hospital ?? ($doctor->isRoxwood() ? 'roxwood' : 'alta') }}"
                                                @if(old('form_data.doctor_name') == $doctor->name) selected @endif
                                                class="bg-slate-900 text-white font-bold">
                                                {{ $doctor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('form_data.doctor_name') <p class="form-error">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="photo_ktp_op"
                                        class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                        Upload Foto KTP <span class="text-red-400">*</span>
                                    </label>
                                    <input type="file" id="photo_ktp_op" name="form_data[photo_ktp]"
                                        class="form-file @error('form_data.photo_ktp') border-red-500 @enderror"
                                        accept="image/*" required>
                                    @error('form_data.photo_ktp') <p class="form-error">{{ $message }}</p> @enderror
                                    <p class="text-blue-200 text-sm mt-1">Format: JPG, PNG, GIF. Maksimal 4MB</p>
                                </div>
                                <div>
                                    <label for="photo_skb_op"
                                        class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                        Upload Foto SKB <span class="text-red-400">*</span>
                                    </label>
                                    <input type="file" id="photo_skb_op" name="form_data[photo_skb]"
                                        class="form-file @error('form_data.photo_skb') border-red-500 @enderror"
                                        accept="image/*" required>
                                    @error('form_data.photo_skb') <p class="form-error">{{ $message }}</p> @enderror
                                    <p class="text-blue-200 text-sm mt-1">Format: JPG, PNG, GIF. Maksimal 4MB</p>
                                </div>

                            @elseif($type === 'tes_psikologi')
                                <div class="mb-6">
                                    <h3 class="text-xl font-semibold text-white mb-4">Tes Psikologi Multi-Aspek</h3>
                                    <p class="text-blue-200 text-sm mb-6">Silakan jawab pertanyaan berikut dengan jujur. Hasil
                                        tes akan membantu psikolog dalam memberikan evaluasi yang tepat.</p>

                                    <!-- Big Five Personality Test (BFI-10) -->
                                    <div class="mb-8">
                                        <h4 class="text-lg font-semibold text-white mb-4">Bagian 1: Tes Kepribadian (BFI-10)
                                        </h4>
                                        <p class="text-blue-200 text-sm mb-4">Pilih jawaban yang paling sesuai dengan diri Anda:
                                        </p>

                                        @php
                                            $bigfive_questions = [
                                                "Saya adalah seseorang yang cenderung ekstrovert, suka bergaul.",
                                                "Saya adalah seseorang yang cenderung bersikap kritis, suka berdebat.",
                                                "Saya adalah seseorang yang cenderung dapat dipercaya, tekun.",
                                                "Saya adalah seseorang yang cenderung mudah merasa cemas, khawatir.",
                                                "Saya adalah seseorang yang cenderung terbuka pada pengalaman baru.",
                                                "Saya adalah seseorang yang cenderung pendiam, tertutup.",
                                                "Saya adalah seseorang yang cenderung ramah, hangat.",
                                                "Saya adalah seseorang yang cenderung ceroboh, kurang teliti.",
                                                "Saya adalah seseorang yang cenderung stabil secara emosional, tenang.",
                                                "Saya adalah seseorang yang cenderung konvensional, kurang imajinatif."
                                            ];
                                            $bigfive_scale = [
                                                1 => "Sangat Tidak Setuju",
                                                2 => "Tidak Setuju",
                                                3 => "Netral",
                                                4 => "Setuju",
                                                5 => "Sangat Setuju"
                                            ];
                                        @endphp

                                        @foreach($bigfive_questions as $i => $question)
                                            <div
                                                class="mb-6 p-6 bg-white/5 rounded-xl border border-white/10 hover:border-sky-500/30 transition-all duration-300">
                                                <p class="text-white mb-4 font-medium text-lg">{{ $i + 1 }}. {{ $question }}</p>
                                                <select name="form_data[bigfive{{ $i + 1 }}]" class="form-select w-full" required>
                                                    <option value="">-- Pilih Jawaban --</option>
                                                    @foreach($bigfive_scale as $val => $label)
                                                        <option value="{{ $val }}" @if(old('form_data.bigfive' . ($i + 1)) == $val)
                                                        selected @endif class="bg-slate-900 text-white font-bold">
                                                            {{ $val }} - {{ $label }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Stress Test (PSS-10) -->
                                    <div class="mb-8">
                                        <h4 class="text-lg font-semibold text-white mb-4">Bagian 2: Tes Stres (PSS-10)</h4>
                                        <p class="text-blue-200 text-sm mb-4">Seberapa sering Anda mengalami hal-hal berikut
                                            dalam sebulan terakhir:</p>

                                        @php
                                            $stress_questions = [
                                                "Seberapa sering Anda merasa terganggu oleh sesuatu yang tidak terduga?",
                                                "Seberapa sering Anda merasa tidak mampu mengendalikan hal-hal penting dalam hidup Anda?",
                                                "Seberapa sering Anda merasa gelisah dan tertekan?",
                                                "Seberapa sering Anda merasa percaya diri dalam kemampuan mengatasi masalah pribadi?",
                                                "Seberapa sering Anda merasa bahwa hal-hal berjalan sesuai keinginan Anda?",
                                                "Seberapa sering Anda merasa tidak mampu mengatasi semua hal yang harus dilakukan?",
                                                "Seberapa sering Anda merasa dapat mengendalikan gangguan dalam hidup Anda?",
                                                "Seberapa sering Anda merasa bahwa hal-hal di luar kendali Anda?",
                                                "Seberapa sering Anda merasa dapat mengendalikan waktu Anda?",
                                                "Seberapa sering Anda merasa kesulitan menanggulangi masalah?"
                                            ];
                                            $stress_scale = [
                                                0 => "Tidak Pernah",
                                                1 => "Jarang",
                                                2 => "Kadang-Kadang",
                                                3 => "Sering",
                                                4 => "Sangat Sering"
                                            ];
                                        @endphp

                                        @foreach($stress_questions as $i => $question)
                                            <div
                                                class="mb-6 p-6 bg-white/5 rounded-xl border border-white/10 hover:border-sky-500/30 transition-all duration-300">
                                                <p class="text-white mb-4 font-medium text-lg">{{ $i + 1 }}. {{ $question }}</p>
                                                <select name="form_data[stress{{ $i + 1 }}]" class="form-select w-full" required>
                                                    <option value="">-- Pilih Jawaban --</option>
                                                    @foreach($stress_scale as $val => $label)
                                                        <option value="{{ $val }}" @if(old('form_data.stress' . ($i + 1)) == $val)
                                                        selected @endif class="bg-slate-900 text-white font-bold">
                                                            {{ $val }} - {{ $label }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Self-Esteem Test (RSES) -->
                                    <div class="mb-8">
                                        <h4 class="text-lg font-semibold text-white mb-4">Bagian 3: Tes Harga Diri (RSES)</h4>
                                        <p class="text-blue-200 text-sm mb-4">Pilih jawaban yang paling sesuai dengan perasaan
                                            Anda:
                                        </p>

                                        @php
                                            $esteem_questions = [
                                                "Saya merasa bahwa saya adalah orang yang berharga, setara dengan orang lain.",
                                                "Saya merasa saya memiliki sejumlah kualitas yang baik.",
                                                "Secara keseluruhan, saya cenderung merasa bahwa saya adalah orang yang gagal.",
                                                "Saya mampu melakukan sesuatu sama baiknya dengan kebanyakan orang lain.",
                                                "Saya merasa tidak banyak hal yang bisa saya banggakan.",
                                                "Saya memiliki sikap positif terhadap diri saya sendiri.",
                                                "Secara keseluruhan, saya puas dengan diri saya.",
                                                "Saya merasa tidak berguna pada beberapa kesempatan.",
                                                "Saya berharap dapat lebih menghargai diri saya.",
                                                "Kadang-kadang saya benar-benar merasa tidak berguna."
                                            ];
                                            $esteem_scale = [
                                                1 => "Sangat Tidak Setuju",
                                                2 => "Tidak Setuju",
                                                3 => "Setuju",
                                                4 => "Sangat Setuju"
                                            ];
                                        @endphp

                                        @foreach($esteem_questions as $i => $question)
                                            <div
                                                class="mb-6 p-6 bg-white/5 rounded-xl border border-white/10 hover:border-sky-500/30 transition-all duration-300">
                                                <p class="text-white mb-4 font-medium text-lg">{{ $i + 1 }}. {{ $question }}</p>
                                                <select name="form_data[esteem{{ $i + 1 }}]" class="form-select w-full" required>
                                                    <option value="">-- Pilih Jawaban --</option>
                                                    @foreach($esteem_scale as $val => $label)
                                                        <option value="{{ $val }}" @if(old('form_data.esteem' . ($i + 1)) == $val)
                                                        selected @endif class="bg-slate-900 text-white font-bold">
                                                            {{ $val }} - {{ $label }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Pilihan Dokter/Psikolog -->
                                    <div class="mb-8">
                                        <div>
                                            <label for="doctor_name_tp"
                                                class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                                Nama Psikolog <span class="text-red-400">*</span>
                                            </label>
                                            <select id="doctor_name_tp" name="form_data[doctor_name]"
                                                class="form-select @error('form_data.doctor_name') border-red-500 @enderror"
                                                required>
                                                <option value="">-- Pilih Psikolog --</option>
                                                @foreach($doctors as $doctor)
                                                    <option value="{{ $doctor->name }}"
                                                        data-hospital="{{ $doctor->hospital ?? ($doctor->isRoxwood() ? 'roxwood' : 'alta') }}"
                                                        @if(old('form_data.doctor_name') == $doctor->name) selected @endif
                                                        class="bg-slate-900 text-white font-bold">
                                                        {{ $doctor->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('form_data.doctor_name') <p class="form-error">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                </div>

                            @elseif($type === 'surat_psikolog')
                                <div>
                                    <label for="purpose_sp"
                                        class="block text-sm font-medium text-white mb-2 font-bold text-lg">Keperluan</label>
                                    <select id="purpose_sp" name="form_data[purpose]" class="form-select">
                                        <option value="Konsultasi Psikolog" class="bg-slate-900 text-white font-bold">Konsultasi
                                            Psikolog</option>
                                        <option value="Lampiran Pembuatan Lisensi" class="bg-slate-900 text-white font-bold">
                                            Lampiran
                                            Pembuatan Lisensi</option>
                                        <option value="Lampiran Mendaftar Pekerjaan" class="bg-slate-900 text-white font-bold">
                                            Lampiran
                                            Mendaftar Pekerjaan</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="doctor_name_sp"
                                        class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                        Nama Psikolog <span class="text-red-400">*</span>
                                    </label>
                                    <select id="doctor_name_sp" name="form_data[doctor_name]"
                                        class="form-select @error('form_data.doctor_name') border-red-500 @enderror" required>
                                        <option value="">-- Pilih Psikolog --</option>
                                        @foreach($doctors as $doctor)
                                            <option value="{{ $doctor->name }}"
                                                data-hospital="{{ $doctor->hospital ?? ($doctor->isRoxwood() ? 'roxwood' : 'alta') }}"
                                                @if(old('form_data.doctor_name') == $doctor->name) selected @endif
                                                class="bg-slate-900 text-white font-bold">
                                                {{ $doctor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('form_data.doctor_name') <p class="form-error">{{ $message }}</p> @enderror
                                </div>

                            @elseif($type === 'konsultasi_medis')
                                <div>
                                    <label for="purpose_km"
                                        class="block text-sm font-medium text-white mb-2 font-bold text-lg">Jenis
                                        Konsultasi</label>
                                    <select id="purpose_km" name="form_data[purpose]" class="form-select">
                                        <option value="Konsultasi Umum" class="bg-slate-900 text-white font-bold">Konsultasi
                                            Umum
                                        </option>
                                        <option value="Konsultasi Spesialis" class="bg-slate-900 text-white font-bold">
                                            Konsultasi
                                            Spesialis</option>
                                        <option value="Konsultasi Darurat" class="bg-slate-900 text-white font-bold">Konsultasi
                                            Darurat
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label for="doctor_name_km"
                                        class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                        Nama Dokter <span class="text-red-400">*</span>
                                    </label>
                                    <select id="doctor_name_km" name="form_data[doctor_name]"
                                        class="form-select @error('form_data.doctor_name') border-red-500 @enderror" required>
                                        <option value="">-- Pilih Dokter --</option>
                                        @foreach($doctors as $doctor)
                                            <option value="{{ $doctor->name }}"
                                                data-hospital="{{ $doctor->hospital ?? ($doctor->isRoxwood() ? 'roxwood' : 'alta') }}"
                                                @if(old('form_data.doctor_name') == $doctor->name) selected @endif
                                                class="bg-slate-900 text-white font-bold">
                                                {{ $doctor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('form_data.doctor_name') <p class="form-error">{{ $message }}</p> @enderror
                                </div>

                            @elseif($type === 'laporan_kecelakaan')
                                <div>
                                    <label for="purpose_lk"
                                        class="block text-sm font-medium text-white mb-2 font-bold text-lg">Jenis
                                        Kecelakaan</label>
                                    <select id="purpose_lk" name="form_data[purpose]" class="form-select">
                                        <option value="Kecelakaan Lalu Lintas" class="bg-slate-900 text-white font-bold">
                                            Kecelakaan
                                            Lalu
                                            Lintas</option>
                                        <option value="Kecelakaan Kerja" class="bg-slate-900 text-white font-bold">Kecelakaan
                                            Kerja
                                        </option>
                                        <option value="Kecelakaan Olahraga" class="bg-slate-900 text-white font-bold">Kecelakaan
                                            Olahraga</option>
                                        <option value="Kecelakaan Lainnya" class="bg-slate-900 text-white font-bold">Kecelakaan
                                            Lainnya
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label for="doctor_name_lk"
                                        class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                        Nama Dokter <span class="text-red-400">*</span>
                                    </label>
                                    <select id="doctor_name_lk" name="form_data[doctor_name]"
                                        class="form-select @error('form_data.doctor_name') border-red-500 @enderror" required>
                                        <option value="">-- Pilih Dokter --</option>
                                        @foreach($doctors as $doctor)
                                            <option value="{{ $doctor->name }}"
                                                data-hospital="{{ $doctor->hospital ?? ($doctor->isRoxwood() ? 'roxwood' : 'alta') }}"
                                                @if(old('form_data.doctor_name') == $doctor->name) selected @endif
                                                class="bg-slate-900 text-white font-bold">
                                                {{ $doctor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('form_data.doctor_name') <p class="form-error">{{ $message }}</p> @enderror
                                </div>

                            @elseif($type === 'permintaan_ambulans')
                                <div>
                                    <label for="purpose_pa"
                                        class="block text-sm font-medium text-white mb-2 font-bold text-lg">Jenis
                                        Permintaan</label>
                                    <select id="purpose_pa" name="form_data[purpose]" class="form-select">
                                        <option value="Ambulans Darurat" class="bg-slate-900 text-white font-bold">Ambulans
                                            Darurat
                                        </option>
                                        <option value="Ambulans Transport" class="bg-slate-900 text-white font-bold">Ambulans
                                            Transport
                                        </option>
                                        <option value="Ambulans ICU" class="bg-slate-900 text-white font-bold">Ambulans ICU
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label for="doctor_name_pa"
                                        class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                        Nama Dokter <span class="text-red-400">*</span>
                                    </label>
                                    <select id="doctor_name_pa" name="form_data[doctor_name]"
                                        class="form-select @error('form_data.doctor_name') border-red-500 @enderror" required>
                                        <option value="">-- Pilih Dokter --</option>
                                        @foreach($doctors as $doctor)
                                            <option value="{{ $doctor->name }}"
                                                data-hospital="{{ $doctor->hospital ?? ($doctor->isRoxwood() ? 'roxwood' : 'alta') }}"
                                                @if(old('form_data.doctor_name') == $doctor->name) selected @endif
                                                class="bg-slate-900 text-white font-bold">
                                                {{ $doctor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('form_data.doctor_name') <p class="form-error">{{ $message }}</p> @enderror
                                </div>

                            @elseif($type === 'penyakit_dalam')
                                <div>
                                    <label for="purpose_pd"
                                        class="block text-sm font-medium text-white mb-2 font-bold text-lg">Keperluan
                                        Konsultasi</label>
                                    <select id="purpose_pd" name="form_data[purpose]" class="form-select">
                                        <option value="Konsultasi Umum" class="bg-slate-900 text-white font-bold">Konsultasi
                                            Umum
                                        </option>
                                        <option value="Pemeriksaan Rutin" class="bg-slate-900 text-white font-bold">Pemeriksaan
                                            Rutin
                                        </option>
                                        <option value="Konsultasi Spesialis" class="bg-slate-900 text-white font-bold">
                                            Konsultasi
                                            Spesialis</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="doctor_name_pd"
                                        class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                        Nama Dokter <span class="text-red-400">*</span>
                                    </label>
                                    <select id="doctor_name_pd" name="form_data[doctor_name]"
                                        class="form-select @error('form_data.doctor_name') border-red-500 @enderror" required>
                                        <option value="">-- Pilih Dokter --</option>
                                        @foreach($doctors as $doctor)
                                            <option value="{{ $doctor->name }}"
                                                data-hospital="{{ $doctor->hospital ?? ($doctor->isRoxwood() ? 'roxwood' : 'alta') }}"
                                                @if(old('form_data.doctor_name') == $doctor->name) selected @endif
                                                class="bg-slate-900 text-white font-bold">
                                                {{ $doctor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('form_data.doctor_name') <p class="form-error">{{ $message }}</p> @enderror
                                </div>

                            @elseif($type === 'spesialis_anak')
                                <div>
                                    <label for="purpose_sa"
                                        class="block text-sm font-medium text-white mb-2 font-bold text-lg">Keperluan
                                        Konsultasi</label>
                                    <select id="purpose_sa" name="form_data[purpose]" class="form-select">
                                        <option value="Konsultasi Anak" class="bg-slate-900 text-white font-bold">Konsultasi
                                            Anak
                                        </option>
                                        <option value="Pemeriksaan Tumbuh Kembang" class="bg-slate-900 text-white font-bold">
                                            Pemeriksaan
                                            Tumbuh Kembang</option>
                                        <option value="Imunisasi" class="bg-slate-900 text-white font-bold">Imunisasi</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="doctor_name_sa"
                                        class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                        Nama Dokter <span class="text-red-400">*</span>
                                    </label>
                                    <select id="doctor_name_sa" name="form_data[doctor_name]"
                                        class="form-select @error('form_data.doctor_name') border-red-500 @enderror" required>
                                        <option value="">-- Pilih Dokter --</option>
                                        @foreach($doctors as $doctor)
                                            <option value="{{ $doctor->name }}"
                                                data-hospital="{{ $doctor->hospital ?? ($doctor->isRoxwood() ? 'roxwood' : 'alta') }}"
                                                @if(old('form_data.doctor_name') == $doctor->name) selected @endif
                                                class="bg-slate-900 text-white font-bold">
                                                {{ $doctor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('form_data.doctor_name') <p class="form-error">{{ $message }}</p> @enderror
                                </div>

                            @elseif($type === 'spesialis_bedah')
                                <div>
                                    <label for="purpose_sb"
                                        class="block text-sm font-medium text-white mb-2 font-bold text-lg">Keperluan
                                        Konsultasi</label>
                                    <select id="purpose_sb" name="form_data[purpose]" class="form-select">
                                        <option value="Konsultasi Bedah" class="bg-slate-900 text-white font-bold">Konsultasi
                                            Bedah
                                        </option>
                                        <option value="Pemeriksaan Pra-Operasi" class="bg-slate-900 text-white font-bold">
                                            Pemeriksaan
                                            Pra-Operasi</option>
                                        <option value="Konsultasi Pasca-Operasi" class="bg-slate-900 text-white font-bold">
                                            Konsultasi
                                            Pasca-Operasi</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="doctor_name_sb"
                                        class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                        Nama Dokter <span class="text-red-400">*</span>
                                    </label>
                                    <select id="doctor_name_sb" name="form_data[doctor_name]"
                                        class="form-select @error('form_data.doctor_name') border-red-500 @enderror" required>
                                        <option value="">-- Pilih Dokter --</option>
                                        @foreach($doctors as $doctor)
                                            <option value="{{ $doctor->name }}"
                                                data-hospital="{{ $doctor->hospital ?? ($doctor->isRoxwood() ? 'roxwood' : 'alta') }}"
                                                @if(old('form_data.doctor_name') == $doctor->name) selected @endif
                                                class="bg-slate-900 text-white font-bold">
                                                {{ $doctor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('form_data.doctor_name') <p class="form-error">{{ $message }}</p> @enderror
                                </div>

                            @elseif($type === 'spesialis_mata')
                                <div>
                                    <label for="purpose_sm"
                                        class="block text-sm font-medium text-white mb-2 font-bold text-lg">Keperluan
                                        Konsultasi</label>
                                    <select id="purpose_sm" name="form_data[purpose]" class="form-select">
                                        <option value="Konsultasi Mata" class="bg-slate-900 text-white font-bold">Konsultasi
                                            Mata
                                        </option>
                                        <option value="Pemeriksaan Mata" class="bg-slate-900 text-white font-bold">Pemeriksaan
                                            Mata
                                        </option>
                                        <option value="Konsultasi Operasi Mata" class="bg-slate-900 text-white font-bold">
                                            Konsultasi
                                            Operasi Mata</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="doctor_name_sm"
                                        class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                        Nama Dokter <span class="text-red-400">*</span>
                                    </label>
                                    <select id="doctor_name_sm" name="form_data[doctor_name]"
                                        class="form-select @error('form_data.doctor_name') border-red-500 @enderror" required>
                                        <option value="">-- Pilih Dokter --</option>
                                        @foreach($doctors as $doctor)
                                            <option value="{{ $doctor->name }}"
                                                data-hospital="{{ $doctor->hospital ?? ($doctor->isRoxwood() ? 'roxwood' : 'alta') }}"
                                                @if(old('form_data.doctor_name') == $doctor->name) selected @endif
                                                class="bg-slate-900 text-white font-bold">
                                                {{ $doctor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('form_data.doctor_name') <p class="form-error">{{ $message }}</p> @enderror
                                </div>

                            @elseif($type === 'spesialis_urologi')
                                <div>
                                    <label for="purpose_km"
                                        class="block text-sm font-medium text-white mb-2 font-bold text-lg">Keperluan
                                        Konsultasi</label>
                                    <select id="purpose_km" name="form_data[purpose]" class="form-select">
                                        <option value="Konsultasi Umum" class="bg-slate-900 text-white font-bold">Konsultasi
                                            Umum
                                        </option>
                                        <option value="Pemeriksaan Rutin" class="bg-slate-900 text-white font-bold">Pemeriksaan
                                            Rutin
                                        </option>
                                        <option value="Konsultasi Spesialis" class="bg-slate-900 text-white font-bold">
                                            Konsultasi
                                            Spesialis</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="doctor_name_km"
                                        class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                        Nama Dokter <span class="text-red-400">*</span>
                                    </label>
                                    <select id="doctor_name_km" name="form_data[doctor_name]"
                                        class="form-select @error('form_data.doctor_name') border-red-500 @enderror" required>
                                        <option value="">-- Pilih Dokter --</option>
                                        @foreach($doctors as $doctor)
                                            <option value="{{ $doctor->name }}"
                                                data-hospital="{{ $doctor->hospital ?? ($doctor->isRoxwood() ? 'roxwood' : 'alta') }}"
                                                @if(old('form_data.doctor_name') == $doctor->name) selected @endif
                                                class="bg-slate-900 text-white font-bold">
                                                {{ $doctor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('form_data.doctor_name') <p class="form-error">{{ $message }}</p> @enderror
                                </div>

                            @elseif($type === 'spesialis_tht')
                                <div>
                                    <label for="purpose_lk"
                                        class="block text-sm font-medium text-white mb-2 font-bold text-lg">Jenis
                                        Kecelakaan</label>
                                    <select id="purpose_lk" name="form_data[purpose]" class="form-select">
                                        <option value="Kecelakaan Lalu Lintas" class="bg-slate-900 text-white font-bold">
                                            Kecelakaan
                                            Lalu
                                            Lintas</option>
                                        <option value="Kecelakaan Kerja" class="bg-slate-900 text-white font-bold">Kecelakaan
                                            Kerja
                                        </option>
                                        <option value="Kecelakaan Olahraga" class="bg-slate-900 text-white font-bold">Kecelakaan
                                            Olahraga</option>
                                        <option value="Kecelakaan Lainnya" class="bg-slate-900 text-white font-bold">Kecelakaan
                                            Lainnya
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label for="doctor_name_lk"
                                        class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                        Nama Dokter <span class="text-red-400">*</span>
                                    </label>
                                    <select id="doctor_name_lk" name="form_data[doctor_name]"
                                        class="form-select @error('form_data.doctor_name') border-red-500 @enderror" required>
                                        <option value="">-- Pilih Dokter --</option>
                                        @foreach($doctors as $doctor)
                                            <option value="{{ $doctor->name }}"
                                                data-hospital="{{ $doctor->hospital ?? ($doctor->isRoxwood() ? 'roxwood' : 'alta') }}"
                                                @if(old('form_data.doctor_name') == $doctor->name) selected @endif
                                                class="bg-slate-900 text-white font-bold">
                                                {{ $doctor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('form_data.doctor_name') <p class="form-error">{{ $message }}</p> @enderror
                                </div>

                            @elseif($type === 'spesialis_ortopedi')
                                <div>
                                    <label for="purpose_pa"
                                        class="block text-sm font-medium text-white mb-2 font-bold text-lg">Jenis
                                        Keadaan Darurat</label>
                                    <select id="purpose_pa" name="form_data[purpose]" class="form-select">
                                        <option value="Kecelakaan Serius" class="bg-slate-900 text-white font-bold">Kecelakaan
                                            Serius
                                        </option>
                                        <option value="Serangan Jantung" class="bg-slate-900 text-white font-bold">Serangan
                                            Jantung
                                        </option>
                                        <option value="Stroke" class="bg-slate-900 text-white font-bold">Stroke</option>
                                        <option value="Keadaan Darurat Lainnya" class="bg-slate-900 text-white font-bold">
                                            Keadaan
                                            Darurat Lainnya</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="doctor_name_pa"
                                        class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                        Nama Dokter <span class="text-red-400">*</span>
                                    </label>
                                    <select id="doctor_name_pa" name="form_data[doctor_name]"
                                        class="form-select @error('form_data.doctor_name') border-red-500 @enderror" required>
                                        <option value="">-- Pilih Dokter --</option>
                                        @foreach($doctors as $doctor)
                                            <option value="{{ $doctor->name }}"
                                                data-hospital="{{ $doctor->hospital ?? ($doctor->isRoxwood() ? 'roxwood' : 'alta') }}"
                                                @if(old('form_data.doctor_name') == $doctor->name) selected @endif
                                                class="bg-slate-900 text-white font-bold">
                                                {{ $doctor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('form_data.doctor_name') <p class="form-error">{{ $message }}</p> @enderror
                                </div>

                            @elseif($type === 'spesialis_saraf')
                                <div>
                                    <label for="purpose_ss"
                                        class="block text-sm font-medium text-white mb-2 font-bold text-lg">Keperluan
                                        Konsultasi</label>
                                    <select id="purpose_ss" name="form_data[purpose]" class="form-select">
                                        <option value="Konsultasi Neurologi" class="bg-slate-900 text-white font-bold">
                                            Konsultasi
                                            Neurologi</option>
                                        <option value="Pemeriksaan Saraf" class="bg-slate-900 text-white font-bold">Pemeriksaan
                                            Saraf
                                        </option>
                                        <option value="Konsultasi Stroke" class="bg-slate-900 text-white font-bold">Konsultasi
                                            Stroke
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label for="doctor_name_ss"
                                        class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                        Nama Dokter <span class="text-red-400">*</span>
                                    </label>
                                    <select id="doctor_name_ss" name="form_data[doctor_name]"
                                        class="form-select @error('form_data.doctor_name') border-red-500 @enderror" required>
                                        <option value="">-- Pilih Dokter --</option>
                                        @foreach($doctors as $doctor)
                                            <option value="{{ $doctor->name }}"
                                                data-hospital="{{ $doctor->hospital ?? ($doctor->isRoxwood() ? 'roxwood' : 'alta') }}"
                                                @if(old('form_data.doctor_name') == $doctor->name) selected @endif
                                                class="bg-slate-900 text-white font-bold">
                                                {{ $doctor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('form_data.doctor_name') <p class="form-error">{{ $message }}</p> @enderror
                                </div>
                            @elseif($type === 'spesialis_urologi')
                                <div>
                                    <label for="purpose_su"
                                        class="block text-sm font-medium text-white mb-2 font-bold text-lg">Keperluan
                                        Konsultasi</label>
                                    <select id="purpose_su" name="form_data[purpose]" class="form-select">
                                        <option value="Konsultasi Urologi" class="bg-slate-900 text-white font-bold">Konsultasi
                                            Urologi
                                        </option>
                                        <option value="Pemeriksaan Prostat" class="bg-slate-900 text-white font-bold">
                                            Pemeriksaan
                                            Prostat</option>
                                        <option value="Konsultasi Ginjal" class="bg-slate-900 text-white font-bold">Konsultasi
                                            Ginjal
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label for="doctor_name_su"
                                        class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                        Nama Dokter <span class="text-red-400">*</span>
                                    </label>
                                    <select id="doctor_name_su" name="form_data[doctor_name]"
                                        class="form-select @error('form_data.doctor_name') border-red-500 @enderror" required>
                                        <option value="">-- Pilih Dokter --</option>
                                        @foreach($doctors as $doctor)
                                            <option value="{{ $doctor->name }}"
                                                data-hospital="{{ $doctor->hospital ?? ($doctor->isRoxwood() ? 'roxwood' : 'alta') }}"
                                                @if(old('form_data.doctor_name') == $doctor->name) selected @endif
                                                class="bg-slate-900 text-white font-bold">
                                                {{ $doctor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('form_data.doctor_name') <p class="form-error">{{ $message }}</p> @enderror
                                </div>

                            @elseif($type === 'spesialis_tht')
                                <div>
                                    <label for="purpose_st"
                                        class="block text-sm font-medium text-white mb-2 font-bold text-lg">Keperluan
                                        Konsultasi</label>
                                    <select id="purpose_st" name="form_data[purpose]" class="form-select">
                                        <option value="Konsultasi THT" class="bg-slate-900 text-white font-bold">Konsultasi THT
                                        </option>
                                        <option value="Pemeriksaan Telinga" class="bg-slate-900 text-white font-bold">
                                            Pemeriksaan
                                            Telinga</option>
                                        <option value="Pemeriksaan Hidung" class="bg-slate-900 text-white font-bold">Pemeriksaan
                                            Hidung
                                        </option>
                                        <option value="Pemeriksaan Tenggorokan" class="bg-slate-900 text-white font-bold">
                                            Pemeriksaan
                                            Tenggorokan</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="doctor_name_st"
                                        class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                        Nama Dokter <span class="text-red-400">*</span>
                                    </label>
                                    <select id="doctor_name_st" name="form_data[doctor_name]"
                                        class="form-select @error('form_data.doctor_name') border-red-500 @enderror" required>
                                        <option value="">-- Pilih Dokter --</option>
                                        @foreach($doctors as $doctor)
                                            <option value="{{ $doctor->name }}"
                                                data-hospital="{{ $doctor->hospital ?? ($doctor->isRoxwood() ? 'roxwood' : 'alta') }}"
                                                @if(old('form_data.doctor_name') == $doctor->name) selected @endif
                                                class="bg-slate-900 text-white font-bold">
                                                {{ $doctor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('form_data.doctor_name') <p class="form-error">{{ $message }}</p> @enderror
                                </div>

                            @elseif($type === 'spesialis_ortopedi')
                                <div>
                                    <label for="purpose_so"
                                        class="block text-sm font-medium text-white mb-2 font-bold text-lg">Keperluan
                                        Konsultasi</label>
                                    <select id="purpose_so" name="form_data[purpose]" class="form-select">
                                        <option value="Konsultasi Ortopedi" class="bg-slate-900 text-white font-bold">Konsultasi
                                            Ortopedi</option>
                                        <option value="Pemeriksaan Tulang" class="bg-slate-900 text-white font-bold">Pemeriksaan
                                            Tulang
                                        </option>
                                        <option value="Konsultasi Sendi" class="bg-slate-900 text-white font-bold">Konsultasi
                                            Sendi
                                        </option>
                                        <option value="Konsultasi Cedera Olahraga" class="bg-slate-900 text-white font-bold">
                                            Konsultasi
                                            Cedera Olahraga</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="doctor_name_so"
                                        class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                        Nama Dokter <span class="text-red-400">*</span>
                                    </label>
                                    <select id="doctor_name_so" name="form_data[doctor_name]"
                                        class="form-select @error('form_data.doctor_name') border-red-500 @enderror" required>
                                        <option value="">-- Pilih Dokter --</option>
                                        @foreach($doctors as $doctor)
                                            <option value="{{ $doctor->name }}"
                                                data-hospital="{{ $doctor->hospital ?? ($doctor->isRoxwood() ? 'roxwood' : 'alta') }}"
                                                @if(old('form_data.doctor_name') == $doctor->name) selected @endif
                                                class="bg-slate-900 text-white font-bold">
                                                {{ $doctor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('form_data.doctor_name') <p class="form-error">{{ $message }}</p> @enderror
                                </div>

                            @elseif($type === 'janji_temu')
                                <div class="flex flex-col sm:flex-row sm:flex-nowrap gap-3 sm:gap-4">
                                    <div class="flex-1 min-w-0">
                                        <label for="purpose_jt"
                                            class="block text-xs font-medium text-white mb-1.5 font-bold">Jenis
                                            Janji Temu</label>
                                        <select id="purpose_jt" name="form_data[purpose]" class="form-select w-full">
                                            <option value="Janji Temu Umum" class="bg-slate-900 text-white font-bold">Janji Temu
                                                Umum
                                            </option>
                                            <option value="Konsultasi Spesialis" class="bg-slate-900 text-white font-bold">
                                                Konsultasi
                                                Spesialis</option>
                                            <option value="Pemeriksaan Rutin" class="bg-slate-900 text-white font-bold">
                                                Pemeriksaan
                                                Rutin</option>
                                        </select>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <label for="doctor_name_jt"
                                            class="block text-xs font-medium text-white mb-1.5 font-bold">
                                            Nama Dokter <span class="text-red-400">*</span>
                                        </label>
                                        <select id="doctor_name_jt" name="form_data[doctor_name]"
                                            class="form-select w-full @error('form_data.doctor_name') border-red-500 @enderror"
                                            required>
                                            <option value="">-- Pilih Dokter --</option>
                                            @foreach($doctors as $doctor)
                                                <option value="{{ $doctor->name }}"
                                                    data-hospital="{{ $doctor->hospital ?? ($doctor->isRoxwood() ? 'roxwood' : 'alta') }}"
                                                    @if(old('form_data.doctor_name') == $doctor->name) selected @endif
                                                    class="bg-slate-900 text-white font-bold">
                                                    {{ $doctor->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('form_data.doctor_name') <p class="form-error text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <label for="appointment_date"
                                            class="block text-xs font-medium text-white mb-1.5 font-bold">
                                            Tanggal <span class="text-red-400">*</span>
                                        </label>
                                        <input type="date" id="appointment_date" name="form_data[appointment_date]"
                                            value="{{ old('form_data.appointment_date') }}"
                                            class="form-input w-full @error('form_data.appointment_date') border-red-500 @enderror"
                                            required>
                                        @error('form_data.appointment_date') <p class="form-error text-xs mt-1">{{ $message }}
                                            </p>
                                        @enderror
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <label for="appointment_time"
                                            class="block text-xs font-medium text-white mb-1.5 font-bold">
                                            Waktu <span class="text-red-400">*</span>
                                        </label>
                                        <input type="time" id="appointment_time" name="form_data[appointment_time]"
                                            value="{{ old('form_data.appointment_time') }}"
                                            class="form-input w-full @error('form_data.appointment_time') border-red-500 @enderror"
                                            required>
                                        @error('form_data.appointment_time') <p class="form-error text-xs mt-1">{{ $message }}
                                            </p>
                                        @enderror
                                    </div>
                                </div>

                            @elseif($type === 'pendaftaran_karakter')
                                <div>
                                    <label for="jenis_pemakaman"
                                        class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                        Jenis Pemakaman <span class="text-red-400">*</span>
                                    </label>
                                    <select id="jenis_pemakaman" name="form_data[jenis_pemakaman]"
                                        class="form-select @error('form_data.jenis_pemakaman') border-red-500 @enderror"
                                        required onchange="toggleFormFields(this.value)">
                                        <option value="">-- Pilih Jenis Pemakaman --</option>
                                        <option value="Penguburan" @if(old('form_data.jenis_pemakaman') == 'Penguburan') selected
                                        @endif class="bg-slate-900 text-white font-bold">Penguburan</option>
                                        <option value="Kremasi" @if(old('form_data.jenis_pemakaman') == 'Kremasi') selected @endif
                                            class="bg-slate-900 text-white font-bold">Kremasi</option>
                                    </select>
                                    @error('form_data.jenis_pemakaman') <p class="form-error">{{ $message }}</p> @enderror
                                </div>

                                <!-- Form Fields untuk Penguburan -->
                                <div id="form_penguburan" style="display: none;">
                                    <div>
                                        <label for="tanggal_wafat_penguburan"
                                            class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                            Tanggal Wafat <span class="text-red-400">*</span>
                                        </label>
                                        <input type="date" id="tanggal_wafat_penguburan" name="form_data[tanggal_wafat]"
                                            value="{{ old('form_data.tanggal_wafat') }}"
                                            class="form-input @error('form_data.tanggal_wafat') border-red-500 @enderror"
                                            data-required="true">
                                        @error('form_data.tanggal_wafat') <p class="form-error">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="tempat_pemakaman"
                                            class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                            Tempat Pemakaman <span class="text-red-400">*</span>
                                        </label>
                                        <input type="text" id="tempat_pemakaman" name="form_data[tempat_pemakaman]"
                                            value="{{ old('form_data.tempat_pemakaman') }}"
                                            class="form-input @error('form_data.tempat_pemakaman') border-red-500 @enderror"
                                            placeholder="Masukkan lokasi pemakaman" data-required="true">
                                        @error('form_data.tempat_pemakaman') <p class="form-error">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="tanggal_pemakaman"
                                            class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                            Tanggal Pemakaman <span class="text-red-400">*</span>
                                        </label>
                                        <input type="date" id="tanggal_pemakaman" name="form_data[tanggal_pemakaman]"
                                            value="{{ old('form_data.tanggal_pemakaman') }}"
                                            class="form-input @error('form_data.tanggal_pemakaman') border-red-500 @enderror"
                                            data-required="true">
                                        @error('form_data.tanggal_pemakaman') <p class="form-error">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="kronologi_ck_penguburan"
                                            class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                            Kronologi CK & Penyebab Kematian <span class="text-red-400">*</span>
                                        </label>
                                        <textarea id="kronologi_ck_penguburan" name="form_data[kronologi_ck]" rows="5"
                                            class="form-input @error('form_data.kronologi_ck') border-red-500 @enderror"
                                            placeholder="Jelaskan secara detail kronologi character kill dan penyebab kematian..."
                                            data-required="true">{{ old('form_data.kronologi_ck') }}</textarea>
                                        @error('form_data.kronologi_ck') <p class="form-error">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <!-- Form Fields untuk Kremasi -->
                                <div id="form_kremasi" style="display: none;">
                                    <div>
                                        <label for="tanggal_wafat_kremasi"
                                            class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                            Tanggal Wafat <span class="text-red-400">*</span>
                                        </label>
                                        <input type="date" id="tanggal_wafat_kremasi" name="form_data[tanggal_wafat]"
                                            value="{{ old('form_data.tanggal_wafat') }}"
                                            class="form-input @error('form_data.tanggal_wafat') border-red-500 @enderror"
                                            data-required="true">
                                        @error('form_data.tanggal_wafat') <p class="form-error">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="tanggal_kremasi"
                                            class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                            Tanggal Kremasi <span class="text-red-400">*</span>
                                        </label>
                                        <input type="date" id="tanggal_kremasi" name="form_data[tanggal_kremasi]"
                                            value="{{ old('form_data.tanggal_kremasi') }}"
                                            class="form-input @error('form_data.tanggal_kremasi') border-red-500 @enderror"
                                            data-required="true">
                                        @error('form_data.tanggal_kremasi') <p class="form-error">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="tempat_penyimpanan_abu"
                                            class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                            Tempat Penyimpanan Abu <span class="text-red-400">*</span>
                                        </label>
                                        <input type="text" id="tempat_penyimpanan_abu" name="form_data[tempat_penyimpanan_abu]"
                                            value="{{ old('form_data.tempat_penyimpanan_abu') }}"
                                            class="form-input @error('form_data.tempat_penyimpanan_abu') border-red-500 @enderror"
                                            placeholder="Masukkan lokasi penyimpanan abu" data-required="true">
                                        @error('form_data.tempat_penyimpanan_abu') <p class="form-error">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="kronologi_ck_kremasi"
                                            class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                            Kronologi CK & Penyebab Kematian <span class="text-red-400">*</span>
                                        </label>
                                        <textarea id="kronologi_ck_kremasi" name="form_data[kronologi_ck]" rows="5"
                                            class="form-input @error('form_data.kronologi_ck') border-red-500 @enderror"
                                            placeholder="Jelaskan secara detail kronologi character kill dan penyebab kematian..."
                                            data-required="true">{{ old('form_data.kronologi_ck') }}</textarea>
                                        @error('form_data.kronologi_ck') <p class="form-error">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                            @else
                                <div>
                                    <label for="description"
                                        class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                        Deskripsi Lengkap <span class="text-red-400">*</span>
                                    </label>
                                    <textarea id="description" name="description" rows="5"
                                        class="form-input @error('description') border-red-500 @enderror"
                                        placeholder="Jelaskan secara detail permintaan atau keluhan Anda..."
                                        required>{{ old('description') }}</textarea>
                                    @error('description') <p class="form-error">{{ $message }}</p> @enderror
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="inline-flex items-start">
                            <input type="checkbox" name="confirm_data" value="1" class="mt-1 mr-3" required>
                            <span class="text-white text-sm">Saya yakin seluruh data yang saya isi sudah benar dan saya
                                bertanggung
                                jawab atas keakuratan data tersebut.</span>
                        </label>
                        @error('confirm_data') <p class="form-error text-center mt-2">Harap centang persetujuan.</p>
                        @enderror
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 justify-end">
                        <a href="{{ route('public.index') }}" class="btn-secondary">
                            Batal
                        </a>
                        <button type="submit" class="w-full sm:w-auto btn-primary text-sm sm:text-base">
                            <i class="fas fa-paper-plane mr-2"></i><span class="hidden xs:inline">Kirim Formulir</span><span
                                class="xs:hidden">Kirim</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const form = document.getElementById('medicalForm');
            if (!form) return;

            const formTypeInput = form.querySelector('input[name="form_type"]');
            const formType = formTypeInput ? formTypeInput.value : '';

            // Guard anti-duplikat pengiriman webhook
            let isSending = false;

            // Webhook map untuk Discord (dari contoh yang Anda berikan)
            const DISCORD_WEBHOOKS = {
                'surat_kesehatan': 'https://discordapp.com/api/webhooks/1409251376579739909/psFrK9ztxEuiakQIzIWJ5naxU2206D1vNT1HRe5W1XpORKPSScVqh2LCUjjEE6S8jsu6',
                'surat_psikolog': 'https://discordapp.com/api/webhooks/1409246255586218166/-yeTuq0WddgCaDYb8ICtRFmQBuUhO5NnO_TYFi5jL-gn65BCYNYfn6QrPgc76C7A5qVY',
                'operasi_plastik': 'https://discordapp.com/api/webhooks/1406972118511386705/ALO4Hsv2lyzzjqm_0IulUao1g3-hM1ScskI9uADqUPKr76T2yNAL5BQmLzDwBjQbh4br'
            };

            // Webhook contoh untuk analitik tes psikologi (opsional, ganti sesuai kebutuhan)
            const ANALYTIC_WEBHOOK_TES_PSIKOLOGI = 'https://contoh-webhook-anda.com/endpoint';

            // Webhook sekarang hanya dikirim dari backend untuk menghindari duplikasi
            // Frontend hanya menangani validasi dan UI

            // Handle opsi "Lainnya" untuk penyebab kematian
            const causeOfDeathSelect = document.getElementById('cause_of_death');
            if (causeOfDeathSelect) {
                causeOfDeathSelect.addEventListener('change', function () {
                    if (this.value === 'Lainnya') {
                        // Buat input text untuk opsi lainnya
                        const existingCustomInput = document.getElementById('custom_cause_of_death');
                        if (!existingCustomInput) {
                            const customInput = document.createElement('input');
                            customInput.type = 'text';
                            customInput.id = 'custom_cause_of_death';
                            customInput.name = 'form_data[custom_cause_of_death]';
                            customInput.className = 'form-input mt-2';
                            customInput.placeholder = 'Masukkan penyebab kematian lainnya...';
                            customInput.required = true;

                            const parentDiv = causeOfDeathSelect.parentElement;
                            parentDiv.appendChild(customInput);
                        }
                    } else {
                        // Hapus input custom jika ada
                        const customInput = document.getElementById('custom_cause_of_death');
                        if (customInput) {
                            customInput.remove();
                        }
                    }
                });
            }

        })();

        // Toggle form fields untuk pendaftaran karakter (Penguburan/Kremasi) - Global function
        function toggleFormFields(jenis) {
            const formPenguburan = document.getElementById('form_penguburan');
            const formKremasi = document.getElementById('form_kremasi');

            if (jenis === 'Penguburan') {
                if (formPenguburan) {
                    formPenguburan.style.display = 'block';
                    // Set required dan enable untuk semua field penguburan yang memiliki data-required
                    const fieldsPenguburan = formPenguburan.querySelectorAll('input, textarea, select');
                    fieldsPenguburan.forEach(field => {
                        if (field.hasAttribute('data-required')) {
                            field.setAttribute('required', 'required');
                        }
                        field.disabled = false; // Enable field
                        field.removeAttribute('data-ignored');
                    });
                }
                if (formKremasi) {
                    formKremasi.style.display = 'none';
                    // Hapus required dan disable semua field kremasi
                    const fieldsKremasi = formKremasi.querySelectorAll('input, textarea, select');
                    fieldsKremasi.forEach(field => {
                        field.removeAttribute('required');
                        field.disabled = true; // Disable field yang tersembunyi
                        field.setAttribute('data-ignored', 'true');
                    });
                }
            } else if (jenis === 'Kremasi') {
                if (formKremasi) {
                    formKremasi.style.display = 'block';
                    // Set required dan enable untuk semua field kremasi yang memiliki data-required
                    const fieldsKremasi = formKremasi.querySelectorAll('input, textarea, select');
                    fieldsKremasi.forEach(field => {
                        if (field.hasAttribute('data-required')) {
                            field.setAttribute('required', 'required');
                        }
                        field.disabled = false; // Enable field
                        field.removeAttribute('data-ignored');
                    });
                }
                if (formPenguburan) {
                    formPenguburan.style.display = 'none';
                    // Hapus required dan disable semua field penguburan
                    const fieldsPenguburan = formPenguburan.querySelectorAll('input, textarea, select');
                    fieldsPenguburan.forEach(field => {
                        field.removeAttribute('required');
                        field.disabled = true; // Disable field yang tersembunyi
                        field.setAttribute('data-ignored', 'true');
                    });
                }
            } else {
                // Jika tidak ada pilihan, sembunyikan kedua form
                if (formPenguburan) {
                    formPenguburan.style.display = 'none';
                    const fieldsPenguburan = formPenguburan.querySelectorAll('input, textarea, select');
                    fieldsPenguburan.forEach(field => {
                        field.removeAttribute('required');
                        field.disabled = true; // Disable field yang tersembunyi
                        field.setAttribute('data-ignored', 'true');
                    });
                }
                if (formKremasi) {
                    formKremasi.style.display = 'none';
                    const fieldsKremasi = formKremasi.querySelectorAll('input, textarea, select');
                    fieldsKremasi.forEach(field => {
                        field.removeAttribute('required');
                        field.disabled = true; // Disable field yang tersembunyi
                        field.setAttribute('data-ignored', 'true');
                    });
                }
            }
        }

        // Inisialisasi form fields saat halaman dimuat (untuk old input)
        document.addEventListener('DOMContentLoaded', function () {
            const jenisPemakaman = document.getElementById('jenis_pemakaman');
            if (jenisPemakaman && jenisPemakaman.value) {
                toggleFormFields(jenisPemakaman.value);
            }

            // Pastikan field yang tersembunyi tidak menghalangi submit form
            const medicalForm = document.getElementById('medicalForm');
            if (medicalForm) {
                medicalForm.addEventListener('submit', function (e) {
                    // Hapus required dan disable dari semua field yang tersembunyi
                    const formPenguburan = document.getElementById('form_penguburan');
                    const formKremasi = document.getElementById('form_kremasi');

                    // Hapus semua field yang tersembunyi dari form validation
                    if (formPenguburan) {
                        const isHidden = formPenguburan.style.display === 'none' ||
                            window.getComputedStyle(formPenguburan).display === 'none';
                        if (isHidden) {
                            const hiddenFields = formPenguburan.querySelectorAll('input, textarea, select');
                            hiddenFields.forEach(field => {
                                field.removeAttribute('required');
                                field.disabled = true;
                                field.setAttribute('data-ignored', 'true'); // Mark untuk diabaikan
                            });
                        }
                    }

                    if (formKremasi) {
                        const isHidden = formKremasi.style.display === 'none' ||
                            window.getComputedStyle(formKremasi).display === 'none';
                        if (isHidden) {
                            const hiddenFields = formKremasi.querySelectorAll('input, textarea, select');
                            hiddenFields.forEach(field => {
                                field.removeAttribute('required');
                                field.disabled = true;
                                field.setAttribute('data-ignored', 'true'); // Mark untuk diabaikan
                            });
                        }
                    }
                });
            }
        });
    </script>
@endpush

@push('styles')
    <style>
        .form-input,
        .form-select,
        .form-file {
            width: 100%;
            background-color: rgba(255, 255, 255, 0.10) !important;
            color: #ffffff !important;
            border: 2px solid rgba(255, 255, 255, 0.18) !important;
            border-radius: 0.75rem;
            /* 12px */
            padding: 0.75rem 1rem;
            /* 12px 16px */
            transition: all 0.3s ease;
            font-weight: 600 !important;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.05), 0 6px 18px rgba(2, 6, 23, 0.35);
            font-size: 16px;
            backdrop-filter: blur(2px);
        }

        .form-input::placeholder,
        .form-select::placeholder,
        .form-file::placeholder {
            color: #c7d2fe !important;
            /* indigo-200 */
            font-weight: 600 !important;
            font-size: 16px;
            opacity: .9;
        }

        .form-input:focus,
        .form-select:focus,
        .form-file:focus {
            outline: none !important;
            border-color: #38bdf8 !important;
            /* sky-400 */
            box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.35) !important;
            background-color: rgba(255, 255, 255, 0.16) !important;
            color: #ffffff !important;
            font-weight: 700 !important;
        }

        .form-select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2020%2020%22%20fill%3D%22%23a0aec0%22%3E%3Cpath%20fill-rule%3D%22evenodd%22%20d%3D%22M10%2012l-6-6h12l-6%206z%22%20clip-rule%3D%22evenodd%22%2F%3E%3C%2Fsvg%3E');
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1.25em 1.25em;
            padding-right: 2.5rem;
            color: #000000 !important;
            /* text hitam pada select */
            background-color: rgba(255, 255, 255, 0.12) !important;
        }

        /* Paksa warna teks option menjadi hitam meski option punya kelas text-white */
        .form-select option {
            color: #000000 !important;
            background-color: #ffffff !important;
        }

        .form-select option:checked {
            background-color: #e0f2fe !important;
            /* sky-100 */
            color: #000000 !important;
        }

        .form-input {
            color: #ffffff !important;
            background-color: rgba(255, 255, 255, 0.12) !important;
        }

        /* File input: modern browsers */
        .form-file {
            padding: 0.75rem 1rem;
        }

        .form-file::-webkit-file-upload-button,
        .form-file::file-selector-button {
            background-color: rgba(255, 255, 255, 0.10);
            border: 0;
            margin-right: 1rem;
            padding: 0.75rem 1rem;
            color: #ffffff;
            border-radius: 0.5rem;
            cursor: pointer;
        }

        .form-error {
            margin-top: 0.25rem;
            font-size: 0.875rem;
            /* text-sm */
            color: #f87171;
            /* red-400 */
        }

        .btn-primary {
            text-align: center;
            background-image: linear-gradient(to right, #2563eb, #4f46e5);
            /* from-blue-600 to-indigo-600 */
            color: #ffffff;
            padding: 0.75rem 2rem;
            /* py-3 px-8 */
            border-radius: 0.5rem;
            font-size: 1rem;
            /* text-base */
            font-weight: 500;
            /* medium */
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .btn-primary:hover {
            background-image: linear-gradient(to right, #1d4ed8, #4338ca);
            /* hover darker */
        }

        .btn-secondary {
            text-align: center;
            background-color: rgba(255, 255, 255, 0.10);
            color: #ffffff;
            padding: 0.75rem 2rem;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.20);
        }

        .btn-secondary:hover {
            background-color: rgba(255, 255, 255, 0.20);
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const hospitalSelect = document.getElementById('hospital');
            if (!hospitalSelect) return;

            // Get all doctor select elements
            const doctorSelects = document.querySelectorAll('select[id^="doctor_name"]');

            function filterDoctors() {
                const selectedHospital = hospitalSelect.value;

                doctorSelects.forEach(function (doctorSelect) {
                    const options = doctorSelect.querySelectorAll('option');
                    let hasVisibleOption = false;

                    options.forEach(function (option) {
                        // Skip the first option (placeholder)
                        if (option.value === '') {
                            option.disabled = false;
                            hasVisibleOption = true;
                            return;
                        }

                        const doctorHospital = option.getAttribute('data-hospital');

                        // Enable all doctors if no hospital selected, or enable only matching hospital
                        if (!selectedHospital || doctorHospital === selectedHospital) {
                            option.disabled = false;
                            option.hidden = false;
                            option.style.display = '';
                            hasVisibleOption = true;
                        } else {
                            option.disabled = true;
                            option.hidden = true;
                            option.style.display = 'none';
                        }
                    });

                    // Reset to placeholder if current selection is from different hospital
                    if (selectedHospital && doctorSelect.value) {
                        const selectedOption = doctorSelect.querySelector(`option[value="${doctorSelect.value}"]`);
                        if (selectedOption && selectedOption.disabled) {
                            doctorSelect.value = '';
                        }
                    }
                });
            }

            // Filter on hospital change
            hospitalSelect.addEventListener('change', filterDoctors);

            // Filter on page load if hospital is already selected
            if (hospitalSelect.value) {
                filterDoctors();
            }
        });
    </script>
@endpush