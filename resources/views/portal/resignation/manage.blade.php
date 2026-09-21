@extends('layouts.app')

@section('title', ($stage === 'pnd' ? 'Verifikasi PND' : 'Manajemen Resign IE') . ' — Portal Alta Hospital')

@section('content')
<div class="min-h-screen pt-20 pb-12" style="background: linear-gradient(135deg, #0b1329 0%, #0c2461 50%, #0b1329 100%);">
    <div class="max-w-6xl mx-auto px-4">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white flex items-center gap-3">
                    @if($stage === 'pnd')
                    <span class="w-10 h-10 rounded-xl bg-blue-500/20 border border-blue-500/40 flex items-center justify-center text-blue-400">
                        <i class="fas fa-user-check text-lg"></i>
                    </span>
                    <span>PND — Verifikasi Permohonan Resign</span>
                    @else
                    <span class="w-10 h-10 rounded-xl bg-orange-500/20 border border-orange-500/40 flex items-center justify-center text-orange-400">
                        <i class="fas fa-hand-holding-usd text-lg"></i>
                    </span>
                    <span>IE — Verifikasi Denda, Bukti &amp; Penonaktifan Resign</span>
                    @endif
                </h1>
                <p class="text-white/60 text-sm mt-1">
                    @if($stage === 'pnd')
                    Tinjau berkas pengajuan resign anggota rumah sakit sebelum diproses kalkulasi denda oleh Divisi IE.
                    @else
                    Kalkulasi denda, cross-check 4 bukti resign (kantong, kunci, surat, billing), dan konfirmasi penonaktifan akhir.
                    @endif
                </p>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap">
                @if($stage === 'ie')
                <a href="{{ route('portal.resignation.logs') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs sm:text-sm font-bold rounded-xl shadow-lg shadow-emerald-950/40 transition-all">
                    <i class="fas fa-archive"></i> Buka Arsip Log Resign
                </a>
                @endif
                <a href="{{ route('portal.resignation.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white text-xs sm:text-sm font-semibold rounded-xl border border-white/20 transition-all">
                    <i class="fas fa-eye"></i> Portal Saya
                </a>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-500/20 border border-emerald-500/40 rounded-xl text-emerald-300 text-sm flex items-center gap-3 shadow-lg shadow-emerald-950/20">
            <i class="fas fa-check-circle text-lg shrink-0"></i> {{ session('success') }}
        </div>
        @endif
        @if(session('info'))
        <div class="mb-4 p-4 bg-sky-500/20 border border-sky-500/40 rounded-xl text-sky-300 text-sm flex items-center gap-3 shadow-lg shadow-sky-950/20">
            <i class="fas fa-info-circle text-lg shrink-0"></i> {{ session('info') }}
        </div>
        @endif
        @if(session('error'))
        <div class="mb-4 p-4 bg-rose-500/20 border border-rose-500/40 rounded-xl text-rose-300 text-sm flex items-center gap-3 shadow-lg shadow-rose-950/20">
            <i class="fas fa-exclamation-circle text-lg shrink-0"></i> {{ session('error') }}
        </div>
        @endif

        {{-- Filter Status --}}
        <form method="GET" class="mb-5 flex flex-wrap items-center gap-2.5">
            <div class="relative">
                <select name="status" onchange="this.form.submit()" class="px-3.5 py-2 pr-8 bg-white/10 border border-white/20 rounded-xl text-white text-xs sm:text-sm focus:outline-none focus:border-sky-400 appearance-none cursor-pointer">
                    <option value="all" class="bg-slate-900 text-white">Semua Status</option>
                    @if($stage === 'pnd')
                    <option value="pending_pnd" {{ request('status') === 'pending_pnd' ? 'selected' : '' }} class="bg-slate-900 text-white">Menunggu Verifikasi PND</option>
                    <option value="pending_ie" {{ request('status') === 'pending_ie' ? 'selected' : '' }} class="bg-slate-900 text-white">Diteruskan ke IE</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }} class="bg-slate-900 text-white">Ditolak</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }} class="bg-slate-900 text-white">Dibatalkan</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }} class="bg-slate-900 text-white">Selesai</option>
                    @else
                    <option value="" {{ request('status') === null || request('status') === '' ? 'selected' : '' }} class="bg-slate-900 text-white">Perlu Tindakan IE (Semua Tahap Aktif)</option>
                    <option value="pending_ie" {{ request('status') === 'pending_ie' ? 'selected' : '' }} class="bg-slate-900 text-white">1. Kalkulasi Denda IE</option>
                    <option value="pending_proof" {{ request('status') === 'pending_proof' ? 'selected' : '' }} class="bg-slate-900 text-white">2. Menunggu Anggota Upload Bukti</option>
                    <option value="proof_submitted" {{ request('status') === 'proof_submitted' ? 'selected' : '' }} class="bg-slate-900 text-white">3. Bukti Masuk (Perlu Verifikasi Akhir)</option>
                    <option value="proof_revision" {{ request('status') === 'proof_revision' ? 'selected' : '' }} class="bg-slate-900 text-white">4. Revisi Bukti Diminta</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }} class="bg-slate-900 text-white">5. Selesai (Not Active)</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }} class="bg-slate-900 text-white">Dibatalkan</option>
                    @endif
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-white/50">
                    <i class="fas fa-chevron-down text-xs"></i>
                </div>
            </div>
            <button type="submit" class="px-4 py-2 bg-sky-500/20 hover:bg-sky-500/30 text-sky-300 rounded-xl border border-sky-500/30 text-xs sm:text-sm font-semibold transition-all">
                <i class="fas fa-filter mr-1"></i> Terapkan Filter
            </button>
        </form>

        {{-- Tabel Pengajuan --}}
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden shadow-2xl">
            @if($requests->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-white/40">
                <i class="fas fa-inbox text-4xl mb-3 opacity-60"></i>
                <p class="text-sm font-medium">Tidak ada permohonan resign pada filter ini.</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-white/10 text-xs text-white/50 uppercase tracking-wider bg-white/3">
                        <tr>
                            <th class="text-left px-5 py-3.5">Nama Staf &amp; ID</th>
                            <th class="text-left px-5 py-3.5">Jabatan Terakhir</th>
                            <th class="text-left px-5 py-3.5">Tanggal Surat</th>
                            <th class="text-left px-5 py-3.5">Status Alur</th>
                            @if($stage === 'ie')
                            <th class="text-left px-5 py-3.5">Denda Resign</th>
                            <th class="text-center px-5 py-3.5">4 Berkas Bukti</th>
                            @endif
                            <th class="text-right px-5 py-3.5">Aksi Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($requests as $req)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="text-white font-semibold">{{ $req->applicant_name }}</div>
                                <div class="text-white/40 text-xs flex items-center gap-2 mt-0.5">
                                    <span>ID: {{ $req->user?->staff_id ?? '-' }}</span>
                                    <span>•</span>
                                    <span>Citizen: {{ $req->user?->citizen_id ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-white/80">
                                <div class="font-medium">{{ $req->position }}</div>
                                @if($req->batch)
                                <div class="text-white/40 text-xs">Batch: {{ $req->batch }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-white/70 whitespace-nowrap">
                                {{ $req->letter_date?->format('d M Y') }}
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold border inline-flex items-center gap-1.5
                                    @if($req->status === 'pending_pnd') bg-yellow-500/20 text-yellow-300 border-yellow-500/30
                                    @elseif($req->status === 'pending_ie') bg-orange-500/20 text-orange-300 border-orange-500/30
                                    @elseif($req->status === 'pending_proof') bg-purple-500/20 text-purple-300 border-purple-500/30
                                    @elseif($req->status === 'proof_submitted') bg-sky-500/20 text-sky-300 border-sky-500/30 animate-pulse
                                    @elseif($req->status === 'proof_revision') bg-rose-500/20 text-rose-300 border-rose-500/30
                                    @elseif($req->status === 'completed') bg-emerald-500/20 text-emerald-300 border-emerald-500/30
                                    @elseif($req->status === 'cancelled') bg-gray-500/20 text-gray-300 border-gray-500/30
                                    @else bg-red-500/20 text-red-300 border-red-500/30 @endif">
                                    @if($req->status === 'proof_submitted')
                                    <span class="w-2 h-2 rounded-full bg-sky-400"></span>
                                    @endif
                                    {{ $req->status_label }}
                                </span>
                            </td>

                            {{-- Kolom IE: Denda & Bukti --}}
                            @if($stage === 'ie')
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <div class="text-orange-300 font-bold text-sm">$ {{ number_format($req->fine_amount, 0, ',', '.') }}</div>
                                <div class="text-white/40 text-xs">{{ $req->fine_percentage }}% dari gapok (${{ number_format($req->base_salary, 0, ',', '.') }})</div>
                            </td>
                            <td class="px-5 py-3.5 text-center whitespace-nowrap">
                                @if($req->hasAllProofs())
                                <button type="button"
                                        onclick="openProofPreviewModal({{ $req->id }}, '{{ addslashes($req->applicant_name) }}', '{{ $req->pocket_proof_url }}', '{{ $req->key_proof_url }}', '{{ $req->letter_proof_url }}', '{{ $req->fine_proof_url }}')"
                                        class="px-2.5 py-1 bg-cyan-500/20 hover:bg-cyan-500/30 text-cyan-300 rounded-lg border border-cyan-500/30 text-xs font-semibold inline-flex items-center gap-1 transition-all">
                                    <i class="fas fa-images"></i> 4 Bukti Lengkap
                                </button>
                                @elseif($req->status === 'pending_proof')
                                <span class="text-purple-300/80 text-xs italic">Menunggu upload</span>
                                @elseif($req->status === 'proof_revision')
                                <span class="text-rose-300/80 text-xs italic">Diminta revisi</span>
                                @else
                                <span class="text-white/30 text-xs">—</span>
                                @endif
                            </td>
                            @endif

                            {{-- Kolom Aksi --}}
                            <td class="px-5 py-3.5 text-right whitespace-nowrap">
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
                                        {{-- Batalkan Resign (PND) --}}
                                        <form method="POST" action="{{ route('portal.resignation.pnd-cancel', $req) }}"
                                              onsubmit="return confirm('Yakin ingin membatalkan pengajuan resign {{ addslashes($req->applicant_name) }}? Pengajuan akan dihapus dan akun tetap aktif.');"
                                              class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 bg-gray-500/20 hover:bg-gray-500/30 text-gray-300 text-xs font-semibold rounded-lg border border-gray-500/30 transition-all flex items-center gap-1">
                                                <i class="fas fa-ban"></i> Batal
                                            </button>
                                        </form>
                                    </div>
                                    @elseif(in_array($req->status, ['approved_pnd', 'pending_ie']))
                                    <div class="flex items-center justify-end gap-1.5">
                                        <form method="POST" action="{{ route('portal.resignation.pnd-cancel', $req) }}"
                                              onsubmit="return confirm('Yakin ingin membatalkan pengajuan resign {{ addslashes($req->applicant_name) }}?');"
                                              class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 text-xs font-semibold rounded-lg border border-rose-500/30 transition-all flex items-center gap-1">
                                                <i class="fas fa-ban"></i> Batalkan Resign
                                            </button>
                                        </form>
                                    </div>
                                    @else
                                    <span class="text-white/20 text-xs">—</span>
                                    @endif

                                {{-- Aksi IE --}}
                                @elseif($stage === 'ie')
                                    {{-- Tahap 1 IE: Konfirmasi Denda & Pindahkan ke Upload Bukti --}}
                                    @if($req->status === 'pending_ie')
                                    <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                        <form method="POST" action="{{ route('portal.resignation.ie-verify', $req) }}">
                                            @csrf
                                            <button type="submit"
                                                    onclick="return confirm('Konfirmasi denda sebesar ${{ number_format($req->fine_amount, 0, ',', '.') }} untuk {{ addslashes($req->applicant_name) }}? Data akan dialihkan ke tahap Upload Bukti Resign dan akun staf TETAP AKTIF sampai konfirmasi akhir.')"
                                                    class="px-3 py-1.5 bg-orange-500/20 hover:bg-orange-500/30 text-orange-300 text-xs font-bold rounded-lg border border-orange-500/40 transition-all flex items-center gap-1.5 shadow-sm">
                                                <i class="fas fa-arrow-right"></i> Konfirmasi Denda &amp; Lanjut Bukti
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('portal.resignation.ie-cancel', $req) }}"
                                              onsubmit="return confirm('Batalkan resign untuk {{ addslashes($req->applicant_name) }}?');" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2 py-1.5 bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 text-xs font-semibold rounded-lg border border-rose-500/30 transition-all">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    </div>

                                    {{-- Tahap 2 IE: Cross-Check 4 Bukti Resign & Konfirmasi Akhir Penonaktifan --}}
                                    @elseif(in_array($req->status, ['proof_submitted', 'pending_proof', 'proof_revision']))
                                    <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                        {{-- Tombol Periksa Bukti (Membuka Modal Review & Keputusan) --}}
                                        <button type="button"
                                                onclick="openIeReviewModal({{ $req->id }}, '{{ addslashes($req->applicant_name) }}', '{{ $req->user?->citizen_id ?? '-' }}', '{{ addslashes($req->position) }}', '{{ number_format($req->fine_amount, 0, ',', '.') }}', '{{ $req->pocket_proof_url ?? '' }}', '{{ $req->key_proof_url ?? '' }}', '{{ $req->letter_proof_url ?? '' }}', '{{ $req->fine_proof_url ?? '' }}', '{{ addslashes($req->proof_revision_notes ?? '') }}')"
                                                class="px-3 py-1.5 bg-sky-500/20 hover:bg-sky-500/30 text-sky-300 text-xs font-bold rounded-lg border border-sky-500/40 transition-all flex items-center gap-1.5 shadow-sm">
                                            <i class="fas fa-search-plus"></i> Cross-Check Bukti
                                        </button>

                                        <form method="POST" action="{{ route('portal.resignation.ie-cancel', $req) }}"
                                              onsubmit="return confirm('Batalkan pengajuan resign {{ addslashes($req->applicant_name) }}? Akun akan tetap aktif.');" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2 py-1.5 bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 text-xs font-semibold rounded-lg border border-rose-500/30 transition-all" title="Batalkan Resign">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    </div>

                                    @elseif($req->status === 'completed')
                                    <span class="text-emerald-300 text-xs font-semibold flex items-center justify-end gap-1">
                                        <i class="fas fa-check-double"></i> Not Active
                                    </span>
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

{{-- ═══════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL REVIEW BUKTI RESIGN & KONFIRMASI AKHIR OLEH IE --}}
{{-- ═══════════════════════════════════════════════════════════════════════════ --}}
<div id="ieReviewModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/85 backdrop-blur-md hidden p-4 overflow-y-auto">
    <div class="bg-gray-900 border border-white/20 rounded-2xl w-full max-w-4xl p-6 shadow-2xl my-auto">
        <div class="flex items-center justify-between pb-4 border-b border-white/10 mb-4">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-sky-500/20 border border-sky-500/40 flex items-center justify-center text-sky-400">
                    <i class="fas fa-clipboard-check text-lg"></i>
                </span>
                <div>
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        Cross-Check Bukti Resign: <span id="reviewApplicantName" class="text-cyan-300 font-extrabold"></span>
                    </h3>
                    <p class="text-xs text-white/50">
                        Citizen ID: <span id="reviewCitizenId" class="text-white font-medium"></span> • 
                        Jabatan: <span id="reviewPosition" class="text-white font-medium"></span> • 
                        Denda: <span id="reviewFineAmount" class="text-orange-400 font-bold">$ </span>
                    </p>
                </div>
            </div>
            <button type="button" onclick="closeModal('ieReviewModal')" class="text-white/50 hover:text-white p-2 rounded-lg text-lg">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- 4 Bukti Resign Galeri --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            {{-- 1. Foto Kantong --}}
            <div class="bg-white/5 border border-white/10 rounded-xl p-3 flex flex-col justify-between">
                <p class="text-xs font-bold text-white mb-2 flex items-center justify-between">
                    <span>1. Foto Kantong</span>
                    <span class="text-[9px] uppercase px-1.5 py-0.5 bg-purple-500/20 text-purple-300 rounded font-semibold">Full Layar</span>
                </p>
                <div class="rounded-lg overflow-hidden border border-white/10 bg-black/50 h-36 flex items-center justify-center">
                    <img id="reviewImgPocket" src="" alt="Foto Kantong" class="w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform" onclick="window.open(this.src, '_blank')">
                </div>
                <a id="reviewLinkPocket" href="#" target="_blank" class="mt-2 text-[11px] text-sky-400 hover:text-sky-300 flex items-center justify-center gap-1 font-medium">
                    <i class="fas fa-expand"></i> Buka Layar Penuh
                </a>
            </div>

            {{-- 2. Foto Kunci --}}
            <div class="bg-white/5 border border-white/10 rounded-xl p-3 flex flex-col justify-between">
                <p class="text-xs font-bold text-white mb-2 flex items-center justify-between">
                    <span>2. Foto Kunci</span>
                    <span class="text-[9px] uppercase px-1.5 py-0.5 bg-purple-500/20 text-purple-300 rounded font-semibold">Tercabut</span>
                </p>
                <div class="rounded-lg overflow-hidden border border-white/10 bg-black/50 h-36 flex items-center justify-center">
                    <img id="reviewImgKey" src="" alt="Foto Kunci" class="w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform" onclick="window.open(this.src, '_blank')">
                </div>
                <a id="reviewLinkKey" href="#" target="_blank" class="mt-2 text-[11px] text-sky-400 hover:text-sky-300 flex items-center justify-center gap-1 font-medium">
                    <i class="fas fa-expand"></i> Buka Layar Penuh
                </a>
            </div>

            {{-- 3. Foto Surat Resign --}}
            <div class="bg-white/5 border border-white/10 rounded-xl p-3 flex flex-col justify-between">
                <p class="text-xs font-bold text-white mb-2 flex items-center justify-between">
                    <span>3. Foto Surat</span>
                    <span class="text-[9px] uppercase px-1.5 py-0.5 bg-purple-500/20 text-purple-300 rounded font-semibold">Surat Resmi</span>
                </p>
                <div class="rounded-lg overflow-hidden border border-white/10 bg-black/50 h-36 flex items-center justify-center">
                    <img id="reviewImgLetter" src="" alt="Foto Surat Resign" class="w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform" onclick="window.open(this.src, '_blank')">
                </div>
                <a id="reviewLinkLetter" href="#" target="_blank" class="mt-2 text-[11px] text-sky-400 hover:text-sky-300 flex items-center justify-center gap-1 font-medium">
                    <i class="fas fa-expand"></i> Buka Layar Penuh
                </a>
            </div>

            {{-- 4. Foto Billing Denda --}}
            <div class="bg-white/5 border border-white/10 rounded-xl p-3 flex flex-col justify-between">
                <p class="text-xs font-bold text-white mb-2 flex items-center justify-between">
                    <span>4. Foto Billing</span>
                    <span class="text-[9px] uppercase px-1.5 py-0.5 bg-purple-500/20 text-purple-300 rounded font-semibold">Denda Resign</span>
                </p>
                <div class="rounded-lg overflow-hidden border border-white/10 bg-black/50 h-36 flex items-center justify-center">
                    <img id="reviewImgFine" src="" alt="Foto Billing Denda" class="w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform" onclick="window.open(this.src, '_blank')">
                </div>
                <a id="reviewLinkFine" href="#" target="_blank" class="mt-2 text-[11px] text-sky-400 hover:text-sky-300 flex items-center justify-center gap-1 font-medium">
                    <i class="fas fa-expand"></i> Buka Layar Penuh
                </a>
            </div>
        </div>

        {{-- Form Minta Revisi (Collapsible / Toggle) --}}
        <div id="revisionSection" class="hidden mb-6 p-4 bg-rose-500/10 border border-rose-500/30 rounded-xl">
            <h4 class="text-sm font-bold text-rose-300 mb-2 flex items-center gap-2">
                <i class="fas fa-redo-alt"></i> Minta Anggota Isi Ulang Bukti Resign
            </h4>
            <p class="text-xs text-white/60 mb-3">Tuliskan secara jelas berkas mana yang tidak sesuai (misal: kunci belum tercabut, kantong tidak full layar, dsb).</p>
            <form id="revisionForm" method="POST">
                @csrf
                <textarea name="revision_notes" rows="3" required placeholder="Contoh: Foto kantong terpotong dan tidak menampilkan full layar. Mohon screenshot ulang secara utuh..."
                          class="w-full px-3 py-2 bg-black/40 border border-white/20 rounded-xl text-white text-xs sm:text-sm focus:outline-none focus:border-rose-400 mb-3"></textarea>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="toggleRevisionSection(false)" class="px-3 py-1.5 bg-white/10 text-white/70 hover:text-white rounded-lg text-xs font-semibold">Batal</button>
                    <button type="submit" class="px-4 py-1.5 bg-rose-600 hover:bg-rose-500 text-white rounded-lg text-xs font-bold shadow">Kirim Permintaan Isi Ulang</button>
                </div>
            </form>
        </div>

        {{-- Form Konfirmasi Akhir Penonaktifan --}}
        <form id="finalConfirmForm" method="POST">
            @csrf
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-xl mb-6">
                <div class="flex items-start gap-3">
                    <i class="fas fa-shield-check text-emerald-400 text-lg mt-0.5 shrink-0"></i>
                    <div>
                        <h4 class="text-sm font-bold text-emerald-300 mb-0.5">Konfirmasi Akhir &amp; Penonaktifan Anggota</h4>
                        <p class="text-xs text-white/70 leading-relaxed">
                            Jika seluruh 4 berkas bukti di atas telah sesuai, klik tombol di bawah. Sistem akan secara otomatis:
                            <strong>(1) Mengubah status anggota menjadi Not Active</strong>, dan 
                            <strong>(2) Mencatat data lengkap ke arsip permanen Log Resign</strong>.
                        </p>
                    </div>
                </div>
                <div class="mt-3">
                    <input type="text" name="final_notes" placeholder="Catatan penonaktifan akhir (opsional)..."
                           class="w-full px-3 py-2 bg-black/30 border border-white/15 rounded-lg text-white text-xs focus:outline-none focus:border-emerald-400">
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-3 border-t border-white/10">
                <button type="button" onclick="toggleRevisionSection(true)" class="w-full sm:w-auto px-4 py-2.5 bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/40 rounded-xl text-xs font-bold flex items-center justify-center gap-2 transition-all">
                    <i class="fas fa-redo-alt"></i> Data Tidak Sesuai (Minta Isi Ulang)
                </button>

                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    <button type="button" onclick="closeModal('ieReviewModal')" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white/70 hover:text-white rounded-xl text-xs font-semibold transition-all">
                        Tutup
                    </button>
                    <button type="submit"
                            onclick="return confirm('Apakah seluruh bukti sudah sesuai? Akun staf akan dinyatakan NOT ACTIVE dan data akan diarsipkan permanen ke Log Resign.');"
                            class="px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-xl text-xs font-extrabold shadow-lg shadow-emerald-950/40 transition-all flex items-center gap-2">
                        <i class="fas fa-check-double"></i> Konfirmasi Akhir (Not Active)
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modal PND Reject --}}
<div id="pndRejectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm hidden p-4">
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

