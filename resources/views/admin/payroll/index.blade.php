@extends('layouts.app')

@section('title', 'Manajemen Gaji - Portal Medis MPK-BA')

@section('content')
<div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700"></div>
    <div class="absolute inset-0 bg-black bg-opacity-20"></div>

    <div class="relative max-w-7xl w-full mx-auto text-white">
        <!-- Header Section -->
        <div class="glass-effect rounded-2xl elegant-shadow-lg p-4 sm:p-6 md:p-8 mb-6 sm:mb-8">
            <div class="flex flex-col space-y-4 sm:space-y-0 sm:flex-row sm:items-center justify-between mb-6">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-2">Manajemen Gaji</h1>
                    <p class="text-sky-200 text-base sm:text-lg">Kelola dan pantau pembayaran gaji staf</p>
                </div>
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <div class="text-right">
                        <p class="text-gray-300 text-sm">Total Gaji</p>
                        <p class="text-xl sm:text-2xl font-bold text-white">{{ $summary['total_payrolls'] ?? 0 }}</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <!-- Generate Payroll Button -->
                        @if(isset($canGenerateManually) && !$canGenerateManually)
                            <button disabled
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-3 bg-gray-400 text-white rounded-lg font-semibold cursor-not-allowed opacity-60"
                                    title="Generate gaji sudah dilakukan secara otomatis pada hari Minggu jam 23:59. Tidak dapat melakukan generate manual setelah auto-generate.">
                                <i class="fas fa-lock mr-2"></i><span class="hidden xs:inline">Generate Gaji (Otomatis)</span><span class="xs:hidden">Generate (Otomatis)</span>
                            </button>
                            @if(isset($lastWeekPayrollExists) && $lastWeekPayrollExists)
                                <div class="flex items-center px-3 py-2 bg-green-500/20 text-green-300 rounded-lg text-sm">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    <span>Auto-generated (Minggu 23:59)</span>
                                </div>
                            @endif
                        @else
                            <button onclick="openGenerateModal()" 
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white rounded-lg font-semibold transition-all duration-300 shadow-lg">
                                <i class="fas fa-plus mr-2"></i><span class="hidden xs:inline">Generate Gaji</span><span class="xs:hidden">Generate</span>
                            </button>
                        @endif
                        @if(isset($currentMonthExport) && $currentMonthExport)
                            <!-- Already Exported - Disabled Button -->
                            <button disabled
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-3 bg-gray-400 text-white rounded-lg font-semibold cursor-not-allowed opacity-60"
                                    title="Data gaji sudah di export bulan ini oleh {{ optional($currentMonthExport->exporter)->name ?? 'Unknown User' }} pada {{ $currentMonthExport->exported_at->format('d M Y H:i') }}">
                                <i class="fas fa-check-circle mr-2"></i>
                                <span class="hidden xs:inline">Sudah Di-Export</span>
                                <span class="xs:hidden">Exported</span>
                            </button>
                            <div class="hidden sm:flex items-center px-3 py-2 bg-green-500/20 text-green-300 rounded-lg text-sm">
                                <i class="fas fa-info-circle mr-2"></i>
                                <span>Export oleh <strong>{{ optional($currentMonthExport->exporter)->name ?? 'Unknown User' }}</strong> ({{ $currentMonthExport->exported_at->format('d M Y') }})</span>
                            </div>
                        @else
                            <!-- Can Export - Active Button -->
                            <a href="{{ route('admin.payroll.export', request()->query()) }}" 
                               class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white rounded-lg font-semibold transition-all duration-300 shadow-lg"
                               onclick="return confirm('Apakah Anda yakin ingin export data gaji?\n\nExport hanya bisa dilakukan 1x per bulan.')">
                                <i class="fas fa-download mr-2"></i>
                                <span class="hidden xs:inline">Export CSV</span>
                                <span class="xs:hidden">Export</span>
                            </a>
                        @endif
                        <button onclick="removeDuplicates()" 
                                class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-3 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white rounded-lg font-semibold transition-all duration-300 shadow-lg"
                                title="Hapus data gaji duplikat, simpan yang terbaru">
                            <i class="fas fa-trash-alt mr-2"></i><span class="hidden xs:inline">Hapus Duplikat</span><span class="xs:hidden">Duplikat</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Pending Meeting Requests Alert Banner -->
            @if(isset($pendingMeetingRequestsCount) && $pendingMeetingRequestsCount > 0)
                <div class="mb-6 p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-amber-500/20 to-orange-500/20 border border-amber-400/40 backdrop-blur-md shadow-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-start sm:items-center gap-3.5">
                        <div class="w-11 h-11 rounded-xl bg-amber-500/30 text-amber-300 flex items-center justify-center flex-shrink-0 border border-amber-400/40 shadow-inner">
                            <i class="fas fa-exclamation-triangle text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-white text-base flex items-center gap-2">
                                <span>Perhatian: Terdapat {{ $pendingMeetingRequestsCount }} Pengajuan Meeting Masih Pending</span>
                                <span class="px-2 py-0.5 text-xs rounded-full bg-amber-400 text-amber-950 font-black uppercase tracking-wider">Perlu Tindakan</span>
                            </h4>
                            <p class="text-sm text-amber-100/90 mt-0.5">
                                Generate gaji tidak dapat dilakukan jika terdapat pengajuan meeting yang belum di-ACC atau divalidasi pada periode tersebut agar jam kerja meeting staf masuk ke sistem penggajian.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('admin.meeting-requests.index', ['status' => 'pending']) }}" 
                       class="w-full sm:w-auto px-4 py-2.5 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-500 hover:to-amber-600 text-slate-900 font-bold rounded-xl text-sm transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-amber-500/25 flex-shrink-0">
                        <i class="fas fa-calendar-check"></i>
                        <span>Tinjau Meeting Request</span>
                    </a>
                </div>
            @endif

            <!-- Week Navigation -->
            <div class="mb-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Navigasi Minggu</h3>
                        <p class="text-sm text-gray-300">
                            @if(!empty($filters['week']))
                                Menampilkan minggu terpilih
                            @else
                                Menampilkan minggu ini ({{ \Carbon\Carbon::now()->startOfWeek()->format('d M Y') }} - {{ \Carbon\Carbon::now()->endOfWeek()->format('d M Y') }})
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-thin scrollbar-thumb-sky-600/40 w-full max-w-full">
                        @if(empty($filters['week']))
                            <a href="{{ route('admin.payroll.index', array_merge(request()->query(), ['week' => 'all'])) }}" 
                               class="whitespace-nowrap flex-shrink-0 px-4 py-2 rounded-lg text-sm font-medium bg-green-500/20 text-green-300 hover:bg-green-500/30 transition-all duration-200">
                                <i class="fas fa-calendar-alt mr-1"></i>Lihat Semua Minggu
                            </a>
                        @else
                            <a href="{{ route('admin.payroll.index', array_diff_key(request()->query(), ['week' => ''])) }}" 
                               class="whitespace-nowrap flex-shrink-0 px-4 py-2 rounded-lg text-sm font-medium bg-blue-500/20 text-blue-300 hover:bg-blue-500/30 transition-all duration-200">
                                <i class="fas fa-calendar-week mr-1"></i>Minggu Ini
                            </a>
                        @endif
                        
                        @if(!empty($filters['week']) && $filters['week'] !== 'all')
                            @foreach($availableWeeks ?? [] as $week)
                                <a href="{{ route('admin.payroll.index', array_merge(request()->query(), ['week' => $week['date'] ?? ''])) }}" 
                                   class="whitespace-nowrap flex-shrink-0 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ ($filters['week'] ?? '') == ($week['date'] ?? '') ? 'bg-sky-500 text-white shadow-lg font-bold' : 'bg-white/10 text-gray-300 hover:bg-white/20' }}">
                                    {{ $week['short_label'] ?? 'Unknown' }}
                                </a>
                            @endforeach
                        @elseif(!empty($filters['week']) && $filters['week'] === 'all')
                            @foreach($availableWeeks ?? [] as $week)
                                <a href="{{ route('admin.payroll.index', array_merge(request()->query(), ['week' => $week['date'] ?? ''])) }}" 
                                   class="whitespace-nowrap flex-shrink-0 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 bg-white/10 text-gray-300 hover:bg-white/20">
                                    {{ $week['short_label'] ?? 'Unknown' }}
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <form method="GET" action="{{ route('admin.payroll.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                    <select name="status" class="w-full bg-white/10 text-white border border-white/20 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 appearance-none text-sm">
                        <option value="">Semua Status</option>
                        <option value="pending" @selected(($filters['status'] ?? '') == 'pending') class="bg-slate-800 text-slate-100">Pending</option>
                        <option value="paid" @selected(($filters['status'] ?? '') == 'paid') class="bg-slate-800 text-slate-100">Dibayar</option>
                        <option value="cancelled" @selected(($filters['status'] ?? '') == 'cancelled') class="bg-slate-800 text-slate-100">Dibatalkan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Rumah Sakit</label>
                    <select name="hospital" class="w-full bg-white/10 text-white border border-white/20 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 appearance-none text-sm">
                        <option value="">Semua Rumah Sakit</option>
                        <option value="alta" @selected(($filters['hospital'] ?? '') == 'alta') class="bg-slate-800 text-slate-100">Alta</option>
                        <option value="roxwood" @selected(($filters['hospital'] ?? '') == 'roxwood') class="bg-slate-800 text-slate-100">Roxwood</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Nama Staf</label>
                    <input type="text" name="staff_name" value="{{ $filters['staff_name'] ?? '' }}" 
                           placeholder="Cari nama staf..."
                           class="w-full bg-white/10 text-white border border-white/20 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 text-sm placeholder-gray-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Dibayar Oleh</label>
                    <select name="paid_by" class="w-full bg-white/10 text-white border border-white/20 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 appearance-none text-sm">
                        <option value="">Semua</option>
                        @foreach($paidByUsers ?? [] as $user)
                            <option value="{{ $user->id }}" @selected(($filters['paid_by'] ?? '') == $user->id) class="bg-slate-800 text-slate-100">
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Minggu</label>
                    <select name="week" class="w-full bg-white/10 text-white border border-white/20 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 appearance-none text-sm">
                        <option value="">Minggu Ini</option>
                        <option value="all" @selected(($filters['week'] ?? '') == 'all') class="bg-slate-800 text-slate-100">Semua Minggu</option>
                        @foreach($availableWeeks ?? [] as $week)
                            <option value="{{ $week['date'] ?? '' }}" @selected(($filters['week'] ?? '') == ($week['date'] ?? '')) class="bg-slate-800 text-slate-100">
                                {{ $week['label'] ?? 'Unknown' }}
                            </option>
                        @endforeach
                    </select>
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
                        <p class="text-white text-lg sm:text-xl font-bold">{{ $summary['total_payrolls'] ?? 0 }}</p>
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
                        <p class="text-white text-lg sm:text-xl font-bold">{{ $summary['pending_payrolls'] ?? 0 }}</p>
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
                        <p class="text-white text-lg sm:text-xl font-bold">{{ $summary['paid_payrolls'] ?? 0 }}</p>
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
                        <p class="text-white text-lg sm:text-xl font-bold">$ {{ number_format($summary['total_amount'] ?? 0, 0, '.', ',') }}</p>
                    </div>
                </div>
            </div>
        </div>


        <!-- Payroll Table - Grouped by Week -->
        @if(isset($payrolls) && $payrolls->count() > 0)
            @foreach($payrolls as $weekStart => $weekPayrolls)
                <div class="glass-effect rounded-2xl elegant-shadow-lg overflow-hidden mb-6">
                    <!-- Week Header -->
                    <div class="bg-gradient-to-r from-sky-500/20 to-blue-500/20 px-4 sm:px-6 py-4 border-b border-white/10">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <h3 class="text-base sm:text-lg font-semibold text-white">
                                    <i class="fas fa-calendar-week mr-2 text-sky-400"></i>
                                    @if($weekStart !== 'unknown' && !empty($weekStart))
                                        @php
                                            try {
                                                $weekDate = \Carbon\Carbon::parse($weekStart);
                                                $weekStartFormatted = $weekDate->format('d M Y');
                                                $weekEndFormatted = $weekDate->copy()->endOfWeek()->format('d M Y');
                                            } catch (\Exception $e) {
                                                $weekStartFormatted = $weekStart;
                                                $weekEndFormatted = '';
                                            }
                                        @endphp
                                        @if(!empty($weekEndFormatted))
                                            Minggu {{ $weekStartFormatted }} - {{ $weekEndFormatted }}
                                        @else
                                            Minggu {{ $weekStartFormatted }}
                                        @endif
                                    @else
                                        Minggu Tidak Diketahui
                                    @endif
                                </h3>
                                <p class="text-xs sm:text-sm text-gray-300">{{ $weekPayrolls->count() ?? 0 }} gaji • Total: Rp {{ number_format($weekPayrolls->sum('calculated_salary') ?? 0, 0, ',', '.') }}</p>
                                @if($weekStart !== 'unknown' && $weekStart === now()->startOfWeek()->format('Y-m-d'))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-500/20 text-sky-300 border border-sky-400/30 mt-1">
                                        <i class="fas fa-star mr-1 text-sky-300"></i>Minggu Ini
                                    </span>
                                @endif
                            </div>
                            <div class="flex flex-wrap items-center justify-between sm:justify-end gap-3 sm:gap-4 w-full sm:w-auto">
                                <div class="text-right">
                                    <div class="text-xs sm:text-sm text-gray-300">Pending</div>
                                    <div class="text-base sm:text-lg font-bold text-amber-400">{{ $weekPayrolls->where('status', 'pending')->count() }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs sm:text-sm text-gray-300">Dibayar</div>
                                    <div class="text-base sm:text-lg font-bold text-emerald-400">{{ $weekPayrolls->where('status', 'paid')->count() }}</div>
                                </div>
                                @if($weekPayrolls->where('status', 'pending')->count() > 0)
                                    <button onclick="regenerateWeek('{{ $weekStart }}')" 
                                            class="px-3.5 py-2 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white rounded-lg text-xs sm:text-sm font-semibold transition-all duration-300 shadow-lg flex items-center gap-1.5"
                                            title="Regenerate semua gaji pending minggu ini">
                                        <i class="fas fa-sync-alt mr-1"></i>Regenerate Minggu
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Payroll Table for this week -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-white/10">
                            <thead class="bg-white/5">
                                <tr>
                                    <th class="px-3 sm:px-4 py-3 sm:py-4 text-center w-10">
                                        <input type="checkbox" onchange="toggleSelectWeek(this)" class="select-all-week rounded border-white/30 bg-white/10 text-sky-400 focus:ring-sky-400 cursor-pointer" title="Pilih Semua Pending Minggu Ini">
                                    </th>
                                    <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Staf</th>
                                    <th class="hidden sm:table-cell px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Periode</th>
                                    <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Total Jam</th>
                                    <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Gaji</th>
                                    <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="hidden md:table-cell px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Dibayar</th>
                                    <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                @foreach($weekPayrolls as $payroll)
                                    @php
                                        $userName = $payroll->user->name ?? 'N/A';
                                        $userEmail = $payroll->user->email ?? 'N/A';
                                        $userInitials = substr($userName, 0, 2);
                                    @endphp
                                    <tr class="table-row-hover transition-all duration-200" id="payroll-row-{{ $payroll->id }}">
                                        <td class="px-3 sm:px-4 py-4 text-center">
                                            @if($payroll->status === 'pending')
                                                <input type="checkbox" name="selected_payrolls[]" value="{{ $payroll->id }}" data-amount="{{ $payroll->calculated_salary }}" onchange="updateSelectedCount()" class="payroll-checkbox rounded border-white/30 bg-white/10 text-sky-400 focus:ring-sky-400 cursor-pointer">
                                            @else
                                                <i class="fas fa-check text-green-400 text-xs" title="Sudah Dibayar"></i>
                                            @endif
                                        </td>
                                        <td class="px-4 sm:px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8 sm:h-10 sm:w-10">
                                                    <div class="h-8 w-8 sm:h-10 sm:w-10 rounded-full bg-gradient-to-r from-sky-400 to-blue-500 flex items-center justify-center">
                                                        <span class="text-white text-xs sm:text-sm font-medium">{{ $userInitials }}</span>
                                                    </div>
                                                </div>
                                                <div class="ml-3 sm:ml-4 min-w-0">
                                                    <div class="text-sm sm:text-base font-medium text-white truncate max-w-[160px] sm:max-w-xs">{{ $userName }}</div>
                                                    <div class="text-xs sm:text-sm text-gray-300 truncate max-w-[160px] sm:max-w-xs">{{ $userEmail }}</div>
                                                    <div class="text-xs text-sky-300 sm:hidden mt-0.5">{{ $payroll->period_description ?? '-' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="hidden sm:table-cell px-4 sm:px-6 py-4">
                                            <div class="text-sm sm:text-base text-white">{{ $payroll->period_description ?? '-' }}</div>
                                        </td>
                                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm sm:text-base text-white">{{ $payroll->formatted_hours ?? '00:00:00' }}</div>
                                        </td>
                                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm sm:text-base font-semibold text-emerald-300">{{ $payroll->formatted_salary ?? '$ 0' }}</div>
                                        </td>
                                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                            @if($payroll->status === 'paid')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                                    <i class="fas fa-check-circle mr-1 text-emerald-400"></i>Dibayar
                                                </span>
                                            @elseif($payroll->status === 'pending')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                                    <i class="fas fa-clock mr-1 text-amber-400"></i>Pending
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                                    <i class="fas fa-times-circle mr-1 text-rose-400"></i>Dibatalkan
                                                </span>
                                            @endif
                                        </td>
                                        <td class="hidden md:table-cell px-4 sm:px-6 py-4">
                                            @if($payroll->paid_at)
                                                @php
                                                    try {
                                                        $paidAtFormatted = $payroll->paid_at instanceof \DateTimeInterface ? $payroll->paid_at->format('d M Y') : \Carbon\Carbon::parse($payroll->paid_at)->format('d M Y');
                                                    } catch (\Throwable $e) {
                                                        $paidAtFormatted = (string) ($payroll->paid_at ?? '-');
                                                    }
                                                @endphp
                                                <div class="text-sm text-white">{{ $paidAtFormatted }}</div>
                                                <div class="text-xs text-gray-300">oleh {{ $payroll->paidBy->name ?? 'Admin' }}</div>
                                            @else
                                                <div class="text-sm text-gray-400">-</div>
                                            @endif
                                        </td>
                                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ route('admin.payroll.show', $payroll) }}" 
                                                   class="p-1.5 text-sky-400 hover:text-sky-300 hover:bg-sky-500/20 rounded transition-colors duration-200"
                                                   title="Lihat Rincian Gaji">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($payroll->status === 'pending')
                                                    <button onclick="regeneratePayroll({{ $payroll->id }})" 
                                                            class="p-1.5 text-purple-400 hover:text-purple-300 hover:bg-purple-500/20 rounded transition-colors duration-200"
                                                            title="Regenerate - Hitung ulang dengan formula terbaru">
                                                        <i class="fas fa-sync-alt"></i>
                                                    </button>
                                                    <button type="button" onclick="markAsPaid({{ $payroll->id }}, '{{ addslashes($userName) }}', '{{ $payroll->formatted_salary ?? '$ 0' }}')" 
                                                            class="p-1.5 text-emerald-400 hover:text-emerald-300 hover:bg-emerald-500/20 rounded transition-colors duration-200"
                                                            title="Ceklis / Tandai Gaji Sudah Dibayar">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button onclick="cancelPayroll({{ $payroll->id }})" 
                                                            class="p-1.5 text-rose-400 hover:text-rose-300 hover:bg-rose-500/20 rounded transition-colors duration-200"
                                                            title="Batalkan Gaji">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                    <button onclick="deletePayroll({{ $payroll->id }})" 
                                                            class="p-1.5 text-orange-400 hover:text-orange-300 hover:bg-orange-500/20 rounded transition-colors duration-200"
                                                            title="Hapus Data Duplikat">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        @else
            <!-- Empty State -->
            <div class="glass-effect rounded-2xl elegant-shadow-lg p-8 text-center">
                <i class="fas fa-inbox text-6xl text-gray-400 mb-4"></i>
                <h3 class="text-xl font-semibold text-white mb-2">
                    @if(!empty($filters['week']) && $filters['week'] === 'all')
                        Tidak ada data gaji untuk semua minggu
                    @elseif(!empty($filters['week']))
                        Tidak ada data gaji untuk minggu terpilih
                    @else
                        Tidak ada data gaji untuk minggu ini
                    @endif
                </h3>
                <p class="text-gray-300 mb-6">
                    @if(!empty($filters['week']) && $filters['week'] === 'all')
                        Belum ada gaji yang di-generate untuk periode apapun
                    @elseif(!empty($filters['week']))
                        Belum ada gaji yang di-generate untuk minggu ini
                    @else
                        Belum ada gaji yang di-generate untuk minggu ini
                    @endif
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    @if(empty($filters['week']))
                        <a href="{{ route('admin.payroll.index', array_merge(request()->query(), ['week' => 'all'])) }}" 
                           class="bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 shadow-lg">
                            <i class="fas fa-calendar-alt mr-2"></i>Lihat Semua Minggu
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>


<!-- Floating Bulk Action Bar -->
<div id="bulkActionBar" class="fixed bottom-6 left-1/2 transform -translate-x-1/2 z-40 hidden transition-all duration-300">
    <div class="bg-slate-900/95 backdrop-blur-md border-2 border-emerald-400/60 text-white px-6 py-4 rounded-2xl shadow-2xl flex flex-col sm:flex-row items-center gap-4">
        <div class="flex items-center space-x-3 text-center sm:text-left">
            <div class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold">
                <i class="fas fa-check-double"></i>
            </div>
            <div>
                <span id="selectedCount" class="font-bold text-emerald-400 text-lg">0</span>
                <span class="text-sm text-gray-200">gaji dipilih</span>
                <span class="text-xs text-gray-300 block">Total: <strong id="selectedTotalAmount" class="text-emerald-300 text-sm">$ 0</strong></span>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <button type="button" onclick="openBulkMarkPaidModal()" class="px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold text-sm rounded-xl shadow-lg transition-all transform hover:scale-105 flex items-center">
                <i class="fas fa-check mr-2"></i>Bayar Semua yang Diceklis
            </button>
            <button type="button" onclick="clearSelectedCheckboxes()" class="px-3 py-2 text-gray-400 hover:text-white text-xs">
                Batal
            </button>
        </div>
    </div>
</div>

<!-- Single Mark as Paid Modal -->
<div id="markPaidModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="bg-slate-900/95 backdrop-blur-xl rounded-3xl p-6 sm:p-8 w-full max-w-md shadow-2xl mx-4 border border-sky-400/30 text-white">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-400/30 text-emerald-400 flex items-center justify-center text-lg font-bold">
                    <i class="fas fa-money-check-alt"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white">Tandai Gaji Dibayar</h3>
                    <p class="text-xs text-sky-200">Konfirmasi pembayaran gaji staf</p>
                </div>
            </div>
            <button type="button" onclick="closeMarkPaidModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-gray-300 hover:text-white transition-colors">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <div class="bg-white/5 border border-white/10 rounded-xl p-4 mb-4">
            <div class="flex justify-between text-sm mb-1.5">
                <span class="text-gray-400">Nama Staf:</span>
                <span id="singleStaffName" class="font-bold text-white">-</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-400">Nominal Gaji:</span>
                <span id="singleStaffAmount" class="font-extrabold text-emerald-300 text-base">$ 0</span>
            </div>
        </div>

        <form id="markPaidForm" method="POST" onsubmit="handleSingleMarkPaidSubmit(this)">
            @csrf
            <div class="space-y-4 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-2">Catatan Pembayaran (Opsional)</label>
                    <textarea name="notes" rows="3" 
                              class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-3 focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 text-white placeholder-gray-400 text-sm transition-all"
                              placeholder="Contoh: Ditransfer via Bank / Kasir"></textarea>
                </div>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeMarkPaidModal()" 
                        class="px-5 py-2.5 text-gray-300 hover:text-white text-sm font-medium rounded-xl hover:bg-white/10 transition-colors">
                    Batal
                </button>
                <button type="submit" id="singleMarkPaidSubmitBtn"
                        class="px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hover:from-emerald-600 hover:to-teal-700 text-sm font-bold transition-all duration-300 shadow-lg flex items-center">
                    <span class="btn-text flex items-center"><i class="fas fa-check mr-2"></i>Konfirmasi Bayar</span>
                    <span class="btn-loading hidden flex items-center"><i class="fas fa-spinner fa-spin mr-2"></i>Memproses...</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Mark as Paid Modal -->
<div id="bulkMarkPaidModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="bg-slate-900/95 backdrop-blur-xl rounded-3xl p-6 sm:p-8 w-full max-w-md shadow-2xl mx-4 border border-sky-400/30 text-white">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-400/30 text-emerald-400 flex items-center justify-center text-lg font-bold">
                    <i class="fas fa-check-double"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white">Bayar Gaji yang Diceklis</h3>
                    <p class="text-xs text-sky-200">Tandai banyak gaji sekaligus</p>
                </div>
            </div>
            <button type="button" onclick="closeBulkMarkPaidModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-gray-300 hover:text-white transition-colors">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <div class="bg-white/5 border border-white/10 rounded-xl p-4 mb-4">
            <div class="flex justify-between text-sm mb-1.5">
                <span class="text-gray-400">Total Staf Diceklis:</span>
                <span id="bulkModalCount" class="font-bold text-white">0 staf</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-400">Total Nominal:</span>
                <span id="bulkModalTotal" class="font-extrabold text-emerald-300 text-base">$ 0</span>
            </div>
        </div>

        <form id="bulkMarkPaidForm" method="POST" action="{{ route('admin.payroll.bulk-mark-paid') }}" onsubmit="handleBulkMarkPaidSubmit(this)">
            @csrf
            <div id="bulkPayrollInputs"></div>
            <div class="space-y-4 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-2">Catatan Pembayaran (Opsional)</label>
                    <textarea name="notes" rows="3" 
                              class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-3 focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 text-white placeholder-gray-400 text-sm transition-all"
                              placeholder="Contoh: Pembayaran serentak mingguan"></textarea>
                </div>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeBulkMarkPaidModal()" 
                        class="px-5 py-2.5 text-gray-300 hover:text-white text-sm font-medium rounded-xl hover:bg-white/10 transition-colors">
                    Batal
                </button>
                <button type="submit" id="bulkMarkPaidSubmitBtn"
                        class="px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hover:from-emerald-600 hover:to-teal-700 text-sm font-bold transition-all duration-300 shadow-lg flex items-center">
                    <span class="btn-text flex items-center"><i class="fas fa-check mr-2"></i>Bayar Semua</span>
                    <span class="btn-loading hidden flex items-center"><i class="fas fa-spinner fa-spin mr-2"></i>Memproses...</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Generate Payroll Modal -->
<div id="generateModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="bg-slate-900/95 backdrop-blur-xl rounded-3xl shadow-2xl max-w-lg w-full mx-4 border border-sky-400/30 text-white">
        <!-- Header -->
        <div class="px-8 py-6 border-b border-white/10 bg-gradient-to-r from-sky-500/20 to-blue-500/20 rounded-t-3xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-sky-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-plus text-white text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Generate Gaji</h3>
                        <p class="text-sm text-sky-200">Buat gaji untuk periode tertentu</p>
                    </div>
                </div>
                <button onclick="closeGenerateModal()" 
                        class="w-8 h-8 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center transition-all duration-200 text-gray-300 hover:text-white">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>
        
        <!-- Form -->
        <form id="generateForm" method="POST" action="{{ route('admin.payroll.generate') }}" class="p-8">
            @csrf
            
            <div class="space-y-6">
                <!-- Period Selection -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2 flex items-center">
                            <i class="fas fa-calendar-alt text-sky-400 mr-2"></i>Periode Mulai
                        </label>
                        <input type="date" name="period_start" required
                               value="{{ \Carbon\Carbon::now()->subWeek()->startOfWeek()->format('Y-m-d') }}"
                               class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 text-white transition-all text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2 flex items-center">
                            <i class="fas fa-calendar-check text-sky-400 mr-2"></i>Periode Akhir
                        </label>
                        <input type="date" name="period_end" required
                               value="{{ \Carbon\Carbon::now()->subWeek()->endOfWeek()->format('Y-m-d') }}"
                               class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 text-white transition-all text-sm">
                    </div>
                </div>
                
                <!-- Info Box -->
                <div class="bg-sky-500/10 border border-sky-400/30 rounded-xl p-3">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-info-circle text-sky-400"></i>
                        <span class="text-sm text-sky-200">Generate gaji untuk semua staf aktif (diurutkan berdasarkan nama)</span>
                    </div>
                </div>

                @if(isset($pendingMeetingRequestsCount) && $pendingMeetingRequestsCount > 0)
                    <div class="bg-amber-500/15 border border-amber-400/30 rounded-xl p-3.5">
                        <div class="flex items-start space-x-2.5">
                            <i class="fas fa-exclamation-triangle text-amber-400 mt-0.5 flex-shrink-0"></i>
                            <div class="text-xs text-amber-200 leading-relaxed">
                                <span class="font-bold">Perhatian:</span> Terdapat <strong>{{ $pendingMeetingRequestsCount }}</strong> pengajuan meeting yang masih pending. Jika berada di periode yang dipilih, generate gaji akan ditolak sampai seluruh meeting request di-ACC atau ditolak.
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            
            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-white/10">
                <button type="button" onclick="closeGenerateModal()" 
                        class="px-6 py-3 text-gray-300 hover:text-white font-medium transition-all duration-200 hover:bg-white/10 rounded-xl text-sm">
                    <i class="fas fa-times mr-2"></i>Batal
                </button>
                <button type="submit" 
                        class="px-8 py-3 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white font-semibold rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 text-sm">
                    <i class="fas fa-plus mr-2"></i>Generate Gaji
                </button>
            </div>
        </form>
    </div>
</div>

<script>

function markAsPaid(payrollId, staffName = '', formattedSalary = '') {
    const form = document.getElementById('markPaidForm');
    form.action = `/admin/payroll/${payrollId}/mark-paid`;
    
    document.getElementById('singleStaffName').textContent = staffName || 'Staf';
    document.getElementById('singleStaffAmount').textContent = formattedSalary || '$ 0';
    
    // Reset submit button state
    const submitBtn = document.getElementById('singleMarkPaidSubmitBtn');
    if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.querySelector('.btn-text')?.classList.remove('hidden');
        submitBtn.querySelector('.btn-loading')?.classList.add('hidden');
    }
    
    document.getElementById('markPaidModal').classList.remove('hidden');
}

