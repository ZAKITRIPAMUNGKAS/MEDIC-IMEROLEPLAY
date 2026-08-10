@extends('layouts.app')

@section('title', 'Portal Medis iMe Roleplay - Layanan Medis untuk Komunitas RP')

@section('meta_description', 'Portal Medis iMe Roleplay - Menyediakan perawatan komprehensif untuk semua kebutuhan kesehatan Anda dengan standar profesional tertinggi. Konsultasi Medis. Pemeriksaan umum dan diagnosis.')

@section('meta_keywords', 'ime roleplay, portal medis ime roleplay, ime roleplay medical, motion ime roleplay, gta roleplay, motionlife roleplay, motion ime, portal medis, motion medical center, layanan medis, EMS, medical center roleplay, roleplay medical services')

@section('og_title', 'Portal Medis iMe Roleplay - Layanan Medis untuk Komunitas RP')

@section('og_description', 'Portal Medis iMe Roleplay - Menyediakan perawatan komprehensif untuk semua kebutuhan kesehatan Anda dengan standar profesional tertinggi. Konsultasi Medis. Pemeriksaan umum dan diagnosis.')

@section('content')
    <!-- Pop-up Informasi Kenaikan Regulasi -->
    <div id="regulationModal"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-2 sm:p-4"
        style="display: none;">
        <div
            class="bg-white rounded-3xl shadow-2xl max-w-5xl w-full max-h-[95vh] overflow-hidden animate-fade-in-up flex flex-col">
            <!-- Header -->
            <div
                class="bg-gradient-to-r from-red-500 via-red-600 to-pink-600 text-white p-6 sm:p-8 relative overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white rounded-full -translate-y-16 translate-x-16">
                    </div>
                    <div class="absolute bottom-0 left-0 w-24 h-24 bg-white rounded-full translate-y-12 -translate-x-12">
                    </div>
                </div>

                <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center space-x-3 sm:space-x-6">
                        <div
                            class="w-12 h-12 sm:w-16 sm:h-16 bg-white/20 backdrop-blur-sm rounded-2xl sm:rounded-3xl flex items-center justify-center shadow-lg animate-pulse flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-2xl sm:text-3xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl sm:text-3xl font-black mb-1 sm:mb-2">UPDATE REGULASI</h2>
                            <div class="flex flex-wrap items-center mt-2 sm:mt-3 gap-2 sm:space-x-4">
                                <i class="fas fa-calendar-alt text-xs sm:text-sm"></i>
                                <span class="text-xs sm:text-sm font-semibold">07 Januari 2026</span>
                            </div>
                            <div class="flex items-center space-x-2 bg-white/20 px-3 py-1 rounded-full">
                                <i class="fas fa-clock text-xs sm:text-sm"></i>
                                <span class="text-xs sm:text-sm font-semibold">Efektif Segera</span>
                            </div>
                        </div>
                    </div>
                </div>
                <button onclick="closeRegulationModal()"
                    class="absolute top-0 right-0 mt-4 mr-4 sm:relative sm:mt-0 sm:mr-0 w-10 h-10 sm:w-12 sm:h-12 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-xl sm:rounded-2xl flex items-center justify-center transition-all duration-300 hover:scale-110 shadow-lg flex-shrink-0">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Content -->
            <div
                class="flex-1 p-6 sm:p-8 space-y-6 sm:space-y-8 bg-gradient-to-b from-gray-50 to-white overflow-y-auto custom-scrollbar">
                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <div class="bg-gradient-to-r from-blue-500 to-cyan-500 text-white p-4 rounded-2xl text-center">
                        <i class="fas fa-cash-register text-2xl mb-2"></i>
                        <h4 class="font-bold text-lg">Cash Payment</h4>
                        <p class="text-sm opacity-90">Pembayaran Tunai</p>
                    </div>
                    <div class="bg-gradient-to-r from-green-500 to-emerald-500 text-white p-4 rounded-2xl text-center">
                        <i class="fas fa-file-invoice text-2xl mb-2"></i>
                        <h4 class="font-bold text-lg">Invoice Payment</h4>
                        <p class="text-sm opacity-90">Pembayaran Tagihan</p>
                    </div>
                    <div class="bg-gradient-to-r from-purple-500 to-pink-500 text-white p-4 rounded-2xl text-center">
                        <i class="fas fa-chart-line text-2xl mb-2"></i>
                        <h4 class="font-bold text-lg">Price Update</h4>
                        <p class="text-sm opacity-90">Update Harga</p>
                    </div>
                </div>

                <!-- REGULASI PENGOBATAN (CASH) -->
                <div
                    class="bg-white rounded-3xl shadow-lg border border-blue-200 overflow-hidden hover:shadow-xl transition-all duration-300">
                    <div class="bg-gradient-to-r from-blue-500 to-cyan-500 text-white p-6">
                        <h3 class="text-2xl font-bold flex items-center">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-pills text-xl"></i>
                            </div>
                            REGULASI PENGOBATAN (CASH)
                        </h3>
                        <p class="text-blue-100 mt-2">Layanan pengobatan dengan pembayaran tunai</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div
                                    class="flex items-center justify-between p-4 bg-blue-50 rounded-xl border border-blue-200">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-hospital text-sky-500 text-xl"></i>
                                        <span class="font-semibold text-gray-800">TREATMENT RS</span>
                                    </div>
                                    <span class="text-2xl font-bold text-sky-500">$200</span>
                                </div>
                                <div
                                    class="flex items-center justify-between p-4 bg-cyan-50 rounded-xl border border-cyan-200">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-ambulance text-cyan-600 text-xl"></i>
                                        <span class="font-semibold text-gray-800">TREATMENT LUAR RS</span>
                                    </div>
                                    <span class="text-2xl font-bold text-cyan-600">$220</span>
                                </div>
                            </div>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-info-circle text-yellow-600 text-xl mt-1"></i>
                                    <div>
                                        <h4 class="font-semibold text-yellow-800 mb-2">Catatan Penting</h4>
                                        <p class="text-sm text-yellow-700">Jika terdapat luka dan diresepkan obat atau
                                            bandage tambahan, akan ditambah <span class="font-bold">$20</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- REGULASI KONSULTASI KESEHATAN (CASH) -->
                <div
                    class="bg-white rounded-3xl shadow-lg border border-emerald-200 overflow-hidden hover:shadow-xl transition-all duration-300">
                    <div class="bg-gradient-to-r from-emerald-500 to-teal-500 text-white p-6">
                        <h3 class="text-2xl font-bold flex items-center">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-user-md text-xl"></i>
                            </div>
                            REGULASI KONSULTASI KESEHATAN (CASH)
                        </h3>
                        <p class="text-emerald-100 mt-2">Layanan konsultasi kesehatan dengan dokter umum dan spesialis</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div
                                class="bg-gradient-to-br from-emerald-50 to-teal-50 p-6 rounded-xl border border-emerald-200">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-stethoscope text-emerald-600 text-2xl"></i>
                                        <h4 class="font-bold text-gray-800">DOKTER UMUM</h4>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-2xl font-bold text-emerald-600">$1,300</span>
                                        <span class="text-lg text-emerald-500">- $1,800</span>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 mb-3">Per-Konsultasi (TERGANTUNG KESULITAN)</p>
                            </div>
                            <div class="bg-gradient-to-br from-teal-50 to-cyan-50 p-6 rounded-xl border border-teal-200">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-user-md text-teal-600 text-2xl"></i>
                                        <h4 class="font-bold text-gray-800">DOKTER SPESIALIS</h4>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-2xl font-bold text-teal-600">$1,900</span>
                                        <span class="text-lg text-teal-500">- $3,500</span>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 mb-3">Per-Konsultasi (TERGANTUNG KESULITAN)</p>
                            </div>
                        </div>
                        <div class="mt-4 space-y-3">
                            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-info-circle text-yellow-600 text-xl mt-1"></i>
                                    <div>
                                        <h4 class="font-semibold text-yellow-800 mb-2">Catatan Penting</h4>
                                        <p class="text-sm text-yellow-700">Jika diresepkan pemeriksaan tambahan <span
                                                class="font-bold">$630</span></p>
                                        <p class="text-sm text-yellow-700 mt-1">Jika diresepkan obat tambahan akan ditambah
                                            <span class="font-bold">$130</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- REGULASI KONSULTASI DAN PENGOBATAN KESEHATAN GIGI UMUM DAN ANAK (CASH) -->
                <div
                    class="bg-white rounded-3xl shadow-lg border border-cyan-200 overflow-hidden hover:shadow-xl transition-all duration-300">
                    <div class="bg-gradient-to-r from-cyan-500 to-blue-500 text-white p-6">
                        <h3 class="text-2xl font-bold flex items-center">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-tooth text-xl"></i>
                            </div>
                            REGULASI KONSULTASI DAN PENGOBATAN KESEHATAN GIGI UMUM DAN ANAK (CASH)
                        </h3>
                        <p class="text-cyan-100 mt-2">Layanan konsultasi dan pengobatan kesehatan gigi</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-gradient-to-br from-cyan-50 to-blue-50 p-4 rounded-xl border border-cyan-200">
                                <div class="flex items-center justify-between mb-2">
                                    <i class="fas fa-user-graduate text-cyan-600 text-xl"></i>
                                    <span class="text-xl font-bold text-cyan-600">TBA</span>
                                </div>
                                <h4 class="font-bold text-gray-800 text-sm">CO-ASS</h4>
                                <p class="text-xs text-gray-600 mt-1">Co-assistant</p>
                            </div>
                            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-4 rounded-xl border border-blue-200">
                                <div class="flex items-center justify-between mb-2">
                                    <i class="fas fa-tooth text-sky-500 text-xl"></i>
                                    <div class="text-right">
                                        <span class="text-xl font-bold text-sky-500">$1,300</span>
                                        <span class="text-sm text-blue-500">- $1,900</span>
                                    </div>
                                </div>
                                <h4 class="font-bold text-gray-800 text-sm">DOKTER GIGI UMUM</h4>
                                <p class="text-xs text-gray-600 mt-1">Per Konsultasi (TERGANTUNG KESULITAN)</p>
                            </div>
                            <div
                                class="bg-gradient-to-br from-indigo-50 to-purple-50 p-4 rounded-xl border border-indigo-200">
                                <div class="flex items-center justify-between mb-2">
                                    <i class="fas fa-user-md text-indigo-600 text-xl"></i>
                                    <div class="text-right">
                                        <span class="text-xl font-bold text-indigo-600">$1,900</span>
                                        <span class="text-sm text-indigo-500">- $3,800</span>
                                    </div>
                                </div>
                                <h4 class="font-bold text-gray-800 text-sm">DOKTER SPESIALIS GIGI & BEDAH MULUT</h4>
                                <p class="text-xs text-gray-600 mt-1">Per Konsultasi (TERGANTUNG KESULITAN)</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-info-circle text-yellow-600 text-xl mt-1"></i>
                                    <div>
                                        <h4 class="font-semibold text-yellow-800 mb-2">Catatan Penting</h4>
                                        <p class="text-sm text-yellow-700">Jika diresepkan pemeriksaan tambahan <span
                                                class="font-bold">$650</span></p>
                                        <p class="text-sm text-yellow-700 mt-1">Jika diresepkan obat tambahan akan ditambah
                                            <span class="font-bold">$130</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- REGULASI PENANGANAN PINGSAN (CASH) -->
                <div
                    class="bg-white rounded-3xl shadow-lg border border-green-200 overflow-hidden hover:shadow-xl transition-all duration-300">
                    <div class="bg-gradient-to-r from-green-500 to-emerald-500 text-white p-6">
                        <h3 class="text-2xl font-bold flex items-center">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-ambulance text-xl"></i>
                            </div>
                            REGULASI PENANGANAN PINGSAN (CASH)
                        </h3>
                        <p class="text-green-100 mt-2">Layanan penanganan pingsan berdasarkan area lokasi</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div
                                class="bg-gradient-to-br from-green-50 to-emerald-50 p-4 rounded-xl border border-green-200 hover:shadow-md transition-all duration-300">
                                <div class="flex items-center justify-between mb-2">
                                    <i class="fas fa-city text-green-600 text-xl"></i>
                                    <span class="text-2xl font-bold text-green-600">$380</span>
                                </div>
                                <h4 class="font-bold text-gray-800 text-sm">AREA KOTA</h4>
                                <p class="text-xs text-gray-600 mt-1">Lokasi perkotaan</p>
                            </div>
                            <div
                                class="bg-gradient-to-br from-emerald-50 to-teal-50 p-4 rounded-xl border border-emerald-200 hover:shadow-md transition-all duration-300">
                                <div class="flex items-center justify-between mb-2">
                                    <i class="fas fa-umbrella-beach text-emerald-600 text-xl"></i>
                                    <span class="text-2xl font-bold text-emerald-600">$320</span>
                                </div>
                                <h4 class="font-bold text-gray-800 text-sm">SANDY SHORES</h4>
                                <p class="text-xs text-gray-600 mt-1">Area pantai</p>
                            </div>
                            <div
                                class="bg-gradient-to-br from-teal-50 to-cyan-50 p-4 rounded-xl border border-teal-200 hover:shadow-md transition-all duration-300">
                                <div class="flex items-center justify-between mb-2">
                                    <i class="fas fa-mountain text-teal-600 text-xl"></i>
                                    <span class="text-2xl font-bold text-teal-600">$550</span>
                                </div>
                                <h4 class="font-bold text-gray-800 text-sm">GUNUNG - LAUT</h4>
                                <p class="text-xs text-gray-600 mt-1">Area terpencil</p>
                            </div>
                            <div
                                class="bg-gradient-to-br from-cyan-50 to-blue-50 p-4 rounded-xl border border-cyan-200 hover:shadow-md transition-all duration-300">
                                <div class="flex items-center justify-between mb-2">
                                    <i class="fas fa-crosshairs text-cyan-600 text-xl"></i>
                                    <span class="text-2xl font-bold text-cyan-600">$570</span>
                                </div>
                                <h4 class="font-bold text-gray-800 text-sm">AREA HUNTING</h4>
                                <p class="text-xs text-gray-600 mt-1">Zona berburu</p>
                            </div>
                            <div
                                class="bg-gradient-to-br from-blue-50 to-indigo-50 p-4 rounded-xl border border-blue-200 hover:shadow-md transition-all duration-300">
                                <div class="flex items-center justify-between mb-2">
                                    <i class="fas fa-bomb text-sky-500 text-xl"></i>
                                    <span class="text-2xl font-bold text-sky-500">$500</span>
                                </div>
                                <h4 class="font-bold text-gray-800 text-sm">ZONA PERANG / PRA-SITUASI</h4>
                                <p class="text-xs text-gray-600 mt-1">Area konflik</p>
                            </div>
                            <div
                                class="bg-gradient-to-br from-indigo-50 to-purple-50 p-4 rounded-xl border border-indigo-200 hover:shadow-md transition-all duration-300">
                                <div class="flex items-center justify-between mb-2">
                                    <i class="fas fa-fist-raised text-indigo-600 text-xl"></i>
                                    <span class="text-2xl font-bold text-indigo-600">$570</span>
                                </div>
                                <h4 class="font-bold text-gray-800 text-sm">ZONA ADU MEKANIK / UFC</h4>
                                <p class="text-xs text-gray-600 mt-1">Area pertarungan</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-info-circle text-yellow-600 text-xl mt-1"></i>
                                    <div>
                                        <h4 class="font-semibold text-yellow-800 mb-2">Catatan Penting</h4>
                                        <p class="text-sm text-yellow-700">Jika terdapat luka dan diresepkan obat atau
                                            bandage tambahan, akan ditambah <span class="font-bold">$20</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- REGULASI PEMBUATAN SURAT-SURAT (CASH) -->
                <div
                    class="bg-white rounded-3xl shadow-lg border border-purple-200 overflow-hidden hover:shadow-xl transition-all duration-300">
                    <div class="bg-gradient-to-r from-purple-500 to-indigo-500 text-white p-6">
                        <h3 class="text-2xl font-bold flex items-center">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-file-alt text-xl"></i>
                            </div>
                            REGULASI PEMBUATAN SURAT-SURAT (CASH)
                        </h3>
                        <p class="text-purple-100 mt-2">Layanan pembuatan dokumen medis resmi</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div
                                class="bg-gradient-to-br from-purple-50 to-indigo-50 p-6 rounded-xl border border-purple-200">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-certificate text-purple-600 text-2xl"></i>
                                        <h4 class="font-bold text-gray-800">SURAT KETERANGAN KESEHATAN</h4>
                                    </div>
                                    <span class="text-3xl font-bold text-purple-600">$2,000</span>
                                </div>
                                <p class="text-sm text-gray-600">Dokumen resmi untuk keperluan administrasi kesehatan</p>
                            </div>
                            <div
                                class="bg-gradient-to-br from-indigo-50 to-blue-50 p-6 rounded-xl border border-indigo-200">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-brain text-indigo-600 text-2xl"></i>
                                        <h4 class="font-bold text-gray-800">SURAT KETERANGAN PSIKOLOGI</h4>
                                    </div>
                                    <span class="text-3xl font-bold text-indigo-600">$3,000</span>
                                </div>
                                <p class="text-sm text-gray-600">Dokumen psikologis untuk keperluan khusus</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- REGULASI KONSULTASI (CASH) -->
                <div
                    class="bg-white rounded-3xl shadow-lg border border-indigo-200 overflow-hidden hover:shadow-xl transition-all duration-300">
                    <div class="bg-gradient-to-r from-indigo-500 to-blue-500 text-white p-6">
                        <h3 class="text-2xl font-bold flex items-center">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-user-md text-xl"></i>
                            </div>
                            REGULASI KONSULTASI (CASH)
                        </h3>
                        <p class="text-indigo-100 mt-2">Layanan konsultasi kesehatan (Wajib Cash)</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div
                                class="bg-gradient-to-br from-indigo-50 to-blue-50 p-6 rounded-xl border border-indigo-200">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-user-nurse text-indigo-600 text-2xl"></i>
                                        <h4 class="font-bold text-gray-800">KONSULTASI DOKTER UMUM</h4>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-2xl font-bold text-indigo-600">$2,500</span>
                                        <span class="text-lg text-indigo-500">- $10,000</span>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600">Pemeriksaan dan konsultasi umum</p>
                            </div>
                            <div class="bg-gradient-to-br from-blue-50 to-cyan-50 p-6 rounded-xl border border-blue-200">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-user-graduate text-sky-500 text-2xl"></i>
                                        <h4 class="font-bold text-gray-800">KONSULTASI SPESIALIS</h4>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-2xl font-bold text-sky-500">$5,000</span>
                                        <span class="text-lg text-blue-500">- $10,000</span>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600">Konsultasi dengan dokter spesialis</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- REGULASI OPERASI (CASH) -->
                <div
                    class="bg-white rounded-3xl shadow-lg border border-orange-200 overflow-hidden hover:shadow-xl transition-all duration-300">
                    <div class="bg-gradient-to-r from-orange-500 to-red-500 text-white p-6">
                        <h3 class="text-2xl font-bold flex items-center">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-cut text-xl"></i>
                            </div>
                            REGULASI OPERASI (CASH)
                        </h3>
                        <p class="text-orange-100 mt-2">Layanan operasi (Wajib Cash)</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-gradient-to-br from-orange-50 to-red-50 p-6 rounded-xl border border-orange-200">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-scalpel text-orange-600 text-2xl"></i>
                                        <h4 class="font-bold text-gray-800">OPERASI BESAR</h4>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-2xl font-bold text-orange-600">$15,000</span>
                                        <span class="text-lg text-orange-500">- $20,000</span>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 mb-3">Prosedur operasi kompleks dan rumit</p>
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                    <p class="text-xs text-yellow-800 font-semibold">TERGANTUNG KESULITAN</p>
                                </div>
                            </div>
                            <div class="bg-gradient-to-br from-red-50 to-pink-50 p-6 rounded-xl border border-red-200">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-tools text-red-600 text-2xl"></i>
                                        <h4 class="font-bold text-gray-800">OPERASI KECIL</h4>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-2xl font-bold text-red-600">$5,000</span>
                                        <span class="text-lg text-red-500">- $10,000</span>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 mb-3">Prosedur operasi sederhana dan ringan</p>
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                    <p class="text-xs text-yellow-800 font-semibold">TERGANTUNG KESULITAN</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- REGULASI FARMASI (INVOICE) -->
                <div
                    class="bg-white rounded-3xl shadow-lg border border-teal-200 overflow-hidden hover:shadow-xl transition-all duration-300">
                    <div class="bg-gradient-to-r from-teal-500 to-cyan-500 text-white p-6">
                        <h3 class="text-2xl font-bold flex items-center">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-pills text-xl"></i>
                            </div>
                            REGULASI FARMASI (INVOICE)
                        </h3>
                        <p class="text-teal-100 mt-2">Layanan obat-obatan dan peralatan medis</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div
                                class="bg-gradient-to-br from-teal-50 to-cyan-50 p-4 rounded-xl border border-teal-200 text-center">
                                <i class="fas fa-band-aid text-teal-600 text-3xl mb-3"></i>
                                <h4 class="font-bold text-gray-800 mb-2">BANDAGE</h4>
                                <div class="text-2xl font-bold text-teal-600 mb-1">$20</div>
                                <p class="text-xs text-gray-600">MAX 25 PCS</p>
                            </div>
                            <div
                                class="bg-gradient-to-br from-cyan-50 to-blue-50 p-4 rounded-xl border border-cyan-200 text-center">
                                <i class="fas fa-first-aid text-cyan-600 text-3xl mb-3"></i>
                                <h4 class="font-bold text-gray-800 mb-2">IFAKS</h4>
                                <div class="text-2xl font-bold text-cyan-600 mb-1">$70</div>
                                <p class="text-xs text-gray-600">MAX 6 PCS</p>
                            </div>
                            <div
                                class="bg-gradient-to-br from-blue-50 to-indigo-50 p-4 rounded-xl border border-blue-200 text-center">
                                <i class="fas fa-pills text-sky-500 text-3xl mb-3"></i>
                                <h4 class="font-bold text-gray-800 mb-2">PAINKILLER</h4>
                                <div class="text-2xl font-bold text-sky-500 mb-1">$70</div>
                                <p class="text-xs text-gray-600">MAX 6 PCS</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- REGULASI KEMATIAN (INVOICE) -->
                <div
                    class="bg-white rounded-3xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300">
                    <div class="bg-gradient-to-r from-gray-500 to-slate-500 text-white p-6">
                        <h3 class="text-2xl font-bold flex items-center">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-cross text-xl"></i>
                            </div>
                            REGULASI KEMATIAN (INVOICE)
                        </h3>
                        <p class="text-gray-100 mt-2">Layanan pemakaman dan kremasi</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-gradient-to-br from-gray-50 to-slate-50 p-6 rounded-xl border border-gray-200">
                                <div class="text-center mb-4">
                                    <i class="fas fa-archway text-gray-600 text-3xl mb-3"></i>
                                    <h4 class="font-bold text-gray-800 mb-2">PAKET A. PENGUBURAN</h4>
                                    <div class="text-3xl font-bold text-gray-600">$25,000</div>
                                </div>
                                <p class="text-sm text-gray-600 mb-2 font-semibold">CASH</p>
                                <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                                    <li>Pembersihan Jenazah (Mandi)</li>
                                    <li>Pengantaran ke Tempat Pemakaman menggunakan IME Van Ambulance</li>
                                    <li>Gratis Kamar Mayat 2 Hari International (untuk Melayat)</li>
                                    <li>Penggalian Liang Kubur + Penguburan Jenazah</li>
                                </ul>
                            </div>
                            <div class="bg-gradient-to-br from-slate-50 to-gray-50 p-6 rounded-xl border border-slate-200">
                                <div class="text-center mb-4">
                                    <i class="fas fa-fire text-slate-600 text-3xl mb-3"></i>
                                    <h4 class="font-bold text-gray-800 mb-2">PAKET B. KREMASI</h4>
                                    <div class="text-3xl font-bold text-slate-600">$18,800</div>
                                </div>
                                <p class="text-sm text-gray-600 mb-2 font-semibold">CASH</p>
                                <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                                    <li>Pembersihan Jenazah (Mandi)</li>
                                    <li>Proses Kremasi Oven Modern 12 - 24 Jam International</li>
                                    <li>IME Medical Center Gucci Abu Exclusive, dibuat oleh pengrajin terbaik dan dilapisi
                                        dengan emas</li>
                                </ul>
                            </div>
                            <div class="bg-gradient-to-br from-blue-50 to-sky-50 p-6 rounded-xl border border-blue-200">
                                <div class="text-center mb-4">
                                    <i class="fas fa-users text-sky-500 text-3xl mb-3"></i>
                                    <h4 class="font-bold text-gray-800 mb-2">PAKET C. KELUARGA</h4>
                                    <div class="text-3xl font-bold text-sky-500">$7,500</div>
                                </div>
                                <p class="text-sm text-gray-600 mb-2 font-semibold">CASH</p>
                                <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                                    <li>Jenazah diambil oleh pihak keluarga</li>
                                    <li>Administrasi surat kematian</li>
                                    <li>Sertifikat kematian resmi</li>
                                    <li>Tidak termasuk layanan pemakaman/kremasi</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- REGULASI RAWAT INAP (15 MENIT / DAY) (INVOICE) -->
                <div
                    class="bg-white rounded-3xl shadow-lg border border-pink-200 overflow-hidden hover:shadow-xl transition-all duration-300">
                    <div class="bg-gradient-to-r from-pink-500 to-rose-500 text-white p-6">
                        <h3 class="text-2xl font-bold flex items-center">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-bed text-xl"></i>
                            </div>
                            REGULASI RAWAT INAP (15 MENIT / DAY)
                        </h3>
                        <p class="text-pink-100 mt-2">Layanan rawat inap dengan sistem tagihan harian</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div
                                class="bg-gradient-to-br from-pink-50 to-rose-50 p-6 rounded-xl border border-pink-200 text-center">
                                <i class="fas fa-crown text-pink-600 text-3xl mb-3"></i>
                                <h4 class="font-bold text-gray-800 mb-2">VIP ROOM</h4>
                                <div class="text-3xl font-bold text-pink-600 mb-1">$650</div>
                                <p class="text-sm text-gray-600">/ DAY</p>
                                <p class="text-xs text-pink-700 mt-2">Kamar mewah dengan fasilitas lengkap</p>
                            </div>
                            <div
                                class="bg-gradient-to-br from-rose-50 to-pink-50 p-6 rounded-xl border border-rose-200 text-center">
                                <i class="fas fa-star text-rose-600 text-3xl mb-3"></i>
                                <h4 class="font-bold text-gray-800 mb-2">KELAS 1</h4>
                                <div class="text-3xl font-bold text-rose-600 mb-1">$400</div>
                                <p class="text-sm text-gray-600">/ DAY</p>
                                <p class="text-xs text-rose-700 mt-2">Kamar standar dengan fasilitas baik</p>
                            </div>
                            <div
                                class="bg-gradient-to-br from-pink-50 to-purple-50 p-6 rounded-xl border border-pink-200 text-center">
                                <i class="fas fa-bed text-purple-600 text-3xl mb-3"></i>
                                <h4 class="font-bold text-gray-800 mb-2">KELAS 2</h4>
                                <div class="text-3xl font-bold text-purple-600 mb-1">$200</div>
                                <p class="text-sm text-gray-600">/ DAY</p>
                                <p class="text-xs text-purple-700 mt-2">Kamar standar dengan fasilitas dasar</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- REGULASI VISUM DAN OTOPSI MAYAT -->
                <div
                    class="bg-white rounded-3xl shadow-lg border border-yellow-200 overflow-hidden hover:shadow-xl transition-all duration-300">
                    <div class="bg-gradient-to-r from-yellow-500 to-amber-500 text-white p-6">
                        <h3 class="text-2xl font-bold flex items-center">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-search text-xl"></i>
                            </div>
                            REGULASI VISUM DAN OTOPSI MAYAT
                        </h3>
                        <p class="text-yellow-100 mt-2">Layanan forensik dan pemeriksaan medis khusus</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div
                                class="bg-gradient-to-br from-yellow-50 to-amber-50 p-4 rounded-xl border border-yellow-200">
                                <div class="flex items-center justify-between mb-2">
                                    <i class="fas fa-user-check text-yellow-600 text-xl"></i>
                                    <span class="text-xl font-bold text-yellow-600">$25,000</span>
                                </div>
                                <h4 class="font-bold text-gray-800 text-sm">Visum et Repertum Pada Orang Hidup</h4>
                                <p class="text-xs text-gray-600">Pemeriksaan pada orang hidup</p>
                            </div>
                            <div
                                class="bg-gradient-to-br from-amber-50 to-orange-50 p-4 rounded-xl border border-amber-200">
                                <div class="flex items-center justify-between mb-2">
                                    <i class="fas fa-user-times text-amber-600 text-xl"></i>
                                    <span class="text-xl font-bold text-amber-600">$38,000</span>
                                </div>
                                <h4 class="font-bold text-gray-800 text-sm">Visum et Repertum Post Mortem</h4>
                                <p class="text-xs text-gray-600">Pemeriksaan setelah kematian</p>
                            </div>
                            <div class="bg-gradient-to-br from-orange-50 to-red-50 p-4 rounded-xl border border-orange-200">
                                <div class="flex items-center justify-between mb-2">
                                    <i class="fas fa-microscope text-orange-600 text-xl"></i>
                                    <span class="text-xl font-bold text-orange-600">$63,000</span>
                                </div>
                                <h4 class="font-bold text-gray-800 text-sm">Visum et Repertum Post Mortem dengan Autopsi
                                </h4>
                                <p class="text-xs text-gray-600">Pemeriksaan lengkap dengan autopsi</p>
                            </div>
                            <div class="bg-gradient-to-br from-red-50 to-pink-50 p-4 rounded-xl border border-red-200">
                                <div class="flex items-center justify-between mb-2">
                                    <i class="fas fa-brain text-red-600 text-xl"></i>
                                    <span class="text-xl font-bold text-red-600">$32,000</span>
                                </div>
                                <h4 class="font-bold text-gray-800 text-sm">Visum et Repertum Psikiatri</h4>
                                <p class="text-xs text-gray-600">Pemeriksaan psikiatris</p>
                            </div>
                            <div class="bg-gradient-to-br from-pink-50 to-purple-50 p-4 rounded-xl border border-pink-200">
                                <div class="flex items-center justify-between mb-2">
                                    <i class="fas fa-flask text-pink-600 text-xl"></i>
                                    <span class="text-xl font-bold text-pink-600">$38,000</span>
                                </div>
                                <h4 class="font-bold text-gray-800 text-sm">Laboratorium Forensik</h4>
                                <p class="text-xs text-gray-600">Pemeriksaan laboratorium forensik</p>
                            </div>
                            <div
                                class="bg-gradient-to-br from-purple-50 to-indigo-50 p-4 rounded-xl border border-purple-200">
                                <div class="flex items-center justify-between mb-2">
                                    <i class="fas fa-vial text-purple-600 text-xl"></i>
                                    <span class="text-xl font-bold text-purple-600">$13,000</span>
                                </div>
                                <h4 class="font-bold text-gray-800 text-sm">Laboratorium Klinis</h4>
                                <p class="text-xs text-gray-600">Pemeriksaan laboratorium klinis</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- OPERASI PLASTIK -->
                <div
                    class="bg-white rounded-3xl shadow-lg border border-fuchsia-200 overflow-hidden hover:shadow-xl transition-all duration-300">
                    <div class="bg-gradient-to-r from-fuchsia-500 to-pink-500 text-white p-6">
                        <h3 class="text-2xl font-bold flex items-center">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-magic text-xl"></i>
                            </div>
                            OPERASI PLASTIK
                        </h3>
                        <p class="text-fuchsia-100 mt-2">Layanan bedah estetika (Cash + Billing)</p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div
                                class="bg-gradient-to-br from-fuchsia-50 to-pink-50 p-6 rounded-xl border border-fuchsia-200">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-comments text-fuchsia-600 text-2xl"></i>
                                        <h4 class="font-bold text-gray-800">KONSULTASI BEDAH PLASTIK</h4>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-3xl font-bold text-fuchsia-600">Gratis</span>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600">Konsultasi awal dengan dokter spesialis</p>
                            </div>
                            <div class="bg-gradient-to-br from-pink-50 to-rose-50 p-6 rounded-xl border border-pink-200">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-scalpel text-pink-600 text-2xl"></i>
                                        <h4 class="font-bold text-gray-800">BIAYA OPERASI</h4>
                                    </div>
                                    <div class="text-right flex flex-col items-end">
                                        <div class="flex items-center">
                                            <span
                                                class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded mr-2">CASH</span>
                                            <span class="text-3xl font-bold text-pink-600">$10,000</span>
                                        </div>
                                        <div class="flex items-center mt-1">
                                            <span
                                                class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded mr-2">BILLING</span>
                                            <span class="text-xl font-bold text-pink-500">$3,000</span>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600">Total Biaya: $13,000 (Cash + Invoice)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gradient-to-r from-gray-50 to-slate-50 p-4 sm:p-6 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center space-x-3 sm:space-x-4">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-red-500 to-pink-500 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Informasi Penting</h4>
                            <p class="text-xs sm:text-sm text-gray-600">Regulasi ini berlaku efektif mulai 02 Januari 2026
                            </p>
                        </div>
                    </div>
                    <div class="flex w-full sm:w-auto space-x-3">
                        <button onclick="closeRegulationModal()"
                            class="w-full sm:w-auto bg-gradient-to-r from-gray-500 to-slate-500 text-white px-6 py-3 rounded-xl font-bold hover:from-gray-600 hover:to-slate-600 transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-times mr-2"></i>Tutup
                        </button>
                        <button onclick="closeRegulationModal()"
                            class="bg-gradient-to-r from-sky-500 to-cyan-500 text-white px-8 py-3 rounded-xl font-bold hover:from-sky-600 hover:to-cyan-600 transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-check mr-2"></i>Mengerti
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ================================================================
     HERO SECTION - Ultra-Modern Glassmorphism Medical Portal
