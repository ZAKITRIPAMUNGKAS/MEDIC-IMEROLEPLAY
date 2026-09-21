@extends('layouts.app')
@section('title', 'Assign Sub-Jabatan ke Staf')

@section('content')
<div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700"></div>
    <div class="absolute inset-0 bg-black bg-opacity-20"></div>

    <div class="relative max-w-5xl mx-auto text-white">

        {{-- Header --}}
        <div class="glass-effect rounded-2xl p-6 mb-6">
            <div class="flex items-center gap-3 mb-1">
                <a href="{{ route('admin.sub-roles.index') }}" class="text-sky-400 hover:text-sky-300 text-sm">← Kembali</a>
            </div>
            <h1 class="text-2xl font-bold text-white">Assign Sub-Jabatan ke Staf</h1>
            <p class="text-sky-200 text-sm mt-1">Setiap staf hanya dapat memegang satu sub-jabatan (divisi).</p>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-green-500/20 border border-green-500/40 rounded-xl px-4 py-3 text-green-300 text-sm flex items-center gap-2">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-500/20 border border-red-500/40 rounded-xl px-4 py-3 text-red-300 text-sm flex items-center gap-2">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        {{-- Form Bulk Assign --}}
        <form method="POST" action="{{ route('admin.sub-roles.assign-bulk') }}">
            @csrf
            <div class="glass-effect rounded-2xl overflow-hidden">
                <div class="px-5 py-4 border-b border-white/10 bg-white/5 flex items-center justify-between">
                    <p class="text-sm font-semibold text-white">{{ $staffList->count() }} Staf {{ ucfirst($hospital) }}</p>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-sky-500 hover:bg-sky-400 text-white rounded-lg font-semibold text-sm transition-all">
                        <i class="fas fa-save"></i> Simpan Semua
                    </button>
                </div>

                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-white/10 bg-white/5">
                            <th class="text-left px-5 py-3 text-gray-300 text-xs uppercase tracking-wide font-semibold">Staf</th>
                            <th class="text-left px-5 py-3 text-gray-300 text-xs uppercase tracking-wide font-semibold">Jabatan Utama</th>
                            <th class="text-left px-5 py-3 text-gray-300 text-xs uppercase tracking-wide font-semibold w-56">Sub-Jabatan / Divisi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($staffList as $idx => $staf)
                        <input type="hidden" name="assignments[{{ $idx }}][user_id]" value="{{ $staf->id }}">
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-sky-500/30 to-cyan-500/30 flex items-center justify-center border border-white/20 text-white text-xs font-bold flex-shrink-0">
                                        {{ strtoupper(substr($staf->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="text-white font-medium text-sm">{{ $staf->name }}</p>
                                        @if($staf->staff_id)
                                            <p class="text-gray-400 text-xs">{{ $staf->staff_id }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-sky-500/20 text-sky-300 border border-sky-500/30">
                                    {{ $staf->role?->display_name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <select name="assignments[{{ $idx }}][sub_role_id]"
                                        class="w-full bg-white/10 text-white border border-white/20 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-sky-400 appearance-none">
                                    <option value="" class="bg-sky-900">— Tidak ada —</option>
                                    @foreach($subRoles as $sub)
                                        <option value="{{ $sub->id }}" class="bg-sky-900"
                                            {{ $staf->sub_role_id == $sub->id ? 'selected' : '' }}>
                                            {{ $sub->short_name }} — {{ $sub->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="px-5 py-4 border-t border-white/10 flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-sky-500 hover:bg-sky-400 text-white rounded-lg font-semibold text-sm transition-all shadow-lg">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>
@endsection
