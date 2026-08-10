@extends('layouts.app')

@section('title', 'Direktori Anggota - Portal Medis')

@section('content')
<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-8">
        
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2">
                    <i class="fas fa-user-md mr-3 text-sky-400"></i>Direktori Dokter & Manajemen
                </h1>
                <p class="text-sky-200">Pantau jadwal praktek, status duty, dan profil jajaran Dokter hingga Manajemen</p>
            </div>
        </div>

        {{-- Filter & Search Container --}}
        <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-2xl border border-white border-opacity-20 p-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                
                {{-- Hospital Filter Tabs --}}
                <div class="flex items-center gap-2 bg-black bg-opacity-20 p-1.5 rounded-xl border border-white border-opacity-10 self-start">
                    <a href="{{ route('staff.members.index', ['hospital' => 'all', 'search' => $search]) }}" 
                       class="px-4 py-2 rounded-lg text-sm font-semibold transition-all {{ $hospital === 'all' ? 'bg-gradient-to-r from-sky-500 to-cyan-500 text-white shadow-md' : 'text-sky-200 hover:text-white' }}">
                        <i class="fas fa-globe mr-1.5"></i> Semua
                    </a>
                    <a href="{{ route('staff.members.index', ['hospital' => 'alta', 'search' => $search]) }}" 
                       class="px-4 py-2 rounded-lg text-sm font-semibold transition-all {{ $hospital === 'alta' ? 'bg-cyan-600 text-white shadow-md' : 'text-sky-200 hover:text-white' }}">
                        <i class="fas fa-hospital mr-1.5 text-cyan-300"></i> Alta Hospital
                    </a>
                    <a href="{{ route('staff.members.index', ['hospital' => 'roxwood', 'search' => $search]) }}" 
                       class="px-4 py-2 rounded-lg text-sm font-semibold transition-all {{ $hospital === 'roxwood' ? 'bg-emerald-600 text-white shadow-md' : 'text-sky-200 hover:text-white' }}">
                        <i class="fas fa-hospital-alt mr-1.5 text-emerald-300"></i> Roxwood Hospital
                    </a>
                </div>

                {{-- Search Input --}}
                <form method="GET" action="{{ route('staff.members.index') }}" class="w-full md:w-80 flex gap-2">
                    <input type="hidden" name="hospital" value="{{ $hospital }}">
                    <div class="relative flex-1">
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}"
                               placeholder="Cari nama atau jabatan..." 
                               class="w-full bg-white bg-opacity-10 text-white placeholder-sky-300 text-sm rounded-xl pl-10 pr-4 py-2.5 border border-white border-opacity-20 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:border-transparent transition-all"
                        />
                        <div class="absolute left-3.5 top-3.5 text-sky-300">
                            <i class="fas fa-search text-xs"></i>
                        </div>
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-semibold rounded-xl text-sm transition-all shadow-md">
                        Cari
                    </button>
                </form>
            </div>
        </div>

        {{-- Members Grid --}}
        @if(count($members) > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($members as $member)
                    <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-2xl border border-white border-opacity-20 p-5 shadow-xl hover:bg-opacity-15 transition-all duration-300 flex flex-col justify-between overflow-hidden relative group">
                        
                        {{-- Top Accent Line --}}
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r {{ $member->hospital === 'roxwood' ? 'from-emerald-400 to-teal-400' : 'from-sky-400 to-cyan-400' }}"></div>

                        <div>
                            {{-- User Header --}}
                            <div class="flex items-start justify-between mb-4">
                                <div class="relative shrink-0">
                                    <img src="{{ $member->profile_image_url }}" 
                                         onerror="{{ $member->profile_image_on_error }}"
                                         alt="{{ $member->name }}" 
                                         class="w-14 h-14 rounded-2xl object-cover border border-white border-opacity-20 shadow-md group-hover:scale-105 transition-all duration-300"
                                    />
                                    {{-- Online indicator: diperbarui real-time via JS polling --}}
                                    @if($member->isOnline())
                                        <span class="online-dot absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-400 border-2 border-slate-900 rounded-full shadow-[0_0_8px_rgba(52,211,153,0.8)] transition-all duration-500" title="Online" data-user-id="{{ $member->id }}"></span>
                                    @else
                                        <span class="online-dot absolute -bottom-1 -right-1 w-4 h-4 bg-slate-500 border-2 border-slate-900 rounded-full transition-all duration-500" title="Offline" data-user-id="{{ $member->id }}"></span>
                                    @endif
                                </div>
                                
                                <div class="flex flex-col items-end gap-1">
                                    {{-- Hospital Tag --}}
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wide uppercase border {{ $member->hospital === 'roxwood' ? 'bg-emerald-500 bg-opacity-20 text-emerald-300 border-emerald-500 border-opacity-30' : 'bg-cyan-500 bg-opacity-20 text-cyan-300 border-cyan-500 border-opacity-30' }}">
                                        {{ $member->hospital === 'roxwood' ? 'Roxwood' : 'Alta' }}
                                    </span>
                                    {{-- On Duty Status --}}
                                    @if($member->isClockedIn())
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500 bg-opacity-20 text-amber-300 border border-amber-500 border-opacity-30 animate-pulse">
                                            ON DUTY
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- User Info --}}
                            <div>
                                <h3 class="font-extrabold text-white text-base truncate group-hover:text-sky-300 transition-colors">
                                    {{ $member->name }}
                                </h3>
                                <p class="text-sky-300 text-xs mt-0.5">ID: {{ $member->staff_id ?? '-' }}</p>
                                
                                {{-- Role Badge --}}
                                <div class="mt-2.5">
                                    @php
                                        $level = $member->role->level ?? 0;
                                        $badgeColor = 'bg-white bg-opacity-10 text-sky-200 border-white border-opacity-20';
                                        if ($level >= 4) {
                                            $badgeColor = 'bg-rose-500 bg-opacity-20 text-rose-300 border-rose-500 border-opacity-30';
                                        } elseif ($level === 3) {
                                            $badgeColor = 'bg-purple-500 bg-opacity-20 text-purple-300 border-purple-500 border-opacity-30';
                                        } elseif ($level === 2) {
                                            $badgeColor = 'bg-indigo-500 bg-opacity-20 text-indigo-300 border-indigo-500 border-opacity-30';
                                        } elseif ($level === 1) {
                                            $badgeColor = 'bg-sky-500 bg-opacity-20 text-sky-300 border-sky-500 border-opacity-30';
                                        }
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold border {{ $badgeColor }}">
                                        {{ $member->role->display_name ?? 'Staff' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Doctor Schedules --}}
                        @php
                            $doctorSchedules = isset($schedules) ? ($schedules->get($member->name) ?? collect()) : collect();
                        @endphp
                        @if($doctorSchedules->count() > 0)
                            <div class="mt-4 pt-3 border-t border-white border-opacity-10">
                                <p class="text-[10px] uppercase tracking-widest text-sky-400 font-bold mb-2">
                                    <i class="far fa-calendar-alt mr-1"></i> Jadwal Praktek
                                </p>
                                <div class="space-y-2">
                                    @foreach($doctorSchedules as $schedule)
                                        <div class="bg-black bg-opacity-20 rounded-lg p-2 border border-white border-opacity-5">
                                            <p class="text-[11px] text-white font-semibold mb-1">
                                                {{ str_replace('🩺 ', '', $schedule->poli) }}
                                            </p>
                                            <div class="flex flex-wrap gap-1 mb-1.5">
                                                @foreach($schedule->day as $day)
                                                    <span class="text-[9px] bg-white bg-opacity-10 px-1.5 py-0.5 rounded text-sky-100">{{ $day }}</span>
                                                @endforeach
                                            </div>
                                            <p class="text-[10px] text-sky-300">
                                                <i class="far fa-clock mr-1 text-[9px]"></i>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }} WIB
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Stats & Actions --}}
                        <div class="mt-4 pt-4 border-t border-white border-opacity-10">
                            <div class="flex justify-between items-center text-xs mb-4">
                                <span class="text-sky-200">Total Jam Kerja:</span>
                                <span class="font-bold text-white tracking-wide">
                                    {{ $member->getTotalDutyHoursFormatted() }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <a href="{{ route('staff.members.show', $member->id) }}" 
                                   class="px-3 py-2 bg-white bg-opacity-10 hover:bg-opacity-20 text-white rounded-xl text-xs font-bold transition-all border border-white border-opacity-20 flex items-center justify-center gap-1.5">
                                    <i class="fas fa-user-circle"></i>
                                    Profil
                                </a>
                                <a href="{{ route('staff.messages.index', ['user' => $member->id]) }}" 
                                   class="px-3 py-2 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1.5 shadow-md">
                                    <i class="fas fa-paper-plane"></i>
                                    Chat
                                </a>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $members->links('vendor.pagination.tailwind') }}
            </div>
        @else
            <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-2xl border border-white border-opacity-20 p-12 text-center">
                <div class="w-16 h-16 bg-sky-500 bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4 border border-sky-500 border-opacity-30">
                    <i class="fas fa-users-slash text-2xl text-sky-300"></i>
                </div>
                <h3 class="text-lg font-bold text-white mb-1">Tidak Ada Anggota Ditemukan</h3>
                <p class="text-sky-200 text-sm max-w-md mx-auto">
                    Coba sesuaikan kata kunci pencarian Anda atau periksa filter kategori rumah sakit yang Anda gunakan.
                </p>
            </div>
        @endif

    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    function refreshOnlineStatus() {
        fetch('/api-online-users', {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json' }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            var onlineIds = data.online_ids || [];
            document.querySelectorAll('.online-dot').forEach(function (dot) {
                var userId = parseInt(dot.getAttribute('data-user-id'));
                if (onlineIds.indexOf(userId) !== -1) {
                    // Online
                    dot.className = 'online-dot absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-400 border-2 border-slate-900 rounded-full shadow-[0_0_8px_rgba(52,211,153,0.8)] transition-all duration-500';
                    dot.setAttribute('title', 'Online');
                } else {
                    // Offline
                    dot.className = 'online-dot absolute -bottom-1 -right-1 w-4 h-4 bg-slate-500 border-2 border-slate-900 rounded-full transition-all duration-500';
                    dot.setAttribute('title', 'Offline');
                }
            });
        })
        .catch(function () {});
    }

    // Cek status online setiap 30 detik
    setInterval(refreshOnlineStatus, 30000);
})();
</script>
@endpush
