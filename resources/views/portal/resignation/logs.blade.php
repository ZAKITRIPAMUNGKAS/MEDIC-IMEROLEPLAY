@extends('layouts.app')

@section('title', 'Log Resign & Arsip Administrasi — Portal Alta Hospital')

@section('content')
<div class="min-h-screen pt-20 pb-12" style="background: linear-gradient(135deg, #0b1329 0%, #0c2461 50%, #0b1329 100%);">
    <div class="max-w-7xl mx-auto px-4">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-emerald-400">
                        <i class="fas fa-archive text-lg"></i>
                    </span>
                    Riwayat Administrasi Log Resign
                </h1>
                <p class="text-white/60 text-sm mt-1">
                    Arsip audit dan rekam jejak pengunduran diri staf medis. Log ini tersimpan permanen dan tidak terhapus saat akun dinonaktifkan.
                </p>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('portal.resignation.manage.ie') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-xs sm:text-sm font-semibold rounded-xl border border-white/20 transition-all">
                    <i class="fas fa-arrow-left"></i> Antrean Denda IE
                </a>
                <button type="button" onclick="window.print()"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-sky-500/20 hover:bg-sky-500/30 text-sky-300 text-xs sm:text-sm font-semibold rounded-xl border border-sky-500/30 transition-all">
                    <i class="fas fa-print"></i> Cetak Arsip
                </button>
            </div>
        </div>

        {{-- Filter & Pencarian --}}
        <div class="mb-6 bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-4 shadow-xl">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 items-end">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-white/60 uppercase mb-1">Cari Anggota / Citizen ID / Jabatan</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Ketik nama anggota, Citizen ID, atau nama IE..."
                               class="w-full pl-9 pr-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-emerald-400">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-white/40">
                            <i class="fas fa-search text-xs"></i>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-white/60 uppercase mb-1">Tanggal Mulai</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-emerald-400">
                </div>

                <div class="flex gap-2">
                    <div class="flex-1">
                        <label class="block text-xs font-semibold text-white/60 uppercase mb-1">Tanggal Sampai</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                               class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-emerald-400">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-sm font-bold shadow-lg transition-all">
                            Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Tabel Log Resign --}}
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden shadow-2xl">
            @if($logs->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-white/40">
                <i class="fas fa-folder-open text-4xl mb-3 opacity-60"></i>
                <p class="text-sm font-medium">Belum ada riwayat Log Resign yang tercatat.</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-white/10 text-xs text-white/50 uppercase tracking-wider bg-white/3">
                        <tr>
                            <th class="text-left px-5 py-3.5">Nama Anggota &amp; Citizen ID</th>
                            <th class="text-left px-5 py-3.5">Jabatan Terakhir</th>
                            <th class="text-left px-5 py-3.5">Tanggal Resign &amp; Nonaktif</th>
                            <th class="text-left px-5 py-3.5">Denda &amp; Status</th>
                            <th class="text-left px-5 py-3.5">Petugas IE Terkait</th>
                            <th class="text-center px-5 py-3.5">Bukti Resign</th>
                            <th class="text-right px-5 py-3.5">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($logs as $log)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="text-white font-bold">{{ $log->member_name }}</div>
                                <div class="text-white/50 text-xs flex items-center gap-1.5 mt-0.5">
                                    <span class="px-1.5 py-0.5 rounded bg-white/10 text-white/80 font-mono text-[11px]">
                                        Citizen ID: {{ $log->citizen_id ?? '-' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-white/80">
                                <div class="font-medium">{{ $log->last_position }}</div>
                                <div class="text-white/40 text-xs">Batch: {{ $log->batch ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <div class="text-white/90 text-xs font-semibold">
                                    Surat: {{ $log->resignation_date?->format('d M Y') }}
                                </div>
                                <div class="text-emerald-300 text-[11px] mt-0.5 flex items-center gap-1 font-medium">
                                    <i class="fas fa-user-slash text-[10px]"></i> Nonaktif: {{ $log->deactivated_at?->format('d M Y H:i') }}
                                </div>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <div class="text-orange-300 font-bold">$ {{ number_format($log->total_fine, 0, ',', '.') }}</div>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                    <i class="fas fa-check"></i> {{ $log->fine_status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-white/70 whitespace-nowrap">
                                <div><span class="text-white/40">Verifikasi:</span> {{ $log->ie_verifier_name ?? '-' }}</div>
                                <div><span class="text-white/40">Penonaktif:</span> {{ $log->ie_deactivator_name }}</div>
                            </td>
                            <td class="px-5 py-3.5 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-1">
                                    {{-- Kantong --}}
                                    @if($log->pocket_proof_url)
                                    <a href="{{ $log->pocket_proof_url }}" target="_blank" title="Foto Kantong"
                                       class="w-7 h-7 rounded-lg bg-purple-500/20 border border-purple-500/30 flex items-center justify-center text-purple-300 hover:scale-110 transition-transform text-xs">
                                        <i class="fas fa-shopping-bag"></i>
                                    </a>
                                    @endif
                                    {{-- Kunci --}}
                                    @if($log->key_proof_url)
                                    <a href="{{ $log->key_proof_url }}" target="_blank" title="Foto Kunci"
                                       class="w-7 h-7 rounded-lg bg-purple-500/20 border border-purple-500/30 flex items-center justify-center text-purple-300 hover:scale-110 transition-transform text-xs">
                                        <i class="fas fa-key"></i>
                                    </a>
                                    @endif
                                    {{-- Surat --}}
                                    @if($log->letter_proof_url)
                                    <a href="{{ $log->letter_proof_url }}" target="_blank" title="Foto Surat Resign"
                                       class="w-7 h-7 rounded-lg bg-purple-500/20 border border-purple-500/30 flex items-center justify-center text-purple-300 hover:scale-110 transition-transform text-xs">
                                        <i class="fas fa-file-alt"></i>
                                    </a>
                                    @endif
                                    {{-- Billing --}}
                                    @if($log->fine_proof_url)
                                    <a href="{{ $log->fine_proof_url }}" target="_blank" title="Foto Billing Denda"
                                       class="w-7 h-7 rounded-lg bg-purple-500/20 border border-purple-500/30 flex items-center justify-center text-purple-300 hover:scale-110 transition-transform text-xs">
                                        <i class="fas fa-receipt"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                <button type="button"
                                        onclick="openLogDetailModal({{ json_encode($log) }})"
                                        class="px-3 py-1.5 bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 border border-emerald-500/30 rounded-lg text-xs font-bold transition-all inline-flex items-center gap-1.5 shadow-sm">
                                    <i class="fas fa-info-circle"></i> Detail Arsip
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-3 border-t border-white/10">
                {{ $logs->links() }}
            </div>
            @endif
        </div>

    </div>
</div>

{{-- MODAL DETAIL ARSIP LOG RESIGN --}}
<div id="logDetailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/85 backdrop-blur-md hidden p-4 overflow-y-auto">
    <div class="bg-gray-900 border border-white/20 rounded-2xl w-full max-w-3xl p-6 shadow-2xl my-auto">
        <div class="flex items-center justify-between pb-4 border-b border-white/10 mb-5">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-emerald-400">
                    <i class="fas fa-id-card text-lg"></i>
                </span>
                <div>
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        Arsip Riwayat Resign: <span id="modalMemberName" class="text-emerald-300"></span>
                    </h3>
                    <p class="text-xs text-white/50">
                        Citizen ID: <span id="modalCitizenId" class="text-white font-medium"></span> • 
                        Status Akun: <span class="px-2 py-0.5 rounded bg-rose-500/20 text-rose-300 font-bold text-[10px]">NOT ACTIVE</span>
                    </p>
                </div>
            </div>
            <button type="button" onclick="closeLogModal()" class="text-white/50 hover:text-white p-2 rounded-lg text-lg">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="space-y-5 text-sm">
            {{-- Identitas & Kronologi --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 bg-white/5 rounded-xl border border-white/10 text-xs">
                <div>
                    <span class="text-white/40 block mb-0.5 uppercase">Jabatan Terakhir</span>
                    <span id="modalPosition" class="text-white font-semibold"></span>
                </div>
                <div>
                    <span class="text-white/40 block mb-0.5 uppercase">Batch / Angkatan</span>
                    <span id="modalBatch" class="text-white font-medium"></span>
                </div>
                <div>
                    <span class="text-white/40 block mb-0.5 uppercase">Tanggal Resign</span>
                    <span id="modalResignDate" class="text-white font-medium"></span>
                </div>
                <div>
                    <span class="text-white/40 block mb-0.5 uppercase">Waktu Dinonaktifkan</span>
                    <span id="modalDeactivatedAt" class="text-emerald-300 font-semibold"></span>
                </div>
            </div>

            {{-- Denda & Petugas IE --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-4 bg-orange-500/10 border border-orange-500/20 rounded-xl text-xs">
                    <p class="font-bold text-orange-300 uppercase tracking-wide mb-2 flex items-center gap-1.5">
                        <i class="fas fa-hand-holding-usd"></i> Rincian Denda Resign
                    </p>
                    <div class="space-y-1 text-white/80">
                        <div class="flex justify-between">
                            <span>Total Denda:</span>
                            <span id="modalFineTotal" class="font-bold text-orange-400"></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Persentase:</span>
                            <span id="modalFinePct" class="font-semibold text-white"></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Status Pembayaran:</span>
                            <span id="modalFineStatus" class="font-bold text-emerald-300"></span>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-white/5 border border-white/10 rounded-xl text-xs">
                    <p class="font-bold text-white/70 uppercase tracking-wide mb-2 flex items-center gap-1.5">
                        <i class="fas fa-user-shield text-sky-400"></i> Petugas IE Berwenang
                    </p>
                    <div class="space-y-1 text-white/80">
                        <div class="flex justify-between">
                            <span>IE Verifikator Denda:</span>
                            <span id="modalVerifier" class="font-semibold text-white"></span>
                        </div>
                        <div class="flex justify-between">
                            <span>IE Eksekutor Nonaktif:</span>
                            <span id="modalDeactivator" class="font-semibold text-white"></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Catatan Eksekusi:</span>
                            <span id="modalNotes" class="text-white/60 italic truncate max-w-[150px]"></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Alasan Resign --}}
            <div class="p-4 bg-white/5 border border-white/10 rounded-xl text-xs">
                <p class="font-bold text-white/70 uppercase tracking-wide mb-2 flex items-center gap-1.5">
                    <i class="fas fa-comment-alt text-amber-400"></i> Alasan Pengunduran Diri
                </p>
                <div class="space-y-3">
                    <div>
                        <span class="text-amber-300 font-bold block mb-0.5">Alasan IC (In-Character):</span>
                        <p id="modalReasonIc" class="text-white/80 whitespace-pre-line leading-relaxed"></p>
                    </div>
                    <div>
                        <span class="text-sky-300 font-bold block mb-0.5">Alasan OOC (Out-Of-Character):</span>
                        <p id="modalReasonOoc" class="text-white/80 whitespace-pre-line leading-relaxed"></p>
                    </div>
                </div>
            </div>

            {{-- 4 Bukti Resign --}}
            <div>
                <p class="font-bold text-white/80 text-xs uppercase tracking-wide mb-3 flex items-center gap-1.5">
                    <i class="fas fa-images text-cyan-400"></i> 4 Berkas Bukti yang Telah Diunggah &amp; Diverifikasi
                </p>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    {{-- Kantong --}}
                    <div class="rounded-xl border border-white/10 overflow-hidden bg-black/40 text-center">
                        <img id="modalImgPocket" src="" alt="Foto Kantong" class="w-full h-24 object-cover cursor-pointer hover:scale-105 transition-transform" onclick="window.open(this.src, '_blank')">
                        <div class="p-1.5 bg-slate-900/90 text-[10px]">
                            <p class="text-white font-bold truncate">Foto Kantong</p>
                            <a id="modalLinkPocket" href="#" target="_blank" class="text-cyan-300 hover:underline">Buka Penuh</a>
                        </div>
                    </div>
                    {{-- Kunci --}}
                    <div class="rounded-xl border border-white/10 overflow-hidden bg-black/40 text-center">
                        <img id="modalImgKey" src="" alt="Foto Kunci" class="w-full h-24 object-cover cursor-pointer hover:scale-105 transition-transform" onclick="window.open(this.src, '_blank')">
                        <div class="p-1.5 bg-slate-900/90 text-[10px]">
                            <p class="text-white font-bold truncate">Foto Kunci</p>
                            <a id="modalLinkKey" href="#" target="_blank" class="text-cyan-300 hover:underline">Buka Penuh</a>
                        </div>
                    </div>
                    {{-- Surat --}}
                    <div class="rounded-xl border border-white/10 overflow-hidden bg-black/40 text-center">
                        <img id="modalImgLetter" src="" alt="Foto Surat Resign" class="w-full h-24 object-cover cursor-pointer hover:scale-105 transition-transform" onclick="window.open(this.src, '_blank')">
                        <div class="p-1.5 bg-slate-900/90 text-[10px]">
                            <p class="text-white font-bold truncate">Surat Resign</p>
                            <a id="modalLinkLetter" href="#" target="_blank" class="text-cyan-300 hover:underline">Buka Penuh</a>
                        </div>
                    </div>
                    {{-- Billing --}}
                    <div class="rounded-xl border border-white/10 overflow-hidden bg-black/40 text-center">
                        <img id="modalImgFine" src="" alt="Foto Billing Denda" class="w-full h-24 object-cover cursor-pointer hover:scale-105 transition-transform" onclick="window.open(this.src, '_blank')">
                        <div class="p-1.5 bg-slate-900/90 text-[10px]">
                            <p class="text-white font-bold truncate">Billing Denda</p>
                            <a id="modalLinkFine" href="#" target="_blank" class="text-cyan-300 hover:underline">Buka Penuh</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-5 border-t border-white/10 mt-6">
            <button type="button" onclick="closeLogModal()" class="px-5 py-2 bg-white/10 hover:bg-white/20 text-white rounded-xl text-xs font-bold transition-all">
                Tutup Arsip
            </button>
        </div>
    </div>
</div>

<script>
function closeLogModal() {
    document.getElementById('logDetailModal').classList.add('hidden');
}

function openLogDetailModal(log) {
    document.getElementById('modalMemberName').innerText = log.member_name || '-';
    document.getElementById('modalCitizenId').innerText = log.citizen_id || '-';
    document.getElementById('modalPosition').innerText = log.last_position || '-';
    document.getElementById('modalBatch').innerText = log.batch || '-';
    document.getElementById('modalResignDate').innerText = log.resignation_date ? new Date(log.resignation_date).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'}) : '-';
    document.getElementById('modalDeactivatedAt').innerText = log.deactivated_at ? new Date(log.deactivated_at).toLocaleString('id-ID', {day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'}) : '-';

    document.getElementById('modalFineTotal').innerText = '$ ' + Number(log.total_fine || 0).toLocaleString('id-ID');
    document.getElementById('modalFinePct').innerText = (log.fine_percentage || 0) + '%';
    document.getElementById('modalFineStatus').innerText = log.fine_status || 'Lunas';

    document.getElementById('modalVerifier').innerText = log.ie_verifier_name || '-';
    document.getElementById('modalDeactivator').innerText = log.ie_deactivator_name || '-';
    document.getElementById('modalNotes').innerText = log.notes || '-';

    document.getElementById('modalReasonIc').innerText = log.reason_ic || '-';
    document.getElementById('modalReasonOoc').innerText = log.reason_ooc || '-';

    const placeholderImg = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="%23666" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>';

    const resolveUrl = (path) => {
        if (!path) return placeholderImg;
        if (path.startsWith('http://') || path.startsWith('https://')) return path;
        return '/storage/' + path.replace(/^\//, '');
    };

    const pUrl = resolveUrl(log.pocket_proof);
    const kUrl = resolveUrl(log.key_proof);
    const lUrl = resolveUrl(log.letter_proof);
    const fUrl = resolveUrl(log.fine_proof);

    document.getElementById('modalImgPocket').src = pUrl;
    document.getElementById('modalLinkPocket').href = pUrl;

    document.getElementById('modalImgKey').src = kUrl;
    document.getElementById('modalLinkKey').href = kUrl;

    document.getElementById('modalImgLetter').src = lUrl;
    document.getElementById('modalLinkLetter').href = lUrl;

    document.getElementById('modalImgFine').src = fUrl;
    document.getElementById('modalLinkFine').href = fUrl;

    document.getElementById('logDetailModal').classList.remove('hidden');
}
</script>
@endsection
