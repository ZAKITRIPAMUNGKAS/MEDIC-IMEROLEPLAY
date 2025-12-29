@extends('layouts.app')

@section('title', 'Detail Formulir - Portal Medis')

@section('content')
<div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700"></div>
    <div class="absolute inset-0 bg-black bg-opacity-20"></div>

    <div class="relative max-w-6xl w-full mx-auto">
        <!-- Header Section -->
        <div class="glass-effect rounded-2xl elegant-shadow-lg p-6 md:p-8 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">Detail Formulir Medis</h1>
                    <p class="text-sky-200 text-lg">Informasi lengkap dan pengelolaan formulir</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('staff.forms') }}" class="inline-flex items-center px-4 py-2 bg-white/10 text-white rounded-lg hover:bg-white/20 transition-all duration-300 text-sm font-medium backdrop-blur-sm border border-white/20">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar
                    </a>
                    @if($form->status === 'pending')
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('staff.forms.reject', $form->id) }}" onsubmit="return confirm('Yakin ingin menolak formulir ini?');">
                                @csrf
                                <button class="inline-flex items-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-all duration-300 text-sm font-medium shadow-lg">
                                    <i class="fas fa-times mr-2"></i>Tolak
                                </button>
                            </form>
                            @if($user->canApproveForm($form->form_type))
                                @php
                                    $isAppointment = in_array($form->form_type, ['penyakit_dalam', 'spesialis_anak', 'spesialis_bedah', 'spesialis_mata', 'spesialis_saraf', 'spesialis_urologi', 'spesialis_tht', 'spesialis_ortopedi']);
                                @endphp
                                <form method="POST" action="{{ route('staff.forms.approve', $form->id) }}" onsubmit="return confirm('{{ $isAppointment ? 'Yakin ingin menandai janji temu sudah ditemui?' : 'Yakin ingin menyetujui formulir ini?' }}');">
                                    @csrf
                                    <button class="inline-flex items-center px-4 py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-lg transition-all duration-300 text-sm font-medium shadow-lg">
                                        <i class="fas fa-check mr-2"></i>{{ $isAppointment ? 'Sudah Ditemui' : 'Setujui' }}
                                    </button>
                                </form>
                            @else
                                <button disabled class="inline-flex items-center px-4 py-2 bg-gray-500/50 text-gray-300 rounded-lg transition-all duration-300 text-sm font-medium shadow-lg cursor-not-allowed opacity-60" title="Level role Anda tidak mencukupi untuk menyetujui formulir ini">
                                    <i class="fas fa-lock mr-2"></i>{{ in_array($form->form_type, ['penyakit_dalam', 'spesialis_anak', 'spesialis_bedah', 'spesialis_mata', 'spesialis_saraf', 'spesialis_urologi', 'spesialis_tht', 'spesialis_ortopedi']) ? 'Sudah Ditemui' : 'Setujui' }}
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Status Badge -->
            <div class="flex items-center justify-center mb-6">
                @if($form->status === 'pending')
                    <div class="inline-flex items-center px-6 py-3 bg-yellow-500/20 text-yellow-300 rounded-full border border-yellow-500/30">
                        <i class="fas fa-clock mr-2"></i>
                        <span class="font-semibold">Menunggu Persetujuan</span>
                    </div>
                @elseif($form->status === 'approved')
                    <div class="inline-flex items-center px-6 py-3 bg-green-500/20 text-green-300 rounded-full border border-green-500/30">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span class="font-semibold">Disetujui</span>
                    </div>
                @else
                    <div class="inline-flex items-center px-6 py-3 bg-red-500/20 text-red-300 rounded-full border border-red-500/30">
                        <i class="fas fa-times-circle mr-2"></i>
                        <span class="font-semibold">Ditolak</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Informasi Utama -->
            <div class="lg:col-span-2">
                <!-- Informasi Pasien & Data Formulir Lengkap -->
                <div class="backdrop-blur-xl border-2 border-sky-400/60 rounded-2xl shadow-2xl overflow-hidden mb-6" style="background-color: rgba(7, 89, 133, 0.9);">
                    <!-- Header Section -->
                    <div class="bg-gradient-to-r from-sky-600/20 to-cyan-600/20 border-b border-white/10 px-6 md:px-8 py-5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-sky-500 to-cyan-500 rounded-xl flex items-center justify-center shadow-lg">
                                    <i class="fas fa-user-circle text-white text-lg"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-white">Informasi Pasien</h2>
                                    <p class="text-gray-300 text-xs mt-0.5">Detail lengkap informasi pasien dan formulir</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 md:p-8">
                        <!-- Informasi Utama Pasien -->
                        <div class="mb-8">
                            <div class="flex items-center mb-5">
                                <div class="h-px bg-gradient-to-r from-transparent via-sky-500/50 to-transparent flex-1"></div>
                                <span class="px-4 text-sm font-semibold text-sky-300 uppercase tracking-wider">Informasi Utama</span>
                                <div class="h-px bg-gradient-to-r from-transparent via-sky-500/50 to-transparent flex-1"></div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <!-- Nama Karakter -->
                                <div class="group relative bg-gradient-to-br from-blue-500/10 via-blue-600/5 to-transparent rounded-xl p-5 border border-blue-500/20 hover:border-blue-400/40 transition-all duration-300 hover:shadow-lg hover:shadow-blue-500/10">
                                    <div class="absolute top-3 right-3 w-8 h-8 bg-blue-500/20 rounded-lg flex items-center justify-center group-hover:bg-blue-500/30 transition-colors">
                                        <i class="fas fa-user text-blue-400 text-sm"></i>
                                    </div>
                                    <label class="block text-gray-400 text-xs font-medium uppercase tracking-wider mb-2">Nama Karakter</label>
                                    <p class="text-white text-lg font-bold pr-10">{{ $form->character_name }}</p>
                                </div>
                                
                                <!-- Citizen ID -->
                                <div class="group relative bg-gradient-to-br from-green-500/10 via-green-600/5 to-transparent rounded-xl p-5 border border-green-500/20 hover:border-green-400/40 transition-all duration-300 hover:shadow-lg hover:shadow-green-500/10">
                                    <div class="absolute top-3 right-3 w-8 h-8 bg-green-500/20 rounded-lg flex items-center justify-center group-hover:bg-green-500/30 transition-colors">
                                        <i class="fas fa-id-card text-green-400 text-sm"></i>
                                    </div>
                                    <label class="block text-gray-400 text-xs font-medium uppercase tracking-wider mb-2">Citizen ID</label>
                                    <p class="text-white text-lg font-bold pr-10">{{ $form->citizen_id ?: '-' }}</p>
                                </div>
                                
                                <!-- Tanggal Pengajuan -->
                                <div class="group relative bg-gradient-to-br from-purple-500/10 via-purple-600/5 to-transparent rounded-xl p-5 border border-purple-500/20 hover:border-purple-400/40 transition-all duration-300 hover:shadow-lg hover:shadow-purple-500/10">
                                    <div class="absolute top-3 right-3 w-8 h-8 bg-purple-500/20 rounded-lg flex items-center justify-center group-hover:bg-purple-500/30 transition-colors">
                                        <i class="fas fa-calendar-alt text-purple-400 text-sm"></i>
                                    </div>
                                    <label class="block text-gray-400 text-xs font-medium uppercase tracking-wider mb-2">Tanggal Pengajuan</label>
                                    <p class="text-white text-lg font-bold pr-10">{{ $form->created_at->format('d F Y, H:i') }}</p>
                                </div>
                                
                                <!-- Jenis Layanan -->
                                <div class="group relative bg-gradient-to-br from-amber-500/10 via-amber-600/5 to-transparent rounded-xl p-5 border border-amber-500/20 hover:border-amber-400/40 transition-all duration-300 hover:shadow-lg hover:shadow-amber-500/10">
                                    <div class="absolute top-3 right-3 w-8 h-8 bg-amber-500/20 rounded-lg flex items-center justify-center group-hover:bg-amber-500/30 transition-colors">
                                        <i class="fas fa-stethoscope text-amber-400 text-sm"></i>
                                    </div>
                                    <label class="block text-gray-400 text-xs font-medium uppercase tracking-wider mb-2">Jenis Layanan</label>
                                    <div class="inline-flex items-center px-3 py-1.5 bg-sky-500/20 text-sky-300 rounded-lg text-sm font-semibold border border-sky-500/30 mt-1">
                                        {{ Str::of($form->form_type)->replace('_',' ')->title() }}
                                    </div>
                                </div>
                                
                                <!-- Status Terakhir -->
                                <div class="group relative bg-gradient-to-br from-red-500/10 via-red-600/5 to-transparent rounded-xl p-5 border border-red-500/20 hover:border-red-400/40 transition-all duration-300 hover:shadow-lg hover:shadow-red-500/10">
                                    <div class="absolute top-3 right-3 w-8 h-8 bg-red-500/20 rounded-lg flex items-center justify-center group-hover:bg-red-500/30 transition-colors">
                                        <i class="fas fa-info-circle text-red-400 text-sm"></i>
                                    </div>
                                    <label class="block text-gray-400 text-xs font-medium uppercase tracking-wider mb-2">Status Terakhir</label>
                                    <div class="mt-1">
                                        @if($form->status === 'pending')
                                            <span class="inline-flex items-center px-3 py-1.5 bg-yellow-500/20 text-yellow-300 rounded-lg text-sm font-bold border border-yellow-500/30 shadow-sm">
                                                <i class="fas fa-clock mr-2"></i>Pending
                                            </span>
                                        @elseif($form->status === 'approved')
                                            <span class="inline-flex items-center px-3 py-1.5 bg-green-500/20 text-green-300 rounded-lg text-sm font-bold border border-green-500/30 shadow-sm">
                                                <i class="fas fa-check-circle mr-2"></i>Approved
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1.5 bg-red-500/20 text-red-300 rounded-lg text-sm font-bold border border-red-500/30 shadow-sm">
                                                <i class="fas fa-times-circle mr-2"></i>Rejected
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- ID Formulir -->
                                <div class="group relative bg-gradient-to-br from-gray-500/10 via-gray-600/5 to-transparent rounded-xl p-5 border border-gray-500/20 hover:border-gray-400/40 transition-all duration-300 hover:shadow-lg hover:shadow-gray-500/10">
                                    <div class="absolute top-3 right-3 w-8 h-8 bg-gray-500/20 rounded-lg flex items-center justify-center group-hover:bg-gray-500/30 transition-colors">
                                        <i class="fas fa-hashtag text-gray-400 text-sm"></i>
                                    </div>
                                    <label class="block text-gray-400 text-xs font-medium uppercase tracking-wider mb-2">ID Formulir</label>
                                    <p class="text-white text-lg font-mono font-bold bg-black/40 px-3 py-1.5 rounded-lg border border-white/10 inline-block mt-1">{{ $form->id }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Data Formulir Lengkap -->
                        @if($form->form_data && is_array($form->form_data) && count($form->form_data) > 0)
                        <div class="mt-8 pt-8 border-t border-white/10">
                            <div class="flex items-center mb-5">
                                <div class="h-px bg-gradient-to-r from-transparent via-cyan-500/50 to-transparent flex-1"></div>
                                <span class="px-4 text-sm font-semibold text-cyan-300 uppercase tracking-wider">Data Formulir Lengkap</span>
                                <div class="h-px bg-gradient-to-r from-transparent via-cyan-500/50 to-transparent flex-1"></div>
                            </div>
                            
                            <div class="bg-white/5 rounded-xl border border-white/10 overflow-hidden shadow-inner">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full">
                                        <thead>
                                            <tr class="bg-gradient-to-r from-cyan-600/20 to-sky-600/20 border-b border-white/10">
                                                <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                                                    <div class="flex items-center space-x-2">
                                                        <div class="w-1 h-4 bg-cyan-400 rounded-full"></div>
                                                        <span>Field</span>
                                                    </div>
                                                </th>
                                                <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                                                    <div class="flex items-center space-x-2">
                                                        <div class="w-1 h-4 bg-sky-400 rounded-full"></div>
                                                        <span>Nilai</span>
                                                    </div>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-white/5">
                                            @foreach($form->form_data as $key => $value)
                                                @if($value !== null && $value !== '' && !in_array($key, ['photo_ktp_url', 'photo_skb_url']))
                                                    @php
                                                        // Format label yang lebih readable
                                                        $label = ucwords(str_replace(['_', '-'], ' ', $key));
                                                        // Mapping label khusus untuk beberapa field
                                                        $labelMap = [
                                                            'birth_date' => 'Tanggal Lahir',
                                                            'gender' => 'Jenis Kelamin',
                                                            'age' => 'Usia',
                                                            'occupation' => 'Pekerjaan',
                                                            'phone_number' => 'Nomor Telepon',
                                                            'purpose' => 'Tujuan',
                                                            'doctor_name' => 'Nama Dokter',
                                                            'photo_ktp_url' => 'Foto KTP',
                                                            'photo_skb_url' => 'Foto SKB',
                                                        ];
                                                        $displayLabel = $labelMap[$key] ?? $label;
                                                        
                                                        // Format value khusus untuk beberapa tipe data
                                                        $displayValue = $value;
                                                        if ($key === 'birth_date' && $value) {
                                                            try {
                                                                $date = \Carbon\Carbon::parse($value);
                                                                $displayValue = $date->format('d F Y');
                                                            } catch (\Exception $e) {
                                                                $displayValue = $value;
                                                            }
                                                        }
                                                    @endphp
                                                    <tr class="hover:bg-white/10 transition-all duration-200 group">
                                                        <td class="px-6 py-4">
                                                            <div class="flex items-center space-x-3">
                                                                <div class="w-2 h-2 bg-gradient-to-br from-cyan-400 to-sky-400 rounded-full group-hover:scale-125 transition-transform"></div>
                                                                <span class="text-gray-300 font-medium text-sm">{{ $displayLabel }}</span>
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4">
                                                            <span class="text-white font-semibold">{{ $displayValue ?: '-' }}</span>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Deskripsi -->
                <div class="backdrop-blur-xl border-2 border-sky-400/60 rounded-2xl shadow-2xl p-6 md:p-8 mb-6" style="background-color: rgba(7, 89, 133, 0.9);">
                    <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
                        <i class="fas fa-file-alt mr-3 text-cyan-400"></i>
                        Deskripsi Permintaan
                    </h2>
                    <div class="bg-white/5 rounded-xl p-6 border border-white/10">
                        <p class="text-gray-200 text-lg leading-relaxed">{{ $form->description }}</p>
                    </div>
                </div>

                <!-- Ringkasan Tes Psikologi (PSS-10) -->
                @if($form->form_type === 'tes_psikologi')
                @php
                    $data = is_array($form->form_data ?? null) ? $form->form_data : [];
                    $stressAnswers = [];
                    $allNull = true;
                    for ($i = 1; $i <= 10; $i++) {
                        $key = 'stress' . $i;
                        $valRaw = $data[$key] ?? null;
                        if ($valRaw !== null && $valRaw !== '') {
                            $allNull = false;
                        }
                        $val = is_numeric($valRaw) ? (int)$valRaw : 0; // skala 0..4
                        // Item dibalik: 4,5,7,9 (pada skala 0..4)
                        if (in_array($i, [4,5,7,9], true)) {
                            $val = 4 - $val;
                        }
                        $stressAnswers[] = $val;
                    }
                    $stressTotal = array_sum($stressAnswers);
                    $stressLevel = $stressTotal <= 13 ? 'Rendah' : ($stressTotal <= 26 ? 'Sedang' : 'Tinggi');
                    $badgeClasses = [
                        'Rendah' => 'bg-green-500/20 text-green-300 border border-green-500/30',
                        'Sedang' => 'bg-yellow-500/20 text-yellow-300 border border-yellow-500/30',
                        'Tinggi' => 'bg-red-500/20 text-red-300 border border-red-500/30',
                    ][$stressLevel] ?? 'bg-white/10 text-white border border-white/20';
                @endphp
                @if(!$allNull)
                <div class="backdrop-blur-xl border-2 border-sky-400/60 rounded-2xl shadow-2xl p-6 md:p-8 mb-6" style="background-color: rgba(7, 89, 133, 0.9);">
                    <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
                        <i class="fas fa-brain mr-3 text-amber-400"></i>
                        Ringkasan Tes Psikologi (PSS-10)
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                            <label class="text-gray-300 text-sm font-medium block mb-2">Skor Total Stres</label>
                            <p class="text-white text-2xl font-extrabold">{{ $stressTotal }}</p>
                        </div>
                        <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                            <label class="text-gray-300 text-sm font-medium block mb-2">Level Stres</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold {{ $badgeClasses }}">{{ $stressLevel }}</span>
                        </div>
                        <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                            <label class="text-gray-300 text-sm font-medium block mb-2">Interpretasi</label>
                            <p class="text-sky-200">
                                @if($stressLevel === 'Rendah')
                                    Indikasi stres rendah.
                                @elseif($stressLevel === 'Sedang')
                                    Indikasi stres sedang.
                                @else
                                    Indikasi stres tinggi. Pertimbangkan tindak lanjut.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                @endif
                @endif

                <!-- Foto Dokumen (untuk operasi plastik) -->
                @if($form->form_type === 'operasi_plastik')
                <div class="backdrop-blur-xl border-2 border-sky-400/60 rounded-2xl shadow-2xl p-6 md:p-8 mb-6" style="background-color: rgba(7, 89, 133, 0.9);">
                    <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
                        <i class="fas fa-images mr-3 text-purple-400"></i>
                        Foto Dokumen
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Foto KTP -->
                        <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                            <h3 class="text-lg font-semibold text-white mb-3 flex items-center">
                                <i class="fas fa-id-card mr-2 text-blue-400"></i>
                                Foto KTP
                            </h3>
                            <div class="relative">
                                @if(isset($form->form_data['photo_ktp_url']) && !empty($form->form_data['photo_ktp_url']))
                                    <div class="relative">
                                        <img src="{{ $form->form_data['photo_ktp_url'] }}" 
                                             alt="Foto KTP" 
                                             class="w-full h-auto rounded-lg border border-white/20 shadow-lg hover:scale-105 transition-transform duration-300 cursor-pointer"
                                             onclick="openImageModal('{{ $form->form_data['photo_ktp_url'] }}', 'Foto KTP')"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                        <div class="hidden text-center py-8 bg-white/5 rounded-lg border border-white/10">
                                            <i class="fas fa-exclamation-triangle text-yellow-400 text-4xl mb-3"></i>
                                            <p class="text-yellow-400 font-medium">Foto KTP tidak dapat dimuat</p>
                                            <p class="text-gray-400 text-sm mt-2">URL: {{ $form->form_data['photo_ktp_url'] }}</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center py-8 bg-white/5 rounded-lg border border-white/10">
                                        <i class="fas fa-image text-gray-400 text-4xl mb-3"></i>
                                        <p class="text-gray-400">Foto KTP tidak diupload</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Foto SKB -->
                        <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                            <h3 class="text-lg font-semibold text-white mb-3 flex items-center">
                                <i class="fas fa-file-medical mr-2 text-green-400"></i>
                                Foto SKB
                            </h3>
                            <div class="relative">
                                @if(isset($form->form_data['photo_skb_url']) && !empty($form->form_data['photo_skb_url']))
                                    <div class="relative">
                                        <img src="{{ $form->form_data['photo_skb_url'] }}" 
                                             alt="Foto SKB" 
                                             class="w-full h-auto rounded-lg border border-white/20 shadow-lg hover:scale-105 transition-transform duration-300 cursor-pointer"
                                             onclick="openImageModal('{{ $form->form_data['photo_skb_url'] }}', 'Foto SKB')"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                        <div class="hidden text-center py-8 bg-white/5 rounded-lg border border-white/10">
                                            <i class="fas fa-exclamation-triangle text-yellow-400 text-4xl mb-3"></i>
                                            <p class="text-yellow-400 font-medium">Foto SKB tidak dapat dimuat</p>
                                            <p class="text-gray-400 text-sm mt-2">URL: {{ $form->form_data['photo_skb_url'] }}</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center py-8 bg-white/5 rounded-lg border border-white/10">
                                        <i class="fas fa-image text-gray-400 text-4xl mb-3"></i>
                                        <p class="text-gray-400">Foto SKB tidak diupload</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Testimoni Section -->
                @if($form->testimoni)
                <div class="backdrop-blur-xl border-2 border-sky-400/60 rounded-2xl shadow-2xl p-6 md:p-8" style="background-color: rgba(7, 89, 133, 0.9);">
                    <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
                        <i class="fas fa-comment-dots mr-3 text-amber-400"></i>
                        Saran dan Masukan Pasien
                    </h2>
                    <div class="bg-gradient-to-br from-amber-500/20 to-orange-500/20 rounded-xl p-6 border border-amber-500/30">
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-white font-semibold">Rating:</span>
                                <div class="flex text-yellow-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $form->rating ? 'text-yellow-400' : 'text-gray-500' }}"></i>
                                    @endfor
                                    <span class="ml-2 text-white font-bold">({{ $form->rating }}/5)</span>
                                </div>
                            </div>
                            <div class="bg-black/30 rounded-lg p-4 border border-white/10">
                                <p class="text-gray-200 italic leading-relaxed">"{{ $form->testimoni }}"</p>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-white/10">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-300 text-sm">Status Testimoni:</span>
                                @if($form->testimoni_approved)
                                    <span class="px-3 py-1 bg-green-500/20 text-green-300 rounded-full text-sm font-medium border border-green-500/30">
                                        <i class="fas fa-check-circle mr-1"></i>Disetujui
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-yellow-500/20 text-yellow-300 rounded-full text-sm font-medium border border-yellow-500/30">
                                        <i class="fas fa-clock mr-1"></i>Menunggu Persetujuan
                                    </span>
                                @endif
                            </div>
                            @if(!$form->testimoni_approved)
                            <form method="POST" action="{{ route('staff.forms.testimoni.approve', $form->id) }}" class="mt-4" onsubmit="return confirm('Yakin ingin menyetujui testimoni ini untuk ditampilkan di halaman beranda?');">
                                @csrf
                                <button type="submit" class="w-full flex items-center justify-center px-4 py-2 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white rounded-lg transition-all duration-300 font-medium shadow-lg">
                                    <i class="fas fa-check-circle mr-2"></i>Setujui Testimoni
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="backdrop-blur-xl border-2 border-sky-400/60 rounded-2xl shadow-2xl p-6" style="background-color: rgba(7, 89, 133, 0.9);">
                    <h3 class="text-xl font-bold text-white mb-4 flex items-center">
                        <i class="fas fa-bolt mr-2 text-amber-400"></i>
                        Aksi Cepat
                    </h3>
                    <div class="space-y-3">
                        @if($form->status === 'pending')
                            @php
                                $isAppointment = in_array($form->form_type, ['penyakit_dalam', 'spesialis_anak', 'spesialis_bedah', 'spesialis_mata', 'spesialis_saraf', 'spesialis_urologi', 'spesialis_tht', 'spesialis_ortopedi']);
                                $canApprove = $user->canApproveForm($form->form_type);
                            @endphp
                            
                            @if($canApprove)
                                <form method="POST" action="{{ route('staff.forms.approve', $form->id) }}" onsubmit="return confirm('{{ $isAppointment ? 'Yakin ingin menandai janji temu sudah ditemui?' : 'Yakin ingin menyetujui formulir ini?' }}');">
                                    @csrf
                                    <button class="w-full flex items-center justify-center px-4 py-3 {{ $isAppointment ? 'bg-green-500 hover:bg-green-600' : 'bg-sky-500 hover:bg-sky-600' }} text-white rounded-lg transition-all duration-300 font-medium shadow-lg">
                                        <i class="fas fa-check mr-2"></i>{{ $isAppointment ? 'Sudah Ditemui' : 'Setujui Formulir' }}
                                    </button>
                                </form>
                            @else
                                <button disabled class="w-full flex items-center justify-center px-4 py-3 bg-gray-500/50 text-gray-300 rounded-lg transition-all duration-300 font-medium shadow-lg cursor-not-allowed opacity-60" title="Level role Anda tidak mencukupi untuk menyetujui formulir ini">
                                    <i class="fas fa-lock mr-2"></i>{{ $isAppointment ? 'Sudah Ditemui' : 'Setujui Formulir' }}
                                </button>
                            @endif
                            
                            <form method="POST" action="{{ route('staff.forms.reject', $form->id) }}" onsubmit="return confirm('Yakin ingin menolak formulir ini?');">
                                @csrf
                                <button class="w-full flex items-center justify-center px-4 py-3 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-all duration-300 font-medium shadow-lg">
                                    <i class="fas fa-times mr-2"></i>Tolak Formulir
                                </button>
                            </form>
                        @endif
                        
                        <a href="{{ route('staff.forms') }}" class="w-full flex items-center justify-center px-4 py-3 bg-white/10 hover:bg-white/20 text-white rounded-lg transition-all duration-300 font-medium border border-white/20">
                            <i class="fas fa-list mr-2"></i>Kembali ke Daftar
                        </a>
                    </div>
                </div>

                <!-- Informasi Tambahan -->
                <div class="backdrop-blur-xl border-2 border-sky-400/60 rounded-2xl shadow-2xl p-6" style="background-color: rgba(7, 89, 133, 0.9);">
                    <h3 class="text-xl font-bold text-white mb-4 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-sky-400"></i>
                        Informasi Tambahan
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-2 border-b border-white/10">
                            <span class="text-gray-300 text-sm">Dibuat</span>
                            <span class="text-white text-sm font-medium">{{ $form->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-white/10">
                            <span class="text-gray-300 text-sm">Diperbarui</span>
                            <span class="text-white text-sm font-medium">{{ $form->updated_at->diffForHumans() }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-300 text-sm">Durasi</span>
                            <span class="text-white text-sm font-medium">{{ $form->created_at->diffInHours($form->updated_at) }} jam</span>
                        </div>
                    </div>
                </div>

                <!-- Status Timeline -->
                <div class="glass-effect rounded-2xl elegant-shadow-lg p-6">
                    <h3 class="text-xl font-bold text-white mb-4 flex items-center">
                        <i class="fas fa-history mr-2 text-cyan-400"></i>
                        Timeline Status
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                            <div>
                                <p class="text-white text-sm font-medium">Formulir Dikirim</p>
                                <p class="text-gray-400 text-xs">{{ $form->created_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</p>
                            </div>
                        </div>
                        
                        @if($form->status !== 'pending')
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-{{ $form->status === 'approved' ? 'green' : 'red' }}-500 rounded-full mr-3"></div>
                                <div>
                                    <p class="text-white text-sm font-medium">
                                        {{ $form->status === 'approved' ? 'Disetujui' : 'Ditolak' }}
                                    </p>
                                    <p class="text-gray-400 text-xs">{{ $form->updated_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</p>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></div>
                                <div>
                                    <p class="text-white text-sm font-medium">Menunggu Review</p>
                                    <p class="text-gray-400 text-xs">Status saat ini</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk menampilkan foto -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden flex items-center justify-center p-4">
    <div class="relative max-w-4xl max-h-full">
        <button onclick="closeImageModal()" class="absolute -top-10 right-0 text-white text-2xl hover:text-gray-300 transition-colors">
            <i class="fas fa-times"></i>
        </button>
        <img id="modalImage" src="" alt="" class="max-w-full max-h-full rounded-lg shadow-2xl">
        <p id="modalTitle" class="text-white text-center mt-4 text-lg font-semibold"></p>
    </div>
</div>

<script>
function openImageModal(imageSrc, title) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('imageModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Tutup modal saat klik di luar gambar
document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});

// Tutup modal dengan tombol ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});
</script>
@endsection


