@extends('layouts.app')
@section('title', 'Kelola Pengajuan Cuti — Alta Hospital')

@section('content')
<div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-rose-950/40 to-slate-900"></div>
    <div class="absolute inset-0 bg-black/30"></div>

    <div class="relative max-w-7xl mx-auto text-white space-y-6">

        {{-- Header --}}
        <div class="glass-effect rounded-2xl p-6 border border-white/10 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('portal.leave.index') }}" class="text-xs text-rose-300 hover:text-white transition-colors">
                        <i class="fas fa-arrow-left mr-1"></i> Pengajuan Saya
                    </a>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-rose-500/20 border border-rose-500/40 flex items-center justify-center text-rose-400 text-lg">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                    Kelola Pengajuan Cuti (Divisi IE)
                </h1>
                <p class="text-slate-300 text-sm mt-1">Verifikasi dan kelola persetujuan izin cuti seluruh staf medis Alta Hospital oleh Divisi IE &amp; Manajemen</p>
            </div>

            <div class="flex items-center gap-3">
                <span class="px-3.5 py-2 rounded-xl bg-rose-500/15 border border-rose-500/30 text-rose-300 text-xs font-semibold flex items-center gap-2">
                    <i class="fas fa-user-shield"></i> Otoritas Divisi IE
                </span>
            </div>
        </div>

        {{-- Flash Notification --}}
        @if(session('success'))
            <div class="bg-emerald-500/20 border border-emerald-500/40 rounded-xl px-4 py-3 text-emerald-300 text-sm flex items-center gap-2 shadow-lg">
                <i class="fas fa-check-circle text-emerald-400"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-rose-500/20 border border-rose-500/40 rounded-xl px-4 py-3 text-rose-300 text-sm flex items-center gap-2 shadow-lg">
                <i class="fas fa-exclamation-triangle text-rose-400"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Status Filter Tabs --}}
        <div class="flex flex-wrap items-center gap-2">
            @php $currentStatus = request('status', ''); @endphp
            <a href="{{ route('portal.leave.manage') }}"
               class="px-4 py-2 rounded-xl text-xs font-semibold transition-all border {{ empty($currentStatus) ? 'bg-rose-500 text-white border-rose-400 shadow-md shadow-rose-500/20' : 'bg-white/5 text-slate-300 border-white/10 hover:bg-white/10' }}">
                Semua Permohonan
            </a>
            <a href="{{ route('portal.leave.manage', ['status' => 'pending']) }}"
               class="px-4 py-2 rounded-xl text-xs font-semibold transition-all border {{ $currentStatus === 'pending' ? 'bg-amber-500 text-white border-amber-400 shadow-md shadow-amber-500/20' : 'bg-white/5 text-slate-300 border-white/10 hover:bg-white/10' }}">
                <i class="fas fa-clock mr-1 text-amber-300"></i> Menunggu Approval
            </a>
            <a href="{{ route('portal.leave.manage', ['status' => 'approved']) }}"
               class="px-4 py-2 rounded-xl text-xs font-semibold transition-all border {{ $currentStatus === 'approved' ? 'bg-emerald-600 text-white border-emerald-400 shadow-md shadow-emerald-500/20' : 'bg-white/5 text-slate-300 border-white/10 hover:bg-white/10' }}">
                <i class="fas fa-check mr-1 text-emerald-300"></i> Disetujui
            </a>
            <a href="{{ route('portal.leave.manage', ['status' => 'rejected']) }}"
               class="px-4 py-2 rounded-xl text-xs font-semibold transition-all border {{ $currentStatus === 'rejected' ? 'bg-rose-600 text-white border-rose-400 shadow-md shadow-rose-500/20' : 'bg-white/5 text-slate-300 border-white/10 hover:bg-white/10' }}">
                <i class="fas fa-times mr-1 text-rose-300"></i> Ditolak
            </a>
        </div>

        {{-- Tabel Pengajuan Cuti --}}
        <div class="glass-effect rounded-2xl overflow-hidden border border-white/10 shadow-2xl">
            <div class="overflow-x-auto w-full">
                <table class="w-full text-sm divide-y divide-white/10" style="display: table !important; min-width: 800px;">
                    <thead>
                        <tr class="bg-white/5 border-b border-white/10 text-slate-300 text-xs font-semibold uppercase tracking-wider">
                            <th class="px-5 py-4 text-left">Pemohon</th>
                            <th class="px-5 py-4 text-left">Periode Cuti</th>
                            <th class="px-5 py-4 text-center">Durasi</th>
                            <th class="px-5 py-4 text-left">Alasan Singkat</th>
                            <th class="px-5 py-4 text-center">Status</th>
                            <th class="px-5 py-4 text-center">Aksi & Persetujuan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-slate-200">
                        @forelse($requests as $req)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-rose-500/30 to-amber-500/30 border border-white/20 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                                        {{ strtoupper(substr($req->applicant_name ?? $req->user?->name ?? 'A', 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="text-white font-medium">{{ $req->applicant_name ?? $req->user?->name ?? '-' }}</p>
                                        <p class="text-xs text-rose-300/80 font-mono">{{ $req->position ?? $req->user?->role?->display_name ?? 'Staf' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="text-xs space-y-0.5">
                                    <p class="text-white font-semibold">
                                        <i class="far fa-calendar-alt text-rose-400 mr-1"></i>
                                        {{ \Carbon\Carbon::parse($req->start_date)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($req->end_date)->format('d M Y') }}
                                    </p>
                                    <p class="text-slate-400 text-[11px]">Surat: {{ $req->letter_date ?? '-' }}</p>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-white/10 text-white border border-white/15">
                                    {{ $req->duration_days }} Hari
                                </span>
                            </td>
                            <td class="px-5 py-4 max-w-xs">
                                <p class="text-xs text-slate-300 truncate" title="Alasan IC: {{ $req->reason_ic }}">
                                    <strong class="text-rose-300">IC:</strong> {{ Str::limit($req->reason_ic, 40) }}
                                </p>
                                <p class="text-[11px] text-slate-400 truncate" title="Alasan OOC: {{ $req->reason_ooc }}">
                                    <strong class="text-slate-400">OOC:</strong> {{ Str::limit($req->reason_ooc, 40) }}
                                </p>
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($req->status === 'approved')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                        <i class="fas fa-check-circle text-[10px]"></i> Disetujui
                                    </span>
                                @elseif($req->status === 'rejected')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                        <i class="fas fa-times-circle text-[10px]"></i> Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                        <i class="fas fa-clock text-[10px]"></i> Menunggu
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('portal.leave.show', $req) }}"
                                       class="px-2.5 py-1.5 rounded-lg bg-sky-500/20 hover:bg-sky-500/30 text-sky-300 border border-sky-500/30 text-xs font-semibold transition-all"
                                       title="Lihat Surat Cuti">
                                        <i class="fas fa-eye mr-1"></i> Surat
                                    </a>

                                    @if($req->status === 'pending')
                                        <form method="POST" action="{{ route('portal.leave.approve', $req) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui permohonan cuti ini?')">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold shadow-md transition-all">
                                                <i class="fas fa-check mr-1"></i> Setujui
                                            </button>
                                        </form>

                                        <button type="button"
                                                onclick="openRejectModal({{ $req->id }}, '{{ addslashes($req->applicant_name ?? $req->user?->name ?? 'Staf') }}')"
                                                class="px-2.5 py-1.5 rounded-lg bg-rose-500/30 hover:bg-rose-500 text-rose-200 hover:text-white border border-rose-500/40 text-xs font-semibold transition-all">
                                            <i class="fas fa-times mr-1"></i> Tolak
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-slate-400">
                                <i class="fas fa-inbox text-3xl mb-2 text-slate-500 block"></i>
                                Tidak ada data pengajuan cuti yang ditemukan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($requests->hasPages())
            <div class="px-5 py-4 border-t border-white/10 flex items-center justify-between text-xs text-slate-400 bg-white/5">
                <span>Menampilkan {{ $requests->firstItem() }}–{{ $requests->lastItem() }} dari {{ $requests->total() }} pengajuan</span>
                <div>
                    {{ $requests->links() }}
                </div>
            </div>
            @endif
        </div>

    </div>
</div>

{{-- Reject Confirmation Modal --}}
<div id="rejectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm hidden p-4">
    <div class="glass-effect bg-slate-900/95 border border-rose-500/30 rounded-2xl max-w-md w-full p-6 text-white shadow-2xl">
        <div class="flex items-center gap-3 mb-4">
            <span class="w-10 h-10 rounded-xl bg-rose-500/20 border border-rose-500/40 flex items-center justify-center text-rose-400 text-lg">
                <i class="fas fa-exclamation-circle"></i>
            </span>
            <div>
                <h3 class="text-lg font-bold text-white">Tolak Permohonan Cuti</h3>
                <p class="text-xs text-slate-400" id="rejectApplicantName">Nama Pemohon</p>
            </div>
        </div>

        <form id="rejectForm" method="POST" action="" class="space-y-4">
            @csrf
            <div>
                <label for="rejectNotes" class="block text-xs font-semibold text-slate-300 mb-1.5">Alasan / Catatan Penolakan</label>
                <textarea id="rejectNotes" name="notes" rows="3" required
                          placeholder="Tuliskan alasan penolakan permohonan cuti..."
                          class="w-full bg-white/10 text-white placeholder-slate-400 border border-white/20 rounded-xl p-3 text-sm focus:outline-none focus:ring-2 focus:ring-rose-400 transition-all"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeRejectModal()"
                        class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/15 text-slate-300 text-xs font-semibold transition-all">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-semibold shadow-md transition-all">
                    Konfirmasi Tolak
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openRejectModal(id, name) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    const nameEl = document.getElementById('rejectApplicantName');

    form.action = `/portal/leave/${id}/reject`;
    nameEl.textContent = 'Pemohon: ' + name;
    modal.classList.remove('hidden');
}

function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    modal.classList.add('hidden');
}
</script>
@endsection
