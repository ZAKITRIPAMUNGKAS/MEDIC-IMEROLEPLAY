@extends('layouts.app')

@section('title', 'Benefit Oplas Gratis - Portal Medis')

@section('content')
<div class="min-h-screen pt-20 pb-16 px-4 sm:px-6 lg:px-8" style="background: linear-gradient(180deg, #0c4a6e 0%, #075985 50%, #0369a1 100%);">
    <div class="max-w-6xl mx-auto text-white">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-xs text-white/50 mb-6">
            <a href="{{ route('public.index') }}" class="hover:text-white transition-colors">Beranda</a>
            <i class="fas fa-chevron-right text-[10px]"></i>
            <span class="text-white/80">Menu Staf</span>
            <i class="fas fa-chevron-right text-[10px]"></i>
            <span class="text-emerald-400 font-medium">Fasilitas Oplas Gratis</span>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-200 flex items-center justify-between shadow-lg shadow-emerald-950/20 backdrop-blur-md">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/30 flex items-center justify-center text-emerald-300">
                        <i class="fas fa-check text-lg"></i>
                    </div>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-white/60 hover:text-white text-lg">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-200 flex items-center justify-between shadow-lg shadow-rose-950/20 backdrop-blur-md">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-rose-500/30 flex items-center justify-center text-rose-300">
                        <i class="fas fa-exclamation-triangle text-lg"></i>
                    </div>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-white/60 hover:text-white text-lg">&times;</button>
            </div>
        @endif

        {{-- Hero Header Card (Sesuai Style Default Portal & Training Alta) --}}
        <div class="rounded-2xl bg-white/[0.05] border border-white/10 shadow-2xl backdrop-blur-md overflow-hidden mb-8">
            <div class="h-2.5 w-full bg-gradient-to-r from-emerald-500 via-teal-400 to-cyan-500"></div>

            <div class="p-6 sm:p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-xs font-semibold uppercase tracking-wider mb-3">
                        <i class="fas fa-star text-amber-300"></i> Benefit Fasilitas Anggota EMS
                    </div>
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-black text-white tracking-wide">
                        Fasilitas Oplas Gratis
                    </h1>
                    <p class="text-sky-100/80 text-sm sm:text-base mt-2 max-w-2xl leading-relaxed">
                        Setiap anggota medis yang aktif berhak mendapatkan <strong>1x kuota Operasi Plastik (Oplas) Gratis</strong> setiap bulannya tanpa perlu input data ulang atau upload berkas. Kuota akan otomatis di-reset setiap tanggal 1 awal bulan.
                    </p>
                </div>

                {{-- Status Box Kuota --}}
                <div class="flex-shrink-0 bg-white/[0.07] border border-white/15 rounded-2xl p-5 text-center min-w-[230px] shadow-lg">
                    <div class="text-[11px] uppercase tracking-wider text-sky-200 font-bold mb-1">
                        Status Kuota {{ now()->translatedFormat('F Y') }}
                    </div>
                    @if($canClaim)
                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-500/20 text-emerald-300 font-extrabold text-sm my-1 border border-emerald-500/30">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            Tersedia (1/1)
                        </div>
                        <p class="text-xs text-emerald-200/90 mt-1 font-medium">Siap diklaim sekarang</p>
                    @else
                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-500/20 text-amber-300 font-extrabold text-sm my-1 border border-amber-500/30">
                            <i class="fas fa-check-circle"></i> Sudah Digunakan
                        </div>
                        <p class="text-[11px] text-sky-200/80 mt-1">Reset: <strong class="text-white">{{ $nextAvailableDate }}</strong></p>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Panel Utama (Klaim 1-Klik) --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="rounded-2xl bg-white/[0.05] border border-white/10 p-6 sm:p-8 backdrop-blur-md shadow-xl">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2 mb-4 pb-3 border-b border-white/10">
                        <i class="fas fa-id-card text-cyan-400"></i> Informasi Data Akun Medis Anda
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        <div class="bg-white/[0.04] rounded-xl p-4 border border-white/10">
                            <span class="block text-xs uppercase tracking-wider text-sky-200/60 mb-1">Nama Karakter (IC)</span>
                            <span class="text-base font-bold text-white">{{ $user->name }}</span>
                        </div>
                        <div class="bg-white/[0.04] rounded-xl p-4 border border-white/10">
                            <span class="block text-xs uppercase tracking-wider text-sky-200/60 mb-1">Citizen ID (CID)</span>
                            <span class="text-base font-bold text-white font-mono">{{ $user->citizen_id ?: $user->staff_id ?: '-' }}</span>
                        </div>
                        <div class="bg-white/[0.04] rounded-xl p-4 border border-white/10">
                            <span class="block text-xs uppercase tracking-wider text-sky-200/60 mb-1">Jabatan / Role</span>
                            <span class="text-sm font-semibold text-cyan-200">{{ $user->role?->display_name ?? $user->role?->name ?? 'Anggota Medic' }}</span>
                        </div>
                        <div class="bg-white/[0.04] rounded-xl p-4 border border-white/10">
                            <span class="block text-xs uppercase tracking-wider text-sky-200/60 mb-1">Batch / Angkatan</span>
                            <span class="text-sm font-semibold text-amber-300">{{ $user->batch ?? 'Batch Medis' }}</span>
                        </div>
                    </div>

                    @if($canClaim)
                        <form action="{{ route('staff.benefits.oplas.claim') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengklaim fasilitas Oplas Gratis untuk bulan ini?');">
                            @csrf
                            <div class="mb-5">
                                <label class="block text-xs uppercase tracking-wider text-sky-200 font-semibold mb-2">
                                    Catatan / Permintaan Khusus (Opsional)
                                </label>
                                <textarea name="notes" rows="2" class="w-full bg-white/[0.05] border border-white/15 rounded-xl px-4 py-2.5 text-white placeholder-sky-200/40 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:border-transparent transition-all"
                                    placeholder="Contoh: Operasi wajah/rambut, atau janjian dengan dr. Joseph..."></textarea>
                            </div>

                            <button type="submit" class="w-full py-4 px-6 rounded-xl bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 hover:from-emerald-500 hover:to-cyan-500 text-white font-bold text-base shadow-xl shadow-cyan-900/30 transition-all duration-300 flex items-center justify-center gap-3 transform hover:-translate-y-0.5 cursor-pointer">
                                <i class="fas fa-magic text-lg text-emerald-200"></i>
                                <span>Klaim 1-Klik Oplas Gratis Sekarang</span>
                            </button>
                        </form>
                    @else
                        <div class="p-6 rounded-2xl bg-amber-500/15 border border-amber-500/30 text-amber-200 text-center">
                            <i class="fas fa-info-circle text-2xl text-amber-400 mb-2"></i>
                            <h4 class="font-bold text-base text-white">Kuota Bulan Ini Sudah Digunakan</h4>
                            <p class="text-xs text-amber-200/90 mt-1.5 max-w-md mx-auto leading-relaxed">
                                Pengajuan Anda pada <strong>{{ $thisMonthRequest->created_at->format('d M Y, H:i') }}</strong> sedang dalam status:
                                <span class="font-bold uppercase text-white px-2 py-0.5 rounded bg-amber-500/40">
                                    {{ $thisMonthRequest->status === 'approved' ? 'Disetujui' : ($thisMonthRequest->status === 'pending' ? 'Menunggu Tindakan' : $thisMonthRequest->status) }}
                                </span>.
                            </p>
                            <p class="text-xs text-sky-200/80 mt-2">
                                Kuota Oplas gratis berikutnya akan terbuka secara otomatis pada <strong class="text-white">{{ $nextAvailableDate }}</strong>.
                            </p>
                        </div>
                    @endif
                </div>

                {{-- Ketentuan Roleplay --}}
                <div class="rounded-2xl bg-white/[0.04] border border-white/10 p-6 backdrop-blur-md">
                    <h4 class="font-bold text-white text-sm flex items-center gap-2 mb-3">
                        <i class="fas fa-clipboard-list text-cyan-400"></i> Ketentuan &amp; SOP Penggunaan
                    </h4>
                    <ul class="text-xs text-sky-100/80 space-y-2 list-disc list-inside leading-relaxed">
                        <li>Fasilitas ini khusus untuk anggota aktif Medic IME Roleplay (Gratis 100%).</li>
                        <li>Sistem otomatis memberikan kuota 1x setiap 1 bulan kalender (auto reset tanggal 1).</li>
                        <li>Setelah menekan tombol klaim, silakan hubungi dokter spesialis / dokter bedah yang sedang bertugas in-game untuk jadwal tindakan.</li>
                        <li>Dokter yang bertugas akan melihat tanda khusus <strong>[BENEFIT EMS - GRATIS]</strong> pada formulir Anda.</li>
                    </ul>
                </div>
            </div>

            {{-- Sidebar Riwayat Pengajuan --}}
            <div class="space-y-6">
                <div class="rounded-2xl bg-white/[0.05] border border-white/10 p-6 backdrop-blur-md shadow-xl">
                    <h3 class="text-base font-bold text-white flex items-center gap-2 mb-4 pb-3 border-b border-white/10">
                        <i class="fas fa-history text-cyan-400"></i> Riwayat Klaim Oplas Anda
                    </h3>

                    @if($history->isEmpty())
                        <div class="text-center py-8 text-sky-200/60 text-xs">
                            <i class="fas fa-receipt text-3xl mb-2 opacity-40"></i>
                            <p>Belum ada riwayat pengajuan oplas.</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($history as $item)
                                <div class="p-3.5 rounded-xl bg-white/[0.04] border border-white/10 text-xs hover:border-white/25 transition-all">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="font-bold text-white">
                                            {{ $item->form_data['bulan_klaim'] ?? $item->created_at->translatedFormat('F Y') }}
                                        </span>
                                        @if($item->status === 'approved')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                                Disetujui
                                            </span>
                                        @elseif($item->status === 'pending')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                                Pending
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                                {{ ucfirst($item->status) }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-sky-200/70 text-[11px]">
                                        Diajukan: {{ $item->created_at->format('d/m/Y H:i') }}
                                    </div>
                                    @if($item->processedBy)
                                        <div class="text-sky-200/70 text-[11px] mt-0.5">
                                            Dokter: <span class="text-white font-medium">{{ $item->processedBy->name }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
