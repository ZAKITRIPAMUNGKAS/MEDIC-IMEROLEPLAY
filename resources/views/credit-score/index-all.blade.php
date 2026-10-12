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
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('credit-score.show', $member) }}"
                                       class="px-2.5 py-1.5 bg-sky-500/20 hover:bg-sky-500/40 text-sky-300 rounded-lg text-xs font-semibold transition-all border border-sky-500/30">
                                        Detail
                                    </a>
                                    @if(auth()->user()->isInDivision('comdis') || auth()->user()->isAdmin() || auth()->user()->isExecutiveOrAbove())
                                    <a href="{{ route('credit-score.input', $member) }}"
                                       class="px-2.5 py-1.5 bg-amber-500/20 hover:bg-amber-500/40 text-amber-300 rounded-lg text-xs font-semibold transition-all border border-amber-500/30"
                                       title="Input Poin (+/-)">
                                        Input
                                    </a>
                                    <button type="button"
                                       onclick="openEditScoreModal({{ $member->id }}, '{{ addslashes($member->name) }}', '{{ $member->staff_id ?? '-' }}', {{ $balance }})"
                                       class="px-2.5 py-1.5 bg-purple-500/20 hover:bg-purple-500/40 text-purple-300 rounded-lg text-xs font-semibold transition-all border border-purple-500/30"
                                       title="Edit Skor Langsung">
                                        Edit
                                    </button>
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

let currentModalUserId = null;
let currentModalBaseScore = 100;

function openEditScoreModal(userId, name, staffId, currentScore) {
    currentModalUserId = userId;
    currentModalBaseScore = currentScore;

    document.getElementById('modalMemberName').textContent = name;
    document.getElementById('modalMemberId').textContent = 'Staff ID: ' + staffId;
    document.getElementById('modalCurrentScore').textContent = currentScore;
    document.getElementById('modalScoreInput').value = currentScore;
    document.getElementById('modalReasonInput').value = '';

    const form = document.getElementById('editScoreModalForm');
    form.action = "{{ url('credit-score') }}/" + userId + "/edit";

    const fullPageLink = document.getElementById('modalFullPageLink');
    if (fullPageLink) fullPageLink.href = "{{ url('credit-score') }}/" + userId + "/edit";

    updateModalDiff();

    const modal = document.getElementById('editScoreModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeEditScoreModal() {
    const modal = document.getElementById('editScoreModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function setModalScore(val) {
    document.getElementById('modalScoreInput').value = val;
    updateModalDiff();
}

function updateModalDiff() {
    const val = parseInt(document.getElementById('modalScoreInput').value) || 0;
    const diff = val - currentModalBaseScore;
    const badge = document.getElementById('modalScoreDiffBadge');
    if (diff === 0) {
        badge.className = 'text-xs font-semibold px-2 py-0.5 rounded-md bg-white/10 text-gray-300';
        badge.textContent = 'Tidak ada perubahan';
    } else if (diff > 0) {
        badge.className = 'text-xs font-semibold px-2 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 border border-emerald-500/30';
        badge.textContent = `+${diff} Poin`;
    } else {
        badge.className = 'text-xs font-semibold px-2 py-0.5 rounded-md bg-rose-500/20 text-rose-300 border border-rose-500/30';
        badge.textContent = `${diff} Poin`;
    }
}

document.getElementById('modalScoreInput')?.addEventListener('input', updateModalDiff);
</script>

{{-- Quick Edit Modal Comdis --}}
<div id="editScoreModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 backdrop-blur-sm p-4">
    <div class="bg-[#0b1329] border border-white/20 rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl animate-in fade-in zoom-in-95 duration-200">
        <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between bg-white/5">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-purple-500/20 border border-purple-500/30 flex items-center justify-center text-purple-400 text-sm">
                    <i class="fas fa-edit"></i>
                </div>
                <div>
                    <h3 class="text-white font-bold text-base">Edit Credit Score</h3>
                    <p class="text-[11px] text-gray-400">Atur nilai skor secara langsung</p>
                </div>
            </div>
            <button type="button" onclick="closeEditScoreModal()" class="text-gray-400 hover:text-white transition-colors p-1">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <form id="editScoreModalForm" method="POST" action="" class="p-6 space-y-4">
            @csrf
            {{-- Member Info Card --}}
            <div class="bg-white/5 rounded-xl p-3.5 border border-white/10 flex items-center justify-between">
                <div>
                    <div id="modalMemberName" class="text-white font-bold text-sm">-</div>
                    <div id="modalMemberId" class="text-sky-300 font-mono text-xs mt-0.5">-</div>
                </div>
                <div class="text-right">
                    <div class="text-[10px] text-gray-400 uppercase tracking-wider">Skor Saat Ini</div>
                    <div id="modalCurrentScore" class="text-2xl font-black text-white">100</div>
                </div>
            </div>

            {{-- Input Skor Baru --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-300">Nilai Skor Baru (0 - 100)</label>
                    <span id="modalScoreDiffBadge" class="text-xs font-semibold px-2 py-0.5 rounded-md"></span>
                </div>
                <input type="number" id="modalScoreInput" name="score" min="0" max="100" required
                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 text-base font-bold">
                <div class="flex gap-2 mt-2">
                    <button type="button" onclick="setModalScore(100)" class="text-[11px] px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 hover:bg-emerald-500/30">100 (Max)</button>
                    <button type="button" onclick="setModalScore(95)" class="text-[11px] px-2 py-0.5 rounded bg-sky-500/20 text-sky-300 border border-sky-500/30 hover:bg-sky-500/30">95</button>
                    <button type="button" onclick="setModalScore(85)" class="text-[11px] px-2 py-0.5 rounded bg-amber-500/20 text-amber-300 border border-amber-500/30 hover:bg-amber-500/30">85 (Min)</button>
                    <button type="button" onclick="setModalScore(75)" class="text-[11px] px-2 py-0.5 rounded bg-rose-500/20 text-rose-300 border border-rose-500/30 hover:bg-rose-500/30">75</button>
                </div>
            </div>

            {{-- Alasan --}}
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-300 mb-1.5">Alasan / Catatan Perubahan <span class="text-rose-400">*</span></label>
                <textarea id="modalReasonInput" name="reason" rows="3" required placeholder="Jelaskan alasan edit/penyesuaian skor..."
                          class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 text-xs"></textarea>
            </div>

            <div class="pt-2 flex items-center justify-between">
                <a id="modalFullPageLink" href="#" class="text-xs text-sky-400 hover:text-sky-300">
                    Buka Halaman Form Lengkap &rarr;
                </a>
                <div class="flex gap-2">
                    <button type="button" onclick="closeEditScoreModal()" class="px-4 py-2 rounded-xl border border-white/10 text-gray-300 text-xs font-semibold hover:bg-white/5 transition-all">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-xl text-xs font-semibold shadow-lg shadow-purple-500/20 transition-all">
                        <i class="fas fa-check mr-1"></i> Simpan Skor
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