function closeMarkPaidModal() {
    document.getElementById('markPaidModal').classList.add('hidden');
}

function handleSingleMarkPaidSubmit(form) {
    const submitBtn = document.getElementById('singleMarkPaidSubmitBtn');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.querySelector('.btn-text')?.classList.add('hidden');
        submitBtn.querySelector('.btn-loading')?.classList.remove('hidden');
    }
    return true;
}

// Bulk Selection Logic
function toggleSelectWeek(sourceEl) {
    const table = sourceEl.closest('table');
    if (!table) return;
    const checkboxes = table.querySelectorAll('.payroll-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = sourceEl.checked;
    });
    updateSelectedCount();
}

function updateSelectedCount() {
    const allChecked = document.querySelectorAll('.payroll-checkbox:checked');
    const count = allChecked.length;
    let totalAmount = 0;

    allChecked.forEach(cb => {
        const amt = parseFloat(cb.getAttribute('data-amount')) || 0;
        totalAmount += amt;
    });

    const bulkBar = document.getElementById('bulkActionBar');
    const selectedCountEl = document.getElementById('selectedCount');
    const selectedTotalEl = document.getElementById('selectedTotalAmount');

    if (count > 0) {
        bulkBar.classList.remove('hidden');
        selectedCountEl.textContent = count;
        selectedTotalEl.textContent = '$ ' + totalAmount.toLocaleString('en-US');
    } else {
        bulkBar.classList.add('hidden');
        // Uncheck all select-all headers
        document.querySelectorAll('.select-all-week').forEach(h => h.checked = false);
    }
}

