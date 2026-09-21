@extends('layouts.app')

@section('title', 'Pengajuan Cuti Saya — Portal Alta Hospital')

@section('content')
<div class="min-h-screen pt-20 pb-10" style="background: linear-gradient(135deg, #0b1329 0%, #0c2461 50%, #0b1329 100%);">
    <div class="max-w-5xl mx-auto px-4">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                    <i class="fas fa-calendar-check text-rose-400"></i> Pengajuan Cuti
                </h1>
                <p class="text-white/60 text-sm mt-0.5">Kelola permohonan cuti Anda</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('portal.leave.public-list') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/15 text-rose-300 hover:text-white text-sm font-semibold rounded-xl border border-rose-500/30 transition-all duration-200">
                    <i class="fas fa-users"></i> Lihat Jadwal Cuti Medis
                </a>
                @if($activeLeave)
                    <button type="button" disabled
                            title="Masa cuti Anda masih aktif sampai {{ \Carbon\Carbon::parse($activeLeave->end_date)->format('d M Y') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600/40 border border-white/10 text-white/40 text-sm font-semibold rounded-xl cursor-not-allowed shadow-none">
                        <i class="fas fa-lock"></i> Masa Cuti Aktif
                    </button>
                @else
                    <a href="{{ route('portal.leave.create') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-rose-500 to-pink-600 hover:from-rose-400 hover:to-pink-500 text-white text-sm font-semibold rounded-xl shadow-lg shadow-rose-900/30 transition-all duration-200">
                        <i class="fas fa-plus"></i> Ajukan Cuti Baru
                    </a>
                @endif
            </div>
        </div>

        {{-- Flash messages --}}
        @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-500/20 border border-emerald-500/40 rounded-xl text-emerald-300 text-sm flex items-center gap-2">
            <i class="fas fa-check-circle text-emerald-400"></i> {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mb-4 p-4 bg-rose-500/20 border border-rose-500/40 rounded-xl text-rose-300 text-sm flex items-center gap-2">
            <i class="fas fa-exclamation-circle text-rose-400"></i> {{ session('error') }}
        </div>
        @endif

        {{-- Active Leave Info Banner --}}
        @if($activeLeave)
        <div class="mb-5 p-4 bg-amber-500/20 border border-amber-500/40 rounded-2xl text-amber-200 text-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-xl">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500/30 border border-amber-500/50 flex items-center justify-center text-amber-300 text-lg flex-shrink-0">
                    <i class="fas fa-umbrella-beach"></i>
                </div>
                <div>
                    <div class="font-bold text-white flex items-center gap-2">
                        <span>Masa Cuti Anda Masih Aktif</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-400/20 text-amber-300 border border-amber-400/40 uppercase">Aktif</span>
                    </div>
                    <p class="text-xs text-amber-200/80 mt-0.5">
                        Periode: <strong>{{ \Carbon\Carbon::parse($activeLeave->start_date)->locale('id')->translatedFormat('d F Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($activeLeave->end_date)->locale('id')->translatedFormat('d F Y') }}</strong> ({{ $activeLeave->duration_days }} hari).
                        Pengajuan cuti baru tidak dapat dikirim sampai masa cuti selesai.
                    </p>
                </div>
            </div>
        </div>
        @endif

        {{-- Tabel daftar pengajuan --}}
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden shadow-2xl">
            @if($requests->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-white/40">
                <i class="fas fa-calendar-times text-4xl mb-3"></i>
                <p class="text-sm">Belum ada pengajuan cuti.</p>
                @if(!$activeLeave)
                <a href="{{ route('portal.leave.create') }}" class="mt-4 text-rose-400 hover:text-rose-300 text-sm underline underline-offset-2">Ajukan sekarang</a>
                @endif
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-white/10 text-xs text-white/50 uppercase tracking-wider">
                            <th class="text-left px-5 py-3 font-semibold">Tanggal Surat</th>
                            <th class="text-left px-5 py-3 font-semibold">Periode Cuti</th>
                            <th class="text-left px-5 py-3 font-semibold">Durasi</th>
                            <th class="text-left px-5 py-3 font-semibold">Status</th>
                            <th class="text-left px-5 py-3 font-semibold">Disetujui oleh</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($requests as $req)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-5 py-3.5 text-white/80">{{ $req->letter_date?->format('d M Y') ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-white/80">
                                {{ $req->start_date?->format('d M Y') }} — {{ $req->end_date?->format('d M Y') }}
                            </td>
                            <td class="px-5 py-3.5 text-white/70">{{ $req->duration_days }} hari</td>
                            <td class="px-5 py-3.5">
                                @php
                                    $color = ['pending'=>'yellow','approved'=>'green','rejected'=>'red'][$req->status] ?? 'gray';
                                @endphp
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold
                                    {{ $color === 'yellow' ? 'bg-yellow-500/20 text-yellow-300 border border-yellow-500/30' :
                                      ($color === 'green'  ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' :
                                       'bg-red-500/20 text-red-300 border border-red-500/30') }}">
                                    {{ $req->status_label }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-white/60">{{ $req->approvedBy?->name ?? ($req->status === 'approved' ? 'Sistem (Otomatis)' : '—') }}</td>
                            <td class="px-5 py-3.5">
                                <a href="{{ route('portal.leave.show', $req) }}"
                                   class="text-sky-400 hover:text-sky-300 text-xs font-medium transition-colors">
                                    Detail <i class="fas fa-arrow-right text-[10px]"></i>
                                </a>
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
@endsection