================================================================ -->
<div class="ime-hero-wrapper relative min-h-[90vh] flex items-center justify-center pt-24 pb-16 px-4 sm:px-6 lg:px-8 overflow-hidden">
    <!-- Animated background glowing orbs -->
    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[400px] bg-gradient-to-r from-sky-600/20 via-cyan-500/20 to-blue-600/10 rounded-full blur-[120px] pointer-events-none animate-pulse"></div>
    <div class="absolute bottom-10 right-10 w-[400px] h-[400px] bg-cyan-600/15 rounded-full blur-[100px] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto w-full relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- LEFT: Hero Copy & Actions -->
            <div class="lg:col-span-7 space-y-8 text-center lg:text-left">
                
                <!-- Live Status Badge -->
                <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white shadow-xl">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    <span class="text-xs font-bold tracking-wide uppercase text-sky-200">Los Santos Medical Portal &bull; On Duty</span>
                </div>

                <!-- Main Headline -->
                <div class="space-y-4">
                    <h1 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold text-white tracking-tight leading-[1.08] font-sans">
                        Portal Medis <br class="hidden sm:inline">
                        <span class="bg-clip-text text-transparent bg-gradient-to-r from-sky-300 via-cyan-200 to-blue-400 drop-shadow-sm">
                            Terpadu iMe
                        </span>
                    </h1>
                    <p class="text-base sm:text-lg text-slate-200 max-w-2xl mx-auto lg:mx-0 font-normal leading-relaxed">
                        Layanan kesehatan digital resmi untuk warga Los Santos. Akses konsultasi medis, operasi plastik, surat keterangan sehat, hingga dukungan darurat secara cepat dan profesional.
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap items-center justify-center lg:justify-start gap-4 pt-2">
                    <a href="#layanan" class="group inline-flex items-center gap-3 px-7 py-3.5 rounded-2xl bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-400 hover:to-cyan-400 text-white font-bold text-sm shadow-lg shadow-sky-500/30 hover:shadow-cyan-500/50 hover:-translate-y-0.5 transition-all duration-300">
                        <i class="fas fa-stethoscope text-base transition-transform group-hover:scale-110"></i>
                        <span>Lihat Layanan Medis</span>
                        <i class="fas fa-arrow-right text-xs transition-transform group-hover:translate-x-1"></i>
                    </a>
                    <button onclick="showRegulationModal()" class="inline-flex items-center gap-2.5 px-6 py-3.5 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-semibold text-sm border border-white/20 backdrop-blur-md hover:-translate-y-0.5 transition-all duration-300">
                        <i class="fas fa-file-invoice-dollar text-sky-300"></i>
                        <span>Regulasi Pengobatan</span>
                    </button>
                </div>

                <!-- Stats Bar -->
                <div class="pt-6 border-t border-white/10 grid grid-cols-3 gap-6 max-w-lg mx-auto lg:mx-0 text-white">
                    <div>
                        <div class="text-2xl sm:text-3xl font-extrabold text-sky-300">{{ number_format($stats['total_forms']) }}+</div>
                        <div class="text-xs text-slate-300 font-medium mt-1">Pasien Terlayani</div>
                    </div>
                    <div>
                        <div class="text-2xl sm:text-3xl font-extrabold text-cyan-300">{{ $stats['total_staff'] }}+</div>
                        <div class="text-xs text-slate-300 font-medium mt-1">Tenaga Medis</div>
                    </div>
                    <div>
                        <div class="text-2xl sm:text-3xl font-extrabold text-emerald-300">100%</div>
                        <div class="text-xs text-slate-300 font-medium mt-1">Sistem Digital</div>
                    </div>
                </div>

            </div>

            <!-- RIGHT: Visual Feature Card Stack -->
            <div class="lg:col-span-5 relative">
                <div class="relative mx-auto max-w-md lg:max-w-none">
                    
                    <!-- Main Card Showcase -->
                    <div class="bg-slate-900/80 backdrop-blur-xl border border-white/15 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6 relative overflow-hidden group">
                        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-sky-500/10 rounded-full blur-2xl group-hover:bg-sky-500/20 transition-all duration-500"></div>

                        <!-- Hospital Badges Header -->
                        <div class="flex items-center justify-between border-b border-white/10 pb-4">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-2 bg-white/95 px-3 py-1.5 rounded-xl shadow-md">
                                    <img src="{{ asset('images/motionlife-logo.png') }}" alt="Alta" class="h-6 w-6 object-contain">
                                    <div class="h-4 w-px bg-slate-300"></div>
                                    <img src="{{ asset('images/logo rhv2.png') }}" alt="Roxwood" class="h-6 w-6 object-contain">
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-white uppercase tracking-wider">iMe Health Center</div>
                                    <div class="text-[10px] text-slate-400">Alta & Roxwood Hospital</div>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">ACTIVE</span>
                        </div>

                        <!-- Hero Doctor Image Container -->
                        <div class="relative h-56 rounded-2xl overflow-hidden group/img border border-white/10">
                            <img src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=900&q=85" alt="Medical Team" class="w-full h-full object-cover object-top transition-transform duration-700 group-hover/img:scale-105">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/20 to-transparent"></div>
                            
                            <!-- Floating Info Tags -->
                            <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between text-white text-xs bg-slate-900/80 backdrop-blur-md px-3.5 py-2 rounded-xl border border-white/10">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-hospital-alt text-sky-400"></i>
                                    <span class="font-semibold">Alta Street, Los Santos</span>
                                </div>
                                <a href="{{ route('public.doctor-schedule') }}" class="text-sky-300 hover:text-white font-bold flex items-center gap-1 transition-colors">
                                    Jadwal <i class="fas fa-chevron-right text-[10px]"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Quick Action Grid Inside Card -->
                        <div class="grid grid-cols-2 gap-3 pt-1">
                            <a href="{{ route('public.cek-kesehatan') }}" class="flex items-center gap-3 p-3 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition-all text-white group/btn">
                                <div class="w-9 h-9 rounded-lg bg-sky-500/20 text-sky-400 flex items-center justify-center flex-shrink-0 group-hover/btn:bg-sky-500 group-hover/btn:text-white transition-all">
                                    <i class="fas fa-file-medical text-sm"></i>
                                </div>
                                <div class="text-left">
                                    <div class="text-xs font-bold">Surat Sehat</div>
                                    <div class="text-[10px] text-slate-400">Cek kesehatan</div>
                                </div>
                            </a>
                            <a href="{{ route('public.operasi-plastik') }}" class="flex items-center gap-3 p-3 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition-all text-white group/btn">
                                <div class="w-9 h-9 rounded-lg bg-cyan-500/20 text-cyan-400 flex items-center justify-center flex-shrink-0 group-hover/btn:bg-cyan-500 group-hover/btn:text-white transition-all">
                                    <i class="fas fa-user-nurse text-sm"></i>
                                </div>
                                <div class="text-left">
                                    <div class="text-xs font-bold">Bedah Oplas</div>
                                    <div class="text-[10px] text-slate-400">Estetika wajah</div>
                                </div>
                            </a>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<!-- ================================================================
     SERVICES SECTION - Sleek Modern Grid
