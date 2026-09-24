@extends('layouts.app')

@section('title', ($stage === 'pnd' ? 'Verifikasi PND' : 'Manajemen Resign IE') . ' — Portal Alta Hospital')

@section('content')
<div class="min-h-screen pt-20 pb-12" style="background: linear-gradient(135deg, #0b1329 0%, #0c2461 50%, #0b1329 100%);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

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
                <button type="button" onclick="openPtdhModal()"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-amber-600 via-orange-600 to-rose-600 hover:from-amber-500 hover:via-orange-500 hover:to-rose-500 text-white text-xs sm:text-sm font-extrabold rounded-xl shadow-lg shadow-orange-950/40 transition-all cursor-pointer">
                    <i class="fas fa-gavel"></i> + Hitung Denda PTDH
                </button>
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
            <div class="overflow-x-auto" style="scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.2) transparent;">
                <table class="w-full text-sm text-left border-collapse" style="min-width: 980px;">
                    <thead class="border-b border-white/10 text-xs text-white/50 uppercase tracking-wider bg-white/5">
                        <tr>
                            <th class="py-4 px-6 font-semibold" style="min-width: 220px;">Nama Staf &amp; ID</th>
                            <th class="py-4 px-5 font-semibold" style="min-width: 150px;">Jabatan Terakhir</th>
                            <th class="py-4 px-5 font-semibold" style="min-width: 120px;">Tanggal Surat</th>
                            <th class="py-4 px-5 font-semibold" style="min-width: 170px;">Status Alur</th>
                            @if($stage === 'ie')
                            <th class="py-4 px-5 font-semibold" style="min-width: 160px;">Denda Resign</th>
                            <th class="py-4 px-5 font-semibold text-center" style="min-width: 140px;">4 Berkas Bukti</th>
                            @endif
                            <th class="py-4 px-6 font-semibold text-right" style="min-width: 230px;">Aksi Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($requests as $req)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="py-4 px-6">
                                <div class="text-white font-semibold text-sm flex items-center gap-2 flex-wrap">
                                    <span>{{ $req->applicant_name }}</span>
                                    @if($req->isPtdh())
                                    <span class="px-2 py-0.5 rounded-full bg-rose-500/25 text-rose-300 border border-rose-500/40 text-[10px] font-extrabold uppercase tracking-wide inline-flex items-center gap-1 shadow-sm shadow-rose-950/30">
                                        <i class="fas fa-gavel text-[9px]"></i> PTDH
                                    </span>
                                    @endif
                                </div>
                                <div class="text-white/40 text-xs flex items-center gap-2 mt-0.5">
                                    <span>ID: {{ $req->user?->staff_id ?? '-' }}</span>
                                    <span>•</span>
                                    <span>Citizen: {{ $req->user?->citizen_id ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-5 text-white/80">
                                <div class="font-medium text-sm">{{ $req->position }}</div>
                                @if($req->batch)
                                <div class="text-white/40 text-xs mt-0.5">Batch: {{ $req->batch }}</div>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-white/70 whitespace-nowrap text-xs sm:text-sm">
                                {{ $req->letter_date?->format('d M Y') }}
                            </td>
                            <td class="py-4 px-5 whitespace-nowrap">
                                <span class="px-3 py-1 rounded-full text-xs font-bold border inline-flex items-center gap-1.5
                                    @if($req->status === 'pending_pnd') bg-yellow-500/20 text-yellow-300 border-yellow-500/30
                                    @elseif($req->status === 'pending_ie') bg-amber-500/20 text-amber-300 border-amber-500/40 shadow-sm shadow-amber-950/20
                                    @elseif($req->status === 'pending_proof') bg-purple-500/20 text-purple-300 border-purple-500/40 shadow-sm shadow-purple-950/20
                                    @elseif($req->status === 'proof_submitted') bg-sky-500/25 text-sky-200 border-sky-400/50 shadow-sm shadow-sky-950/30 animate-pulse
                                    @elseif($req->status === 'proof_revision') bg-rose-500/20 text-rose-300 border-rose-500/40 shadow-sm shadow-rose-950/20
                                    @elseif($req->status === 'completed') bg-emerald-500/20 text-emerald-300 border-emerald-500/40 shadow-sm shadow-emerald-950/20
                                    @elseif($req->status === 'cancelled') bg-gray-500/20 text-gray-300 border-gray-500/30
                                    @else bg-red-500/20 text-red-300 border-red-500/30 @endif">
                                    @if($req->status === 'proof_submitted')
                                    <span class="w-2 h-2 rounded-full bg-sky-400 animate-ping"></span>
                                    @endif
                                    {{ $req->status_label }}
                                </span>
                            </td>

                            {{-- Kolom IE: Denda & Bukti --}}
                            @if($stage === 'ie')
                            <td class="py-4 px-5 whitespace-nowrap">
                                <div class="text-amber-300 font-extrabold text-sm">$ {{ number_format($req->fine_amount, 0, ',', '.') }}</div>
                                @if($req->isPtdh())
                                <div class="text-rose-300 text-xs mt-0.5 font-semibold flex items-center gap-1">
                                    <i class="fas fa-plus-circle text-[10px] text-rose-400"></i> PTDH: ${{ number_format($req->ptdh_additional_fee, 0, ',', '.') }}
                                </div>
                                <div class="text-white/40 text-[11px] mt-0.5">(Denda Dasar: ${{ number_format($req->base_fine_amount, 0, ',', '.') }})</div>
                                @else
                                <div class="text-white/40 text-xs mt-0.5">{{ $req->fine_percentage }}% dari gapok (${{ number_format($req->base_salary, 0, ',', '.') }})</div>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-center whitespace-nowrap">
                                @if($req->hasAllProofs())
                                <button type="button"
                                        onclick="openProofPreviewModal({{ $req->id }}, '{{ addslashes($req->applicant_name) }}', '{{ $req->pocket_proof_url }}', '{{ $req->key_proof_url }}', '{{ $req->letter_proof_url }}', '{{ $req->fine_proof_url }}', {{ $req->isPtdh() ? 'true' : 'false' }}, '{{ number_format($req->ptdh_additional_fee, 0, ',', '.') }}', '{{ number_format($req->base_fine_amount, 0, ',', '.') }}')"
                                        class="px-3 py-1.5 bg-cyan-500/20 hover:bg-cyan-500/30 text-cyan-300 rounded-xl border border-cyan-500/40 text-xs font-bold inline-flex items-center gap-1.5 transition-all shadow-sm shadow-cyan-950/30">
                                    <i class="fas fa-images"></i> 4 Bukti Lengkap
                                </button>
                                @elseif($req->status === 'pending_proof')
                                <span class="text-purple-300/80 text-xs italic font-medium">Menunggu upload</span>
                                @elseif($req->status === 'proof_revision')
                                <span class="text-rose-300/80 text-xs italic font-medium">Diminta revisi</span>
                                @else
                                <span class="text-white/30 text-xs">—</span>
                                @endif
                            </td>
                            @endif

                            {{-- Kolom Aksi --}}
                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                {{-- Aksi PND --}}
                                @if($stage === 'pnd')
                                    @if($req->status === 'pending_pnd')
                                    <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                        {{-- Setujui --}}
                                        <form method="POST" action="{{ route('portal.resignation.pnd-approve', $req) }}">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 text-xs font-semibold rounded-xl border border-emerald-500/30 transition-all flex items-center gap-1">
                                                <i class="fas fa-check"></i> Setujui
                                            </button>
                                        </form>
                                        {{-- Tolak --}}
                                        <button type="button" onclick="openPndRejectModal({{ $req->id }}, '{{ addslashes($req->applicant_name) }}')"
                                                class="px-3 py-1.5 bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 text-xs font-semibold rounded-xl border border-rose-500/30 transition-all flex items-center gap-1">
                                                <i class="fas fa-times"></i> Tolak
                                        </button>
                                        {{-- Batalkan Resign (PND) --}}
                                        <form method="POST" action="{{ route('portal.resignation.pnd-cancel', $req) }}"
                                              onsubmit="return confirm('Yakin ingin membatalkan pengajuan resign {{ addslashes($req->applicant_name) }}? Pengajuan akan dihapus dan akun tetap aktif.');"
                                              class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 bg-gray-500/20 hover:bg-gray-500/30 text-gray-300 text-xs font-semibold rounded-xl border border-gray-500/30 transition-all" title="Batal Pengajuan">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    </div>
                                    @elseif(in_array($req->status, ['approved_pnd', 'pending_ie']))
                                    <div class="flex items-center justify-end gap-1.5">
                                        <form method="POST" action="{{ route('portal.resignation.pnd-cancel', $req) }}"
                                              onsubmit="return confirm('Yakin ingin membatalkan pengajuan resign {{ addslashes($req->applicant_name) }}?');"
                                              class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 text-xs font-semibold rounded-xl border border-rose-500/30 transition-all flex items-center gap-1">
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
                                    <div class="flex items-center justify-end gap-2">
                                        <form method="POST" action="{{ route('portal.resignation.ie-verify', $req) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    onclick="return confirm('Konfirmasi denda sebesar ${{ number_format($req->fine_amount, 0, ',', '.') }} untuk {{ addslashes($req->applicant_name) }}? Data akan dialihkan ke tahap Upload Bukti Resign dan akun staf TETAP AKTIF sampai konfirmasi akhir.')"
                                                    class="px-3.5 py-2 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-400 hover:to-orange-500 text-white text-xs font-bold rounded-xl shadow-md shadow-orange-950/40 transition-all flex items-center gap-1.5">
                                                <i class="fas fa-arrow-right"></i> Konfirmasi Denda &amp; Lanjut Bukti
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('portal.resignation.ie-cancel', $req) }}"
                                              onsubmit="return confirm('Batalkan resign untuk {{ addslashes($req->applicant_name) }}?');" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 bg-rose-500/20 hover:bg-rose-500/35 text-rose-300 hover:text-rose-200 text-xs font-semibold rounded-xl border border-rose-500/30 transition-all" title="Batalkan Resign">
                                                <i class="fas fa-ban text-sm"></i>
                                            </button>
                                        </form>
                                    </div>

                                    {{-- Tahap 2 IE: Cross-Check 4 Bukti Resign & Konfirmasi Akhir Penonaktifan --}}
                                    @elseif(in_array($req->status, ['proof_submitted', 'pending_proof', 'proof_revision']))
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Tombol Periksa Bukti (Membuka Modal Review & Keputusan) --}}
                                        <button type="button"
                                                onclick="openIeReviewModal({{ $req->id }}, '{{ addslashes($req->applicant_name) }}', '{{ $req->user?->citizen_id ?? '-' }}', '{{ addslashes($req->position) }}', '{{ number_format($req->fine_amount, 0, ',', '.') }}', '{{ $req->pocket_proof_url ?? '' }}', '{{ $req->key_proof_url ?? '' }}', '{{ $req->letter_proof_url ?? '' }}', '{{ $req->fine_proof_url ?? '' }}', '{{ addslashes($req->proof_revision_notes ?? '') }}', {{ $req->isPtdh() ? 'true' : 'false' }}, '{{ number_format($req->ptdh_additional_fee, 0, ',', '.') }}', '{{ number_format($req->base_fine_amount, 0, ',', '.') }}')"
                                                class="px-3.5 py-2 bg-gradient-to-r from-sky-600 to-blue-600 hover:from-sky-500 hover:to-blue-500 text-white text-xs font-bold rounded-xl shadow-md shadow-sky-950/40 transition-all flex items-center gap-1.5">
                                            <i class="fas fa-search-plus"></i> Cross-Check Bukti
                                        </button>

                                        <form method="POST" action="{{ route('portal.resignation.ie-cancel', $req) }}"
                                              onsubmit="return confirm('Batalkan pengajuan resign {{ addslashes($req->applicant_name) }}? Akun akan tetap aktif.');" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 bg-rose-500/20 hover:bg-rose-500/35 text-rose-300 hover:text-rose-200 text-xs font-semibold rounded-xl border border-rose-500/30 transition-all" title="Batalkan Resign">
                                                <i class="fas fa-ban text-sm"></i>
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
                        Cross-Check Bukti: <span id="reviewApplicantName" class="text-cyan-300 font-extrabold"></span>
                        <span id="reviewPtdhBadge" class="hidden px-2 py-0.5 rounded-full bg-rose-500/30 text-rose-300 border border-rose-500/50 text-[10px] font-extrabold uppercase tracking-wide">
                            <i class="fas fa-gavel"></i> PTDH
                        </span>
                    </h3>
                    <p class="text-xs text-white/50">
                        Citizen ID: <span id="reviewCitizenId" class="text-white font-medium"></span> • 
                        Jabatan: <span id="reviewPosition" class="text-white font-medium"></span> • 
                        Total Denda: <span id="reviewFineAmount" class="text-orange-400 font-bold"></span>
                        <span id="reviewFineDetails" class="text-rose-300/90 text-xs font-semibold ml-1"></span>
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

