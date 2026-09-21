@extends('layouts.app')
@section('title', 'Manajemen Sub-Jabatan (Divisi) - Alta Hospital')

@section('content')
<div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700"></div>
    <div class="absolute inset-0 bg-black bg-opacity-20"></div>

    <div class="relative max-w-7xl mx-auto text-white">

        {{-- Header --}}
        <div class="glass-effect rounded-2xl elegant-shadow-lg p-6 md:p-8 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1">Sub-Jabatan / Divisi</h1>
                    <p class="text-sky-200 text-sm">Kelola divisi Alta Hospital dan assignment anggota staf</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.sub-roles.assign') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-500 hover:bg-sky-400 text-white rounded-lg font-semibold text-sm transition-all shadow-lg">
                        <i class="fas fa-user-tag"></i> Assign Divisi ke Staf
                    </a>
                </div>
            </div>

            @if($unassigned > 0)
            <div class="mt-4 flex items-center gap-2 bg-amber-500/20 border border-amber-500/40 rounded-lg px-4 py-2.5 text-sm">
                <i class="fas fa-exclamation-triangle text-amber-400"></i>
                <span class="text-amber-200"><strong class="text-amber-300">{{ $unassigned }} staf</strong> di {{ ucfirst($hospital) }} belum memiliki sub-jabatan.</span>
                <a href="{{ route('admin.sub-roles.assign', ['hospital' => $hospital]) }}" class="ml-auto text-amber-300 hover:text-amber-100 font-semibold text-xs underline">Assign sekarang →</a>
            </div>
            @endif
        </div>

        {{-- Flash --}}
        @if(session('success'))
            <div class="mb-4 bg-green-500/20 border border-green-500/40 rounded-xl px-4 py-3 text-green-300 text-sm flex items-center gap-2">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        {{-- Grid Divisi --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($subRoles as $sub)
            <div class="glass-effect rounded-2xl p-5 flex flex-col gap-4">
                {{-- Badge warna + nama --}}
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center text-white font-black text-sm flex-shrink-0"
                             style="background-color: {{ $sub->color }}30; border: 1px solid {{ $sub->color }}60;">
                            <span style="color: {{ $sub->color }}">{{ $sub->short_name }}</span>
                        </div>
                        <div>
                            <p class="text-white font-semibold text-sm leading-tight">{{ $sub->display_name }}</p>
                            <p class="text-gray-400 text-xs mt-0.5">{{ ucfirst($sub->hospital) }}</p>
                        </div>
                    </div>
                    {{-- Toggle aktif --}}
                    <form method="POST" action="{{ route('admin.sub-roles.toggle-active', $sub) }}">
                        @csrf
                        <button type="submit" title="{{ $sub->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                            class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all
                                {{ $sub->is_active
                                    ? 'bg-green-500/20 text-green-400 border border-green-500/30 hover:bg-red-500/20 hover:text-red-400 hover:border-red-500/30'
                                    : 'bg-red-500/20 text-red-400 border border-red-500/30 hover:bg-green-500/20 hover:text-green-400 hover:border-green-500/30' }}">
                            {{ $sub->is_active ? 'Aktif' : 'Nonaktif' }}
                        </button>
                    </form>
                </div>

                {{-- Deskripsi --}}
                @if($sub->description)
                <p class="text-gray-400 text-xs leading-relaxed">{{ $sub->description }}</p>
                @endif

                {{-- Jumlah anggota --}}
                <div class="flex items-center justify-between pt-3 border-t border-white/10">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-users text-sky-400 text-xs"></i>
                        <span class="text-gray-300 text-sm"><strong class="text-white">{{ $sub->users_count }}</strong> anggota</span>
                    </div>
                    <a href="{{ route('admin.sub-roles.members', $sub) }}"
                       class="text-xs text-sky-400 hover:text-sky-300 font-semibold transition-colors">
                        Lihat anggota →
                    </a>
                </div>
            </div>
            @empty
            <div class="col-span-full glass-effect rounded-2xl p-12 text-center">
                <i class="fas fa-sitemap text-gray-600 text-4xl mb-3"></i>
                <p class="text-gray-400">Belum ada sub-jabatan untuk hospital ini.</p>
            </div>
            @endforelse
        </div>

    </div>
</div>
@endsection
