@extends('layouts.app')

@section('title', 'Rekam Operasi - Portal Medis')

@push('styles')
<style>
    /* Filter dark input fields */
    .filter-input-dark {
        background: rgba(255, 255, 255, 0.08) !important;
        border: 1px solid rgba(255, 255, 255, 0.18) !important;
        color: #ffffff !important;
        border-radius: 0.75rem !important;
        padding: 10px 14px !important;
        width: 100% !important;
        font-size: 0.875rem !important;
        color-scheme: dark !important;
        transition: all 0.2s ease !important;
    }
    .filter-input-dark:focus {
        outline: none !important;
        border-color: rgba(56, 189, 248, 0.8) !important;
        background: rgba(255, 255, 255, 0.12) !important;
        box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.2) !important;
    }
    .filter-input-dark::placeholder {
        color: rgba(186, 230, 253, 0.5) !important;
    }
    .filter-input-dark option {
        background: #075985 !important;
        color: #ffffff !important;
    }

    /* Tim Operasi badge */
    .team-badge {
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.35rem !important;
        white-space: nowrap !important;
        background: rgba(255, 255, 255, 0.08) !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        color: #e0f2fe !important;
        font-size: 0.75rem !important;
        font-weight: 500 !important;
        padding: 4px 10px !important;
        border-radius: 0.5rem !important;
        transition: all 0.15s ease !important;
    }
    .team-badge:hover {
        background: rgba(255, 255, 255, 0.15) !important;
        color: #ffffff !important;
    }
    .team-badge-dpjp {
        background: rgba(16, 185, 129, 0.18) !important;
        border-color: rgba(16, 185, 129, 0.4) !important;
        color: #6ee7b7 !important;
        font-weight: 700 !important;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen pt-28 pb-16 px-4 sm:px-6 lg:px-8" style="background: linear-gradient(180deg, #0c4a6e 0%, #075985 50%, #0369a1 100%);">
    <div class="max-w-7xl mx-auto text-white">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight flex items-center gap-3">
                    <span class="p-2.5 bg-gradient-to-r from-sky-500 to-cyan-500 rounded-2xl shadow-lg">
                        <i class="fas fa-procedures text-white text-2xl"></i>
                    </span>
                    Rekam Operasi
                </h1>
                <p class="text-sky-200 text-sm mt-2">Catatan rekam medis setiap kegiatan operasi tim medis terintegrasi</p>
            </div>
            @php
                $isTrainee = strtolower(trim(auth()->user()->role->name ?? '')) === 'trainee';
            @endphp
            @if(!$isTrainee)
            <a href="{{ route('staff.operations.create') }}"
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white font-bold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg shadow-sky-900/30 whitespace-nowrap">
                <i class="fas fa-plus mr-2"></i> Tambah Rekam Operasi
            </a>
            @else
            <div class="inline-flex items-center px-6 py-3 bg-white/10 text-sky-300 font-semibold rounded-xl border border-white/20 text-sm cursor-not-allowed opacity-60 whitespace-nowrap">
                <i class="fas fa-lock mr-2"></i> Trainee Tidak Bisa Mengisi
            </div>
            @endif
        </div>

        {{-- Alert --}}
        @if(session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-200 flex items-center justify-between shadow-lg shadow-emerald-950/20 backdrop-blur-md">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-500/30 flex items-center justify-center text-emerald-300">
                    <i class="fas fa-check text-lg"></i>
                </div>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-white/60 hover:text-white text-lg">&times;</button>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 p-4 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-200 flex items-center justify-between shadow-lg shadow-rose-950/20 backdrop-blur-md">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-rose-500/30 flex items-center justify-center text-rose-300">
                    <i class="fas fa-exclamation-triangle text-lg"></i>
                </div>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-white/60 hover:text-white text-lg">&times;</button>
        </div>
        @endif

        {{-- Filter --}}
        <div class="rounded-2xl bg-white/[0.05] border border-white/10 shadow-xl backdrop-blur-md p-6 mb-6">
            <form action="{{ route('staff.operations.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-sky-200 text-xs font-semibold uppercase tracking-wider mb-2">
                        <i class="fas fa-user mr-1 text-sky-400"></i> Nama Pasien
                    </label>
                    <input type="text" name="nama_pasien" class="filter-input-dark"
                           value="{{ request('nama_pasien') }}" placeholder="Cari nama pasien...">
                </div>
                <div>
                    <label class="block text-sky-200 text-xs font-semibold uppercase tracking-wider mb-2">
                        <i class="fas fa-id-card mr-1 text-sky-400"></i> Citizen ID Pasien
                    </label>
                    <input type="text" name="citizen_id" class="filter-input-dark"
                           value="{{ request('citizen_id') }}" placeholder="Contoh: 100234">
                </div>
                <div>
                    <label class="block text-sky-200 text-xs font-semibold uppercase tracking-wider mb-2">
                        <i class="fas fa-user-md mr-1 text-sky-400"></i> Tim Operasi / Staf
                    </label>
                    <input type="text" name="tim_operasi" class="filter-input-dark"
                           value="{{ request('tim_operasi') }}" placeholder="Cari nama staf bertugas...">
                </div>
                <div>
                    <label class="block text-sky-200 text-xs font-semibold uppercase tracking-wider mb-2">
                        <i class="fas fa-stethoscope mr-1 text-sky-400"></i> Jenis Operasi
                    </label>
                    <select name="jenis_operasi" class="filter-input-dark cursor-pointer">
                        <option value="">Semua Jenis Operasi</option>
                        <option value="Operasi Minor" {{ request('jenis_operasi') == 'Operasi Minor' ? 'selected' : '' }}>Operasi Minor</option>
                        <option value="Operasi Mayor" {{ request('jenis_operasi') == 'Operasi Mayor' ? 'selected' : '' }}>Operasi Mayor</option>
                        <option value="Emergency" {{ request('jenis_operasi') == 'Emergency' ? 'selected' : '' }}>Emergency</option>
                        <option value="Konsultasi Spesialisasi" {{ request('jenis_operasi') == 'Konsultasi Spesialisasi' ? 'selected' : '' }}>Konsultasi Spesialisasi</option>
                        <option value="Lainnya" {{ request('jenis_operasi') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
                <div class="lg:col-span-4 flex gap-2 justify-end pt-2">
                    <button type="submit"
                            class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white font-bold rounded-xl shadow-md transition-all text-sm cursor-pointer">
                        <i class="fas fa-filter mr-2"></i> Filter Data
                    </button>
                    <a href="{{ route('staff.operations.index') }}"
                       class="inline-flex items-center px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-xl border border-white/10 transition-all text-sm">
                        <i class="fas fa-redo mr-2"></i> Reset Filter
                    </a>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="rounded-2xl bg-white/[0.05] border border-white/10 shadow-2xl backdrop-blur-md overflow-hidden">
            {{-- Table Header --}}
            <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between" style="background-color: rgba(14, 165, 233, 0.15);">
                <h2 class="text-lg font-bold text-white flex items-center">
                    <i class="fas fa-list-alt mr-2.5 text-sky-400"></i>
                    Daftar Rekam Operasi
                    <span class="ml-3 px-3 py-0.5 bg-sky-500/20 text-sky-300 text-xs font-semibold rounded-full border border-sky-400/30">
                        {{ $operations->total() }} data
                    </span>
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10">
                    <thead>
                        <tr class="border-b border-white/10" style="background-color: rgba(14, 165, 233, 0.2);">
                            <th class="px-5 py-4 text-left text-xs font-bold text-sky-200 uppercase tracking-wider whitespace-nowrap">Tanggal & Waktu</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-sky-200 uppercase tracking-wider whitespace-nowrap">Nama Pasien</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-sky-200 uppercase tracking-wider whitespace-nowrap">Jenis Operasi</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-sky-200 uppercase tracking-wider whitespace-nowrap">Lokasi</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-sky-200 uppercase tracking-wider min-w-[220px]">Tim Operasi</th>
                            <th class="px-5 py-4 text-right text-xs font-bold text-sky-200 uppercase tracking-wider whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse($operations as $op)
                        <tr class="hover:bg-white/[0.06] transition-all duration-200">
                            <td class="px-5 py-5 whitespace-nowrap">
                                <div class="text-white font-semibold text-sm">{{ $op->tanggal_waktu->format('d M Y') }}</div>
                                <div class="text-sky-300 text-xs mt-1">{{ $op->tanggal_waktu->format('H:i') }} WIB</div>
                            </td>
                            <td class="px-5 py-5 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-sky-500 to-cyan-500 flex items-center justify-center text-white font-bold text-sm mr-3 shadow-md shrink-0">
                                        {{ substr($op->nama_pasien, 0, 1) }}
                                    </div>
                                    <div>
                                        <span class="text-white font-medium text-sm block">{{ $op->nama_pasien }}</span>
                                        @php $lastLog = $op->logs->first(); @endphp
                                        @if($lastLog)
                                            <span class="text-[10px] text-sky-300/80 flex items-center gap-1 mt-0.5" title="{{ $lastLog->created_at->format('d M Y H:i') }}">
                                                <i class="fas {{ $lastLog->action === 'edit' ? 'fa-pen text-amber-400' : ($lastLog->action === 'create' ? 'fa-plus text-emerald-400' : 'fa-eye text-sky-400') }} text-[9px]"></i>
                                                <span>{{ ucfirst($lastLog->action) }}: {{ $lastLog->user->name ?? 'Staf' }} ({{ $lastLog->created_at->diffForHumans() }})</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-5 whitespace-nowrap">
                                @php
                                    $badgeColors = [
                                        'Operasi Mayor'          => 'from-red-500 to-pink-500',
                                        'Operasi Minor'          => 'from-yellow-500 to-orange-500',
                                        'Emergency'              => 'from-red-600 to-red-700',
                                        'Konsultasi Spesialisasi'=> 'from-blue-500 to-indigo-500',
                                    ];
                                    $badgeColor = $badgeColors[$op->jenis_operasi] ?? 'from-slate-500 to-gray-600';
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold text-white bg-gradient-to-r {{ $badgeColor }} shadow-sm">
                                    {{ $op->jenis_operasi }}
                                </span>
                            </td>
                            <td class="px-5 py-5 whitespace-nowrap text-sky-200 text-sm">
                                <i class="fas fa-map-marker-alt mr-1.5 text-sky-400"></i>
                                {{ $op->lokasi }}
                            </td>
                            <td class="px-5 py-5 min-w-[220px]">
                                <div class="flex flex-col gap-1.5 items-start">
                                    @if($op->dpjp)
                                        <span class="team-badge team-badge-dpjp" title="DPJP: {{ $op->dpjp->name }}">
                                            <i class="fas fa-star text-[10px] text-amber-300 shrink-0"></i>
                                            <span class="max-w-[170px] truncate inline-block"><strong class="font-bold">DPJP:</strong> {{ $op->dpjp->name }}</span>
                                        </span>
                                    @endif
                                    @php
                                        $otherMembers = $op->members->filter(fn($m) => !$op->dpjp_id || $m->id !== $op->dpjp_id);
                                    @endphp
                                    @if($otherMembers->isNotEmpty())
                                    <div class="flex flex-wrap gap-1 items-center">
                                        @foreach($otherMembers->take(3) as $member)
                                            <span class="team-badge" title="{{ $member->name }}">
                                                <i class="fas fa-user-md text-[10px] text-sky-400 shrink-0"></i>
                                                <span class="max-w-[150px] truncate inline-block">{{ $member->name }}</span>
                                            </span>
                                        @endforeach
                                        @if($otherMembers->count() > 3)
                                            <span class="inline-flex items-center px-2 py-0.5 bg-sky-500/20 text-sky-300 text-[10px] rounded-md border border-sky-400/30 font-semibold whitespace-nowrap">
                                                +{{ $otherMembers->count() - 3 }} lainnya
                                            </span>
                                        @endif
                                    </div>
                                    @endif
                                    @if(!$op->dpjp && $otherMembers->isEmpty())
                                        <span class="text-xs text-sky-300/60 italic">- Belum ada tim -</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-5 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @php
                                        $userRole = strtolower(trim(auth()->user()->role->name ?? ''));
                                        $canDelete = auth()->user()->isAdmin() || in_array($userRole, ['ceo', 'executive', 'direktur', 'high_command']);
                                        $isTagged = auth()->user()->isAdmin() || $op->created_by == auth()->id() || $op->dpjp_id == auth()->id() || $op->members->contains('id', auth()->id());
                                    @endphp

                                    @if($isTagged)
                                    <a href="{{ route('staff.operations.edit', $op->id) }}"
                                       class="inline-flex items-center px-3 py-2 bg-amber-500/20 hover:bg-amber-500/40 text-amber-300 hover:text-white text-xs font-bold rounded-xl border border-amber-500/30 transition-all duration-300"
                                       title="Lengkapi / Edit Rekam Operasi Ini">
                                        <i class="fas fa-edit mr-1"></i> Lengkapi
                                    </a>
                                    @endif

                                    <a href="{{ route('staff.operations.show', $op->id) }}"
                                       class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white text-xs font-bold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-md">
                                        <i class="fas fa-eye mr-1.5"></i> Detail
                                    </a>
                                    
                                    @if($canDelete)
                                    <form action="{{ route('admin.operations.destroy', $op->id) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Hapus rekam operasi ini? Data yang terhapus tidak dapat dikembalikan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center px-3 py-2 bg-rose-500/20 hover:bg-rose-500/40 text-rose-300 text-xs font-bold rounded-xl border border-rose-500/30 transition-all duration-300">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center text-sky-300">
                                    <div class="w-20 h-20 bg-sky-500/10 rounded-full flex items-center justify-center mb-4 border border-sky-400/20">
                                        <i class="fas fa-procedures text-3xl text-sky-400"></i>
                                    </div>
                                    <p class="text-lg font-semibold text-white mb-1">Belum ada data rekam operasi</p>
                                    <p class="text-sm text-sky-300 mb-5">Mulai dengan menambah rekam operasi pertama</p>
                                    <a href="{{ route('staff.operations.create') }}"
                                       class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-sky-500 to-cyan-500 text-white font-semibold rounded-xl hover:from-sky-600 hover:to-cyan-600 transition-all duration-300 shadow-lg shadow-sky-900/30">
                                        <i class="fas fa-plus mr-2"></i> Tambah Sekarang
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($operations->hasPages())
            <div class="px-6 py-4 border-t border-white/10">
                {{ $operations->links() }}
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
