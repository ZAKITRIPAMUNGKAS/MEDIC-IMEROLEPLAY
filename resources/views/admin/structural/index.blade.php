@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/print-structural.css') }}">
<div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        {{-- Header with Actions --}}
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                        <i class="fas fa-sitemap text-sky-400"></i>
                        Manajemen Struktural EMS
                    </h1>
                    <p class="text-gray-300 mt-2">Kelola struktur organisasi dengan mudah - visualisasikan hierarki tim Anda.</p>
                </div>
                
                <div class="flex gap-3">
                    <button onclick="window.print()" 
                            class="inline-flex items-center px-4 py-3 bg-white/5 hover:bg-white/10 text-white rounded-xl font-semibold transition-all border border-white/10">
                        <i class="fas fa-print mr-2"></i>
                        Print / PDF
                    </button>
                    <button onclick="openAddModal()" 
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Tambah Posisi Baru
                    </button>
                </div>
            </div>

            {{-- View Toggle & Filter --}}
            <div class="mt-8 flex flex-col sm:flex-row justify-between items-center gap-4 bg-white/5 p-2 rounded-2xl border border-white/10">
                {{-- View Switcher --}}
                <div class="flex bg-black/20 rounded-xl p-1">
                    <button onclick="switchView('grid')" id="btnGrid" class="px-6 py-2 rounded-lg text-sm font-medium transition-all duration-300 bg-sky-500 text-white shadow-lg">
                        <i class="fas fa-th-large mr-2"></i>Grid View
                    </button>
                    <button onclick="switchView('tree')" id="btnTree" class="px-6 py-2 rounded-lg text-sm font-medium transition-all duration-300 text-gray-400 hover:text-white">
                        <i class="fas fa-project-diagram mr-2"></i>Tree View
                    </button>
                </div>

                {{-- Search --}}
                <div class="relative w-full sm:w-96">
                    <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="searchInput" placeholder="Cari posisi atau staff..." 
                           class="w-full pl-12 pr-4 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                </div>
            </div>
        </div>

        {{-- Success/Error Messages --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-500/20 border border-green-500 text-green-100 rounded-xl flex items-center animate-fade-in-down">
                <i class="fas fa-check-circle mr-3 text-xl"></i>
                {{ session('success') }}
            </div>
        @endif

        {{-- VIEW 1: GRID VIEW (By Level) --}}
        <div id="gridView" class="space-y-8 animate-fade-in">
            @php
                // Group positions by level manually since controller structure might vary
                $groupedPositions = $positions->groupBy('level');
            @endphp

            @forelse($groupedPositions as $level => $levelPositions)
                <div class="level-group" data-level="{{ $level }}">
                    {{-- Level Header --}}
                    <div class="bg-gradient-to-r from-purple-600/30 to-pink-600/30 backdrop-blur-sm rounded-xl p-4 border border-purple-500/30 mb-4 flex items-center justify-between">
                        <h2 class="text-xl font-bold text-white flex items-center gap-3">
                            <span class="px-3 py-1 bg-purple-500/50 rounded-lg text-sm shadow-md">
                                Level {{ $level }}
                            </span>
                            <span class="text-purple-200 text-sm font-normal hidden sm:inline-block">
                                {{ $levelPositions->count() }} Posisi
                            </span>
                        </h2>
                        <div class="h-1 flex-1 mx-4 bg-white/5 rounded-full"></div>
                    </div>

                    {{-- Positions Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 position-container">
                        @foreach($levelPositions as $position)
                            <div class="position-card bg-white/10 backdrop-blur-sm rounded-xl border border-white/20 p-5 hover:border-sky-500/50 transition-all duration-300 hover:shadow-xl hover:shadow-sky-500/20 group relative overflow-hidden"
                                 data-id="{{ $position->id }}"
                                 data-title="{{ $position->title }}"
                                 data-user="{{ $position->user ? $position->user->name : '' }}">
                                
                                {{-- Background decoration --}}
                                <div class="absolute top-0 right-0 w-24 h-24 bg-white/5 rounded-full -mr-12 -mt-12 blur-2xl group-hover:bg-sky-500/10 transition-colors duration-500"></div>

                                {{-- Card Header --}}
                                <div class="flex items-start justify-between mb-4 relative z-10">
                                    <div class="flex-1">
                                        <h3 class="font-bold text-white text-lg leading-tight">{{ $position->title }}</h3>
                                        @if($position->position_name)
                                            <p class="text-xs text-sky-300 mt-1 uppercase tracking-wider font-semibold">{{ $position->position_name }}</p>
                                        @endif
                                    </div>
                                    <button class="text-gray-500 hover:text-white transition-colors cursor-move opacity-0 group-hover:opacity-100" title="Drag untuk reorder">
                                        <i class="fas fa-grip-vertical"></i>
                                    </button>
                                </div>

                                {{-- Connector Line (Visual) --}}
                                @if($position->parent)
                                    <div class="mb-3 flex items-center gap-2 text-xs text-gray-400 bg-black/20 p-2 rounded-lg">
                                        <i class="fas fa-level-up-alt rotate-90 text-sky-500"></i>
                                        <span>Bawahan dari: <span class="text-sky-300 font-medium">{{ $position->parent->title }}</span></span>
                                    </div>
                                @endif

                                {{-- Assigned User --}}
                                <div class="mb-5 relative z-10">
                                    @if($position->user)
                                        <div class="flex items-center gap-3">
                                            <div class="relative">
                                                @php
                                                    $avatarUrl = $position->user->profile_image 
                                                        ? asset('uploads/profile-images/' . basename($position->user->profile_image))
                                                        : 'https://ui-avatars.com/api/?name=' . urlencode($position->user->name) . '&background=0ea5e9&color=fff';
                                                @endphp
                                                <img src="{{ $avatarUrl }}" 
                                                     alt="{{ $position->user->name }}"
                                                     onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ str_replace(' ', '+', $position->user->name) }}&background=0ea5e9&color=fff';"
                                                     class="w-12 h-12 rounded-full border-2 border-green-500/50 object-cover shadow-md">
                                                <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-gray-800 rounded-full"></div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-white font-medium text-sm truncate">{{ $position->user->name }}</p>
                                                <p class="text-xs text-gray-400 truncate">{{ $position->user->role->display_name ?? $position->user->role->name ?? 'Staff' }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-3 opacity-60">
                                            <div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center border-2 border-dashed border-white/20">
                                                <i class="fas fa-user-plus text-gray-400"></i>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-gray-300 font-medium text-sm">Vacant Position</p>
                                                <p class="text-xs text-gray-500">Belum ada staff</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                {{-- Footer Stats & Actions --}}
                                <div class="flex items-center justify-between pt-4 border-t border-white/10 relative z-10">
                                    <div class="flex items-center gap-3 text-xs text-gray-400">
                                        <span title="{{ $position->children->count() }} bawahan langsung">
                                            <i class="fas fa-users mr-1 {{ $position->children->count() > 0 ? 'text-sky-400' : '' }}"></i>
                                            {{ $position->children->count() }}
                                        </span>
                                        <span class="w-1 h-1 bg-gray-600 rounded-full"></span>
                                        <span>Order: {{ $position->display_order }}</span>
                                    </div>

                                    <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                        <button onclick="openEditModal({{ $position->id }})" class="w-8 h-8 rounded-lg bg-sky-500/20 hover:bg-sky-500 text-sky-400 hover:text-white flex items-center justify-center transition-all">
                                            <i class="fas fa-pencil-alt text-xs"></i>
                                        </button>
                                        <button onclick="confirmDelete({{ $position->id }}, '{{ $position->title }}')" class="w-8 h-8 rounded-lg bg-red-500/20 hover:bg-red-500 text-red-400 hover:text-white flex items-center justify-center transition-all">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="text-center py-20 bg-white/5 rounded-3xl border border-white/10 border-dashed">
                    <i class="fas fa-sitemap text-6xl text-gray-600 mb-6"></i>
                    <h3 class="text-xl font-bold text-white mb-2">Struktur Masih Kosong</h3>
                    <p class="text-gray-400">Mulai bangun tim Anda dengan menambahkan posisi pertama.</p>
                    <button onclick="openAddModal()" class="mt-6 px-8 py-3 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-bold shadow-lg shadow-sky-500/20 transition-all">
                        Buat Root Position (Level 0)
                    </button>
                </div>
            @endforelse
        </div>

        {{-- VIEW 2: TREE VIEW (Hierarchical) --}}
        <div id="treeView" class="hidden space-y-6 animate-fade-in">
            <div class="bg-white/5 border border-white/10 rounded-2xl p-8 overflow-x-auto">
                {{-- Recursive Tree Macro --}}
                @php
                    if (!function_exists('renderTree')) {
                        function renderTree($nodes, $level = 0) {
                            if (empty($nodes)) return;
                            
                            echo '<ul class="pl-8 border-l-2 border-white/10 space-y-4 relative">';
                            foreach ($nodes as $node) {
                                $position = $node['position']; // Access the position object
                                $children = $node['children']; // Access children array
                                
                                $hasChildren = !empty($children);
                                $user = $position->user;
                                $borderColor = $user ? 'border-green-500/30' : 'border-amber-500/30';
                                $bgColor = $user ? 'bg-green-500/10' : 'bg-amber-500/10';
                                
                                echo '<li class="relative group">';
                                // Connector line
                                echo '<div class="absolute -left-8 top-8 w-8 h-0.5 bg-white/10"></div>';
                                
                                echo '<div class="bg-white/10 hover:bg-white/15 border border-white/20 hover:border-sky-500/50 rounded-xl p-4 transition-all duration-300 w-full sm:w-96 relative flex items-center gap-4 group-hover:shadow-lg hover:translate-x-1">';
                                    
                                    // User Avatar
                                    echo '<div class="relative flex-shrink-0">';
                                        if ($user) {
                                            $avatarUrl = $user->profile_image 
                                                ? asset('uploads/profile-images/' . basename($user->profile_image))
                                                : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0ea5e9&color=fff';
                                            echo "<img src='$avatarUrl' alt='{$user->name}' class='w-12 h-12 rounded-full border-2 border-green-500/50 object-cover'>";
                                        } else {
                                            echo '<div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center border-2 border-dashed border-white/20"><i class="fas fa-user-plus text-gray-400"></i></div>';
                                        }
                                        echo "<div class='absolute -top-2 -right-2 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold text-white bg-gray-700 border border-gray-600 shadow-sm' title='Level'>L{$position->level}</div>";
                                    echo '</div>';

                                    // Info
                                    echo '<div class="flex-1 min-w-0">';
                                        echo "<h4 class='text-white font-bold truncate' title='{$position->title}'>{$position->title}</h4>";
                                        if ($position->position_name) {
                                            echo "<p class='text-xs text-sky-400 truncate'>{$position->position_name}</p>";
                                        }
                                        echo "<p class='text-xs text-gray-400 mt-1 truncate'>" . ($user ? $user->name : 'Vacant') . "</p>";
                                    echo '</div>';

                                    // Action
                                    echo '<button onclick="openEditModal('.$position->id.')" class="w-8 h-8 rounded-lg bg-white/5 hover:bg-sky-500 text-gray-400 hover:text-white flex items-center justify-center transition-all opacity-0 group-hover:opacity-100"><i class="fas fa-pencil-alt text-xs"></i></button>';

                                echo '</div>';
                                
                                if ($hasChildren) {
                                    renderTree($children, $level + 1);
                                }
                                
                                echo '</li>';
                            }
                            echo '</ul>';
                        }
                    }
                @endphp

                <div class="tree-root">
                    @if(!empty($tree))
                        {{ renderTree($tree) }}
                    @else
                         <div class="text-center py-10">
                            <i class="fas fa-sitemap text-4xl text-gray-600 mb-4"></i>
                            <p class="text-gray-400">Tree view requires hierarchy data.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Add/Edit Modal --}}
<div id="positionModal" class="fixed inset-0 bg-black/80 backdrop-blur-md hidden items-center justify-center z-50 p-4 transition-opacity duration-300">
    <div class="bg-gray-900 rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto border border-white/10 shadow-2xl transform scale-100 transition-transform duration-300">
        <form id="positionForm" method="POST">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            
            {{-- Modal Header --}}
            <div class="sticky top-0 bg-gray-900/95 backdrop-blur-sm p-6 border-b border-white/10 flex justify-between items-center z-10">
                <h3 class="text-2xl font-bold text-white flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center shadow-lg">
                        <i class="fas fa-sitemap text-white text-lg"></i>
                    </span>
                    <span id="modalTitle">Tambah Posisi</span>
                </h3>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-8 space-y-6">
                 {{-- Hierarchy Section (Parent & Level) --}}
                <div class="bg-white/5 rounded-xl p-5 border border-white/10 space-y-4">
                    <h4 class="text-sm font-bold text-sky-400 uppercase tracking-wider mb-2">Hierarki Organisasi</h4>
                    
                    {{-- Parent Position --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                             Posisi Atasan (Parent)
                        </label>
                        <select name="parent_id" id="parent_id" onchange="autoSetLevel(this)"
                                class="w-full px-4 py-3 bg-black/40 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all font-mono text-sm">
                            <option value="" data-level="-1">⭐ Tidak ada (Root Level / Petinggi Utama)</option>
                            @php
                                // Helper to flatten tree for select options if needed, or just use flat list sorted by title/level
                                // Using $positions flat list is easier for select dropdown
                                $sortedPositions = $positions->sortBy('level');
                            @endphp
                            @foreach($sortedPositions as $pos)
                                <option value="{{ $pos->id }}" data-level="{{ $pos->level }}">
                                    Level {{ $pos->level }} — {{ $pos->title }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            Pilih atasan langsung. Level akan otomatis disesuaikan (Level Atasan + 1).
                        </p>
                    </div>

                    {{-- Level (Auto-set) --}}
                    <div>
                        <div class="flex justify-between">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Level (Auto)</label>
                            <span id="levelBadge" class="text-xs font-bold px-2 py-0.5 rounded bg-sky-500/20 text-sky-300">Level 0</span>
                        </div>
                        {{-- Hidden real input, and a dummy disabled input for display --}}
                        <input type="hidden" name="level" id="level_input" value="0"> 
                        <input type="text" id="level_display" value="Level 0 - High Command" disabled
                               class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-gray-400 cursor-not-allowed font-medium">
                    </div>
                </div>

                {{-- Position Details --}}
                <div class="space-y-4">
                    <h4 class="text-sm font-bold text-purple-400 uppercase tracking-wider mb-2">Detail Posisi</h4>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Nama Jabatan *</label>
                        <input type="text" name="title" id="title" required
                               placeholder="Contoh: Chief Executive Officer, Head of Surgery"
                               class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Departemen / Unit</label>
                        <input type="text" name="position_name" id="position_name"
                               placeholder="Contoh: Board of Directors, Medical Department"
                               class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all">
                    </div>
                </div>

                {{-- Assignment --}}
                <div class="bg-white/5 rounded-xl p-5 border border-white/10">
                    <h4 class="text-sm font-bold text-green-400 uppercase tracking-wider mb-4">Penugasan Staff</h4>
                    
                    <div class="relative">
                         <i class="fas fa-user-circle absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
                        <select name="user_id" id="user_id"
                                class="w-full pl-12 pr-4 py-3 bg-black/40 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-green-500 transition-all appearance-none">
                            <option value="">-- Kosongkan (Vacant) --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->name }} — {{ $user->role->display_name ?? $user->role->name ?? 'Staff' }}
                                </option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 pointer-events-none"></i>
                    </div>
                </div>

                {{-- Status --}}
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_active" id="is_active" value="1" checked
                           class="w-5 h-5 text-sky-500 bg-white/10 border-white/20 rounded focus:ring-sky-500 cursor-pointer">
                    <label for="is_active" class="text-sm font-medium text-gray-300 cursor-pointer select-none">
                        Posisi Aktif (Tampilkan di Struktur Publik)
                    </label>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="sticky bottom-0 bg-gray-900/95 backdrop-blur-sm p-6 border-t border-white/10 flex gap-3 z-10">
                <button type="button" onclick="closeModal()" 
                        class="flex-1 px-6 py-3 bg-white/5 hover:bg-white/10 text-gray-300 rounded-xl font-medium transition-all duration-300">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-sky-600 to-blue-600 hover:from-sky-700 hover:to-blue-700 text-white rounded-xl font-bold shadow-lg shadow-sky-900/20 transition-all duration-300 transform hover:scale-[1.02]">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Delete Modal --}}
<div id="deleteModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-gray-900 rounded-2xl max-w-sm w-full border border-white/10 shadow-2xl p-6 text-center transform scale-100 transition-all">
        <div class="w-20 h-20 bg-red-500/20 rounded-full flex items-center justify-center mx-auto mb-6 animate-pulse">
            <i class="fas fa-trash-alt text-3xl text-red-500"></i>
        </div>
        <h3 class="text-xl font-bold text-white mb-2">Hapus Posisi?</h3>
        <p class="text-gray-400 mb-8" id="deleteMessage">Tindakan ini tidak dapat dibatalkan.</p>
        
        <div class="flex gap-3">
            <button onclick="closeDeleteModal()" class="flex-1 px-5 py-3 bg-white/5 hover:bg-white/10 text-white rounded-xl font-medium transition-colors">Batal</button>
            <form id="deleteForm" method="POST" class="flex-1">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full px-5 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold shadow-lg shadow-red-900/20 transition-all">
                    Ya, Hapus
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // View Switcher Logic
    function switchView(view) {
        const gridView = document.getElementById('gridView');
        const treeView = document.getElementById('treeView');
        const btnGrid = document.getElementById('btnGrid');
        const btnTree = document.getElementById('btnTree');

        if (view === 'grid') {
            gridView.classList.remove('hidden');
            treeView.classList.add('hidden');
            
            btnGrid.classList.remove('text-gray-400', 'hover:text-white');
            btnGrid.classList.add('bg-sky-500', 'text-white', 'shadow-lg');
            
            btnTree.classList.add('text-gray-400', 'hover:text-white');
            btnTree.classList.remove('bg-sky-500', 'text-white', 'shadow-lg');
        } else {
            gridView.classList.add('hidden');
            treeView.classList.remove('hidden');
            
            btnTree.classList.remove('text-gray-400', 'hover:text-white');
            btnTree.classList.add('bg-sky-500', 'text-white', 'shadow-lg');
            
            btnGrid.classList.add('text-gray-400', 'hover:text-white');
            btnGrid.classList.remove('bg-sky-500', 'text-white', 'shadow-lg');
        }
    }

    // Auto Level Logic
    const levelDescriptions = {
        0: 'Level 0 - High Command / Board',
        1: 'Level 1 - Deputy / Vice',
        2: 'Level 2 - Department Heads',
        3: 'Level 3 - Unit Heads / Managers',
        4: 'Level 4 - Supervisors',
        5: 'Level 5 - Staff',
        6: 'Level 6 - Junior Staff',
        7: 'Level 7 - Support / Intern'
    };

    function autoSetLevel(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const parentLevel = parseInt(selectedOption.getAttribute('data-level'));
        
        let newLevel = 0;
        if (!isNaN(parentLevel) && parentLevel >= -1) { // -1 used for empty value check if needed, but here logic is simpler
             // If parent is selected, level = parentLevel + 1
             // If root (value=""), parentLevel won't be found or structured differently.
             // Let's check value
             if(selectElement.value === "") {
                 newLevel = 0;
             } else {
                 newLevel = parentLevel + 1;
             }
        }

        // Limit level to max 7
        if (newLevel > 7) newLevel = 7;

        // Update Inputs
        document.getElementById('level_input').value = newLevel;
        document.getElementById('level_display').value = levelDescriptions[newLevel] || `Level ${newLevel}`;
        
        // Update Badge
        const badge = document.getElementById('levelBadge');
        badge.textContent = `Level ${newLevel}`;
    }

    // Modal Functions
    function openAddModal() {
        document.getElementById('modalTitle').textContent = 'Tambah Posisi Baru';
        document.getElementById('positionForm').action = '{{ route("admin.structural.store") }}';
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('positionForm').reset();
        document.getElementById('is_active').checked = true;
        
        // Reset Level to 0
        const parentSelect = document.getElementById('parent_id');
        parentSelect.value = "";
        autoSetLevel(parentSelect);

        document.getElementById('positionModal').classList.remove('hidden');
        document.getElementById('positionModal').classList.add('flex');
    }

    function openEditModal(id) {
        document.getElementById('modalTitle').textContent = 'Edit Posisi';
        document.getElementById('positionForm').action = `/admin/structural/${id}`;
        document.getElementById('formMethod').value = 'PUT';
        
        fetch(`/admin/structural/${id}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.json();
            })
            .then(data => {
                document.getElementById('title').value = data.title || '';
                document.getElementById('position_name').value = data.position_name || '';
                document.getElementById('user_id').value = data.user_id || '';
                document.getElementById('parent_id').value = data.parent_id || '';
                document.getElementById('is_active').checked = data.is_active;

                // Trigger auto level to set displays correctly based on parent
                // Ideally backend sends level, but we enforce parent-child logic. 
                // However, to respect existing data, we should allow the level from DB if possible, 
                // BUT the new logic enforces structure. Let's rely on autoSetLevel for consistency 
                // OR set it manually if data.level exists.
                // Let's USE the existing level from data first.
                
                const level = data.level;
                document.getElementById('level_input').value = level;
                document.getElementById('level_display').value = levelDescriptions[level] || `Level ${level}`;
                 document.getElementById('levelBadge').textContent = `Level ${level}`;

                document.getElementById('positionModal').classList.remove('hidden');
                document.getElementById('positionModal').classList.add('flex');
            });
    }

    function closeModal() {
        document.getElementById('positionModal').classList.add('hidden');
        document.getElementById('positionModal').classList.remove('flex');
    }

    function confirmDelete(id, title) {
        document.getElementById('deleteMessage').textContent = `Yakin ingin menghapus "${title}"?`;
        document.getElementById('deleteForm').action = `/admin/structural/${id}`;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteModal').classList.add('flex');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.getElementById('deleteModal').classList.remove('flex');
    }

    // Search Filter
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase();
        
        // Filter Grid View
        document.querySelectorAll('.position-card').forEach(card => {
            const text = (card.dataset.title + ' ' + card.dataset.user).toLowerCase();
            card.style.display = text.includes(term) ? 'block' : 'none';
        });

        // Filter Tree View (Simple implementation: Highlight or Hide?)
        // Hiding in tree structure is complex because of connectors. 
        // Let's just dim non-matches or highlight matches.
        if (term.length > 0) {
            document.querySelectorAll('#treeView h4').forEach(el => {
                const parent = el.closest('div'); // The box
                if (el.textContent.toLowerCase().includes(term)) {
                    parent.classList.add('ring-2', 'ring-sky-500', 'bg-sky-500/20');
                } else {
                    parent.classList.remove('ring-2', 'ring-sky-500', 'bg-sky-500/20');
                }
            });
        }
    });

    // Close on Escape
    document.addEventListener('keydown', e => {
        if(e.key === 'Escape') { closeModal(); closeDeleteModal(); }
    });
</script>
@endpush
@endsection
