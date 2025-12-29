@extends('layouts.app')

@section('title', 'Detail Gaji Saya - Portal Medis MPK-BA')

@section('content')
<div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700"></div>
    <div class="absolute inset-0 bg-black bg-opacity-20"></div>

    <div class="relative max-w-4xl w-full mx-auto text-white">
        <!-- Header Section -->
        <div class="glass-effect rounded-2xl elegant-shadow-lg p-4 sm:p-6 md:p-8 mb-6 sm:mb-8">
            <div class="flex flex-col space-y-4 sm:space-y-0 sm:flex-row sm:items-center justify-between mb-6">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-2">Detail Gaji Saya</h1>
                    <p class="text-sky-200 text-base sm:text-lg">Informasi lengkap gaji Anda</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('staff.payroll.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold transition-all duration-300">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Payroll Status -->
        <div class="glass-effect rounded-2xl elegant-shadow-lg p-4 sm:p-6 mb-6 sm:mb-8">
            <div class="text-center">
                <h3 class="text-lg sm:text-xl font-semibold text-white mb-4">Status Gaji</h3>
                <div class="mb-6">
                    @if($payroll->status === 'paid')
                        <div class="inline-flex items-center px-6 py-3 rounded-full text-lg font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-3"></i>Gaji Sudah Dibayar
                        </div>
                    @elseif($payroll->status === 'pending')
                        <div class="inline-flex items-center px-6 py-3 rounded-full text-lg font-medium bg-yellow-100 text-yellow-800">
                            <i class="fas fa-clock mr-3"></i>Menunggu Pembayaran
                        </div>
                    @else
                        <div class="inline-flex items-center px-6 py-3 rounded-full text-lg font-medium bg-red-100 text-red-800">
                            <i class="fas fa-times-circle mr-3"></i>Gaji Dibatalkan
                        </div>
                    @endif
                </div>
                
                @if($payroll->paid_at)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-md mx-auto">
                        <div class="bg-white/5 rounded-lg p-4">
                            <p class="text-sm text-gray-300">Dibayar Pada</p>
                            <p class="text-white font-medium">{{ $payroll->paid_at->format('d M Y H:i') }}</p>
                        </div>
                        <div class="bg-white/5 rounded-lg p-4">
                            <p class="text-sm text-gray-300">Dibayar Oleh</p>
                            <p class="text-white font-medium">{{ $payroll->paidBy->name ?? 'Admin' }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Payroll Details -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8 mb-6 sm:mb-8">
            <!-- Period Information -->
            <div class="glass-effect rounded-2xl elegant-shadow-lg p-4 sm:p-6">
                <h3 class="text-lg sm:text-xl font-semibold text-white mb-4">Informasi Periode</h3>
                <div class="space-y-4">
                    <div class="text-center">
                        <p class="text-sm text-gray-300 mb-2">Periode Kerja</p>
                        <p class="text-xl font-bold text-white">{{ $payroll->period_description }}</p>
                    </div>
                    <div class="border-t border-white/10 pt-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center">
                                <p class="text-sm text-gray-300">Tanggal Mulai</p>
                                <p class="text-white font-medium">{{ $payroll->period_start->format('d M Y') }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-gray-300">Tanggal Akhir</p>
                                <p class="text-white font-medium">{{ $payroll->period_end->format('d M Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Salary Information -->
            <div class="glass-effect rounded-2xl elegant-shadow-lg p-4 sm:p-6">
                <h3 class="text-lg sm:text-xl font-semibold text-white mb-4">Informasi Gaji</h3>
                <div class="space-y-4">
                    <div class="text-center">
                        <p class="text-sm text-gray-300 mb-2">Total Jam Kerja</p>
                        <p class="text-xl font-bold text-white">{{ $payroll->formatted_hours }}</p>
                    </div>
                    <div class="border-t border-white/10 pt-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center">
                                <p class="text-sm text-gray-300">Gaji Pokok</p>
                                <p class="text-white font-medium">{{ $payroll->formatted_base_salary }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-gray-300">Gaji Dihitung</p>
                                <p class="text-lg font-bold text-green-400">{{ $payroll->formatted_salary }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Role Information -->
        <div class="glass-effect rounded-2xl elegant-shadow-lg p-4 sm:p-6 mb-6 sm:mb-8">
            <h3 class="text-lg sm:text-xl font-semibold text-white mb-4">Informasi Role</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-white/5 rounded-lg p-4">
                    <p class="text-sm text-gray-300 mb-2">Role</p>
                    <p class="text-white font-medium">{{ $payroll->user->role->display_name ?? 'N/A' }}</p>
                </div>
                <div class="bg-white/5 rounded-lg p-4">
                    <p class="text-sm text-gray-300 mb-2">Base Salary per Minggu</p>
                    <p class="text-white font-medium">{{ $payroll->formatted_base_salary }}</p>
                </div>
            </div>
        </div>

        <!-- Notes -->
        @if($payroll->notes)
            <div class="glass-effect rounded-2xl elegant-shadow-lg p-4 sm:p-6 mb-6 sm:mb-8">
                <h3 class="text-lg sm:text-xl font-semibold text-white mb-4">Catatan</h3>
                <div class="bg-white/5 rounded-lg p-4">
                    <p class="text-white">{{ $payroll->notes }}</p>
                </div>
            </div>
        @endif

        <!-- Notifications -->
        @if($payroll->notifications->count() > 0)
            <div class="glass-effect rounded-2xl elegant-shadow-lg p-4 sm:p-6">
                <h3 class="text-lg sm:text-xl font-semibold text-white mb-4">Riwayat Notifikasi</h3>
                <div class="space-y-3">
                    @foreach($payroll->notifications as $notification)
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
                                        @if($notification->sent_at)
                                            {{ $notification->sent_at->format('d M Y H:i') }}
                                        @else
                                            Belum dikirim
                                        @endif
                                    </p>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        @if($notification->status === 'sent') bg-green-100 text-green-800
                                        @elseif($notification->status === 'failed') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ ucfirst($notification->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
