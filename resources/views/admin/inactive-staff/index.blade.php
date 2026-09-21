@extends('layouts.app')

@section('title', 'Staf Tidak Aktif - Portal Medis')

@section('content')
<div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700"></div>
    <div class="absolute inset-0 bg-black bg-opacity-20"></div>

    <div class="relative max-w-7xl w-full mx-auto text-white">

        {{-- Header --}}
        <div class="glass-effect rounded-2xl elegant-shadow-lg p-4 sm:p-6 md:p-8 mb-6">
            <div class="flex flex-col space-y-4 sm:space-y-0 sm:flex-row sm:items-start justify-between mb-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 bg-red-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-user-slash text-red-400 text-lg"></i>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-white">Staf Tidak Aktif Duty</h1>
                    </div>
                    <p class="text-sky-200 text-sm sm:text-base">
                        Staf aktif yang <strong>tidak pernah duty</strong> selama
                        <span class="text-amber-300 font-semibold">{{ $months }} bulan terakhir</span>
                        (sejak {{ $since->translatedFormat('d F Y') }})
                    </p>
                </div>

                {{-- Stat Cards --}}
                <div class="flex flex-wrap gap-3">
                    <div class="glass-effect rounded-xl p-4 text-center min-w-[110px]">
                        <p class="text-3xl font-bold text-red-400">{{ $totalInactive }}</p>
                        <p class="text-xs text-gray-300 mt-1">Tidak Aktif Duty</p>
                    </div>
                    <div class="glass-effect rounded-xl p-4 text-center min-w-[110px]">
                        <p class="text-3xl font-bold text-green-400">{{ $totalAktif - $totalInactive }}</p>
                        <p class="text-xs text-gray-300 mt-1">Masih Aktif Duty</p>
                    </div>
                    <div class="glass-effect rounded-xl p-4 text-center min-w-[110px]">
                        <p class="text-3xl font-bold text-white">{{ $totalAktif }}</p>
                        <p class="text-xs text-gray-300 mt-1">Total Staf</p>
                    </div>
                </div>
            </div>

            {{-- Per Hospital --}}
            @if($perHospital->count() > 0)
            <div class="flex flex-wrap gap-3 mb-6">
                <div class="flex items-center gap-2 bg-white/5 rounded-lg px-3 py-2 text-xs">
                    <i class="fas fa-hospital text-sky-400"></i>
                    <span class="text-gray-300">Alta:</span>
                    <span class="font-bold text-white">{{ $perHospital->get('alta', 0) }}</span>
                </div>
                <div class="flex items-center gap-2 bg-white/5 rounded-lg px-3 py-2 text-xs">
                    <i class="fas fa-hospital text-purple-400"></i>
                    <span class="text-gray-300">Roxwood:</span>
                    <span class="font-bold text-white">{{ $perHospital->get('roxwood', 0) }}</span>
                </div>
                @foreach($perHospital as $hosp => $count)
                    @if(!in_array($hosp, ['alta', 'roxwood']) && $hosp)
                    <div class="flex items-center gap-2 bg-white/5 rounded-lg px-3 py-2 text-xs">
                        <i class="fas fa-hospital text-amber-400"></i>
                        <span class="text-gray-300">{{ ucfirst($hosp) }}:</span>
                        <span class="font-bold text-white">{{ $count }}</span>
                    </div>
                    @endif
                @endforeach
            </div>
            @endif

            {{-- Filter Form --}}
            <form method="GET" action="{{ route('admin.inactive-staff.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                {{-- Search --}}
                <div class="sm:col-span-2 lg:col-span-1">
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Cari nama / staff ID..."
                           class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 text-sm">
                </div>

                {{-- Bulan --}}
                <div>
                    <select name="months" class="w-full bg-white/10 text-white border border-white/20 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-sky-400 text-sm appearance-none">
                        @foreach([1 => '1 Bulan', 2 => '2 Bulan', 3 => '3 Bulan', 6 => '6 Bulan'] as $val => $label)
                            <option value="{{ $val }}" {{ $months == $val ? 'selected' : '' }} class="bg-sky-900">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Hospital --}}
                <div>
                    <select name="hospital" class="w-full bg-white/10 text-white border border-white/20 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-sky-400 text-sm appearance-none">
                        <option value="all" {{ $hospital === 'all' ? 'selected' : '' }} class="bg-sky-900">Semua RS</option>
                        <option value="alta" {{ $hospital === 'alta' ? 'selected' : '' }} class="bg-sky-900">Alta</option>
                        <option value="roxwood" {{ $hospital === 'roxwood' ? 'selected' : '' }} class="bg-sky-900">Roxwood</option>
                    </select>
                </div>

                {{-- Submit --}}
                <div>
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-sky-500 hover:bg-sky-400 text-white rounded-lg font-semibold text-sm transition-all">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </form>
        </div>

        {{-- Tabel --}}
        <div class="glass-effect rounded-2xl elegant-shadow-lg overflow-hidden">
            @if($inactiveStaff->isEmpty())
                <div class="text-center py-20 px-4">
                    <div class="w-20 h-20 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check-circle text-green-400 text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Semua Staf Aktif!</h3>
                    <p class="text-gray-300 text-sm">
                        Tidak ada staf yang absen duty selama {{ $months }} bulan terakhir
                        @if($hospital !== 'all') di RS {{ ucfirst($hospital) }} @endif.
                    </p>
                </div>
            @else
                {{-- Desktop Table --}}
                <div class="hidden sm:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-white/10 bg-white/5">
                                <th class="text-left px-5 py-4 text-gray-300 font-semibold text-xs uppercase tracking-wide">#</th>
                                <th class="text-left px-5 py-4 text-gray-300 font-semibold text-xs uppercase tracking-wide">Staf</th>
                                <th class="text-left px-5 py-4 text-gray-300 font-semibold text-xs uppercase tracking-wide">Jabatan</th>
                                <th class="text-left px-5 py-4 text-gray-300 font-semibold text-xs uppercase tracking-wide">RS</th>
                                <th class="text-left px-5 py-4 text-gray-300 font-semibold text-xs uppercase tracking-wide">Terakhir Duty</th>
                                <th class="text-left px-5 py-4 text-gray-300 font-semibold text-xs uppercase tracking-wide">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach($inactiveStaff as $index => $staf)
                            @php
                                $lastAttendance = $staf->attendances->first();
                                $lastDutyDate = $lastAttendance?->clock_in;
                                $daysSinceLastDuty = $lastDutyDate ? (int) \Carbon\Carbon::parse($lastDutyDate)->diffInDays(now()) : null;
                            @endphp
                            <tr class="hover:bg-white/5 transition-colors">
                                {{-- No --}}
                                <td class="px-5 py-4 text-gray-400 text-xs">
                                    {{ ($inactiveStaff->currentPage() - 1) * $inactiveStaff->perPage() + $loop->iteration }}
                                </td>

                                {{-- Nama & Staff ID --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        @if($staf->profile_image && file_exists(public_path($staf->profile_image)))
                                            <img src="{{ asset($staf->profile_image) }}" class="w-9 h-9 rounded-full object-cover border-2 border-white/20" alt="">
                                        @else
                                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-red-500/40 to-orange-500/40 flex items-center justify-center border-2 border-white/20">
                                                <span class="text-white text-xs font-bold">{{ strtoupper(substr($staf->name, 0, 2)) }}</span>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="text-white font-semibold text-sm">{{ $staf->name }}</p>
                                            @if($staf->staff_id)
                                                <p class="text-gray-400 text-xs">{{ $staf->staff_id }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Jabatan --}}
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-sky-500/20 text-sky-300 border border-sky-500/30">
                                        {{ $staf->role?->display_name ?? $staf->role?->name ?? '-' }}
                                    </span>
                                </td>

                                {{-- RS --}}
                                <td class="px-5 py-4">
                                    @if(strtolower($staf->hospital ?? '') === 'alta')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-500/20 text-blue-300 border border-blue-500/30">
                                            <i class="fas fa-hospital text-[10px]"></i> Alta
                                        </span>
                                    @elseif(strtolower($staf->hospital ?? '') === 'roxwood')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-purple-500/20 text-purple-300 border border-purple-500/30">
                                            <i class="fas fa-hospital text-[10px]"></i> Roxwood
                                        </span>
                                    @else
                                        <span class="text-gray-500 text-xs">-</span>
                                    @endif
                                </td>

                                {{-- Terakhir Duty --}}
                                <td class="px-5 py-4">
                                    @if($lastDutyDate)
                                        <p class="text-white text-sm">{{ \Carbon\Carbon::parse($lastDutyDate)->translatedFormat('d M Y') }}</p>
                                        <p class="text-gray-400 text-xs">{{ $daysSinceLastDuty }} hari lalu</p>
                                    @else
                                        <span class="text-red-400 text-xs font-medium">Belum pernah duty</span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="px-5 py-4">
                                    @if(!$lastDutyDate)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-red-500/20 text-red-300 border border-red-500/30">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> Tidak Pernah Duty
                                        </span>
                                    @elseif($daysSinceLastDuty > 90)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-orange-500/20 text-orange-300 border border-orange-500/30">
                                            <span class="w-1.5 h-1.5 rounded-full bg-orange-400"></span> Sangat Lama Absen
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-500/20 text-yellow-300 border border-yellow-500/30">
                                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-400"></span> Tidak Aktif {{ $months }}bln
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Cards --}}
                <div class="sm:hidden divide-y divide-white/10">
                    @foreach($inactiveStaff as $staf)
                    @php
                        $lastAttendance = $staf->attendances->first();
                        $lastDutyDate = $lastAttendance?->clock_in;
                        $daysSinceLastDuty = $lastDutyDate ? (int) \Carbon\Carbon::parse($lastDutyDate)->diffInDays(now()) : null;
                    @endphp
                    <div class="p-4 hover:bg-white/5 transition-colors">
                        <div class="flex items-start gap-3">
                            @if($staf->profile_image && file_exists(public_path($staf->profile_image)))
                                <img src="{{ asset($staf->profile_image) }}" class="w-10 h-10 rounded-full object-cover border-2 border-white/20 flex-shrink-0" alt="">
                            @else
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-red-500/40 to-orange-500/40 flex items-center justify-center border-2 border-white/20 flex-shrink-0">
                                    <span class="text-white text-xs font-bold">{{ strtoupper(substr($staf->name, 0, 2)) }}</span>
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <p class="text-white font-semibold text-sm truncate">{{ $staf->name }}</p>
                                        @if($staf->staff_id)
                                            <p class="text-gray-400 text-xs">{{ $staf->staff_id }}</p>
                                        @endif
                                    </div>
                                    @if(!$lastDutyDate)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-500/20 text-red-300 border border-red-500/30 whitespace-nowrap flex-shrink-0">
                                            <span class="w-1 h-1 rounded-full bg-red-400"></span> Belum Pernah
                                        </span>
                                    @elseif($daysSinceLastDuty > 90)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-orange-500/20 text-orange-300 border border-orange-500/30 whitespace-nowrap flex-shrink-0">
                                            <span class="w-1 h-1 rounded-full bg-orange-400"></span> Sangat Lama
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-yellow-500/20 text-yellow-300 border border-yellow-500/30 whitespace-nowrap flex-shrink-0">
                                            <span class="w-1 h-1 rounded-full bg-yellow-400"></span> Tidak Aktif
                                        </span>
                                    @endif
                                </div>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-sky-500/20 text-sky-300 border border-sky-500/30">
                                        {{ $staf->role?->display_name ?? '-' }}
                                    </span>
                                    @if(strtolower($staf->hospital ?? '') === 'alta')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-500/20 text-blue-300 border border-blue-500/30">
                                            <i class="fas fa-hospital text-[8px]"></i> Alta
                                        </span>
                                    @elseif(strtolower($staf->hospital ?? '') === 'roxwood')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-purple-500/20 text-purple-300 border border-purple-500/30">
                                            <i class="fas fa-hospital text-[8px]"></i> Roxwood
                                        </span>
                                    @endif
                                </div>
                                <p class="text-gray-400 text-xs mt-1.5">
                                    Terakhir duty:
                                    @if($lastDutyDate)
                                        <span class="text-white">{{ \Carbon\Carbon::parse($lastDutyDate)->translatedFormat('d M Y') }}</span>
                                        <span>({{ $daysSinceLastDuty }} hari lalu)</span>
                                    @else
                                        <span class="text-red-400 font-medium">Belum pernah</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($inactiveStaff->hasPages())
                <div class="px-5 py-4 border-t border-white/10">
                    <div class="flex items-center justify-between text-xs text-gray-400">
                        <span>
                            Menampilkan {{ $inactiveStaff->firstItem() }}–{{ $inactiveStaff->lastItem() }}
                            dari {{ $inactiveStaff->total() }} staf
                        </span>
                        <div class="flex items-center gap-1">
                            @if($inactiveStaff->onFirstPage())
                                <span class="px-3 py-1.5 bg-white/5 rounded-lg text-gray-600 cursor-not-allowed">‹</span>
                            @else
                                <a href="{{ $inactiveStaff->previousPageUrl() }}" class="px-3 py-1.5 bg-white/10 hover:bg-white/20 rounded-lg text-white transition-colors">‹</a>
                            @endif

                            @foreach($inactiveStaff->getUrlRange(max(1, $inactiveStaff->currentPage() - 2), min($inactiveStaff->lastPage(), $inactiveStaff->currentPage() + 2)) as $page => $url)
                                @if($page == $inactiveStaff->currentPage())
                                    <span class="px-3 py-1.5 bg-sky-500 rounded-lg text-white font-bold">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="px-3 py-1.5 bg-white/10 hover:bg-white/20 rounded-lg text-white transition-colors">{{ $page }}</a>
                                @endif
                            @endforeach

                            @if($inactiveStaff->hasMorePages())
                                <a href="{{ $inactiveStaff->nextPageUrl() }}" class="px-3 py-1.5 bg-white/10 hover:bg-white/20 rounded-lg text-white transition-colors">›</a>
                            @else
                                <span class="px-3 py-1.5 bg-white/5 rounded-lg text-gray-600 cursor-not-allowed">›</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            @endif
        </div>

        {{-- Info Box --}}
        <div class="mt-6 glass-effect rounded-xl p-4 flex items-start gap-3 text-sm">
            <i class="fas fa-info-circle text-sky-400 mt-0.5 flex-shrink-0"></i>
            <p class="text-gray-300">
                Halaman ini menampilkan staf <strong class="text-white">aktif</strong> yang tidak memiliki catatan duty
                sejak <strong class="text-amber-300">{{ $since->translatedFormat('d F Y') }}</strong>.
                Data diambil dari tabel <code class="bg-white/10 px-1 rounded text-xs">attendances</code>.
                Gunakan filter untuk menyesuaikan periode atau rumah sakit.
            </p>
        </div>

    </div>
</div>
@endsection
