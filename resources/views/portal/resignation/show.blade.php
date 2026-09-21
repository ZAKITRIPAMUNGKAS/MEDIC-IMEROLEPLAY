@extends('layouts.app')

@section('title', 'Detail Surat Pengajuan Resign — Portal Alta Hospital')

@section('content')
<div class="min-h-screen pt-20 pb-10" style="background: linear-gradient(135deg, #0b1329 0%, #0c2461 50%, #0b1329 100%);">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">

        {{-- Top Bar --}}
        <div class="flex items-center justify-between mb-6">
            <a href="{{ route('portal.resignation.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-white/10 hover:bg-white/20 text-white rounded-xl text-xs font-semibold border border-white/10 transition-all">
                <i class="fas fa-arrow-left"></i> Kembali ke Riwayat
            </a>
            <div>
                @if($resignation->status === 'completed')
                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 rounded-full text-xs font-semibold">
                        <i class="fas fa-check-circle"></i> Selesai (Resign Disetujui)
                    </span>
                @elseif($resignation->status === 'rejected')
                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-rose-500/20 text-rose-300 border border-rose-500/30 rounded-full text-xs font-semibold">
                        <i class="fas fa-times-circle"></i> Ditolak
                    </span>
                @elseif($resignation->status === 'cancelled')
                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-gray-500/20 text-gray-300 border border-gray-500/30 rounded-full text-xs font-semibold">
                        <i class="fas fa-ban"></i> Dibatalkan (Resign Batal)
                    </span>
                @elseif($resignation->status === 'pending_pnd')
                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-amber-500/20 text-amber-300 border border-amber-500/30 rounded-full text-xs font-semibold">
                        <i class="fas fa-clock"></i> Tahap 1: Verifikasi PND
                    </span>
                @elseif($resignation->status === 'pending_ie' || $resignation->status === 'approved_pnd')
                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-sky-500/20 text-sky-300 border border-sky-500/30 rounded-full text-xs font-semibold">
                        <i class="fas fa-hand-holding-usd"></i> Tahap 2: Verifikasi IE (Denda)
                    </span>
                @endif
            </div>
        </div>

        {{-- Surat Preview Document --}}
        <div class="bg-white text-gray-900 rounded-2xl shadow-2xl p-8 sm:p-12 border border-gray-200">
            <div class="border-b-2 border-gray-800 pb-4 mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold tracking-tight text-gray-900 uppercase">Alta Hospital Medical Center</h2>
                    <p class="text-xs text-gray-500 font-medium tracking-wider">Surat Permohonan Pengunduran Diri (Resign)</p>
                </div>
                <div class="text-right text-xs text-gray-500">
                    <div>Tanggal Surat: <span class="font-semibold text-gray-800">{{ $resignation->letter_date ? $resignation->letter_date->format('d F Y') : '-' }}</span></div>
                    <div>Nomor Ref: <span class="font-mono font-bold text-gray-700">#RES-{{ str_pad($resignation->id, 5, '0', STR_PAD_LEFT) }}</span></div>
                </div>
            </div>

            <div class="space-y-4 text-sm leading-relaxed text-gray-700">
                <p>Kepada Yth.<br><strong>Direksi & Manajemen Divisi PND / IE</strong><br>Alta Hospital</p>

                <p>Dengan hormat,<br>Saya yang bertanda tangan di bawah ini:</p>

                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-2 text-xs sm:text-sm">
                    <div class="grid grid-cols-3 gap-2">
                        <span class="text-gray-500">Nama Lengkap</span>
                        <span class="col-span-2 font-bold text-gray-900">: {{ $resignation->applicant_name }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <span class="text-gray-500">Jabatan Medis</span>
                        <span class="col-span-2 font-medium text-gray-800">: {{ $resignation->position }}</span>
                    </div>
                    @if($resignation->managerial_position)
                    <div class="grid grid-cols-3 gap-2">
                        <span class="text-gray-500">Jabatan Manajerial</span>
                        <span class="col-span-2 font-medium text-gray-800">: {{ $resignation->managerial_position }}</span>
                    </div>
                    @endif
                    @if($resignation->batch)
                    <div class="grid grid-cols-3 gap-2">
                        <span class="text-gray-500">Angkatan / Batch</span>
                        <span class="col-span-2 font-medium text-gray-800">: {{ $resignation->batch }}</span>
                    </div>
                    @endif
                </div>

                <div>
                    <h4 class="font-bold text-gray-900 mb-1">Alasan Pengunduran Diri:</h4>
                    <div class="space-y-2 text-xs sm:text-sm">
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                            <span class="font-semibold text-gray-700 block mb-0.5">Alasan IC (In-Character):</span>
                            <p class="text-gray-600 whitespace-pre-line">{{ $resignation->reason_ic }}</p>
                        </div>
                        @if($resignation->reason_ooc)
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                            <span class="font-semibold text-gray-700 block mb-0.5">Alasan OOC (Out of Character):</span>
                            <p class="text-gray-600 whitespace-pre-line">{{ $resignation->reason_ooc }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                @if($resignation->standard_text)
                <div class="text-xs text-gray-600 italic bg-gray-50 p-3 rounded-lg border border-gray-100">
                    {{ $resignation->standard_text }}
                </div>
                @endif
            </div>

            {{-- Audit Trail Approval --}}
            <div class="mt-8 pt-6 border-t border-gray-200 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div class="p-3 rounded-xl {{ $resignation->pndApprovedBy ? 'bg-emerald-50 border border-emerald-200' : 'bg-gray-50 border border-gray-200' }}">
                    <div class="font-bold {{ $resignation->pndApprovedBy ? 'text-emerald-800' : 'text-gray-600' }} flex items-center gap-1.5 mb-1">
                        <i class="fas {{ $resignation->pndApprovedBy ? 'fa-check-circle text-emerald-600' : 'fa-clock text-gray-400' }}"></i>
                        Tahap 1: Verifikasi PND
                    </div>
                    @if($resignation->pndApprovedBy)
                        <div class="text-gray-700">Diverifikasi oleh: <strong>{{ $resignation->pndApprovedBy->name }}</strong></div>
                        <div class="text-gray-500 text-[11px]">{{ $resignation->pnd_approved_at ? $resignation->pnd_approved_at->format('d M Y H:i') : '-' }}</div>
                        @if($resignation->pnd_notes)
                            <div class="mt-1 text-gray-600 italic">"{{ $resignation->pnd_notes }}"</div>
                        @endif
                    @else
                        <div class="text-gray-400 italic">Menunggu verifikasi PND</div>
                    @endif
                </div>

                <div class="p-3 rounded-xl {{ $resignation->ieVerifiedBy ? 'bg-sky-50 border border-sky-200' : 'bg-gray-50 border border-gray-200' }}">
                    <div class="font-bold {{ $resignation->ieVerifiedBy ? 'text-sky-800' : 'text-gray-600' }} flex items-center gap-1.5 mb-1">
                        <i class="fas {{ $resignation->ieVerifiedBy ? 'fa-check-circle text-sky-600' : 'fa-clock text-gray-400' }}"></i>
                        Tahap 2: Denda & Verifikasi IE
                    </div>
                    @if($resignation->ieVerifiedBy)
                        <div class="text-gray-700">Diverifikasi oleh: <strong>{{ $resignation->ieVerifiedBy->name }}</strong></div>
                        <div class="text-gray-500 text-[11px]">{{ $resignation->ie_verified_at ? $resignation->ie_verified_at->format('d M Y H:i') : '-' }}</div>
                        @if($resignation->fine_amount)
                            <div class="mt-1 font-semibold text-rose-700">Denda: ${{ number_format($resignation->fine_amount, 0, ',', '.') }} ({{ $resignation->fine_percentage }}% dari total gaji pokok ${{ number_format($resignation->base_salary, 0, ',', '.') }})</div>
                            <div class="text-[11px] {{ $resignation->fine_paid ? 'text-emerald-700 font-bold' : 'text-amber-700' }}">
                                Status Pembayaran: {{ $resignation->fine_paid ? 'Sudah Lunas' : 'Belum Lunas' }}
                            </div>
                        @endif
                        @if($resignation->ie_notes)
                            <div class="mt-1 text-gray-600 italic">"{{ $resignation->ie_notes }}"</div>
                        @endif
                    @else
                        <div class="text-gray-400 italic">Menunggu penetapan denda IE</div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
