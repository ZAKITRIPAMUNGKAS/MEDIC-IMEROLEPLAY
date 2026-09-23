@extends('layouts.app')

@section('title', 'Pendaftaran Berhasil - Paramedic IME Medical Center')

@section('content')
<div class="min-h-screen bg-[#f0ede6] py-10 px-4 sm:px-6 flex items-center justify-center">
    <div class="max-w-2xl w-full space-y-6">

        <!-- SUCCESS CARD REPLICATING GOOGLE FORM SCREENSHOT 5 -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-200">
            <!-- Header Bronze / Gold Bar -->
            <div class="bg-[#9c834a] px-6 py-4 flex items-center gap-2.5 text-white">
                <span class="text-xl">📋</span>
                <h1 class="text-base sm:text-lg font-black uppercase tracking-wider">
                    TERIMA KASIH 🙏
                </h1>
            </div>

            <!-- Content Body -->
            <div class="p-6 sm:p-8 space-y-6">
                <div class="space-y-3 text-slate-700 leading-relaxed text-sm sm:text-base">
                    <p class="font-medium text-slate-800">
                        Formulir pendaftaran anda sudah berhasil dikirim ke <span class="font-bold text-[#9c834a]">Industrial & Employee Relation (IE)</span>.
                    </p>
                    <p class="text-slate-600">
                        Pengumuman selanjutnya mengenai hasil seleksi berkas dan jadwal wawancara akan diberitahukan melalui website dan saluran resmi Discord.
                    </p>
                    <p class="italic text-xs sm:text-sm text-slate-500 pt-2 border-t border-slate-100">
                        Terima kasih sudah berpartisipasi mengikuti proses recruitment IME Medical Center!
                    </p>
                </div>

                <!-- Registration Summary Badge -->
                <div class="bg-amber-50/70 border border-amber-200/80 rounded-xl p-4 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-amber-900">
                    <div>
                        <span class="font-bold block text-sm text-amber-950">{{ $application->ic_name }}</span>
                        <span>CID: #{{ $application->cid }} &bull; Terkirim: {{ $application->created_at?->translatedFormat('d F Y, H:i') ?? now()->translatedFormat('d F Y, H:i') }} WIB</span>
                    </div>
                    <span class="px-3 py-1 bg-amber-500 text-white font-bold rounded-full text-[11px] uppercase tracking-wide">
                        Berkas Terkirim
                    </span>
                </div>

                <!-- Official Circular Emblem -->
                <div class="flex justify-center pt-2">
                    <div class="relative max-w-xs w-full overflow-hidden rounded-2xl shadow-2xl border-4 border-[#9c834a]/30">
                        <img src="{{ asset('images/recruitment-success-logo.png') }}" 
                             alt="IME Medical Center - Where Hope Meets Healing" 
                             class="w-full h-auto object-cover block mx-auto">
                    </div>
                </div>

                <!-- Navigation Action -->
                <div class="pt-4 flex flex-wrap items-center justify-center gap-3">
                    <a href="{{ route('public.recruitment.status', ['cid' => $application->cid]) }}" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#9c834a] hover:bg-[#87703d] text-white font-bold text-xs sm:text-sm transition shadow-md">
                        <i class="fas fa-search"></i> Pantau Status Pendaftaran Anda
                    </a>
                    <a href="{{ route('public.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs sm:text-sm transition shadow-md">
                        <i class="fas fa-home"></i> Kembali ke Beranda Utama
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