<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.35);
    border-radius: 9999px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(245, 158, 11, 0.45);
    border-radius: 9999px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(245, 158, 11, 0.75);
}
</style>

{{-- ═══════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL HITUNG & TERBITKAN DENDA PTDH OLEH IE --}}
{{-- ═══════════════════════════════════════════════════════════════════════════ --}}
<div id="ptdhModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/85 backdrop-blur-md hidden p-3 sm:p-4 overflow-y-auto">
    <div class="bg-slate-900 border border-amber-500/50 rounded-2xl w-full max-w-2xl lg:max-w-3xl p-5 sm:p-6 shadow-2xl my-auto relative max-h-[94vh] flex flex-col backdrop-blur-2xl">
        {{-- Header: Compact & Clean --}}
        <div class="flex items-center justify-between pb-3.5 border-b border-white/10 shrink-0">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-500/40 flex items-center justify-center text-amber-400 text-base shadow-inner">
                    <i class="fas fa-gavel"></i>
                </span>
                <div>
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        Hitung Denda PTDH
                        <span class="text-[10px] bg-rose-500/20 text-rose-300 px-2 py-0.5 rounded-full border border-rose-500/30 font-extrabold uppercase tracking-wider">Manual IE</span>
                    </h3>
                    <p class="text-xs text-white/50">Terbitkan kalkulasi denda PTDH langsung ke tahap upload bukti.</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('ptdhModal')" class="text-white/40 hover:text-white p-1.5 rounded-xl hover:bg-white/10 text-base transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="ptdhForm" action="{{ route('portal.resignation.ptdh-store') }}" method="POST" class="space-y-4 pt-3.5 overflow-y-auto pr-1 text-xs custom-scrollbar">
            @csrf

            {{-- Pemilihan Staf Medis (Searchable Dropdown: Aktif & Paused) --}}
            <div class="relative" id="ptdhSelectContainer">
                <label class="block text-xs font-bold text-amber-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                    <span class="flex items-center gap-1.5">
                        <i class="fas fa-user-md"></i> Pilih Anggota Medis (Aktif &amp; Paused) <span class="text-rose-400">*</span>
                    </span>
                    <span class="text-[10px] text-white/40 normal-case font-normal">Termasuk akun pemutihan / di-pause</span>
                </label>

                <input type="hidden" id="ptdh_user_id" name="user_id" required>

                {{-- Trigger Button --}}
                <button type="button" id="ptdhSelectTrigger" onclick="togglePtdhDropdown()"
                        class="w-full px-3.5 py-2.5 bg-black/60 border border-white/20 rounded-xl text-white text-xs sm:text-sm flex items-center justify-between text-left focus:outline-none focus:border-amber-400 hover:border-white/30 transition shadow-inner">
                    <span id="ptdh_select_text" class="text-white/60 truncate flex items-center gap-2">
                        <i class="fas fa-search text-white/30 text-xs"></i>
                        <span>-- Cari &amp; Pilih Anggota Medis (Nama / CID / ID) --</span>
                    </span>
                    <i class="fas fa-chevron-down text-white/40 text-xs ml-2 shrink-0 transition-transform" id="ptdh_select_arrow"></i>
                </button>

                {{-- Popover Dropdown Panel --}}
                <div id="ptdhDropdownPanel" class="hidden absolute left-0 right-0 top-full mt-1.5 bg-slate-900 border border-amber-500/40 rounded-2xl shadow-2xl z-50 p-2.5 space-y-2 backdrop-blur-2xl">
                    {{-- Search Field --}}
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-white/40 text-sm"></i>
                        <input type="text" id="ptdh_search_input" placeholder="Ketik nama, ID staf, citizen ID, jabatan..."
                               autocomplete="off"
                               class="w-full pl-9 pr-8 py-2.5 bg-black/60 border border-white/20 rounded-xl text-white text-xs sm:text-sm placeholder-white/40 focus:outline-none focus:border-amber-400 shadow-inner">
                        <button type="button" onclick="clearPtdhSearch()" id="ptdh_clear_search" class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-white/40 hover:text-white text-sm">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    {{-- List of Members --}}
                    <div id="ptdh_members_list" class="max-h-72 sm:max-h-80 overflow-y-auto space-y-1.5 pr-1.5 custom-scrollbar text-xs">
                        @if(isset($staffList))
                            @foreach($staffList as $staff)
                                @php
                                    $isPaused = !$staff->is_active;
                                    $roleTitle = $staff->medicRole?->display_name ?? $staff->role?->display_name ?? 'Staf';
                                    $searchString = strtolower($staff->name . ' ' . ($staff->staff_id ?? '') . ' ' . ($staff->citizen_id ?? '') . ' ' . ($staff->role?->display_name ?? '') . ' ' . ($staff->medicRole?->display_name ?? '') . ' ' . ($staff->batch ?? '') . ' ' . ($isPaused ? 'paused nonaktif pemutihan' : 'aktif'));
                                @endphp
                                <div class="ptdh-member-item p-2.5 sm:p-3 hover:bg-white/10 rounded-xl cursor-pointer flex items-center justify-between transition border border-transparent hover:border-amber-500/30"
                                     data-id="{{ $staff->id }}"
                                     data-search="{{ $searchString }}"
                                     onclick="selectPtdhMember({{ $staff->id }}, '{{ addslashes($staff->name) }}', '{{ addslashes($roleTitle) }}', '{{ $staff->staff_id ?? '-' }}', '{{ $staff->citizen_id ?? '-' }}', {{ $isPaused ? 'true' : 'false' }})">
                                    <div class="min-w-0 pr-2">
                                        <div class="font-bold text-white text-xs sm:text-sm truncate flex items-center gap-2">
                                            <span>{{ $staff->name }}</span>
                                            @if($isPaused)
                                                <span class="text-[9px] px-2 py-0.5 rounded-full font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40">
                                                    <i class="fas fa-pause text-[8px] mr-1"></i>Paused
                                                </span>
                                            @else
                                                <span class="text-[9px] px-2 py-0.5 rounded-full font-bold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">
                                                    Aktif
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-white/50 truncate mt-1">
                                            <span class="text-amber-200/90 font-medium">{{ $roleTitle }}</span>
                                            &bull; ID: <span class="font-mono text-white/70">{{ $staff->staff_id ?? '-' }}</span>
                                            &bull; CID: <span class="font-mono text-white/70">{{ $staff->citizen_id ?? '-' }}</span>
                                            @if($staff->batch)
                                                &bull; <span class="text-white/60">{{ $staff->batch }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <i class="fas fa-check text-amber-400 text-sm ptdh-check-icon shrink-0" style="display: none;"></i>
                                </div>
                            @endforeach
                        @endif
                        <div id="ptdh_no_results" class="hidden py-6 text-center text-white/40 text-xs">
                            <i class="fas fa-user-slash text-xl mb-1 block"></i>
                            Tidak ada anggota yang cocok dengan pencarian.
                        </div>
                    </div>
                </div>
            </div>

            {{-- Loading Spinner --}}
            <div id="ptdhLoading" class="hidden py-4 text-center text-amber-400">
                <i class="fas fa-circle-notch fa-spin text-xl mb-1"></i>
                <p class="text-[11px] text-white/60">Menghitung riwayat gaji pokok & skema denda...</p>
            </div>

            {{-- Container Hasil Kalkulasi Denda --}}
            <div id="ptdhCalculationBox" class="hidden space-y-2.5">
                {{-- Compact Member Info Bar --}}
                <div class="p-2.5 bg-white/5 border border-white/10 rounded-lg grid grid-cols-2 sm:grid-cols-4 gap-2 text-[11px]">
                    <div>
                        <span class="text-white/40 block text-[9px] uppercase font-semibold">Nama</span>
                        <span id="ptdh_disp_name" class="font-bold text-white truncate block"></span>
                    </div>
                    <div>
                        <span class="text-white/40 block text-[9px] uppercase font-semibold">Jabatan</span>
                        <span id="ptdh_disp_position" class="font-medium text-white/80 truncate block"></span>
                    </div>
                    <div>
                        <span class="text-white/40 block text-[9px] uppercase font-semibold">Citizen / Batch</span>
                        <span id="ptdh_disp_citizen" class="font-medium text-white/80 truncate block"></span>
                    </div>
                    <div>
                        <span class="text-white/40 block text-[9px] uppercase font-semibold">Persentase Denda</span>
                        <div class="flex items-center gap-1 mt-1">
                            <button type="button" id="btn_pct_30" onclick="setPtdhPercentage(30)"
                                    class="px-2 py-0.5 rounded border text-[10px] font-bold transition flex items-center gap-1 bg-amber-500/30 text-amber-300 border-amber-400">
                                <span>30%</span> <span class="text-[8px] opacity-75 font-normal">(Perawat/Co-Ass)</span>
                            </button>
                            <button type="button" id="btn_pct_25" onclick="setPtdhPercentage(25)"
                                    class="px-2 py-0.5 rounded border text-[10px] font-bold transition flex items-center gap-1 bg-white/5 text-white/60 border-white/10 hover:bg-white/10">
                                <span>25%</span> <span class="text-[8px] opacity-75 font-normal">(Dokter Umum)</span>
                            </button>
                        </div>
                        <input type="hidden" name="fine_percentage" id="ptdh_fine_percentage" value="30">
                    </div>
                </div>

                {{-- Unified Financial Breakdown --}}
                <div class="p-2.5 bg-black/40 border border-white/10 rounded-lg space-y-2">
                    <div class="grid grid-cols-2 gap-2 text-[11px]">
                        <div class="bg-white/5 p-2 rounded border border-white/5">
                            <span class="text-white/50 block text-[10px]">Total Gaji Pokok (Paid):</span>
                            <div class="text-sm font-bold text-white mt-0.5">$ <span id="ptdh_disp_salary">0</span></div>
                        </div>
                        <div class="bg-white/5 p-2 rounded border border-white/5">
                            <span class="text-white/50 block text-[10px]">Denda Dasar:</span>
                            <div class="text-sm font-bold text-amber-300 mt-0.5">$ <span id="ptdh_disp_base_fine">0</span></div>
                            <span class="text-[9px] text-white/40 block truncate" id="ptdh_disp_base_formula">Persentase dari gaji pokok</span>
                        </div>
                    </div>

                    {{-- Biaya Tambahan PTDH (Customizable inline) --}}
                    <div class="flex items-center justify-between gap-2 pt-1.5 border-t border-white/10">
                        <div class="shrink-0">
                            <label class="text-[11px] font-bold text-amber-300 flex items-center gap-1">
                                <i class="fas fa-plus-circle text-rose-400"></i> Biaya Tambahan PTDH:
                            </label>
                            <span class="text-[9px] text-white/40 block">Default Rp250.000 (Customizable)</span>
                        </div>
                        <div class="relative w-36 sm:w-44">
                            <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-amber-400 font-bold text-xs">$</span>
                            <input type="number" id="ptdh_additional_fee" name="ptdh_additional_fee" value="250000" min="0" step="1000" required
                                   oninput="recalculatePtdhTotal()"
                                   class="w-full pl-6 pr-2.5 py-1 bg-black/60 border border-amber-500/40 rounded-lg text-white font-bold text-xs text-right focus:outline-none focus:border-amber-400">
                        </div>
                    </div>
                </div>

                {{-- Total Highlight Banner --}}
                <div class="px-3 py-2 bg-gradient-to-r from-orange-600/30 via-rose-600/30 to-amber-600/30 border border-orange-500/50 rounded-lg flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-bold text-orange-200 uppercase tracking-wider block">Total Denda PTDH</span>
                        <div class="text-lg font-black text-white">$ <span id="ptdh_disp_total_fine">0</span></div>
                    </div>
                    <span class="px-2 py-0.5 bg-rose-500/20 text-rose-300 border border-rose-500/40 rounded text-[10px] font-extrabold uppercase flex items-center gap-1">
                        <i class="fas fa-gavel"></i> Wajib Bayar
                    </span>
                </div>

                {{-- Alasan IC & OOC (Compact 2 cols) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[10px] font-semibold text-white/70 mb-0.5">Alasan PTDH (IC)</label>
                        <textarea name="reason_ic" rows="2" placeholder="Pelanggaran SOP / desersi..."
                                  class="w-full px-2.5 py-1.5 bg-black/40 border border-white/15 rounded-lg text-white text-[11px] focus:outline-none focus:border-amber-400 resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-white/70 mb-0.5">Alasan PTDH (OOC)</label>
                        <textarea name="reason_ooc" rows="2" placeholder="Catatan indisipliner internal..."
                                  class="w-full px-2.5 py-1.5 bg-black/40 border border-white/15 rounded-lg text-white text-[11px] focus:outline-none focus:border-amber-400 resize-none"></textarea>
                    </div>
                </div>

                {{-- Catatan IE --}}
                <div>
                    <label class="block text-[10px] font-semibold text-white/70 mb-0.5">Catatan Tambahan IE (Opsional)</label>
                    <input type="text" name="ie_notes" placeholder="Catatan pembayaran atau instruksi pelunasan..."
                           class="w-full px-2.5 py-1.5 bg-black/40 border border-white/15 rounded-lg text-white text-[11px] focus:outline-none focus:border-amber-400">
                </div>

                {{-- Info Note --}}
                <div class="p-2 bg-purple-500/10 border border-purple-500/20 rounded-lg text-[10px] text-purple-200/80 flex items-center gap-1.5">
                    <i class="fas fa-info-circle text-purple-400 shrink-0"></i>
                    <span>Setelah diterbitkan, alur <strong>langsung beralih ke tahap Upload Bukti</strong> (Kantong, Kunci, SK, Billing).</span>
                </div>
            </div>

            {{-- Footer Action Buttons --}}
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-white/10 shrink-0">
                <button type="button" onclick="closeModal('ptdhModal')" class="px-3 py-1.5 bg-white/10 hover:bg-white/20 text-white/70 hover:text-white rounded-lg text-xs font-semibold transition-all">
                    Batal
                </button>
                <button type="submit" id="ptdhSubmitBtn" disabled
                        onclick="return confirm('Apakah perhitungan denda PTDH ini telah sesuai? Status anggota akan langsung dialihkan ke tahap Upload Bukti.');"
                        class="px-4 py-1.5 bg-gradient-to-r from-amber-600 to-rose-600 hover:from-amber-500 hover:to-rose-500 text-white rounded-lg text-xs font-extrabold shadow-md transition-all flex items-center gap-1.5 disabled:opacity-40 disabled:cursor-not-allowed">
                    <i class="fas fa-check-circle"></i> Terbitkan Denda PTDH
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let currentBaseFine = 0;
let currentBaseSalary = 0;
let currentPercentage = 30;

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    if (id === 'ptdhModal') {
        closePtdhDropdown();
    }
}

function openPtdhModal() {
    document.getElementById('ptdhModal').classList.remove('hidden');
}

function togglePtdhDropdown() {
    const panel = document.getElementById('ptdhDropdownPanel');
    const arrow = document.getElementById('ptdh_select_arrow');
    const isOpen = !panel.classList.contains('hidden');
    if (isOpen) {
        closePtdhDropdown();
    } else {
        panel.classList.remove('hidden');
        arrow.classList.add('rotate-180');
        const input = document.getElementById('ptdh_search_input');
        input.value = '';
        filterPtdhMembers('');
        setTimeout(() => input.focus(), 50);
    }
}

function closePtdhDropdown() {
    const panel = document.getElementById('ptdhDropdownPanel');
    const arrow = document.getElementById('ptdh_select_arrow');
    if (panel) panel.classList.add('hidden');
    if (arrow) arrow.classList.remove('rotate-180');
}

function clearPtdhSearch() {
    const input = document.getElementById('ptdh_search_input');
    input.value = '';
    filterPtdhMembers('');
    input.focus();
}

function filterPtdhMembers(query) {
    const q = query.toLowerCase().trim();
    const items = document.querySelectorAll('.ptdh-member-item');
    const clearBtn = document.getElementById('ptdh_clear_search');
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

    const noRes = document.getElementById('ptdh_no_results');
    if (noRes) noRes.classList.toggle('hidden', count > 0);
}

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('ptdh_search_input');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            filterPtdhMembers(this.value);
        });
    }

    // Close on click outside
    document.addEventListener('click', function(e) {
        const container = document.getElementById('ptdhSelectContainer');
        if (container && !container.contains(e.target)) {
            closePtdhDropdown();
        }
    });
});

