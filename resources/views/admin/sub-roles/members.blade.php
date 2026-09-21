@extends('layouts.app')
@section('title', 'Anggota Divisi ' . $subRole->short_name)

@section('content')
<div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700"></div>
    <div class="absolute inset-0 bg-black bg-opacity-20"></div>

    <div class="relative max-w-5xl mx-auto text-white">

        {{-- Header --}}
        <div class="glass-effect rounded-2xl p-6 mb-6">
            <div class="flex items-center gap-4 mb-4">
                <a href="{{ route('admin.sub-roles.index') }}" class="text-sky-400 hover:text-sky-300 text-sm">
                    ← Kembali
                </a>
            </div>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center font-black text-lg"
                     style="background-color: {{ $subRole->color }}25; border: 2px solid {{ $subRole->color }}50; color: {{ $subRole->color }}">
                    {{ $subRole->short_name }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">{{ $subRole->display_name }}</h1>
                    <p class="text-sky-200 text-sm">{{ $members->total() }} anggota terdaftar</p>
                </div>
            </div>

            {{-- Search --}}
            <form method="GET" class="mt-4">
                <input type="text" name="q" value="{{ $search }}"
                       placeholder="Cari nama / staff ID..."
                       class="w-full sm:w-72 bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-sky-400">
            </form>
        </div>

        {{-- Tabel --}}
        <div class="glass-effect rounded-2xl overflow-hidden">
            @if($members->isEmpty())
                <div class="text-center py-16">
                    <i class="fas fa-users text-gray-600 text-4xl mb-3"></i>
                    <p class="text-gray-400">Belum ada anggota di divisi ini.</p>
                    <a href="{{ route('admin.sub-roles.assign') }}" class="mt-3 inline-block text-sky-400 text-sm hover:underline">Assign anggota →</a>
                </div>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-white/10 bg-white/5">
                            <th class="text-left px-5 py-4 text-gray-300 text-xs uppercase tracking-wide font-semibold">#</th>
                            <th class="text-left px-5 py-4 text-gray-300 text-xs uppercase tracking-wide font-semibold">Staf</th>
                            <th class="text-left px-5 py-4 text-gray-300 text-xs uppercase tracking-wide font-semibold">Jabatan Utama</th>
                            <th class="text-left px-5 py-4 text-gray-300 text-xs uppercase tracking-wide font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($members as $i => $member)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-5 py-3 text-gray-500 text-xs">
                                {{ ($members->currentPage() - 1) * $members->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-sky-500/40 to-cyan-500/40 flex items-center justify-center border border-white/20 text-white text-xs font-bold flex-shrink-0">
                                        {{ strtoupper(substr($member->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="text-white font-medium">{{ $member->name }}</p>
                                        @if($member->staff_id)
                                            <p class="text-gray-400 text-xs">{{ $member->staff_id }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-sky-500/20 text-sky-300 border border-sky-500/30">
                                    {{ $member->role?->display_name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                @if($member->is_active)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-500/20 text-green-300 border border-green-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-500/20 text-red-300 border border-red-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> Nonaktif
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($members->hasPages())
                <div class="px-5 py-4 border-t border-white/10 flex items-center justify-between text-xs text-gray-400">
                    <span>{{ $members->firstItem() }}–{{ $members->lastItem() }} dari {{ $members->total() }}</span>
                    <div class="flex gap-1">
                        @if(!$members->onFirstPage())
                            <a href="{{ $members->previousPageUrl() }}" class="px-3 py-1.5 bg-white/10 hover:bg-white/20 rounded-lg text-white transition-colors">‹</a>
                        @endif
                        @if($members->hasMorePages())
                            <a href="{{ $members->nextPageUrl() }}" class="px-3 py-1.5 bg-white/10 hover:bg-white/20 rounded-lg text-white transition-colors">›</a>
                        @endif
                    </div>
                </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
