@extends('layouts.app')

@section('title', 'PND: Verifikasi Operasi — Portal Alta Hospital')

@section('content')
<div class="min-h-screen pt-20 pb-10" style="background: linear-gradient(135deg, #0b1329 0%, #0c2461 50%, #0b1329 100%);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400">
                        <i class="fas fa-hospital text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white tracking-wide">PND: Verifikasi Pengajuan Operasi</h1>
                        <p class="text-white/50 text-sm mt-0.5">Kelola dan verifikasi jadwal serta kelayakan operasi staf medis Alta Hospital</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('portal.pnd.cert-index') }}" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-xl text-sm font-medium transition-all flex items-center gap-2 border border-white/10">
                    <i class="fas fa-award text-emerald-400"></i> Sertifikat Operasi
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-500/20 border border-emerald-500/40 rounded-xl text-emerald-300 text-sm flex items-center gap-3">
            <i class="fas fa-check-circle text-lg"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 p-4 bg-rose-500/20 border border-rose-500/40 rounded-xl text-rose-300 text-sm flex items-center gap-3">
            <i class="fas fa-exclamation-triangle text-lg"></i>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        {{-- Filter Status --}}
        <div class="flex flex-wrap items-center gap-2 mb-6">
            <a href="{{ route('portal.pnd.operations') }}"
               class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all {{ !request('status') ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/30' : 'bg-white/10 text-white/70 hover:bg-white/15' }}">
                Semua Status
            </a>
            <a href="{{ route('portal.pnd.operations', ['status' => 'pending']) }}"
               class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all {{ request('status') === 'pending' ? 'bg-amber-500 text-white shadow-lg shadow-amber-500/30' : 'bg-white/10 text-white/70 hover:bg-white/15' }}">
                <i class="fas fa-clock mr-1"></i> Menunggu Verifikasi
            </a>
            <a href="{{ route('portal.pnd.operations', ['status' => 'approved']) }}"
               class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all {{ request('status') === 'approved' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/30' : 'bg-white/10 text-white/70 hover:bg-white/15' }}">
                <i class="fas fa-check-circle mr-1"></i> Disetujui
            </a>
            <a href="{{ route('portal.pnd.operations', ['status' => 'rejected']) }}"
               class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all {{ request('status') === 'rejected' ? 'bg-rose-600 text-white shadow-lg shadow-rose-600/30' : 'bg-white/10 text-white/70 hover:bg-white/15' }}">
                <i class="fas fa-times-circle mr-1"></i> Ditolak
            </a>
        </div>

        {{-- Table Card --}}
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden shadow-2xl">
            @if($items->isEmpty())
            <div class="py-16 text-center text-white/40">
                <i class="fas fa-clipboard-check text-5xl mb-3 block opacity-40"></i>
                <p class="text-base font-medium">Tidak ada pengajuan operasi pada filter ini.</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-white/10 text-xs text-white/50 uppercase tracking-wider bg-white/[0.02]">
                        <tr>
                            <th class="text-left px-5 py-3.5">Pemohon</th>
                            <th class="text-left px-5 py-3.5">Pasien & Tindakan</th>
                            <th class="text-left px-5 py-3.5">Jadwal & DPJP</th>
                            <th class="text-left px-5 py-3.5">Asisten</th>
                            <th class="text-left px-5 py-3.5">Status</th>
                            <th class="text-right px-5 py-3.5">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($items as $item)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-5 py-4">
                                <div class="text-white font-semibold">{{ $item->user?->name ?? 'User #'.$item->user_id }}</div>
                                <div class="text-xs text-emerald-400/80 font-mono">{{ $item->user?->staff_id ?? '-' }}</div>
                                <div class="text-[11px] text-white/40 mt-1">Diajukan: {{ $item->created_at->format('d M Y H:i') }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="text-white font-medium flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                        {{ $item->jenis_operasi }}
                                    </span>
                                </div>
                                <div class="text-white text-sm font-semibold mt-1">
                                    <i class="fas fa-user-injured text-xs text-white/40 mr-1"></i> {{ $item->patient_name }}
                                </div>
                                <div class="text-xs text-white/60 mt-0.5 line-clamp-1">
                                    <span class="text-white/40">Diag:</span> {{ $item->diagnosis }}
                                </div>
                                <div class="text-xs text-white/60 line-clamp-1">
                                    <span class="text-white/40">Rencana:</span> {{ $item->planned_procedure }}
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="text-xs text-white/90">
                                    <i class="fas fa-calendar-alt text-white/40 mr-1"></i>
                                    {{ $item->scheduled_at ? $item->scheduled_at->format('d M Y H:i') : 'Menyesuaikan' }}
                                </div>
                                <div class="text-xs text-white/70 mt-1">
                                    <i class="fas fa-user-md text-emerald-400 mr-1"></i>
                                    DPJP: <span class="font-medium text-white">{{ $item->dpjp?->name ?? 'Belum ditentukan' }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                @php $assistants = $item->assistants; @endphp
                                @if(!empty($assistants) && count($assistants) > 0)
                                    <div class="flex flex-wrap gap-1 max-w-[200px]">
                                        @foreach($assistants as $ast)
                                            <span class="text-[11px] bg-white/10 text-white/80 px-2 py-0.5 rounded">
                                                {{ $ast->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-xs text-white/30 italic">Tidak ada</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if($item->status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span> Pending PND
                                    </span>
                                @elseif($item->status === 'approved')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                        <i class="fas fa-check text-[10px]"></i> Disetujui
                                    </span>
                                    @if($item->verifiedByPnd)
                                        <div class="text-[10px] text-white/40 mt-1">oleh {{ $item->verifiedByPnd->name }}</div>
                                    @endif
                                @elseif($item->status === 'rejected')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                        <i class="fas fa-times text-[10px]"></i> Ditolak
                                    </span>
                                    @if($item->pnd_notes)
                                        <div class="text-[11px] text-rose-300/80 mt-1 max-w-[180px] truncate" title="{{ $item->pnd_notes }}">
                                            "{{ $item->pnd_notes }}"
                                        </div>
                                    @endif
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-500/20 text-blue-300 border border-blue-500/30">
                                        <i class="fas fa-flag-checkered text-[10px]"></i> {{ ucfirst($item->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                @if($item->status === 'pending')
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" onclick="openApproveModal({{ $item->id }}, '{{ addslashes($item->patient_name) }}')"
                                                class="px-3 py-1.5 bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 border border-emerald-500/40 rounded-lg text-xs font-semibold transition-all">
                                            <i class="fas fa-check mr-1"></i> Setujui
                                        </button>
                                        <button type="button" onclick="openRejectModal({{ $item->id }}, '{{ addslashes($item->patient_name) }}')"
                                                class="px-3 py-1.5 bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/40 rounded-lg text-xs font-semibold transition-all">
                                            <i class="fas fa-times mr-1"></i> Tolak
                                        </button>
                                    </div>
                                @else
                                    <span class="text-xs text-white/30">Selesai diverifikasi</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($items->hasPages())
            <div class="p-4 border-t border-white/10 bg-white/[0.02]">
                {{ $items->links() }}
            </div>
            @endif
            @endif
        </div>

    </div>
</div>

{{-- Modal Approve --}}
<div id="approveModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm hidden">
    <div class="bg-gray-900 border border-white/15 rounded-2xl w-full max-w-md p-6 shadow-2xl">
        <h3 class="text-lg font-bold text-white mb-2 flex items-center gap-2">
            <i class="fas fa-check-circle text-emerald-400"></i> Setujui Pengajuan Operasi
        </h3>
        <p class="text-white/60 text-sm mb-4">Anda akan menyetujui pengajuan operasi untuk pasien <span id="approvePatient" class="font-semibold text-white"></span>.</p>
        <form id="approveForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-semibold text-white/70 uppercase mb-2">Catatan PND (Opsional)</label>
                <textarea name="pnd_notes" rows="3" placeholder="Contoh: Jadwal OK 2 dikonfirmasi, lengkapi informed consent..."
                          class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-emerald-400"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('approveModal')" class="px-4 py-2 bg-white/10 text-white/70 hover:text-white rounded-xl text-xs font-semibold">Batal</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-semibold">Ya, Setujui Operasi</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Reject --}}
<div id="rejectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm hidden">
    <div class="bg-gray-900 border border-white/15 rounded-2xl w-full max-w-md p-6 shadow-2xl">
        <h3 class="text-lg font-bold text-white mb-2 flex items-center gap-2">
            <i class="fas fa-times-circle text-rose-400"></i> Tolak Pengajuan Operasi
        </h3>
        <p class="text-white/60 text-sm mb-4">Berikan alasan penolakan untuk pasien <span id="rejectPatient" class="font-semibold text-white"></span>.</p>
        <form id="rejectForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-semibold text-white/70 uppercase mb-2">Alasan Penolakan (Wajib)</label>
                <textarea name="pnd_notes" rows="3" required placeholder="Jelaskan alasan penolakan pengajuan operasi..."
                          class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-rose-400"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('rejectModal')" class="px-4 py-2 bg-white/10 text-white/70 hover:text-white rounded-xl text-xs font-semibold">Batal</button>
                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-semibold">Tolak Pengajuan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openApproveModal(id, patient) {
    document.getElementById('approvePatient').textContent = patient;
    document.getElementById('approveForm').action = "/portal/pnd/operations/" + id + "/approve";
    document.getElementById('approveModal').classList.remove('hidden');
}
function openRejectModal(id, patient) {
    document.getElementById('rejectPatient').textContent = patient;
    document.getElementById('rejectForm').action = "/portal/pnd/operations/" + id + "/reject";
    document.getElementById('rejectModal').classList.remove('hidden');
}
function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}
</script>
@endsection
