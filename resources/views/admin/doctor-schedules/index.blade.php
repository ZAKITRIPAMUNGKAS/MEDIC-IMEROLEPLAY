@extends('layouts.app')

@section('title', 'Jadwal Praktek Dokter - Panel Admin')

@section('content')
<div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700"></div>
    <div class="absolute inset-0 bg-black bg-opacity-20"></div>

    <div class="relative max-w-7xl w-full mx-auto text-white">
        <!-- Header Section -->
        <div class="glass-effect rounded-2xl elegant-shadow-lg p-4 sm:p-6 md:p-8 mb-6 sm:mb-8">
            <div class="flex flex-col space-y-4 sm:space-y-0 sm:flex-row sm:items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-2">Jadwal Praktek Dokter</h1>
                    <p class="text-sky-200 text-base sm:text-lg">Kelola jadwal praktek dokter di setiap poli</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('admin.doctor-schedules.create') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white rounded-lg font-semibold transition-all duration-300 shadow-lg">
                        <i class="fas fa-plus mr-2"></i><span>Tambah Jadwal</span>
                    </a>
                </div>
            </div>

            <!-- Search and Filter -->
            <form method="GET" action="{{ route('admin.doctor-schedules.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari dokter atau poli..." 
                           class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 text-sm">
                </div>
                <div>
                    <select name="poli" class="w-full bg-white/10 text-white border border-white/20 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 appearance-none text-sm">
                        <option value="">Semua Poli</option>
                        @foreach($poliList as $poli)
                            <option value="{{ $poli }}" @selected(request('poli') == $poli) class="bg-slate-800 text-slate-100">{{ $poli }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white rounded-lg font-semibold transition-all duration-300 text-sm">
                        <i class="fas fa-search mr-2"></i><span>Filter</span>
                    </button>
                    <a href="{{ route('admin.doctor-schedules.index') }}" class="inline-flex items-center justify-center px-4 py-3 bg-white/10 text-white rounded-lg border border-white/20 hover:bg-white/20 transition-all duration-300">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>

        @if(session('success'))
        <div class="mb-6 px-6 py-4 bg-green-500/20 border border-green-500/30 text-green-300 rounded-xl">
            {{ session('success') }}
        </div>
        @endif

        <!-- Schedule List -->
        <div class="glass-effect rounded-2xl elegant-shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-black/20">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Poliklinik</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Dokter</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Hari & Jam</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Lokasi</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse($schedules as $schedule)
                            <tr class="hover:bg-white/5 transition-colors duration-200">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-sky-300">{{ $schedule->poli }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-user-md text-white text-xs"></i>
                                        </div>
                                        <div class="text-sm font-medium text-white">{{ $schedule->doctor_name }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs text-blue-200 font-medium mb-1">
                                        @foreach($schedule->day as $d)
                                            <span class="inline-block bg-white/10 px-2 py-0.5 rounded-md mr-1 mb-1">{{ $d }}</span>
                                        @endforeach
                                    </div>
                                    <div class="text-sm text-gray-300">
                                        <i class="far fa-clock mr-1 text-sky-400"></i>
                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $schedule->hospital == 'alta' ? 'bg-blue-500/20 text-blue-300' : 'bg-purple-500/20 text-purple-300' }}">
                                        <i class="fas fa-hospital mr-1"></i>
                                        {{ $schedule->hospital == 'alta' ? 'Alta Hospital' : 'Roxwood Hospital' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <form method="POST" action="{{ route('admin.doctor-schedules.toggle-active', $schedule) }}">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium cursor-pointer transition-all duration-300 {{ $schedule->is_active ? 'bg-green-500/20 text-green-300 hover:bg-green-500/30' : 'bg-red-500/20 text-red-300 hover:bg-red-500/30' }}">
                                            <i class="fas fa-{{ $schedule->is_active ? 'check-circle' : 'times-circle' }} mr-1"></i>
                                            {{ $schedule->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <a href="{{ route('admin.doctor-schedules.edit', $schedule) }}" class="text-sky-400 hover:text-sky-300 transition-colors">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.doctor-schedules.destroy', $schedule) }}" onsubmit="return confirm('Hapus jadwal ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-300 transition-colors">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                    <i class="fas fa-calendar-times text-4xl mb-4 opacity-20"></i>
                                    <p>Belum ada jadwal dokter yang terdaftar.</p>
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
