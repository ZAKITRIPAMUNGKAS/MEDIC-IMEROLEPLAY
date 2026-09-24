@extends('layouts.app')
@section('title', 'Kontrak Medis IE — Portal Alta Hospital')
@section('content')
<div class="min-h-screen pt-20 pb-10" style="background: linear-gradient(135deg, #0b1329 0%, #0c2461 50%, #0b1329 100%);">
<div class="max-w-6xl mx-auto px-4">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2"><i class="fas fa-file-contract text-sky-400"></i> IE — Surat Perjanjian Kontrak Medis</h1>
            <p class="text-white/50 text-sm mt-0.5">Terbitkan kontrak medis anggota yang otomatis muncul di profil mereka</p>
        </div>
    </div>
    @if(session('success'))
    <div class="mb-4 p-4 bg-emerald-500/20 border border-emerald-500/40 rounded-xl text-emerald-300 text-sm flex items-center gap-2"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    {{-- Form Terbitkan --}}
    {{-- Form Terbitkan --}}
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 mb-6">
        <h3 class="text-white font-semibold mb-4 flex items-center gap-2"><i class="fas fa-plus-circle text-sky-400"></i> Terbitkan Kontrak Medis Baru</h3>
        <form method="POST" action="{{ route('portal.ie.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @csrf

            {{-- Searchable Anggota Dropdown --}}
            <div class="relative sm:col-span-1" id="contractSelectContainer">
                <label class="block text-xs text-white/50 mb-1">Anggota Medis *</label>
                <input type="hidden" name="user_id" id="contract_user_id" required>

                {{-- Trigger Button --}}
                <button type="button" id="contractSelectTrigger" onclick="toggleContractDropdown()"
                        class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm flex items-center justify-between text-left focus:outline-none focus:border-sky-400 hover:border-white/30 transition">
                    <span id="contract_select_text" class="text-white/40 truncate flex items-center gap-1.5">
                        <i class="fas fa-search text-white/30 text-xs"></i>
                        <span>— Cari &amp; Pilih Anggota —</span>
                    </span>
                    <i class="fas fa-chevron-down text-white/40 text-xs ml-2 shrink-0 transition-transform" id="contract_select_arrow"></i>
                </button>

                {{-- Popover Dropdown Panel --}}
                <div id="contractDropdownPanel" class="hidden absolute left-0 right-0 top-full mt-1 bg-slate-900/98 border border-white/20 rounded-xl shadow-2xl z-50 p-2 space-y-2 backdrop-blur-xl">
                    <div class="relative">
                        <i class="fas fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-white/40 text-xs"></i>
                        <input type="text" id="contract_search_input" placeholder="Cari nama, ID staf, citizen ID..."
                               autocomplete="off"
                               class="w-full pl-8 pr-7 py-1.5 bg-black/60 border border-white/15 rounded-lg text-white text-xs placeholder-white/40 focus:outline-none focus:border-sky-400">
                        <button type="button" onclick="clearContractSearch()" id="contract_clear_search" class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 text-white/40 hover:text-white text-xs">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div id="contract_members_list" class="max-h-60 overflow-y-auto space-y-1 pr-1 custom-scrollbar text-xs">
                        @if(isset($staffList))
                            @foreach($staffList as $s)
                                @php
                                    $isPaused = !$s->is_active;
                                    $roleTitle = $s->medicRole?->display_name ?? $s->role?->display_name ?? 'Staf';
                                    $searchString = strtolower($s->name . ' ' . ($s->staff_id ?? '') . ' ' . ($s->citizen_id ?? '') . ' ' . ($s->role?->display_name ?? '') . ' ' . ($s->medicRole?->display_name ?? '') . ' ' . ($isPaused ? 'paused nonaktif' : 'aktif'));
                                @endphp
                                <div class="contract-member-item p-2 hover:bg-white/10 rounded-lg cursor-pointer flex items-center justify-between transition border border-transparent hover:border-white/10"
                                     data-id="{{ $s->id }}"
                                     data-search="{{ $searchString }}"
                                     onclick="selectContractMember({{ $s->id }}, '{{ addslashes($s->name) }}', '{{ $s->staff_id ?? '-' }}', '{{ addslashes($roleTitle) }}', {{ $isPaused ? 'true' : 'false' }})">
                                    <div class="min-w-0 pr-2">
                                        <div class="font-bold text-white truncate flex items-center gap-1.5">
                                            <span>{{ $s->name }}</span>
                                            @if($isPaused)
                                                <span class="text-[9px] px-1.5 py-0.2 rounded font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40">Paused</span>
                                            @endif
                                        </div>
                                        <div class="text-[10px] text-white/50 truncate mt-0.5">
                                            <span class="text-sky-300 font-semibold">{{ $roleTitle }}</span>
                                            &bull; ID: <span class="font-mono text-white/70">{{ $s->staff_id ?? '-' }}</span>
                                            @if(!empty($s->citizen_id))
                                                &bull; CID: <span class="font-mono text-white/70">{{ $s->citizen_id }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <i class="fas fa-check text-sky-400 text-xs hidden contract-check-icon shrink-0"></i>
                                </div>
                            @endforeach
                        @endif
                        <div id="contract_no_results" class="hidden py-6 text-center text-white/40 text-xs">
                            <i class="fas fa-user-slash text-xl mb-1 block"></i>
                            Tidak ada anggota yang cocok dengan pencarian.
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs text-white/50 mb-1">Judul Kontrak *</label>
                <input type="text" name="title" required maxlength="255" value="Surat Perjanjian Kontrak Medis" class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-sky-400">
            </div>

            <div>
                <label class="block text-xs text-white/50 mb-1">Tanggal Terbit *</label>
                <input type="date" name="issue_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-sky-400">
            </div>

            <div class="col-span-1 sm:col-span-3 p-3 bg-sky-500/10 border border-sky-500/25 rounded-xl text-sky-200 text-xs flex items-start gap-2.5">
                <i class="fas fa-magic text-sky-400 mt-0.5 shrink-0"></i>
                <div class="leading-relaxed text-[11px]">
                    <strong class="text-white">Cetak Dokumen &amp; Nomor Registrasi Otomatis:</strong> Nomor kontrak akan dibuat otomatis oleh sistem secara berurutan (format: <code class="font-mono text-sky-300 bg-sky-500/20 px-1 py-0.5 rounded">001/IER-IMC/KK/IX/2026</code>) dengan masa berlaku tetap/permanen. Sistem men-generate template dokumen resmi dengan Stempel Resmi &amp; Tanda Tangan Digital yang langsung tersimpan di profil anggota.
                </div>
            </div>

            <div class="col-span-1 sm:col-span-3">
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-sky-500 to-blue-600 text-white font-semibold rounded-xl text-sm hover:from-sky-400 hover:to-blue-500 transition-all flex items-center gap-2 shadow-lg">
                    <i class="fas fa-magic"></i> Terbitkan Kontrak &amp; Generate Dokumen ke Profil
                </button>
            </div>
        </form>
    </div>

    {{-- Tabel --}}
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden">
        @if($contracts->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-white/40"><i class="fas fa-file-contract text-4xl mb-3"></i><p class="text-sm">Belum ada kontrak medis diterbitkan.</p></div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-white/10 text-xs text-white/50 uppercase tracking-wider">
                    <tr><th class="text-left px-5 py-3">Anggota</th><th class="text-left px-5 py-3">Judul</th><th class="text-left px-5 py-3">No. Kontrak</th><th class="text-left px-5 py-3">Terbit</th><th class="text-left px-5 py-3">Berakhir</th><th class="text-left px-5 py-3">Status</th><th class="px-5 py-3">Aksi</th></tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($contracts as $c)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-5 py-3.5"><div class="text-white font-medium">{{ $c->user?->name }}</div><div class="text-white/40 text-xs">{{ $c->user?->staff_id }}</div></td>
                        <td class="px-5 py-3.5 text-white/80">{{ $c->title }}</td>
                        <td class="px-5 py-3.5 text-white/70 font-mono text-xs">{{ $c->certificate_number ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-white/70">{{ $c->issue_date?->format('d M Y') }}</td>
                        <td class="px-5 py-3.5 text-white/70">
                            @if($c->expiry_date)
                                {{ $c->expiry_date->format('d M Y') }}
                            @else
                                <span class="text-emerald-400 text-xs font-semibold">Permanen</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5"><span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $c->status === 'active' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-red-500/20 text-red-300 border border-red-500/30' }}">{{ ucfirst($c->status) }}</span></td>
                        <td class="px-5 py-3.5">
                            @if($c->status === 'active')
                            <form method="POST" action="{{ route('portal.ie.revoke', $c) }}">
                                @csrf
                                <button type="submit" onclick="return confirm('Cabut kontrak {{ addslashes($c->user?->name) }}?')" class="px-3 py-1 bg-red-500/20 hover:bg-red-500/30 text-red-300 text-xs font-medium rounded-lg border border-red-500/30">
                                    <i class="fas fa-ban text-[10px]"></i> Cabut
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-white/10">{{ $contracts->links() }}</div>
        @endif
    </div>
</div>
</div>

<script>
function toggleContractDropdown() {
    const panel = document.getElementById('contractDropdownPanel');
    const arrow = document.getElementById('contract_select_arrow');
    const isOpen = !panel.classList.contains('hidden');
    if (isOpen) {
        closeContractDropdown();
    } else {
        panel.classList.remove('hidden');
        arrow.classList.add('rotate-180');
        const input = document.getElementById('contract_search_input');
        input.value = '';
        filterContractMembers('');
        setTimeout(() => input.focus(), 50);
    }
}

function closeContractDropdown() {
    const panel = document.getElementById('contractDropdownPanel');
    const arrow = document.getElementById('contract_select_arrow');
    if (panel) panel.classList.add('hidden');
    if (arrow) arrow.classList.remove('rotate-180');
}

function clearContractSearch() {
    const input = document.getElementById('contract_search_input');
    input.value = '';
    filterContractMembers('');
    input.focus();
}

function filterContractMembers(query) {
    const q = query.toLowerCase().trim();
    const items = document.querySelectorAll('.contract-member-item');
    const clearBtn = document.getElementById('contract_clear_search');
    if (clearBtn) clearBtn.classList.toggle('hidden', q.length === 0);

    let count = 0;
    items.forEach(el => {
        const text = el.getAttribute('data-search') || '';
        if (text.includes(q)) {
            el.classList.remove('hidden');
            count++;
        } else {
            el.classList.add('hidden');
        }
    });

    const noRes = document.getElementById('contract_no_results');
    if (noRes) noRes.classList.toggle('hidden', count > 0);
}

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('contract_search_input');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            filterContractMembers(this.value);
        });
    }

    document.addEventListener('click', function(e) {
        const container = document.getElementById('contractSelectContainer');
        if (container && !container.contains(e.target)) {
            closeContractDropdown();
        }
    });
});

function selectContractMember(id, name, staffId, roleTitle, isPaused) {
    document.getElementById('contract_user_id').value = id;

    document.querySelectorAll('.contract-member-item').forEach(el => {
        const isMatch = el.getAttribute('data-id') == id;
        el.classList.toggle('bg-white/10', isMatch);
        const icon = el.querySelector('.contract-check-icon');
        if (icon) icon.classList.toggle('hidden', !isMatch);
    });

    const pausedBadge = isPaused ? '<span class="text-[9px] px-1.5 py-0.2 rounded font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40 ml-1">Paused</span>' : '';
    document.getElementById('contract_select_text').innerHTML = `
        <span class="font-bold text-white">${name}</span>
        ${pausedBadge}
        <span class="text-white/50 text-xs">(${staffId} &bull; ${roleTitle})</span>
    `;

    closeContractDropdown();
}
</script>
@endsection
