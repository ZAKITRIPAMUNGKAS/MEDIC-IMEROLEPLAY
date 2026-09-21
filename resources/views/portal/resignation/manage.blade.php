@extends('layouts.app')

@section('title', '{{ $stage === "pnd" ? "Verifikasi PND" : "Denda IE" }} — Resign Portal')

@section('content')
<div class="min-h-screen pt-20 pb-10" style="background: linear-gradient(135deg, #0b1329 0%, #0c2461 50%, #0b1329 100%);">
    <div class="max-w-6xl mx-auto px-4">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                    @if($stage === 'pnd')
                    <i class="fas fa-user-check text-orange-400"></i> Verifikasi Resign — Divisi PND
                    @else
                    <i class="fas fa-calculator text-sky-400"></i> Denda Resign — Divisi IE
                    @endif
                </h1>
                <p class="text-white/50 text-sm mt-0.5">
                    @if($stage === 'pnd') Setujui atau tolak pengajuan resign sebelum diteruskan ke IE @else Verifikasi pelunasan denda untuk menyelesaikan proses resign @endif
                </p>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-500/20 border border-emerald-500/40 rounded-xl text-emerald-300 text-sm flex items-center gap-2">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif

        {{-- Filter --}}
        <form method="GET" class="mb-4 flex gap-2">
            <select name="status" class="px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-sky-400">
                <option value="">Semua Status</option>
                @if($stage === 'pnd')
                <option value="pending_pnd" {{ request('status') === 'pending_pnd' ? 'selected' : '' }}>Menunggu PND</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                @else
                <option value="pending_ie" {{ request('status') === 'pending_ie' ? 'selected' : '' }}>Menunggu IE</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                @endif
            </select>
            <button type="submit" class="px-4 py-2 bg-sky-500/20 hover:bg-sky-500/30 text-sky-300 rounded-xl border border-sky-500/30 text-sm transition-all">
                <i class="fas fa-filter mr-1"></i> Filter
            </button>
        </form>

        {{-- Tabel --}}
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden shadow-2xl">
            @if($requests->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-white/40">
                <i class="fas fa-inbox text-4xl mb-3"></i>
                <p class="text-sm">Tidak ada pengajuan resign yang perlu ditinjau.</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-white/10 text-xs text-white/50 uppercase tracking-wider">
                        <tr>
                            <th class="text-left px-5 py-3">Nama Anggota</th>
                            <th class="text-left px-5 py-3">Jabatan</th>
                            <th class="text-left px-5 py-3">Tanggal Surat</th>
                            <th class="text-left px-5 py-3">Status</th>
                            @if($stage === 'ie')
                            <th class="text-left px-5 py-3">Denda</th>
                            @endif
                            <th class="px-5 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($requests as $req)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="text-white font-medium">{{ $req->applicant_name }}</div>
                                <div class="text-white/40 text-xs">{{ $req->user?->staff_id }}</div>
                            </td>
                            <td class="px-5 py-3.5 text-white/70">{{ $req->position }}</td>
                            <td class="px-5 py-3.5 text-white/70">{{ $req->letter_date?->format('d M Y') }}</td>
                            <td class="px-5 py-3.5">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                    @if($req->status === 'pending_pnd') bg-yellow-500/20 text-yellow-300 border border-yellow-500/30
                                    @elseif($req->status === 'pending_ie') bg-orange-500/20 text-orange-300 border border-orange-500/30
                                    @elseif($req->status === 'completed') bg-emerald-500/20 text-emerald-300 border border-emerald-500/30
                                    @else bg-red-500/20 text-red-300 border border-red-500/30 @endif">
                                    {{ $req->status_label }}
                                </span>
                            </td>
                            @if($stage === 'ie')
                            <td class="px-5 py-3.5">
                                <div class="text-orange-300 font-semibold">Rp {{ number_format($req->fine_amount, 0, ',', '.') }}</div>
                                <div class="text-white/40 text-xs">{{ $req->fine_percentage }}% dari gaji</div>
                            </td>
                            @endif
                            <td class="px-5 py-3.5">
                                {{-- PND Approve/Reject --}}
                                @if($stage === 'pnd' && $req->status === 'pending_pnd')
                                <div class="flex gap-2">
                                    <form method="POST" action="{{ route('portal.resignation.pnd-approve', $req) }}">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 text-xs font-semibold rounded-lg border border-emerald-500/30 transition-all">
                                            <i class="fas fa-check"></i> Setujui
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('portal.resignation.pnd-reject', $req) }}"
                                          onsubmit="return prompt('Alasan penolakan:', '') !== null"
                                          class="flex gap-1">
                                        @csrf
                                        <input type="hidden" name="pnd_notes" id="note_{{ $req->id }}">
                                        <button type="button"
                                                onclick="
                                                    var n = prompt('Alasan penolakan:');
                                                    if(n !== null) { document.getElementById('note_{{ $req->id }}').value = n; this.form.submit(); }
                                                "
                                                class="px-3 py-1.5 bg-red-500/20 hover:bg-red-500/30 text-red-300 text-xs font-semibold rounded-lg border border-red-500/30 transition-all">
                                            <i class="fas fa-times"></i> Tolak
                                        </button>
                                    </form>
                                </div>
                                @elseif($stage === 'ie' && $req->status === 'pending_ie')
                                <form method="POST" action="{{ route('portal.resignation.ie-verify', $req) }}">
                                    @csrf
                                    <button type="submit"
                                            onclick="return confirm('Konfirmasi denda sudah dilunasi? Akun {{ addslashes($req->applicant_name) }} akan dinonaktifkan.')"
                                            class="px-3 py-1.5 bg-sky-500/20 hover:bg-sky-500/30 text-sky-300 text-xs font-semibold rounded-lg border border-sky-500/30 transition-all">
                                        <i class="fas fa-check-double"></i> Lunas & Nonaktifkan
                                    </button>
                                </form>
                                @else
                                <span class="text-white/30 text-xs">—</span>
                                @endif
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
