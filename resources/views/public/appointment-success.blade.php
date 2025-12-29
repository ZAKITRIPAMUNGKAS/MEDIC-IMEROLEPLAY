@extends('layouts.app')

@section('title', 'Janji Temu Berhasil Dikirim - Portal Medis MOTIONLIFE')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700 relative overflow-hidden flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <!-- Background Effects -->
    <div class="absolute inset-0 bg-black/20"></div>
    <div class="absolute inset-0" style="background-image: radial-gradient(circle at 20% 20%, rgba(14, 165, 233, 0.1) 0%, transparent 50%), radial-gradient(circle at 80% 80%, rgba(6, 182, 212, 0.1) 0%, transparent 50%);"></div>
    
    <!-- Animated Elements -->
    <div class="absolute top-20 left-20 w-16 h-16 border-2 border-sky-400 rotate-45 animate-float opacity-30"></div>
    <div class="absolute top-40 right-20 w-12 h-12 border-2 border-cyan-400 rotate-12 animate-pulse opacity-40" style="animation-delay: 1s;"></div>
    <div class="absolute bottom-40 left-1/4 w-8 h-8 border-2 border-blue-400 rotate-45 animate-float opacity-50" style="animation-delay: 2s;"></div>
    <div class="absolute bottom-20 right-1/3 w-10 h-10 border-2 border-sky-500 rotate-12 animate-pulse opacity-30" style="animation-delay: 0.5s;"></div>

    <div class="relative z-10 max-w-4xl w-full">
        <div class="card bg-white/98 backdrop-blur-lg shadow-2xl border border-white/30 rounded-3xl p-12 text-center animate-fade-in-up overflow-hidden">
            <!-- Success Icon with Enhanced Design -->
            <div class="relative mb-8">
                <div class="w-28 h-28 bg-gradient-to-br from-green-500 via-emerald-500 to-teal-500 rounded-full flex items-center justify-center mx-auto shadow-2xl animate-pulse-slow relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-green-400 to-emerald-400 rounded-full animate-ping opacity-20"></div>
                    <i class="fas fa-check text-white text-5xl relative z-10"></i>
                </div>
                <!-- Success Ring -->
                <div class="absolute inset-0 w-28 h-28 mx-auto border-4 border-green-300 rounded-full animate-ping opacity-30"></div>
            </div>

            <!-- Enhanced Success Message -->
            <div class="mb-8">
                <h1 class="text-5xl md:text-6xl font-black bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 bg-clip-text text-transparent mb-4">
                    Janji Temu Berhasil Dikirim!
                </h1>
                <div class="w-32 h-1 bg-gradient-to-r from-green-500 to-teal-500 mx-auto mb-6 rounded-full"></div>
                <p class="text-2xl text-slate-700 mb-4 font-medium">
                    Terima kasih, <span class="font-bold text-sky-600 bg-sky-50 px-3 py-1 rounded-full">{{ $appointment->character_name ?? 'N/A' }}</span>
                </p>
                <p class="text-lg text-slate-600 font-medium">
                    Tim medis kami akan segera menghubungi Anda untuk konfirmasi 💚
                </p>
            </div>

            <!-- Enhanced Appointment Details -->
            <div class="bg-gradient-to-br from-sky-50 via-cyan-50 to-blue-50 rounded-3xl p-8 mb-8 text-left border border-sky-200 shadow-lg">
                <div class="flex items-center justify-center mb-6">
                    <div class="w-10 h-10 bg-gradient-to-br from-sky-500 to-cyan-500 rounded-2xl flex items-center justify-center mr-3 shadow-lg">
                        <i class="fas fa-calendar-check text-white text-lg"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900">Detail Janji Temu</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="group p-5 bg-white/90 rounded-2xl border border-sky-100 hover:border-sky-200 transition-all duration-300 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-hashtag text-sky-500 mr-3"></i>
                                    <span class="text-slate-600 font-semibold">ID Janji Temu:</span>
                                </div>
                                <span class="font-mono text-lg font-bold text-sky-600 bg-sky-100 px-3 py-1 rounded-full">
                                    #{{ str_pad($appointment->id, 6, '0', STR_PAD_LEFT) }}
                                </span>
                            </div>
                        </div>
                        <div class="group p-5 bg-white/90 rounded-2xl border border-sky-100 hover:border-sky-200 transition-all duration-300 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-user text-sky-500 mr-3"></i>
                                    <span class="text-slate-600 font-semibold">Nama Karakter:</span>
                                </div>
                                <span class="font-bold text-slate-900">{{ $appointment->character_name ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="group p-5 bg-white/90 rounded-2xl border border-sky-100 hover:border-sky-200 transition-all duration-300 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-stethoscope text-sky-500 mr-3"></i>
                                    <span class="text-slate-600 font-semibold">Poli:</span>
                                </div>
                                <span class="font-bold text-slate-900">{{ $appointment->form_type ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="group p-5 bg-white/90 rounded-2xl border border-sky-100 hover:border-sky-200 transition-all duration-300 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar text-sky-500 mr-3"></i>
                                    <span class="text-slate-600 font-semibold">Tanggal:</span>
                                </div>
                                <span class="font-bold text-slate-900">{{ $appointment->appointment_date ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="group p-5 bg-white/90 rounded-2xl border border-sky-100 hover:border-sky-200 transition-all duration-300 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-clock text-sky-500 mr-3"></i>
                                    <span class="text-slate-600 font-semibold">Waktu:</span>
                                </div>
                                <span class="font-bold text-slate-900">{{ $appointment->appointment_time ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="group p-5 bg-white/90 rounded-2xl border border-sky-100 hover:border-sky-200 transition-all duration-300 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-hourglass-half text-sky-500 mr-3"></i>
                                    <span class="text-slate-600 font-semibold">Status:</span>
                                </div>
                                <span class="px-4 py-2 bg-orange-100 text-orange-800 text-sm font-bold rounded-full border border-orange-200 animate-pulse">
                                    <i class="fas fa-clock mr-1"></i>Menunggu Konfirmasi
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Next Steps -->
            <div class="bg-gradient-to-br from-sky-100 via-cyan-100 to-blue-100 rounded-3xl p-8 mb-8 border border-sky-200 shadow-lg">
                <div class="flex items-center justify-center mb-6">
                    <div class="w-10 h-10 bg-gradient-to-br from-sky-500 to-cyan-500 rounded-2xl flex items-center justify-center mr-3 shadow-lg">
                        <i class="fas fa-route text-white text-lg"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900">Langkah Selanjutnya</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="group text-center p-6 bg-white/90 rounded-2xl border border-sky-200 hover:border-sky-300 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                        <div class="relative mb-4">
                            <div class="w-16 h-16 bg-gradient-to-br from-sky-500 to-cyan-500 rounded-2xl flex items-center justify-center mx-auto shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-phone text-white text-xl"></i>
                            </div>
                            <div class="absolute -top-2 -right-2 w-8 h-8 bg-sky-400 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                1
                            </div>
                        </div>
                        <h4 class="font-bold text-slate-900 mb-2 text-lg">Konfirmasi Dokter</h4>
                        <p class="text-slate-600 text-sm leading-relaxed">Dokter akan menghubungi Anda untuk konfirmasi</p>
                    </div>
                    <div class="group text-center p-6 bg-white/90 rounded-2xl border border-sky-200 hover:border-sky-300 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                        <div class="relative mb-4">
                            <div class="w-16 h-16 bg-gradient-to-br from-cyan-500 to-blue-500 rounded-2xl flex items-center justify-center mx-auto shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-calendar-check text-white text-xl"></i>
                            </div>
                            <div class="absolute -top-2 -right-2 w-8 h-8 bg-cyan-400 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                2
                            </div>
                        </div>
                        <h4 class="font-bold text-slate-900 mb-2 text-lg">Persiapan Konsultasi</h4>
                        <p class="text-slate-600 text-sm leading-relaxed">Siapkan dokumen yang diperlukan</p>
                    </div>
                    <div class="group text-center p-6 bg-white/90 rounded-2xl border border-sky-200 hover:border-sky-300 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                        <div class="relative mb-4">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-2xl flex items-center justify-center mx-auto shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-user-md text-white text-xl"></i>
                            </div>
                            <div class="absolute -top-2 -right-2 w-8 h-8 bg-blue-400 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                3
                            </div>
                        </div>
                        <h4 class="font-bold text-slate-900 mb-2 text-lg">Konsultasi Medis</h4>
                        <p class="text-slate-600 text-sm leading-relaxed">Lakukan konsultasi sesuai jadwal</p>
                    </div>
                </div>
            </div>

            <!-- Enhanced Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-6 justify-center mb-8">
                <a href="{{ route('public.index') }}" class="group relative px-8 py-4 bg-gradient-to-r from-sky-500 to-cyan-500 text-white text-lg font-bold rounded-2xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-sky-600 to-cyan-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative flex items-center justify-center">
                        <i class="fas fa-calendar-plus mr-3 text-xl"></i>
                        <span>Buat Janji Temu Lain</span>
                    </div>
                </a>
                <a href="{{ route('public.index') }}" class="group relative px-8 py-4 bg-white text-sky-600 text-lg font-bold rounded-2xl border-2 border-sky-500 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-sky-50 to-cyan-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative flex items-center justify-center">
                        <i class="fas fa-arrow-left mr-3 text-xl"></i>
                        <span>Kembali</span>
                    </div>
                </a>
            </div>

            <!-- Enhanced Contact Info -->
            <div class="mt-8 pt-8 border-t border-sky-200">
                <div class="bg-gradient-to-r from-sky-50 via-cyan-50 to-blue-50 rounded-2xl p-8 border border-sky-200 shadow-lg">
                    <div class="flex flex-col sm:flex-row items-center justify-center mb-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-sky-500 to-cyan-500 rounded-2xl flex items-center justify-center mr-4 shadow-lg mb-4 sm:mb-0">
                            <i class="fas fa-headset text-white text-2xl"></i>
                        </div>
                        <div class="text-center sm:text-left">
                            <h4 class="text-2xl font-bold text-slate-900 mb-2">Butuh Bantuan?</h4>
                            <p class="text-slate-600 text-lg">
                                Tim medis kami siap membantu Anda 24/7
                            </p>
                        </div>
                    </div>
                    <div class="text-center">
                        <p class="text-slate-600 mb-4">
                            Hubungi tim medis melalui sistem internal atau 
                            <a href="https://discord.com/channels/1357345255728480356/1357367699369492501" class="text-sky-600 hover:text-sky-700 font-bold transition-colors underline decoration-2 underline-offset-2">
                                klik di sini
                            </a> untuk bantuan langsung.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                            <div class="flex items-center text-sky-600">
                                <i class="fas fa-phone mr-2"></i>
                                <span class="font-semibold">Hotline: 24/7</span>
                            </div>
                            <div class="flex items-center text-sky-600">
                                <i class="fas fa-envelope mr-2"></i>
                                <span class="font-semibold">Email Support</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
