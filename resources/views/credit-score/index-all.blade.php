@extends('layouts.app')
@section('title', 'Credit Score Anggota — Alta Hospital')

@section('content')
<div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700"></div>
    <div class="absolute inset-0 bg-black bg-opacity-20"></div>

    <div class="relative max-w-6xl mx-auto text-white">

        {{-- Header --}}
        <div class="glass-effect rounded-2xl p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1">Credit Score Anggota</h1>
                    <p class="text-sky-200 text-sm">Kelola dan pantau Credit Score seluruh anggota Alta Hospital</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-400">Total Anggota</p>
                    <p class="text-2xl font-bold text-white" id="displayed-count">{{ $members->total() }}</p>
                </div>
            </div>

            {{-- Filter & Search Form (Khusus Alta Hospital) --}}
            <form method="GET" action="{{ route('credit-score.index') }}" class="mt-5 flex flex-wrap items-center gap-3">
                <div class="relative flex-1 min-w-[220px]">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" id="csSearchInput" name="q" value="{{ $search }}"
                           placeholder="Ketik nama anggota, Staff ID, divisi, pangkat..."
                           class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-xl pl-10 pr-10 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400 focus:border-transparent transition-all">
                    @if(!empty($search))
                    <a href="{{ route('credit-score.index') }}"
                       class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white text-xs" title="Reset pencarian">
                        <i class="fas fa-times-circle"></i>
                    </a>
                    @endif
                </div>

                {{-- Indikator Alta Hospital --}}
                <div class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 whitespace-nowrap">
                    <i class="fas fa-hospital-alt text-emerald-400"></i>
                    <span>Alta Hospital</span>
                </div>

                <button type="submit"
                        class="px-5 py-2.5 bg-sky-500 hover:bg-sky-400 text-white rounded-xl font-semibold text-sm transition-all shadow-md flex items-center gap-2">
                    <i class="fas fa-search text-xs"></i> Cari
                </button>
            </form>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-green-500/20 border border-green-500/40 rounded-xl px-4 py-3 text-green-300 text-sm flex items-center gap-2">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        {{-- Tabel Anggota --}}
        <div class="glass-effect rounded-2xl overflow-hidden shadow-2xl">
            <div class="overflow-x-auto w-full">
                <table class="w-full text-sm divide-y divide-white/10" id="csTable" style="display: table !important; min-width: 650px;">
                    <thead>
                        <tr class="border-b border-white/10 bg-white/5">
                            <th class="text-left px-5 py-4 text-gray-300 text-xs uppercase tracking-wide font-semibold">#</th>
                            <th class="text-left px-5 py-4 text-gray-300 text-xs uppercase tracking-wide font-semibold">Anggota</th>
                            <th class="text-left px-5 py-4 text-gray-300 text-xs uppercase tracking-wide font-semibold">Jabatan</th>
                            <th class="text-left px-5 py-4 text-gray-300 text-xs uppercase tracking-wide font-semibold">Divisi</th>
                            <th class="text-center px-5 py-4 text-gray-300 text-xs uppercase tracking-wide font-semibold">Credit Score</th>
                            <th class="text-center px-5 py-4 text-gray-300 text-xs uppercase tracking-wide font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5" id="csTableBody">
                        @forelse($members as $member)
                        @php
                            $balance = $member->creditScore?->balance ?? 100;
                            $scoreColor = $balance >= 85 ? 'text-green-400' : ($balance >= 80 ? 'text-yellow-400' : 'text-red-400');
                            $scoreBg   = $balance >= 85 ? 'bg-green-500/20 border-green-500/30' : ($balance >= 80 ? 'bg-yellow-500/20 border-yellow-500/30' : 'bg-red-500/20 border-red-500/30');
                        @endphp
                        <tr class="hover:bg-white/5 transition-colors cs-row"
                            data-name="{{ strtolower($member->name) }}"
                            data-id="{{ strtolower($member->staff_id ?? '') }}"
                            data-role="{{ strtolower($member->role?->display_name ?? '') }}"
                            data-divisi="{{ strtolower($member->subRole?->short_name ?? '') }}">
                            <td class="px-5 py-3 text-gray-500 text-xs">{{ ($members->currentPage()-1)*$members->perPage()+$loop->iteration }}</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-sky-500/30 to-cyan-500/30 flex items-center justify-center border border-white/20 text-white text-xs font-bold flex-shrink-0">
                                        {{ strtoupper(substr($member->name,0,2)) }}
                                    </div>
                                    <div>
                                        <p class="text-white font-medium">{{ $member->name }}</p>
                                        @if($member->staff_id)<p class="text-sky-300 font-mono text-xs">{{ $member->staff_id }}</p>@endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-sky-500/20 text-sky-300 border border-sky-500/30">
                                    {{ $member->role?->display_name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                @if($member->subRole)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold" style="{{ $member->subRole->badge_style }}">
                                        {{ $member->subRole->short_name }}
                                    </span>
                                @else
                                    <span class="text-gray-500 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-sm font-black border {{ $scoreBg }} {{ $scoreColor }}">
                                    <i class="fas fa-star text-xs"></i> {{ $balance }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('credit-score.show', $member) }}"
                                       class="px-3 py-1.5 bg-sky-500/20 hover:bg-sky-500/40 text-sky-300 rounded-lg text-xs font-semibold transition-all border border-sky-500/30">
                                        Detail
                                    </a>
                                    @if(auth()->user()->isInDivision('comdis') || auth()->user()->isAdmin() || auth()->user()->isExecutiveOrAbove())
                                    <a href="{{ route('credit-score.input', $member) }}"
                                       class="px-3 py-1.5 bg-amber-500/20 hover:bg-amber-500/40 text-amber-300 rounded-lg text-xs font-semibold transition-all border border-amber-500/30">
                                        Input
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-gray-400">
                                <i class="fas fa-user-slash text-2xl mb-2 text-gray-500 block"></i>
                                Tidak ada data anggota ditemukan.
                            </td>
                        </tr>
                        @endforelse
                        <tr id="noResultsDesktop" class="hidden">
                            <td colspan="6" class="px-5 py-8 text-center text-gray-400">
                                <i class="fas fa-search text-2xl mb-2 text-gray-500 block"></i>
                                Tidak ada anggota yang sesuai dengan kata kunci pencarian.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if($members->hasPages())
            <div class="px-5 py-4 border-t border-white/10 flex items-center justify-between text-xs text-gray-400 bg-white/5">
                <span>Menampilkan {{ $members->firstItem() }}–{{ $members->lastItem() }} dari {{ $members->total() }} anggota</span>
                <div class="flex gap-1">
                    {{ $members->links() }}
                </div>
            </div>
            @endif
        </div>

    </div>
</div>

{{-- Real-time instant search script --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('csSearchInput');
    const desktopRows = document.querySelectorAll('.cs-row');
    const countDisplay = document.getElementById('displayed-count');
    const noResultsDesktop = document.getElementById('noResultsDesktop');

    if (input) {
        input.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            let visibleCount = 0;

            desktopRows.forEach(function (row) {
                const name = row.getAttribute('data-name') || '';
                const staffId = row.getAttribute('data-id') || '';
                const role = row.getAttribute('data-role') || '';
                const divisi = row.getAttribute('data-divisi') || '';

                if (name.includes(query) || staffId.includes(query) || role.includes(query) || divisi.includes(query)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (countDisplay) {
                countDisplay.textContent = visibleCount;
            }

            if (noResultsDesktop) {
                if (visibleCount === 0 && desktopRows.length > 0) {
                    noResultsDesktop.classList.remove('hidden');
                } else {
                    noResultsDesktop.classList.add('hidden');
                }
            }
        });
    }
});
</script>
@endsection
