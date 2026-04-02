@extends('layouts.app')

@section('title', 'Jadwal Praktek Dokter - Motion Medical Center')

@section('content')
<div class="relative min-h-screen pt-24 pb-12 px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700"></div>
    <div class="absolute inset-0 bg-black bg-opacity-20"></div>

    <div class="relative max-w-7xl mx-auto">
        <!-- Hero Section -->
        <div class="text-center mb-16 animate-fade-in-up">
            <div class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/20 backdrop-blur-md mb-6">
                <i class="fas fa-calendar-check text-cyan-400 mr-2"></i>
                <span class="text-sky-100 text-sm font-medium tracking-wide">Update Jadwal Terbaru</span>
            </div>
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold text-white mb-6 tracking-tight">
                Jadwal Praktek <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-sky-400">Dokter Spesialis</span>
            </h1>
            <p class="text-lg sm:text-xl text-sky-100 max-w-3xl mx-auto leading-relaxed opacity-90">
                Temukan jadwal ketersediaan dokter ahli kami untuk membantu kesehatan Anda. Silakan buat janji temu untuk konsultasi lebih lanjut.
            </p>
        </div>

        <!-- Filter/Quick Actions Section -->
        <div class="mb-12 flex flex-wrap justify-center gap-4 animate-fade-in-up" style="animation-delay: 0.2s">
            <a href="#penyakit-dalam" class="px-5 py-2.5 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 hover:border-white/20 text-white text-sm font-medium transition-all duration-300">
                Penyakit Dalam
            </a>
            <a href="#bedah" class="px-5 py-2.5 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 hover:border-white/20 text-white text-sm font-medium transition-all duration-300">
                Bedah
            </a>
            <a href="#anak" class="px-5 py-2.5 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 hover:border-white/20 text-white text-sm font-medium transition-all duration-300">
                Anak
            </a>
            <a href="#mata" class="px-5 py-2.5 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 hover:border-white/20 text-white text-sm font-medium transition-all duration-300">
                Mata
            </a>
        </div>

        <!-- Schedule Grid by Poli -->
        <div class="space-y-16">
            @forelse($schedules as $poli => $doctors)
                <div id="{{ Str::slug($poli) }}" class="animate-fade-in-up" style="animation-delay: 0.3s">
                    <!-- Poli Header -->
                    <div class="flex items-center mb-8">
                        <div class="h-px flex-1 bg-gradient-to-r from-transparent to-white/20"></div>
                        <h2 class="mx-6 text-2xl sm:text-3xl font-bold text-white flex items-center">
                            <span class="mr-3">{{ explode(' ', $poli)[0] ?? '🩺' }}</span>
                            {{ str_replace(explode(' ', $poli)[0] . ' ', '', $poli) }}
                        </h2>
                        <div class="h-px flex-1 bg-gradient-to-l from-transparent to-white/20"></div>
                    </div>

                    <!-- Doctors Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                        @foreach($doctors as $doctor)
                            <div class="group relative bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-6 sm:p-8 hover:bg-white/15 transition-all duration-500 overflow-hidden group">
                                <!-- Decoration -->
                                <div class="absolute -right-10 -top-10 w-32 h-32 bg-sky-500/10 rounded-full blur-3xl group-hover:bg-sky-500/20 transition-all duration-500"></div>
                                
                                <div class="relative flex flex-col h-full">
                                    <div class="flex items-start justify-between mb-6">
                                        <div class="w-16 h-16 bg-gradient-to-br from-sky-400 to-cyan-500 rounded-2xl flex items-center justify-center shadow-lg transform group-hover:rotate-6 transition-transform duration-500">
                                            <i class="fas fa-user-md text-white text-2xl"></i>
                                        </div>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] uppercase tracking-widest font-bold {{ $doctor->hospital == 'alta' ? 'bg-blue-500/20 text-blue-300 border border-blue-500/30' : 'bg-purple-500/20 text-purple-300 border border-purple-500/30' }}">
                                            <i class="fas fa-hospital mr-1"></i>
                                            {{ $doctor->hospital == 'alta' ? 'Alta' : 'Roxwood' }}
                                        </span>
                                    </div>

                                    <h3 class="text-xl font-bold text-white mb-2 leading-tight group-hover:text-cyan-300 transition-colors duration-300">
                                        {{ $doctor->doctor_name }}
                                    </h3>
                                    <p class="text-sky-300 text-sm font-medium mb-6 uppercase tracking-wider">
                                        {{ str_replace('🩺 ', '', $doctor->poli) }}
                                    </p>

                                    <div class="mt-auto space-y-4">
                                        <!-- Days -->
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($doctor->day as $day)
                                                <span class="px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 text-xs text-white/80">
                                                    {{ $day }}
                                                </span>
                                            @endforeach
                                        </div>

                                        <!-- Time -->
                                        <div class="flex items-center text-white/90">
                                            <div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center mr-3">
                                                <i class="far fa-clock text-cyan-400 text-sm"></i>
                                            </div>
                                            <span class="text-sm font-semibold tracking-wide">
                                                {{ \Carbon\Carbon::parse($doctor->start_time)->format('H:i') }} — {{ \Carbon\Carbon::parse($doctor->end_time)->format('H:i') }} WIB
                                            </span>
                                        </div>
                                        
                                        @if($doctor->notes)
                                            <div class="p-3 rounded-xl bg-black/20 border border-white/5 text-[11px] text-sky-200 leading-relaxed">
                                                <i class="fas fa-info-circle mr-1 text-cyan-500"></i>
                                                {{ $doctor->notes }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mt-8 pt-6 border-t border-white/10 flex items-center justify-between">
                                        <a href="{{ route('public.form', ['type' => 'janji_temu']) }}?hospital={{ $doctor->hospital }}&poli={{ urlencode($doctor->poli) }}" 
                                           class="inline-flex items-center text-sm font-bold text-white hover:text-cyan-400 transition-colors group/link">
                                            Buat Janji Temu
                                            <i class="fas fa-arrow-right ml-2 transform group-hover/link:translate-x-1 transition-transform"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="text-center py-20 bg-white/5 rounded-3xl border border-white/10 backdrop-blur-md">
                    <i class="fas fa-calendar-times text-6xl text-white/20 mb-6 font-thin"></i>
                    <h3 class="text-2xl font-bold text-white mb-2">Belum Tersedia</h3>
                    <p class="text-sky-200 max-w-md mx-auto opacity-70">
                        Jadwal praktek dokter untuk saat ini belum tersedia secara publik. Silakan hubungi kami untuk informasi lebih lanjut.
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Footer Note -->
        <div class="mt-20 text-center animate-fade-in-up" style="animation-delay: 0.6s">
            <div class="inline-block p-6 rounded-3xl bg-gradient-to-br from-white/5 to-transparent border border-white/10 backdrop-blur-md max-w-2xl">
                <p class="text-sm text-sky-100/70 italic leading-relaxed">
                    * Jadwal sewaktu-waktu dapat berubah tanpa pemberitahuan sebelumnya. Konfirmasi ketersediaan dokter dapat dilakukan melalui layanan darurat atau meja resepsionis di rumah sakit masing-masing.
                </p>
                <div class="mt-6 flex justify-center gap-6">
                    <a href="{{ route('public.form', ['type' => 'janji_temu', 'hospital' => 'alta']) }}" class="text-white hover:text-red-400 transition-colors flex items-center text-xs font-bold uppercase tracking-widest">
                        <i class="fas fa-phone-alt mr-2"></i> Alta Emergency
                    </a>
                    <a href="{{ route('public.form', ['type' => 'janji_temu', 'hospital' => 'roxwood']) }}" class="text-white hover:text-red-400 transition-colors flex items-center text-xs font-bold uppercase tracking-widest">
                        <i class="fas fa-phone-alt mr-2"></i> Roxwood Emergency
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .animate-fade-in-up {
        animation: fadeInUp 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        opacity: 0;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endsection
