@extends('layouts.app')

@section('title', 'IE — Manajemen Jabatan Staf Alta Hospital')

@section('content')
<div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-sky-950/40 to-slate-900"></div>
    <div class="absolute inset-0 bg-black/40"></div>

    <div class="relative max-w-7xl mx-auto text-white space-y-6">

        {{-- Header --}}
        <div class="glass-effect rounded-2xl p-6 border border-white/10 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-3 py-1 rounded-full bg-sky-500/20 text-sky-300 border border-sky-500/30 text-xs font-semibold">
                        <i class="fas fa-shield-alt mr-1"></i> Divisi Industrial &amp; Employee Relations (IE)
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-sky-500/20 border border-sky-500/40 flex items-center justify-center text-sky-400 text-lg">
                        <i class="fas fa-user-tag"></i>
                    </span>
                    Manajemen Jabatan Medis &amp; Manajerial
                </h1>
                <p class="text-slate-300 text-sm mt-1">
                    IE memiliki wewenang untuk mengubah jabatan struktural, jenjang klinis medis, dan penempatan divisi staf Alta Hospital.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('portal.ie.pemutihan.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/15 text-amber-300 text-xs font-semibold rounded-xl border border-amber-500/30 transition-all">
                    <i class="fas fa-history"></i> Pemutihan Duty Medis
                </a>
                <a href="{{ route('portal.ie.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-sky-500/20 hover:bg-sky-500/30 text-sky-300 text-xs font-semibold rounded-xl border border-sky-500/30 transition-all">
                    <i class="fas fa-file-contract"></i> Kontrak Medis
                </a>
            </div>
        </div>

        {{-- Notifications --}}
        @if(session('success'))
            <div class="bg-emerald-500/20 border border-emerald-500/40 rounded-xl px-4 py-3 text-emerald-300 text-sm flex items-center gap-2 shadow-lg">
                <i class="fas fa-check-circle text-emerald-400"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-rose-500/20 border border-rose-500/40 rounded-xl px-4 py-3 text-rose-300 text-sm flex items-center gap-2 shadow-lg">
                <i class="fas fa-exclamation-circle text-rose-400"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Filter Box --}}
        <div class="glass-effect rounded-2xl p-5 border border-white/10">
            <form method="GET" action="{{ route('portal.ie.roles.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                <div>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, Staff ID, CID..."
                           class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-xl px-3.5 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-sky-400">
                </div>
                <div>
                    <select name="role_id" class="w-full bg-slate-900 text-white border border-white/20 rounded-xl px-3.5 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-sky-400">
                        <option value="">— Filter Jabatan Utama —</option>
                        @foreach($allRoles as $r)
                            <option value="{{ $r->id }}" @selected(request('role_id') == $r->id)>{{ $r->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="medic_role_id" class="w-full bg-slate-900 text-emerald-300 border border-emerald-500/30 rounded-xl px-3.5 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-400">
                        <option value="" class="text-white">— Filter Jabatan Medis —</option>
                        @foreach($medicalRoles as $mr)
                            <option value="{{ $mr->id }}" @selected(request('medic_role_id') == $mr->id)>🩺 {{ $mr->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <select name="sub_role_id" class="flex-1 bg-slate-900 text-purple-300 border border-purple-500/30 rounded-xl px-3.5 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-purple-400">
                        <option value="" class="text-white">— Filter Divisi —</option>
                        @foreach($subRoles as $sub)
                            <option value="{{ $sub->id }}" @selected(request('sub_role_id') == $sub->id)>[{{ $sub->short_name }}] {{ $sub->display_name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-4 py-2.5 bg-sky-500 hover:bg-sky-400 text-white rounded-xl text-xs font-semibold shadow-md transition-all">
                        <i class="fas fa-search"></i>
                    </button>
                    @if(request()->hasAny(['q', 'role_id', 'medic_role_id', 'sub_role_id']))
                        <a href="{{ route('portal.ie.roles.index') }}" class="px-3 py-2.5 bg-white/10 hover:bg-white/15 text-white/70 rounded-xl text-xs flex items-center">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="glass-effect rounded-2xl overflow-hidden border border-white/10 shadow-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-white/5 border-b border-white/10 text-slate-300 text-xs font-semibold uppercase tracking-wider">
                            <th class="px-5 py-3.5 text-left">Nama Anggota</th>
                            <th class="px-5 py-3.5 text-left">Jabatan Utama / Manajemen</th>
                            <th class="px-5 py-3.5 text-left">Jabatan Medis (Klinis)</th>
                            <th class="px-5 py-3.5 text-left">Divisi (Sub-Role)</th>
                            <th class="px-5 py-3.5 text-center">Aksi Perubahan IE</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-slate-200">
                        @forelse($staffList as $staf)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-sky-500/30 to-blue-500/30 border border-white/20 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                                        {{ strtoupper(substr($staf->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="text-white font-semibold text-sm">{{ $staf->name }}</p>
                                        <div class="flex items-center gap-2 text-xs text-slate-400">
                                            @if($staf->staff_id)
                                                <span class="font-mono text-sky-300">{{ $staf->staff_id }}</span>
                                            @endif
                                            @if($staf->citizen_id)
                                                <span class="font-mono text-emerald-300">CID: {{ $staf->citizen_id }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-sky-500/20 text-sky-300 border border-sky-500/30">
                                    {{ $staf->role?->display_name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                @if($staf->medicRole)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                        🩺 {{ $staf->medicRole->display_name }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs italic">
                                        (Sesuai peran utama: {{ $staf->role?->display_name }})
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    @if($staf->subRole)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-500/20 text-purple-300 border border-purple-500/30">
                                            [{{ $staf->subRole->short_name }}] {{ $staf->subRole->display_name }}
                                        </span>
                                    @else
                                        <span class="text-slate-500 text-xs">—</span>
                                    @endif
                                    @if($staf->isInterviewer())
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                                            <i class="fas fa-user-tie text-[9px]"></i> Interviewer
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <button type="button"
                                        onclick="openEditRoleModal({{ $staf->id }}, '{{ addslashes($staf->name) }}', {{ $staf->role_id ?? 'null' }}, {{ $staf->medic_role_id ?? 'null' }}, {{ $staf->sub_role_id ?? 'null' }}, {{ $staf->isInterviewer() ? 'true' : 'false' }})"
                                        class="px-3 py-1.5 bg-gradient-to-r from-sky-500 to-indigo-600 hover:from-sky-400 hover:to-indigo-500 text-white rounded-xl text-xs font-semibold shadow-md transition-all flex items-center gap-1.5 mx-auto">
                                    <i class="fas fa-edit text-[10px]"></i> Ubah Jabatan
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-slate-400">
                                <i class="fas fa-user-slash text-3xl mb-2 text-slate-500 block"></i>
                                Tidak ada data staf yang cocok dengan kriteria pencarian.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($staffList->hasPages())
            <div class="px-5 py-3 border-t border-white/10">
                {{ $staffList->links() }}
            </div>
            @endif
        </div>

    </div>
</div>

{{-- Modal Edit Jabatan IE --}}
<div id="editRoleModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/75 backdrop-blur-sm hidden p-4">
    <div class="glass-effect bg-slate-900 border border-white/20 rounded-2xl max-w-lg w-full p-6 text-white shadow-2xl">
        <div class="flex items-center justify-between pb-4 border-b border-white/10 mb-4">
            <div>
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fas fa-user-edit text-sky-400"></i> Perubahan Jabatan Staf
                </h3>
                <p class="text-xs text-sky-200 mt-0.5" id="modalStaffName">Staf</p>
            </div>
            <button type="button" onclick="closeModal('editRoleModal')" class="text-slate-400 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="editRoleForm" method="POST" action="" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1.5">
                    <i class="fas fa-briefcase mr-1 text-sky-400"></i> Jabatan Struktural / Utama *
                </label>
                <select name="role_id" id="modalRoleId" required class="w-full bg-slate-800 text-white border border-white/20 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400">
                    @foreach($allRoles as $r)
                        <option value="{{ $r->id }}">{{ $r->display_name }}</option>
                    @endforeach
                </select>
                <p class="text-[11px] text-slate-400 mt-1">Jabatan manajemen, misal: Staff Manager, Manajer, Trainee, dll.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-emerald-300 uppercase mb-1.5">
                    <i class="fas fa-stethoscope mr-1 text-emerald-400"></i> Jabatan Medis (Jenjang Klinis)
                </label>
                <select name="medic_role_id" id="modalMedicRoleId" class="w-full bg-slate-800 text-emerald-300 border border-emerald-500/30 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 font-medium">
                    <option value="" class="text-slate-400">— Otomatis Sesuai Jabatan Utama —</option>
                    @foreach($medicalRoles as $mr)
                        <option value="{{ $mr->id }}">🩺 {{ $mr->display_name }} (Level {{ $mr->level }})</option>
                    @endforeach
                </select>
                <p class="text-[11px] text-slate-400 mt-1">Wajib dipilih jika staf memegang jabatan manajerial namun tetap memiliki pangkat medis (Co-Ass/Dokter).</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-purple-300 uppercase mb-1.5">
                    <i class="fas fa-layer-group mr-1 text-purple-400"></i> Penempatan Divisi (Sub-Jabatan)
                </label>
                <select name="sub_role_id" id="modalSubRoleId" class="w-full bg-slate-800 text-white border border-white/20 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400">
                    <option value="">— Tanpa Divisi Khusus —</option>
                    @foreach($subRoles as $sub)
                        <option value="{{ $sub->id }}">[{{ $sub->short_name }}] {{ $sub->display_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="p-3.5 rounded-xl bg-indigo-500/10 border border-indigo-500/30 flex items-center justify-between gap-3">
                <div>
                    <span class="text-xs font-bold text-indigo-300 flex items-center gap-1.5">
                        <i class="fas fa-user-tie text-indigo-400"></i> Hak Akses Role Interviewer Calon Medis
                    </span>
                    <span class="text-[11px] text-slate-400 block mt-0.5">
                        Centang untuk memberikan wewenang melakukan wawancara &amp; evaluasi calon anggota medis baru.
                    </span>
                </div>
                <input type="checkbox" name="is_interviewer" id="modalIsInterviewer" value="1" class="w-5 h-5 rounded text-indigo-500 focus:ring-indigo-400 border-white/20 bg-slate-800 shrink-0">
            </div>

            <div class="pt-4 border-t border-white/10 flex justify-end gap-2">
                <button type="button" onclick="closeModal('editRoleModal')" class="px-4 py-2 bg-white/10 hover:bg-white/15 text-slate-300 rounded-xl text-xs font-semibold transition-all">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-400 hover:to-blue-500 text-white rounded-xl text-xs font-semibold shadow-md transition-all flex items-center gap-1.5">
                    <i class="fas fa-save"></i> Simpan Perubahan IE
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditRoleModal(id, name, roleId, medicRoleId, subRoleId, isInterviewer) {
    document.getElementById('modalStaffName').textContent = 'Anggota: ' + name;
    document.getElementById('editRoleForm').action = '/portal/ie/roles/' + id + '/update';
    document.getElementById('modalRoleId').value = roleId || '';
    document.getElementById('modalMedicRoleId').value = medicRoleId || '';
    document.getElementById('modalSubRoleId').value = subRoleId || '';
    document.getElementById('modalIsInterviewer').checked = !!isInterviewer;
    document.getElementById('editRoleModal').classList.remove('hidden');
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
}
</script>
@endsection
