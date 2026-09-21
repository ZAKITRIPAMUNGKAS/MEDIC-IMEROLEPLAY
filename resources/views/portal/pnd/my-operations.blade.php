@extends('layouts.app')

@section('title', 'Pengajuan Operasi Saya — Portal Alta Hospital')

@section('content')
<div class="min-h-screen pt-20 pb-10" style="background: linear-gradient(135deg, #0b1329 0%, #0c2461 50%, #0b1329 100%);">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400">
                        <i class="fas fa-procedures text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white tracking-wide">Pengajuan Operasi Saya</h1>
                        <p class="text-white/50 text-sm mt-0.5">Daftar riwayat dan status pengajuan operasi Anda di Alta Hospital</p>
                    </div>
                </div>
            </div>
            <a href="{{ route('portal.pnd.operation.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-xl text-sm font-semibold shadow-lg shadow-emerald-900/30 transition-all">
                <i class="fas fa-plus"></i> Buat Pengajuan Operasi
            </a>
        </div>

        @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-500/20 border border-emerald-500/40 rounded-xl text-emerald-300 text-sm flex items-center gap-3">
            <i class="fas fa-check-circle text-lg"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        {{-- Table Card --}}
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden shadow-2xl">
            @if($items->isEmpty())
            <div class="py-16 text-center text-white/40">
                <i class="fas fa-procedures text-5xl mb-3 block opacity-40"></i>
                <p class="text-base font-medium">Anda belum pernah mengajukan operasi.</p>
                <p class="text-xs text-white/30 mt-1">Klik tombol "Buat Pengajuan Operasi" di atas untuk mengirim pengajuan pertama.</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-white/10 text-xs text-white/50 uppercase tracking-wider bg-white/[0.02]">
                        <tr>
                            <th class="text-left px-5 py-3.5">Pasien & Jenis Operasi</th>
                            <th class="text-left px-5 py-3.5">Diagnosis & Tindakan</th>
                            <th class="text-left px-5 py-3.5">Jadwal & DPJP</th>
                            <th class="text-left px-5 py-3.5">Status PND</th>
                            <th class="text-left px-5 py-3.5">Catatan Verifikator</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($items as $item)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-5 py-4">
                                <div class="text-white font-semibold flex items-center gap-2">
                                    <i class="fas fa-user-injured text-emerald-400 text-xs"></i>
                                    {{ $item->patient_name }}
                                </div>
                                <div class="mt-1">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                        {{ $item->jenis_operasi }}
                                    </span>
                                </div>
                                <div class="text-[11px] text-white/40 mt-1">
                                    Diajukan: {{ $item->created_at->format('d M Y H:i') }}
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="text-xs text-white/80">
                                    <span class="text-white/40">Diagnosis:</span> {{ $item->diagnosis }}
                                </div>
                                <div class="text-xs text-white/70 mt-1">
                                    <span class="text-white/40">Tindakan:</span> {{ $item->planned_procedure }}
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="text-xs text-white/90">
                                    <i class="fas fa-calendar-alt text-white/40 mr-1"></i>
                                    {{ $item->scheduled_at ? $item->scheduled_at->format('d M Y H:i') : 'Menyesuaikan' }}
                                </div>
                                <div class="text-xs text-white/70 mt-1">
                                    <i class="fas fa-user-md text-emerald-400 mr-1"></i>
                                    DPJP: {{ $item->dpjp?->name ?? 'Belum ditentukan' }}
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                @if($item->status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span> Pending PND
                                    </span>
                                @elseif($item->status === 'approved')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                        <i class="fas fa-check text-[10px]"></i> Disetujui
                                    </span>
                                    @if($item->verifiedByPnd)
                                        <div class="text-[10px] text-white/40 mt-1">oleh {{ $item->verifiedByPnd->name }}</div>
                                    @endif
                                @elseif($item->status === 'rejected')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                        <i class="fas fa-times text-[10px]"></i> Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-500/20 text-blue-300 border border-blue-500/30">
                                        <i class="fas fa-flag-checkered text-[10px]"></i> {{ ucfirst($item->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if($item->pnd_notes)
                                    <div class="text-xs text-white/70 italic bg-white/5 p-2 rounded-lg border border-white/10 max-w-xs">
                                        "{{ $item->pnd_notes }}"
                                    </div>
                                @else
                                    <span class="text-xs text-white/30 italic">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($items->hasPages())
            <div class="p-4 border-t border-white/10 bg-white/[0.02]">
                {{ $items->links() }}
            </div>
            @endif
            @endif
        </div>

    </div>
</div>
@endsection