function selectPtdhMember(id, name, role, staffId, citizenId, isPaused) {
    document.getElementById('ptdh_user_id').value = id;

    // Highlight selected item
    document.querySelectorAll('.ptdh-member-item').forEach(el => {
        const isMatch = el.getAttribute('data-id') == id;
        el.classList.toggle('bg-white/10', isMatch);
        const icon = el.querySelector('.ptdh-check-icon');
        if (icon) icon.style.display = isMatch ? 'inline-block' : 'none';
    });

    // Update trigger text
    const pausedBadge = isPaused ? '<span class="text-[9px] px-1.5 py-0.2 rounded font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40 ml-1">Paused</span>' : '';
    document.getElementById('ptdh_select_text').innerHTML = `
        <span class="font-bold text-white">${name}</span>
        ${pausedBadge}
        <span class="text-white/50 text-[11px]">(${role} &bull; ID: ${staffId} &bull; CID: ${citizenId})</span>
    `;

    closePtdhDropdown();
    fetchPtdhCalculation(id);
}

function setPtdhPercentage(pct) {
    currentPercentage = pct;
    const hiddenInput = document.getElementById('ptdh_fine_percentage');
    if (hiddenInput) hiddenInput.value = pct;

    const btn30 = document.getElementById('btn_pct_30');
    const btn25 = document.getElementById('btn_pct_25');

    if (btn30 && btn25) {
        if (pct === 25) {
            btn25.className = 'px-2 py-0.5 rounded border text-[10px] font-bold transition flex items-center gap-1 bg-amber-500/30 text-amber-300 border-amber-400 shadow-sm';
            btn30.className = 'px-2 py-0.5 rounded border text-[10px] font-semibold transition flex items-center gap-1 bg-white/5 text-white/60 border-white/10 hover:bg-white/10';
        } else {
            btn30.className = 'px-2 py-0.5 rounded border text-[10px] font-bold transition flex items-center gap-1 bg-amber-500/30 text-amber-300 border-amber-400 shadow-sm';
            btn25.className = 'px-2 py-0.5 rounded border text-[10px] font-semibold transition flex items-center gap-1 bg-white/5 text-white/60 border-white/10 hover:bg-white/10';
        }
    }

    if (currentBaseSalary > 0) {
        currentBaseFine = Math.round(currentBaseSalary * pct / 100);
        document.getElementById('ptdh_disp_base_fine').innerText = currentBaseFine.toLocaleString('id-ID');
        document.getElementById('ptdh_disp_base_formula').innerText = pct + '% dari gapok ($' + currentBaseSalary.toLocaleString('id-ID') + ')';
        recalculatePtdhTotal();
    }
}

