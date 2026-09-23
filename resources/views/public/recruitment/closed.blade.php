@extends('layouts.app')

@section('title', 'Pendaftaran Ditutup - Paramedic IME Medical Center')

@section('content')
<div class="min-h-[80vh] bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 py-16 px-4 flex items-center justify-center">
    <div class="max-w-xl w-full text-center space-y-6">
        <!-- Logo / Icon -->
        <div class="inline-flex items-center justify-center w-24 h-24 rounded-3xl bg-amber-500/10 border border-amber-500/30 text-amber-400 shadow-2xl mx-auto animate-bounce">
            <i class="fas fa-door-closed text-4xl"></i>
        </div>

        <div class="space-y-3">
            <span class="inline-block px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-widest bg-amber-500/20 text-amber-300 border border-amber-500/30">
                Informasi Rekrutmen Alta Hospital
            </span>
            <h1 class="text-3xl sm:text-4xl font-black text-white tracking-tight">
                Pendaftaran Paramedic Saat Ini Ditutup
            </h1>
            <p class="text-sm text-slate-300 leading-relaxed max-w-md mx-auto">
                Terima kasih atas antusiasme Anda. Periode pendaftaran calon anggota paramedic IME Medical Center saat ini belum atau sedang ditutup oleh divisi Industrial & Employee Relations (IE) dan PND.
            </p>
        </div>

        <div class="bg-white/5 backdrop-blur-md rounded-2xl p-6 border border-white/10 text-left space-y-3 max-w-md mx-auto">
            <div class="flex items-start gap-3">
                <i class="fas fa-info-circle text-sky-400 text-base mt-0.5"></i>
                <p class="text-xs text-slate-300">
                    Pengumuman pembukaan batch rekrutmen selanjutnya akan selalu diinformasikan secara resmi melalui website ini dan saluran komunitas <strong>Discord IME Roleplay</strong>.
                </p>
            </div>
            <div class="flex items-start gap-3">
                <i class="fas fa-clipboard-check text-emerald-400 text-base mt-0.5"></i>
                <p class="text-xs text-slate-300">
                    Persiapkan berkas IC Anda seperti <strong>KTP, SKB yang masih berlaku, dan Surat Kesehatan</strong> sebelum pendaftaran dibuka kembali.
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
            <a href="{{ route('public.recruitment.status') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 font-bold text-xs transition shadow-lg">
                <i class="fas fa-search"></i> Cek Status Pendaftaran
            </a>
            <a href="{{ route('public.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-white text-slate-900 font-bold text-xs hover:bg-slate-100 transition shadow-lg">
                <i class="fas fa-home"></i> Kembali ke Beranda
            </a>
            @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isExecutiveOrAbove() || auth()->user()->isInDivision('ie') || auth()->user()->isInDivision('pnd')))
            <a href="{{ route('portal.recruitment.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs transition shadow-lg">
                <i class="fas fa-cog"></i> Buka Recruitment (IE / PND)
            </a>
            @endif
        </div>
    </div>
</div>
@endsection
