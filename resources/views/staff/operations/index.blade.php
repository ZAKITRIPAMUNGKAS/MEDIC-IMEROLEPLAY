@extends('layouts.app')

@section('title', 'Rekam Operasi - Portal Medis')

@section('content')
<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2">
                    <i class="fas fa-procedures mr-3 text-sky-400"></i>Rekam Operasi
                </h1>
                <p class="text-sky-200">Catatan rekam medis setiap kegiatan operasi tim medic</p>
            </div>
            @php
                $isTrainee = strtolower(trim(auth()->user()->role->name ?? '')) === 'trainee';
            @endphp
            @if(!$isTrainee)
            <a href="{{ route('staff.operations.create') }}"
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white font-bold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg">
                <i class="fas fa-plus mr-2"></i> Tambah Rekam Operasi
            </a>
            @else
            <div class="inline-flex items-center px-6 py-3 bg-white/10 text-sky-300 font-semibold rounded-xl border border-white/20 text-sm cursor-not-allowed opacity-60">
                <i class="fas fa-lock mr-2"></i> Trainee Tidak Bisa Mengisi
            </div>
            @endif
        </div>

        {{-- Alert --}}
        @if(session('success'))
        <div class="mb-6 bg-emerald-500 bg-opacity-20 border border-emerald-500 border-opacity-40 text-emerald-300 px-6 py-4 rounded-xl flex items-center" role="alert">
            <i class="fas fa-check-circle mr-3 text-emerald-400"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 bg-red-500 bg-opacity-20 border border-red-500 border-opacity-40 text-red-300 px-6 py-4 rounded-xl flex items-center" role="alert">
            <i class="fas fa-times-circle mr-3 text-red-400"></i>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        {{-- Filter --}}
        <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-2xl border border-white border-opacity-20 p-6 mb-6">
            <form action="{{ route('staff.operations.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-sky-200 text-sm font-medium mb-2">
                        <i class="fas fa-user mr-1"></i> Nama Pasien
                    </label>
                    <input type="text" name="nama_pasien"
                           class="w-full bg-white bg-opacity-10 text-white border border-white border-opacity-20 rounded-xl px-4 py-2.5 placeholder-sky-300 focus:ring-2 focus:ring-sky-400 focus:border-transparent transition text-sm"
                           value="{{ request('nama_pasien') }}" placeholder="Cari nama pasien...">
                </div>
                <div>
                    <label class="block text-sky-200 text-sm font-medium mb-2">
                        <i class="fas fa-id-card mr-1 text-sky-300"></i> Citizen ID Pasien
                    </label>
                    <input type="text" name="citizen_id"
                           class="w-full bg-white bg-opacity-10 text-white border border-white border-opacity-20 rounded-xl px-4 py-2.5 placeholder-sky-300 focus:ring-2 focus:ring-sky-400 focus:border-transparent transition text-sm"
                           value="{{ request('citizen_id') }}" placeholder="Contoh: 100234">
                </div>
                <div>
                    <label class="block text-sky-200 text-sm font-medium mb-2">
                        <i class="fas fa-user-md mr-1 text-sky-300"></i> Tim Operasi / Staf
                    </label>
                    <input type="text" name="tim_operasi"
                           class="w-full bg-white bg-opacity-10 text-white border border-white border-opacity-20 rounded-xl px-4 py-2.5 placeholder-sky-300 focus:ring-2 focus:ring-sky-400 focus:border-transparent transition text-sm"
                           value="{{ request('tim_operasi') }}" placeholder="Cari nama staf bertugas...">
                </div>
                <div>
                    <label class="block text-sky-200 text-sm font-medium mb-2">
                        <i class="fas fa-stethoscope mr-1"></i> Jenis Operasi
                    </label>
                    <select name="jenis_operasi"
                            class="w-full bg-white bg-opacity-10 text-white border border-white border-opacity-20 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-sky-400 focus:border-transparent transition cursor-pointer text-sm">
                        <option value="" class="bg-gray-800">Semua Jenis</option>
                        <option value="Operasi Minor" class="bg-gray-800" {{ request('jenis_operasi') == 'Operasi Minor' ? 'selected' : '' }}>Operasi Minor</option>
                        <option value="Operasi Mayor" class="bg-gray-800" {{ request('jenis_operasi') == 'Operasi Mayor' ? 'selected' : '' }}>Operasi Mayor</option>
                        <option value="Emergency" class="bg-gray-800" {{ request('jenis_operasi') == 'Emergency' ? 'selected' : '' }}>Emergency</option>
                        <option value="Konsultasi Spesialisasi" class="bg-gray-800" {{ request('jenis_operasi') == 'Konsultasi Spesialisasi' ? 'selected' : '' }}>Konsultasi Spesialisasi</option>
                        <option value="Lainnya" class="bg-gray-800" {{ request('jenis_operasi') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
                <div class="lg:col-span-4 flex gap-2 justify-end pt-2">
                    <button type="submit"
                            class="inline-flex items-center px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-semibold rounded-xl transition-all duration-300 shadow-md text-sm">
                        <i class="fas fa-filter mr-2"></i> Filter Data
                    </button>
                    <a href="{{ route('staff.operations.index') }}"
                       class="inline-flex items-center px-5 py-2.5 bg-white bg-opacity-10 hover:bg-opacity-20 text-white font-semibold rounded-xl transition-all duration-300 text-sm">
                        <i class="fas fa-redo mr-2"></i> Reset Filter
                    </a>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-2xl border border-white border-opacity-20 overflow-hidden">
            {{-- Table Header --}}
            <div class="px-6 py-4 border-b border-white border-opacity-10">
                <h2 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-list-alt mr-2 text-sky-400"></i>
                    Daftar Rekam Operasi
                    <span class="ml-3 px-3 py-0.5 bg-sky-500 bg-opacity-20 text-sky-300 text-sm rounded-full border border-sky-500 border-opacity-30">
                        {{ $operations->total() }} data
                    </span>
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-white border-opacity-10" style="background-color: rgba(14, 165, 233, 0.2);">
                            <th class="px-6 py-4 text-left text-xs font-bold text-sky-200 uppercase tracking-wider">Tanggal & Waktu</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-sky-200 uppercase tracking-wider">Nama Pasien</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-sky-200 uppercase tracking-wider">Jenis Operasi</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-sky-200 uppercase tracking-wider">Lokasi</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-sky-200 uppercase tracking-wider">Tim Operasi</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-sky-200 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white divide-opacity-10">
                        @forelse($operations as $op)
                        <tr class="hover:bg-sky-700 hover:bg-opacity-20 transition-all duration-200">
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="text-white font-semibold text-sm">{{ $op->tanggal_waktu->format('d M Y') }}</div>
                                <div class="text-sky-300 text-xs mt-1">{{ $op->tanggal_waktu->format('H:i') }} WIB</div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-sky-500 to-cyan-500 flex items-center justify-center text-white font-bold text-sm mr-3 shadow-md">
                                        {{ substr($op->nama_pasien, 0, 1) }}
                                    </div>
                                    <span class="text-white font-medium text-sm">{{ $op->nama_pasien }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
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
                            <td class="px-6 py-5 whitespace-nowrap text-sky-200 text-sm">
                                <i class="fas fa-map-marker-alt mr-1.5 text-sky-400"></i>
                                {{ $op->lokasi }}
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($op->members->take(3) as $member)
                                        <span class="inline-block px-2.5 py-1 bg-white bg-opacity-10 text-sky-200 text-xs rounded-full border border-white border-opacity-15">
                                            {{ $member->name }}
                                        </span>
                                    @endforeach
                                    @if($op->members->count() > 3)
                                        <span class="inline-block px-2.5 py-1 bg-sky-500 bg-opacity-20 text-sky-300 text-xs rounded-full border border-sky-500 border-opacity-30">
                                            +{{ $op->members->count() - 3 }} lainnya
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @php
                                        $userRole = strtolower(trim(auth()->user()->role->name ?? ''));
                                        $canDelete = auth()->user()->isAdmin() || in_array($userRole, ['ceo', 'executive', 'direktur', 'high_command']);
                                        $isTagged = auth()->user()->isAdmin() || $op->created_by == auth()->id() || $op->dpjp_id == auth()->id() || $op->members->contains('id', auth()->id());
                                    @endphp

                                    @if($isTagged)
                                    <a href="{{ route('staff.operations.edit', $op->id) }}"
                                       class="inline-flex items-center px-3 py-2 bg-amber-500 bg-opacity-20 hover:bg-amber-500 hover:bg-opacity-40 text-amber-300 hover:text-white text-xs font-bold rounded-xl border border-amber-500 border-opacity-30 transition-all duration-300"
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
                                                class="inline-flex items-center px-3 py-2 bg-red-500 bg-opacity-20 hover:bg-opacity-40 text-red-300 text-xs font-bold rounded-xl border border-red-500 border-opacity-30 transition-all duration-300">
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
                                    <div class="w-20 h-20 bg-sky-500 bg-opacity-10 rounded-full flex items-center justify-center mb-4 border border-sky-500 border-opacity-20">
                                        <i class="fas fa-procedures text-3xl text-sky-400"></i>
                                    </div>
                                    <p class="text-lg font-semibold text-white mb-1">Belum ada data rekam operasi</p>
                                    <p class="text-sm text-sky-300 mb-5">Mulai dengan menambah rekam operasi pertama</p>
                                    <a href="{{ route('staff.operations.create') }}"
                                       class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-sky-500 to-cyan-500 text-white font-semibold rounded-xl hover:from-sky-600 hover:to-cyan-600 transition-all duration-300">
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
            <div class="px-6 py-4 border-t border-white border-opacity-10">
                {{ $operations->links() }}
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
