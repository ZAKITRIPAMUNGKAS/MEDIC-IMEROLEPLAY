@extends('layouts.app')

@section('title', 'Salary Reimbursement Tracking')

@section('content')
    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            {{-- Header --}}
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">
                    <i class="fas fa-file-invoice-dollar mr-3"></i>Salary Reimbursement Tracking
                </h1>
                <p class="text-sky-200">Monitor salary payments by management and track reimbursement status</p>
            </div>

            {{-- Summary Stats --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg p-6 border border-white border-opacity-20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sky-200 text-sm font-medium">Pending</p>
                            <h3 class="text-2xl font-bold text-white mt-1">
                                {!! \App\Helpers\PayrollHelper::formatCurrency($summary['total_pending']) !!}</h3>
                            <p class="text-yellow-300 text-xs mt-1">{{ $summary['pending_count'] }} records</p>
                        </div>
                        <div class="bg-yellow-500 bg-opacity-20 rounded-lg p-3">
                            <i class="fas fa-hourglass-half text-2xl text-yellow-300"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg p-6 border border-white border-opacity-20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sky-200 text-sm font-medium">Reimbursed</p>
                            <h3 class="text-2xl font-bold text-white mt-1">
                                {!! \App\Helpers\PayrollHelper::formatCurrency($summary['total_reimbursed']) !!}</h3>
                            <p class="text-emerald-300 text-xs mt-1">{{ $summary['reimbursed_count'] }} records</p>
                        </div>
                        <div class="bg-emerald-500 bg-opacity-20 rounded-lg p-3">
                            <i class="fas fa-check-circle text-2xl text-emerald-300"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg p-6 border border-white border-opacity-20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sky-200 text-sm font-medium">Total Paid</p>
                            <h3 class="text-2xl font-bold text-white mt-1">
                                {!! \App\Helpers\PayrollHelper::formatCurrency($summary['total_pending'] + $summary['total_reimbursed']) !!}
                            </h3>
                            <p class="text-sky-300 text-xs mt-1">
                                {{ $summary['pending_count'] + $summary['reimbursed_count'] }} records</p>
                        </div>
                        <div class="bg-sky-500 bg-opacity-20 rounded-lg p-3">
                            <i class="fas fa-money-bill-wave text-2xl text-sky-300"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg p-6 border border-white border-opacity-20">
                    <button onclick="openCalculateModal()" class="w-full h-full flex items-center justify-center flex-col">
                        <i class="fas fa-calculator text-3xl text-cyan-300 mb-2"></i>
                        <span class="text-white font-semibold">Calculate Period</span>
                        <span class="text-sky-200 text-xs mt-1">Generate new records</span>
                    </button>
                </div>
            </div>

            {{-- Filters --}}
        <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg p-6 mb-6 border border-white border-opacity-20">
            <form method="GET" action="{{ route('admin.reimbursements.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sky-200 text-sm font-medium mb-2">Minggu</label>
                    <input type="week" name="week" value="{{ request('week') }}" 
                        class="w-full px-4 py-2 rounded-lg bg-white bg-opacity-10 border border-white border-opacity-20 text-white placeholder-gray-400 focus:outline-none focus:border-sky-400"
                        placeholder="Pilih minggu">
                </div>
                <div>
                    <label class="block text-sky-200 text-sm font-medium mb-2">Manager</label>
                    <select name="manager_id" class="w-full px-4 py-2 rounded-lg bg-white bg-opacity-10 border border-white border-opacity-20 text-white focus:outline-none focus:border-sky-400">
                        <option value="">Semua Manager</option>
                        @foreach($managers as $manager)
                            <option value="{{ $manager->id }}" {{ request('manager_id') == $manager->id ? 'selected' : '' }}>
                                {{ $manager->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sky-200 text-sm font-medium mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 rounded-lg bg-white bg-opacity-10 border border-white border-opacity-20 text-white focus:outline-none focus:border-sky-400">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="reimbursed" {{ request('status') == 'reimbursed' ? 'selected' : '' }}>Reimbursed</option>
                    </select>
                </div>
                <div class="md:col-span-3 flex gap-2">
                    <button type="submit" class="bg-sky-500 bg-opacity-80 hover:bg-opacity-100 text-white px-6 py-2 rounded-lg font-medium transition-all">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                    <a href="{{ route('admin.reimbursements.index') }}" class="bg-gray-500 bg-opacity-50 hover:bg-opacity-70 text-white px-6 py-2 rounded-lg font-medium transition-all">
                        <i class="fas fa-times mr-2"></i>Clear
                    </a>
                </div>
            </form>
        </div>

            {{-- Reimbursements Table --}}
            <div
                class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg overflow-hidden border border-white border-opacity-20">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-white divide-opacity-10">
                        <thead class="bg-white bg-opacity-5">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-sky-200 uppercase tracking-wider">
                                    Manager</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-sky-200 uppercase tracking-wider">
                                    Period</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-sky-200 uppercase tracking-wider">
                                    Payrolls</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-sky-200 uppercase tracking-wider">
                                    Total Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-sky-200 uppercase tracking-wider">
                                    Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-sky-200 uppercase tracking-wider">
                                    Reimbursed By</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-sky-200 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white divide-opacity-10">
                            @forelse($reimbursements as $reimbursement)
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
                                                <div class="text-sm font-medium text-white">{{ $reimbursement->manager->name }}
                                                </div>
                                                <div class="text-xs text-sky-300">
                                                    {{ $reimbursement->manager->role->name ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-white">{{ $reimbursement->period_description }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-white">{{ $reimbursement->payroll_count }} gaji</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="text-sm font-semibold text-emerald-300">{!! $reimbursement->formatted_amount !!}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($reimbursement->is_reimbursed)
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-emerald-500 bg-opacity-20 text-emerald-300">
                                                <i class="fas fa-check mr-1"></i> Reimbursed
                                            </span>
                                        @else
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-500 bg-opacity-20 text-yellow-300">
                                                <i class="fas fa-clock mr-1"></i> Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($reimbursement->reimbursedBy)
                                            <div class="text-sm text-white">{{ $reimbursement->reimbursedBy->name }}</div>
                                            <div class="text-xs text-sky-300">
                                                {{ $reimbursement->reimbursed_at->format('d M Y H:i') }}</div>
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('admin.reimbursements.show', $reimbursement) }}"
                                            class="text-sky-300 hover:text-sky-200 mr-3">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        @if(!$reimbursement->is_reimbursed)
                                            <button onclick="markAsReimbursed({{ $reimbursement->id }})"
                                                class="text-emerald-300 hover:text-emerald-200">
                                                <i class="fas fa-check-circle"></i> Reimburse
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                        <i class="fas fa-inbox text-4xl mb-3"></i>
                                        <p>No reimbursement records found.</p>
                                        <button onclick="openCalculateModal()" class="mt-4 text-sky-300 hover:text-sky-200">
                                            <i class="fas fa-plus-circle mr-1"></i>Calculate Period
                                        </button>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($reimbursements->hasPages())
                    <div class="px-6 py-4 border-t border-white border-opacity-10">
                        {{ $reimbursements->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Calculate Period Modal --}}
    <div id="calculateModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div
            class="bg-gradient-to-br from-sky-900 to-sky-800 rounded-2xl p-8 max-w-mdw-full mx-4 border border-sky-400 border-opacity-30">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h3 class="text-2xl font-bold text-white mb-2">
                        <i class="fas fa-calculator mr-2"></i>Calculate Period
                    </h3>
                    <p class="text-sky-200 text-sm">Generate reimbursement records for a period</p>
                </div>
                <button onclick="closeCalculateModal()" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="calculateForm" onsubmit="submitCalculate(event)">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sky-200 text-sm font-medium mb-2">Pilih Minggu</label>
                    <input type="week" id="calc_week" required 
                        class="w-full px-4 py-2 rounded-lg bg-white bg-opacity-10 border border-white border-opacity-20 text-white focus:outline-none focus:border-sky-400">
                </div>
                <div class="flex gap-3 pt-4">
                    <button type="submit" class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-3 rounded-lg font-medium transition-all">
                        <i class="fas fa-check mr-2"></i>Calculate
                    </button>
                    <button type="button" onclick="closeCalculateModal()" 
                        class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-all">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                </div>
            </div>
        </form>
        </div>
    </div>

    <script>
        function openCalculateModal() {
            document.getElementById('calculateModal').classList.remove('hidden');
            document.getElementById('calculateModal').classList.add('flex');
        }

        function closeCalculateModal() {
            document.getElementById('calculateModal').classList.add('hidden');
            document.getElementById('calculateModal').classList.remove('flex');
        }

        function submitCalculate(event) {
    event.preventDefault();
    
    const weekInput = document.getElementById('calc_week').value;
    if (!weekInput) {
        alert('Silakan pilih minggu');
        return;
    }
    
    // Convert week input (YYYY-Www) to start and end dates
    const [year, week] = weekInput.split('-W');
    const firstDayOfYear = new Date(year, 0, 1);
    const daysOffset = (parseInt(week) - 1) * 7;
    const weekStart = new Date(firstDayOfYear.getTime() + daysOffset * 24 * 60 * 60 * 1000);
    
    // Adjust to Monday (week starts on Monday)
    const dayOfWeek = weekStart.getDay();
    const diff = dayOfWeek === 0 ? -6 : 1 - dayOfWeek;
    weekStart.setDate(weekStart.getDate() + diff);
    
    const weekEnd = new Date(weekStart);
    weekEnd.setDate(weekStart.getDate() + 6); // Sunday
    
    const periodStart = weekStart.toISOString().split('T')[0];
    const periodEnd = weekEnd.toISOString().split('T')[0];
    
    fetch('{{ route("admin.reimbursements.calculate") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            period_start: periodStart,
            period_end: periodEnd
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
        function markAsReimbursed(reimbursementId) {
            if (!confirm('Are you sure you want to mark this as reimbursed?')) {
                return;
            }

            const notes = prompt('Enter notes (optional):');

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