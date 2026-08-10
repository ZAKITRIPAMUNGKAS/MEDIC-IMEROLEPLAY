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
                                        <i class="fas fa-hospital text-blue-600 text-xl"></i>
                                        <span class="font-semibold text-gray-800">TREATMENT RS</span>
                                    </div>
                                    <span class="text-2xl font-bold text-blue-600">$200</span>
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
                                    <i class="fas fa-tooth text-blue-600 text-xl"></i>
                                    <div class="text-right">
                                        <span class="text-xl font-bold text-blue-600">$1,300</span>
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
                                    <i class="fas fa-bomb text-blue-600 text-xl"></i>
                                    <span class="text-2xl font-bold text-blue-600">$500</span>
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
                                        <i class="fas fa-user-graduate text-blue-600 text-2xl"></i>
                                        <h4 class="font-bold text-gray-800">KONSULTASI SPESIALIS</h4>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-2xl font-bold text-blue-600">$5,000</span>
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
                                <i class="fas fa-pills text-blue-600 text-3xl mb-3"></i>
                                <h4 class="font-bold text-gray-800 mb-2">PAINKILLER</h4>
                                <div class="text-2xl font-bold text-blue-600 mb-1">$70</div>
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
                                    <i class="fas fa-users text-blue-600 text-3xl mb-3"></i>
                                    <h4 class="font-bold text-gray-800 mb-2">PAKET C. KELUARGA</h4>
                                    <div class="text-3xl font-bold text-blue-600">$7,500</div>
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
    <!-- =========================================================
         HERO
    ========================================================= -->
    <section class="hero-wrapper">
        <div class="hero">

            <!-- HERO COPY -->
            <div class="hero-content">
                <div class="hero-eyebrow">Los Santos Medical Services</div>
                <h1 class="hero-title">iMe</h1>
                <p class="hero-subtitle">
                    Portal medis terpadu untuk mendapatkan layanan kesehatan
                    yang cepat, profesional, dan terpercaya.
                </p>
                <div class="hero-arrow">â†“</div>
            </div>

            <!-- WEBSITE MOCKUP -->
            <div class="mockup-stage">
                <div class="browser">
                    <div class="browser-bar">
                        <span class="dot"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                        <div class="ml-4 h-5 w-64 rounded-full bg-white border border-gray-200"></div>
                    </div>

                    <div class="browser-content">
                        <div class="browser-left">
                            <div class="browser-small">INNOVATIVE MEDICAL SERVICE</div>
                            <h2 class="browser-heading">
                                Medical care
                                <span>with confidence.</span>
                            </h2>
                            <p class="browser-description">
                                Kami menghadirkan pengalaman pelayanan medis
                                yang nyaman, cepat, dan profesional untuk
                                seluruh masyarakat Los Santos.
                            </p>
                            <a href="#layanan" class="browser-button">Lihat Layanan â†’</a>
                        </div>

                        <div class="browser-right">
                            <img
                                class="doctor-image"
                                src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=900&q=85"
                                alt="Doctor"
                            >
                            <div class="floating-label label-1">Reliability</div>
                            <div class="floating-label label-2">Professional</div>
                            <div class="floating-label label-3">Experience</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- =========================================================
         MAIN CONTENT
    ========================================================= -->
    <main class="content-section">
        <div class="section-container">

            <!-- INTRO -->
            <div class="intro">
                <div class="text-xs uppercase tracking-[.18em] text-blue-600 font-bold">iMe Medical</div>
                <h2 class="mt-4">
                    Teknologi medis,<br>dengan sentuhan manusia.
                </h2>
                <p>
                    Kami menggabungkan sistem digital dengan pelayanan
                    kesehatan yang humanis agar setiap pasien mendapatkan
                    pengalaman yang cepat, nyaman, dan terpercaya.
                </p>
            </div>

            <!-- ABOUT -->
            <section id="tentang">
                <div class="about-block">
                    <div class="about-content">
                        <div class="text-xs uppercase tracking-[.15em] text-blue-600 font-bold">Tentang iMe</div>
                        <h3 class="mt-4">
                            Pelayanan yang
                            <span class="text-blue-600">sederhana.</span>
                        </h3>
                        <p>
                            Seluruh kebutuhan medis Anda tersedia dalam
                            satu portal. Mulai dari konsultasi, tindakan
                            medis, administrasi hingga layanan khusus karakter.
                        </p>
                        <div class="stats">
                            <div class="stat">
                                <strong>{{ number_format($stats['total_forms']) }}+</strong>
                                <span>Pasien</span>
                            </div>
                            <div class="stat">
                                <strong>{{ $stats['total_staff'] }}+</strong>
                                <span>Tenaga Medis</span>
                            </div>
                            <div class="stat">
                                <strong>98%</strong>
                                <span>Kepuasan</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- SERVICES -->
            <section id="layanan" class="services-section">
                <div class="section-header">
                    <div>
                        <div class="text-xs uppercase tracking-[.15em] text-blue-600 font-bold">Our Services</div>
                        <h3 class="mt-3">Layanan medis</h3>
                    </div>
                    <p>
                        Pilih layanan yang Anda butuhkan
                        dan dapatkan pelayanan dari tenaga
                        medis profesional.
                    </p>
                </div>

                <div class="service-grid">
                    <article class="service-card">
                        <div class="service-number">01</div>
                        <div class="service-icon">âœš</div>
                        <h4>Konsultasi Medis</h4>
                        <p>Pemeriksaan dan konsultasi kesehatan umum.</p>
                        <a href="{{ route('public.cek-kesehatan') }}" class="service-link">Surat Kesehatan â†’</a>
                        <a href="{{ route('public.form', 'janji_temu') }}" class="service-link" style="margin-left:8px;">Janji Temu â†’</a>
                    </article>

                    <article class="service-card">
                        <div class="service-number">02</div>
                        <div class="service-icon">âœ¦</div>
                        <h4>Operasi Plastik</h4>
                        <p>Prosedur bedah estetika dengan tenaga profesional.</p>
                        <a href="{{ route('public.operasi-plastik') }}" class="service-link">Daftar Oplas â†’</a>
                    </article>

                    <article class="service-card">
                        <div class="service-number">03</div>
                        <div class="service-icon">â—‰</div>
                        <h4>Konsultasi Psikologi</h4>
                        <p>Dukungan kesehatan mental untuk karakter Anda.</p>
                        <a href="{{ route('public.surat-psikolog') }}" class="service-link">Formulir Psikologi â†’</a>
                    </article>

                    <article class="service-card">
                        <div class="service-number">04</div>
                        <div class="service-icon">â™¡</div>
                        <h4>Karakter Kill</h4>
                        <p>Layanan khusus penanganan medis kritis untuk kebutuhan roleplay.</p>
                        <a href="{{ route('public.pendaftaran-karakter') }}" class="service-link">Daftar Sekarang â†’</a>
                    </article>
                </div>
            </section>

            <!-- WHY US -->
            <section class="why-section">
                <div class="why-block">
                    <div class="why-copy">
                        <div class="text-xs uppercase tracking-[.15em] text-blue-200 font-bold">Why choose us</div>
                        <h3 class="mt-4">
                            Care<br>beyond<br>medicine.
                        </h3>
                        <p>
                            Karena pelayanan kesehatan bukan hanya
                            tentang tindakan medis. Kami memastikan
                            setiap pasien merasa aman dan dihargai.
                        </p>
                    </div>

                    <div class="why-image">
                        <img
                            src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=1200&q=85"
                            alt="Medical team"
                        >

                        <!-- FLOATING PHONE -->
                        <div class="phone">
                            <div class="phone-screen">
                                <div class="phone-top">iMe Medical</div>
                                <h4>Find<br>Doctor</h4>
                                <img
                                    src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=500&q=80"
                                    alt="Doctor"
                                >
                                <a href="{{ route('public.doctor-schedule') }}" class="phone-button">Lihat Jadwal â†’</a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- JAM OPERASIONAL -->
            <section id="jadwal" class="py-16">
                <div class="text-center mb-10">
                    <div class="text-xs uppercase tracking-[.15em] text-blue-600 font-bold">Jam Operasional</div>
                    <h2 class="mt-4 font-display" style="font-family:'DM Sans',sans-serif;font-size:clamp(32px,4vw,52px);line-height:.95;letter-spacing:-.05em;font-weight:500;">
                        Jadwal pelayanan<br>rumah sakit.
                    </h2>
                </div>

                <div class="schedule-table">
                    <!-- Notice -->
                    <div class="notice-bar">
                        <span style="font-size:13px;">âš ï¸</span>
                        <span><strong>Informasi:</strong> Pelayanan diberikan apabila tenaga medis bersangkutan bersedia & available (On Duty).</span>
                    </div>

                    <!-- Rows -->
                    <div class="schedule-row">
                        <div class="schedule-info">
                            <div class="schedule-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </div>
                            <div>
                                <div class="schedule-name">Operasi Plastik</div>
                                <div class="schedule-desc">Layanan estetika & pembedahan</div>
                            </div>
                        </div>
                        <div class="schedule-time">Shift 1: 13:00â€“16:00 &nbsp;â€¢&nbsp; Shift 2: 20:00â€“22:00 WIB</div>
                    </div>

                    <div class="schedule-row">
                        <div class="schedule-info">
                            <div class="schedule-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            </div>
                            <div>
                                <div class="schedule-name">Surat-Suratan Medis</div>
                                <div class="schedule-desc">Surat sehat, bebas narkoba & psikologi</div>
                            </div>
                        </div>
                        <div class="schedule-time">Shift 1: 13:00â€“17:00 &nbsp;â€¢&nbsp; Shift 2: 19:00â€“22:00 WIB</div>
                    </div>

                    <div class="schedule-row">
                        <div class="schedule-info">
                            <div class="schedule-icon" style="background:#dcfce7;color:#16a34a;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                            </div>
                            <div>
                                <div class="schedule-name">Layanan Farmasi</div>
                                <div class="schedule-desc">Pengambilan & pengobatan medis</div>
                            </div>
                        </div>
                        <div class="schedule-badge">
                            <span class="badge-dot"></span>
                            BUKA 24 JAM
                        </div>
                    </div>
                </div>
            </section>

            <!-- TESTIMONIALS -->
            @if(isset($testimonials) && $testimonials->count() > 0)
            <section class="py-16">
                <div class="text-center mb-10">
                    <div class="text-xs uppercase tracking-[.15em] text-blue-600 font-bold">Pengalaman Pasien</div>
                    <h2 class="mt-4" style="font-family:'DM Sans',sans-serif;font-size:clamp(32px,4vw,52px);line-height:.95;letter-spacing:-.05em;font-weight:500;">
                        Apa kata mereka?
                    </h2>
                </div>

                <div class="relative" style="overflow:hidden;">
                    <div id="testimonialTrack" style="display:flex;transition:transform .6s ease;">
                        @foreach($testimonials as $index => $testimoniItem)
                        <div style="min-width:100%;padding:0 4px;">
                            <div class="about-block" style="text-align:left;">
                                <div class="about-content" style="max-width:100%;">
                                    <div style="display:flex;gap:3px;margin-bottom:16px;">
                                        @for($i = 1; $i <= 5; $i++)
                                            <span style="font-size:14px;color:{{ $i <= $testimoniItem->rating ? '#f59e0b' : '#e5e7eb' }};">â˜…</span>
                                        @endfor
                                    </div>
                                    <p style="font-size:16px;line-height:1.7;color:#111827;font-style:italic;margin-bottom:20px;">
                                        "{{ $testimoniItem->testimoni }}"
                                    </p>
                                    <div style="display:flex;align-items:center;gap:12px;">
                                        <div style="width:36px;height:36px;border-radius:50%;background:#2563eb;color:white;display:grid;place-items:center;font-weight:700;font-size:14px;">
                                            {{ strtoupper(substr($testimoniItem->character_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight:700;font-size:13px;color:#111827;">{{ $testimoniItem->character_name }}</div>
                                            <div style="font-size:10px;color:#667085;">Pasien Terverifikasi &nbsp;â€¢&nbsp; {{ $testimoniItem->created_at->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @if($testimonials->count() > 1)
                    <div style="display:flex;align-items:center;justify-content:center;gap:12px;margin-top:20px;">
                        <button id="prevBtn" style="width:36px;height:36px;border-radius:50%;background:#f3f4f6;border:1px solid #e5e7eb;cursor:pointer;font-size:14px;display:grid;place-items:center;">â€¹</button>
                        <div id="dotsContainer" style="display:flex;gap:6px;">
                            @foreach($testimonials as $index => $item)
                            <button class="testimonial-dot" data-index="{{ $index }}" style="width:8px;height:8px;border-radius:50%;background:#d1d5db;border:none;cursor:pointer;padding:0;transition:.2s;"></button>
                            @endforeach
                        </div>
                        <button id="nextBtn" style="width:36px;height:36px;border-radius:50%;background:#f3f4f6;border:1px solid #e5e7eb;cursor:pointer;font-size:14px;display:grid;place-items:center;">â€º</button>
                    </div>
                    @endif
                </div>
            </section>
            @endif

            <!-- CTA -->
            <section class="py-16 text-center">
                <div class="text-xs uppercase tracking-[.15em] text-blue-600 font-bold">Ready when you are</div>
                <h2 class="mt-4" style="font-family:'DM Sans',sans-serif;font-size:clamp(32px,4vw,52px);line-height:.95;letter-spacing:-.05em;font-weight:500;">
                    Butuh layanan medis?
                </h2>
                <p style="max-width:480px;margin:16px auto 0;font-size:13px;line-height:1.8;color:#667085;">
                    Akses layanan kesehatan iMe dengan mudah
                    dan dapatkan bantuan dari tenaga medis kami.
                </p>
                <div style="display:flex;justify-content:center;gap:12px;margin-top:24px;flex-wrap:wrap;">
                    <a href="#layanan" style="padding:12px 24px;border-radius:999px;background:#2563eb;color:white;font-size:12px;font-weight:700;text-decoration:none;transition:.2s;" onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
                        Lihat Layanan
                    </a>
                    <button onclick="showRegulationModal()" style="padding:12px 24px;border-radius:999px;background:white;border:1px solid #e5e7eb;color:#374151;font-size:12px;font-weight:700;cursor:pointer;transition:.2s;">
                        Regulasi Pengobatan
                    </button>
                </div>
            </section>

        </div>
    </main>

@endsection

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,300;1,9..40,400&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    /* ===================================================
       RESET & TOKENS
    =================================================== */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
        --navy:   #07527A;
        --blue:   #087EAE;
        --cyan:   #22C7E8;
        --ink:    #111827;
        --muted:  #667085;
        --border: #e5e7eb;
        --white:  #ffffff;
        --bg:     #f9fafb;
        --card:   #ffffff;
        --radius: 20px;
        --ff-display: 'DM Sans', sans-serif;
        --ff-body:    'Plus Jakarta Sans', sans-serif;
    }

    body { font-family: var(--ff-body); background: var(--bg); color: var(--ink); }

    /* ===================================================
       CUSTOM SCROLLBAR (MODAL)
    =================================================== */
    .custom-scrollbar::-webkit-scrollbar { width: 8px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: linear-gradient(135deg,#3b82f6,#1d4ed8); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: linear-gradient(135deg,#2563eb,#1e40af); }

    /* ===================================================
       MODAL ANIMATION
    =================================================== */
    @keyframes fadeInUp {
        from { opacity:0; transform:translateY(30px); }
        to   { opacity:1; transform:translateY(0); }
    }
    .animate-fade-in-up { animation: fadeInUp 0.5s ease-out; }

    /* ===================================================
       HERO
    =================================================== */
    .hero-wrapper {
        background: var(--navy);
        overflow: hidden;
    }
    .hero {
        max-width: 1280px;
        margin: 0 auto;
        padding: 80px 40px 0;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 64px;
        align-items: center;
        min-height: 100vh;
    }
    @media (max-width: 900px) {
        .hero { grid-template-columns: 1fr; min-height: auto; padding: 60px 24px 0; }
        .mockup-stage { display: none; }
    }

    /* hero copy */
    .hero-content { color: var(--white); }
    .hero-eyebrow {
        font-size: 11px;
        letter-spacing: .18em;
        text-transform: uppercase;
        color: var(--cyan);
        font-weight: 700;
        margin-bottom: 20px;
    }
    .hero-title {
        font-family: var(--ff-display);
        font-size: clamp(72px, 10vw, 140px);
        line-height: .9;
        letter-spacing: -.04em;
        font-weight: 500;
        margin-bottom: 24px;
    }
    .hero-subtitle {
        font-size: 15px;
        line-height: 1.8;
        color: rgba(255,255,255,.65);
        max-width: 360px;
        margin-bottom: 40px;
    }
    .hero-arrow {
        font-size: 28px;
        color: var(--cyan);
        animation: arrowBob 2s ease-in-out infinite;
    }
    @keyframes arrowBob {
        0%,100% { transform: translateY(0); }
        50%      { transform: translateY(8px); }
    }

    /* browser mockup */
    .mockup-stage {
        display: flex;
        align-items: flex-end;
        justify-content: center;
        height: 100%;
        padding-bottom: 0;
    }
    .browser {
        width: 100%;
        max-width: 560px;
        background: var(--white);
        border-radius: 16px 16px 0 0;
        overflow: hidden;
        box-shadow: 0 -24px 80px rgba(0,0,0,.4);
    }
    .browser-bar {
        background: #f3f4f6;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 6px;
        border-bottom: 1px solid var(--border);
    }
    .dot {
        width: 10px; height: 10px;
        border-radius: 50%;
        background: #e5e7eb;
    }
    .browser-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        min-height: 340px;
    }
    .browser-left {
        padding: 32px 24px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 12px;
    }
    .browser-small {
        font-size: 9px;
        letter-spacing: .15em;
        text-transform: uppercase;
        color: var(--muted);
        font-weight: 700;
    }
    .browser-heading {
        font-family: var(--ff-display);
        font-size: 22px;
        line-height: 1.15;
        letter-spacing: -.03em;
        font-weight: 500;
        color: var(--ink);
    }
    .browser-heading span { color: var(--blue); }
    .browser-description {
        font-size: 11px;
        line-height: 1.7;
        color: var(--muted);
    }
    .browser-button {
        display: inline-block;
        margin-top: 4px;
        padding: 8px 16px;
        border-radius: 999px;
        background: var(--navy);
        color: var(--white);
        font-size: 11px;
        font-weight: 700;
        text-decoration: none;
        width: fit-content;
        transition: background .2s;
    }
    .browser-button:hover { background: var(--blue); }
    .browser-right {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #dbeafe 0%, #e0f2fe 100%);
    }
    .doctor-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: top center;
    }
    .floating-label {
        position: absolute;
        background: white;
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 5px 10px;
        font-size: 10px;
        font-weight: 700;
        color: var(--ink);
        box-shadow: 0 4px 12px rgba(0,0,0,.08);
    }
    .label-1 { top: 20px;  left: 16px;  animation: float 3s ease-in-out infinite; }
    .label-2 { top: 20px;  right: 16px; animation: float 3.5s ease-in-out infinite .5s; }
    .label-3 { bottom: 20px; left: 16px; animation: float 4s ease-in-out infinite 1s; }
    @keyframes float {
        0%,100% { transform: translateY(0); }
        50%      { transform: translateY(-6px); }
    }

    /* ===================================================
       MAIN CONTENT
    =================================================== */
    .content-section { background: var(--bg); }
    .section-container {
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 40px;
    }
    @media (max-width: 640px) {
        .section-container { padding: 0 20px; }
    }

    /* intro */
    .intro {
        padding: 96px 0 64px;
        border-bottom: 1px solid var(--border);
        max-width: 680px;
    }
    .intro h2 {
        font-family: var(--ff-display);
        font-size: clamp(36px, 5vw, 64px);
        line-height: .95;
        letter-spacing: -.05em;
        font-weight: 500;
        margin-bottom: 20px;
    }
    .intro p {
        font-size: 14px;
        line-height: 1.8;
        color: var(--muted);
        max-width: 420px;
    }

    /* about block */
    .about-block {
        padding: 64px 0;
        border-bottom: 1px solid var(--border);
    }
    .about-content {
        max-width: 600px;
    }
    .about-content h3 {
        font-family: var(--ff-display);
        font-size: clamp(32px, 4vw, 52px);
        line-height: .95;
        letter-spacing: -.05em;
        font-weight: 500;
        margin-bottom: 16px;
    }
    .about-content p {
        font-size: 13px;
        line-height: 1.8;
        color: var(--muted);
        margin-bottom: 32px;
    }
    .stats {
        display: flex;
        gap: 40px;
        flex-wrap: wrap;
    }
    .stat strong {
        display: block;
        font-family: var(--ff-display);
        font-size: 36px;
        font-weight: 500;
        letter-spacing: -.04em;
        color: var(--ink);
    }
    .stat span {
        font-size: 12px;
        color: var(--muted);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .08em;
    }

    /* services */
    .services-section {
        padding: 64px 0;
        border-bottom: 1px solid var(--border);
    }
    .section-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 32px;
        margin-bottom: 48px;
        flex-wrap: wrap;
    }
    .section-header h3 {
        font-family: var(--ff-display);
        font-size: clamp(28px, 4vw, 44px);
        line-height: 1;
        letter-spacing: -.04em;
        font-weight: 500;
        margin-top: 8px;
    }
    .section-header > p {
        font-size: 13px;
        line-height: 1.8;
        color: var(--muted);
        max-width: 240px;
        margin-top: 32px;
    }
    .service-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 2px;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
        background: var(--border);
    }
    @media (max-width: 900px) {
        .service-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 480px) {
        .service-grid { grid-template-columns: 1fr; }
    }
    .service-card {
        background: var(--card);
        padding: 32px 24px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        transition: background .2s;
    }
    .service-card:hover { background: #f0f9ff; }
    .service-number {
        font-family: var(--ff-display);
        font-size: 11px;
        font-weight: 700;
        color: var(--muted);
        letter-spacing: .08em;
        margin-bottom: 8px;
    }
    .service-icon {
        font-size: 24px;
        color: var(--blue);
        margin-bottom: 8px;
    }
    .service-card h4 {
        font-size: 15px;
        font-weight: 700;
        letter-spacing: -.02em;
        color: var(--ink);
    }
    .service-card p {
        font-size: 12px;
        line-height: 1.6;
        color: var(--muted);
        flex: 1;
    }
    .service-link {
        font-size: 11px;
        font-weight: 700;
        color: var(--blue);
        text-decoration: none;
        letter-spacing: .02em;
        transition: color .2s;
    }
    .service-link:hover { color: var(--navy); }

    /* why us */
    .why-section {
        padding: 64px 0;
        border-bottom: 1px solid var(--border);
    }
    .why-block {
        background: var(--navy);
        border-radius: var(--radius);
        overflow: hidden;
        display: grid;
        grid-template-columns: 1fr 1.4fr;
        min-height: 500px;
    }
    @media (max-width: 900px) {
        .why-block { grid-template-columns: 1fr; }
        .why-image { min-height: 300px; }
    }
    .why-copy {
        padding: 64px 48px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 16px;
    }
    .why-copy h3 {
        font-family: var(--ff-display);
        font-size: clamp(40px, 5vw, 72px);
        line-height: .9;
        letter-spacing: -.05em;
        font-weight: 500;
        color: var(--white);
        margin-top: 12px;
    }
    .why-copy p {
        font-size: 13px;
        line-height: 1.8;
        color: rgba(255,255,255,.6);
        max-width: 280px;
    }
    .why-image {
        position: relative;
        overflow: hidden;
    }
    .why-image > img {
        width: 100%; height: 100%;
        object-fit: cover;
        opacity: .45;
    }

    /* floating phone */
    .phone {
        position: absolute;
        bottom: 32px;
        right: 32px;
        width: 148px;
        background: var(--white);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 24px 64px rgba(0,0,0,.3);
        animation: float 4s ease-in-out infinite;
    }
    .phone-screen { padding: 16px; }
    .phone-top {
        font-size: 8px;
        font-weight: 700;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: .1em;
        margin-bottom: 8px;
    }
    .phone-screen h4 {
        font-family: var(--ff-display);
        font-size: 20px;
        line-height: 1;
        letter-spacing: -.04em;
        font-weight: 500;
        color: var(--ink);
        margin-bottom: 10px;
    }
    .phone-screen > img {
        width: 100%;
        height: 64px;
        object-fit: cover;
        object-position: top;
        border-radius: 10px;
        margin-bottom: 10px;
    }
    .phone-button {
        display: block;
        background: var(--navy);
        color: var(--white);
        text-align: center;
        padding: 7px;
        border-radius: 8px;
        font-size: 9px;
        font-weight: 700;
        text-decoration: none;
        transition: background .2s;
    }
    .phone-button:hover { background: var(--blue); }

    /* schedule table */
    .schedule-table {
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
        background: var(--card);
        max-width: 800px;
        margin: 0 auto;
    }
    .notice-bar {
        background: #fefce8;
        border-bottom: 1px solid #fef08a;
        padding: 12px 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 12px;
        color: #713f12;
    }
    .schedule-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 24px;
        border-bottom: 1px solid var(--border);
        gap: 16px;
        flex-wrap: wrap;
    }
    .schedule-row:last-child { border-bottom: none; }
    .schedule-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .schedule-icon {
        width: 36px; height: 36px;
        border-radius: 10px;
        background: #dbeafe;
        color: var(--blue);
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }
    .schedule-name {
        font-size: 13px;
        font-weight: 700;
        color: var(--ink);
    }
    .schedule-desc {
        font-size: 11px;
        color: var(--muted);
        margin-top: 2px;
    }
    .schedule-time {
        font-size: 12px;
        font-weight: 600;
        color: var(--blue);
        white-space: nowrap;
    }
    .schedule-badge {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 10px;
        font-weight: 700;
        color: #16a34a;
        letter-spacing: .08em;
        background: #dcfce7;
        padding: 4px 10px;
        border-radius: 999px;
    }
    .badge-dot {
        width: 6px; height: 6px;
        border-radius: 50%;
        background: #16a34a;
        animation: pulse 1.5s ease-in-out infinite;
    }
    @keyframes pulse {
        0%,100% { opacity:1; transform:scale(1); }
        50%      { opacity:.5; transform:scale(.8); }
    }
</style>
@endpush

@push('scripts')
<script>
    // --- Regulation Modal ---
    function showRegulationModal() {
        const modal = document.getElementById('regulationModal');
        if (modal) { modal.style.display = 'flex'; document.body.style.overflow = 'hidden'; }
    }
    function closeRegulationModal() {
        const modal = document.getElementById('regulationModal');
        if (modal) { modal.style.display = 'none'; document.body.style.overflow = 'auto'; }
    }
    function handleModalClick(e) {
        if (e.target === document.getElementById('regulationModal')) closeRegulationModal();
    }

    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('regulationModal');
        if (modal) {
            modal.addEventListener('click', handleModalClick);
            document.addEventListener('keydown', e => { if (e.key === 'Escape') closeRegulationModal(); });
        }

        // --- Testimonial Carousel ---
        const track  = document.getElementById('testimonialTrack');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const dots   = document.querySelectorAll('.testimonial-dot');
        let idx = 0;

        if (!track) return;

        const total = track.children.length;
        if (total === 0) return;

        function go(n) {
            idx = (n + total) % total;
            track.style.transform = 'translateX(-' + (idx * 100) + '%)';
            dots.forEach((d, i) => {
                d.style.background  = i === idx ? '#2563eb' : '#d1d5db';
                d.style.width       = i === idx ? '20px'   : '8px';
            });
        }

        go(0);
        if (prevBtn) prevBtn.addEventListener('click', () => go(idx - 1));
        if (nextBtn) nextBtn.addEventListener('click', () => go(idx + 1));
        dots.forEach((d, i) => d.addEventListener('click', () => go(i)));

        setInterval(() => go(idx + 1), 6000);
    });
</script>
@endpush

