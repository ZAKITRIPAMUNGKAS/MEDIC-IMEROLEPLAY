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
    <!-- Hero Section -->
    <div class="relative min-h-[90vh] flex items-center justify-center bg-[#07476B] overflow-hidden">
        <!-- Background Image with Balanced Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/hero.png') }}" alt="Medical Center" class="w-full h-full object-cover object-[center_bottom] opacity-35">
            <div class="absolute inset-0 bg-gradient-to-b from-[#042F48]/80 via-[#07527A]/85 to-[#07527A]"></div>
        </div>

        <!-- Hero Content -->
        <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-24 text-center animate-fade-in-up">
            <!-- Hospital & iMe Logos -->
            <div class="flex justify-center items-center gap-4 sm:gap-6 mb-8 flex-wrap">
                <div class="p-2 bg-white/10 backdrop-blur-md rounded-2xl border border-white/15 shadow-lg">
                    <img src="{{ asset('images/logoime.webp') }}" alt="iMe Roleplay" class="h-14 w-14 sm:h-16 sm:w-16 object-contain">
                </div>
                <div class="p-2 bg-white/10 backdrop-blur-md rounded-2xl border border-white/15 shadow-lg">
                    <img src="{{ asset('images/motionlife-logo.png') }}" alt="EMS Alta" class="h-14 w-14 sm:h-16 sm:w-16 object-contain">
                </div>
                <div class="p-2 bg-white/10 backdrop-blur-md rounded-2xl border border-white/15 shadow-lg">
                    <img src="{{ asset('images/logo rhv2.png') }}" alt="Roxwood Hospital" class="h-14 w-14 sm:h-16 sm:w-16 object-contain">
                </div>
            </div>

            <!-- Title & Subtitle -->
            <h1 class="text-6xl sm:text-7xl md:text-8xl font-black text-transparent bg-clip-text bg-gradient-to-r from-sky-200 via-cyan-200 to-white tracking-tight drop-shadow-md mb-2">
                iMe
            </h1>
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-sky-100 mb-4">
                Portal Medis Terpadu
            </h2>
            <p class="text-base sm:text-lg text-sky-200/90 max-w-2xl mx-auto font-light leading-relaxed mb-8">
                Akses layanan kesehatan Los Santos EMS secara cepat, profesional, dan terintegrasi.
            </p>

            <!-- Buttons: 2 CTAs + 2 Small Links -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-6">
                <a href="#services" class="w-full sm:w-auto px-8 py-3.5 bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-base rounded-xl shadow-lg hover:shadow-cyan-500/30 transition-all duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-stethoscope"></i>
                    <span>Lihat Layanan</span>
                </a>
                <a href="{{ route('public.doctor-schedule') }}" class="w-full sm:w-auto px-7 py-3.5 bg-white/10 hover:bg-white/20 text-white font-semibold text-base rounded-xl border border-white/25 transition-all duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-calendar-check text-cyan-300"></i>
                    <span>Jadwal Praktik</span>
                </a>
            </div>

            <!-- Secondary Sub-links -->
            <div class="flex items-center justify-center gap-6 text-xs text-sky-200/80">
                <button onclick="showRegulationModal()" type="button" class="hover:text-white transition-colors flex items-center gap-1.5 cursor-pointer">
                    <i class="fas fa-file-alt text-amber-400"></i> Regulasi Pengobatan
                </button>
                <span class="text-white/20">•</span>
                <a href="{{ route('public.struktural-ems') }}" class="hover:text-white transition-colors flex items-center gap-1.5">
                    <i class="fas fa-sitemap text-sky-300"></i> Struktural EMS
                </a>
            </div>
        </div>
    </div>

    <!-- Keunggulan Layanan Medis (Stats Section) -->
    <div class="py-20 bg-[#08658D] text-white relative">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="text-xs font-bold text-cyan-300 uppercase tracking-widest block mb-2">KEUNGGULAN</span>
                <h2 class="text-3xl sm:text-4xl font-bold">Keunggulan Layanan Medis</h2>
                <p class="text-sky-200/80 text-sm mt-2 font-light">Standar pelayanan terbaik untuk seluruh warga Los Santos.</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 text-center hover:bg-white/10 transition-all">
                    <i class="fas fa-users text-cyan-300 text-xl mb-3 block"></i>
                    <div class="text-3xl sm:text-4xl font-extrabold text-white mb-1">{{ number_format($stats['total_forms']) }}+</div>
                    <div class="text-xs text-sky-200/80 font-medium">Pasien Dilayani</div>
                </div>
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 text-center hover:bg-white/10 transition-all">
                    <i class="fas fa-user-md text-cyan-300 text-xl mb-3 block"></i>
                    <div class="text-3xl sm:text-4xl font-extrabold text-white mb-1">{{ $stats['total_staff'] }}+</div>
                    <div class="text-xs text-sky-200/80 font-medium">Tenaga Medis</div>
                </div>
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 text-center hover:bg-white/10 transition-all">
                    <i class="fas fa-check-circle text-cyan-300 text-xl mb-3 block"></i>
                    <div class="text-3xl sm:text-4xl font-extrabold text-white mb-1">98%</div>
                    <div class="text-xs text-sky-200/80 font-medium">Tingkat Kepuasan</div>
                </div>
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 text-center hover:bg-white/10 transition-all">
                    <i class="fas fa-heartbeat text-cyan-300 text-xl mb-3 block"></i>
                    <div class="text-3xl sm:text-4xl font-extrabold text-white mb-1">24/7</div>
                    <div class="text-xs text-sky-200/80 font-medium">Layanan Darurat</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Jam Operasional Section -->
    <div class="py-20 bg-[#075778] text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <span class="text-xs font-bold text-cyan-300 uppercase tracking-widest block mb-2">JAM OPERASIONAL</span>
                <h2 class="text-3xl sm:text-4xl font-bold">Jadwal Pelayanan Rumah Sakit</h2>
            </div>

            <!-- Compact Notice Banner -->
            <div class="bg-white/5 border border-amber-400/30 rounded-xl p-4 mb-8 flex items-start gap-3 text-xs text-amber-200/90">
                <i class="fas fa-info-circle text-amber-400 text-base shrink-0 mt-0.5"></i>
                <p>
                    <strong class="text-amber-300">Informasi Pelayanan:</strong> Pelayanan rumah sakit diberikan apabila tenaga medis yang bersangkutan bersedia & available (On Duty).
                </p>
            </div>

            <!-- Operational Hours Table Card -->
            <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden divide-y divide-white/10">
                <!-- Row 1: Oplas -->
                <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-white/5 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-cyan-500/20 text-cyan-300 flex items-center justify-center shrink-0">
                            <i class="fas fa-user-md text-base"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-white text-base">Operasi Plastik</h4>
                            <p class="text-xs text-sky-200/70">Layanan estetika & pembedahan</p>
                        </div>
                    </div>
                    <div class="text-sm font-semibold text-cyan-200 bg-white/5 px-4 py-2 rounded-xl border border-white/10">
                        Shift 1: 13:00 – 16:00 WIB &nbsp;•&nbsp; Shift 2: 20:00 – 22:00 WIB
                    </div>
                </div>

                <!-- Row 2: Surat-Suratan -->
                <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-white/5 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-cyan-500/20 text-cyan-300 flex items-center justify-center shrink-0">
                            <i class="fas fa-file-alt text-base"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-white text-base">Surat-Suratan Medis</h4>
                            <p class="text-xs text-sky-200/70">Surat sehat, bebas narkoba & psikologi</p>
                        </div>
                    </div>
                    <div class="text-sm font-semibold text-cyan-200 bg-white/5 px-4 py-2 rounded-xl border border-white/10">
                        Shift 1: 13:00 – 17:00 WIB &nbsp;•&nbsp; Shift 2: 19:00 – 22:00 WIB
                    </div>
                </div>

                <!-- Row 3: Farmasi -->
                <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-white/5 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-300 flex items-center justify-center shrink-0">
                            <i class="fas fa-pills text-base"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-white text-base">Layanan Farmasi</h4>
                            <p class="text-xs text-sky-200/70">Pengambilan & pengobatan medis</p>
                        </div>
                    </div>
                    <div class="inline-flex items-center gap-2 text-xs font-bold text-emerald-300 bg-emerald-500/20 px-4 py-2 rounded-xl border border-emerald-400/30">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>BUKA 24 JAM NON-STOP</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Services Section -->
    <div class="py-20 bg-[#086B93] text-white" id="services">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-12">
                <span class="text-xs font-bold text-cyan-300 uppercase tracking-widest block mb-2">LAYANAN</span>
                <h2 class="text-3xl sm:text-4xl font-bold">Layanan Medis Kami</h2>
                <p class="text-sky-200/80 text-sm mt-2 font-light max-w-xl mx-auto">Berbagai layanan kesehatan komprehensif yang tersedia untuk kebutuhan karakter Anda.</p>
            </div>

            <!-- 3 Main Service Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16">
                <!-- Card 1: Konsultasi Medis -->
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition-all flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 bg-cyan-500/20 text-cyan-300 rounded-xl flex items-center justify-center mb-4 text-xl">
                            <i class="fas fa-stethoscope"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Konsultasi Medis</h3>
                        <p class="text-sky-200/80 text-xs leading-relaxed mb-6">
                            Pemeriksaan umum dan diagnosis kesehatan oleh tim dokter medis berpengalaman.
                        </p>
                    </div>
                    <div class="space-y-2 pt-4 border-t border-white/10">
                        <a href="{{ route('public.cek-kesehatan') }}" class="block w-full text-center py-2.5 px-4 bg-cyan-500/20 hover:bg-cyan-500/30 text-cyan-200 text-xs font-bold rounded-xl border border-cyan-400/30 transition-all">
                            Surat Kesehatan →
                        </a>
                        <a href="{{ route('public.form', 'janji_temu') }}" class="block w-full text-center py-2.5 px-4 bg-white/5 hover:bg-white/10 text-white text-xs font-semibold rounded-xl border border-white/10 transition-all">
                            Janji Temu Dokter →
                        </a>
                    </div>
                </div>

                <!-- Card 2: Operasi Plastik -->
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition-all flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 bg-cyan-500/20 text-cyan-300 rounded-xl flex items-center justify-center mb-4 text-xl">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Operasi Plastik</h3>
                        <p class="text-sky-200/80 text-xs leading-relaxed mb-6">
                            Prosedur bedah kosmetik dan estetika profesional yang aman oleh tim bedah bersertifikat.
                        </p>
                    </div>
                    <div class="pt-4 border-t border-white/10">
                        <a href="{{ route('public.operasi-plastik') }}" class="block w-full text-center py-2.5 px-4 bg-cyan-500/20 hover:bg-cyan-500/30 text-cyan-200 text-xs font-bold rounded-xl border border-cyan-400/30 transition-all">
                            Daftar Oplas →
                        </a>
                    </div>
                </div>

                <!-- Card 3: Konsultasi Psikologi -->
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition-all flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 bg-cyan-500/20 text-cyan-300 rounded-xl flex items-center justify-center mb-4 text-xl">
                            <i class="fas fa-brain"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Konsultasi Psikologi</h3>
                        <p class="text-sky-200/80 text-xs leading-relaxed mb-6">
                            Sesi konseling dan evaluasi psikologis bersama profesional medis kesehatan mental.
                        </p>
                    </div>
                    <div class="pt-4 border-t border-white/10">
                        <a href="{{ route('public.surat-psikolog') }}" class="block w-full text-center py-2.5 px-4 bg-cyan-500/20 hover:bg-cyan-500/30 text-cyan-200 text-xs font-bold rounded-xl border border-cyan-400/30 transition-all">
                            Formulir Psikologi →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Distinct Section: Layanan Khusus Roleplay (Karakter Kill) -->
            <div class="max-w-3xl mx-auto">
                <div class="bg-white/5 border border-red-400/30 rounded-2xl p-8 text-center relative overflow-hidden">
                    <span class="text-[10px] font-bold text-red-400 uppercase tracking-widest bg-red-500/10 px-3 py-1 rounded-full border border-red-500/20 inline-block mb-3">
                        LAYANAN KHUSUS ROLEPLAY
                    </span>
                    <h3 class="text-2xl font-bold text-white mb-2">Pendaftaran Karakter Kill</h3>
                    <p class="text-sky-200/80 text-xs leading-relaxed max-w-lg mx-auto mb-6">
                        Layanan penanganan medis khusus dan perawatan intensif untuk skenario penanganan medis kritis karakter Anda.
                    </p>
                    <a href="{{ route('public.pendaftaran-karakter') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-red-500/20 hover:bg-red-500/30 text-red-200 text-xs font-bold rounded-xl border border-red-400/30 transition-all">
                        <i class="fas fa-user-plus text-xs"></i>
                        <span>Daftar Karakter Kill →</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Testimonial Section -->
    <div class="py-20 bg-[#075778] text-white relative">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="text-xs font-bold text-cyan-300 uppercase tracking-widest block mb-2">PENGALAMAN PASIEN</span>
                <h2 class="text-3xl sm:text-4xl font-bold">Apa Kata Mereka?</h2>
                <p class="text-sky-200/80 text-sm mt-2 font-light">Ulasan dan pengalaman langsung dari pasien terverifikasi.</p>
            </div>

            @if(isset($testimonials) && $testimonials->count() > 0)
                <div class="relative max-w-4xl mx-auto">
                    <div class="overflow-hidden rounded-2xl">
                        <div id="testimonialTrack" class="flex transition-transform duration-700 ease-in-out">
                            @foreach($testimonials as $index => $testimoniItem)
                                <div class="w-full flex-shrink-0 p-2">
                                    <div class="bg-white/5 border border-white/10 rounded-2xl p-8 relative">
                                        <div class="flex items-center gap-1 mb-4">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star text-sm {{ $i <= $testimoniItem->rating ? 'text-amber-400' : 'text-white/20' }}"></i>
                                            @endfor
                                        </div>
                                        <blockquote class="text-white text-base sm:text-lg leading-relaxed mb-6 font-light italic">
                                            "{{ $testimoniItem->testimoni }}"
                                        </blockquote>
                                        <div class="flex items-center gap-3 pt-4 border-t border-white/10 text-xs">
                                            <div class="w-10 h-10 rounded-full bg-cyan-500/20 text-cyan-300 font-bold flex items-center justify-center text-sm shrink-0">
                                                {{ strtoupper(substr($testimoniItem->character_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="text-white font-bold">{{ $testimoniItem->character_name }}</div>
                                                <div class="text-sky-200/60 text-[11px]">Pasien Terverifikasi • {{ $testimoniItem->created_at->diffForHumans() }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Navigation controls -->
                    @if($testimonials->count() > 1)
                    <div class="flex items-center justify-center gap-4 mt-6">
                        <button id="prevBtn" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-white hover:bg-white/15 transition-all cursor-pointer">
                            <i class="fas fa-chevron-left text-xs"></i>
                        </button>
                        <div id="dotsContainer" class="flex gap-2">
                            @foreach($testimonials as $index => $item)
                                <button class="testimonial-dot w-2.5 h-2.5 rounded-full bg-white/20 hover:bg-cyan-400 transition-all" data-index="{{ $index }}"></button>
                            @endforeach
                        </div>
                        <button id="nextBtn" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-white hover:bg-white/15 transition-all cursor-pointer">
                            <i class="fas fa-chevron-right text-xs"></i>
                        </button>
                    </div>
                    @endif
                </div>
            @else
                <div class="text-center text-sky-200/60 py-8 text-sm">Belum ada testimoni.</div>
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
</script>
@endpush