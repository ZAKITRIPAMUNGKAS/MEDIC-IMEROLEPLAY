@extends('layouts.app')

@section('title', 'Detail Reimbursement')

@section('content')
    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto">
            {{-- Back Button --}}
            <div class="mb-6">
                <a href="{{ route('admin.reimbursements.index') }}" class="text-sky-300 hover:text-sky-200">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali ke List
                </a>
            </div>

            {{-- Header --}}
            <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg p-6 mb-6 border border-white border-opacity-20">
                <div class="flex items-start justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-2">
                            <i class="fas fa-file-invoice-dollar mr-3"></i>Detail Reimbursement
                        </h1>
                        <p class="text-sky-200">{{ $reimbursement->period_description }}</p>
                    </div>
                    <div>
                        @if($reimbursement->is_reimbursed)
                            <span
                                class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-emerald-500 bg-opacity-20 text-emerald-300">
                                <i class="fas fa-check mr-2"></i> Sudah Direimburse
                            </span>
                        @else
                            <span
                                class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-500 bg-opacity-20 text-yellow-300">
                                <i class="fas fa-clock mr-2"></i> Belum Direimburse
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg p-6 border border-white border-opacity-20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sky-200 text-sm font-medium">Manager</p>
                            <h3 class="text-xl font-bold text-white mt-1">{{ $reimbursement->manager->name }}</h3>
                            <p class="text-sky-300 text-xs mt-1">{{ $reimbursement->manager->role->name ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-sky-500 bg-opacity-20 rounded-lg p-3">
                            <i class="fas fa-user text-2xl text-sky-300"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg p-6 border border-white border-opacity-20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sky-200 text-sm font-medium">Total Gaji</p>
                            <h3 class="text-xl font-bold text-emerald-300 mt-1">{!! $reimbursement->formatted_amount !!}
                            </h3>
                            <p class="text-sky-300 text-xs mt-1">{{ $reimbursement->payroll_count }} payroll</p>
                        </div>
                        <div class="bg-emerald-500 bg-opacity-20 rounded-lg p-3">
                            <i class="fas fa-money-bill-wave text-2xl text-emerald-300"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg p-6 border border-white border-opacity-20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sky-200 text-sm font-medium">Direimburse Oleh</p>
                            @if($reimbursement->reimbursedBy)
                                <h3 class="text-xl font-bold text-white mt-1">{{ $reimbursement->reimbursedBy->name }}</h3>
                                <p class="text-sky-300 text-xs mt-1">{{ $reimbursement->reimbursed_at->format('d M Y H:i') }}
                                </p>
                            @else
                                <h3 class="text-xl font-bold text-gray-400 mt-1">-</h3>
                                <p class="text-sky-300 text-xs mt-1">Belum direimburse</p>
                            @endif
                        </div>
                        <div class="bg-purple-500 bg-opacity-20 rounded-lg p-3">
                            <i class="fas fa-user-check text-2xl text-purple-300"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Notes (if any) --}}
            @if($reimbursement->notes)
                <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg p-6 mb-6 border border-white border-opacity-20">
                    <h3 class="text-lg font-bold text-white mb-3">
                        <i class="fas fa-sticky-note mr-2"></i>Catatan
                    </h3>
                    <p class="text-sky-200">{{ $reimbursement->notes }}</p>
                </div>
            @endif

            {{-- Payroll Breakdown --}}
            <div
                class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg overflow-hidden border border-white border-opacity-20">
                <div class="px-6 py-4 bg-white bg-opacity-5 border-b border-white border-opacity-10">
                    <h3 class="text-lg font-bold text-white">
                        <i class="fas fa-list mr-2"></i>Detail Gaji yang Dibayarkan ({{ $payrolls->count() }})
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-white divide-opacity-10">
                        <thead class="bg-white bg-opacity-5">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-sky-200 uppercase tracking-wider">
                                    Staff</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-sky-200 uppercase tracking-wider">
                                    Periode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-sky-200 uppercase tracking-wider">
                                    Total Jam</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-sky-200 uppercase tracking-wider">
                                    Gaji</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-sky-200 uppercase tracking-wider">
                                    Dibayar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white divide-opacity-10">
                            @forelse($payrolls as $payroll)
                                <tr class="hover:bg-white hover:bg-opacity-5 transition-all">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div
                                                    class="h-10 w-10 rounded-full bg-sky-500 bg-opacity-20 flex items-center justify-center">
                                                    <i class="fas fa-user text-sky-300"></i>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-white">{{ $payroll->user->name }}</div>
                                                <div class="text-xs text-sky-300">{{ $payroll->user->role->name ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-white">{{ $payroll->period_description }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-sky-300">{{ $payroll->formatted_hours }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="text-sm font-semibold text-emerald-300">{!! $payroll->formatted_salary !!}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-white">{{ $payroll->paid_at->format('d M Y') }}</div>
                                        <div class="text-xs text-sky-300">{{ $payroll->paid_at->format('H:i') }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                        <i class="fas fa-inbox text-4xl mb-3"></i>
                                        <p>Tidak ada data payroll</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-white bg-opacity-5">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right">
                                    <span class="text-lg font-bold text-white">TOTAL:</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="text-lg font-bold text-emerald-300">{!! $reimbursement->formatted_amount !!}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-sky-300">{{ $payrolls->count() }} gaji</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Action Button --}}
            @if(!$reimbursement->is_reimbursed)
                <div class="mt-6 flex justify-end">
                    <button onclick="markAsReimbursed({{ $reimbursement->id }})"
                        class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-3 rounded-lg font-medium transition-all">
                        <i class="fas fa-check-circle mr-2"></i>Tandai Sudah Direimburse
                    </button>
                </div>
            @endif
        </div>
    </div>

    <script>
        function markAsReimbursed(reimbursementId) {
            if (!confirm('Apakah Anda yakin ingin menandai ini sebagai sudah direimburse?')) {
                return;
            }

            const notes = prompt('Catatan (opsional):');

            fetch(`/admin/reimbursements/${reimbursementId}/reimburse`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    notes: notes
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        window.location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    alert('Error: ' + error.message);
                });
        }
    </script>
@endsection