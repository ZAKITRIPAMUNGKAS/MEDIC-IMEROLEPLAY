@extends('layouts.app')
@section('title', 'Review Pengajuan Kenaikan Jabatan — Divisi PND')
@section('content')
<div class="min-h-screen pt-20 pb-10" style="background: linear-gradient(180deg, #0c4a6e 0%, #075985 50%, #0369a1 100%);">
<div class="max-w-7xl mx-auto px-4">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2"><i class="fas fa-clipboard-list text-violet-400"></i> PND — Review Pengajuan Kenaikan Jabatan</h1>
            <p class="text-white/50 text-sm mt-0.5">Tinjau dan putuskan pengajuan kenaikan jabatan anggota</p>
        </div>
        <a href="{{ route('portal.promotion.period.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-violet-500/20 hover:bg-violet-500/30 text-violet-300 text-sm font-medium rounded-xl border border-violet-500/30 transition-all">
            <i class="fas fa-toggle-on"></i> Kelola Periode
        </a>
    </div>
    @if(session('success'))
    <div class="mb-4 p-4 bg-emerald-500/20 border border-emerald-500/40 rounded-xl text-emerald-300 text-sm flex items-center gap-2"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif

    <form method="GET" class="mb-4 flex gap-2 flex-wrap">
        <select name="period_id" class="px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none">
            <option value="">Semua Periode</option>
            @foreach($periods as $p)<option value="{{ $p->id }}" {{ request('period_id') == $p->id ? 'selected' : '' }}>{{ $p->name }} {{ $p->batch ? '('.$p->batch.')' : '' }}</option>@endforeach
        </select>
        <select name="status" class="px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none">
            <option value="">Semua Status</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu ACC 1 (Penerimaan)</option>
            <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>ACC 1 Selesai: Diterima (Menunggu ACC 2)</option>
            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>ACC 2 Selesai: Jabatan Dinaikkan</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-violet-500/20 hover:bg-violet-500/30 text-violet-300 rounded-xl border border-violet-500/30 text-sm">Filter</button>
    </form>

    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden shadow-2xl">
        @if($applications->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-white/40"><i class="fas fa-inbox text-4xl mb-3"></i><p class="text-sm">Tidak ada pengajuan kenaikan jabatan.</p></div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-white/10 text-xs text-white/50 uppercase tracking-wider">
                    <tr><th class="text-left px-5 py-3">Anggota</th><th class="text-left px-5 py-3">Dari → Ke</th><th class="text-left px-5 py-3">Periode</th><th class="text-left px-5 py-3">Credit Score</th><th class="text-left px-5 py-3">Status</th><th class="px-5 py-3 text-center">Aksi PND (2 Tahap ACC)</th></tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($applications as $app)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-5 py-3.5"><div class="text-white font-medium">{{ $app->user?->name }}</div><div class="text-white/40 text-xs">{{ $app->user?->staff_id }}</div></td>
                        <td class="px-5 py-3.5">
                            <span class="text-white/60">{{ $app->currentRole?->display_name ?? '—' }}</span>
                            <i class="fas fa-arrow-right text-violet-400 mx-1 text-xs"></i>
                            <span class="text-violet-300 font-semibold">{{ $app->targetRole?->display_name ?? '—' }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-white/60 text-xs">{{ $app->period?->name }}</td>
                        <td class="px-5 py-3.5 text-white/80 font-semibold">{{ $app->credit_score_at_submission }} poin</td>
                        <td class="px-5 py-3.5">
                            @if($app->status === 'approved')
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 inline-flex items-center gap-1.5">
                                    <i class="fas fa-check-double text-emerald-400"></i> Jabatan Dinaikkan
                                </span>
                            @elseif($app->status === 'accepted')
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-sky-500/20 text-sky-300 border border-sky-500/30 inline-flex items-center gap-1.5">
                                    <i class="fas fa-check text-sky-400"></i> ACC 1: Diterima
                                </span>
                                <div class="text-[11px] text-sky-200/70 mt-1">Menunggu ACC 2</div>
                            @elseif($app->status === 'rejected')
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-red-500/20 text-red-300 border border-red-500/30 inline-flex items-center gap-1.5">
                                    <i class="fas fa-times text-red-400"></i> Ditolak
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/30 inline-flex items-center gap-1.5">
                                    <i class="fas fa-clock text-amber-400"></i> Menunggu ACC 1
                                </span>
                            @endif

                            {{-- Berkas Upload Link --}}
                            <div class="mt-2 flex flex-wrap gap-1">
                                @if($app->case_study_file)
                                    @php
                                        $targetRoleText = strtolower(($app->targetRole?->name ?? '') . ' ' . ($app->targetRole?->display_name ?? ''));
                                        $isDokspelRole = str_contains($targetRoleText, 'spesialis');
                                    @endphp
                                    @if($isDokspelRole)
                                        <a href="{{ asset('storage/' . $app->case_study_file) }}" target="_blank"
                                           class="inline-flex items-center gap-1 px-2 py-0.5 bg-violet-500/20 hover:bg-violet-500/30 text-violet-300 border border-violet-500/30 rounded text-[11px] font-medium transition-all" title="Buka Dokumen Studi Kasus Dokspel">
                                            <i class="fas fa-file-pdf text-rose-400 text-[10px]"></i> Studi Kasus (Dokspel)
                                        </a>
                                    @else
                                        <a href="{{ asset('storage/' . $app->case_study_file) }}" target="_blank"
                                           class="inline-flex items-center gap-1 px-2 py-0.5 bg-sky-500/20 hover:bg-sky-500/30 text-sky-300 border border-sky-500/30 rounded text-[11px] font-medium transition-all" title="Buka Berkas Laporan Kenaikan Jabatan">
                                            <i class="fas fa-file-alt text-sky-400 text-[10px]"></i> Berkas Laporan
                                        </a>
                                    @endif
                                @endif
                                @if($app->recommendation_letter_1)
                                    <a href="{{ asset('storage/' . $app->recommendation_letter_1) }}" target="_blank"
                                       class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 border border-blue-500/30 rounded text-[11px] font-medium transition-all" title="Buka Rekomendasi 1">
                                        <i class="fas fa-file-alt text-sky-400 text-[10px]"></i> Rekom 1
                                    </a>
                                @endif
                                @if($app->recommendation_letter_2)
                                    <a href="{{ asset('storage/' . $app->recommendation_letter_2) }}" target="_blank"
                                       class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 border border-blue-500/30 rounded text-[11px] font-medium transition-all" title="Buka Rekomendasi 2">
                                        <i class="fas fa-file-alt text-sky-400 text-[10px]"></i> Rekom 2
                                    </a>
                                @endif
                                @if($app->supporting_document)
                                    <a href="{{ asset('storage/' . $app->supporting_document) }}" target="_blank"
                                       class="inline-flex items-center gap-1 px-2 py-0.5 bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/30 rounded text-[11px] font-medium transition-all" title="Buka Berkas Pendukung">
                                        <i class="fas fa-folder-open text-amber-400 text-[10px]"></i> Berkas Pendukung
                                    </a>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            @if($app->status === 'pending')
                            {{-- TAHAP 1: Belum Di-ACC, tampilkan tombol ACC 1 (Terima Pengajuan) --}}
                            <div class="flex flex-col gap-2">
                                {{-- Checklist preview --}}
                                @if($app->requirements_checklist)
                                <details class="text-xs text-white/50 cursor-pointer mb-1">
                                    <summary class="text-violet-300 hover:text-violet-200">Lihat checklist ({{ collect($app->requirements_checklist)->where('met',true)->count() }}/{{ count($app->requirements_checklist) }} terpenuhi)</summary>
                                    <div class="mt-1 space-y-1 pl-2">
                                        @foreach($app->requirements_checklist as $item)
                                        <div class="{{ $item['met'] ? 'text-emerald-300' : 'text-red-300' }}">
                                            {{ $item['met'] ? '✅' : '❌' }} {{ $item['label'] }}
                                        </div>
                                        @endforeach
                                    </div>
                                </details>
                                @endif
                                <div class="flex flex-wrap gap-1.5 items-center">
                                    <form method="POST" action="{{ route('portal.promotion.applications.review', $app) }}" class="flex gap-1 items-center">
                                        @csrf
                                        <input type="hidden" name="action" value="accept">
                                        <input type="text" name="pnd_notes" placeholder="Catatan ACC 1 (opsional)" class="px-2 py-1 bg-white/10 border border-white/20 rounded-lg text-white text-xs focus:outline-none w-36">
                                        <button type="submit" onclick="return confirm('ACC TAHAP 1: Terima pengajuan kenaikan jabatan {{ addslashes($app->user?->name) }}? Pengajuan akan berstatus Diterima dan lanjut ke tahap ACC Kenaikan Jabatan.')"
                                                class="px-2.5 py-1.5 bg-sky-500/20 hover:bg-sky-500/30 text-sky-300 text-xs font-semibold rounded-lg border border-sky-500/30 transition-all shadow-sm flex items-center gap-1" title="ACC Tahap 1: Penerimaan Berkas/Pengajuan">
                                            <i class="fas fa-clipboard-check"></i> ACC 1 (Terima)
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('portal.promotion.applications.review', $app) }}">
                                        @csrf
                                        <input type="hidden" name="action" value="reject">
                                        <button type="button"
                                                onclick="var n=prompt('Alasan penolakan pengajuan:'); if(n!==null){this.form.querySelector('[name=pnd_notes]').value=n;this.form.submit()}"
                                                class="px-2 py-1.5 bg-red-500/20 hover:bg-red-500/30 text-red-300 text-xs font-semibold rounded-lg border border-red-500/30 transition-all" title="Tolak Pengajuan">
                                            <i class="fas fa-times"></i> Tolak
                                        </button>
                                        <input type="hidden" name="pnd_notes" value="">
                                    </form>
                                </div>
                            </div>
                            @elseif($app->status === 'accepted')
                            {{-- TAHAP 2: Sudah Di-ACC 1 (Diterima), tampilkan tombol ACC 2 (Kenaikan Jabatan) --}}
                            <div class="flex flex-col gap-2">
                                <div class="p-2 bg-sky-500/10 border border-sky-500/20 rounded-lg text-xs">
                                    <div class="text-sky-300 font-semibold flex items-center gap-1">
                                        <i class="fas fa-check-circle text-sky-400"></i> ACC 1 Selesai
                                    </div>
                                    <div class="text-white/60 text-[11px] mt-0.5">
                                        Oleh: <strong>{{ $app->firstApprover?->name ?? $app->approvedByPnd?->name ?? 'PND' }}</strong>
                                        @if($app->first_approved_at || $app->pnd_reviewed_at)
                                            ({{ ($app->first_approved_at ?? $app->pnd_reviewed_at)->format('d M Y H:i') }})
                                        @endif
                                    </div>
                                    @if($app->first_approval_notes || $app->pnd_notes)
                                        <div class="text-sky-200/80 text-[11px] mt-1 italic">
                                            "{{ $app->first_approval_notes ?: $app->pnd_notes }}"
                                        </div>
                                    @endif
                                </div>

                                <div class="flex flex-wrap gap-1.5 items-center">
                                    <form method="POST" action="{{ route('portal.promotion.applications.review', $app) }}" class="flex gap-1 items-center">
                                        @csrf
                                        <input type="hidden" name="action" value="promote">
                                        <input type="text" name="pnd_notes" placeholder="Catatan ACC 2 / SK (opsional)" class="px-2 py-1 bg-white/10 border border-white/20 rounded-lg text-white text-xs focus:outline-none w-36">
                                        <button type="submit" onclick="return confirm('ACC TAHAP 2 (FINAL): Sahkan kenaikan jabatan {{ addslashes($app->user?->name) }} ke {{ addslashes($app->targetRole?->display_name) }}? Jabatan staf akan otomatis langsung dinaikkan!')"
                                                class="px-2.5 py-1.5 bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 text-xs font-semibold rounded-lg border border-emerald-500/30 transition-all shadow-sm flex items-center gap-1" title="ACC Tahap 2: Pengesahan Kenaikan Jabatan">
                                            <i class="fas fa-level-up-alt"></i> ACC 2 (Naik Jabatan)
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('portal.promotion.applications.review', $app) }}">
                                        @csrf
                                        <input type="hidden" name="action" value="reject">
                                        <button type="button"
                                                onclick="var n=prompt('Alasan penolakan / pembatalan kenaikan jabatan:'); if(n!==null){this.form.querySelector('[name=pnd_notes]').value=n;this.form.submit()}"
                                                class="px-2 py-1.5 bg-red-500/20 hover:bg-red-500/30 text-red-300 text-xs font-semibold rounded-lg border border-red-500/30 transition-all" title="Tolak / Batalkan">
                                            <i class="fas fa-times"></i> Tolak
                                        </button>
                                        <input type="hidden" name="pnd_notes" value="">
                                    </form>
                                </div>
                            </div>
                            @elseif($app->status === 'approved')
                            {{-- SUDAH DI-ACC FINAL --}}
                            <div class="text-xs space-y-1">
                                <div class="text-emerald-300 font-semibold flex items-center gap-1">
                                    <i class="fas fa-check-double text-emerald-400"></i> Kenaikan Resmi Disahkan
                                </div>
                                <div class="text-white/50 text-[11px]">
                                    ACC 1: {{ $app->firstApprover?->name ?? 'PND' }}
                                    @if($app->first_approved_at) ({{ $app->first_approved_at->format('d/m/y') }}) @endif
                                </div>
                                <div class="text-white/50 text-[11px]">
                                    ACC 2: {{ $app->finalApprover?->name ?? $app->approvedByPnd?->name ?? 'PND' }}
                                    @if($app->final_approved_at ?? $app->pnd_reviewed_at) ({{ ($app->final_approved_at ?? $app->pnd_reviewed_at)->format('d/m/y') }}) @endif
                                </div>
                                @if($app->final_approval_notes || $app->pnd_notes)
                                    <div class="text-emerald-200/80 text-[11px] italic">
                                        "{{ Str::limit($app->final_approval_notes ?: $app->pnd_notes, 40) }}"
                                    </div>
                                @endif
                            </div>
                            @else
                            {{-- REJECTED --}}
                            <div class="text-xs">
                                <div class="text-red-300 font-semibold flex items-center gap-1 mb-1">
                                    <i class="fas fa-times-circle text-red-400"></i> Ditolak PND
                                </div>
                                @if($app->pnd_notes)
                                    <div class="text-white/50 text-[11px] italic">
                                        "{{ Str::limit($app->pnd_notes, 45) }}"
                                    </div>
                                @else
                                    <span class="text-white/30 text-xs">—</span>
                                @endif
                            </div>
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
@endsection
