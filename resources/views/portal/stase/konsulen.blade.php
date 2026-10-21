@extends('layouts.app')
@section('title', 'Panel Konsulen — Evaluasi & Laporan Stase')
@section('content')
<div class="min-h-screen pt-20 pb-10" style="background: linear-gradient(180deg, #0c4a6e 0%, #075985 50%, #0369a1 100%);">
<div class="max-w-6xl mx-auto px-4">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <a href="{{ route('portal.stase.index') }}" class="text-white/50 hover:text-white text-sm flex items-center gap-1.5 mb-2 transition-colors">
                <i class="fas fa-arrow-left text-xs"></i> Kembali ke Daftar Stase Saya
            </a>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-user-check text-blue-400"></i> Panel Dokter Konsulen — Evaluasi Stase
            </h1>
            <p class="text-white/60 text-sm mt-0.5">Evaluasi kinerja bimbingan dokter muda/residen, input laporan hasil stase, dan kirimkan ke Divisi MSL untuk penerbitan sertifikat resmi.</p>
        </div>
        <div class="px-4 py-2 bg-blue-500/20 border border-blue-500/30 rounded-xl text-blue-200 text-xs flex items-center gap-2">
            <i class="fas fa-id-badge text-blue-400 text-sm"></i>
            <span>Konsulen Aktif: <strong class="text-white">{{ auth()->user()->name }}</strong></span>
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

    @if($errors->any())
    <div class="mb-4 p-4 bg-red-500/20 border border-red-500/40 rounded-xl text-red-300 text-sm">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden shadow-2xl">
        @if($applications->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-white/40">
            <i class="fas fa-inbox text-4xl mb-3"></i>
            <p class="text-sm">Tidak ada pengajuan stase bimbingan untuk Anda saat ini.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-white/10 text-xs text-white/50 uppercase tracking-wider">
                    <tr>
                        <th class="text-left px-5 py-3.5">Anggota Bimbingan</th>
                        <th class="text-left px-5 py-3.5">Bidang Stase</th>
                        <th class="text-left px-5 py-3.5">Catatan Pemohon</th>
                        <th class="text-left px-5 py-3.5">Status</th>
                        <th class="text-left px-5 py-3.5">Laporan / Hasil</th>
                        <th class="text-center px-5 py-3.5">Tindakan Konsulen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($applications as $app)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-5 py-3.5">
                            <div class="text-white font-medium">{{ $app->user?->name }}</div>
                            <div class="text-white/40 text-xs">
                                <span>{{ $app->user?->staff_id ?? '-' }}</span>
                                @if(!empty($app->user?->role?->display_name))
                                    <span class="text-white/20">&bull;</span>
                                    <span>{{ $app->user->role->display_name }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="text-blue-300 font-semibold">{{ $app->stase_name }}</span>
                            <div class="text-white/40 text-[11px]">Diajukan: {{ $app->created_at->format('d M Y') }}</div>
                        </td>
                        <td class="px-5 py-3.5 text-white/70 text-xs max-w-[200px]">
                            {{ $app->notes ?: '—' }}
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                @if($app->status === 'pending_konsulen') bg-yellow-500/20 text-yellow-300 border border-yellow-500/30
                                @elseif($app->status === 'approved_konsulen' || $app->status === 'pending_msl') bg-sky-500/20 text-sky-300 border border-sky-500/30
                                @elseif($app->status === 'completed') bg-emerald-500/20 text-emerald-300 border border-emerald-500/30
                                @elseif($app->status === 'rejected') bg-red-500/20 text-red-300 border border-red-500/30
                                @else bg-blue-500/20 text-blue-300 border border-blue-500/30 @endif">
                                {{ $app->status_label }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            @if($app->konsulen_notes)
                                <div class="text-xs text-white/90 line-clamp-2 max-w-[220px]" title="{{ $app->konsulen_notes }}">
                                    <i class="fas fa-quote-left text-sky-400 text-[10px] mr-1"></i> {{ $app->konsulen_notes }}
                                </div>
                                <div class="text-[11px] text-sky-300 mt-0.5 font-medium">Grade: {{ $app->grade ?: 'Lulus' }}</div>
                            @else
                                <span class="text-white/30 text-xs italic">Belum diinput</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            @if($app->status === 'pending_konsulen')
                            <div class="flex items-center justify-center gap-2">
                                {{-- Tombol Buka Form Input Laporan Stase --}}
                                <button type="button" onclick="openReportModal('{{ $app->id }}', '{{ addslashes($app->user?->name) }}', '{{ addslashes($app->stase_name) }}')"
                                        class="px-3 py-1.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white text-xs font-semibold rounded-lg shadow-md transition-all flex items-center gap-1.5">
                                    <i class="fas fa-edit"></i> Input Laporan &amp; Setujui
                                </button>

                                {{-- Tombol Tolak --}}
                                <button type="button" onclick="openRejectModal('{{ $app->id }}', '{{ addslashes($app->user?->name) }}', '{{ addslashes($app->stase_name) }}')"
                                        class="px-2.5 py-1.5 bg-red-500/20 hover:bg-red-500/30 text-red-300 text-xs font-semibold rounded-lg border border-red-500/30 transition-all">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            @elseif($app->status === 'approved_konsulen' || $app->status === 'pending_msl')
                                <div class="text-center text-xs text-sky-300 font-medium flex items-center justify-center gap-1">
                                    <i class="fas fa-hourglass-half"></i> Menunggu Verifikasi MSL
                                </div>
                            @elseif($app->status === 'completed')
                                <div class="text-center text-xs text-emerald-300 font-semibold flex items-center justify-center gap-1">
                                    <i class="fas fa-check-double"></i> Selesai &amp; Sertifikat Terbit
                                </div>
                            @else
                                <div class="text-center text-xs text-white/40 italic">Ditolak</div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-white/10">{{ $applications->links() }}</div>
        @endif
    </div>
</div>
</div>

{{-- MODAL INPUT LAPORAN STASE OLEH KONSULEN --}}
<div id="reportModal" class="fixed inset-0 z-50 bg-black/75 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-slate-900 border border-white/20 rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl animate-fade-in">
        <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between">
            <h3 class="text-white font-bold flex items-center gap-2">
                <i class="fas fa-file-medical-alt text-teal-400"></i> Input Laporan Hasil Bimbingan Stase
            </h3>
            <button type="button" onclick="closeReportModal()" class="text-white/50 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="reportForm" method="POST" action="" class="p-6 space-y-4">
            @csrf
            <div class="p-3 bg-white/5 border border-white/10 rounded-xl text-xs space-y-1">
                <div>Anggota Medis: <strong id="modalCandidateName" class="text-white"></strong></div>
                <div>Bidang Stase: <strong id="modalStaseName" class="text-blue-300"></strong></div>
            </div>

            <div>
                <label class="block text-xs text-white/70 font-medium mb-1">
                    Hasil Laporan Evaluasi / Bimbingan Klinis *
                </label>
                <textarea name="konsulen_notes" rows="4" required maxlength="1000"
                          placeholder="Tuliskan evaluasi bimbingan, pencapaian kompetensi tindakan medis, rekam jejak bimbingan, dan rekomendasi kelulusan..."
                          class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm placeholder-white/30 focus:outline-none focus:border-teal-400 resize-none"></textarea>
                <p class="text-[11px] text-white/40 mt-1">Laporan ini akan diteruskan ke Divisi MSL sebagai dasar penerbitan sertifikat kelulusan stase.</p>
            </div>

            <div>
                <label class="block text-xs text-white/70 font-medium mb-1">
                    Nilai / Grade Kelulusan (Opsional)
                </label>
                <select name="grade" class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-teal-400">
                    <option value="A" class="bg-slate-900 text-white">Grade A (Sangat Memuaskan / Cum Laude)</option>
                    <option value="A-" class="bg-slate-900 text-white">Grade A- (Sangat Baik)</option>
                    <option value="B+" class="bg-slate-900 text-white">Grade B+ (Baik Sekali)</option>
                    <option value="B" class="bg-slate-900 text-white">Grade B (Memuaskan / Lulus)</option>
                    <option value="Lulus" class="bg-slate-900 text-white">Lulus Memenuhi Standar</option>
                </select>
            </div>

            <div class="pt-2 flex justify-end gap-2">
                <button type="button" onclick="closeReportModal()" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-xs font-medium rounded-xl">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white text-xs font-semibold rounded-xl shadow-lg flex items-center gap-1.5">
                    <i class="fas fa-paper-plane"></i> Simpan &amp; Teruskan ke MSL
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL TOLAK STASE OLEH KONSULEN --}}
<div id="rejectModal" class="fixed inset-0 z-50 bg-black/75 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-slate-900 border border-white/20 rounded-2xl w-full max-w-md overflow-hidden shadow-2xl">
        <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between">
            <h3 class="text-white font-bold flex items-center gap-2">
                <i class="fas fa-times-circle text-rose-400"></i> Tolak Pengajuan Stase
            </h3>
            <button type="button" onclick="closeRejectModal()" class="text-white/50 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="rejectForm" method="POST" action="" class="p-6 space-y-4">
            @csrf
            <div class="text-xs text-white/70">
                Tolak permohonan stase untuk <strong id="modalRejectCandidate" class="text-white"></strong>?
            </div>
            <div>
                <label class="block text-xs text-white/70 font-medium mb-1">Alasan Penolakan *</label>
                <textarea name="konsulen_notes" rows="3" required maxlength="500" placeholder="Jelaskan alasan penolakan bimbingan stase ini..."
                          class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm placeholder-white/30 focus:outline-none focus:border-rose-400 resize-none"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-white/10 text-white text-xs rounded-xl">Batal</button>
                <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-500 text-white text-xs font-semibold rounded-xl">
                    Tolak Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openReportModal(appId, name, stase) {
    document.getElementById('reportForm').action = "/portal/stase/" + appId + "/konsulen-approve";
    document.getElementById('modalCandidateName').textContent = name;
    document.getElementById('modalStaseName').textContent = stase;
    const modal = document.getElementById('reportModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function closeReportModal() {
    const modal = document.getElementById('reportModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
function openRejectModal(appId, name, stase) {
    document.getElementById('rejectForm').action = "/portal/stase/" + appId + "/konsulen-reject";
    document.getElementById('modalRejectCandidate').textContent = name + " (" + stase + ")";
    const modal = document.getElementById('rejectModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
@endsection
