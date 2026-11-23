@extends('layouts.app')

@section('title', 'Jadwal Praktek Dokter & Spesialisasi - Panel Admin')

@section('content')
<div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700"></div>
    <div class="absolute inset-0 bg-black bg-opacity-20"></div>

    <div class="relative max-w-7xl w-full mx-auto text-white">
        <!-- Header Section -->
        <div class="glass-effect rounded-2xl elegant-shadow-lg p-4 sm:p-6 md:p-8 mb-6 sm:mb-8">
            <div class="flex flex-col space-y-4 sm:space-y-0 sm:flex-row sm:items-center justify-between mb-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-500/10 border border-sky-400/30 text-sky-300 text-xs font-semibold mb-2">
                        <i class="fas fa-stethoscope"></i> Layanan Dokter Spesialis & Umum
                    </div>
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-1">Jadwal Praktek Dokter</h1>
                    <p class="text-sky-200 text-sm sm:text-base">Dokter spesialis dapat mendaftarkan dan mengelola spesialisasi serta jadwal praktek masing-masing</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('public.doctor-schedule') }}" target="_blank" class="inline-flex items-center justify-center px-5 py-3 bg-white/10 hover:bg-white/20 text-sky-200 rounded-xl font-semibold transition-all duration-300 text-sm border border-white/20 shadow">
                        <i class="fas fa-external-link-alt mr-2 text-xs"></i><span>Lihat Portal Publik</span>
                    </a>
                    <a href="{{ route('admin.doctor-schedules.create') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white rounded-xl font-bold transition-all duration-300 shadow-lg shadow-sky-900/30">
                        <i class="fas fa-plus mr-2"></i><span>Tambah / Daftarkan Jadwal</span>
                    </a>
                </div>
            </div>

            <!-- Quick Filter Tabs (Semua vs Jadwal Saya) -->
            <div class="flex flex-wrap items-center gap-2 mb-6 pt-2 border-t border-white/10">
                <a href="{{ route('admin.doctor-schedules.index') }}"
                   class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all {{ empty($filterMySchedules) ? 'bg-sky-500 text-white shadow-md' : 'bg-white/5 text-gray-300 hover:bg-white/10' }}">
                    <i class="fas fa-list mr-1.5"></i>Semua Jadwal
                    <span class="ml-1.5 px-2 py-0.5 rounded-full text-xs {{ empty($filterMySchedules) ? 'bg-sky-700/50 text-white' : 'bg-white/10 text-gray-300' }}">{{ $totalCount }}</span>
                </a>
                <a href="{{ route('admin.doctor-schedules.index', ['my_schedules' => 1]) }}"
                   class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all {{ !empty($filterMySchedules) ? 'bg-cyan-500 text-white shadow-md' : 'bg-white/5 text-gray-300 hover:bg-white/10' }}">
                    <i class="fas fa-user-md mr-1.5"></i>Jadwal Saya ({{ auth()->user()->name }})
                    <span class="ml-1.5 px-2 py-0.5 rounded-full text-xs {{ !empty($filterMySchedules) ? 'bg-cyan-700/50 text-white' : 'bg-white/10 text-gray-300' }}">{{ $mySchedulesCount }}</span>
                </a>
            </div>

            <!-- Search and Filter -->
            <form method="GET" action="{{ route('admin.doctor-schedules.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                @if(!empty($filterMySchedules))
                    <input type="hidden" name="my_schedules" value="1">
                @endif
                <div>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama dokter atau spesialis..." 
                           class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 text-sm">
                </div>
                <div>
                    <select name="poli" class="w-full bg-white/10 text-white border border-white/20 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 appearance-none text-sm">
                        <option value="" class="bg-slate-800">Semua Spesialisasi / Poli</option>
                        @foreach($poliList as $poli)
                            <option value="{{ $poli }}" @selected(request('poli') == $poli) class="bg-slate-800 text-slate-100">{{ $poli }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="hospital" class="w-full bg-white/10 text-white border border-white/20 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 appearance-none text-sm">
                        <option value="" class="bg-slate-800">Semua Rumah Sakit</option>
                        <option value="alta" @selected(request('hospital') == 'alta') class="bg-slate-800 text-slate-100">Alta Hospital</option>
                        <option value="roxwood" @selected(request('hospital') == 'roxwood') class="bg-slate-800 text-slate-100">Roxwood Hospital</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white rounded-lg font-semibold transition-all duration-300 text-sm">
                        <i class="fas fa-search mr-1.5"></i><span>Filter</span>
                    </button>
                    <a href="{{ route('admin.doctor-schedules.index') }}" class="inline-flex items-center justify-center px-3.5 py-2.5 bg-white/10 text-white rounded-lg border border-white/20 hover:bg-white/20 transition-all duration-300" title="Reset Filter">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>

        @if(session('success'))
        <div class="mb-6 px-6 py-4 bg-emerald-500/20 border border-emerald-500/30 text-emerald-200 rounded-2xl flex items-center gap-3">
            <i class="fas fa-check-circle text-emerald-400 text-lg"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 px-6 py-4 bg-rose-500/20 border border-rose-500/30 text-rose-200 rounded-2xl flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-rose-400 text-lg"></i>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        <!-- Schedule List -->
        <div class="glass-effect rounded-2xl elegant-shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-black/30">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Spesialisasi / Poli</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Dokter Praktek</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Hari & Jam</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Rumah Sakit</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Status Publik</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse($schedules as $schedule)
                            @php
                                $isMine = (strcasecmp(trim((string)$schedule->doctor_name), trim((string)auth()->user()->name)) === 0) ||
                                          (!empty($schedule->user_id) && (int)$schedule->user_id === (int)auth()->id());
                                $canManage = \App\Http\Controllers\Admin\DoctorScheduleController::canManageSchedule($schedule);
                            @endphp
                            <tr class="hover:bg-white/5 transition-colors duration-200 {{ $isMine ? 'bg-sky-500/5' : '' }}">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-sky-200">
                                        {{ $schedule->poli }}
                                    </div>
                                    @if($schedule->notes)
                                        <div class="text-[11px] text-gray-400 line-clamp-1 mt-0.5">
                                            <i class="fas fa-info-circle mr-1 text-sky-400/70"></i>{{ $schedule->notes }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gradient-to-br from-sky-400 to-cyan-500 rounded-full flex items-center justify-center mr-3 shadow">
                                            <i class="fas fa-user-md text-white text-xs"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-white flex items-center flex-wrap gap-1.5">
                                                <span>{{ $schedule->doctor_name }}</span>
                                                @if($isMine)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-500/25 text-sky-200 border border-sky-400/40">
                                                        <i class="fas fa-user-check mr-1 text-[9px]"></i>Jadwal Anda
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs text-sky-300 font-medium mb-1 flex flex-wrap gap-1">
                                        @foreach($schedule->day as $d)
                                            <span class="inline-block bg-white/10 px-2 py-0.5 rounded-md border border-white/10">{{ $d }}</span>
                                        @endforeach
                                    </div>
                                    <div class="text-xs text-gray-300 font-mono">
                                        <i class="far fa-clock mr-1 text-sky-400"></i>
                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }} WIB
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $schedule->hospital == 'alta' ? 'bg-sky-500/20 text-sky-300 border border-sky-400/30' : 'bg-purple-500/20 text-purple-300 border border-purple-400/30' }}">
                                        <i class="fas fa-hospital mr-1.5"></i>
                                        {{ $schedule->hospital == 'alta' ? 'Alta Hospital' : 'Roxwood Hospital' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($canManage)
                                        <form method="POST" action="{{ route('admin.doctor-schedules.toggle-active', $schedule) }}">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold cursor-pointer transition-all duration-300 {{ $schedule->is_active ? 'bg-emerald-500/20 text-emerald-300 hover:bg-emerald-500/30 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-300 hover:bg-rose-500/30 border border-rose-500/30' }}">
                                                <i class="fas fa-{{ $schedule->is_active ? 'check-circle' : 'times-circle' }} mr-1.5"></i>
                                                {{ $schedule->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </button>
                                        </form>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $schedule->is_active ? 'bg-emerald-500/15 text-emerald-300' : 'bg-rose-500/15 text-rose-300' }}">
                                            <i class="fas fa-{{ $schedule->is_active ? 'check-circle' : 'times-circle' }} mr-1.5"></i>
                                            {{ $schedule->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($canManage)
                                        <div class="flex items-center space-x-3">
                                            <a href="{{ route('admin.doctor-schedules.edit', $schedule) }}" 
                                               class="px-2.5 py-1.5 rounded-lg bg-sky-500/20 hover:bg-sky-500/30 text-sky-300 border border-sky-400/30 text-xs font-semibold inline-flex items-center gap-1 transition-colors"
                                               title="Edit Jadwal">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form method="POST" action="{{ route('admin.doctor-schedules.destroy', $schedule) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal praktek ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="px-2.5 py-1.5 rounded-lg bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-400/30 text-xs font-semibold inline-flex items-center gap-1 transition-colors"
                                                        title="Hapus Jadwal">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Hanya Pemilik</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                    <i class="fas fa-calendar-times text-4xl mb-4 opacity-30"></i>
                                    <p class="text-base font-semibold text-gray-300">Belum ada jadwal dokter yang terdaftar.</p>
                                    <p class="text-xs text-gray-400 mt-1">Gunakan tombol "Tambah / Daftarkan Jadwal" di atas untuk mendaftarkan spesialisasi dan jam praktek Anda.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($schedules->hasPages())
                <div class="px-6 py-4 bg-black/20 border-t border-white/10">
                    {{ $schedules->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
