@extends('layouts.app')

@section('title', 'Manajemen Staf - Portal Medis MPK-BA')

@section('content')
<div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700"></div>
    <div class="absolute inset-0 bg-black bg-opacity-20"></div>

    <div class="relative max-w-7xl w-full mx-auto text-white">
        <!-- Header Section -->
        <div class="glass-effect rounded-2xl elegant-shadow-lg p-4 sm:p-6 md:p-8 mb-6 sm:mb-8">
            <div class="flex flex-col space-y-4 sm:space-y-0 sm:flex-row sm:items-center justify-between mb-6">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-2">Manajemen Staf</h1>
                    <p class="text-sky-200 text-base sm:text-lg">Kelola data staf dan akun mereka dengan mudah</p>
                </div>
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <div class="text-right">
                        <p class="text-gray-300 text-sm">Total Staf</p>
                        <p class="text-xl sm:text-2xl font-bold text-white">{{ $staff->total() }}</p>
                    </div>
                    <a href="{{ route('admin.staff.export', request()->query()) }}" class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white rounded-lg font-semibold transition-all duration-300 shadow-lg">
                        <i class="fas fa-file-excel mr-2"></i><span class="hidden xs:inline">Export Excel</span><span class="xs:hidden">Export</span>
                    </a>
                    <a href="{{ route('admin.staff.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-3 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white rounded-lg font-semibold transition-all duration-300 shadow-lg">
                        <i class="fas fa-plus mr-2"></i><span class="hidden xs:inline">Tambah Staf</span><span class="xs:hidden">Tambah</span>
                    </a>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="glass-effect rounded-xl elegant-shadow-lg p-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-check-circle text-green-400 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-gray-300 text-sm">Aktif</p>
                            <p class="text-xl font-bold text-white">{{ $staff->where('is_active', true)->count() }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="glass-effect rounded-xl elegant-shadow-lg p-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-red-500/20 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-times-circle text-red-400 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-gray-300 text-sm">Nonaktif</p>
                            <p class="text-xl font-bold text-white">{{ $staff->where('is_active', false)->count() }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="glass-effect rounded-xl elegant-shadow-lg p-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-user-shield text-blue-400 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-gray-300 text-sm">Admin</p>
                            <p class="text-xl font-bold text-white">{{ $staff->where('role.name', 'admin')->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filter -->
            <form method="GET" action="{{ route('admin.staff.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="sm:col-span-2 lg:col-span-1">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau email..." 
                           class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 text-sm">
                </div>
                <div class="sm:col-span-2 lg:col-span-1">
                    <select name="role" class="w-full bg-white/10 text-white border border-white/20 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 appearance-none text-sm">
                        <option value="">Semua Peran</option>
                        @isset($roles)
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" @selected(request('role') == $role->id) class="bg-slate-800 text-slate-100">{{ $role->display_name ?? $role->name }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
                <div class="sm:col-span-2 lg:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white rounded-lg font-semibold transition-all duration-300 text-sm">
                        <i class="fas fa-search mr-2"></i><span class="hidden xs:inline">Cari</span>
                    </button>
                    <a href="{{ route('admin.staff.index') }}" class="inline-flex items-center justify-center px-4 py-3 bg-white/10 text-white rounded-lg border border-white/20 hover:bg-white/20 transition-all duration-300">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- Staff List -->
        <div class="glass-effect rounded-2xl elegant-shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-black/20">
                        <tr>
                            <th class="px-3 sm:px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Staf</th>
                            <th class="hidden sm:table-cell px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Peran</th>
                            <th class="hidden md:table-cell px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="hidden lg:table-cell px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Terakhir Login</th>
                            <th class="px-3 sm:px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse($staff as $user)
                            <tr class="hover:bg-white/5 transition-colors duration-200">
                                <td class="px-3 sm:px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 sm:w-12 sm:h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center mr-3 sm:mr-4">
                                            <i class="fas fa-user text-white text-sm sm:text-lg"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-white font-semibold text-sm sm:text-lg truncate">{{ $user->name }}</p>
                                            <p class="text-gray-300 text-xs sm:text-sm truncate">{{ $user->email }}</p>
                                            <!-- Mobile: Show role and status -->
                                            <div class="sm:hidden mt-1 space-y-1">
                                                <div class="inline-flex items-center px-2 py-1 bg-sky-500/20 text-sky-300 rounded-full text-xs font-medium border border-sky-500/30">
                                                    <i class="fas fa-user-tag mr-1"></i>
                                                    {{ $user->role->display_name ?? $user->role->name ?? 'Tidak ada' }}
                                                </div>
                                                @if($user->is_active)
                                                    <div class="inline-flex items-center px-2 py-1 bg-green-500/20 text-green-300 rounded-full text-xs font-medium border border-green-500/30">
                                                        <i class="fas fa-check-circle mr-1"></i>Aktif
                                                    </div>
                                                @else
                                                    <div class="inline-flex items-center px-2 py-1 bg-red-500/20 text-red-300 rounded-full text-xs font-medium border border-red-500/30">
                                                        <i class="fas fa-times-circle mr-1"></i>Nonaktif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="hidden sm:table-cell px-6 py-4">
                                    <div class="inline-flex items-center px-3 py-1 bg-sky-500/20 text-sky-300 rounded-full text-sm font-medium border border-sky-500/30">
                                        <i class="fas fa-user-tag mr-2"></i>
                                        {{ $user->role->display_name ?? $user->role->name ?? 'Tidak ada' }}
                                    </div>
                                </td>
                                <td class="hidden md:table-cell px-6 py-4">
                                    @if($user->is_active)
                                        <div class="inline-flex items-center px-3 py-1 bg-green-500/20 text-green-300 rounded-full text-sm font-medium border border-green-500/30">
                                            <i class="fas fa-check-circle mr-2"></i>Aktif
                                        </div>
                                    @else
                                        <div class="inline-flex items-center px-3 py-1 bg-red-500/20 text-red-300 rounded-full text-sm font-medium border border-red-500/30">
                                            <i class="fas fa-times-circle mr-2"></i>Nonaktif
                                        </div>
                                    @endif
                                </td>
                                <td class="hidden lg:table-cell px-6 py-4">
                                    <div>
                                        <p class="text-white font-medium text-sm">{{ $user->updated_at->format('d M Y') }}</p>
                                        <p class="text-gray-400 text-xs">{{ $user->updated_at->setTimezone('Asia/Jakarta')->format('H:i') }} WIB</p>
                                    </div>
                                </td>
                                <td class="px-3 sm:px-6 py-4">
                                    <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-2">
                                        <!-- Mobile: Stack actions vertically -->
                                        <div class="flex flex-wrap gap-1 sm:gap-2">
                                            <!-- Toggle Active -->
                                            <form method="POST" action="{{ route('admin.staff.toggle-active', $user) }}" class="inline">
                                                @csrf
                                                <button class="inline-flex items-center px-2 sm:px-3 py-1 sm:py-2 {{ $user->is_active ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-green-500 hover:bg-green-600' }} text-white rounded-lg text-xs sm:text-sm font-medium transition-all duration-300 shadow-lg">
                                                    <i class="fas fa-{{ $user->is_active ? 'pause' : 'play' }} mr-1 sm:mr-2"></i>
                                                    <span class="hidden xs:inline">{{ $user->is_active ? 'Nonaktif' : 'Aktif' }}</span>
                                                </button>
                                            </form>

                                            <!-- Edit -->
                                            <a href="{{ route('admin.staff.edit', $user) }}" class="inline-flex items-center px-2 sm:px-3 py-1 sm:py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-lg text-xs sm:text-sm font-medium transition-all duration-300 shadow-lg">
                                                <i class="fas fa-edit mr-1 sm:mr-2"></i><span class="hidden xs:inline">Edit</span>
                                            </a>

                                            <!-- More Actions Dropdown -->
                                            <div class="relative inline-block text-left">
                                                <button type="button" class="inline-flex items-center px-2 sm:px-3 py-1 sm:py-2 rounded-lg bg-white/10 hover:bg-white/20 border border-white/20 text-white text-xs sm:text-sm font-medium transition-all duration-300 action-menu-btn">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <div class="hidden absolute right-0 mt-2 w-32 sm:w-40 rounded-lg bg-slate-900/95 border border-white/10 shadow-xl z-20 action-menu">
                                                    <div class="py-1">
                                                        <form method="POST" action="{{ route('admin.staff.reset-password', $user) }}" onsubmit="return confirm('Reset password untuk {{ $user->name }}? Password baru akan ditampilkan.');">
                                                            @csrf
                                                            <button class="w-full text-left px-3 py-2 text-xs sm:text-sm hover:bg-white/10">
                                                                <i class="fas fa-key mr-2"></i>Reset Password
                                                            </button>
                                                        </form>
                                                        
                                                        @php
                                                            $hasRolePermission = $user->role && in_array('reply_livechat', $user->role->permissions ?? []);
                                                            $hasCustomPermission = in_array('reply_livechat', $user->custom_permissions ?? []);
                                                        @endphp
                                                        
                                                        @if(!$hasRolePermission)
                                                            <form method="POST" action="{{ route('admin.users.toggle-chat-permission', $user) }}">
                                                                @csrf
                                                                <button class="w-full text-left px-3 py-2 text-xs sm:text-sm hover:bg-white/10">
                                                                    @if($hasCustomPermission)
                                                                        <span class="text-yellow-400"><i class="fas fa-comment-slash mr-2"></i>Cabut Akses Chat</span>
                                                                    @else
                                                                        <span class="text-green-400"><i class="fas fa-comment-medical mr-2"></i>Beri Akses Chat</span>
                                                                    @endif
                                                                </button>
                                                            </form>
                                                        @endif

                                                        <form method="POST" action="{{ route('admin.staff.destroy', $user) }}" onsubmit="return confirm('Hapus staf {{ $user->name }}?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="w-full text-left px-3 py-2 text-xs sm:text-sm text-red-300 hover:bg-red-500/10">
                                                                <i class="fas fa-trash mr-2"></i>Hapus
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-users text-white text-2xl"></i>
                                        </div>
                                        <h3 class="text-xl font-semibold text-white mb-2">Tidak Ada Staf</h3>
                                        <p class="text-sky-200 mb-6">Mulai dengan menambahkan staf pertama</p>
                                        <a href="{{ route('admin.staff.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white rounded-lg font-semibold transition-all duration-300 shadow-lg">
                                            <i class="fas fa-plus mr-2"></i>Tambah Staf Pertama
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($staff->hasPages())
                <div class="px-6 py-4 bg-black/20 border-t border-white/10">
                    {{ $staff->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
// Dropdown menu toggling - Inline untuk ensure execution
(function() {
    'use strict';
    
    console.log('[Staff Page] Dropdown script initialized');
    
    function initDropdowns() {
        // Click handler
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.action-menu-btn');
            const allMenus = document.querySelectorAll('.action-menu');
            
            if (btn) {
                e.stopPropagation();
                e.preventDefault();
                
                const menu = btn.parentElement.querySelector('.action-menu');
                console.log('[Staff Page] Dropdown button clicked, menu found:', !!menu);
                
                // Close all other menus
                allMenus.forEach(m => {
                    if (m !== menu) {
                        m.classList.add('hidden');
                    }
                });
                
                // Toggle clicked menu
                if (menu) {
                    const wasHidden = menu.classList.contains('hidden');
                    menu.classList.toggle('hidden');
                    console.log('[Staff Page] Menu toggled to:', wasHidden ? 'visible' : 'hidden');
                }
                
                return false;
            }
            
            // Click outside - close all
            allMenus.forEach(m => m.classList.add('hidden'));
        });
        
        console.log('[Staff Page] Click handler registered successfully');
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDropdowns);
    } else {
        initDropdowns();
    }
})();
</script>

@endsection
