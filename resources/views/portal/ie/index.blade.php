@extends('layouts.app')
@section('title', 'Kontrak Medis IE — Portal Alta Hospital')
@section('content')
<div class="min-h-screen pt-20 pb-10" style="background: linear-gradient(135deg, #0b1329 0%, #0c2461 50%, #0b1329 100%);">
<div class="max-w-6xl mx-auto px-4">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2"><i class="fas fa-file-contract text-sky-400"></i> IE — Surat Perjanjian Kontrak Medis</h1>
            <p class="text-white/50 text-sm mt-0.5">Terbitkan kontrak medis anggota yang otomatis muncul di profil mereka</p>
        </div>
    </div>
    @if(session('success'))
    <div class="mb-4 p-4 bg-emerald-500/20 border border-emerald-500/40 rounded-xl text-emerald-300 text-sm flex items-center gap-2"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    {{-- Form Terbitkan --}}
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 mb-6">
        <h3 class="text-white font-semibold mb-4 flex items-center gap-2"><i class="fas fa-plus-circle text-sky-400"></i> Terbitkan Kontrak Medis Baru</h3>
        <form method="POST" action="{{ route('portal.ie.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @csrf
            <div>
                <label class="block text-xs text-white/50 mb-1">Anggota *</label>
                <select name="user_id" required class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-sky-400">
                    <option value="">— Pilih Anggota —</option>
                    @foreach($staffList as $s)<option value="{{ $s->id }}">{{ $s->name }} ({{ $s->staff_id }})</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-white/50 mb-1">Judul Kontrak *</label>
                <input type="text" name="title" required maxlength="255" value="Surat Perjanjian Kontrak Medis" class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-sky-400">
            </div>
            <div>
                <label class="block text-xs text-white/50 mb-1">Nomor Kontrak</label>
                <input type="text" name="certificate_number" maxlength="100" placeholder="IE-XXXX-YYYY" class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm placeholder-white/30 focus:outline-none focus:border-sky-400">
            </div>
            <div>
                <label class="block text-xs text-white/50 mb-1">Tanggal Terbit *</label>
                <input type="date" name="issue_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-sky-400">
            </div>
            <div>
                <label class="block text-xs text-white/50 mb-1">Tanggal Berakhir</label>
                <input type="date" name="expiry_date" class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-sky-400">
            </div>
            <div>
                <label class="block text-xs text-white/50 mb-1">Upload Kontrak (PDF/Gambar)</label>
                <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" class="w-full px-3 py-2 bg-white/10 border border-white/20 border-dashed rounded-xl text-white/70 text-xs file:mr-2 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-sky-500/20 file:text-sky-300">
            </div>
            <div class="col-span-1 sm:col-span-2">
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-sky-500 to-blue-600 text-white font-semibold rounded-xl text-sm hover:from-sky-400 hover:to-blue-500 transition-all">
                    <i class="fas fa-file-contract mr-1.5"></i> Terbitkan Kontrak & Sync ke Profil
                </button>
            </div>
        </form>
    </div>
    {{-- Tabel --}}
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden">
        @if($contracts->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-white/40"><i class="fas fa-file-contract text-4xl mb-3"></i><p class="text-sm">Belum ada kontrak medis diterbitkan.</p></div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-white/10 text-xs text-white/50 uppercase tracking-wider">
                    <tr><th class="text-left px-5 py-3">Anggota</th><th class="text-left px-5 py-3">Judul</th><th class="text-left px-5 py-3">No. Kontrak</th><th class="text-left px-5 py-3">Terbit</th><th class="text-left px-5 py-3">Berakhir</th><th class="text-left px-5 py-3">Status</th><th class="px-5 py-3">Aksi</th></tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($contracts as $c)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-5 py-3.5"><div class="text-white font-medium">{{ $c->user?->name }}</div><div class="text-white/40 text-xs">{{ $c->user?->staff_id }}</div></td>
                        <td class="px-5 py-3.5 text-white/80">{{ $c->title }}</td>
                        <td class="px-5 py-3.5 text-white/60 text-xs">{{ $c->certificate_number ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-white/70">{{ $c->issue_date?->format('d M Y') }}</td>
                        <td class="px-5 py-3.5 text-white/70">{{ $c->expiry_date?->format('d M Y') ?? '—' }}</td>
                        <td class="px-5 py-3.5"><span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $c->status === 'active' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-red-500/20 text-red-300 border border-red-500/30' }}">{{ ucfirst($c->status) }}</span></td>
                        <td class="px-5 py-3.5">
                            @if($c->status === 'active')
                            <form method="POST" action="{{ route('portal.ie.revoke', $c) }}">
                                @csrf
                                <button type="submit" onclick="return confirm('Cabut kontrak {{ addslashes($c->user?->name) }}?')" class="px-3 py-1 bg-red-500/20 hover:bg-red-500/30 text-red-300 text-xs font-medium rounded-lg border border-red-500/30">
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
        <div class="px-5 py-3 border-t border-white/10">{{ $contracts->links() }}</div>
        @endif
    </div>
</div>
</div>
@endsection
