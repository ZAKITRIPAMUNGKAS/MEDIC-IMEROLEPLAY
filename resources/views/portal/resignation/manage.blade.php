@extends('layouts.app')

@section('title', ($stage === 'pnd' ? 'Verifikasi PND' : 'Denda IE') . ' — Resign Portal Alta')

@section('content')
<div class="min-h-screen pt-20 pb-10" style="background: linear-gradient(135deg, #0b1329 0%, #0c2461 50%, #0b1329 100%);">
    <div class="max-w-6xl mx-auto px-4">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                    @if($stage === 'pnd')
                    <i class="fas fa-user-check text-yellow-400"></i> PND — Verifikasi Pengajuan Resign
                    @else
                    <i class="fas fa-hand-holding-usd text-orange-400"></i> IE — Kalkulasi &amp; Pelunasan Denda Resign
                    @endif
                </h1>
                <p class="text-white/50 text-sm mt-0.5">
                    @if($stage === 'pnd')
                    Tinjau alasan pengunduran diri staf dan putuskan persetujuan, penolakan, atau pembatalan.
                    @else
                    Verifikasi pembayaran denda resign dan kelola status permohonan staf.
                    @endif
                </p>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-500/20 border border-emerald-500/40 rounded-xl text-emerald-300 text-sm flex items-center gap-2">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mb-4 p-4 bg-red-500/20 border border-red-500/40 rounded-xl text-red-300 text-sm flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
        @endif

        {{-- Filter --}}
        <form method="GET" class="mb-4 flex gap-2">
            <select name="status" class="px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-sky-400">
                <option value="">Semua Status</option>
                @if($stage === 'pnd')
                <option value="pending_pnd" {{ request('status') === 'pending_pnd' ? 'selected' : '' }}>Menunggu PND</option>
                <option value="pending_ie" {{ request('status') === 'pending_ie' ? 'selected' : '' }}>Diteruskan ke IE</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                @else
                <option value="pending_ie" {{ request('status') === 'pending_ie' ? 'selected' : '' }}>Menunggu Pelunasan IE</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai (Lunas)</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                @endif
            </select>
            <button type="submit" class="px-4 py-2 bg-sky-500/20 hover:bg-sky-500/30 text-sky-300 rounded-xl border border-sky-500/30 text-sm transition-all">
                <i class="fas fa-filter mr-1"></i> Filter
            </button>
        </form>

        {{-- Tabel --}}
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden shadow-2xl">
            @if($requests->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-white/40">
                <i class="fas fa-inbox text-4xl mb-3"></i>
                <p class="text-sm">Tidak ada pengajuan resign pada filter ini.</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-white/10 text-xs text-white/50 uppercase tracking-wider">
                        <tr>
                            <th class="text-left px-5 py-3">Nama Anggota</th>
                            <th class="text-left px-5 py-3">Jabatan</th>
                            <th class="text-left px-5 py-3">Tanggal Surat</th>
                            <th class="text-left px-5 py-3">Status</th>
                            @if($stage === 'ie')
                            <th class="text-left px-5 py-3">Estimasi Denda</th>
                            @endif
                            <th class="text-right px-5 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($requests as $req)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="text-white font-medium">{{ $req->applicant_name }}</div>
                                <div class="text-white/40 text-xs">{{ $req->user?->staff_id }}</div>
                            </td>
                            <td class="px-5 py-3.5 text-white/70">{{ $req->position }}</td>
                            <td class="px-5 py-3.5 text-white/70">{{ $req->letter_date?->format('d M Y') }}</td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                    @if($req->status === 'pending_pnd') bg-yellow-500/20 text-yellow-300 border border-yellow-500/30
                                    @elseif($req->status === 'pending_ie') bg-orange-500/20 text-orange-300 border border-orange-500/30
                                    @elseif($req->status === 'completed') bg-emerald-500/20 text-emerald-300 border border-emerald-500/30
                                    @elseif($req->status === 'cancelled') bg-gray-500/20 text-gray-300 border border-gray-500/30
                                    @else bg-red-500/20 text-red-300 border border-red-500/30 @endif">
                                    {{ $req->status_label }}
                                </span>
                            </td>
                            @if($stage === 'ie')
                            <td class="px-5 py-3.5">
                                <div class="text-orange-300 font-semibold">${{ number_format($req->fine_amount, 0, ',', '.') }}</div>
                                <div class="text-white/40 text-xs">{{ $req->fine_percentage }}% dari gaji</div>
                            </td>
                            @endif
                            <td class="px-5 py-3.5 text-right">
                                {{-- Aksi PND --}}
                                @if($stage === 'pnd')
                                    @if($req->status === 'pending_pnd')
                                    <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                        {{-- Setujui --}}
                                        <form method="POST" action="{{ route('portal.resignation.pnd-approve', $req) }}">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 text-xs font-semibold rounded-lg border border-emerald-500/30 transition-all flex items-center gap-1">
                                                <i class="fas fa-check"></i> Setujui
                                            </button>
                                        </form>
                                        {{-- Tolak --}}
                                        <button type="button" onclick="openPndRejectModal({{ $req->id }}, '{{ addslashes($req->applicant_name) }}')"
                                                class="px-2.5 py-1.5 bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 text-xs font-semibold rounded-lg border border-rose-500/30 transition-all flex items-center gap-1">
                                            <i class="fas fa-times"></i> Tolak
                                        </button>
                                        {{-- Batalkan & Hapus Resign (PND) --}}
                                        <form method="POST" action="{{ route('portal.resignation.pnd-cancel', $req) }}"
                                              onsubmit="return confirm('Yakin ingin membatalkan pengajuan resign {{ addslashes($req->applicant_name) }}? Pengajuan akan dihapus dari sistem dan akun staf dipastikan tetap aktif.');"
                                              class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="px-2.5 py-1.5 bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 text-xs font-semibold rounded-lg border border-rose-500/30 transition-all flex items-center gap-1">
                                                <i class="fas fa-ban"></i> Batalkan Resign
                                            </button>
                                        </form>
                                    </div>
                                    @elseif(in_array($req->status, ['approved_pnd', 'pending_ie']))
                                    <div class="flex items-center justify-end gap-1.5">
                                        <form method="POST" action="{{ route('portal.resignation.pnd-cancel', $req) }}"
                                              onsubmit="return confirm('Yakin ingin membatalkan pengajuan resign {{ addslashes($req->applicant_name) }}? Pengajuan akan dihapus dari sistem dan akun staf dipastikan tetap aktif.');"
                                              class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="px-2.5 py-1.5 bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 text-xs font-semibold rounded-lg border border-rose-500/30 transition-all flex items-center gap-1">
                                                <i class="fas fa-ban"></i> Batalkan Resign
                                            </button>
                                        </form>
                                    </div>
                                    @else
                                    <span class="text-white/30 text-xs">—</span>
                                    @endif

                                {{-- Aksi IE --}}
                                @elseif($stage === 'ie')
                                    @if($req->status === 'pending_ie')
                                    <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                        {{-- Lunas & Nonaktifkan --}}
                                        <form method="POST" action="{{ route('portal.resignation.ie-verify', $req) }}">
                                            @csrf
                                            <button type="submit"
                                                    onclick="return confirm('Konfirmasi denda sebesar ${{ number_format($req->fine_amount, 0, ',', '.') }} sudah dilunasi? Akun {{ addslashes($req->applicant_name) }} akan dinonaktifkan.')"
                                                    class="px-2.5 py-1.5 bg-sky-500/20 hover:bg-sky-500/30 text-sky-300 text-xs font-semibold rounded-lg border border-sky-500/30 transition-all flex items-center gap-1">
                                                <i class="fas fa-check-double"></i> Lunas &amp; Nonaktifkan
                                            </button>
                                        </form>
                                        {{-- Batalkan & Hapus Resign (IE) --}}
                                        <form method="POST" action="{{ route('portal.resignation.ie-cancel', $req) }}"
                                              onsubmit="return confirm('Batalkan resign untuk {{ addslashes($req->applicant_name) }}? Beban denda dihapuskan, pengajuan dihapus, dan akun staf tetap aktif.');"
                                              class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="px-2.5 py-1.5 bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 text-xs font-semibold rounded-lg border border-rose-500/30 transition-all flex items-center gap-1">
                                                <i class="fas fa-ban"></i> Batalkan Resign
                                            </button>
                                        </form>
                                    </div>
                                    @else
                                    <span class="text-white/30 text-xs">—</span>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-3 border-t border-white/10">
                {{ $requests->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal PND Reject --}}
<div id="pndRejectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm hidden">
    <div class="bg-gray-900 border border-white/15 rounded-2xl w-full max-w-md p-6 shadow-2xl">
        <h3 class="text-lg font-bold text-white mb-2 flex items-center gap-2">
            <i class="fas fa-times-circle text-rose-400"></i> Tolak Permohonan Resign (PND)
        </h3>
        <p class="text-white/60 text-sm mb-4">Berikan alasan penolakan resign untuk staf <span id="rejectApplicantName" class="font-semibold text-white"></span>.</p>
        <form id="pndRejectForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-semibold text-white/70 uppercase mb-2">Alasan Penolakan <span class="text-rose-400">*</span></label>
                <textarea name="pnd_notes" rows="3" required placeholder="Contoh: Masih terikat dinas operasi khusus atau masa stase wajib..."
                          class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-rose-400"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('pndRejectModal')" class="px-4 py-2 bg-white/10 text-white/70 hover:text-white rounded-xl text-xs font-semibold">Tutup</button>
                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-semibold">Tolak Pengajuan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal PND Cancel --}}
<div id="pndCancelModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm hidden">
    <div class="bg-gray-900 border border-white/15 rounded-2xl w-full max-w-md p-6 shadow-2xl">
        <h3 class="text-lg font-bold text-white mb-2 flex items-center gap-2">
            <i class="fas fa-ban text-amber-400"></i> Batalkan Pengajuan Resign (PND)
        </h3>
        <p class="text-white/60 text-sm mb-4">Batalkan pengajuan resign staf <span id="cancelPndApplicantName" class="font-semibold text-white"></span>. Akun staf akan dipastikan tetap aktif.</p>
        <form id="pndCancelForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-semibold text-white/70 uppercase mb-2">Alasan Pembatalan <span class="text-amber-400">*</span></label>
                <textarea name="pnd_notes" rows="3" required placeholder="Contoh: Pemohon sepakat membatalkan permohonan resign dan melanjutkan dinas..."
                          class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-amber-400"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('pndCancelModal')" class="px-4 py-2 bg-white/10 text-white/70 hover:text-white rounded-xl text-xs font-semibold">Tutup</button>
                <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-xs font-semibold">Ya, Batalkan Resign</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal IE Cancel --}}
<div id="ieCancelModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm hidden">
    <div class="bg-gray-900 border border-white/15 rounded-2xl w-full max-w-md p-6 shadow-2xl">
        <h3 class="text-lg font-bold text-white mb-2 flex items-center gap-2">
            <i class="fas fa-ban text-rose-400"></i> Batalkan Pengajuan Resign (IE)
        </h3>
        <p class="text-white/60 text-sm mb-4">Batalkan resign untuk <span id="cancelIeApplicantName" class="font-semibold text-white"></span>. Beban denda akan dihapuskan dan akun staf tetap aktif.</p>
        <form id="ieCancelForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-semibold text-white/70 uppercase mb-2">Alasan Pembatalan IE <span class="text-rose-400">*</span></label>
                <textarea name="ie_notes" rows="3" required placeholder="Contoh: Kesepakatan perpanjangan kontrak medis / pembatalan resign dari kedua pihak..."
                          class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-rose-400"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('ieCancelModal')" class="px-4 py-2 bg-white/10 text-white/70 hover:text-white rounded-xl text-xs font-semibold">Tutup</button>
                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-semibold">Batalkan Resign Ini</button>
            </div>
        </form>
    </div>
</div>

<script>
function openPndRejectModal(id, name) {
    document.getElementById('rejectApplicantName').textContent = name;
    document.getElementById('pndRejectForm').action = "/portal/resignation/" + id + "/pnd-reject";
    document.getElementById('pndRejectModal').classList.remove('hidden');
}

function openPndCancelModal(id, name) {
    document.getElementById('cancelPndApplicantName').textContent = name;
    document.getElementById('pndCancelForm').action = "/portal/resignation/" + id + "/pnd-cancel";
    document.getElementById('pndCancelModal').classList.remove('hidden');
}

function openIeCancelModal(id, name) {
    document.getElementById('cancelIeApplicantName').textContent = name;
    document.getElementById('ieCancelForm').action = "/portal/resignation/" + id + "/ie-cancel";
    document.getElementById('ieCancelModal').classList.remove('hidden');
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
}
</script>
@endsection
