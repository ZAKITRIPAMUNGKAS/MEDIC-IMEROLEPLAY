@extends('layouts.app')

@section('title', 'Detail Pengajuan Cuti — Portal Alta Hospital')

@section('content')
<div class="min-h-screen pt-20 pb-10" style="background: linear-gradient(135deg, #0b1329 0%, #0c2461 50%, #0b1329 100%);">
    <div class="max-w-3xl mx-auto px-4">

        <div class="mb-6">
            <a href="{{ route('portal.leave.index') }}" class="text-white/50 hover:text-white text-sm flex items-center gap-1.5 mb-3 transition-colors">
                <i class="fas fa-arrow-left text-xs"></i> Kembali ke Daftar Cuti
            </a>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-calendar-check text-rose-400"></i> Detail Pengajuan Cuti
            </h1>
        </div>

        @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-500/20 border border-emerald-500/40 rounded-xl text-emerald-300 text-sm flex items-center gap-2">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif

        {{-- Detail Card --}}
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden shadow-2xl">
            {{-- Status Banner --}}
            @php $color = ['pending'=>'yellow','approved'=>'green','rejected'=>'red'][$leave->status] ?? 'gray'; @endphp
            <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between
                {{ $color==='yellow' ? 'bg-yellow-500/10' : ($color==='green' ? 'bg-emerald-500/10' : 'bg-red-500/10') }}">
                <span class="font-semibold text-white">Status Pengajuan</span>
                <span class="px-3 py-1 rounded-full text-sm font-bold
                    {{ $color==='yellow' ? 'bg-yellow-500/20 text-yellow-300 border border-yellow-500/30' :
                      ($color==='green'  ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' :
                       'bg-red-500/20 text-red-300 border border-red-500/30') }}">
                    {{ $leave->status_label }}
                </span>
            </div>

            <div class="p-6 space-y-5">
                {{-- Info Pemohon --}}
                <div class="grid grid-cols-2 gap-4 pb-4 border-b border-white/10 text-sm">
                    <div><span class="text-white/40 block text-xs uppercase tracking-wider mb-0.5">Nama</span><span class="text-white font-semibold">{{ $leave->applicant_name }}</span></div>
                    <div><span class="text-white/40 block text-xs uppercase tracking-wider mb-0.5">Jabatan</span><span class="text-white/80">{{ $leave->position }}</span></div>
                    <div><span class="text-white/40 block text-xs uppercase tracking-wider mb-0.5">Tanggal Surat</span><span class="text-white/80">{{ $leave->letter_date?->format('d M Y') }}</span></div>
                    <div><span class="text-white/40 block text-xs uppercase tracking-wider mb-0.5">Durasi</span><span class="text-white/80">{{ $leave->duration_days }} hari</span></div>
                </div>

                {{-- Periode Cuti --}}
                <div class="flex items-center gap-4 p-4 bg-white/5 rounded-xl border border-white/10">
                    <div class="text-center">
                        <p class="text-xs text-white/40 mb-0.5">Mulai</p>
                        <p class="text-white font-bold">{{ $leave->start_date?->format('d M Y') }}</p>
                    </div>
                    <div class="flex-1 flex items-center gap-2">
                        <div class="flex-1 h-px bg-gradient-to-r from-rose-400/50 to-pink-400/50"></div>
                        <i class="fas fa-clock text-rose-400 text-xs"></i>
                        <div class="flex-1 h-px bg-gradient-to-l from-rose-400/50 to-pink-400/50"></div>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-white/40 mb-0.5">Selesai</p>
                        <p class="text-white font-bold">{{ $leave->end_date?->format('d M Y') }}</p>
                    </div>
                </div>

                {{-- Alasan --}}
                <div class="grid grid-cols-1 gap-4 text-sm">
                    <div class="p-4 bg-white/5 rounded-xl border border-white/10">
                        <p class="text-xs text-white/40 uppercase tracking-wider mb-1.5">Alasan IC</p>
                        <p class="text-white/80 leading-relaxed">{{ $leave->reason_ic }}</p>
                    </div>
                    <div class="p-4 bg-white/5 rounded-xl border border-white/10">
                        <p class="text-xs text-white/40 uppercase tracking-wider mb-1.5">Alasan OOC</p>
                        <p class="text-white/80 leading-relaxed">{{ $leave->reason_ooc }}</p>
                    </div>
                    @if($leave->notes)
                    <div class="p-4 bg-amber-500/10 rounded-xl border border-amber-500/20">
                        <p class="text-xs text-amber-300 uppercase tracking-wider mb-1.5">Catatan dari Approver</p>
                        <p class="text-white/80">{{ $leave->notes }}</p>
                    </div>
                    @endif
                </div>

                @if($leave->approvedBy || $leave->status === 'approved')
                <div class="text-xs text-white/40 pt-2 border-t border-white/10">
                    Diproses oleh <span class="text-white/60 font-medium">{{ $leave->approvedBy?->name ?? 'Sistem (Otomatis Disetujui)' }}</span>
                    pada {{ $leave->approved_at?->format('d M Y H:i') }}
                </div>
                @endif
            </div>

            {{-- Approve / Reject (Manager ke atas) --}}
            @if(auth()->user()->isManagerOrAbove() && $leave->status === 'pending')
            <div class="px-6 py-4 border-t border-white/10 bg-white/3 flex gap-3">
                <form method="POST" action="{{ route('portal.leave.approve', $leave) }}" class="flex-1">
                    @csrf
                    <button type="submit"
                            class="w-full py-2.5 bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 font-semibold rounded-xl border border-emerald-500/30 transition-all text-sm">
                        <i class="fas fa-check mr-1.5"></i> Setujui
                    </button>
                </form>
                <form method="POST" action="{{ route('portal.leave.reject', $leave) }}" class="flex-1 flex gap-2">
                    @csrf
                    <input type="text" name="notes" placeholder="Alasan penolakan (opsional)"
                           class="flex-1 px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-xs placeholder-white/30 focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
                    <button type="submit"
                            class="px-4 py-2.5 bg-red-500/20 hover:bg-red-500/30 text-red-300 font-semibold rounded-xl border border-red-500/30 transition-all text-sm">
                        <i class="fas fa-times mr-1"></i> Tolak
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
