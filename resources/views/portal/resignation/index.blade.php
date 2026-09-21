@extends('layouts.app')

@section('title', 'Pengajuan Resign — Portal Alta Hospital')

@section('content')
<div class="min-h-screen pt-20 pb-10" style="background: linear-gradient(135deg, #0b1329 0%, #0c2461 50%, #0b1329 100%);">
    <div class="max-w-3xl mx-auto px-4">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                    <i class="fas fa-file-signature text-orange-400"></i> Pengajuan Resign
                </h1>
                <p class="text-white/50 text-sm mt-0.5">Status permohonan pengunduran diri Anda</p>
            </div>
            @if(!$request || in_array($request?->status, ['completed', 'rejected', 'cancelled']))
            <a href="{{ route('portal.resignation.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-400 hover:to-red-500 text-white text-sm font-semibold rounded-xl shadow-lg shadow-red-900/30 transition-all duration-200">
                <i class="fas fa-plus"></i> Ajukan Resign
            </a>
            @endif
        </div>

        @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-500/20 border border-emerald-500/40 rounded-xl text-emerald-300 text-sm flex items-center gap-2">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif
        @if(session('info'))
        <div class="mb-4 p-4 bg-sky-500/20 border border-sky-500/40 rounded-xl text-sky-300 text-sm flex items-center gap-2">
            <i class="fas fa-info-circle"></i> {{ session('info') }}
        </div>
        @endif

        @if($request)
        {{-- Progress Flow --}}
        <div class="mb-6 bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-5">
            <h3 class="text-white/70 text-xs uppercase tracking-wider font-semibold mb-4">Alur Proses Resign</h3>
            <div class="flex items-center gap-2 text-xs">
                @php
                    $steps = [
                        ['label'=>'Submit', 'icon'=>'fa-paper-plane', 'done'=>true],
                        ['label'=>'Verifikasi PND', 'icon'=>'fa-user-check', 'done'=>in_array($request->status, ['approved_pnd','pending_ie','completed'])],
                        ['label'=>'Kalkulasi Denda IE', 'icon'=>'fa-calculator', 'done'=>$request->status === 'completed'],
                        ['label'=>'Selesai', 'icon'=>'fa-flag-checkered', 'done'=>$request->status === 'completed'],
                    ];
                @endphp
                @foreach($steps as $i => $step)
                <div class="flex items-center {{ $i > 0 ? 'flex-1' : '' }}">
                    @if($i > 0)
                    <div class="flex-1 h-px {{ $step['done'] ? 'bg-emerald-400' : 'bg-white/20' }} mx-1"></div>
                    @endif
                    <div class="flex flex-col items-center gap-1">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $step['done'] ? 'bg-emerald-500/30 border border-emerald-400 text-emerald-300' : 'bg-white/10 border border-white/20 text-white/30' }}">
                            <i class="fas {{ $step['icon'] }} text-xs"></i>
                        </div>
                        <span class="{{ $step['done'] ? 'text-emerald-300' : 'text-white/30' }} whitespace-nowrap">{{ $step['label'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Detail Card --}}
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden shadow-2xl">
            @php $color = ['pending_pnd'=>'yellow','approved_pnd'=>'blue','pending_ie'=>'orange','completed'=>'green','rejected'=>'red','cancelled'=>'gray'][$request->status] ?? 'gray'; @endphp
            <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between bg-white/3">
                <span class="text-white font-semibold">{{ $request->status_label }}</span>
                @if($request->status === 'completed')
                <span class="text-xs text-emerald-300 font-medium">✅ Akun dinonaktifkan setelah pelunasan denda</span>
                @elseif($request->status === 'pending_pnd')
                <form method="POST" action="{{ route('portal.resignation.cancel-own') }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan resign ini?');" class="inline">
                    @csrf
                    <button type="submit" class="text-xs text-rose-300 hover:text-rose-200 bg-rose-500/20 hover:bg-rose-500/30 px-3 py-1.5 rounded-lg border border-rose-500/30 transition-all flex items-center gap-1.5 font-semibold">
                        <i class="fas fa-times-circle"></i> Batalkan Pengajuan Saya
                    </button>
                </form>
                @elseif($request->status === 'cancelled')
                <span class="text-xs text-amber-300 font-medium">⚠️ Pengajuan resign telah dibatalkan</span>
                @endif
            </div>
            <div class="p-6 space-y-4 text-sm">
                <div class="grid grid-cols-2 gap-4">
                    <div><span class="text-white/40 text-xs uppercase block mb-0.5">Nama</span><span class="text-white font-semibold">{{ $request->applicant_name }}</span></div>
                    <div><span class="text-white/40 text-xs uppercase block mb-0.5">Jabatan</span><span class="text-white/80">{{ $request->position }}</span></div>
                    <div><span class="text-white/40 text-xs uppercase block mb-0.5">Tanggal Surat</span><span class="text-white/80">{{ $request->letter_date?->format('d M Y') }}</span></div>
                    <div><span class="text-white/40 text-xs uppercase block mb-0.5">Batch</span><span class="text-white/80">{{ $request->batch ?? '—' }}</span></div>
                </div>
                <div class="p-4 bg-white/5 rounded-xl border border-white/10">
                    <p class="text-xs text-white/40 uppercase tracking-wider mb-1">Alasan IC</p>
                    <p class="text-white/80">{{ $request->reason_ic }}</p>
                </div>
                <div class="p-4 bg-white/5 rounded-xl border border-white/10">
                    <p class="text-xs text-white/40 uppercase tracking-wider mb-1">Alasan OOC</p>
                    <p class="text-white/80">{{ $request->reason_ooc }}</p>
                </div>

                {{-- Info Denda --}}
                @if($request->status !== 'pending_pnd')
                <div class="p-4 bg-orange-500/10 rounded-xl border border-orange-500/20">
                    <p class="text-xs text-orange-300 uppercase tracking-wider mb-2 font-semibold">Kalkulasi Denda Resign</p>
                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div>
                            <p class="text-white/40 text-xs mb-0.5">Total Gaji Pokok (Tanpa Bonus)</p>
                            <p class="text-white font-bold">$ {{ number_format($request->base_salary, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-white/40 text-xs mb-0.5">Persentase Denda</p>
                            <p class="text-orange-300 font-bold">{{ $request->fine_percentage }}%</p>
                        </div>
                        <div>
                            <p class="text-white/40 text-xs mb-0.5">Jumlah Denda</p>
                            <p class="text-orange-300 font-bold">$ {{ number_format($request->fine_amount, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <div class="mt-2 text-center">
                        <span class="text-xs {{ $request->fine_paid ? 'text-emerald-300' : 'text-red-300' }}">
                            {{ $request->fine_paid ? '✅ Denda telah dibayar' : '⏳ Menunggu pelunasan denda' }}
                        </span>
                    </div>
                </div>
                @endif

                @if($request->pnd_notes)
                <div class="p-3 bg-blue-500/10 rounded-xl border border-blue-500/20 text-xs text-blue-200">
                    <span class="font-semibold">Catatan PND:</span> {{ $request->pnd_notes }}
                </div>
                @endif
                @if($request->ie_notes)
                <div class="p-3 bg-orange-500/10 rounded-xl border border-orange-500/20 text-xs text-orange-200">
                    <span class="font-semibold">Catatan IE:</span> {{ $request->ie_notes }}
                </div>
                @endif
            </div>
        </div>

        @else
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-12 text-center">
            <i class="fas fa-file-signature text-4xl text-orange-400/50 mb-4"></i>
            <p class="text-white/50 text-sm">Belum ada pengajuan resign aktif.</p>
            <a href="{{ route('portal.resignation.create') }}"
               class="mt-4 inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-orange-500 to-red-600 text-white text-sm font-semibold rounded-xl hover:from-orange-400 hover:to-red-500 transition-all">
                <i class="fas fa-plus"></i> Ajukan Resign Sekarang
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