function fetchPtdhCalculation(userId) {
    if (!userId) {
        document.getElementById('ptdhCalculationBox').classList.add('hidden');
        document.getElementById('ptdhSubmitBtn').disabled = true;
        return;
    }

    document.getElementById('ptdhLoading').classList.remove('hidden');
    document.getElementById('ptdhCalculationBox').classList.add('hidden');
    document.getElementById('ptdhSubmitBtn').disabled = true;

    fetch('{{ route("portal.resignation.ptdh-calculate") }}?user_id=' + encodeURIComponent(userId), {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('ptdhLoading').classList.add('hidden');
        if (!data.success) {
            alert(data.message || 'Gagal menghitung denda PTDH.');
            return;
        }

        currentBaseSalary = data.base_salary;
        currentBaseFine   = data.base_fine;

        document.getElementById('ptdh_disp_name').innerText = data.name;
        document.getElementById('ptdh_disp_position').innerText = data.position;
        document.getElementById('ptdh_disp_citizen').innerText = (data.citizen_id || '-') + (data.batch ? ' • ' + data.batch : '');
        document.getElementById('ptdh_disp_salary').innerText = data.base_salary_formatted;
        document.getElementById('ptdh_disp_base_fine').innerText = data.base_fine_formatted;
        document.getElementById('ptdh_disp_base_formula').innerText = (data.fine_percentage || 30) + '% dari gapok ($' + data.base_salary_formatted + ')';

        // Apply percentage button state
        setPtdhPercentage(data.fine_percentage || 30);

        // Set additional fee input
        document.getElementById('ptdh_additional_fee').value = data.default_additional_fee;
        recalculatePtdhTotal();

        document.getElementById('ptdhCalculationBox').classList.remove('hidden');
        document.getElementById('ptdhSubmitBtn').disabled = false;
    })
    .catch(err => {
        document.getElementById('ptdhLoading').classList.add('hidden');
        console.error('Error fetching PTDH calculation:', err);
        alert('Terjadi kesalahan saat mengambil kalkulasi denda.');
    });
}

