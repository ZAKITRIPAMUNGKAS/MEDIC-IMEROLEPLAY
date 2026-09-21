@extends('layouts.app')

@section('title', 'Sertifikat Kendaraan GA — Portal Alta Hospital')

@section('content')
<div class="min-h-screen pt-20 pb-10" style="background: linear-gradient(135deg, #0b1329 0%, #0c2461 50%, #0b1329 100%);">
    <div class="max-w-6xl mx-auto px-4">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                    <i class="fas fa-car text-amber-400"></i> GA — Sertifikat Kendaraan
                </h1>
                <p class="text-white/50 text-sm mt-0.5">Kelola sertifikasi kendaraan darat dan helikopter</p>
            </div>
            <a href="{{ route('portal.ga.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-amber-500 to-yellow-600 hover:from-amber-400 hover:to-yellow-500 text-white text-sm font-semibold rounded-xl shadow-lg shadow-amber-900/30 transition-all duration-200">
                <i class="fas fa-plus"></i> Terbitkan Sertifikat
            </a>
        </div>

        @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-500/20 border border-emerald-500/40 rounded-xl text-emerald-300 text-sm flex items-center gap-2">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif

        {{-- Filter --}}
        <form method="GET" class="mb-4 flex gap-2 flex-wrap">
            <select name="type" class="px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-amber-400">
                <option value="">Semua Tipe</option>
                <option value="vehicle_land" {{ request('type') === 'vehicle_land' ? 'selected' : '' }}>Kendaraan Darat</option>
                <option value="vehicle_heli" {{ request('type') === 'vehicle_heli' ? 'selected' : '' }}>Kendaraan Heli</option>
            </select>
            <select name="status" class="px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-amber-400">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Kedaluwarsa</option>
                <option value="revoked" {{ request('status') === 'revoked' ? 'selected' : '' }}>Dicabut</option>
            </select>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / ID staf..."
                   class="flex-1 min-w-[180px] px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm placeholder-white/30 focus:outline-none focus:border-amber-400">
            <button type="submit" class="px-4 py-2 bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 rounded-xl border border-amber-500/30 text-sm transition-all">
                <i class="fas fa-search mr-1"></i> Cari
            </button>
        </form>

        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden shadow-2xl">
            @if($certifications->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-white/40">
                <i class="fas fa-car text-4xl mb-3"></i>
                <p class="text-sm">Belum ada sertifikat kendaraan yang diterbitkan.</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-white/10 text-xs text-white/50 uppercase tracking-wider">
                        <tr>
                            <th class="text-left px-5 py-3">Anggota</th>
                            <th class="text-left px-5 py-3">Tipe Sertifikat</th>
                            <th class="text-left px-5 py-3">Judul</th>
                            <th class="text-left px-5 py-3">Tanggal Terbit</th>
                            <th class="text-left px-5 py-3">Status</th>
                            <th class="text-left px-5 py-3">Diterbitkan oleh</th>
                            <th class="px-5 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($certifications as $cert)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="text-white font-medium">{{ $cert->user?->name }}</div>
                                <div class="text-white/40 text-xs">{{ $cert->user?->staff_id }}</div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold
                                    {{ $cert->type === 'vehicle_land' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-sky-500/20 text-sky-300 border border-sky-500/30' }}">
                                    <i class="fas {{ $cert->type === 'vehicle_land' ? 'fa-car' : 'fa-helicopter' }} text-[10px]"></i>
                                    {{ $cert->type === 'vehicle_land' ? 'Kendaraan Darat' : 'Helikopter' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-white/80">{{ $cert->title }}</td>
                            <td class="px-5 py-3.5 text-white/70">{{ $cert->issue_date?->format('d M Y') }}</td>
                            <td class="px-5 py-3.5">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                    {{ $cert->status === 'active' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' :
                                      ($cert->status === 'revoked' ? 'bg-red-500/20 text-red-300 border border-red-500/30' :
                                       'bg-gray-500/20 text-gray-400 border border-gray-500/30') }}">
                                    {{ ucfirst($cert->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-white/60 text-xs">{{ $cert->issuedBy?->name ?? '—' }}</td>
                            <td class="px-5 py-3.5">
                                @if($cert->status === 'active')
                                <form method="POST" action="{{ route('portal.ga.revoke', $cert) }}">
                                    @csrf
                                    <button type="submit"
                                            onclick="return confirm('Cabut sertifikat {{ addslashes($cert->user?->name) }}?')"
                                            class="px-3 py-1 bg-red-500/20 hover:bg-red-500/30 text-red-300 text-xs font-medium rounded-lg border border-red-500/30 transition-all">
                                        <i class="fas fa-ban text-[10px]"></i> Cabut
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-3 border-t border-white/10">
                {{ $certifications->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
