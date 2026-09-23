@extends('layouts.app')

@section('title', 'Cek Status Pendaftaran - Paramedic IME Medical Center')

@section('content')
<div class="min-h-screen bg-[#f0ede6] py-10 px-4 sm:px-6 flex items-center justify-center">
    <div class="max-w-3xl w-full space-y-6">

        <!-- SEARCH FORM CARD -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-200">
            <!-- Header Bronze / Gold Bar -->
            <div class="bg-[#9c834a] px-6 py-4 flex items-center justify-between text-white">
                <div class="flex items-center gap-2.5">
                    <span class="text-xl">🔍</span>
                    <h1 class="text-base sm:text-lg font-black uppercase tracking-wider">
                        Cek Status Pendaftaran Paramedic
                    </h1>
                </div>
                <a href="{{ route('public.recruitment') }}" class="text-xs bg-white/20 hover:bg-white/30 text-white px-3 py-1 rounded-full font-bold transition">
                    <i class="fas fa-arrow-left mr-1"></i> Form Oprec
                </a>
            </div>

            <!-- Form Content -->
            <div class="p-6 sm:p-8 space-y-6">
                <p class="text-slate-600 text-sm">
                    Masukkan <strong>Citizen ID (CID)</strong> karakter FiveM Anda untuk memeriksa status berkas, antrian interview, dan keputusan penerimaan secara langsung.
                </p>

                <form method="GET" action="{{ route('public.recruitment.status') }}" class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 font-bold text-sm">#</span>
                        <input type="text" name="cid" value="{{ $cid }}" placeholder="Contoh: DR3CHXY4" 
                               class="w-full pl-9 pr-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#9c834a] uppercase font-mono text-sm tracking-wider shadow-sm font-semibold"
                               required autofocus />
                    </div>
                    <button type="submit" class="px-6 py-3 bg-[#9c834a] hover:bg-[#856e3c] text-white font-black text-sm rounded-xl transition shadow-md flex items-center justify-center gap-2">
                        <i class="fas fa-search"></i> Cek Status
                    </button>
                </form>

                @if($searched && $applications->isEmpty())
                    <div class="bg-amber-50 border border-amber-300 rounded-xl p-4 text-amber-900 text-xs sm:text-sm flex items-start gap-3">
                        <i class="fas fa-exclamation-triangle text-amber-600 text-lg mt-0.5"></i>
                        <div>
                            <strong>Tidak ditemukan berkas pendaftaran dengan Citizen ID: #{{ strtoupper($cid) }}</strong>
                            <p class="text-amber-800 mt-1">Pastikan Citizen ID yang Anda masukkan sama persis dengan yang Anda isi di formulir pendaftaran.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- SEARCH RESULTS LIST -->
        @if($searched && $applications->isNotEmpty())
            @foreach($applications as $app)
                @php
                    $status = strtolower($app->status);
                    $periodName = $app->period?->batch_name ?? 'Recruitment Alta Hospital';

                    // Progress steps calculation
                    // 1: submitted/pending, 2: reviewed, 3: interview, 4: accepted / rejected
                    $currentStep = 1;
                    if ($status === 'reviewed') $currentStep = 2;
                    elseif ($status === 'interview') $currentStep = 3;
                    elseif (in_array($status, ['accepted', 'approved', 'rejected'])) $currentStep = 4;
                @endphp

                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-200">
                    <div class="px-6 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3 bg-slate-50">
                        <div>
                            <span class="text-xs text-slate-500 font-semibold uppercase tracking-wider block">Calon Paramedis</span>
                            <h2 class="text-base sm:text-lg font-black text-slate-900">{{ $app->ic_name }}</h2>
                            <span class="text-xs text-slate-500 font-mono">CID: #{{ $app->cid }} &bull; {{ $periodName }}</span>
                        </div>

                        {{-- Main Status Badge --}}
                        <div>
                            @if(in_array($status, ['accepted', 'approved']))
                                <span class="px-4 py-1.5 bg-emerald-500 text-white font-black rounded-full text-xs uppercase tracking-wide shadow flex items-center gap-1.5">
                                    <i class="fas fa-check-circle"></i> Diterima (Lolos)
                                </span>
                            @elseif($status === 'rejected')
                                <span class="px-4 py-1.5 bg-rose-500 text-white font-black rounded-full text-xs uppercase tracking-wide shadow flex items-center gap-1.5">
                                    <i class="fas fa-times-circle"></i> Belum Lolos
                                </span>
                            @elseif($status === 'interview')
                                <span class="px-4 py-1.5 bg-sky-500 text-white font-black rounded-full text-xs uppercase tracking-wide shadow flex items-center gap-1.5 animate-pulse">
                                    <i class="fas fa-microphone-alt"></i> Tahap Wawancara
                                </span>
                            @elseif($status === 'reviewed')
                                <span class="px-4 py-1.5 bg-indigo-500 text-white font-black rounded-full text-xs uppercase tracking-wide shadow flex items-center gap-1.5">
                                    <i class="fas fa-clipboard-check"></i> Berkas Lolos
                                </span>
                            @else
                                <span class="px-4 py-1.5 bg-amber-500 text-white font-black rounded-full text-xs uppercase tracking-wide shadow flex items-center gap-1.5">
                                    <i class="fas fa-hourglass-half"></i> Sedang Ditinjau
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="p-6 sm:p-8 space-y-6">
                        <!-- PROGRESS STEPPER TIMELINE -->
                        <div class="py-2">
                            <div class="relative flex items-center justify-between">
                                <!-- Background Line -->
                                <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-slate-200 w-full z-0"></div>
                                <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-gradient-to-r from-[#9c834a] to-emerald-500 z-0 transition-all duration-500"
                                     style="width: {{ $status === 'rejected' ? '100%' : (($currentStep - 1) / 3 * 100) }}%"></div>

                                <!-- Step 1: Submit -->
                                <div class="relative z-10 flex flex-col items-center">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shadow {{ $currentStep >= 1 ? 'bg-[#9c834a] text-white' : 'bg-slate-200 text-slate-500' }}">
                                        1
                                    </div>
                                    <span class="text-[11px] font-bold text-slate-700 mt-1 text-center">Berkas Masuk</span>
                                </div>

                                <!-- Step 2: Review -->
                                <div class="relative z-10 flex flex-col items-center">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shadow {{ $currentStep >= 2 ? 'bg-[#9c834a] text-white' : 'bg-slate-200 text-slate-500' }}">
                                        2
                                    </div>
                                    <span class="text-[11px] font-bold text-slate-700 mt-1 text-center">Verifikasi Berkas</span>
                                </div>

                                <!-- Step 3: Interview -->
                                <div class="relative z-10 flex flex-col items-center">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shadow {{ $currentStep >= 3 ? 'bg-sky-500 text-white ring-4 ring-sky-100' : 'bg-slate-200 text-slate-500' }}">
                                        3
                                    </div>
                                    <span class="text-[11px] font-bold text-slate-700 mt-1 text-center">Wawancara</span>
                                </div>

                                <!-- Step 4: Decision -->
                                <div class="relative z-10 flex flex-col items-center">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shadow {{ $status === 'rejected' ? 'bg-rose-500 text-white' : ($currentStep >= 4 ? 'bg-emerald-500 text-white ring-4 ring-emerald-100' : 'bg-slate-200 text-slate-500') }}">
                                        {!! $status === 'rejected' ? '<i class="fas fa-times"></i>' : ($currentStep >= 4 ? '<i class="fas fa-check"></i>' : '4') !!}
                                    </div>
                                    <span class="text-[11px] font-bold text-slate-700 mt-1 text-center">Keputusan</span>
                                </div>
                            </div>
                        </div>

                        <!-- STATUS DETAIL BOX -->
                        <div class="rounded-2xl p-5 border text-sm {{ in_array($status, ['accepted', 'approved']) ? 'bg-emerald-50/70 border-emerald-300 text-emerald-950' : ($status === 'rejected' ? 'bg-rose-50/70 border-rose-300 text-rose-950' : ($status === 'interview' ? 'bg-sky-50/70 border-sky-300 text-sky-950' : 'bg-amber-50/70 border-amber-300 text-amber-950')) }}">
                            
                            @if(in_array($status, ['accepted', 'approved']))
                                <div class="space-y-3">
                                    <div class="flex items-center gap-2 text-emerald-800 font-black text-base">
                                        <i class="fas fa-award text-xl text-emerald-600"></i>
                                        <span>SELAMAT! ANDA RESMI DITERIMA SEBAGAI PARAMEDIS</span>
                                    </div>
                                    <p class="leading-relaxed">
                                        Selamat bergabung di <strong>Alta Hospital Medical Center</strong>! Anda terdaftar sebagai anggota <strong>{{ $periodName }}</strong>.
                                    </p>
                                    <div class="bg-white/80 rounded-xl p-3.5 border border-emerald-200 text-xs space-y-1 text-emerald-900">
                                        <div><strong>Status Akun:</strong> <span class="text-emerald-700 font-bold">AKTIF & TERDAFTAR</span></div>
                                        <div><strong>ID Anggota / CID:</strong> <code class="font-mono bg-emerald-100 px-1.5 py-0.5 rounded text-emerald-900 font-bold">#{{ $app->cid }}</code></div>
                                        <div><strong>Badge Angkatan:</strong> <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-bold border bg-purple-500/20 text-purple-700 border-purple-500/30 font-mono">{{ $periodName }}</span></div>
                                    </div>
                                    <div class="pt-2">
                                        <a href="{{ route('staff.login') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs shadow transition">
                                            <i class="fas fa-sign-in-alt"></i> Masuk ke Portal Staf
                                        </a>
                                    </div>
                                </div>

                            @elseif($status === 'rejected')
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2 text-rose-800 font-black text-base">
                                        <i class="fas fa-times-circle text-xl text-rose-600"></i>
                                        <span>MOHON MAAF, ANDA BELUM LOLOS PADA PERIODE INI</span>
                                    </div>
                                    <p class="text-slate-700 leading-relaxed text-xs sm:text-sm">
                                        Terima kasih atas ketertarikan dan partisipasi Anda. Jangan berkecil hati, Anda tetap dapat mencoba kembali pada pembukaan rekrutmen batch berikutnya.
                                    </p>
                                    @if(!empty($app->reviewer_notes))
                                        <div class="mt-3 p-3 bg-white/80 rounded-xl border border-rose-200 text-xs text-rose-900">
                                            <strong class="block mb-1 text-rose-950 font-bold"><i class="fas fa-comment-dots mr-1"></i> Catatan Penilai:</strong>
                                            {{ $app->reviewer_notes }}
                                        </div>
                                    @endif
                                </div>

                            @elseif($status === 'interview')
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2 text-sky-800 font-black text-base">
                                        <i class="fas fa-headset text-xl text-sky-600"></i>
                                        <span>TAHAP WAWANCARA (INTERVIEW)</span>
                                    </div>
                                    <p class="text-slate-700 leading-relaxed text-xs sm:text-sm">
                                        Berkas pendaftaran Anda telah <strong>LOLOS VERIFIKASI</strong>. Anda saat ini masuk dalam antrian wawancara dengan tim Interviewer Alta Hospital.
                                    </p>
                                    <p class="text-xs text-sky-900 font-semibold bg-white/70 p-3 rounded-xl border border-sky-200">
                                        📢 Silakan pantau pengumuman antrian atau hubungi pihak Interviewer di Discord resmi Rumah Sakit untuk jadwal wawancara Anda.
                                    </p>
                                </div>

                            @elseif($status === 'reviewed')
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2 text-indigo-800 font-black text-base">
                                        <i class="fas fa-file-signature text-xl text-indigo-600"></i>
                                        <span>BERKAS TELAH DIVERIFIKASI</span>
                                    </div>
                                    <p class="text-slate-700 leading-relaxed text-xs sm:text-sm">
                                        Dokumen persyaratan Anda (KTP, SKB, Surat Kesehatan) telah diverifikasi oleh tim IE/PND dan memenuhi syarat awal. Berkas Anda sedang diarahkan ke tim pewawancara.
                                    </p>
                                </div>

                            @else
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2 text-amber-800 font-black text-base">
                                        <i class="fas fa-clock text-xl text-amber-600"></i>
                                        <span>BERKAS DALAM ANTRIAN PENINJAUAN</span>
                                    </div>
                                    <p class="text-slate-700 leading-relaxed text-xs sm:text-sm">
                                        Formulir pendaftaran Anda sudah masuk ke sistem dan sedang menunggu antrian pengecekan oleh divisi <strong>Industrial & Employee Relations (IE)</strong>. Mohon menunggu dengan sabar.
                                    </p>
                                </div>
                            @endif

                        </div>

                        <!-- APPLICATION META -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs pt-2 border-t border-slate-100 text-slate-600">
                            <div>
                                <span class="block text-slate-400 text-[10px] uppercase font-bold">Waktu Pendaftaran</span>
                                <span class="font-semibold text-slate-800">{{ $app->created_at?->format('d/m/Y H:i') ?? '-' }} WIB</span>
                            </div>
                            <div>
                                <span class="block text-slate-400 text-[10px] uppercase font-bold">Kategori RS</span>
                                <span class="font-semibold text-slate-800 uppercase">{{ $app->hospital }} Hospital</span>
                            </div>
                            <div>
                                <span class="block text-slate-400 text-[10px] uppercase font-bold">Discord</span>
                                <span class="font-semibold text-slate-800 font-mono">{{ $app->discord_username ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="block text-slate-400 text-[10px] uppercase font-bold">Update Terakhir</span>
                                <span class="font-semibold text-slate-800">{{ $app->updated_at?->diffForHumans() ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

        <!-- FOOTER NAV -->
        <div class="text-center pt-2">
            <a href="{{ route('public.index') }}" class="text-xs text-slate-500 hover:text-slate-800 transition font-semibold">
                <i class="fas fa-home mr-1"></i> Kembali ke Beranda Utama IME Medical Center
            </a>
        </div>

    </div>
</div>
@endsection
