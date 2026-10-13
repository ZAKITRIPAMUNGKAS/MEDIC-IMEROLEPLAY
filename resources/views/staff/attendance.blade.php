@extends('layouts.app')

@section('title', 'Absensi - Portal Medis MPK-BA')

@section('content')
<div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8 font-sans">
    <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700"></div>
    <div class="absolute inset-0 bg-black bg-opacity-20"></div>

    <div class="relative max-w-7xl w-full mx-auto text-white">
        <!-- Header -->
        <div class="glass-effect rounded-2xl elegant-shadow-lg p-4 sm:p-6 md:p-8 mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-2">Absensi & Jam Kerja</h1>
                    <p class="text-sky-200 text-base sm:text-lg">Kelola kehadiran dan jam kerja Anda</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-semibold {{ $activeSession ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' }}">
                        <i class="fas {{ $activeSession ? 'fa-spinner fa-spin' : 'fa-check-circle' }} mr-1.5"></i>
                        {{ $activeSession ? 'Sedang Bertugas' : 'Standby / Off Duty' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Today's Status -->
        <div class="glass-effect rounded-2xl elegant-shadow-lg p-4 sm:p-6 md:p-8 mb-6 sm:mb-8">
            <h3 class="text-lg sm:text-xl font-semibold text-white mb-4">Status Hari Ini</h3>
            
            @if($todaySessions->count() > 0)
                <!-- Today's Sessions Summary -->
                <div class="mb-6">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                        <div class="text-center bg-white/10 border border-white/10 p-4 rounded-xl">
                            <p class="text-xs sm:text-sm text-gray-300">Total Sesi</p>
                            <p class="text-2xl font-bold text-sky-400 mt-1">{{ $todaySessions->count() }}</p>
                        </div>
                        <div class="text-center bg-white/10 border border-white/10 p-4 rounded-xl">
                            <p class="text-xs sm:text-sm text-gray-300">Total Jam Kerja</p>
                            <p class="text-2xl font-bold text-emerald-400 mt-1">{{ \App\Helpers\TimeHelper::formatDuration($todayTotalHours) }}</p>
                        </div>
                        <div class="text-center bg-white/10 border border-white/10 p-4 rounded-xl">
                            <p class="text-xs sm:text-sm text-gray-300">Status</p>
                            <p class="text-lg font-bold mt-1 {{ $activeSession ? 'text-amber-400' : 'text-emerald-400' }}">
                                {{ $activeSession ? 'Sedang Bekerja' : 'Selesai' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Warning for Active Session from Previous Day -->
                @if($anyActiveSession && !$activeSession)
                    <div class="mb-6 p-4 bg-amber-500/15 border border-amber-500/30 rounded-xl shadow-sm text-amber-200">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-amber-500/25 rounded-full flex items-center justify-center flex-shrink-0 text-amber-300 border border-amber-400/30">
                                <i class="fas fa-clock text-lg"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-white mb-0.5">Sesi Aktif dari Hari Sebelumnya</h4>
                                <p class="text-xs text-amber-200/90 mb-1">Anda memiliki sesi aktif yang belum di-clock out dari {{ $anyActiveSession->work_date->format('d/m/Y') }}</p>
                                <p class="text-xs text-amber-300 font-bold">Durasi: {{ \App\Helpers\TimeHelper::formatDuration($anyActiveSession->calculateTotalHours()) }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Today's Sessions List -->
                <div class="mb-6">
                    <h4 class="text-sm sm:text-base font-semibold text-sky-200 mb-3">Daftar Sesi Hari Ini</h4>
                    <div class="space-y-3">
                        @foreach($todaySessions as $session)
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3.5 border rounded-xl gap-3 {{ $session->is_active ? 'bg-amber-500/10 border-amber-500/30' : 'bg-white/5 border-white/10' }}">
                                <div class="flex items-center space-x-3 sm:space-x-4">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-sm {{ $session->is_active ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' }}">
                                        <span>#{{ $session->session_number }}</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-white">
                                            Sesi {{ $session->session_number }} 
                                            <span class="text-xs text-sky-300 font-normal">({{ ucfirst($session->session_type) }})</span>
                                        </p>
                                        <p class="text-xs text-gray-300">
                                            {{ $session->clock_in->format('H:i') }} - 
                                            {{ $session->clock_out ? $session->clock_out->format('H:i') : 'Masih aktif' }}
                                            @if($session->session_duration)
                                                <span class="text-emerald-300 font-medium">({{ \App\Helpers\TimeHelper::formatDuration($session->session_duration) }})</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right self-end sm:self-center">
                                    @if($session->is_active)
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                            <i class="fas fa-clock mr-1"></i>Aktif
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                            <i class="fas fa-check mr-1"></i>Selesai
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if($activeSession)
                    <!-- Active Session Actions -->
                    <div class="text-center bg-amber-500/10 border border-amber-500/20 p-5 rounded-2xl">
                        <h4 class="text-base sm:text-lg font-bold text-white mb-1">Sesi Aktif - Sesi {{ $activeSession->session_number }}</h4>
                        <p class="text-xs sm:text-sm text-sky-200 mb-4">
                            Clock In: <strong class="text-white">{{ $activeSession->clock_in->format('H:i') }}</strong> • Durasi: <strong class="text-white">{{ $activeSession->clock_in->diffForHumans() }}</strong>
                        </p>
                        <form method="POST" action="{{ route('staff.attendance.clock-out') }}" class="inline-block w-full max-w-md text-left" id="clockOutForm">
                            @csrf
                            <div class="mb-4">
                                <label for="notes" class="block text-xs sm:text-sm font-semibold text-gray-300 mb-1.5">Catatan Clock Out</label>
                                <textarea id="notes" name="notes" rows="2" 
                                          class="w-full px-3.5 py-2.5 bg-white/10 border border-white/20 text-white placeholder-gray-400 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-400 text-sm"
                                          placeholder="Tambahkan catatan untuk sesi ini..."></textarea>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white rounded-xl font-bold transition-all duration-300 shadow-lg" id="clockOutBtn">
                                    <span class="btn-text flex items-center">
                                        <i class="fas fa-stop mr-2"></i>Clock Out
                                    </span>
                                    <span class="btn-loading hidden flex items-center">
                                        <i class="fas fa-spinner fa-spin mr-2"></i>Memproses...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <!-- Start New Session -->
                    <div class="text-center bg-white/5 border border-white/10 p-5 rounded-2xl">
                    @if($anyActiveSession && !$activeSession)
                        <h4 class="text-base sm:text-lg font-bold text-white mb-1">Lanjutkan Sesi dari Hari Sebelumnya</h4>
                        <p class="text-xs sm:text-sm text-sky-200 mb-4">Lanjutkan sesi aktif dari {{ $anyActiveSession->work_date->format('d/m/Y') }}</p>
                    @else
                        <h4 class="text-base sm:text-lg font-bold text-white mb-1">Mulai Sesi Baru</h4>
                        <p class="text-xs sm:text-sm text-sky-200 mb-4">Mulai sesi kerja berikutnya</p>
                    @endif
                        <form method="POST" action="{{ route('staff.attendance.clock-in') }}" class="inline-block w-full max-w-md text-left">
                            @csrf
                            <div class="mb-4">
                                <label for="session_type" class="block text-xs sm:text-sm font-semibold text-gray-300 mb-1.5">Jenis Sesi</label>
                                <select id="session_type" name="session_type" class="w-full px-3.5 py-2.5 bg-white/10 border border-white/20 text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-400 text-sm">
                                    <option value="work" class="bg-slate-800 text-slate-100">Kerja</option>
                                    <option value="break" class="bg-slate-800 text-slate-100">Istirahat</option>
                                    <option value="meeting" class="bg-slate-800 text-slate-100">Meeting</option>
                                    <option value="overtime" class="bg-slate-800 text-slate-100">Lembur</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="notes" class="block text-xs sm:text-sm font-semibold text-gray-300 mb-1.5">Catatan Clock In</label>
                                <textarea id="notes" name="notes" rows="2" 
                                          class="w-full px-3.5 py-2.5 bg-white/10 border border-white/20 text-white placeholder-gray-400 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-400 text-sm"
                                          placeholder="Tambahkan catatan untuk sesi ini..."></textarea>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white rounded-xl font-bold transition-all duration-300 shadow-lg">
                                    <i class="fas fa-play mr-2"></i>
                                    @if($anyActiveSession && !$activeSession)
                                        Lanjutkan Sesi
                                    @else
                                        Clock In
                                    @endif
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            @else
                <!-- No Sessions Today -->
                <div class="text-center py-6">
                    <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-white/10 text-sky-400 shadow-inner">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                    <h4 class="text-lg sm:text-xl font-bold text-white mb-1">Belum Ada Sesi Hari Ini</h4>
                    <p class="text-sm text-sky-200 mb-6">Mulai sesi kerja pertama Anda hari ini</p>
                    <form method="POST" action="{{ route('staff.attendance.clock-in') }}" class="inline-block w-full max-w-md text-left">
                        @csrf
                        <div class="mb-4">
                            <label for="session_type" class="block text-xs sm:text-sm font-semibold text-gray-300 mb-1.5">Jenis Sesi</label>
                            <select id="session_type" name="session_type" class="w-full px-3.5 py-2.5 bg-white/10 border border-white/20 text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-400 text-sm">
                                <option value="work" class="bg-slate-800 text-slate-100">Kerja</option>
                                <option value="break" class="bg-slate-800 text-slate-100">Istirahat</option>
                                <option value="meeting" class="bg-slate-800 text-slate-100">Meeting</option>
                                <option value="overtime" class="bg-slate-800 text-slate-100">Lembur</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="notes" class="block text-xs sm:text-sm font-semibold text-gray-300 mb-1.5">Catatan Clock In</label>
                            <textarea id="notes" name="notes" rows="2" 
                                      class="w-full px-3.5 py-2.5 bg-white/10 border border-white/20 text-white placeholder-gray-400 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-400 text-sm"
                                      placeholder="Tambahkan catatan untuk sesi ini..."></textarea>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white rounded-xl font-bold transition-all duration-300 shadow-lg">
                                <i class="fas fa-play mr-2"></i>Clock In
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>

        <!-- Weekly Stats -->
        @if($weeklyStats && $weeklyStats->total_days > 0)
            <div class="glass-effect rounded-2xl elegant-shadow-lg p-4 sm:p-6 mb-6 sm:mb-8">
                <h3 class="text-lg sm:text-xl font-semibold text-white mb-4">Statistik Minggu Ini</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
                    <div class="text-center bg-white/5 border border-white/10 p-4 rounded-xl">
                        <div class="w-10 h-10 bg-sky-500/20 text-sky-400 rounded-xl flex items-center justify-center mx-auto mb-2 border border-sky-400/30">
                            <i class="fas fa-calendar-check text-lg"></i>
                        </div>
                        <p class="text-2xl font-bold text-white">{{ $weeklyStats->total_days }}</p>
                        <p class="text-xs sm:text-sm text-gray-300 mt-0.5">Hari Kerja</p>
                    </div>
                    <div class="text-center bg-white/5 border border-white/10 p-4 rounded-xl">
                        <div class="w-10 h-10 bg-emerald-500/20 text-emerald-400 rounded-xl flex items-center justify-center mx-auto mb-2 border border-emerald-400/30">
                            <i class="fas fa-business-time text-lg"></i>
                        </div>
                        <p class="text-2xl font-bold text-emerald-300">{{ \App\Helpers\TimeHelper::formatDuration($weeklyStats->total_hours) }}</p>
                        <p class="text-xs sm:text-sm text-gray-300 mt-0.5">Total Jam</p>
                    </div>
                    <div class="text-center bg-white/5 border border-white/10 p-4 rounded-xl">
                        <div class="w-10 h-10 bg-purple-500/20 text-purple-400 rounded-xl flex items-center justify-center mx-auto mb-2 border border-purple-400/30">
                            <i class="fas fa-chart-line text-lg"></i>
                        </div>
                        <p class="text-2xl font-bold text-purple-300">{{ \App\Helpers\TimeHelper::formatDuration($weeklyStats->avg_hours_per_day) }}</p>
                        <p class="text-xs sm:text-sm text-gray-300 mt-0.5">Rata-rata/Hari</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Attendance History -->
        <div class="glass-effect rounded-2xl elegant-shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-white/10 bg-white/5">
                <h3 class="text-lg font-semibold text-white">Riwayat Absensi</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10">
                    <thead class="bg-black/20">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Tanggal</th>
                            <th class="hidden sm:table-cell px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Sumber</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Sesi</th>
                            <th class="hidden sm:table-cell px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Jenis</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Clock In</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Clock Out</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Durasi</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="hidden md:table-cell px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse($attendanceHistory as $attendance)
                            <tr class="hover:bg-white/5 transition-colors duration-200 {{ $attendance->is_active ? 'bg-amber-500/10' : '' }}">
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-white">
                                    {{ $attendance->work_date->format('d/m/Y') }}
                                </td>
                                <td class="hidden sm:table-cell px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-white">
                                    @if(isset($attendance->source) && $attendance->source === 'fivem')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-purple-500/20 text-purple-300 border border-purple-500/30">
                                            FiveM
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-white/10 text-gray-300 border border-white/20">
                                            Manual
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-white">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $attendance->is_active ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-sky-500/20 text-sky-300 border border-sky-500/30' }}">
                                        Sesi {{ $attendance->session_number }}
                                    </span>
                                </td>
                                <td class="hidden sm:table-cell px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-white">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold 
                                        @switch($attendance->session_type)
                                            @case('work') bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 @break
                                            @case('break') bg-orange-500/20 text-orange-300 border border-orange-500/30 @break
                                            @case('meeting') bg-purple-500/20 text-purple-300 border border-purple-500/30 @break
                                            @case('overtime') bg-rose-500/20 text-rose-300 border border-rose-500/30 @break
                                            @default bg-white/10 text-gray-300 border border-white/20
                                        @endswitch">
                                        {{ ucfirst($attendance->session_type) }}
                                    </span>
                                </td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-white">
                                    {{ $attendance->clock_in->format('H:i') }}
                                </td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    {{ $attendance->clock_out ? $attendance->clock_out->format('H:i') : '-' }}
                                </td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-emerald-300 font-semibold">
                                    @if($attendance->session_duration)
                                        {{ \App\Helpers\TimeHelper::formatDuration($attendance->session_duration) }}
                                    @elseif($attendance->clock_out)
                                        {{ \App\Helpers\TimeHelper::formatDuration($attendance->total_hours * 60) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                    @if($attendance->is_active)
                                        <span class="px-2.5 py-1 inline-flex text-xs font-semibold rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                            <i class="fas fa-clock mr-1 text-amber-400"></i>Aktif
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 inline-flex text-xs font-semibold rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                            <i class="fas fa-check mr-1 text-emerald-400"></i>Selesai
                                        </span>
                                    @endif
                                </td>
                                <td class="hidden md:table-cell px-4 sm:px-6 py-4 text-xs sm:text-sm text-gray-300 max-w-xs truncate">
                                    {{ $attendance->notes ? Str::limit($attendance->notes, 50) : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-8 text-center text-gray-400">
                                    <i class="fas fa-inbox text-3xl mb-2 text-gray-500"></i>
                                    <p>Belum ada riwayat absensi</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($attendanceHistory->hasPages())
                <div class="px-6 py-4 border-t border-white/10 bg-white/5">
                    {{ $attendanceHistory->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const clockOutForm = document.getElementById('clockOutForm');
    const clockOutBtn = document.getElementById('clockOutBtn');
    
    if (clockOutForm && clockOutBtn) {
        clockOutForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show loading state
            const btnText = clockOutBtn.querySelector('.btn-text');
            const btnLoading = clockOutBtn.querySelector('.btn-loading');
            
            btnText.classList.add('hidden');
            btnLoading.classList.remove('hidden');
            clockOutBtn.disabled = true;
            
            // Submit form with retry mechanism
            submitClockOutForm();
        });
    }
    
    function submitClockOutForm(retryCount = 0) {
        const form = document.getElementById('clockOutForm');
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            credentials: 'same-origin',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            redirect: 'follow'
        })
        .then(response => {
            if (response.status === 419) {
                if (retryCount < 2) {
                    console.log('CSRF token expired, refreshing and retrying...');
                    return refreshCsrfToken().then(() => {
                        return submitClockOutForm(retryCount + 1);
                    });
                } else {
                    throw new Error('CSRF token refresh failed after multiple attempts');
                }
            }
            return response;
        })
        .then(response => {
            if (response.ok) {
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    window.location.href = '{{ route("staff.dashboard") }}';
                }
            } else {
                throw new Error('Clock out failed');
            }
        })
        .catch(error => {
            console.error('Clock out error:', error);
            
            const btnText = clockOutBtn.querySelector('.btn-text');
            const btnLoading = clockOutBtn.querySelector('.btn-loading');
            
            btnText.classList.remove('hidden');
            btnLoading.classList.add('hidden');
            clockOutBtn.disabled = false;
            
            alert('Terjadi kesalahan saat clock out. Silakan coba lagi atau refresh halaman.');
        });
    }
    
    function refreshCsrfToken() {
        return fetch('/csrf-token', {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.csrf_token) {
                const metaTag = document.querySelector('meta[name="csrf-token"]');
                if (metaTag) {
                    metaTag.setAttribute('content', data.csrf_token);
                }
                const csrfInput = document.querySelector('input[name="_token"]');
                if (csrfInput) {
                    csrfInput.value = data.csrf_token;
                }
                console.log('CSRF token refreshed successfully');
            }
        });
    }
});
</script>
@endpush
