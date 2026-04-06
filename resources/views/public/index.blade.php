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
    <!-- Hero Section - Premium Redesign -->
    <div class="relative min-h-screen bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700 overflow-hidden">
        <!-- Modern Background Layers -->
        <div class="absolute inset-0">
            <!-- Base Gradient -->
            <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700"></div>

            <!-- Hero Image Background -->
            <div class="absolute inset-0 opacity-40">
                <img src="{{ asset('images/hero.png') }}" alt="Medical Background" class="w-full h-full object-cover object-[center_bottom]">
            </div>

            <!-- Blurred Medical Illustration Overlay -->
            <div class="absolute inset-0 opacity-20">
                <img src="{{ asset('images/hero.png') }}" alt="Medical Background"
                    class="w-full h-full object-cover blur-2xl scale-110 object-[center_bottom]">
            </div>

            <!-- Animated Gradient Orbs -->
            <div class="absolute top-0 left-1/4 w-[500px] h-[500px] bg-cyan-500/20 rounded-full blur-3xl animate-pulse">
            </div>
            <div class="absolute bottom-0 right-1/4 w-[500px] h-[500px] bg-sky-500/20 rounded-full blur-3xl animate-pulse"
                style="animation-delay: 2s;"></div>
            <div
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-sky-500/15 rounded-full blur-3xl">
            </div>

            <!-- Grid Pattern Overlay -->
            <div class="absolute inset-0 opacity-5"
                style="background-image: linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 60px 60px;">
            </div>
        </div>

        <!-- Hero Content -->
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-24 lg:py-32">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">

                <!-- Left Column: Main Content -->
                <div class="text-center lg:text-left space-y-8 animate-fade-in-left">

                    <!-- Logo Section with Enhanced Effects -->
                    <div class="flex justify-center lg:justify-start items-center gap-5 mb-8">
                        <div class="relative group">
                            <div
                                class="absolute inset-0 bg-cyan-500/30 rounded-full blur-2xl group-hover:opacity-75 transition-opacity duration-300">
                            </div>
                            <img src="{{ asset('images/logoime.webp') }}" alt="MOTIONLIFE"
                                class="relative h-20 w-20 sm:h-24 sm:w-24 lg:h-28 lg:w-28 object-contain drop-shadow-2xl animate-float">
                        </div>
                        <div class="relative group">
                            <div
                                class="absolute inset-0 bg-blue-500/30 rounded-full blur-2xl group-hover:opacity-75 transition-opacity duration-300">
                            </div>
                            <div
                                class="relative h-20 w-20 sm:h-24 sm:w-24 lg:h-28 lg:w-28 flex items-center justify-center bg-white/15 backdrop-blur-xl rounded-3xl border-2 border-white/30 shadow-2xl animate-float hover:bg-white/25 transition-all duration-300">
                                <img src="{{ asset('images/motionlife-logo.png') }}" alt="EMS"
                                    class="h-16 w-16 sm:h-20 sm:w-20 lg:h-24 lg:w-24 object-contain">
                            </div>
                        </div>
                    </div>

                    <!-- Title Section with Enhanced Typography -->
                    <div class="space-y-5">
                        <h1
                            class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl xl:text-9xl font-black text-transparent bg-clip-text bg-gradient-to-r from-sky-300 via-cyan-300 to-blue-300 leading-[0.9] tracking-tight drop-shadow-2xl">
                            iMe
                        </h1>
                        <h2
                            class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-sky-100 leading-tight drop-shadow-lg">
                            Portal Medis Terpadu
                        </h2>
                    </div>

                    <!-- Description with Better Readability -->
                    <p
                        class="text-lg sm:text-xl lg:text-2xl text-sky-200/95 max-w-2xl mx-auto lg:mx-0 leading-relaxed font-light">
                        Dapatkan layanan medis <span class="font-semibold text-cyan-300">in-character</span> yang realistis,
                        profesional, dan cepat untuk segala kebutuhan kesehatan Anda.
                    </p>

                    <!-- CTA Buttons with Premium 2x2 Grid Layout -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 max-w-2xl mx-auto lg:mx-0 pt-6">
                        <!-- Primary Button (Layanan) -->
                        <a href="#services"
                            class="group relative inline-flex items-center justify-center gap-3 bg-gradient-to-r from-sky-600 via-cyan-600 to-blue-600 text-white font-bold text-base px-6 py-4 rounded-xl shadow-2xl hover:shadow-cyan-500/50 hover:from-sky-500 hover:via-cyan-500 hover:to-blue-500 transition-all duration-300 transform hover:scale-[1.02] overflow-hidden">
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/25 to-white/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700">
                            </div>
                            <i class="fas fa-stethoscope relative z-10 text-lg"></i>
                            <span class="relative z-10">Lihat Layanan</span>
                        </a>

                        <!-- Jadwal Praktek Button -->
                        <a href="{{ route('public.doctor-schedule') }}"
                            class="relative z-10 group inline-flex items-center justify-center gap-2 bg-white/10 backdrop-blur-xl border-2 border-white/25 text-white font-semibold text-base px-6 py-4 rounded-xl shadow-xl hover:bg-white/20 hover:border-white/40 hover:shadow-2xl transition-all duration-300 transform hover:scale-[1.02]">
                            <i class="fas fa-calendar-check text-lg text-cyan-400"></i>
                            <span>Jadwal Praktek</span>
                        </a>

                        <!-- Regulasi Button -->
                        <button onclick="showRegulationModal()" type="button"
                            class="relative z-10 group inline-flex items-center justify-center gap-2 bg-white/10 backdrop-blur-xl border-2 border-white/25 text-white font-semibold text-base px-6 py-4 rounded-xl shadow-xl hover:bg-white/20 hover:border-white/40 hover:shadow-2xl transition-all duration-300 transform hover:scale-[1.02] cursor-pointer">
                            <i class="fas fa-file-alt text-lg text-amber-400"></i>
                            <span>Lihat Regulasi</span>
                        </button>

                        <!-- Struktural EMS Button -->
                        <a href="{{ route('public.struktural-ems') }}"
                            class="relative z-10 group inline-flex items-center justify-center gap-2 bg-white/10 backdrop-blur-xl border-2 border-white/25 text-white font-semibold text-base px-6 py-4 rounded-xl shadow-xl hover:bg-white/20 hover:border-white/40 hover:shadow-2xl transition-all duration-300 transform hover:scale-[1.02]">
                            <i class="fas fa-sitemap text-lg text-blue-400"></i>
                            <span>Struktural EMS</span>
                        </a>
                    </div>
                </div>

                <!-- Right Column: Staff On Duty Card with Premium Glassmorphism -->
                <div class="animate-fade-in-right">
                    <div
                        class="bg-white/10 backdrop-blur-2xl rounded-3xl border-2 border-white/25 shadow-2xl p-8 sm:p-10 max-w-lg mx-auto lg:mx-0 relative overflow-hidden group hover:shadow-cyan-500/30 hover:border-white/40 transition-all duration-500">

                        <!-- Animated Background Glow -->
                        <div
                            class="absolute inset-0 bg-gradient-to-br from-cyan-500/10 via-blue-500/10 to-indigo-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                        </div>

                        <!-- Subtle Animated Orbs -->
                        <div class="absolute top-6 right-6 w-24 h-24 bg-cyan-400/20 rounded-full blur-2xl animate-pulse">
                        </div>
                        <div
                            class="absolute bottom-6 left-6 w-20 h-20 bg-blue-400/20 rounded-full blur-2xl animate-pulse delay-1000">
                        </div>

                        <!-- Shine Effect -->
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000">
                        </div>

                        <!-- Header -->
                        <div class="relative z-10 text-center mb-8">
                            <div class="relative inline-block mb-5">
                                <div
                                    class="absolute inset-0 bg-gradient-to-br from-sky-400 to-cyan-400 rounded-2xl blur-xl opacity-60 group-hover:opacity-80 transition-opacity duration-300">
                                </div>
                                <div
                                    class="relative w-20 h-20 bg-gradient-to-br from-sky-500 via-cyan-500 to-blue-500 rounded-2xl flex items-center justify-center shadow-2xl group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                                    <i class="fas fa-user-md text-white text-3xl"></i>
                                </div>
                            </div>
                            <h3 class="text-2xl sm:text-3xl font-black text-white mb-2 drop-shadow-lg">Staff On Duty</h3>
                            <p class="text-sm text-sky-200/90 font-medium">Tim medis yang siap melayani Anda</p>
                        </div>

                        <!-- Stats Cards with Enhanced Design -->
                        <div class="relative z-10 space-y-5">
                            <!-- Alta Hospital -->
                            <div
                                class="bg-white/15 backdrop-blur-md rounded-2xl p-6 border-2 border-white/20 hover:border-cyan-400/50 hover:bg-white/20 transition-all duration-300 group/item relative overflow-hidden">
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-cyan-500/0 via-cyan-500/10 to-cyan-500/0 translate-x-[-100%] group-hover/item:translate-x-[100%] transition-transform duration-1000">
                                </div>
                                <div class="relative z-10">
                                    <div class="flex items-center gap-4 mb-4">
                                        <div class="relative">
                                            <div
                                                class="absolute inset-0 bg-cyan-400/40 rounded-xl blur-lg group-hover/item:opacity-75 transition-opacity duration-300">
                                            </div>
                                            <div
                                                class="relative w-16 h-16 bg-gradient-to-br from-blue-500 via-cyan-500 to-sky-500 rounded-xl flex items-center justify-center shadow-xl group-hover/item:scale-110 group-hover/item:rotate-6 transition-all duration-300">
                                                <i class="fas fa-hospital text-white text-xl"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-white uppercase tracking-wide drop-shadow-md">
                                                Alta Hospital</p>
                                            <p class="text-xs text-sky-300/80 mt-1 font-medium">Emergency Medical Services
                                            </p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div
                                            class="bg-gradient-to-br from-green-500/20 to-emerald-500/20 backdrop-blur-sm rounded-xl p-4 border border-green-400/30">
                                            <div class="flex items-center gap-2 mb-2">
                                                <i class="fas fa-briefcase-medical text-green-300 text-sm"></i>
                                                <p class="text-xs font-bold text-green-200 uppercase">Kerja</p>
                                            </div>
                                            <p class="text-4xl font-black text-green-300 leading-none drop-shadow-lg">
                                                {{ $staffStatusStats['ems_working'] ?? 0 }}
                                            </p>
                                            <p class="text-xs text-green-200/80 mt-1">Staff</p>
                                        </div>
                                        <div
                                            class="bg-gradient-to-br from-purple-500/20 to-pink-500/20 backdrop-blur-sm rounded-xl p-4 border border-purple-400/30">
                                            <div class="flex items-center gap-2 mb-2">
                                                <i class="fas fa-users text-purple-300 text-sm"></i>
                                                <p class="text-xs font-bold text-purple-200 uppercase">Meeting</p>
                                            </div>
                                            <p class="text-4xl font-black text-purple-300 leading-none drop-shadow-lg">
                                                {{ $staffStatusStats['ems_meeting'] ?? 0 }}
                                            </p>
                                            <p class="text-xs text-purple-200/80 mt-1">Staff</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Roxwood Hospital -->
                            <div
                                class="bg-white/15 backdrop-blur-md rounded-2xl p-6 border-2 border-white/20 hover:border-amber-400/50 hover:bg-white/20 transition-all duration-300 group/item relative overflow-hidden">
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-amber-500/0 via-amber-500/10 to-amber-500/0 translate-x-[-100%] group-hover/item:translate-x-[100%] transition-transform duration-1000">
                                </div>
                                <div class="relative z-10">
                                    <div class="flex items-center gap-3 mb-3">
                                        <div class="relative">
                                            <div
                                                class="absolute inset-0 bg-amber-400/40 rounded-xl blur-lg group-hover/item:opacity-75 transition-opacity duration-300">
                                            </div>
                                            <div
                                                class="relative w-12 h-12 bg-gradient-to-br from-amber-500 via-orange-500 to-yellow-500 rounded-xl flex items-center justify-center shadow-xl group-hover/item:scale-110 group-hover/item:rotate-6 transition-all duration-300">
                                                <i class="fas fa-hospital text-white text-base"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-white uppercase tracking-wide drop-shadow-md">
                                                Roxwood Hospital</p>
                                            <p class="text-[10px] text-sky-300/80 font-medium"> Medical Care</p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div
                                            class="bg-gradient-to-br from-green-500/20 to-emerald-500/20 backdrop-blur-sm rounded-lg p-3 border border-green-400/30">
                                            <div class="flex items-center gap-1.5 mb-1">
                                                <i class="fas fa-briefcase-medical text-green-300 text-xs"></i>
                                                <p class="text-[10px] font-bold text-green-200 uppercase">Kerja</p>
                                            </div>
                                            <p class="text-3xl font-black text-green-300 leading-none drop-shadow-lg">
                                                {{ $staffStatusStats['roxwood_working'] ?? 0 }}
                                            </p>
                                            <p class="text-[10px] text-green-200/80 mt-0.5">Staff</p>
                                        </div>
                                        <div
                                            class="bg-gradient-to-br from-purple-500/20 to-pink-500/20 backdrop-blur-sm rounded-lg p-3 border border-purple-400/30">
                                            <div class="flex items-center gap-1.5 mb-1">
                                                <i class="fas fa-users text-purple-300 text-xs"></i>
                                                <p class="text-[10px] font-bold text-purple-200 uppercase">Meeting</p>
                                            </div>
                                            <p class="text-3xl font-black text-purple-300 leading-none drop-shadow-lg">
                                                {{ $staffStatusStats['roxwood_meeting'] ?? 0 }}
                                            </p>
                                            <p class="text-[10px] text-purple-200/80 mt-0.5">Staff</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="relative z-10 mt-8 pt-6 border-t border-white/20">
                            <div class="flex items-center justify-center gap-2 text-sky-300/80">
                                <i class="fas fa-info-circle text-sm"></i>
                                <p class="text-xs font-medium">Data diperbarui secara real-time</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Section - Dark Theme -->
    <div class="relative py-20 sm:py-24 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700 overflow-hidden">
        <!-- Background Effects -->
        <div class="absolute inset-0">
            <div class="absolute top-0 right-1/4 w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-1/4 w-96 h-96 bg-sky-500/10 rounded-full blur-3xl"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-black text-white mb-4 drop-shadow-lg">Keunggulan Kami</h2>
                <p class="text-sky-200/90 text-lg sm:text-xl font-medium">Bukti kualitas pelayanan medis terbaik</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 md:gap-8">
                <div class="group animate-fade-in-up bg-white/10 backdrop-blur-xl rounded-2xl border-2 border-white/20 p-6 sm:p-8 hover:bg-white/15 hover:border-white/30 transition-all duration-300 transform hover:scale-105 flex flex-col items-center justify-center text-center h-full"
                    style="animation-delay: 0.1s;">
                    <div
                        class="relative w-20 h-20 bg-gradient-to-br from-sky-400 to-cyan-400 rounded-2xl flex items-center justify-center mx-auto mb-5 shadow-xl group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                    <h4 class="text-4xl font-black text-cyan-300 mb-3 drop-shadow-lg leading-none">
                        {{ number_format($stats['total_forms']) }}+
                    </h4>
                    <p class="text-sky-200 font-semibold text-base">Pasien Puas</p>
                </div>
                <div class="group animate-fade-in-up bg-white/10 backdrop-blur-xl rounded-2xl border-2 border-white/20 p-6 sm:p-8 hover:bg-white/15 hover:border-white/30 transition-all duration-300 transform hover:scale-105 flex flex-col items-center justify-center text-center h-full"
                    style="animation-delay: 0.2s;">
                    <div
                        class="relative w-20 h-20 bg-gradient-to-br from-emerald-400 to-teal-400 rounded-2xl flex items-center justify-center mx-auto mb-5 shadow-xl group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-user-md text-white text-2xl"></i>
                    </div>
                    <h4 class="text-4xl font-black text-emerald-300 mb-3 drop-shadow-lg leading-none">
                        {{ $stats['total_staff'] }}+
                    </h4>
                    <p class="text-sky-200 font-semibold text-base">Dokter Ahli</p>
                </div>
                <div class="group animate-fade-in-up bg-white/10 backdrop-blur-xl rounded-2xl border-2 border-white/20 p-6 sm:p-8 hover:bg-white/15 hover:border-white/30 transition-all duration-300 transform hover:scale-105 flex flex-col items-center justify-center text-center h-full"
                    style="animation-delay: 0.3s;">
                    <div
                        class="relative w-20 h-20 bg-gradient-to-br from-cyan-400 to-blue-400 rounded-2xl flex items-center justify-center mx-auto mb-5 shadow-xl group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-check-circle text-white text-2xl"></i>
                    </div>
                    <h4 class="text-4xl font-black text-cyan-300 mb-3 drop-shadow-lg leading-none">98%</h4>
                    <p class="text-sky-200 font-semibold text-base">Tingkat Keberhasilan</p>
                </div>
                <div class="group animate-fade-in-up bg-white/10 backdrop-blur-xl rounded-2xl border-2 border-white/20 p-6 sm:p-8 hover:bg-white/15 hover:border-white/30 transition-all duration-300 transform hover:scale-105 flex flex-col items-center justify-center text-center h-full"
                    style="animation-delay: 0.4s;">
                    <div
                        class="relative w-20 h-20 bg-gradient-to-br from-red-400 to-pink-400 rounded-2xl flex items-center justify-center mx-auto mb-5 shadow-xl group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-heartbeat text-white text-2xl"></i>
                    </div>
                    <h4 class="text-4xl font-black text-red-300 mb-3 drop-shadow-lg leading-none">24/7</h4>
                    <p class="text-sky-200 font-semibold text-base">Gawat Darurat</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Hospital Service Hours Section - Sky Blue Theme -->
    <div class="relative py-16 sm:py-20 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700 overflow-hidden">
        <!-- Background Effects -->
        <div class="absolute inset-0">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-sky-500/10 rounded-full blur-3xl"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header with Clock Icon -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center gap-3 mb-6">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-amber-400 to-orange-500 rounded-2xl flex items-center justify-center shadow-2xl animate-pulse">
                        <i class="fas fa-clock text-white text-3xl"></i>
                    </div>
                    <h2 class="text-3xl sm:text-4xl md:text-5xl font-black text-white drop-shadow-lg">
                        <i class="fas fa-clock mr-3 text-cyan-300"></i>
                        LAYANAN RUMAH SAKIT
                        <i class="fas fa-clock ml-3 text-cyan-300"></i>
                    </h2>
                </div>
                <div
                    class="max-w-4xl mx-auto bg-red-500/20 backdrop-blur-md border-2 border-red-400/40 rounded-2xl p-6 mb-8">
                    <p class="text-xl sm:text-2xl font-bold text-white mb-3">
                        <i class="fas fa-exclamation-triangle mr-2 text-yellow-300 animate-pulse"></i>
                        MOHON DIBACA DENGAN SAKSAMA
                    </p>
                    <p class="text-base sm:text-lg text-amber-50 leading-relaxed">
                        Pelayanan rumah sakit akan dilayani <span class="font-bold text-yellow-300">APABILA ADA TENAGA
                            MEDIS</span> yang bersangkutan,
                        <span class="font-bold text-yellow-300">BERSEDIA</span>, dan
                        <span class="font-bold text-yellow-300">AVAILABLE (ON DUTY)</span>
                    </p>
                </div>
            </div>

            <!-- Service Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
                <!-- OPLAS Card -->
                <div
                    class="group bg-white/10 backdrop-blur-xl rounded-3xl border-2 border-white/20 p-8 hover:bg-white/15 hover:border-amber-400/50 transition-all duration-300 transform hover:scale-105 hover:shadow-2xl hover:shadow-amber-500/30">
                    <div class="text-center mb-6">
                        <div
                            class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl shadow-xl mb-4 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-user-md text-white text-3xl"></i>
                        </div>
                        <h3 class="text-2xl font-black text-white mb-2">JAM PELAYANAN OPLAS</h3>
                        <div class="w-16 h-1 bg-gradient-to-r from-purple-400 to-pink-400 mx-auto rounded-full"></div>
                    </div>
                    <div class="space-y-4">
                        <div
                            class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20 hover:bg-white/20 transition-all">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-cyan-300 font-bold text-lg">Shift 1</span>
                                <span
                                    class="text-xs bg-purple-500/30 text-purple-200 px-3 py-1 rounded-full font-semibold">Siang</span>
                            </div>
                            <p class="text-white text-xl font-black">13:00 - 16:00 WIB</p>
                        </div>
                        <div
                            class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20 hover:bg-white/20 transition-all">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-cyan-300 font-bold text-lg">Shift 2</span>
                                <span
                                    class="text-xs bg-indigo-500/30 text-indigo-200 px-3 py-1 rounded-full font-semibold">Malam</span>
                            </div>
                            <p class="text-white text-xl font-black">20:00 - 22:00 WIB</p>
                        </div>
                    </div>
                </div>

                <!-- SURAT-SURATAN Card -->
                <div
                    class="group bg-white/10 backdrop-blur-xl rounded-3xl border-2 border-white/20 p-8 hover:bg-white/15 hover:border-amber-400/50 transition-all duration-300 transform hover:scale-105 hover:shadow-2xl hover:shadow-amber-500/30">
                    <div class="text-center mb-6">
                        <div
                            class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl shadow-xl mb-4 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-file-alt text-white text-3xl"></i>
                        </div>
                        <h3 class="text-2xl font-black text-white mb-2">JAM PELAYANAN SURAT-SURATAN</h3>
                        <div class="w-16 h-1 bg-gradient-to-r from-blue-400 to-cyan-400 mx-auto rounded-full"></div>
                    </div>
                    <div class="space-y-4">
                        <div
                            class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20 hover:bg-white/20 transition-all">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-cyan-300 font-bold text-lg">Shift 1</span>
                                <span
                                    class="text-xs bg-blue-500/30 text-blue-200 px-3 py-1 rounded-full font-semibold">Siang</span>
                            </div>
                            <p class="text-white text-xl font-black">13:00 - 17:00 WIB</p>
                        </div>
                        <div
                            class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20 hover:bg-white/20 transition-all">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-cyan-300 font-bold text-lg">Shift 2</span>
                                <span
                                    class="text-xs bg-cyan-500/30 text-cyan-200 px-3 py-1 rounded-full font-semibold">Malam</span>
                            </div>
                            <p class="text-white text-xl font-black">19:00 - 22:00 WIB</p>
                        </div>
                    </div>
                </div>

                <!-- FARMASI Card -->
                <div
                    class="group bg-white/10 backdrop-blur-xl rounded-3xl border-2 border-white/20 p-8 hover:bg-white/15 hover:border-amber-400/50 transition-all duration-300 transform hover:scale-105 hover:shadow-2xl hover:shadow-amber-500/30">
                    <div class="text-center mb-6">
                        <div
                            class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl shadow-xl mb-4 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-pills text-white text-3xl"></i>
                        </div>
                        <h3 class="text-2xl font-black text-white mb-2">JAM PELAYANAN FARMASI</h3>
                        <div class="w-16 h-1 bg-gradient-to-r from-green-400 to-emerald-400 mx-auto rounded-full"></div>
                    </div>
                    <div
                        class="bg-gradient-to-br from-green-500/20 to-emerald-500/20 backdrop-blur-sm rounded-2xl p-6 border-2 border-green-400/30 relative overflow-hidden">
                        <!-- Animated glow -->
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-green-400/0 via-green-400/20 to-green-400/0 animate-pulse">
                        </div>

                        <div class="relative z-10 text-center">
                            <div class="flex items-center justify-center gap-3 mb-4">
                                <i class="fas fa-check-circle text-green-300 text-4xl animate-bounce"></i>
                                <h4 class="text-3xl font-black text-white">BUKA 24 JAM</h4>
                            </div>
                            <div class="space-y-2">
                                <p class="text-green-100 text-base font-semibold leading-relaxed">
                                    Farmasi membuka layanan
                                </p>
                                <p class="text-white text-xl font-black">
                                    24 JAM NON-STOP
                                </p>
                                <div class="mt-4 pt-4 border-t border-green-400/30">
                                    <p class="text-sm text-green-200/90 leading-relaxed">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        Apabila ada tenaga medis yang bersedia menjaga secara bergantian
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Services Section - Dark Theme -->
    <div class="relative py-20 sm:py-24 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700 overflow-hidden"
        id="services">
        <!-- Background Effects -->
        <div class="absolute inset-0">
            <div class="absolute top-1/4 left-0 w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-1/4 right-0 w-96 h-96 bg-sky-500/10 rounded-full blur-3xl"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-black text-white mb-6 drop-shadow-lg">Layanan
                    Medis Kami</h2>
                <p class="text-lg sm:text-xl text-sky-200/90 max-w-3xl mx-auto leading-relaxed font-medium">Menyediakan
                    perawatan komprehensif untuk semua kebutuhan kesehatan Anda dengan standar profesional tertinggi.</p>
            </div>

            <!-- Service Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 w-full">
                <div class="group relative bg-white/10 backdrop-blur-xl rounded-2xl border-2 border-white/20 p-8 hover:bg-white/15 hover:border-white/30 transition-all duration-300 transform hover:-translate-y-2 animate-fade-in-up shadow-xl hover:shadow-2xl"
                    style="animation-delay: 0.1s;">
                    <div class="text-center mb-6">
                        <div class="relative inline-block mb-4">
                            <div
                                class="absolute inset-0 bg-sky-400/40 rounded-2xl blur-xl opacity-50 group-hover:opacity-75 transition-opacity duration-300">
                            </div>
                            <div
                                class="relative w-20 h-20 bg-gradient-to-br from-sky-500 to-cyan-500 rounded-2xl flex items-center justify-center mx-auto shadow-xl group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-stethoscope text-white text-2xl"></i>
                            </div>
                        </div>
                        <h4 class="text-2xl font-black text-white mb-3 drop-shadow-md">Konsultasi Medis</h4>
                        <p class="text-sky-300 font-semibold text-sm sm:text-base">Pemeriksaan umum dan diagnosis</p>
                    </div>
                    <p class="text-sky-200/90 mb-6 text-center text-sm sm:text-base leading-relaxed">Konsultasi menyeluruh
                        dengan dokter berpengalaman untuk mendiagnosis dan menangani keluhan kesehatan Anda.</p>
                    <div class="space-y-3">
                        <a href="{{ route('public.cek-kesehatan') }}"
                            class="block w-full bg-gradient-to-r from-sky-600 to-cyan-600 text-white font-bold text-base py-3.5 px-6 rounded-xl shadow-lg hover:shadow-xl hover:from-sky-500 hover:to-cyan-500 transition-all duration-300 transform hover:scale-[1.02] text-center">
                            <i class="fas fa-file-medical mr-2"></i>Form Surat Kesehatan
                        </a>
                        <a href="{{ route('public.form', 'janji_temu') }}"
                            class="block w-full bg-white/10 backdrop-blur-md border-2 border-white/30 text-white font-bold text-base py-3.5 px-6 rounded-xl hover:bg-white/20 hover:border-white/40 transition-all duration-300 transform hover:scale-[1.02] text-center">
                            <i class="fas fa-calendar-check mr-2"></i>Form Janji Temu
                        </a>
                    </div>
                </div>

                <div class="group relative bg-white/10 backdrop-blur-xl rounded-2xl border-2 border-white/20 p-8 hover:bg-white/15 hover:border-white/30 transition-all duration-300 transform hover:-translate-y-2 animate-fade-in-up shadow-xl hover:shadow-2xl"
                    style="animation-delay: 0.2s;">
                    <div class="text-center mb-6">
                        <div class="relative inline-block mb-4">
                            <div
                                class="absolute inset-0 bg-emerald-400/40 rounded-2xl blur-xl opacity-50 group-hover:opacity-75 transition-opacity duration-300">
                            </div>
                            <div
                                class="relative w-20 h-20 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-2xl flex items-center justify-center mx-auto shadow-xl group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-cut text-white text-2xl"></i>
                            </div>
                        </div>
                        <h4 class="text-2xl font-black text-white mb-3 drop-shadow-md">Operasi Plastik</h4>
                        <p class="text-emerald-300 font-semibold text-sm sm:text-base">Layanan estetika profesional</p>
                    </div>
                    <p class="text-sky-200/90 mb-6 text-center text-sm sm:text-base leading-relaxed">Prosedur bedah kosmetik
                        yang aman dan dilakukan oleh ahli bedah plastik bersertifikat untuk meningkatkan penampilan.</p>
                    <a href="{{ route('public.operasi-plastik') }}"
                        class="block w-full bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold text-base py-3.5 px-6 rounded-xl shadow-lg hover:shadow-xl hover:from-emerald-500 hover:to-teal-500 transition-all duration-300 transform hover:scale-[1.02] text-center">
                        <i class="fas fa-user-md mr-2"></i>Form Operasi Plastik
                    </a>
                </div>

                <div class="group relative bg-white/10 backdrop-blur-xl rounded-2xl border-2 border-white/20 p-8 hover:bg-white/15 hover:border-white/30 transition-all duration-300 transform hover:-translate-y-2 animate-fade-in-up shadow-xl hover:shadow-2xl"
                    style="animation-delay: 0.3s;">
                    <div class="text-center mb-6">
                        <div class="relative inline-block mb-4">
                            <div
                                class="absolute inset-0 bg-cyan-400/40 rounded-2xl blur-xl opacity-50 group-hover:opacity-75 transition-opacity duration-300">
                            </div>
                            <div
                                class="relative w-20 h-20 bg-gradient-to-br from-cyan-500 to-blue-500 rounded-2xl flex items-center justify-center mx-auto shadow-xl group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-brain text-white text-2xl"></i>
                            </div>
                        </div>
                        <h4 class="text-2xl font-black text-white mb-3 drop-shadow-md">Konsultasi Psikologi</h4>
                        <p class="text-cyan-300 font-semibold text-sm sm:text-base">Dukungan kesehatan mental</p>
                    </div>
                    <p class="text-sky-200/90 mb-6 text-center text-sm sm:text-base leading-relaxed">Sesi konseling dan
                        terapi bersama psikolog profesional untuk membantu Anda mengatasi masalah kesehatan mental.</p>
                    <div class="space-y-3">
                        <a href="{{ route('public.surat-psikolog') }}"
                            class="block w-full bg-gradient-to-r from-cyan-600 to-blue-600 text-white font-bold text-base py-3.5 px-6 rounded-xl shadow-lg hover:shadow-xl hover:from-cyan-500 hover:to-blue-500 transition-all duration-300 transform hover:scale-[1.02] text-center">
                            <i class="fas fa-clipboard-check mr-2"></i>Formulir Psikologi
                        </a>
                    </div>
                </div>

                <!-- Pendaftaran Karakter Kill Card -->
                <div class="group relative bg-white/10 backdrop-blur-xl rounded-2xl border-2 border-white/20 p-8 hover:bg-white/15 hover:border-white/30 transition-all duration-300 transform hover:-translate-y-2 animate-fade-in-up shadow-xl hover:shadow-2xl"
                    style="animation-delay: 0.4s;">
                    <div class="text-center mb-6">
                        <div class="relative inline-block mb-4">
                            <div
                                class="absolute inset-0 bg-red-400/40 rounded-2xl blur-xl opacity-50 group-hover:opacity-75 transition-opacity duration-300">
                            </div>
                            <div
                                class="relative w-20 h-20 bg-gradient-to-br from-red-500 to-pink-500 rounded-2xl flex items-center justify-center mx-auto shadow-xl group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-exclamation-triangle text-white text-2xl"></i>
                            </div>
                        </div>
                        <h4 class="text-2xl font-black text-white mb-3 drop-shadow-md">Karakter Kill</h4>
                        <p class="text-red-300 font-semibold text-sm sm:text-base">Layanan medis khusus</p>
                    </div>
                    <p class="text-sky-200/90 mb-6 text-center text-sm sm:text-base leading-relaxed">Daftarkan karakter Anda
                        untuk layanan medis khusus dan perawatan intensif dalam skenario kritis dengan tim medis
                        berpengalaman.</p>
                    <a href="{{ route('public.pendaftaran-karakter') }}"
                        class="block w-full bg-gradient-to-r from-red-600 to-pink-600 text-white font-bold text-base py-3.5 px-6 rounded-xl shadow-lg hover:shadow-xl hover:from-red-500 hover:to-pink-500 transition-all duration-300 transform hover:scale-[1.02] text-center">
                        <i class="fas fa-user-plus mr-2"></i>Daftar Sekarang
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Testimonial Section - Premium Dark Theme -->
    <div class="relative py-24 sm:py-32 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700 overflow-hidden">
        <!-- Background Effects -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-0 left-1/4 w-[500px] h-[500px] bg-cyan-500/20 rounded-full blur-[100px] animate-pulse">
            </div>
            <div class="absolute bottom-0 right-1/4 w-[500px] h-[500px] bg-sky-500/20 rounded-full blur-[100px]"
                style="animation-delay: 2s;"></div>
            <div
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-10 brightness-100 contrast-150">
            </div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-20 animate-fade-in-up">
                <div
                    class="inline-flex items-center justify-center p-1 rounded-full bg-gradient-to-r from-sky-500/20 to-cyan-500/20 border border-sky-500/30 backdrop-blur-md mb-6 shadow-[0_0_15px_rgba(14,165,233,0.3)]">
                    <span class="px-5 py-2 text-sm font-bold text-sky-300 tracking-wide uppercase flex items-center gap-2">
                        <i class="fas fa-heart text-sky-400 animate-pulse"></i> Testimonials
                    </span>
                </div>

                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-white mb-6 drop-shadow-2xl tracking-tight leading-tight"
                    style="text-shadow: 0 4px 20px rgba(0,0,0,0.5);">
                    Apa Kata <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-sky-400 via-cyan-300 to-blue-400 filter drop-shadow-[0_0_10px_rgba(14,165,233,0.5)]">Pasien
                        Kami</span>
                </h2>

                <p class="text-lg sm:text-xl text-sky-100/90 max-w-2xl mx-auto leading-relaxed font-light mb-8">
                    Kami bangga dapat memberikan pelayanan terbaik bagi komunitas dengan standar profesional tertinggi.
                </p>

                <!-- New Statistics Badge -->
                <div
                    class="inline-flex items-center gap-3 bg-white/5 backdrop-blur-md border border-white/10 rounded-full px-6 py-3 shadow-lg hover:bg-white/10 transition-colors cursor-default group">
                    <div class="flex items-center gap-1">
                        <i
                            class="fas fa-star text-amber-400 text-lg drop-shadow-[0_0_5px_rgba(251,191,36,0.5)] group-hover:scale-110 transition-transform"></i>
                        <span class="text-2xl font-bold text-white ml-1">4.9</span>
                    </div>
                    <div class="h-8 w-px bg-white/20"></div>
                    <div class="text-left">
                        <div class="text-[10px] text-sky-200 uppercase tracking-wider font-semibold">Rating Rata-rata</div>
                        <div class="text-sm font-bold text-white">dari 1.200+ Ulasan Asli</div>
                    </div>
                </div>
            </div>

            @if(isset($testimonials) && $testimonials->count() > 0)
                <!-- Single Testimonial Carousel -->
                <div class="relative max-w-5xl mx-auto px-4">
                    <!-- Testimonial Card Container -->
                    <div class="overflow-hidden rounded-3xl">
                        <div id="testimonialTrack" class="flex transition-transform duration-700 ease-in-out">
                            @foreach($testimonials as $index => $testimoniItem)
                                <div class="w-full flex-shrink-0">
                                    <div
                                        class="bg-white/10 backdrop-blur-xl border-2 border-white/20 rounded-3xl p-8 md:p-12 relative overflow-hidden">
                                        <!-- Gradient overlay matching landing page -->
                                        <div
                                            class="absolute inset-0 bg-gradient-to-br from-sky-500/10 via-cyan-500/5 to-transparent pointer-events-none">
                                        </div>

                                        <!-- Quote Icon -->
                                        <div class="absolute top-8 right-8 text-sky-400/20">
                                            <i class="fas fa-quote-right text-7xl"></i>
                                        </div>

                                        <!-- Content -->
                                        <div class="relative z-10">
                                            <!-- Stars -->
                                            <div class="flex items-center gap-1 mb-6">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i
                                                        class="fas fa-star text-2xl {{ $i <= $testimoniItem->rating ? 'text-amber-400' : 'text-white/20' }}"></i>
                                                @endfor
                                            </div>

                                            <!-- Testimonial Text -->
                                            <blockquote
                                                class="text-white text-xl md:text-2xl leading-relaxed mb-8 font-light italic">
                                                "{{ $testimoniItem->testimoni }}"
                                            </blockquote>

                                            <!-- Author Info -->
                                            <div class="flex items-center gap-4 pt-6 border-t border-white/20">
                                                <div
                                                    class="w-16 h-16 rounded-full bg-gradient-to-br from-sky-400 via-cyan-400 to-blue-400 p-0.5 shadow-lg shadow-sky-500/30">
                                                    <div
                                                        class="w-full h-full rounded-full bg-sky-900 flex items-center justify-center">
                                                        <span class="text-2xl font-bold text-white">
                                                            {{ strtoupper(substr($testimoniItem->character_name, 0, 1)) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h4 class="text-white font-bold text-lg">{{ $testimoniItem->character_name }}
                                                    </h4>
                                                    <p class="text-sky-300 text-sm flex items-center gap-2">
                                                        <i class="fas fa-check-circle text-xs"></i>
                                                        Verified Patient
                                                    </p>
                                                    <p class="text-sky-200/60 text-xs mt-1">
                                                        {{ $testimoniItem->created_at->diffForHumans() }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Navigation Arrows -->
                    <button id="prevBtn"
                        class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-6 w-14 h-14 bg-white/15 backdrop-blur-xl border-2 border-white/30 rounded-full flex items-center justify-center text-white hover:border-sky-400 hover:bg-sky-500/30 hover:scale-110 transition-all duration-300 shadow-xl">
                        <i class="fas fa-chevron-left text-xl"></i>
                    </button>
                    <button id="nextBtn"
                        class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-6 w-14 h-14 bg-white/15 backdrop-blur-xl border-2 border-white/30 rounded-full flex items-center justify-center text-white hover:border-sky-400 hover:bg-sky-500/30 hover:scale-110 transition-all duration-300 shadow-xl">
                        <i class="fas fa-chevron-right text-xl"></i>
                    </button>

                    <!-- Dots Indicator -->
                    <div id="dotsContainer" class="flex justify-center gap-3 mt-10">
                        @foreach($testimonials as $index => $item)
                            <button
                                class="testimonial-dot w-2.5 h-2.5 rounded-full bg-white/40 hover:bg-sky-400 transition-all duration-300"
                                data-index="{{ $index }}"></button>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="text-center text-sky-200/60 py-12 text-lg">Belum ada testimoni.</div>
            @endif
        </div>
    </div>

    </div>

@endsection

@push('styles')
    <style>
        /* Custom Scrollbar untuk Pop-up */
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #2563eb, #1e40af);
        }

        /* Animasi fade in up untuk pop-up */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.5s ease-out;
        }

        /* Fade in from left animation */
        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animate-fade-in-left {
            animation: fadeInLeft 0.6s ease-out;
        }

        /* Fade in from right animation */
        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animate-fade-in-right {
            animation: fadeInRight 0.6s ease-out;
        }

        /* Animation delay untuk floating elements */
        .delay-1000 {
            animation-delay: 1s;
        }

        /* Style untuk select box di appointment form */
        select#form_type {
            color: #1e293b !important;
            /* text-slate-800 */
            font-weight: 700 !important;
        }

        select#form_type option {
            color: #1e293b !important;
            /* text-slate-800 */
            font-weight: 700 !important;
            background-color: #ffffff !important;
        }

        select#form_type option:checked {
            color: #1e293b !important;
            /* text-slate-800 */
            font-weight: 700 !important;
            background-color: #e0f2fe !important;
            /* bg-blue-50 */
        }

        select#form_type:focus {
            color: #1e293b !important;
            /* text-slate-800 */
            font-weight: 700 !important;
        }

        /* New Testimonial Animations */
        .drop-shadow-glow {
            filter: drop-shadow(0 0 4px rgba(251, 191, 36, 0.5));
        }

        .perspective-1000 {
            perspective: 1000px;
        }

        .backface-hidden {
            backface-visibility: hidden;
        }

        @keyframes spin-slow {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .animate-spin-slow {
            animation: spin-slow 8s linear infinite;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // --- Regulation Modal Functions ---
        function showRegulationModal() {
            console.log('showRegulationModal called');
            const modal = document.getElementById('regulationModal');
            console.log('Modal element:', modal);
            if (modal) {
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
                console.log('Modal opened successfully');
            } else {
                console.error('Modal element not found!');
            }
        }
        function closeRegulationModal() { const modal = document.getElementById('regulationModal'); if (modal) { modal.style.display = 'none'; document.body.style.overflow = 'auto'; } }
        function handleModalClick(event) { const modal = document.getElementById('regulationModal'); if (event.target === modal) { closeRegulationModal(); } }
        function demoSuccess() { showNotification('Data berhasil disimpan!', 'success'); } function demoError() { showNotification('Terjadi kesalahan saat menyimpan data!', 'error'); } function demoWarning() { showNotification('Perhatian! Pastikan data yang diisi sudah benar.', 'warning'); } function demoInfo() { showNotification('Informasi: Form akan otomatis tersimpan setiap 30 detik.', 'info'); }
        document.addEventListener('DOMContentLoaded', function () {         // Modal Listeners         const modal = document.getElementById('regulationModal');         if (modal) {             modal.addEventListener('click', handleModalClick);             document.addEventListener('keydown', function (event) {                 if (event.key === 'Escape') closeRegulationModal();             });         }



            // --- Testimonial Carousel ---
            var carouselTrack = document.getElementById('testimonialTrack');
            var carouselPrevBtn = document.getElementById('prevBtn');
            var carouselNextBtn = document.getElementById('nextBtn');
            var carouselDots = document.querySelectorAll('.testimonial-dot');
            var carouselIndex = 0;
            var carouselTotal = 0;

            if (carouselTrack) {
                carouselTotal = carouselTrack.children.length;

                if (carouselTotal > 0) {
                    // Initial state
                    if (carouselDots.length > 0) {
                        carouselDots[0].classList.add('!w-8', '!bg-sky-400', '!shadow-lg', '!shadow-sky-400/50');
                    }

                    // Functions
                    function updateCarousel() {
                        var offset = carouselIndex * -100;
                        carouselTrack.style.transform = 'translateX(' + offset + '%)';

                        for (var i = 0; i < carouselDots.length; i++) {
                            if (i === carouselIndex) {
                                carouselDots[i].classList.add('!w-8', '!bg-sky-400', '!shadow-lg', '!shadow-sky-400/50');
                            } else {
                                carouselDots[i].classList.remove('!w-8', '!bg-sky-400', '!shadow-lg', '!shadow-sky-400/50');
                            }
                        }
                    }

                    function nextSlide() {
                        carouselIndex = (carouselIndex + 1) % carouselTotal;
                        updateCarousel();
                    }

                    function prevSlide() {
                        carouselIndex = (carouselIndex - 1 + carouselTotal) % carouselTotal;
                        updateCarousel();
                    }

                    function goToSlide(index) {
                        carouselIndex = index;
                        updateCarousel();
                    }

                    // Event listeners
                    if (carouselPrevBtn) {
                        carouselPrevBtn.addEventListener('click', prevSlide);
                    }
                    if (carouselNextBtn) {
                        carouselNextBtn.addEventListener('click', nextSlide);
                    }
                    for (var i = 0; i < carouselDots.length; i++) {
                        (function (idx) {
                            carouselDots[idx].addEventListener('click', function () {
                                goToSlide(idx);
                            });
                        })(i);
                    }

                    // Auto-play
                    setInterval(nextSlide, 6000);
                }
            }

        }); // Close DOMContentLoaded

        // --- REDESIGNED TESTIMONIAL CAROUSEL ENGINE ---
        // (Removed: Switched to Grid Layout for better visibility of 10+ reviews)
    </script>
@endpush