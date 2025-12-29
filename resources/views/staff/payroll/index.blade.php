@extends('layouts.app')

@section('title', 'Gaji Saya - Portal Medis MPK-BA')

@section('content')
<div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700"></div>
    <div class="absolute inset-0 bg-black bg-opacity-20"></div>

    <div class="relative max-w-7xl w-full mx-auto text-white">
        <!-- Header Section -->
        <div class="glass-effect rounded-2xl elegant-shadow-lg p-4 sm:p-6 md:p-8 mb-6 sm:mb-8">
            <div class="flex flex-col space-y-4 sm:space-y-0 sm:flex-row sm:items-center justify-between mb-6">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-2">Gaji Saya</h1>
                    <p class="text-sky-200 text-base sm:text-lg">Lihat riwayat dan status gaji Anda</p>
                </div>
                <div class="text-right">
                    <p class="text-gray-300 text-sm">Total Gaji</p>
                    <p class="text-xl sm:text-2xl font-bold text-white">{{ $summary['total_payrolls'] }}</p>
                </div>
            </div>

            <!-- Filter Section -->
            <form method="GET" action="{{ route('staff.payroll.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                    <select name="status" class="w-full bg-white/10 text-white border border-white/20 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 appearance-none text-sm">
                        <option value="">Semua Status</option>
                        <option value="pending" @selected($filters['status'] == 'pending') class="bg-slate-800 text-slate-100">Pending</option>
                        <option value="paid" @selected($filters['status'] == 'paid') class="bg-slate-800 text-slate-100">Dibayar</option>
                        <option value="cancelled" @selected($filters['status'] == 'cancelled') class="bg-slate-800 text-slate-100">Dibatalkan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Periode Mulai</label>
                    <input type="date" name="period_start" value="{{ $filters['period_start'] }}" 
                           class="w-full bg-white/10 text-white border border-white/20 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Periode Akhir</label>
                    <input type="date" name="period_end" value="{{ $filters['period_end'] }}" 
                           class="w-full bg-white/10 text-white border border-white/20 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 text-sm">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-gradient-to-r from-sky-500 to-blue-500 hover:from-sky-600 hover:to-blue-600 text-white rounded-lg px-4 py-3 font-semibold transition-all duration-300 shadow-lg">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
            <div class="glass-effect rounded-xl elegant-shadow-lg p-4 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-list text-white text-sm sm:text-base"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-300 text-xs sm:text-sm font-medium">Total Gaji</p>
                        <p class="text-white text-lg sm:text-xl font-bold">{{ $summary['total_payrolls'] }}</p>
                    </div>
                </div>
            </div>

            <div class="glass-effect rounded-xl elegant-shadow-lg p-4 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-white text-sm sm:text-base"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-300 text-xs sm:text-sm font-medium">Pending</p>
                        <p class="text-white text-lg sm:text-xl font-bold">{{ $summary['pending_payrolls'] }}</p>
                    </div>
                </div>
            </div>

            <div class="glass-effect rounded-xl elegant-shadow-lg p-4 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-circle text-white text-sm sm:text-base"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-300 text-xs sm:text-sm font-medium">Dibayar</p>
                        <p class="text-white text-lg sm:text-xl font-bold">{{ $summary['paid_payrolls'] }}</p>
                    </div>
                </div>
            </div>

            <div class="glass-effect rounded-xl elegant-shadow-lg p-4 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-dollar-sign text-white text-sm sm:text-base"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-300 text-xs sm:text-sm font-medium">Total Dibayar</p>
                        <p class="text-white text-lg sm:text-xl font-bold">$ {{ number_format($summary['total_amount'], 0, '.', ',') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Notifications -->
        @if($recentNotifications->count() > 0)
            <div class="glass-effect rounded-2xl elegant-shadow-lg p-4 sm:p-6 mb-6 sm:mb-8">
                <h3 class="text-lg sm:text-xl font-semibold text-white mb-4">Notifikasi Terbaru</h3>
                <div class="space-y-3">
                    @foreach($recentNotifications as $notification)
                        <div class="bg-white/5 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-white font-medium">
                                        @if($notification->notification_type === 'salary_paid')
                                            <i class="fas fa-check-circle text-green-400 mr-2"></i>Gaji Dibayar
                                        @elseif($notification->notification_type === 'salary_pending')
                                            <i class="fas fa-clock text-yellow-400 mr-2"></i>Gaji Pending
                                        @else
                                            <i class="fas fa-bell text-blue-400 mr-2"></i>Reminder Gaji
                                        @endif
                                    </p>
                                    <p class="text-sm text-gray-300 mt-1">{{ $notification->message }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-400">
                                        {{ $notification->sent_at ? $notification->sent_at->format('d M Y H:i') : '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Payroll Table -->
        <div class="glass-effect rounded-2xl elegant-shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10">
                    <thead class="bg-white/5">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Periode</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Total Jam</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Gaji</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Dibayar</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse($payrolls as $payroll)
                            <tr class="table-row-hover transition-all duration-200">
                                <td class="px-4 sm:px-6 py-4">
                                    <div class="text-sm sm:text-base text-white">{{ $payroll->period_description }}</div>
                                </td>
                                <td class="px-4 sm:px-6 py-4">
                                    <div class="text-sm sm:text-base text-white">{{ $payroll->formatted_hours }}</div>
                                </td>
                                <td class="px-4 sm:px-6 py-4">
                                    <div class="text-sm sm:text-base font-semibold text-green-400">{{ $payroll->formatted_salary }}</div>
                                </td>
                                <td class="px-4 sm:px-6 py-4">
                                    @if($payroll->status === 'paid')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>Dibayar
                                        </span>
                                    @elseif($payroll->status === 'pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i>Pending
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i>Dibatalkan
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 sm:px-6 py-4">
                                    @if($payroll->paid_at)
                                        <div class="text-sm text-white">{{ $payroll->paid_at ? $payroll->paid_at->format('d M Y') : '-' }}</div>
                                        <div class="text-xs text-gray-300">oleh {{ optional($payroll->paidBy)->name ?? 'Admin' }}</div>
                                    @else
                                        <div class="text-sm text-gray-400">-</div>
                                    @endif
                                </td>
                                <td class="px-4 sm:px-6 py-4">
                                    <a href="{{ route('staff.payroll.show', $payroll->id) }}" 
                                       class="text-sky-400 hover:text-sky-300 transition-colors duration-200">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 sm:px-6 py-8 text-center text-gray-400">
                                    <i class="fas fa-inbox text-4xl mb-4"></i>
                                    <p class="text-lg">Tidak ada data gaji</p>
                                    <p class="text-sm">Gaji akan muncul setelah admin generate gaji untuk periode Anda</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($payrolls->hasPages())
                <div class="px-4 sm:px-6 py-4 border-t border-white/10">
                    {{ $payrolls->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
