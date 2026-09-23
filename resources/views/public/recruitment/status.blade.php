@extends('layouts.app')

@section('title', 'Cek Status Pendaftaran - IME Medical Center')

@section('content')
<div class="min-h-screen py-10 px-4 sm:px-6" style="background: linear-gradient(135deg, #1e3a5f 0%, #0f2540 50%, #0a1a30 100%);">
    <div class="max-w-2xl mx-auto space-y-5">

        {{-- ── SEARCH CARD ── --}}
        <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl overflow-hidden shadow-2xl">
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between gap-3"
                 style="background: linear-gradient(90deg, rgba(156,131,74,0.35) 0%, rgba(30,58,95,0.5) 100%);">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="text-xl shrink-0">🔍</span>
                    <h1 class="text-sm sm:text-base font-black uppercase tracking-wider text-white truncate">
                        Cek Status Pendaftaran Paramedic
                    </h1>
                </div>
                <a href="{{ route('public.recruitment') }}"
                   class="shrink-0 text-xs bg-white/15 hover:bg-white/25 text-white px-3 py-1.5 rounded-full font-bold transition flex items-center gap-1">
                    <i class="fas fa-arrow-left text-[10px]"></i> Form Oprec
                </a>
            </div>

            {{-- Form --}}
            <div class="p-6 space-y-5">
                <p class="text-slate-300 text-sm leading-relaxed">
                    Masukkan <strong class="text-white">Citizen ID (CID)</strong> karakter FiveM Anda
                    untuk memeriksa status berkas, antrian interview, dan keputusan penerimaan secara langsung.
                </p>

                <form method="GET" action="{{ route('public.recruitment.status') }}"
                      class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 font-bold text-sm pointer-events-none">#</span>
                        <input type="text" name="cid" value="{{ $cid }}"
                               placeholder="Contoh: DR3CHXY4"
                               class="w-full pl-8 pr-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:outline-none focus:ring-2 focus:ring-[#9c834a] text-white uppercase font-mono text-sm tracking-wider placeholder-slate-500 transition"
                               required autofocus />
                    </div>
                    <button type="submit"
                            class="px-6 py-3 font-black text-sm rounded-xl text-white shadow-lg transition flex items-center justify-center gap-2 whitespace-nowrap"
                            style="background: linear-gradient(135deg, #9c834a, #b89b60);">
                        <i class="fas fa-search"></i> Cek Status
                    </button>
                </form>

                @if($searched && $applications->isEmpty())
                    <div class="bg-amber-500/15 border border-amber-500/30 rounded-xl p-4 flex items-start gap-3">
                        <i class="fas fa-exclamation-triangle text-amber-400 text-lg mt-0.5 shrink-0"></i>
                        <div class="text-sm">
                            <p class="text-amber-300 font-bold">Berkas tidak ditemukan untuk CID: <code class="font-mono">#{{ strtoupper($cid) }}</code></p>
                            <p class="text-amber-200/80 text-xs mt-1">Pastikan Citizen ID yang Anda masukkan sama persis dengan yang Anda isi di formulir pendaftaran.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- ── RESULTS ── --}}
        @if($searched && $applications->isNotEmpty())
            @foreach($applications as $app)
                @php
                    $status     = strtolower($app->status);
                    $periodName = $app->period?->batch_name ?? 'Alta Hospital Medical Center';

                    $currentStep = 1;
                    if ($status === 'reviewed')                               $currentStep = 2;
                    elseif ($status === 'interview')                          $currentStep = 3;
                    elseif (in_array($status, ['accepted','approved','rejected'])) $currentStep = 4;

                    $steps = [
                        ['label' => 'Berkas Masuk',      'icon' => 'fa-inbox'],
                        ['label' => 'Verifikasi Berkas',  'icon' => 'fa-clipboard-check'],
                        ['label' => 'Wawancara',          'icon' => 'fa-microphone-alt'],
                        ['label' => 'Keputusan',          'icon' => 'fa-gavel'],
                    ];
                @endphp

                <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl overflow-hidden shadow-2xl">

                    {{-- Card Header: Identity + Badge --}}
                    <div class="px-6 py-4 border-b border-white/10 bg-white/5">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="min-w-0">
                                <span class="text-[11px] text-slate-400 font-bold uppercase tracking-widest block mb-0.5">
                                    Calon Paramedis
                                </span>
                                <h2 class="text-lg font-black text-white leading-tight truncate">{{ $app->ic_name }}</h2>
                                <p class="text-xs text-slate-400 font-mono mt-0.5">
                                    CID: <span class="text-slate-200">#{{ $app->cid }}</span>
                                    &nbsp;&bull;&nbsp;
                                    <span class="text-slate-200">{{ $periodName }}</span>
                                </p>
                            </div>

                            {{-- Status Badge --}}
                            <div class="shrink-0">
                                @if(in_array($status, ['accepted','approved']))
                                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-emerald-500 text-white font-black rounded-full text-xs uppercase tracking-wide shadow">
                                        <i class="fas fa-check-circle"></i> Diterima
                                    </span>
                                @elseif($status === 'rejected')
                                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-rose-500 text-white font-black rounded-full text-xs uppercase tracking-wide shadow">
                                        <i class="fas fa-times-circle"></i> Belum Lolos
                                    </span>
                                @elseif($status === 'interview')
                                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-sky-500 text-white font-black rounded-full text-xs uppercase tracking-wide shadow animate-pulse">
                                        <i class="fas fa-microphone-alt"></i> Wawancara
                                    </span>
                                @elseif($status === 'reviewed')
                                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-indigo-500 text-white font-black rounded-full text-xs uppercase tracking-wide shadow">
                                        <i class="fas fa-clipboard-check"></i> Berkas Lolos
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-amber-500 text-white font-black rounded-full text-xs uppercase tracking-wide shadow">
                                        <i class="fas fa-hourglass-half"></i> Ditinjau
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="p-5 sm:p-6 space-y-5">

                        {{-- ── PROGRESS STEPPER ── --}}
                        <div class="relative">
                            {{-- Connector line background --}}
                            <div class="absolute top-4 left-4 right-4 h-0.5 bg-white/10 z-0"></div>
                            {{-- Connector line filled --}}
                            @php
                                $fillPct = $status === 'rejected' ? 100 : (($currentStep - 1) / 3 * 100);
                            @endphp
                            <div class="absolute top-4 left-4 h-0.5 z-0 transition-all duration-500 rounded-full"
                                 style="width: calc({{ $fillPct }}% - 2rem); background: linear-gradient(90deg, #9c834a, #5eead4);"></div>

                            {{-- Steps --}}
                            <div class="relative z-10 flex justify-between">
                                @foreach($steps as $i => $step)
                                    @php
                                        $stepNum  = $i + 1;
                                        $isActive = $stepNum === $currentStep;
                                        $isDone   = $stepNum < $currentStep;
                                        $isFail   = ($status === 'rejected' && $stepNum === 4);

                                        if ($isFail)       $circleClass = 'bg-rose-500 text-white ring-2 ring-rose-400/40';
                                        elseif ($isActive && in_array($status, ['accepted','approved'])) $circleClass = 'bg-emerald-500 text-white ring-2 ring-emerald-400/40';
                                        elseif ($isActive) $circleClass = 'bg-sky-500 text-white ring-2 ring-sky-400/40';
                                        elseif ($isDone)   $circleClass = 'bg-[#9c834a] text-white';
                                        else               $circleClass = 'bg-white/10 text-slate-500';
                                    @endphp
                                    <div class="flex flex-col items-center gap-1.5 w-1/4">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shadow-lg transition-all {{ $circleClass }}">
                                            @if($isFail)
                                                <i class="fas fa-times text-[10px]"></i>
                                            @elseif($isDone || ($isActive && in_array($status, ['accepted','approved'])))
                                                <i class="fas fa-check text-[10px]"></i>
                                            @else
                                                {{ $stepNum }}
                                            @endif
                                        </div>
                                        <span class="text-[10px] font-semibold text-center leading-tight
                                            {{ $isActive ? 'text-white' : ($isDone ? 'text-slate-300' : 'text-slate-500') }}">
                                            {{ $step['label'] }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- ── STATUS DETAIL BOX ── --}}
                        @if(in_array($status, ['accepted','approved']))
                            <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-4 space-y-3">
                                <div class="flex items-center gap-2 text-emerald-300 font-black text-sm">
                                    <i class="fas fa-award text-base shrink-0"></i>
                                    <span>SELAMAT! ANDA RESMI DITERIMA SEBAGAI PARAMEDIS</span>
                                </div>
                                <p class="text-slate-300 text-sm leading-relaxed">
                                    Selamat bergabung di <strong class="text-white">Alta Hospital Medical Center</strong>!
                                    Anda terdaftar sebagai anggota <strong class="text-white">{{ $periodName }}</strong>.
                                </p>

                                {{-- Login Info Box --}}
                                <div class="rounded-xl bg-white/5 border border-white/10 p-4 space-y-3">
                                    <p class="text-xs font-black text-emerald-300 uppercase tracking-wider flex items-center gap-1.5">
                                        <i class="fas fa-key"></i> Informasi Akun Portal Staf
                                    </p>
                                    <div class="space-y-1.5 text-xs text-slate-300">
                                        <div class="flex items-start gap-2">
                                            <i class="fas fa-circle-check text-emerald-400 mt-0.5 shrink-0 text-[10px]"></i>
                                            <span>Akun Anda sudah <strong class="text-white">AKTIF & TERDAFTAR</strong> secara otomatis di sistem.</span>
                                        </div>
                                        <div class="flex items-start gap-2">
                                            <i class="fas fa-circle-check text-emerald-400 mt-0.5 shrink-0 text-[10px]"></i>
                                            <span><strong class="text-white">Tidak perlu daftar ulang.</strong> Langsung login menggunakan email & password yang Anda buat saat mendaftar.</span>
                                        </div>
                                        @if(!empty($app->email))
                                        <div class="flex items-start gap-2">
                                            <i class="fas fa-circle-check text-emerald-400 mt-0.5 shrink-0 text-[10px]"></i>
                                            <span>
                                                Email login Anda:
                                                <code class="font-mono bg-emerald-500/20 px-1.5 py-0.5 rounded text-emerald-200 font-bold">
                                                    {{ substr($app->email, 0, 3) }}***@{{ explode('@', $app->email)[1] ?? '' }}
                                                </code>
                                            </span>
                                        </div>
                                        @endif
                                        <div class="flex items-start gap-2">
                                            <i class="fas fa-circle-check text-emerald-400 mt-0.5 shrink-0 text-[10px]"></i>
                                            <span>CID Anda: <code class="font-mono bg-emerald-500/20 px-1.5 py-0.5 rounded text-emerald-300 font-bold">#{{ $app->cid }}</code></span>
                                        </div>
                                        <div class="flex items-start gap-2">
                                            <i class="fas fa-circle-check text-emerald-400 mt-0.5 shrink-0 text-[10px]"></i>
                                            <span>Badge angkatan Anda: <span class="font-bold text-white">{{ $periodName }}</span></span>
                                        </div>
                                        <div class="flex items-start gap-2">
                                            <i class="fas fa-circle-info text-sky-400 mt-0.5 shrink-0 text-[10px]"></i>
                                            <span class="text-sky-300">Belum punya akun? Hubungi Admin / HRD melalui Discord untuk dibuatkan akun.</span>
                                        </div>
                                    </div>
                                    <a href="{{ route('staff.login') }}"
                                       class="mt-1 w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl font-black text-xs text-white shadow-lg transition"
                                       style="background: linear-gradient(135deg, #059669, #10b981);">
                                        <i class="fas fa-sign-in-alt"></i> Login ke Portal Staf Sekarang
                                    </a>
                                </div>
                            </div>

                        @elseif($status === 'rejected')
                            <div class="rounded-xl border border-rose-500/30 bg-rose-500/10 p-4 space-y-3">
                                <div class="flex items-center gap-2 text-rose-300 font-black text-sm">
                                    <i class="fas fa-times-circle text-base shrink-0"></i>
                                    <span>MOHON MAAF, ANDA BELUM LOLOS PADA PERIODE INI</span>
                                </div>
                                <p class="text-slate-300 text-sm leading-relaxed">
                                    Terima kasih atas ketertarikan dan partisipasi Anda. Jangan berkecil hati —
                                    Anda tetap dapat mencoba kembali pada pembukaan rekrutmen batch berikutnya.
                                </p>
                                @if(!empty($app->reviewer_notes))
                                    <div class="rounded-xl bg-white/5 border border-rose-500/20 p-3 text-xs text-slate-300">
                                        <p class="font-bold text-rose-300 mb-1 flex items-center gap-1">
                                            <i class="fas fa-comment-dots text-[10px]"></i> Catatan Penilai:
                                        </p>
                                        {{ $app->reviewer_notes }}
                                    </div>
                                @endif
                            </div>

                        @elseif($status === 'interview')
                            <div class="rounded-xl border border-sky-500/30 bg-sky-500/10 p-4 space-y-2">
                                <div class="flex items-center gap-2 text-sky-300 font-black text-sm">
                                    <i class="fas fa-headset text-base shrink-0"></i>
                                    <span>TAHAP WAWANCARA (INTERVIEW)</span>
                                </div>
                                <p class="text-slate-300 text-sm leading-relaxed">
                                    Berkas pendaftaran Anda telah <strong class="text-white">LOLOS VERIFIKASI</strong>.
                                    Anda saat ini masuk dalam antrian wawancara dengan tim Interviewer Alta Hospital.
                                </p>
                                <div class="rounded-xl bg-white/5 border border-sky-500/20 p-3 text-xs text-sky-300 font-semibold">
                                    📢 Pantau pengumuman antrian dan hubungi Interviewer melalui Discord resmi Rumah Sakit untuk informasi jadwal wawancara Anda.
                                </div>
                            </div>

                        @elseif($status === 'reviewed')
                            <div class="rounded-xl border border-indigo-500/30 bg-indigo-500/10 p-4 space-y-2">
                                <div class="flex items-center gap-2 text-indigo-300 font-black text-sm">
                                    <i class="fas fa-file-signature text-base shrink-0"></i>
                                    <span>BERKAS TELAH DIVERIFIKASI</span>
                                </div>
                                <p class="text-slate-300 text-sm leading-relaxed">
                                    Dokumen persyaratan Anda (KTP, SKB, Surat Kesehatan) telah diverifikasi oleh tim IE/PND
                                    dan memenuhi syarat awal. Berkas Anda sedang diarahkan ke tim pewawancara.
                                </p>
                            </div>

                        @else
                            <div class="rounded-xl border border-amber-500/30 bg-amber-500/10 p-4 space-y-2">
                                <div class="flex items-center gap-2 text-amber-300 font-black text-sm">
                                    <i class="fas fa-clock text-base shrink-0"></i>
                                    <span>BERKAS DALAM ANTRIAN PENINJAUAN</span>
                                </div>
                                <p class="text-slate-300 text-sm leading-relaxed">
                                    Formulir pendaftaran Anda sudah masuk ke sistem dan sedang menunggu antrian pengecekan
                                    oleh divisi <strong class="text-white">Industrial &amp; Employee Relations (IE)</strong>.
                                    Mohon menunggu dengan sabar.
                                </p>
                            </div>
                        @endif

                        {{-- ── APPLICATION META ── --}}
                        <div class="border-t border-white/10 pt-4">
                            <div class="grid grid-cols-2 gap-x-4 gap-y-3">
                                <div>
                                    <span class="block text-[10px] text-slate-500 font-bold uppercase tracking-wider mb-0.5">Waktu Pendaftaran</span>
                                    <span class="text-xs text-slate-300 font-semibold">{{ $app->created_at?->format('d/m/Y H:i') ?? '-' }} WIB</span>
                                </div>
                                <div>
                                    <span class="block text-[10px] text-slate-500 font-bold uppercase tracking-wider mb-0.5">Kategori RS</span>
                                    <span class="text-xs text-slate-300 font-semibold uppercase">{{ $app->hospital }} Hospital</span>
                                </div>
                                <div>
                                    <span class="block text-[10px] text-slate-500 font-bold uppercase tracking-wider mb-0.5">Discord</span>
                                    <span class="text-xs text-slate-300 font-mono font-semibold break-all">{{ $app->discord_username ?? '-' }}</span>
                                </div>
                                <div>
                                    <span class="block text-[10px] text-slate-500 font-bold uppercase tracking-wider mb-0.5">Update Terakhir</span>
                                    <span class="text-xs text-slate-300 font-semibold">{{ $app->updated_at?->diffForHumans() ?? '-' }}</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        @endif

        {{-- Footer --}}
        <div class="text-center pb-4">
            <a href="{{ route('public.index') }}"
               class="text-xs text-slate-500 hover:text-slate-300 transition font-semibold inline-flex items-center gap-1.5">
                <i class="fas fa-home text-[10px]"></i> Kembali ke Beranda IME Medical Center
            </a>
        </div>

    </div>
</div>
@endsection
