@extends('layouts.app')
@section('title', 'Credit Score Anggota')

@section('content')
<div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700"></div>
    <div class="absolute inset-0 bg-black bg-opacity-20"></div>

    <div class="relative max-w-6xl mx-auto text-white">

        {{-- Header --}}
        <div class="glass-effect rounded-2xl p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1">Credit Score Anggota</h1>
                    <p class="text-sky-200 text-sm">Kelola dan pantau Credit Score seluruh anggota {{ ucfirst($hospital) }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-400">Total Anggota</p>
                    <p class="text-2xl font-bold text-white">{{ $members->total() }}</p>
                </div>
            </div>

            {{-- Filter --}}
            <form method="GET" class="mt-4 flex flex-wrap gap-3">
                <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama / staff ID..."
                       class="flex-1 min-w-[200px] bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-sky-400">
                <select name="hospital" class="bg-white/10 text-white border border-white/20 rounded-lg px-4 py-2.5 text-sm appearance-none focus:ring-2 focus:ring-sky-400">
                    <option value="alta" {{ $hospital==='alta'?'selected':'' }} class="bg-sky-900">Alta</option>
                    <option value="roxwood" {{ $hospital==='roxwood'?'selected':'' }} class="bg-sky-900">Roxwood</option>
                </select>
                <button type="submit" class="px-4 py-2.5 bg-sky-500 hover:bg-sky-400 text-white rounded-lg font-semibold text-sm transition-all">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
            </form>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-green-500/20 border border-green-500/40 rounded-xl px-4 py-3 text-green-300 text-sm flex items-center gap-2">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        {{-- Tabel --}}
        <div class="glass-effect rounded-2xl overflow-hidden">
            <table class="w-full text-sm hidden sm:table">
                <thead>
                    <tr class="border-b border-white/10 bg-white/5">
                        <th class="text-left px-5 py-4 text-gray-300 text-xs uppercase tracking-wide font-semibold">#</th>
                        <th class="text-left px-5 py-4 text-gray-300 text-xs uppercase tracking-wide font-semibold">Anggota</th>
                        <th class="text-left px-5 py-4 text-gray-300 text-xs uppercase tracking-wide font-semibold">Jabatan</th>
                        <th class="text-left px-5 py-4 text-gray-300 text-xs uppercase tracking-wide font-semibold">Divisi</th>
                        <th class="text-center px-5 py-4 text-gray-300 text-xs uppercase tracking-wide font-semibold">Credit Score</th>
                        <th class="text-center px-5 py-4 text-gray-300 text-xs uppercase tracking-wide font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($members as $member)
                    @php
                        $balance = $member->creditScore?->balance ?? 100;
                        $scoreColor = $balance >= 85 ? 'text-green-400' : ($balance >= 80 ? 'text-yellow-400' : 'text-red-400');
                        $scoreBg   = $balance >= 85 ? 'bg-green-500/20 border-green-500/30' : ($balance >= 80 ? 'bg-yellow-500/20 border-yellow-500/30' : 'bg-red-500/20 border-red-500/30');
                    @endphp
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-5 py-3 text-gray-500 text-xs">{{ ($members->currentPage()-1)*$members->perPage()+$loop->iteration }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-sky-500/30 to-cyan-500/30 flex items-center justify-center border border-white/20 text-white text-xs font-bold flex-shrink-0">
                                    {{ strtoupper(substr($member->name,0,2)) }}
                                </div>
                                <div>
                                    <p class="text-white font-medium">{{ $member->name }}</p>
                                    @if($member->staff_id)<p class="text-gray-400 text-xs">{{ $member->staff_id }}</p>@endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-sky-500/20 text-sky-300 border border-sky-500/30">
                                {{ $member->role?->display_name ?? '-' }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            @if($member->subRole)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold" style="{{ $member->subRole->badge_style }}">
                                    {{ $member->subRole->short_name }}
                                </span>
                            @else
                                <span class="text-gray-500 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-center">
                            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-sm font-black border {{ $scoreBg }} {{ $scoreColor }}">
                                <i class="fas fa-star text-xs"></i> {{ $balance }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('credit-score.show', $member) }}"
                                   class="px-3 py-1.5 bg-sky-500/20 hover:bg-sky-500/40 text-sky-300 rounded-lg text-xs font-semibold transition-all border border-sky-500/30">
                                    Detail
                                </a>
                                @if(auth()->user()->isInDivision('comdis') || auth()->user()->isAdmin() || auth()->user()->isExecutiveOrAbove())
                                <a href="{{ route('credit-score.input', $member) }}"
                                   class="px-3 py-1.5 bg-amber-500/20 hover:bg-amber-500/40 text-amber-300 rounded-lg text-xs font-semibold transition-all border border-amber-500/30">
                                    Input
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Mobile cards --}}
            <div class="sm:hidden divide-y divide-white/10">
                @foreach($members as $member)
                @php $balance = $member->creditScore?->balance ?? 100; $scoreColor = $balance >= 85 ? 'text-green-400' : ($balance >= 80 ? 'text-yellow-400' : 'text-red-400'); @endphp
                <div class="p-4 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-sky-500/30 to-cyan-500/30 flex items-center justify-center border border-white/20 text-white text-xs font-bold flex-shrink-0">
                            {{ strtoupper(substr($member->name,0,2)) }}
                        </div>
                        <div>
                            <p class="text-white font-medium text-sm">{{ $member->name }}</p>
                            <p class="text-gray-400 text-xs">{{ $member->role?->display_name ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="font-black text-lg {{ $scoreColor }}">{{ $balance }}</span>
                        <a href="{{ route('credit-score.show', $member) }}" class="px-2 py-1 bg-sky-500/20 text-sky-300 rounded-lg text-xs font-semibold border border-sky-500/30">→</a>
                    </div>
                </div>
                @endforeach
            </div>

            @if($members->hasPages())
            <div class="px-5 py-4 border-t border-white/10 flex items-center justify-between text-xs text-gray-400">
                <span>{{ $members->firstItem() }}–{{ $members->lastItem() }} dari {{ $members->total() }}</span>
                <div class="flex gap-1">
                    @if(!$members->onFirstPage())<a href="{{ $members->previousPageUrl() }}" class="px-3 py-1.5 bg-white/10 hover:bg-white/20 rounded-lg text-white">‹</a>@endif
                    @if($members->hasMorePages())<a href="{{ $members->nextPageUrl() }}" class="px-3 py-1.5 bg-white/10 hover:bg-white/20 rounded-lg text-white">›</a>@endif
                </div>
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