function clearSelectedCheckboxes() {
    document.querySelectorAll('.payroll-checkbox').forEach(cb => cb.checked = false);
    document.querySelectorAll('.select-all-week').forEach(h => h.checked = false);
    updateSelectedCount();
}

function openBulkMarkPaidModal() {
    const allChecked = document.querySelectorAll('.payroll-checkbox:checked');
    if (allChecked.length === 0) {
        alert('Silakan pilih minimal satu data gaji yang ingin dibayar.');
        return;
    }

    const inputsContainer = document.getElementById('bulkPayrollInputs');
    inputsContainer.innerHTML = '';
    let totalAmount = 0;

    allChecked.forEach(cb => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'payroll_ids[]';
        input.value = cb.value;
        inputsContainer.appendChild(input);

        const amt = parseFloat(cb.getAttribute('data-amount')) || 0;
        totalAmount += amt;
    });

    document.getElementById('bulkModalCount').textContent = `${allChecked.length} staf`;
    document.getElementById('bulkModalTotal').textContent = '$ ' + totalAmount.toLocaleString('en-US');

    const submitBtn = document.getElementById('bulkMarkPaidSubmitBtn');
    if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.querySelector('.btn-text')?.classList.remove('hidden');
        submitBtn.querySelector('.btn-loading')?.classList.add('hidden');
    }

    document.getElementById('bulkMarkPaidModal').classList.remove('hidden');
}

