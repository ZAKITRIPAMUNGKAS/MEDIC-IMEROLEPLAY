@extends('layouts.app')

@section('title', 'IE — Pemutihan Duty & Pengecualian Cuti')

@section('content')
<div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-amber-950/30 to-slate-900"></div>
    <div class="absolute inset-0 bg-black/40"></div>

    <div class="relative max-w-7xl mx-auto text-white space-y-6">

        {{-- Header --}}
        <div class="glass-effect rounded-2xl p-6 border border-white/10 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs font-semibold">
                        <i class="fas fa-filter mr-1"></i> Evaluasi Duty Bulanan &lt; 10 Jam
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-500/40 flex items-center justify-center text-amber-400 text-lg">
                        <i class="fas fa-user-clock"></i>
                    </span>
                    Daftar Pemutihan Duty Medis
                </h1>
                <p class="text-slate-300 text-sm mt-1">
                    Medis yang memiliki total duty di bawah 10 jam dalam satu bulan masuk ke daftar pemutihan. IE dapat menetapkan pengecualian berdasarkan riwayat cuti staf.
                </p>
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <button type="button" onclick="openDiscordModal()"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white text-xs font-semibold rounded-xl shadow-lg shadow-indigo-900/40 border border-indigo-500/30 transition-all">
                    <i class="fab fa-discord text-sm"></i> Export Teks Discord
                </button>
                <a href="{{ route('portal.ie.roles.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/15 text-sky-300 text-xs font-semibold rounded-xl border border-sky-500/30 transition-all">
                    <i class="fas fa-user-tag"></i> Manajemen Jabatan
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

        {{-- Filter Periode Bulan --}}
        <div class="glass-effect rounded-2xl p-5 border border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4">
            <form method="GET" action="{{ route('portal.ie.pemutihan.index') }}" class="flex items-center gap-3 w-full sm:w-auto">
                <label class="text-xs font-semibold text-slate-300 uppercase whitespace-nowrap">
                    <i class="far fa-calendar-alt text-amber-400 mr-1"></i> Periode Bulan:
                </label>
                <input type="month" name="period" value="{{ $period }}"
                       class="bg-white/10 text-white border border-white/20 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-amber-400 cursor-pointer">
                <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-white rounded-xl text-xs font-semibold shadow-md transition-all">
                    Tampilkan
                </button>
            </form>

            <div class="text-xs text-slate-400 text-right">
                Rentang Tanggal: <strong class="text-white">{{ \Carbon\Carbon::parse($startOfMonth)->format('d M Y') }}</strong> s/d <strong class="text-white">{{ \Carbon\Carbon::parse($endOfMonth)->format('d M Y') }}</strong>
            </div>
        </div>

        {{-- Table Pemutihan --}}
        <div class="glass-effect rounded-2xl overflow-hidden border border-white/10 shadow-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-white/5 border-b border-white/10 text-slate-300 text-xs font-semibold uppercase tracking-wider">
                            <th class="px-5 py-3.5 text-left">Nama Medis</th>
                            <th class="px-5 py-3.5 text-left">Jabatan</th>
                            <th class="px-5 py-3.5 text-center">Total Duty (Bulan Ini)</th>
                            <th class="px-5 py-3.5 text-left">Riwayat Izin Cuti Bulan Ini</th>
                            <th class="px-5 py-3.5 text-center">Status Pemutihan</th>
                            <th class="px-5 py-3.5 text-center">Aksi Pengecualian IE</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-slate-200">
                        @forelse($pemutihanList as $item)
                        @php
                            $staf = $item['user'];
                            $hours = $item['total_hours'];
                            $hasLeave = $item['has_approved_leave'];
                            $isExempt = $item['is_exempted'];
                        @endphp
                        <tr class="hover:bg-white/5 transition-colors {{ $isExempt ? 'opacity-70 bg-emerald-950/10' : '' }}">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-amber-500/30 to-rose-500/30 border border-white/20 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                                        {{ strtoupper(substr($staf->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="text-white font-semibold text-sm">{{ $staf->name }}</p>
                                        <div class="flex items-center gap-2 text-xs text-slate-400 font-mono">
                                            <span>{{ $staf->staff_id ?? '-' }}</span>
                                            @if($staf->citizen_id)
                                                <span class="text-emerald-300">CID: {{ $staf->citizen_id }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-sky-500/20 text-sky-300 border border-sky-500/30">
                                    {{ $staf->role?->display_name ?? '-' }}
                                </span>
                                @if($staf->medicRole && $staf->medic_role_id !== $staf->role_id)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 ml-1">
                                        🩺 {{ $staf->medicRole->display_name }}
                                    </span>
                                @endif
                                @if($staf->subRole)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-purple-500/20 text-purple-300 border border-purple-500/30 ml-1">
                                        {{ $staf->subRole->short_name }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                    <i class="fas fa-exclamation-triangle mr-1 text-[10px]"></i> {{ $hours }} Jam
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                @if($hasLeave)
                                    <div class="space-y-1">
                                        @foreach($item['leaves'] as $leave)
                                            <div class="text-xs bg-emerald-500/10 border border-emerald-500/30 rounded-lg px-2 py-1 text-emerald-300">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ \Carbon\Carbon::parse($leave->start_date)->format('d M') }} – {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}
                                                <span class="font-bold">({{ $leave->duration_days }} hari)</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-slate-500 text-xs italic">Tidak ada pengajuan cuti yang sah</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                @if($isExempt)
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                        <i class="fas fa-shield-alt text-[10px]"></i> Dikecualikan (Exempt)
                                    </span>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Oleh: {{ $item['exemption']->exemptedBy?->name ?? 'IE' }}</p>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                        <i class="fas fa-user-times text-[10px]"></i> Masuk Pemutihan
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                @if($isExempt)
                                    <form method="POST" action="{{ route('portal.ie.pemutihan.toggle-exemption', $staf) }}" onsubmit="return confirm('Cabut status pengecualian pemutihan untuk {{ addslashes($staf->name) }}?')">
                                        @csrf
                                        <input type="hidden" name="month_period" value="{{ $period }}">
                                        <button type="submit" class="px-3 py-1.5 bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 rounded-xl text-xs font-semibold border border-rose-500/30 transition-all">
                                            <i class="fas fa-undo mr-1"></i> Cabut Pengecualian
                                        </button>
                                    </form>
                                @else
                                    <button type="button"
                                            onclick="openExemptionModal({{ $staf->id }}, '{{ addslashes($staf->name) }}', {{ $hasLeave ? json_encode($item['leaves']->pluck('id')->first()) : 'null' }})"
                                            class="px-3 py-1.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-xl text-xs font-semibold shadow-md transition-all flex items-center gap-1.5 mx-auto">
                                        <i class="fas fa-check-shield text-[10px]"></i> Kecualikan dari Pemutihan
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-emerald-300">
                                <i class="fas fa-check-circle text-3xl mb-2 text-emerald-400 block"></i>
                                Hebat! Tidak ada staf medis yang memiliki duty di bawah 10 jam pada periode {{ $period }}.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

{{-- Modal Pengecualian Pemutihan --}}
<div id="exemptionModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/75 backdrop-blur-sm hidden p-4">
    <div class="glass-effect bg-slate-900 border border-white/20 rounded-2xl max-w-md w-full p-6 text-white shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-white/10 mb-4">
            <div>
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <i class="fas fa-shield-alt text-emerald-400"></i> Pengecualian Pemutihan Duty
                </h3>
                <p class="text-xs text-slate-400" id="exemptStaffName">Staf</p>
            </div>
            <button type="button" onclick="closeModal('exemptionModal')" class="text-slate-400 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="exemptionForm" method="POST" action="" class="space-y-4">
            @csrf
            <input type="hidden" name="month_period" value="{{ $period }}">
            <input type="hidden" name="leave_request_id" id="exemptLeaveRequestId" value="">

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Alasan Pengecualian Pemutihan</label>
                <textarea name="reason" rows="3" required placeholder="Contoh: Menjalani izin cuti resmi yang disetujui / dinas luar..."
                          class="w-full bg-slate-800 text-white border border-white/20 rounded-xl p-3 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-400">Staf memiliki izin cuti resmi yang sah pada periode {{ $period }}.</textarea>
            </div>

            <div class="pt-3 border-t border-white/10 flex justify-end gap-2">
                <button type="button" onclick="closeModal('exemptionModal')" class="px-4 py-2 bg-white/10 text-slate-300 rounded-xl text-xs font-semibold">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-semibold shadow-md flex items-center gap-1.5">
                    <i class="fas fa-check"></i> Konfirmasi Pengecualian
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openExemptionModal(userId, name, leaveId) {
    document.getElementById('exemptStaffName').textContent = 'Anggota: ' + name;
    document.getElementById('exemptionForm').action = '/portal/ie/pemutihan/' + userId + '/toggle-exemption';
    document.getElementById('exemptLeaveRequestId').value = leaveId || '';
    document.getElementById('exemptionModal').classList.remove('hidden');
}

function openDiscordModal() {
    document.getElementById('discordModal').classList.remove('hidden');
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
}

function copyDiscordText() {
    const textarea = document.getElementById('discordExportTextarea');
    if (!textarea) return;

    const text = textarea.value;
    const btn = document.getElementById('btnCopyDiscord');

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(() => {
            showCopiedState(btn);
        }).catch(() => {
            fallbackCopy(textarea, btn);
        });
    } else {
        fallbackCopy(textarea, btn);
    }
}

function fallbackCopy(textarea, btn) {
    textarea.focus();
    textarea.select();
    try {
        document.execCommand('copy');
        showCopiedState(btn);
    } catch (err) {
        alert('Gagal menyalin otomatis. Silakan salin teks manual dari kotak di atas.');
    }
}

function showCopiedState(btn) {
    if (!btn) return;
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check"></i> <span>Berhasil Disalin!</span>';
    btn.classList.remove('from-indigo-600', 'to-purple-600', 'hover:from-indigo-500', 'hover:to-purple-500', 'bg-indigo-600');
    btn.classList.add('bg-emerald-600', 'hover:bg-emerald-500');

    setTimeout(() => {
        btn.innerHTML = originalHTML;
        btn.classList.remove('bg-emerald-600', 'hover:bg-emerald-500');
        btn.classList.add('from-indigo-600', 'to-purple-600', 'hover:from-indigo-500', 'hover:to-purple-500', 'bg-indigo-600');
    }, 2500);
}
</script>

{{-- Modal Export Teks Discord --}}
<div id="discordModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm hidden p-4">
    <div class="glass-effect bg-slate-900 border border-indigo-500/30 rounded-2xl max-w-2xl w-full p-6 text-white shadow-2xl space-y-4">
        <div class="flex items-start justify-between pb-3 border-b border-white/10">
            <div>
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-base">
                        <i class="fab fa-discord"></i>
                    </span>
                    Export Format Pengumuman Discord
                </h3>
                <p class="text-xs text-slate-400 mt-1">
                    Teks ini siap di-copy paste langsung ke channel Discord pengumuman untuk periode <strong>{{ \Carbon\Carbon::parse($startOfMonth)->locale('id')->translatedFormat('F Y') }}</strong>.
                </p>
            </div>
            <button type="button" onclick="closeModal('discordModal')" class="text-slate-400 hover:text-white p-1">
                <i class="fas fa-times text-base"></i>
            </button>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase mb-1.5 flex items-center justify-between">
                <span>Isi Teks Pengumuman (Markdown Discord):</span>
                <span class="text-[11px] text-slate-400 lowercase font-normal">klik tombol di bawah untuk salin otomatis</span>
            </label>
            <textarea id="discordExportTextarea" rows="13" readonly
                      class="w-full bg-slate-950 text-slate-200 border border-white/20 rounded-xl p-3.5 font-mono text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500 leading-relaxed select-all">{{ $discordText }}</textarea>
        </div>

        <div class="pt-3 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="text-[11px] text-slate-400 flex items-center gap-1.5">
                <i class="fas fa-info-circle text-indigo-400"></i>
                Hanya staf dengan status <strong>"Masuk Pemutihan"</strong> yang disertakan.
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="button" onclick="closeModal('discordModal')" class="px-4 py-2 bg-white/10 hover:bg-white/15 text-slate-300 rounded-xl text-xs font-semibold transition-all">
                    Tutup
                </button>
                <button type="button" id="btnCopyDiscord" onclick="copyDiscordText()"
                        class="flex-1 sm:flex-initial px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-semibold shadow-lg shadow-indigo-900/40 transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-copy"></i>
                    <span>Salin Format Discord</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
