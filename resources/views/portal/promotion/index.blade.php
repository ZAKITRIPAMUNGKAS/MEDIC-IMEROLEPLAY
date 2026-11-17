@extends('layouts.app')
@section('title', 'Kenaikan Jabatan — Portal Alta Hospital')
@section('content')
<div class="min-h-screen pt-20 pb-10" style="background: linear-gradient(180deg, #0c4a6e 0%, #075985 50%, #0369a1 100%);">
<div class="max-w-4xl mx-auto px-4">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2"><i class="fas fa-level-up-alt text-violet-400"></i> Kenaikan Jabatan</h1>
            <p class="text-white/50 text-sm mt-0.5">Status pengajuan promosi Anda</p>
        </div>
        @if($period && (!$latestApp || !in_array($latestApp->status, ['pending', 'accepted'])))
        <a href="{{ route('portal.promotion.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-violet-500 to-purple-600 hover:from-violet-400 hover:to-purple-500 text-white text-sm font-semibold rounded-xl shadow-lg transition-all">
            <i class="fas fa-plus"></i> Ajukan Kenaikan
        </a>
        @endif
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-emerald-500/20 border border-emerald-500/40 rounded-xl text-emerald-300 text-sm flex items-center gap-2"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="mb-4 p-4 bg-red-500/20 border border-red-500/40 rounded-xl text-red-300 text-sm flex items-center gap-2"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif
    @if(session('info'))
    <div class="mb-4 p-4 bg-sky-500/20 border border-sky-500/40 rounded-xl text-sky-300 text-sm flex items-center gap-2"><i class="fas fa-info-circle"></i> {{ session('info') }}</div>
    @endif

    {{-- Status Periode --}}
    <div class="mb-6 p-5 rounded-2xl border {{ $period ? 'bg-emerald-500/10 border-emerald-500/30' : 'bg-white/5 border-white/10' }}">
        @if($period)
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/20 flex items-center justify-center"><i class="fas fa-door-open text-emerald-400"></i></div>
            <div>
                <p class="text-emerald-300 font-semibold">Periode Kenaikan Jabatan Sedang Dibuka</p>
                <p class="text-white/60 text-sm">{{ $period->name }} @if($period->batch)({{ $period->batch }})@endif</p>
            </div>
        </div>
        @else
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center"><i class="fas fa-door-closed text-white/50"></i></div>
            <div>
                <p class="text-white/70 font-semibold">Periode Kenaikan Jabatan Sedang Ditutup</p>
                <p class="text-white/40 text-sm">Pantau pengumuman dari divisi PND untuk periode selanjutnya.</p>
            </div>
        </div>
        @endif
    </div>

    {{-- Jabatan Saat Ini --}}
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 mb-6">
        <h3 class="text-white/60 text-xs uppercase tracking-wider font-semibold mb-3">Jabatan Saat Ini</h3>
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-violet-500/20 to-purple-500/20 border border-violet-500/30 flex items-center justify-center">
                <i class="fas fa-user-md text-violet-400 text-xl"></i>
            </div>
            <div>
                <p class="text-white text-lg font-bold">{{ $user->role?->display_name ?? 'Belum ada jabatan' }}</p>
                <p class="text-white/50 text-sm">{{ $user->staff_id }} — Level {{ $user->role?->level ?? '—' }}</p>
            </div>
        </div>
    </div>

    {{-- Pengajuan Terakhir --}}
    @if($latestApp)
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6">
        <h3 class="text-white/60 text-xs uppercase tracking-wider font-semibold mb-4">Pengajuan Terakhir</h3>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-5">
            <div>
                <p class="text-white text-lg font-bold">{{ $latestApp->currentRole?->display_name }} <i class="fas fa-arrow-right text-violet-400 mx-1.5 text-xs"></i> {{ $latestApp->targetRole?->display_name }}</p>
                <p class="text-white/50 text-sm">{{ $latestApp->period?->name }}</p>
            </div>
            @if($latestApp->status === 'approved')
                <span class="px-3.5 py-1.5 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 inline-flex items-center gap-1.5 w-max">
                    <i class="fas fa-check-double text-emerald-400"></i> Kenaikan Jabatan Disahkan
                </span>
            @elseif($latestApp->status === 'accepted')
                <span class="px-3.5 py-1.5 rounded-full text-xs font-semibold bg-sky-500/20 text-sky-300 border border-sky-500/30 inline-flex items-center gap-1.5 w-max">
                    <i class="fas fa-check text-sky-400"></i> ACC 1 Selesai: Pengajuan Diterima
                </span>
            @elseif($latestApp->status === 'rejected')
                <span class="px-3.5 py-1.5 rounded-full text-xs font-semibold bg-red-500/20 text-red-300 border border-red-500/30 inline-flex items-center gap-1.5 w-max">
                    <i class="fas fa-times text-red-400"></i> Pengajuan Ditolak
                </span>
            @else
                <span class="px-3.5 py-1.5 rounded-full text-xs font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/30 inline-flex items-center gap-1.5 w-max">
                    <i class="fas fa-clock text-amber-400 animate-pulse"></i> Menunggu ACC 1 (Penerimaan)
                </span>
            @endif
        </div>

        {{-- 2-Stage Approval Stepper --}}
        <div class="mb-6 p-4 rounded-xl bg-white/5 border border-white/10">
            <p class="text-xs font-semibold text-white/50 uppercase tracking-wider mb-3">Tahapan Persetujuan (2 Kali ACC)</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                {{-- Step 1 --}}
                <div class="flex items-center gap-2.5 p-2.5 rounded-lg bg-emerald-500/10 border border-emerald-500/30">
                    <div class="w-7 h-7 rounded-full bg-emerald-500/20 text-emerald-300 flex items-center justify-center font-bold text-xs shrink-0">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="text-xs">
                        <div class="font-bold text-emerald-300">1. Pengajuan Dikirim</div>
                        <div class="text-white/40 text-[10px]">Berkas & data tersimpan</div>
                    </div>
                </div>

                {{-- Step 2 --}}
                @php
                    $isStep2Done = in_array($latestApp->status, ['accepted', 'approved']);
                    $isStep2Current = ($latestApp->status === 'pending');
                    $isStep2Rejected = ($latestApp->status === 'rejected' && empty($latestApp->first_approved_at));
                @endphp
                <div class="flex items-center gap-2.5 p-2.5 rounded-lg border {{ $isStep2Done ? 'bg-emerald-500/10 border-emerald-500/30' : ($isStep2Rejected ? 'bg-red-500/10 border-red-500/30' : ($isStep2Current ? 'bg-amber-500/10 border-amber-500/30' : 'bg-white/5 border-white/10')) }}">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs shrink-0 {{ $isStep2Done ? 'bg-emerald-500/20 text-emerald-300' : ($isStep2Rejected ? 'bg-red-500/20 text-red-300' : ($isStep2Current ? 'bg-amber-500/20 text-amber-300 animate-pulse' : 'bg-white/10 text-white/40')) }}">
                        @if($isStep2Done) <i class="fas fa-check"></i> @elseif($isStep2Rejected) <i class="fas fa-times"></i> @else 2 @endif
                    </div>
                    <div class="text-xs">
                        <div class="font-bold {{ $isStep2Done ? 'text-emerald-300' : ($isStep2Rejected ? 'text-red-300' : ($isStep2Current ? 'text-amber-300' : 'text-white/50')) }}">
                            2. ACC 1: Penerimaan
                        </div>
                        <div class="text-white/40 text-[10px]">
                            @if($isStep2Done) Pengajuan diterima PND @elseif($isStep2Rejected) Berkas ditolak @else Sedang ditinjau PND @endif
                        </div>
                    </div>
                </div>

                {{-- Step 3 --}}
                @php
                    $isStep3Done = ($latestApp->status === 'approved');
                    $isStep3Current = ($latestApp->status === 'accepted');
                    $isStep3Rejected = ($latestApp->status === 'rejected' && !empty($latestApp->first_approved_at));
                @endphp
                <div class="flex items-center gap-2.5 p-2.5 rounded-lg border {{ $isStep3Done ? 'bg-emerald-500/10 border-emerald-500/30' : ($isStep3Rejected ? 'bg-red-500/10 border-red-500/30' : ($isStep3Current ? 'bg-sky-500/10 border-sky-500/30' : 'bg-white/5 border-white/10')) }}">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs shrink-0 {{ $isStep3Done ? 'bg-emerald-500/20 text-emerald-300' : ($isStep3Rejected ? 'bg-red-500/20 text-red-300' : ($isStep3Current ? 'bg-sky-500/20 text-sky-300 animate-pulse' : 'bg-white/10 text-white/40')) }}">
                        @if($isStep3Done) <i class="fas fa-check-double"></i> @elseif($isStep3Rejected) <i class="fas fa-times"></i> @else 3 @endif
                    </div>
                    <div class="text-xs">
                        <div class="font-bold {{ $isStep3Done ? 'text-emerald-300' : ($isStep3Rejected ? 'text-red-300' : ($isStep3Current ? 'text-sky-300' : 'text-white/50')) }}">
                            3. ACC 2: Kenaikan Jabatan
                        </div>
                        <div class="text-white/40 text-[10px]">
                            @if($isStep3Done) Resmi dinaikkan @elseif($isStep3Rejected) Ditolak tahap akhir @elseif($isStep3Current) Menunggu ACC kenaikan @else Belum dimulai @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Status Notification Message Banner --}}
        @if($latestApp->status === 'accepted')
        <div class="mb-5 p-4 bg-sky-500/15 border border-sky-500/30 rounded-xl text-sky-200 text-xs flex items-start gap-3">
            <i class="fas fa-info-circle text-sky-400 text-base mt-0.5 shrink-0"></i>
            <div>
                <p class="font-bold text-sky-100">Pengajuan Anda Telah Diterima (ACC Tahap 1 Selesai)</p>
                <p class="mt-0.5 text-sky-200/90 leading-relaxed">PND telah menerima dan memverifikasi berkas pengajuan Anda. Saat ini pengajuan Anda berada dalam tahap evaluasi akhir untuk menunggu <strong>ACC Tahap 2 (Pengesahan Kenaikan Jabatan)</strong>.</p>
                @if($latestApp->first_approval_notes || $latestApp->pnd_notes)
                    <div class="mt-2 p-2.5 bg-black/20 rounded-lg border border-sky-500/20 text-[11px]">
                        <span class="font-semibold text-sky-300">Catatan PND (ACC 1):</span> "{{ $latestApp->first_approval_notes ?: $latestApp->pnd_notes }}"
                    </div>
                @endif
            </div>
        </div>
        @elseif($latestApp->status === 'approved')
        <div class="mb-5 p-4 bg-emerald-500/15 border border-emerald-500/30 rounded-xl text-emerald-200 text-xs flex items-start gap-3">
            <i class="fas fa-check-circle text-emerald-400 text-base mt-0.5 shrink-0"></i>
            <div>
                <p class="font-bold text-emerald-100">Selamat! Kenaikan Jabatan Telah Disahkan (ACC 2 Selesai)</p>
                <p class="mt-0.5 text-emerald-200/90 leading-relaxed">Kenaikan jabatan Anda ke <strong>{{ $latestApp->targetRole?->display_name }}</strong> telah resmi disahkan oleh PND dan manajemen. Jabatan profil Anda telah diperbarui secara otomatis di seluruh sistem rumah sakit.</p>
                @if($latestApp->final_approval_notes || $latestApp->pnd_notes)
                    <div class="mt-2 p-2.5 bg-black/20 rounded-lg border border-emerald-500/20 text-[11px]">
                        <span class="font-semibold text-emerald-300">Catatan Pengesahan:</span> "{{ $latestApp->final_approval_notes ?: $latestApp->pnd_notes }}"
                    </div>
                @endif
            </div>
        </div>
        @elseif($latestApp->status === 'rejected')
        <div class="mb-5 p-4 bg-red-500/15 border border-red-500/30 rounded-xl text-red-200 text-xs flex items-start gap-3">
            <i class="fas fa-times-circle text-red-400 text-base mt-0.5 shrink-0"></i>
            <div>
                <p class="font-bold text-red-100">Pengajuan Belum Disetujui</p>
                <p class="mt-0.5 text-red-200/90 leading-relaxed">Pengajuan kenaikan jabatan Anda belum dapat disetujui untuk periode ini.</p>
                @if($latestApp->pnd_notes)
                    <div class="mt-2 p-2.5 bg-black/20 rounded-lg border border-red-500/20 text-[11px]">
                        <span class="font-semibold text-red-300">Alasan PND:</span> "{{ $latestApp->pnd_notes }}"
                    </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Checklist snapshot --}}
        @if($latestApp->requirements_checklist)
        <div class="space-y-2">
            <p class="text-white/50 text-xs uppercase tracking-wider font-semibold">Checklist Persyaratan saat Submit</p>
            @foreach($latestApp->requirements_checklist as $item)
            <div class="flex items-center gap-3 text-sm {{ $item['met'] ? 'text-emerald-300' : 'text-red-300' }}">
                <i class="fas {{ $item['met'] ? 'fa-check-circle' : 'fa-times-circle' }} text-sm shrink-0"></i>
                <span>{{ $item['label'] }}</span>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Berkas yang diunggah --}}
        @if($latestApp->case_study_file || $latestApp->recommendation_letter_1 || $latestApp->recommendation_letter_2 || $latestApp->supporting_document)
        <div class="mt-4 pt-3 border-t border-white/10">
            <p class="text-white/50 text-xs uppercase tracking-wider font-semibold mb-2">Berkas yang Anda Unggah</p>
            <div class="flex flex-wrap gap-2">
                @if($latestApp->case_study_file)
                    @php
                        $targetName = strtolower(($latestApp->targetRole?->name ?? '') . ' ' . ($latestApp->targetRole?->display_name ?? ''));
                        $isDokspel = str_contains($targetName, 'spesialis');
                    @endphp
                    <a href="{{ asset('storage/' . $latestApp->case_study_file) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 {{ $isDokspel ? 'bg-violet-500/20 text-violet-300 border-violet-500/30' : 'bg-sky-500/20 text-sky-300 border-sky-500/30' }} border rounded-xl text-xs font-medium hover:brightness-125 transition-all">
                        <i class="fas {{ $isDokspel ? 'fa-file-pdf text-rose-400' : 'fa-file-alt text-sky-400' }}"></i>
                        <span>{{ $isDokspel ? 'Studi Kasus Dokspel' : 'Berkas Laporan Kenaikan' }}</span>
                    </a>
                @endif
                @if($latestApp->recommendation_letter_1)
                    <a href="{{ asset('storage/' . $latestApp->recommendation_letter_1) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-500/20 text-blue-300 border border-blue-500/30 rounded-xl text-xs font-medium hover:brightness-125 transition-all">
                        <i class="fas fa-file-alt text-sky-400"></i>
                        <span>Rekomendasi Konsulen 1</span>
                    </a>
                @endif
                @if($latestApp->recommendation_letter_2)
                    <a href="{{ asset('storage/' . $latestApp->recommendation_letter_2) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-500/20 text-blue-300 border border-blue-500/30 rounded-xl text-xs font-medium hover:brightness-125 transition-all">
                        <i class="fas fa-file-alt text-sky-400"></i>
                        <span>Rekomendasi Konsulen 2</span>
                    </a>
                @endif
                @if($latestApp->supporting_document)
                    <a href="{{ asset('storage/' . $latestApp->supporting_document) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500/20 text-amber-300 border border-amber-500/30 rounded-xl text-xs font-medium hover:brightness-125 transition-all">
                        <i class="fas fa-folder-open text-amber-400"></i>
                        <span>Berkas Pendukung</span>
                    </a>
                @endif
            </div>
        </div>
        @endif

        @if($latestApp->pnd_notes && !in_array($latestApp->status, ['accepted', 'approved', 'rejected']))
        <div class="mt-4 p-3 bg-violet-500/10 rounded-xl border border-violet-500/20 text-xs text-violet-200">
            <span class="font-semibold">Catatan PND:</span> {{ $latestApp->pnd_notes }}
        </div>
        @endif
    </div>
    @endif
</div>
</div>
@endsection
