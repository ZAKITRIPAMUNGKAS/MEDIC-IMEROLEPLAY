@extends('layouts.app')
@section('title', 'Assign Sub-Jabatan ke Staf')

@section('content')
<div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700"></div>
    <div class="absolute inset-0 bg-black bg-opacity-20"></div>

    <div class="relative max-w-5xl mx-auto text-white">

        {{-- Header --}}
        <div class="glass-effect rounded-2xl p-6 mb-6">
            <div class="flex items-center gap-3 mb-1">
                <a href="{{ route('admin.sub-roles.index') }}" class="text-sky-400 hover:text-sky-300 text-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Divisi
                </a>
            </div>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mt-2">
                <div>
                    <h1 class="text-2xl font-bold text-white">Assign Sub-Jabatan ke Staf</h1>
                    <p class="text-sky-200 text-sm mt-0.5">Pilih divisi (GA, MSL, PND, IE, Comdis) untuk setiap staf Alta Hospital.</p>
                </div>
                <div class="text-right">
                    <span class="text-xs text-sky-300">Total Ditampilkan:</span>
                    <span id="displayed-count" class="text-lg font-bold text-white ml-1">{{ $staffList->count() }}</span>
                </div>
            </div>

            {{-- Filter & Search Form (Khusus Alta Hospital) --}}
            <form method="GET" action="{{ route('admin.sub-roles.assign') }}" class="mt-5 flex flex-wrap items-center gap-3">
                <div class="relative flex-1 min-w-[220px]">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" id="searchInput" name="q" value="{{ $search ?? '' }}"
                           placeholder="Ketik nama anggota atau Staff ID..."
                           class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-xl pl-10 pr-10 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400 focus:border-transparent transition-all">
                    @if(!empty($search))
                    <a href="{{ route('admin.sub-roles.assign') }}"
                       class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white text-xs">
                        <i class="fas fa-times-circle"></i>
                    </a>
                    @endif
                </div>

                {{-- Indikator Alta Hospital --}}
                <div class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl text-xs font-semibold bg-sky-500/20 text-sky-300 border border-sky-500/30 whitespace-nowrap">
                    <i class="fas fa-hospital-alt text-sky-400"></i>
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

        @if(session('error'))
            <div class="mb-4 bg-red-500/20 border border-red-500/40 rounded-xl px-4 py-3 text-red-300 text-sm flex items-center gap-2">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        {{-- Form Bulk Assign --}}
        <form method="POST" action="{{ route('admin.sub-roles.assign-bulk') }}">
            @csrf
            <div class="glass-effect rounded-2xl overflow-hidden shadow-2xl">
                <div class="px-5 py-4 border-b border-white/10 bg-white/5 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-white">Daftar Anggota Staf</p>
                        <p class="text-xs text-gray-400">Pilih sub-jabatan lalu klik simpan</p>
                    </div>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-sky-500 to-indigo-600 hover:from-sky-400 hover:to-indigo-500 text-white rounded-xl font-semibold text-sm transition-all shadow-lg shadow-sky-500/20">
                        <i class="fas fa-save"></i> Simpan Semua
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm" id="staffTable">
                        <thead>
                            <tr class="border-b border-white/10 bg-white/5">
                                <th class="text-left px-5 py-3 text-gray-300 text-xs uppercase tracking-wide font-semibold">Staf</th>
                                <th class="text-left px-5 py-3 text-gray-300 text-xs uppercase tracking-wide font-semibold">Jabatan Utama</th>
                                <th class="text-left px-5 py-3 text-gray-300 text-xs uppercase tracking-wide font-semibold w-64">Sub-Jabatan / Divisi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5" id="staffTableBody">
                            @forelse($staffList as $idx => $staf)
                            <input type="hidden" name="assignments[{{ $idx }}][user_id]" value="{{ $staf->id }}">
                            <tr class="hover:bg-white/5 transition-colors staff-row"
                                data-name="{{ strtolower($staf->name) }}"
                                data-id="{{ strtolower($staf->staff_id ?? '') }}"
                                data-role="{{ strtolower($staf->role?->display_name ?? '') }}">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-sky-500/30 to-cyan-500/30 flex items-center justify-center border border-white/20 text-white text-xs font-bold flex-shrink-0">
                                            {{ strtoupper(substr($staf->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="text-white font-medium text-sm">{{ $staf->name }}</p>
                                            @if($staf->staff_id)
                                                <p class="text-sky-300 font-mono text-xs">{{ $staf->staff_id }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-sky-500/20 text-sky-300 border border-sky-500/30">
                                        {{ $staf->role?->display_name ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3">
                                    <select name="assignments[{{ $idx }}][sub_role_id]"
                                            class="w-full bg-white/10 text-white border border-white/20 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-sky-400 appearance-none cursor-pointer">
                                        <option value="" class="bg-slate-900 text-gray-400">— Tanpa Sub-Jabatan —</option>
                                        @foreach($subRoles as $sub)
                                            <option value="{{ $sub->id }}" class="bg-slate-900 text-white font-semibold"
                                                {{ $staf->sub_role_id == $sub->id ? 'selected' : '' }}>
                                                [{{ $sub->short_name }}] {{ $sub->display_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            @empty
                            <tr id="emptyRow">
                                <td colspan="3" class="px-5 py-8 text-center text-gray-400">
                                    <i class="fas fa-user-slash text-2xl mb-2 text-gray-500 block"></i>
                                    Tidak ada data staf yang ditemukan.
                                </td>
                            </tr>
                            @endforelse
                            <tr id="noResultsRow" class="hidden">
                                <td colspan="3" class="px-5 py-8 text-center text-gray-400">
                                    <i class="fas fa-search text-2xl mb-2 text-gray-500 block"></i>
                                    Tidak ada staf yang sesuai dengan kata kunci pencarian.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="px-5 py-4 border-t border-white/10 flex items-center justify-between bg-white/5">
                    <span class="text-xs text-gray-400">Pastikan memeriksa kembali penugasan sebelum klik simpan.</span>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-sky-500 to-indigo-600 hover:from-sky-400 hover:to-indigo-500 text-white rounded-xl font-semibold text-sm transition-all shadow-lg shadow-sky-500/20">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>

{{-- Real-time instant search script --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('searchInput');
    const rows = document.querySelectorAll('.staff-row');
    const countDisplay = document.getElementById('displayed-count');
    const noResultsRow = document.getElementById('noResultsRow');

    if (input) {
        input.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            let visibleCount = 0;

            rows.forEach(function (row) {
                const name = row.getAttribute('data-name') || '';
                const staffId = row.getAttribute('data-id') || '';
                const role = row.getAttribute('data-role') || '';

                if (name.includes(query) || staffId.includes(query) || role.includes(query)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (countDisplay) {
                countDisplay.textContent = visibleCount;
            }

            if (noResultsRow) {
                if (visibleCount === 0 && rows.length > 0) {
                    noResultsRow.classList.remove('hidden');
                } else {
                    noResultsRow.classList.add('hidden');
                }
            }
        });
    }
});
</script>
@endsection
