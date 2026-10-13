@extends('layouts.app')

@section('title', 'Detail Gaji - Portal Medis MPK-BA')

@section('content')
<div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700"></div>
    <div class="absolute inset-0 bg-black bg-opacity-20"></div>

    <div class="relative max-w-4xl w-full mx-auto text-white">
        <!-- Header Section -->
        <div class="glass-effect rounded-2xl elegant-shadow-lg p-4 sm:p-6 md:p-8 mb-6 sm:mb-8">
            <div class="flex flex-col space-y-4 sm:space-y-0 sm:flex-row sm:items-center justify-between mb-6">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-2">Detail Gaji</h1>
                    <p class="text-sky-200 text-base sm:text-lg">Informasi lengkap gaji staf</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.payroll.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold transition-all duration-300">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                    @if($payroll->status === 'pending')
                        <button onclick="markAsPaid({{ $payroll->id }})" 
                                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white rounded-lg font-semibold transition-all duration-300">
                            <i class="fas fa-check mr-2"></i>Tandai Dibayar
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Payroll Information -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8 mb-6 sm:mb-8">
            <!-- Staff Information -->
            <div class="glass-effect rounded-2xl elegant-shadow-lg p-4 sm:p-6">
                <h3 class="text-lg sm:text-xl font-semibold text-white mb-4">Informasi Staf</h3>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-12 w-12 rounded-full bg-gradient-to-r from-sky-400 to-blue-500 flex items-center justify-center">
                            <span class="text-white text-lg font-medium">{{ substr($payroll->user->name, 0, 2) }}</span>
                        </div>
                        <div class="ml-4">
                            <div class="text-lg font-medium text-white">{{ $payroll->user->name }}</div>
                            <div class="text-sm text-gray-300">{{ $payroll->user->email }}</div>
                        </div>
                    </div>
                    <div class="border-t border-white/10 pt-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-300">Role</p>
                                <p class="text-white font-medium">{{ $payroll->user->role->display_name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-300">Status Akun</p>
                                <p class="text-white font-medium">
                                    @if($payroll->user->is_active)
                                        <span class="text-green-400">Aktif</span>
                                    @else
                                        <span class="text-red-400">Tidak Aktif</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payroll Status -->
            <div class="glass-effect rounded-2xl elegant-shadow-lg p-4 sm:p-6">
                <h3 class="text-lg sm:text-xl font-semibold text-white mb-4">Status Gaji</h3>
                <div class="space-y-4">
                    <div class="text-center">
                        @if($payroll->status === 'paid')
                            <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                <i class="fas fa-check-circle mr-2 text-emerald-400"></i>Dibayar
                            </div>
                        @elseif($payroll->status === 'pending')
                            <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                <i class="fas fa-clock mr-2 text-amber-400"></i>Pending
                            </div>
                        @else
                            <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                <i class="fas fa-times-circle mr-2 text-rose-400"></i>Dibatalkan
                            </div>
                        @endif
                    </div>
                    
                    @if($payroll->paid_at)
                        <div class="border-t border-white/10 pt-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-300">Dibayar Pada</p>
                                    <p class="text-white font-medium">{{ $payroll->paid_at->format('d M Y H:i') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-300">Dibayar Oleh</p>
                                    <p class="text-white font-medium">{{ $payroll->paidBy->name ?? 'Admin' }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Payroll Details -->
        <div class="glass-effect rounded-2xl elegant-shadow-lg p-4 sm:p-6 mb-6 sm:mb-8">
            <h3 class="text-lg sm:text-xl font-semibold text-white mb-6">Detail Gaji</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <div class="text-center">
                    <p class="text-sm text-gray-300 mb-2">Periode</p>
                    <p class="text-lg font-semibold text-white">{{ $payroll->period_description }}</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-300 mb-2">Total Jam</p>
                    <p class="text-lg font-semibold text-white">{{ $payroll->formatted_hours }}</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-300 mb-2">Gaji Pokok per Minggu</p>
                    <p class="text-lg font-semibold text-white">{{ $payroll->formatted_base_salary }}</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-300 mb-2">Gaji Dihitung</p>
                    <p class="text-xl font-bold text-green-400">{{ $payroll->formatted_salary }}</p>
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
        @php
            $displayNotifications = $payroll->notifications->unique(function ($n) {
                return $n->notification_type . '_' . $n->message;
            });
        @endphp
        @if($displayNotifications->count() > 0)
            <div class="glass-effect rounded-2xl elegant-shadow-lg p-4 sm:p-6">
                <h3 class="text-lg sm:text-xl font-semibold text-white mb-4">Riwayat Notifikasi</h3>
                <div class="space-y-3">
                    @foreach($displayNotifications as $notification)
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
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                        @if($notification->status === 'sent') bg-emerald-500/20 text-emerald-300 border border-emerald-500/30
                                        @elseif($notification->status === 'failed') bg-rose-500/20 text-rose-300 border border-rose-500/30
                                        @else bg-amber-500/20 text-amber-300 border border-amber-500/30 @endif">
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

<!-- Mark as Paid Modal -->
<div id="markPaidModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-slate-900/95 backdrop-blur-xl border border-sky-400/30 rounded-3xl p-6 sm:p-8 w-full max-w-md shadow-2xl text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-400/30 text-emerald-400 flex items-center justify-center text-lg font-bold">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">Tandai sebagai Dibayar</h3>
                        <p class="text-xs text-sky-200">Konfirmasi status pembayaran staf</p>
                    </div>
                </div>
                <button type="button" onclick="closeMarkPaidModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-gray-300 hover:text-white transition-colors">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            <form id="markPaidForm" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Catatan (Opsional)</label>
                        <textarea name="notes" rows="3" 
                                  class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-3 focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 text-white placeholder-gray-400 text-sm transition-all"
                                  placeholder="Tambahkan catatan untuk pembayaran ini..."></textarea>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeMarkPaidModal()" 
                            class="px-5 py-2.5 text-gray-300 hover:text-white text-sm font-medium rounded-xl hover:bg-white/10 transition-colors">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hover:from-emerald-600 hover:to-teal-700 text-sm font-bold transition-all duration-300 shadow-lg">
                        Tandai Dibayar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function markAsPaid(payrollId) {
    document.getElementById('markPaidForm').action = `/admin/payroll/${payrollId}/mark-paid`;
    document.getElementById('markPaidModal').classList.remove('hidden');
}

function closeMarkPaidModal() {
    document.getElementById('markPaidModal').classList.add('hidden');
}
</script>
@endsection
