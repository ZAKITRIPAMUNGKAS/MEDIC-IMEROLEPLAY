@extends('layouts.app')

@section('title', 'Duty Tracking & Ranking')

@section('content')
<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">
                <i class="fas fa-trophy mr-3"></i>Duty Tracking & Ranking
            </h1>
            <p class="text-sky-200">Leaderboard dan tracking duty staff berdasarkan total waktu</p>
        </div>

        {{-- Month Selector --}}
        <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg p-6 mb-6 border border-white border-opacity-20">
            <form method="GET" action="{{ route('admin.duty-tracking.index') }}" id="monthForm">
                <div class="mb-4">
                    <label class="block text-sky-200 text-sm font-medium mb-3">
                        <i class="fas fa-calendar-alt mr-2"></i>Pilih Bulan (bisa multiple):
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                        @foreach($availableMonths as $month)
                            <label class="flex items-center bg-white bg-opacity-5 hover:bg-opacity-10 p-3 rounded-lg cursor-pointer transition-all border border-white border-opacity-10">
                                <input type="checkbox" name="months[]" value="{{ $month }}" 
                                    {{ in_array($month, $selectedMonths) ? 'checked' : '' }}
                                    class="mr-2 rounded text-sky-500 focus:ring-sky-400">
                                <span class="text-white text-sm">{{ \Carbon\Carbon::parse($month . '-01')->format('M Y') }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-sky-500 hover:bg-sky-600 text-white px-6 py-2 rounded-lg font-medium transition-all">
                        <i class="fas fa-filter mr-2"></i>Tampilkan
                    </button>
                    <a href="{{ route('admin.duty-tracking.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium transition-all">
                        <i class="fas fa-redo mr-2"></i>Reset
                    </a>
                    <button type="button" onclick="selectAll()" class="bg-purple-500 hover:bg-purple-600 text-white px-6 py-2 rounded-lg font-medium transition-all">
                        <i class="fas fa-check-double mr-2"></i>Pilih Semua
                    </button>
                </div>
            </form>
        </div>

        {{-- Selected Months Display --}}
        @if(!empty($selectedMonths))
            <div class="mb-6">
                <div class="flex flex-wrap gap-2">
                    <span class="text-sky-200">Periode:</span>
                    @foreach($selectedMonths as $month)
                        <span class="px-3 py-1 bg-sky-500 bg-opacity-20 text-sky-300 rounded-full text-sm">
                            {{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg p-6 border border-white border-opacity-20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sky-200 text-sm font-medium">Total Staff</p>
                        <h3 class="text-3xl font-bold text-white mt-1">{{ $stats['total_staff'] }}</h3>
                    </div>
                    <div class="bg-sky-500 bg-opacity-20 rounded-lg p-3">
                        <i class="fas fa-users text-2xl text-sky-300"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg p-6 border border-white border-opacity-20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sky-200 text-sm font-medium">Total Duty</p>
                        <h3 class="text-3xl font-bold text-emerald-300 mt-1">{{ number_format($stats['total_duty_seconds'] / 3600, 1) }}h</h3>
                    </div>
                    <div class="bg-emerald-500 bg-opacity-20 rounded-lg p-3">
                        <i class="fas fa-clock text-2xl text-emerald-300"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg p-6 border border-white border-opacity-20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sky-200 text-sm font-medium">Total Sessions</p>
                        <h3 class="text-3xl font-bold text-purple-300 mt-1">{{ number_format($stats['total_sessions']) }}</h3>
                    </div>
                    <div class="bg-purple-500 bg-opacity-20 rounded-lg p-3">
                        <i class="fas fa-clipboard-list text-2xl text-purple-300"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg p-6 border border-white border-opacity-20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sky-200 text-sm font-medium">Rata-rata</p>
                        <h3 class="text-3xl font-bold text-yellow-300 mt-1">{{ number_format(($stats['avg_duty_seconds'] ?? 0) / 3600, 1) }}h</h3>
                    </div>
                    <div class="bg-yellow-500 bg-opacity-20 rounded-lg p-3">
                        <i class="fas fa-chart-bar text-2xl text-yellow-300"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Rankings Table --}}
        <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg overflow-hidden border border-white border-opacity-20">
            <div class="px-6 py-4 bg-white bg-opacity-5 border-b border-white border-opacity-10">
                <h3 class="text-lg font-bold text-white">
                    <i class="fas fa-medal mr-2"></i>Leaderboard Duty Time
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white divide-opacity-10">
                    <thead class="bg-white bg-opacity-5">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-sky-200 uppercase tracking-wider">Rank</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-sky-200 uppercase tracking-wider">Staff</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-sky-200 uppercase tracking-wider">Total Duty</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-sky-200 uppercase tracking-wider">Sessions</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-sky-200 uppercase tracking-wider">Avg/Session</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-sky-200 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white divide-opacity-10">
                        @forelse($rankings as $index => $user)
                            <tr class="hover:bg-black hover:bg-opacity-20 transition-all">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($rankings->currentPage() == 1 && $index == 0)
                                            <span class="text-3xl">🥇</span>
                                        @elseif($rankings->currentPage() == 1 && $index == 1)
                                            <span class="text-3xl">🥈</span>
                                        @elseif($rankings->currentPage() == 1 && $index == 2)
                                            <span class="text-3xl">🥉</span>
                                        @else
                                            <span class="text-xl font-bold text-gray-400">#{{ $rankings->firstItem() + $index }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-sky-500 bg-opacity-20 flex items-center justify-center">
                                                <i class="fas fa-user text-sky-300"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-white">{{ $user->name }}</div>
                                            <div class="text-xs text-sky-300">{{ $user->role->name ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-lg font-bold text-emerald-300">
                                        {{ number_format($user->total_duty_seconds / 3600, 1) }} jam
                                    </div>
                                    <div class="text-xs text-sky-300">
                                        {{ gmdate('H:i:s', $user->total_duty_seconds) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-white">{{ number_format($user->session_count) }} sesi</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-yellow-300">{{ number_format($user->avg_duty_seconds / 3600, 1) }} jam</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('admin.duty-tracking.show', ['user' => $user->id, 'months' => $selectedMonths]) }}" 
                                        class="text-sky-300 hover:text-sky-200">
                                        <i class="fas fa-eye mr-1"></i>Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                    <i class="fas fa-inbox text-4xl mb-3"></i>
                                    <p>Tidak ada data duty untuk periode yang dipilih</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($rankings->hasPages())
                <div class="px-6 py-4 border-t border-white border-opacity-10">
                    {{ $rankings->appends(['months' => $selectedMonths])->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function selectAll() {
    const checkboxes = document.querySelectorAll('input[name="months[]"]');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
}
</script>
@endsection