================================================================ -->
<section id="layanan" class="py-20 bg-slate-900/60 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

        <!-- Section Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-14 gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-500/10 border border-sky-500/20 text-sky-400 text-xs font-bold tracking-widest uppercase mb-3">
                    <i class="fas fa-briefcase-medical"></i> Services
                </div>
                <h2 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight">
                    Layanan Medis Utama
                </h2>
            </div>
            <p class="text-slate-400 text-sm max-w-md leading-relaxed">
                Pilih kategori layanan yang Anda butuhkan. Setiap permohonan diproses oleh tim medis berizin dan profesional.
            </p>
        </div>

        <!-- Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

            <!-- Card 01: Konsultasi Medis -->
            <a href="{{ route('public.cek-kesehatan') }}" class="group relative bg-slate-800/50 hover:bg-slate-800/90 border border-white/10 hover:border-sky-500/50 rounded-3xl p-6 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:shadow-sky-500/10 flex flex-col justify-between overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-sky-500/5 rounded-bl-full transition-all group-hover:bg-sky-500/15"></div>
                <div>
                    <div class="flex items-center justify-between mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-sky-500/15 border border-sky-500/30 text-sky-400 flex items-center justify-center text-xl group-hover:scale-110 group-hover:bg-sky-500 group-hover:text-white transition-all duration-300">
                            <i class="fas fa-stethoscope"></i>
                        </div>
                        <span class="text-xs font-black text-slate-500 tracking-wider">01</span>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2 group-hover:text-sky-300 transition-colors">
                        Konsultasi Medis
                    </h3>
                    <p class="text-slate-400 text-xs leading-relaxed mb-6">
                        Pemeriksaan kesehatan umum, diagnosis penyakit, serta penerbitan surat keterangan sehat resmi.
                    </p>
                </div>
                <div class="inline-flex items-center gap-2 text-xs font-bold text-sky-400 group-hover:text-sky-300 transition-colors pt-4 border-t border-white/5">
                    <span>Surat Kesehatan</span>
                    <i class="fas fa-arrow-right transition-transform group-hover:translate-x-1.5"></i>
                </div>
            </a>

            <!-- Card 02: Operasi Plastik -->
            <a href="{{ route('public.operasi-plastik') }}" class="group relative bg-slate-800/50 hover:bg-slate-800/90 border border-white/10 hover:border-cyan-500/50 rounded-3xl p-6 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:shadow-cyan-500/10 flex flex-col justify-between overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-cyan-500/5 rounded-bl-full transition-all group-hover:bg-cyan-500/15"></div>
                <div>
                    <div class="flex items-center justify-between mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-cyan-500/15 border border-cyan-500/30 text-cyan-400 flex items-center justify-center text-xl group-hover:scale-110 group-hover:bg-cyan-500 group-hover:text-white transition-all duration-300">
                            <i class="fas fa-user-nurse"></i>
                        </div>
                        <span class="text-xs font-black text-slate-500 tracking-wider">02</span>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2 group-hover:text-cyan-300 transition-colors">
                        Operasi Plastik
                    </h3>
                    <p class="text-slate-400 text-xs leading-relaxed mb-6">
                        Prosedur bedah rekonstruksi & estetika oleh dokter spesialis bedah berpengalaman.
                    </p>
                </div>
                <div class="inline-flex items-center gap-2 text-xs font-bold text-cyan-400 group-hover:text-cyan-300 transition-colors pt-4 border-t border-white/5">
                    <span>Pendaftaran Oplas</span>
                    <i class="fas fa-arrow-right transition-transform group-hover:translate-x-1.5"></i>
                </div>
            </a>

            <!-- Card 03: Konsultasi Psikologi -->
            <a href="{{ route('public.surat-psikolog') }}" class="group relative bg-slate-800/50 hover:bg-slate-800/90 border border-white/10 hover:border-indigo-500/50 rounded-3xl p-6 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:shadow-indigo-500/10 flex flex-col justify-between overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-500/5 rounded-bl-full transition-all group-hover:bg-indigo-500/15"></div>
                <div>
                    <div class="flex items-center justify-between mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-500/15 border border-indigo-500/30 text-indigo-400 flex items-center justify-center text-xl group-hover:scale-110 group-hover:bg-indigo-500 group-hover:text-white transition-all duration-300">
                            <i class="fas fa-brain"></i>
                        </div>
                        <span class="text-xs font-black text-slate-500 tracking-wider">03</span>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2 group-hover:text-indigo-300 transition-colors">
                        Konsultasi Psikologi
                    </h3>
                    <p class="text-slate-400 text-xs leading-relaxed mb-6">
                        Layanan konsultasi kesehatan mental dan penerbitan Surat Keterangan Psikologi resmi.
                    </p>
                </div>
                <div class="inline-flex items-center gap-2 text-xs font-bold text-indigo-400 group-hover:text-indigo-300 transition-colors pt-4 border-t border-white/5">
                    <span>Formulir Psikologi</span>
                    <i class="fas fa-arrow-right transition-transform group-hover:translate-x-1.5"></i>
                </div>
            </a>

            <!-- Card 04: Karakter Kill (CK) -->
            <a href="{{ route('public.pendaftaran-karakter') }}" class="group relative bg-slate-800/50 hover:bg-slate-800/90 border border-white/10 hover:border-rose-500/50 rounded-3xl p-6 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:shadow-rose-500/10 flex flex-col justify-between overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-rose-500/5 rounded-bl-full transition-all group-hover:bg-rose-500/15"></div>
                <div>
                    <div class="flex items-center justify-between mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-rose-500/15 border border-rose-500/30 text-rose-400 flex items-center justify-center text-xl group-hover:scale-110 group-hover:bg-rose-500 group-hover:text-white transition-all duration-300">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <span class="text-xs font-black text-slate-500 tracking-wider">04</span>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2 group-hover:text-rose-300 transition-colors">
                        Karakter Kill (CK)
                    </h3>
                    <p class="text-slate-400 text-xs leading-relaxed mb-6">
                        Penanganan tindakan medis khusus dan administrasi untuk kelengkapan storyline roleplay.
                    </p>
                </div>
                <div class="inline-flex items-center gap-2 text-xs font-bold text-rose-400 group-hover:text-rose-300 transition-colors pt-4 border-t border-white/5">
                    <span>Daftar Karakter Kill</span>
                    <i class="fas fa-arrow-right transition-transform group-hover:translate-x-1.5"></i>
                </div>
            </a>

        </div>
    </div>