<script>
function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
}

function openPndRejectModal(id, name) {
    document.getElementById('rejectApplicantName').innerText = name;
    document.getElementById('pndRejectForm').action = '/portal/resignation/' + id + '/pnd-reject';
    document.getElementById('pndRejectModal').classList.remove('hidden');
}

function openIeReviewModal(id, name, citizenId, position, fineAmount, pocketUrl, keyUrl, letterUrl, fineUrl, revNotes) {
    document.getElementById('reviewApplicantName').innerText = name;
    document.getElementById('reviewCitizenId').innerText = citizenId;
    document.getElementById('reviewPosition').innerText = position;
    document.getElementById('reviewFineAmount').innerText = '$ ' + fineAmount;

    // Set preview images and links
    const placeholderImg = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="%23666" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>';
    
    document.getElementById('reviewImgPocket').src = pocketUrl || placeholderImg;
    document.getElementById('reviewLinkPocket').href = pocketUrl || '#';

    document.getElementById('reviewImgKey').src = keyUrl || placeholderImg;
    document.getElementById('reviewLinkKey').href = keyUrl || '#';

    document.getElementById('reviewImgLetter').src = letterUrl || placeholderImg;
    document.getElementById('reviewLinkLetter').href = letterUrl || '#';

    document.getElementById('reviewImgFine').src = fineUrl || placeholderImg;
    document.getElementById('reviewLinkFine').href = fineUrl || '#';

    // Set form actions
    document.getElementById('revisionForm').action = '/portal/resignation/' + id + '/ie-request-revision';
    document.getElementById('finalConfirmForm').action = '/portal/resignation/' + id + '/ie-final-confirm';

    // Reset revision section
    toggleRevisionSection(false);

    document.getElementById('ieReviewModal').classList.remove('hidden');
}

function toggleRevisionSection(show) {
    const sec = document.getElementById('revisionSection');
    if (show) {
        sec.classList.remove('hidden');
        sec.scrollIntoView({ behavior: 'smooth' });
    } else {
        sec.classList.add('hidden');
    }
}

function openProofPreviewModal(id, name, pUrl, kUrl, lUrl, fUrl) {
    openIeReviewModal(id, name, '-', '-', '0', pUrl, kUrl, lUrl, fUrl, '');
}
</script>
@endsection