function closeBulkMarkPaidModal() {
    document.getElementById('bulkMarkPaidModal').classList.add('hidden');
}

function handleBulkMarkPaidSubmit(form) {
    const submitBtn = document.getElementById('bulkMarkPaidSubmitBtn');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.querySelector('.btn-text')?.classList.add('hidden');
        submitBtn.querySelector('.btn-loading')?.classList.remove('hidden');
    }
    return true;
}

// Close modals when clicking outside
window.addEventListener('click', function(e) {
    const markPaidModal = document.getElementById('markPaidModal');
    if (e.target === markPaidModal) {
        closeMarkPaidModal();
    }
    const bulkMarkPaidModal = document.getElementById('bulkMarkPaidModal');
    if (e.target === bulkMarkPaidModal) {
        closeBulkMarkPaidModal();
    }
    const generateModal = document.getElementById('generateModal');
    if (e.target === generateModal) {
        closeGenerateModal();
    }
});

function cancelPayroll(payrollId) {
    const reason = prompt('Masukkan alasan pembatalan gaji (Sesi dan jam akan tetap hangus):');
    if (reason !== null) {
        if (reason.trim() === '') {
            alert('Alasan pembatalan wajib diisi.');
            return;
        }
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/payroll/${payrollId}/cancel`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const reasonInput = document.createElement('input');
        reasonInput.type = 'hidden';
        reasonInput.name = 'cancel_reason';
        reasonInput.value = reason;
        
        form.appendChild(csrfToken);
        form.appendChild(reasonInput);
        document.body.appendChild(form);
        form.submit();
    }
}

function deletePayroll(payrollId) {
    if (confirm('Apakah Anda yakin ingin menghapus data gaji ini? Data yang sudah dihapus tidak dapat dikembalikan.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/payroll/${payrollId}`;
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        form.appendChild(methodInput);
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function removeDuplicates() {
    if (confirm('Apakah Anda yakin ingin menghapus data gaji duplikat?\n\nScript ini akan:\n- Mencari data gaji dengan user, periode mulai, dan periode akhir yang sama\n- Menyimpan data yang terbaru (berdasarkan created_at)\n- Menghapus data duplikat yang lebih lama\n\nData yang sudah dibayar tidak akan dihapus kecuali menggunakan force mode.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/payroll/remove-duplicates';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function openGenerateModal() {
    document.getElementById('generateModal').classList.remove('hidden');
}

function closeGenerateModal() {
    document.getElementById('generateModal').classList.add('hidden');
}

function regeneratePayroll(payrollId) {
    if (confirm('Apakah Anda yakin ingin regenerate gaji ini?\n\nGaji akan dihitung ulang menggunakan formula terbaru berdasarkan data attendance saat ini.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/payroll/${payrollId}/regenerate`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function regenerateWeek(weekStart) {
    if (confirm('Apakah Anda yakin ingin regenerate SEMUA gaji pending untuk minggu ini?\n\nSemua gaji pending akan dihitung ulang menggunakan formula terbaru.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/payroll/regenerate-week';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const weekInput = document.createElement('input');
        weekInput.type = 'hidden';
        weekInput.name = 'week_start';
        weekInput.value = weekStart;
        
        form.appendChild(csrfToken);
        form.appendChild(weekInput);
        document.body.appendChild(form);
        form.submit();
    }
}

</script>
@endsection

