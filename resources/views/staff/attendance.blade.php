@extends('layouts.app')

@section('title', 'Absensi - Portal Medis MPK-BA')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Absensi & Jam Kerja</h1>
        <p class="text-gray-600">Kelola kehadiran dan jam kerja Anda</p>
    </div>

    <!-- Today's Status -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Status Hari Ini</h3>
        
        @if($todaySessions->count() > 0)
            <!-- Today's Sessions Summary -->
            <div class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div class="text-center bg-blue-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-500">Total Sesi</p>
                        <p class="text-2xl font-semibold text-blue-600">{{ $todaySessions->count() }}</p>
                    </div>
                    <div class="text-center bg-green-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-500">Total Jam Kerja</p>
                        <p class="text-2xl font-semibold text-green-600">{{ \App\Helpers\TimeHelper::formatDuration($todayTotalHours) }}</p>
                    </div>
                    <div class="text-center bg-purple-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-500">Status</p>
                        <p class="text-lg font-semibold {{ $activeSession ? 'text-yellow-600' : 'text-green-600' }}">
                            {{ $activeSession ? 'Sedang Bekerja' : 'Selesai' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Warning for Active Session from Previous Day -->
            @if($anyActiveSession && !$activeSession)
                <div class="mb-6 p-4 bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-xl shadow-sm">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-clock text-white"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-semibold text-amber-800 mb-1">Sesi Aktif dari Hari Sebelumnya</h4>
                            <p class="text-xs text-amber-700 mb-1">Anda memiliki sesi aktif yang belum di-clock out dari {{ $anyActiveSession->work_date->format('d/m/Y') }}</p>
                            <p class="text-xs text-amber-600 font-medium">Durasi: {{ \App\Helpers\TimeHelper::formatDuration($anyActiveSession->calculateTotalHours()) }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Today's Sessions List -->
            <div class="mb-6">
                <h4 class="text-md font-medium text-gray-900 mb-3">Sesi Hari Ini</h4>
                <div class="space-y-3">
                    @foreach($todaySessions as $session)
                        <div class="flex items-center justify-between p-3 border rounded-lg {{ $session->is_active ? 'bg-yellow-50 border-yellow-200' : 'bg-gray-50 border-gray-200' }}">
                            <div class="flex items-center space-x-4">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $session->is_active ? 'bg-yellow-100 text-yellow-600' : 'bg-green-100 text-green-600' }}">
                                    <span class="text-sm font-semibold">{{ $session->session_number }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        Sesi {{ $session->session_number }} 
                                        <span class="text-xs text-gray-500">({{ ucfirst($session->session_type) }})</span>
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $session->clock_in->format('H:i') }} - 
                                        {{ $session->clock_out ? $session->clock_out->format('H:i') : 'Masih aktif' }}
                                        @if($session->session_duration)
                                            ({{ \App\Helpers\TimeHelper::formatDuration($session->session_duration) }})
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                @if($session->is_active)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Aktif
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Selesai
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            @if($activeSession)
                <!-- Active Session Actions -->
                <div class="text-center bg-yellow-50 p-4 rounded-lg">
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Sesi Aktif - Sesi {{ $activeSession->session_number }}</h4>
                    <p class="text-gray-600 mb-4">
                        Clock In: {{ $activeSession->clock_in->format('H:i') }}<br>
                        Durasi: {{ $activeSession->clock_in->diffForHumans() }}
                    </p>
                    <form method="POST" action="{{ route('staff.attendance.clock-out') }}" class="inline" id="clockOutForm">
                        @csrf
                        <div class="mb-4">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan Clock Out</label>
                            <textarea id="notes" name="notes" rows="2" 
                                      class="w-full md:w-96 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                                      placeholder="Tambahkan catatan untuk sesi ini..."></textarea>
                        </div>
                        <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-md hover:bg-red-700 transition-colors" id="clockOutBtn">
                            <span class="btn-text">
                                <i class="fas fa-stop mr-2"></i>Clock Out
                            </span>
                            <span class="btn-loading hidden">
                                <i class="fas fa-spinner fa-spin mr-2"></i>Memproses...
                            </span>
                        </button>
                    </form>
                </div>
            @else
                <!-- Start New Session -->
                <div class="text-center bg-gray-50 p-4 rounded-lg">
                @if($anyActiveSession && !$activeSession)
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Lanjutkan Sesi dari Hari Sebelumnya</h4>
                    <p class="text-gray-600 mb-4">Lanjutkan sesi aktif dari {{ $anyActiveSession->work_date->format('d/m/Y') }}</p>
                @else
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Mulai Sesi Baru</h4>
                    <p class="text-gray-600 mb-4">Mulai sesi kerja berikutnya</p>
                @endif
                    <form method="POST" action="{{ route('staff.attendance.clock-in') }}" class="inline">
                        @csrf
                        <div class="mb-4">
                            <label for="session_type" class="block text-sm font-medium text-gray-700 mb-2">Jenis Sesi</label>
                            <select id="session_type" name="session_type" class="w-full md:w-48 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="work">Kerja</option>
                                <option value="break">Istirahat</option>
                                <option value="meeting">Meeting</option>
                                <option value="overtime">Lembur</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan Clock In</label>
                            <textarea id="notes" name="notes" rows="2" 
                                      class="w-full md:w-96 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                                      placeholder="Tambahkan catatan untuk sesi ini..."></textarea>
                        </div>
                        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700">
                            @if($anyActiveSession && !$activeSession)
                                Lanjutkan Sesi
                            @else
                                Clock In
                            @endif
                        </button>
                    </form>
                </div>
            @endif
        @else
            <!-- No Sessions Today -->
            <div class="text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h4 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Sesi Hari Ini</h4>
                <p class="text-gray-600 mb-4">Mulai sesi kerja pertama Anda hari ini</p>
                <form method="POST" action="{{ route('staff.attendance.clock-in') }}" class="inline">
                    @csrf
                    <div class="mb-4">
                        <label for="session_type" class="block text-sm font-medium text-gray-700 mb-2">Jenis Sesi</label>
                        <select id="session_type" name="session_type" class="w-full md:w-48 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="work">Kerja</option>
                            <option value="break">Istirahat</option>
                            <option value="meeting">Meeting</option>
                            <option value="overtime">Lembur</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan Clock In</label>
                        <textarea id="notes" name="notes" rows="2" 
                                  class="w-full md:w-96 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                                  placeholder="Tambahkan catatan untuk sesi ini..."></textarea>
                    </div>
                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700">
                        Clock In
                    </button>
                </form>
            </div>
        @endif
    </div>

    <!-- Weekly Stats -->
    @if($weeklyStats && $weeklyStats->total_days > 0)
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Statistik Minggu Ini</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <p class="text-2xl font-semibold text-gray-900">{{ $weeklyStats->total_days }}</p>
                    <p class="text-sm text-gray-500">Hari Kerja</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-2xl font-semibold text-gray-900">{{ \App\Helpers\TimeHelper::formatDuration($weeklyStats->total_hours) }}</p>
                    <p class="text-sm text-gray-500">Total Jam</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <p class="text-2xl font-semibold text-gray-900">{{ \App\Helpers\TimeHelper::formatDuration($weeklyStats->avg_hours_per_day) }}</p>
                    <p class="text-sm text-gray-500">Rata-rata/Jam</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Attendance History -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Riwayat Absensi</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sesi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clock In</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clock Out</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($attendanceHistory as $attendance)
                        <tr class="{{ $attendance->is_active ? 'bg-yellow-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $attendance->work_date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $attendance->is_active ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                                    Sesi {{ $attendance->session_number }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @switch($attendance->session_type)
                                        @case('work') bg-green-100 text-green-800 @break
                                        @case('break') bg-orange-100 text-orange-800 @break
                                        @case('meeting') bg-purple-100 text-purple-800 @break
                                        @case('overtime') bg-red-100 text-red-800 @break
                                        @default bg-gray-100 text-gray-800
                                    @endswitch">
                                    {{ ucfirst($attendance->session_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $attendance->clock_in->format('H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $attendance->clock_out ? $attendance->clock_out->format('H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($attendance->session_duration)
                                    {{ \App\Helpers\TimeHelper::formatDuration($attendance->session_duration) }}
                                @elseif($attendance->clock_out)
                                    {{ \App\Helpers\TimeHelper::formatDuration($attendance->total_hours * 60) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($attendance->is_active)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Aktif
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Selesai
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $attendance->notes ? Str::limit($attendance->notes, 50) : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                Belum ada riwayat absensi
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($attendanceHistory->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $attendanceHistory->links() }}
            </div>
        @endif
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
            credentials: 'same-origin', // Include cookies for same-origin requests
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            redirect: 'follow'
        })
        .then(response => {
            if (response.status === 419) {
                // CSRF token expired
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
                // Success - redirect to dashboard
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
            
            // Reset button state
            const btnText = clockOutBtn.querySelector('.btn-text');
            const btnLoading = clockOutBtn.querySelector('.btn-loading');
            
            btnText.classList.remove('hidden');
            btnLoading.classList.add('hidden');
            clockOutBtn.disabled = false;
            
            // Show error message
            alert('Terjadi kesalahan saat clock out. Silakan coba lagi atau refresh halaman.');
        });
    }
    
    function refreshCsrfToken() {
        return fetch('/csrf-token', {
            method: 'GET',
            credentials: 'same-origin', // Include cookies for same-origin requests
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.csrf_token) {
                // Update meta tag
                const metaTag = document.querySelector('meta[name="csrf-token"]');
                if (metaTag) {
                    metaTag.setAttribute('content', data.csrf_token);
                }
                
                // Update CSRF token input
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
