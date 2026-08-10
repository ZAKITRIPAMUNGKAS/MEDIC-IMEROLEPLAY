@extends('layouts.app')

@section('title', 'Kelola Voting - Portal Medis iMe Roleplay')

@section('content')
<div class="min-h-screen bg-slate-950 text-slate-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-8">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900 border border-slate-800 p-6 rounded-2xl shadow-xl">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-500/10 text-purple-400 border border-purple-500/20 text-xs font-semibold uppercase tracking-wider mb-2">
                    <i class="fas fa-user-shield"></i> Panel Kelola Voting
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white">
                    Manajemen Sesi Voting
                </h1>
                <p class="text-slate-400 text-sm mt-1">
                    Buat, aktifkan, atau tutup sesi pemilihan kapan saja untuk Roxwood Hospital & Alta Hospital.
                </p>
            </div>

            <a href="{{ route('admin.voting.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold text-sm shadow-lg shadow-indigo-900/30 transition-all">
                <i class="fas fa-plus-circle"></i> Buat Voting Baru
            </a>
        </div>

        <!-- Alert Notification -->
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm flex items-center gap-3">
                <i class="fas fa-check-circle text-emerald-400 text-lg"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-sm flex items-center gap-3">
                <i class="fas fa-exclamation-triangle text-rose-400 text-lg"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Filter Bar -->
        <div class="flex flex-wrap items-center justify-between gap-4 bg-slate-900/60 p-4 rounded-xl border border-slate-800">
            <form action="{{ route('admin.voting.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                <div>
                    <select name="hospital" onchange="this.form.submit()" class="bg-slate-950 border border-slate-800 text-slate-200 text-xs rounded-xl px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                        <option value="all" {{ $hospital === 'all' ? 'selected' : '' }}>Semua Rumah Sakit</option>
                        <option value="alta" {{ $hospital === 'alta' ? 'selected' : '' }}>Alta Hospital</option>
                        <option value="roxwood" {{ $hospital === 'roxwood' ? 'selected' : '' }}>Roxwood Hospital</option>
                    </select>
                </div>
                <div>
                    <select name="status" onchange="this.form.submit()" class="bg-slate-950 border border-slate-800 text-slate-200 text-xs rounded-xl px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="closed" {{ $status === 'closed' ? 'selected' : '' }}>Ditutup</option>
                        <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>
            </form>

            <a href="{{ route('staff.voting.index') }}" class="text-xs text-indigo-400 hover:text-indigo-300 flex items-center gap-1 font-medium">
                <i class="fas fa-external-link-alt"></i> Lihat Tampilan Staf
            </a>
        </div>

        <!-- Table / List of Votings -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-950 text-xs uppercase text-slate-400 border-b border-slate-800 font-semibold">
                        <tr>
                            <th class="px-6 py-4">Judul & Posisi</th>
                            <th class="px-6 py-4">Rumah Sakit</th>
                            <th class="px-6 py-4 text-center">Kandidat</th>
                            <th class="px-6 py-4 text-center">Total Suara</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @forelse($votings as $voting)
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-white text-base">
                                        {{ $voting->title }}
                                    </div>
                                    @if($voting->target_position)
                                        <div class="text-xs text-indigo-400 font-medium">
                                            Posisi: {{ $voting->target_position }}
                                        </div>
                                    @endif
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        Dibuat oleh {{ $voting->creator->name ?? 'Admin' }} &bull; {{ $voting->created_at->format('d/m/Y H:i') }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($voting->hospital === 'roxwood')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30">
                                            🏥 Roxwood
                                        </span>
                                    @elseif($voting->hospital === 'alta')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-cyan-500/10 text-cyan-400 border border-cyan-500/30">
                                            🏥 Alta
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-purple-500/10 text-purple-400 border border-purple-500/30">
                                            🌐 Semua Staf
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center font-bold text-white whitespace-nowrap">
                                    {{ $voting->candidates->count() }}
                                </td>

                                <td class="px-6 py-4 text-center font-bold text-emerald-400 whitespace-nowrap">
                                    {{ $voting->totalVotesCount() }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($voting->status === 'active')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 flex items-center gap-1 w-max">
                                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Aktif
                                        </span>
                                    @elseif($voting->status === 'closed')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-800 text-slate-400 border border-slate-700">
                                            🔒 Ditutup
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-500/10 text-yellow-400 border border-yellow-500/30">
                                            📝 Draft
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                    <!-- Toggle Status Quick Form -->
                                    <form action="{{ route('admin.voting.toggle-status', $voting->id) }}" method="POST" class="inline">
                                        @csrf
                                        @if($voting->status === 'active')
                                            <input type="hidden" name="status" value="closed">
                                            <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 text-xs font-semibold border border-amber-500/30 transition-all" title="Tutup Voting">
                                                <i class="fas fa-stop-circle mr-1"></i> Tutup
                                            </button>
                                        @else
                                            <input type="hidden" name="status" value="active">
                                            <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 text-xs font-semibold border border-emerald-500/30 transition-all" title="Mulai Voting">
                                                <i class="fas fa-play-circle mr-1"></i> Mulai
                                            </button>
                                        @endif
                                    </form>

                                    <!-- Detail View -->
                                    <a href="{{ route('staff.voting.show', $voting->id) }}" class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 inline-flex items-center gap-1" title="Lihat Hasil">
                                        <i class="fas fa-chart-pie"></i>
                                    </a>

                                    <!-- Edit -->
                                    <a href="{{ route('admin.voting.edit', $voting->id) }}" class="px-2.5 py-1.5 rounded-lg bg-indigo-600/20 hover:bg-indigo-600/30 text-indigo-300 text-xs font-semibold border border-indigo-500/30 inline-flex items-center gap-1" title="Edit Voting">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <!-- Delete Form -->
                                    <form action="{{ route('admin.voting.destroy', $voting->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus sesi voting ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 text-xs font-semibold border border-rose-500/30 transition-all" title="Hapus Voting">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                                    Belum ada sesi voting yang dibuat. Klik tombol "Buat Voting Baru" untuk menambahkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-slate-800">
                {{ $votings->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
