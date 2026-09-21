@extends('layouts.app')

@section('title', 'PND: Kelola Pendaftaran Pelatihan — IME Medical Center')

@section('content')
<div class="min-h-screen pt-20 pb-12" style="background: linear-gradient(135deg, #0b1329 0%, #0c2461 50%, #0b1329 100%);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400 shadow-lg shadow-emerald-500/10">
                        <i class="fas fa-tasks text-xl"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-2xl font-bold text-white tracking-wide">PND: Kelola Pendaftaran Pelatihan</h1>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                Divisi PND
                            </span>
                        </div>
                        <p class="text-white/50 text-sm mt-0.5">Verifikasi, review kelayakan, dan kelola peserta Pelatihan Operasi, Surat Menyurat, & Visum Hidup</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button type="button" onclick="openDiscordModal()" class="px-4 py-2.5 rounded-xl bg-indigo-600/30 hover:bg-indigo-600/50 text-indigo-200 hover:text-white border border-indigo-500/40 text-sm font-semibold transition-all flex items-center gap-2 shadow-lg shadow-indigo-950/30">
                    <i class="fab fa-discord text-base"></i>
                    <span>Export Discord</span>
                </button>

                <a href="{{ route('portal.training.index') }}" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl text-sm font-medium transition-all flex items-center gap-2 border border-white/10">
                    <i class="fas fa-external-link-alt text-xs text-emerald-400"></i>
                    <span>Halaman Anggota</span>
                </a>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-500/15 border border-emerald-500/30 rounded-2xl text-emerald-300 text-sm flex items-center gap-3 shadow-lg shadow-emerald-950/20">
            <div class="w-8 h-8 rounded-xl bg-emerald-500/20 flex items-center justify-center shrink-0">
                <i class="fas fa-check-circle text-emerald-400 text-base"></i>
            </div>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 p-4 bg-rose-500/15 border border-rose-500/30 rounded-2xl text-rose-300 text-sm flex items-center gap-3 shadow-lg shadow-rose-950/20">
            <div class="w-8 h-8 rounded-xl bg-rose-500/20 flex items-center justify-center shrink-0">
                <i class="fas fa-exclamation-triangle text-rose-400 text-base"></i>
            </div>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        {{-- Statistics Row --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
            <div class="rounded-2xl bg-white/[0.03] border border-white/10 p-4 backdrop-blur-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-white/50 font-medium">Total Pendaftar</span>
                    <i class="fas fa-users text-white/30 text-sm"></i>
                </div>
                <div class="text-2xl font-black text-white mt-2">{{ $stats['total'] }}</div>
                <div class="text-[11px] text-white/40 mt-1">Semua program pelatihan</div>
            </div>

            <div class="rounded-2xl bg-amber-500/10 border border-amber-500/20 p-4 backdrop-blur-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-amber-300/80 font-medium">Menunggu Review</span>
                    <i class="fas fa-clock text-amber-400/50 text-sm"></i>
                </div>
                <div class="text-2xl font-black text-amber-300 mt-2">{{ $stats['pending'] }}</div>
                <div class="text-[11px] text-amber-300/60 mt-1">Perlu diverifikasi PND</div>
            </div>

            <div class="rounded-2xl bg-emerald-500/10 border border-emerald-500/20 p-4 backdrop-blur-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-emerald-300/80 font-medium">Disetujui</span>
                    <i class="fas fa-check-circle text-emerald-400/50 text-sm"></i>
                </div>
                <div class="text-2xl font-black text-emerald-300 mt-2">{{ $stats['approved'] }}</div>
                <div class="text-[11px] text-emerald-300/60 mt-1">Lolos seleksi</div>
            </div>

            <div class="rounded-2xl bg-rose-500/10 border border-rose-500/20 p-4 backdrop-blur-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-rose-300/80 font-medium">Ditolak</span>
                    <i class="fas fa-times-circle text-rose-400/50 text-sm"></i>
                </div>
                <div class="text-2xl font-black text-rose-300 mt-2">{{ $stats['rejected'] }}</div>
                <div class="text-[11px] text-rose-300/60 mt-1">Tidak memenuhi syarat</div>
            </div>
        </div>

        {{-- Training Type Tabs --}}
        <div class="flex flex-wrap items-center gap-2 mb-4 border-b border-white/10 pb-3">
            <a href="{{ route('portal.pnd.training.index', array_merge(request()->except(['type', 'page']), ['type' => 'all'])) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $type === 'all' ? 'bg-white text-slate-900 shadow-md' : 'bg-white/5 text-white/70 hover:bg-white/10 hover:text-white' }}">
                <span>Semua Pelatihan</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] {{ $type === 'all' ? 'bg-slate-200 text-slate-800' : 'bg-white/10 text-white/60' }}">{{ $stats['total'] }}</span>
            </a>

            <a href="{{ route('portal.pnd.training.index', array_merge(request()->except(['type', 'page']), ['type' => 'operasi'])) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $type === 'operasi' ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/30' : 'bg-white/5 text-white/70 hover:bg-white/10 hover:text-white' }}">
                <i class="fas fa-procedures"></i>
                <span>Pelatihan Operasi</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] {{ $type === 'operasi' ? 'bg-emerald-700 text-white' : 'bg-white/10 text-white/60' }}">{{ $stats['operasi'] }}</span>
            </a>

            <a href="{{ route('portal.pnd.training.index', array_merge(request()->except(['type', 'page']), ['type' => 'surat_menyurat'])) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $type === 'surat_menyurat' ? 'bg-blue-500 text-white shadow-lg shadow-blue-500/30' : 'bg-white/5 text-white/70 hover:bg-white/10 hover:text-white' }}">
                <i class="fas fa-envelope-open-text"></i>
                <span>Surat Menyurat</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] {{ $type === 'surat_menyurat' ? 'bg-blue-700 text-white' : 'bg-white/10 text-white/60' }}">{{ $stats['surat_menyurat'] }}</span>
            </a>

            <a href="{{ route('portal.pnd.training.index', array_merge(request()->except(['type', 'page']), ['type' => 'visum_hidup'])) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $type === 'visum_hidup' ? 'bg-purple-500 text-white shadow-lg shadow-purple-500/30' : 'bg-white/5 text-white/70 hover:bg-white/10 hover:text-white' }}">
                <i class="fas fa-notes-medical"></i>
                <span>Visum Hidup</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] {{ $type === 'visum_hidup' ? 'bg-purple-700 text-white' : 'bg-white/10 text-white/60' }}">{{ $stats['visum_hidup'] }}</span>
            </a>
        </div>

        {{-- Filter and Search Bar --}}
        <div class="rounded-2xl bg-white/[0.03] border border-white/10 p-4 mb-6 backdrop-blur-sm">
            <form method="GET" action="{{ route('portal.pnd.training.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                <input type="hidden" name="type" value="{{ $type }}">

                {{-- Search --}}
                <div class="sm:col-span-4 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-white/40">
                        <i class="fas fa-search text-xs"></i>
                    </div>
                    <input type="text"
                           name="q"
                           value="{{ $q }}"
                           placeholder="Cari Nama IC, HP, CID, atau Akun..."
                           class="w-full pl-9 pr-3 py-2 bg-white/5 border border-white/10 rounded-xl text-xs text-white placeholder-white/40 focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-transparent">
                </div>

                {{-- Filter Status --}}
                <div class="sm:col-span-3">
                    <select name="status" class="w-full py-2 px-3 bg-white/5 border border-white/10 rounded-xl text-xs text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 appearance-none cursor-pointer">
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }} class="bg-slate-900">Semua Status</option>
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }} class="bg-slate-900">⏳ Menunggu Review</option>
                        <option value="approved" {{ $status === 'approved' ? 'selected' : '' }} class="bg-slate-900">✅ Disetujui</option>
                        <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }} class="bg-slate-900">❌ Ditolak</option>
                    </select>
                </div>

                {{-- Filter Batch --}}
                <div class="sm:col-span-3">
                    <select name="batch" class="w-full py-2 px-3 bg-white/5 border border-white/10 rounded-xl text-xs text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 appearance-none cursor-pointer">
                        <option value="all" {{ $batch === 'all' ? 'selected' : '' }} class="bg-slate-900">Semua Batch</option>
                        @foreach($availableBatches as $b)
                        <option value="{{ $b }}" {{ $batch === $b ? 'selected' : '' }} class="bg-slate-900">{{ $b }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Buttons --}}
                <div class="sm:col-span-2 flex items-center gap-2">
                    <button type="submit" class="w-full py-2 px-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-semibold transition-all">
                        Terapkan
                    </button>
                    <a href="{{ route('portal.pnd.training.index', ['type' => $type]) }}" class="py-2 px-3 bg-white/10 hover:bg-white/15 text-white/70 hover:text-white rounded-xl text-xs transition-all" title="Reset Filter">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>

        {{-- Bulk Action & Table Form --}}
        <form id="bulkForm" action="{{ route('portal.pnd.training.bulk') }}" method="POST">
            @csrf

            {{-- Bulk Action Bar (Hidden by default, shown when items selected) --}}
            <div id="bulkActionBar" class="hidden mb-4 p-3 rounded-xl bg-slate-900/90 border border-emerald-500/30 flex flex-wrap items-center justify-between gap-3 shadow-xl">
                <div class="flex items-center gap-2 text-xs text-white/80">
                    <i class="fas fa-check-double text-emerald-400"></i>
                    <span><strong id="selectedCount" class="text-white">0</strong> data terpilih</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" name="action" value="approve" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold transition-all flex items-center gap-1.5">
                        <i class="fas fa-check"></i> Setujui Terpilih
                    </button>
                    <button type="submit" name="action" value="reject" class="px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-500 text-white text-xs font-semibold transition-all flex items-center gap-1.5">
                        <i class="fas fa-times"></i> Tolak Terpilih
                    </button>
                    <button type="submit" name="action" value="delete" onclick="return confirm('Apakah Anda yakin ingin menghapus data pendaftaran terpilih?')" class="px-3 py-1.5 rounded-lg bg-red-800/80 hover:bg-red-700 text-white text-xs font-semibold transition-all flex items-center gap-1.5">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>
            </div>

            {{-- Table Container --}}
            <div class="rounded-2xl bg-white/[0.03] border border-white/10 backdrop-blur-sm overflow-hidden shadow-2xl">
                @if($applications->isEmpty())
                <div class="text-center py-16 px-4">
                    <div class="w-16 h-16 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center mx-auto text-white/30 text-2xl mb-3">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <h3 class="text-base font-bold text-white/80">Tidak Ada Data Pendaftaran</h3>
                    <p class="text-xs text-white/40 mt-1 max-w-sm mx-auto">Tidak ada pengajuan pelatihan yang sesuai dengan kriteria filter saat ini.</p>
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-white/80">
                        <thead class="text-[11px] uppercase tracking-wider text-white/40 bg-white/[0.02] border-b border-white/10">
                            <tr>
                                <th class="py-3 px-4 w-8">
                                    <input type="checkbox" id="selectAll" class="w-4 h-4 rounded bg-white/10 border-white/20 text-emerald-500 focus:ring-0">
                                </th>
                                <th class="py-3 px-4">Pelatihan</th>
                                <th class="py-3 px-4">Nama IC & Kontak</th>
                                <th class="py-3 px-4">Akun Staf</th>
                                <th class="py-3 px-4">Jabatan</th>
                                <th class="py-3 px-4">Batch</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4">Reviewer</th>
                                <th class="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach($applications as $item)
                            <tr class="hover:bg-white/[0.02] transition-colors">
                                <td class="py-3.5 px-4">
                                    <input type="checkbox" name="ids[]" value="{{ $item->id }}" class="item-checkbox w-4 h-4 rounded bg-white/10 border-white/20 text-emerald-500 focus:ring-0">
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-white">
                                    <div class="flex items-center gap-2">
                                        @if($item->training_type === 'operasi')
                                            <span class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center text-xs shrink-0">
                                                <i class="fas fa-procedures"></i>
                                            </span>
                                        @elseif($item->training_type === 'surat_menyurat')
                                            <span class="w-7 h-7 rounded-lg bg-blue-500/20 text-blue-400 border border-blue-500/30 flex items-center justify-center text-xs shrink-0">
                                                <i class="fas fa-envelope-open-text"></i>
                                            </span>
                                        @else
                                            <span class="w-7 h-7 rounded-lg bg-purple-500/20 text-purple-400 border border-purple-500/30 flex items-center justify-center text-xs shrink-0">
                                                <i class="fas fa-notes-medical"></i>
                                            </span>
                                        @endif
                                        <div>
                                            <div class="text-xs font-bold text-white">{{ $item->type_label }}</div>
                                            <div class="text-[10px] text-white/40">{{ $item->created_at->translatedFormat('d M Y, H:i') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-white text-sm">{{ $item->nama_ic }}</div>
                                    <div class="flex items-center gap-2 text-xs text-white/60 mt-0.5">
                                        <span><i class="fas fa-venus-mars text-[10px] text-white/40"></i> {{ $item->gender }}</span>
                                        @if($item->phone_ic)
                                        <span>•</span>
                                        <span class="font-mono text-[11px] text-white/70"><i class="fas fa-phone-alt text-[9px] text-white/40"></i> {{ $item->phone_ic }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-xs">
                                    @if($item->user)
                                        <div class="text-white/90 font-medium">{{ $item->user->name }}</div>
                                        <div class="text-white/40 text-[11px]">CID: {{ $item->user->staff_id ?? '-' }}</div>
                                    @else
                                        <span class="text-white/30 italic">User Terhapus</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-xs">
                                    @if($item->jabatan)
                                        <span class="px-2 py-0.5 rounded-md bg-white/10 text-white/80 font-medium">{{ $item->jabatan }}</span>
                                    @else
                                        <span class="text-white/30">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-white/10 border border-white/15 text-white">
                                        {{ $item->batch }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    {!! $item->status_badge !!}
                                </td>
                                <td class="py-3.5 px-4 text-xs max-w-xs">
                                    @if($item->reviewed_by && $item->reviewer)
                                        <div class="text-white/90 font-medium">{{ $item->reviewer->name }}</div>
                                        <div class="text-white/40 text-[10px]">{{ $item->reviewed_at?->translatedFormat('d M Y, H:i') }}</div>
                                    @endif
                                    @if($item->admin_notes)
                                        <div class="text-[11px] text-white/70 italic mt-0.5 line-clamp-2">"{{ $item->admin_notes }}"</div>
                                    @elseif(!$item->reviewed_by)
                                        <span class="text-white/30 italic">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        {{-- Modal Review Button --}}
                                        <button type="button"
                                                onclick="openReviewModal({{ $item->id }}, '{{ addslashes($item->nama_ic) }}', '{{ $item->type_label }}', '{{ $item->status }}', '{{ addslashes($item->admin_notes ?? '') }}')"
                                                class="px-2.5 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-medium transition-all flex items-center gap-1"
                                                title="Verifikasi / Ubah Status">
                                            <i class="fas fa-edit text-xs text-emerald-400"></i>
                                            <span>Review</span>
                                        </button>

                                        {{-- Delete Button --}}
                                        <button type="button"
                                                onclick="deleteItem({{ $item->id }}, '{{ addslashes($item->nama_ic) }}')"
                                                class="w-8 h-8 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 flex items-center justify-center transition-all text-xs"
                                                title="Hapus Data">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-white/10">
                    {{ $applications->links() }}
                </div>
                @endif
            </div>
        </form>

    </div>
</div>

{{-- Hidden Delete Form --}}
<form id="deleteForm" method="POST" action="" class="hidden">
    @csrf
    @method('DELETE')
</form>

{{-- Review Modal --}}
<div id="reviewModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm hidden opacity-0 transition-opacity duration-200">
    <div class="w-full max-w-md bg-slate-900 rounded-2xl border border-white/15 p-6 shadow-2xl scale-95 transition-transform duration-200" id="reviewModalContent">
        <div class="flex items-center justify-between pb-3 border-b border-white/10 mb-4">
            <div class="flex items-center gap-2 text-white font-bold text-base">
                <i class="fas fa-user-check text-emerald-400"></i>
                <span>Verifikasi Pendaftaran</span>
            </div>
            <button type="button" onclick="closeReviewModal()" class="text-white/50 hover:text-white text-sm">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="reviewForm" method="POST" action="">
            @csrf
            <div class="mb-4">
                <div class="text-xs text-white/50">Peserta:</div>
                <div id="modalApplicantName" class="text-base font-bold text-white mt-0.5"></div>
                <div id="modalTrainingType" class="text-xs text-emerald-400 font-medium"></div>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-white mb-2">Pilih Status Verifikasi:</label>
                <div class="grid grid-cols-3 gap-2">
                    <label class="flex flex-col items-center justify-center p-2.5 rounded-xl border border-white/10 bg-white/5 hover:bg-white/10 cursor-pointer has-[:checked]:border-amber-500 has-[:checked]:bg-amber-500/15 transition-all text-center">
                        <input type="radio" name="status" value="pending" class="sr-only">
                        <i class="fas fa-clock text-amber-400 text-sm mb-1"></i>
                        <span class="text-[11px] font-bold text-white">Pending</span>
                    </label>
                    <label class="flex flex-col items-center justify-center p-2.5 rounded-xl border border-white/10 bg-white/5 hover:bg-white/10 cursor-pointer has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-500/15 transition-all text-center">
                        <input type="radio" name="status" value="approved" class="sr-only">
                        <i class="fas fa-check-circle text-emerald-400 text-sm mb-1"></i>
                        <span class="text-[11px] font-bold text-white">Setujui</span>
                    </label>
                    <label class="flex flex-col items-center justify-center p-2.5 rounded-xl border border-white/10 bg-white/5 hover:bg-white/10 cursor-pointer has-[:checked]:border-rose-500 has-[:checked]:bg-rose-500/15 transition-all text-center">
                        <input type="radio" name="status" value="rejected" class="sr-only">
                        <i class="fas fa-times-circle text-rose-400 text-sm mb-1"></i>
                        <span class="text-[11px] font-bold text-white">Tolak</span>
                    </label>
                </div>
            </div>

            <div class="mb-5">
                <label for="admin_notes" class="block text-xs font-bold text-white mb-1.5">
                    Catatan Verifikasi PND (Opsional):
                </label>
                <textarea id="admin_notes"
                          name="admin_notes"
                          rows="3"
                          placeholder="Tuliskan catatan atau instruksi bagi peserta..."
                          class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-xl text-xs text-white placeholder-white/30 focus:outline-none focus:ring-1 focus:ring-emerald-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-white/10">
                <button type="button" onclick="closeReviewModal()" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/15 text-white text-xs font-semibold transition-all">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all shadow-lg shadow-emerald-600/20">
                    Simpan Keputusan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Discord Export Modal --}}
<div id="discordModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm hidden opacity-0 transition-opacity duration-200">
    <div class="w-full max-w-2xl bg-slate-900 rounded-2xl border border-indigo-500/30 p-6 shadow-2xl scale-95 transition-transform duration-200" id="discordModalContent">
        <div class="flex items-center justify-between pb-3 border-b border-white/10 mb-4">
            <div class="flex items-center gap-2 text-white font-bold text-base">
                <i class="fab fa-discord text-indigo-400 text-lg"></i>
                <span>Export Pengumuman Discord Peserta Pelatihan</span>
            </div>
            <button type="button" onclick="closeDiscordModal()" class="text-white/50 hover:text-white text-sm">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
            <div>
                <label class="block text-xs font-semibold text-white/70 mb-1">Pilih Program Pelatihan:</label>
                <select id="discordTrainingType" onchange="generateDiscordText()" class="w-full py-2 px-3 bg-white/5 border border-white/10 rounded-xl text-xs text-white focus:outline-none focus:ring-1 focus:ring-indigo-500 appearance-none cursor-pointer">
                    <option value="operasi" {{ $type === 'operasi' ? 'selected' : '' }} class="bg-slate-900">Pelatihan Operasi</option>
                    <option value="surat_menyurat" {{ $type === 'surat_menyurat' ? 'selected' : '' }} class="bg-slate-900">Pelatihan Surat Menyurat</option>
                    <option value="visum_hidup" {{ $type === 'visum_hidup' ? 'selected' : '' }} class="bg-slate-900">Pelatihan Visum Hidup</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-white/70 mb-1">Pilih Batch:</label>
                <select id="discordBatch" onchange="generateDiscordText()" class="w-full py-2 px-3 bg-white/5 border border-white/10 rounded-xl text-xs text-white focus:outline-none focus:ring-1 focus:ring-indigo-500 appearance-none cursor-pointer">
                    <option value="all" class="bg-slate-900">Semua Batch</option>
                    @foreach($availableBatches as $b)
                    <option value="{{ $b }}" {{ $batch === $b ? 'selected' : '' }} class="bg-slate-900">{{ $b }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-4">
            <div class="flex items-center justify-between mb-1.5">
                <label class="text-xs font-bold text-white">Preview Teks Discord:</label>
                <span id="discordCountBadge" class="text-[11px] px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 font-semibold border border-indigo-500/30">
                    Memuat...
                </span>
            </div>
            <textarea id="discordOutput" rows="12" readonly class="w-full p-3.5 font-mono text-xs text-white/90 bg-black/40 border border-white/10 rounded-xl focus:outline-none focus:border-indigo-500 select-all"></textarea>
        </div>

        <div class="flex items-center justify-between gap-3 pt-3 border-t border-white/10">
            <span class="text-[11px] text-white/40">Tinggal copy dan paste langsung ke channel pengumuman Discord IME.</span>
            <div class="flex items-center gap-2">
                <button type="button" onclick="closeDiscordModal()" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/15 text-white text-xs font-semibold transition-all">
                    Tutup
                </button>
                <button type="button" onclick="copyDiscordToClipboard()" id="btnCopyDiscord" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition-all shadow-lg shadow-indigo-600/30 flex items-center gap-1.5">
                    <i class="fas fa-copy"></i>
                    <span>Salin ke Clipboard</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Checkbox selection logic for bulk actions
    const selectAll = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const bulkActionBar = document.getElementById('bulkActionBar');
    const selectedCount = document.getElementById('selectedCount');

    function updateBulkBar() {
        const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
        if (checkedCount > 0) {
            bulkActionBar.classList.remove('hidden');
            selectedCount.textContent = checkedCount;
        } else {
            bulkActionBar.classList.add('hidden');
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            itemCheckboxes.forEach(cb => cb.checked = selectAll.checked);
            updateBulkBar();
        });
    }

    itemCheckboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            updateBulkBar();
            if (!this.checked && selectAll) {
                selectAll.checked = false;
            }
        });
    });

    // Review Modal
    function openReviewModal(id, name, typeLabel, currentStatus, notes) {
        const modal = document.getElementById('reviewModal');
        const content = document.getElementById('reviewModalContent');
        const form = document.getElementById('reviewForm');
        
        form.action = `/portal/pnd/training/${id}/status`;
        document.getElementById('modalApplicantName').textContent = name;
        document.getElementById('modalTrainingType').textContent = typeLabel;
        document.getElementById('admin_notes').value = notes || '';

        const radio = form.querySelector(`input[name="status"][value="${currentStatus}"]`);
        if (radio) radio.checked = true;

        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
        }, 10);
    }

    function closeReviewModal() {
        const modal = document.getElementById('reviewModal');
        const content = document.getElementById('reviewModalContent');
        modal.classList.add('opacity-0');
        content.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }

    // Delete single item
    function deleteItem(id, name) {
        if (confirm(`Apakah Anda yakin ingin menghapus data pendaftaran "${name}"?`)) {
            const form = document.getElementById('deleteForm');
            form.action = `/portal/pnd/training/${id}`;
            form.submit();
        }
    }

    // Discord Modal
    function openDiscordModal() {
        const modal = document.getElementById('discordModal');
        const content = document.getElementById('discordModalContent');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
        }, 10);
        generateDiscordText();
    }

    function closeDiscordModal() {
        const modal = document.getElementById('discordModal');
        const content = document.getElementById('discordModalContent');
        modal.classList.add('opacity-0');
        content.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }

    function generateDiscordText() {
        const type = document.getElementById('discordTrainingType').value;
        const batch = document.getElementById('discordBatch').value;
        const badge = document.getElementById('discordCountBadge');
        const textarea = document.getElementById('discordOutput');

        badge.textContent = 'Memuat...';

        fetch(`/portal/pnd/training/export-discord?type=${encodeURIComponent(type)}&batch=${encodeURIComponent(batch)}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    textarea.value = data.template;
                    badge.textContent = `${data.count} Peserta Disetujui`;
                } else {
                    textarea.value = 'Gagal memuat template pengumuman.';
                    badge.textContent = '0 Peserta';
                }
            })
            .catch(err => {
                console.error(err);
                textarea.value = 'Terjadi kesalahan saat memuat data pengumuman.';
                badge.textContent = 'Error';
            });
    }

    function copyDiscordToClipboard() {
        const textarea = document.getElementById('discordOutput');
        textarea.select();
        navigator.clipboard.writeText(textarea.value).then(() => {
            const btn = document.getElementById('btnCopyDiscord');
            const orig = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i> <span>Tersalin!</span>';
            btn.classList.remove('bg-indigo-600', 'hover:bg-indigo-500');
            btn.classList.add('bg-emerald-600');
            setTimeout(() => {
                btn.innerHTML = orig;
                btn.classList.remove('bg-emerald-600');
                btn.classList.add('bg-indigo-600', 'hover:bg-indigo-500');
            }, 2000);
        });
    }
</script>
@endsection