function recalculatePtdhTotal() {
    const feeInput = document.getElementById('ptdh_additional_fee');
    const fee = parseInt(feeInput.value) || 0;
    const total = currentBaseFine + fee;
    document.getElementById('ptdh_disp_total_fine').innerText = total.toLocaleString('id-ID');
}

function openPndRejectModal(id, name) {
    document.getElementById('rejectApplicantName').innerText = name;
    document.getElementById('pndRejectForm').action = '/portal/resignation/' + id + '/pnd-reject';
    document.getElementById('pndRejectModal').classList.remove('hidden');
}

function openIeReviewModal(id, name, citizenId, position, fineAmount, pocketUrl, keyUrl, letterUrl, fineUrl, revNotes, isPtdh = false, ptdhFee = '0', baseFine = '0') {
    document.getElementById('reviewApplicantName').innerText = name;
    document.getElementById('reviewCitizenId').innerText = citizenId;
    document.getElementById('reviewPosition').innerText = position;

    const ptdhBadge = document.getElementById('reviewPtdhBadge');
    const fineSubtitle = document.getElementById('reviewFineDetails');

    if (isPtdh) {
        if (ptdhBadge) ptdhBadge.classList.remove('hidden');
        document.getElementById('reviewFineAmount').innerText = '$ ' + fineAmount;
        if (fineSubtitle) fineSubtitle.innerText = ' (Denda Dasar: $' + baseFine + ' + Biaya PTDH: $' + ptdhFee + ')';
    } else {
        if (ptdhBadge) ptdhBadge.classList.add('hidden');
        document.getElementById('reviewFineAmount').innerText = '$ ' + fineAmount;
        if (fineSubtitle) fineSubtitle.innerText = '';
    }

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

function openProofPreviewModal(id, name, pUrl, kUrl, lUrl, fUrl, isPtdh = false, ptdhFee = '0', baseFine = '0') {
    openIeReviewModal(id, name, '-', '-', '0', pUrl, kUrl, lUrl, fUrl, '', isPtdh, ptdhFee, baseFine);
}
</script>
@endsection