</section>

<!-- ================================================================
     JAM OPERASIONAL & CTA SECTION
================================================================ -->
<section id="jadwal" class="py-20 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
            
            <!-- LEFT: Schedule Table Card (lg:col-span-7) -->
            <div class="lg:col-span-7 bg-slate-900/80 backdrop-blur-xl border border-white/10 rounded-3xl p-6 sm:p-8 flex flex-col justify-between shadow-xl">
                
                <div>
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-sky-500/20 text-sky-400 flex items-center justify-center text-lg">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-white">Jam Operasional</h3>
                                <p class="text-xs text-slate-400">Jadwal pelayanan medis di rumah sakit</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-white/10 text-slate-300 border border-white/10">WIB</span>
                    </div>

                    <!-- Notice Bar -->
                    <div class="mb-6 p-3.5 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center gap-3 text-xs text-amber-200">
                        <i class="fas fa-exclamation-triangle text-amber-400 text-base flex-shrink-0"></i>
                        <span><strong>Informasi:</strong> Pelayanan diberikan sesuai ketersediaan tenaga medis (On Duty).</span>
                    </div>

                    <!-- Schedule Items List -->
                    <div class="space-y-4">
                        
                        <!-- Item 1: Operasi Plastik -->
                        <div class="flex items-center justify-between p-4 rounded-2xl bg-white/5 border border-white/5 hover:border-white/10 transition-all">
                            <div class="flex items-center gap-3.5">
                                <div class="w-9 h-9 rounded-xl bg-sky-500/15 text-sky-400 flex items-center justify-center">
                                    <i class="fas fa-user-nurse"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-white">Operasi Plastik</div>
                                    <div class="text-xs text-slate-400">Shift 1: 13:00&ndash;16:00 &bull; Shift 2: 20:00&ndash;22:00</div>
                                </div>
                            </div>
                            <span class="text-xs font-semibold text-sky-300 bg-sky-500/10 px-2.5 py-1 rounded-lg">Shifted</span>
                        </div>

                        <!-- Item 2: Surat Medis -->
                        <div class="flex items-center justify-between p-4 rounded-2xl bg-white/5 border border-white/5 hover:border-white/10 transition-all">
                            <div class="flex items-center gap-3.5">
                                <div class="w-9 h-9 rounded-xl bg-indigo-500/15 text-indigo-400 flex items-center justify-center">
                                    <i class="fas fa-file-medical"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-white">Surat-Suratan Medis</div>
                                    <div class="text-xs text-slate-400">Shift 1: 13:00&ndash;17:00 &bull; Shift 2: 19:00&ndash;22:00</div>
                                </div>
                            </div>
                            <span class="text-xs font-semibold text-indigo-300 bg-indigo-500/10 px-2.5 py-1 rounded-lg">Shifted</span>
                        </div>

                        <!-- Item 3: Layanan Farmasi -->
                        <div class="flex items-center justify-between p-4 rounded-2xl bg-white/5 border border-white/5 hover:border-white/10 transition-all">
                            <div class="flex items-center gap-3.5">
                                <div class="w-9 h-9 rounded-xl bg-emerald-500/15 text-emerald-400 flex items-center justify-center">
                                    <i class="fas fa-pills"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-white">Layanan Farmasi & IGD</div>
                                    <div class="text-xs text-slate-400">Pengambilan obat & penanganan pingsan</div>
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                BUKA 24 JAM
                            </span>
                        </div>

                    </div>
                </div>

                <div class="pt-6 mt-6 border-t border-white/10 flex items-center justify-between text-xs text-slate-400">
                    <span>*Jadwal sewaktu-waktu dapat menyesuaikan situasi emergency roleplay</span>
                    <a href="{{ route('public.doctor-schedule') }}" class="text-sky-400 font-bold hover:underline">Jadwal Staf &rarr;</a>
                </div>

            </div>

            <!-- RIGHT: CTA Card (lg:col-span-5) -->
            <div class="lg:col-span-5 bg-gradient-to-br from-sky-600 via-sky-700 to-blue-900 rounded-3xl p-6 sm:p-8 text-white flex flex-col justify-between shadow-2xl relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full blur-3xl group-hover:scale-125 transition-transform duration-700"></div>

                <div class="space-y-4 relative z-10">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md text-sky-200 text-xs font-bold tracking-wider uppercase border border-white/20">
                        <i class="fas fa-headset"></i> Ready When You Are
                    </div>
                    <h3 class="text-2xl sm:text-3xl font-extrabold leading-tight">
                        Butuh Layanan Medis Sekarang?
                    </h3>
                    <p class="text-sky-100 text-xs sm:text-sm leading-relaxed">
                        Akses portal medis iMe dengan mudah dari mana saja. Isi formulir kesehatan, daftarkan janji temu, atau periksa regulasi biaya pengobatan.
                    </p>
                </div>

                <div class="pt-8 space-y-3 relative z-10">
                    <button onclick="showRegulationModal()" class="w-full py-3.5 px-6 rounded-2xl bg-white text-sky-900 hover:bg-sky-50 font-bold text-sm shadow-lg transition-all duration-300 flex items-center justify-center gap-2 group/btn">
                        <i class="fas fa-file-alt text-sky-600"></i>
                        <span>Cek Regulasi Pengobatan</span>
                        <i class="fas fa-chevron-right text-xs transition-transform group-hover/btn:translate-x-1"></i>
                    </button>
                    <a href="#layanan" class="w-full py-3.5 px-6 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-semibold text-sm border border-white/20 transition-all duration-300 flex items-center justify-center gap-2">
                        <i class="fas fa-th-list"></i>
                        <span>Semua Formulir Layanan</span>
                    </a>
                </div>

            </div>

        </div>

    </div>
</section>

@endsection

@push('styles')
<style>
    /* Sleek Custom Styles */
    .ime-hero-wrapper {
        background: radial-gradient(circle at 50% 0%, #0c4a6e 0%, #0f172a 100%);
    }
</style>
@endpush

@push('scripts')
<script>
    function showRegulationModal(){
        var m=document.getElementById('regulationModal');
        if(m){m.style.display='flex';document.body.style.overflow='hidden';}
    }
    function closeRegulationModal(){
        var m=document.getElementById('regulationModal');
        if(m){m.style.display='none';document.body.style.overflow='auto';}
    }
    document.addEventListener('DOMContentLoaded',function(){
        var m=document.getElementById('regulationModal');
        if(m){
            m.addEventListener('click',function(e){if(e.target===m)closeRegulationModal();});
            document.addEventListener('keydown',function(e){if(e.key==='Escape')closeRegulationModal();});
        }
    });
</script>
@endpush
