@extends('layouts.app')

@section('content')
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
                    <p class="text-gray-300 mt-2">Kelola struktur organisasi dengan mudah - klik, edit, dan atur!</p>
                </div>
                
                <button onclick="openAddModal()" 
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Tambah Posisi Baru
                </button>
            </div>

            {{-- Quick Stats --}}
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-gradient-to-br from-blue-500/20 to-cyan-500/20 backdrop-blur-sm rounded-xl p-4 border border-blue-500/30">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-300">Total Posisi</p>
                            <p class="text-3xl font-bold text-white mt-1">{{ $positions->count() }}</p>
                        </div>
                        <i class="fas fa-briefcase text-4xl text-blue-400/50"></i>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-green-500/20 to-emerald-500/20 backdrop-blur-sm rounded-xl p-4 border border-green-500/30">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-300">Sudah Terisi</p>
                            <p class="text-3xl font-bold text-white mt-1">{{ $positions->whereNotNull('user_id')->count() }}</p>
                        </div>
                        <i class="fas fa-user-check text-4xl text-green-400/50"></i>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-amber-500/20 to-orange-500/20 backdrop-blur-sm rounded-xl p-4 border border-amber-500/30">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-300">Masih Kosong</p>
                            <p class="text-3xl font-bold text-white mt-1">{{ $positions->whereNull('user_id')->count() }}</p>
                        </div>
                        <i class="fas fa-user-slash text-4xl text-amber-400/50"></i>
                    </div>
                </div>
            </div>

            {{-- Search & Filter --}}
            <div class="mt-6 flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="searchInput" placeholder="Cari nama posisi atau staff..." 
                           class="w-full pl-12 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                </div>
                <select id="levelFilter" class="px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                    <option value="">Semua Level</option>
                    @for($i = 0; $i <= 7; $i++)
                        <option value="{{ $i }}">Level {{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>

        {{-- Success/Error Messages --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-500/20 border border-green-500 text-green-100 rounded-xl flex items-center">
                <i class="fas fa-check-circle mr-3 text-xl"></i>
                {{ session('success') }}
            </div>
        @endif

        {{-- Visual Tree View --}}
        <div class="space-y-6" id="organizationTree">
            @forelse($tree as $levelKey => $levelData)
                <div class="level-group" data-level="{{ str_replace('level_', '', $levelKey) }}">
                    {{-- Level Header --}}
                    <div class="bg-gradient-to-r from-purple-600/30 to-pink-600/30 backdrop-blur-sm rounded-xl p-4 border border-purple-500/30 mb-4">
                        <h2 class="text-xl font-bold text-white flex items-center gap-2">
                            <span class="px-3 py-1 bg-purple-500/50 rounded-lg text-sm">
                                Level {{ str_replace('level_', '', $levelKey) }}
                            </span>
                            {{ $levelData['title'] ?? 'Tidak ada judul' }}
                        </h2>
                    </div>

                    {{-- Positions Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 position-container" data-level="{{ str_replace('level_', '', $levelKey) }}">
                        @foreach($positions->where('level', str_replace('level_', '', $levelKey))->where('parent_id', null) as $position)
                            <div class="position-card bg-white/10 backdrop-blur-sm rounded-xl border border-white/20 p-5 hover:border-sky-500/50 transition-all duration-300 hover:shadow-xl hover:shadow-sky-500/20"
                                 data-id="{{ $position->id }}"
                                 data-title="{{ $position->title }}"
                                 data-user="{{ $position->user ? $position->user->name : '' }}">
                                
                                {{-- Card Header --}}
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-white text-lg">{{ $position->title }}</h3>
                                        @if($position->position_name)
                                            <p class="text-sm text-gray-400 mt-1">{{ $position->position_name }}</p>
                                        @endif
                                    </div>
                                    <button class="text-gray-400 hover:text-white transition-colors cursor-move" title="Drag untuk reorder">
                                        <i class="fas fa-grip-vertical"></i>
                                    </button>
                                </div>

                                {{-- Assigned User --}}
                                <div class="mb-4">
                                    @if($position->user)
                                        <div class="flex items-center gap-3 p-3 bg-green-500/20 rounded-lg border border-green-500/30">
                                            <img src="{{ $position->user->profile_image ? asset('storage/' . $position->user->profile_image) : asset('profile.jpg') }}" 
                                                 alt="{{ $position->user->name }}"
                                                 class="w-10 h-10 rounded-full border-2 border-green-500/50">
                                            <div class="flex-1">
                                                <p class="text-white font-medium text-sm">{{ $position->user->name }}</p>
                                                @if($position->user->role)
                                                    <p class="text-xs text-gray-300">{{ $position->user->role->display_name ?? $position->user->role->name }}</p>
                                                @endif
                                            </div>
                                            <i class="fas fa-check-circle text-green-400"></i>
                                        </div>
                                    @else
                                        <div class="p-3 bg-amber-500/20 rounded-lg border border-amber-500/30 flex items-center gap-2">
                                            <i class="fas fa-exclamation-triangle text-amber-400"></i>
                                            <p class="text-amber-200 text-sm flex-1">Belum ada yang ditugaskan</p>
                                        </div>
                                    @endif
                                </div>

                                {{-- Children count --}}
                                @if($position->children->count() > 0)
                                    <div class="mb-4 flex items-center gap-2 text-sm text-gray-300">
                                        <i class="fas fa-users"></i>
                                        <span>{{ $position->children->count() }} staff dibawahnya</span>
                                    </div>
                                @endif

                                {{-- Actions --}}
                                <div class="flex gap-2">
                                    <button onclick="openEditModal({{ $position->id }})" 
                                            class="flex-1 px-4 py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-lg transition-all duration-300 text-sm font-medium flex items-center justify-center gap-2">
                                        <i class="fas fa-edit"></i>
                                        Edit
                                    </button>
                                    <button onclick="confirmDelete({{ $position->id }}, '{{ $position->title }}')" 
                                            class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-all duration-300 text-sm font-medium">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>

                                {{-- Status Badge --}}
                                <div class="mt-3 flex items-center justify-between text-xs">
                                    @if($position->is_active)
                                        <span class="px-2 py-1 bg-green-500/30 text-green-200 rounded-full flex items-center gap-1">
                                            <i class="fas fa-circle text-[6px]"></i>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="px-2 py-1 bg-gray-500/30 text-gray-300 rounded-full flex items-center gap-1">
                                            <i class="fas fa-circle text-[6px]"></i>
                                            Tidak Aktif
                                        </span>
                                    @endif
                                    <span class="text-gray-400">Order: {{ $position->display_order }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="text-center py-20">
                    <i class="fas fa-inbox text-6xl text-gray-500 mb-4"></i>
                    <p class="text-xl text-gray-400">Belum ada posisi struktural</p>
                    <button onclick="openAddModal()" class="mt-4 px-6 py-3 bg-green-500 hover:bg-green-600 text-white rounded-lg">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Posisi Pertama
                    </button>
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Add/Edit Modal --}}
<div id="positionModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto border border-white/20 shadow-2xl">
        <form id="positionForm" method="POST">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            
            {{-- Modal Header --}}
            <div class="sticky top-0 bg-gradient-to-r from-sky-600 to-cyan-600 p-6 rounded-t-2xl border-b border-white/10">
                <h3 class="text-2xl font-bold text-white" id="modalTitle">Tambah Posisi Baru</h3>
            </div>

            {{-- Modal Body --}}
            <div class="p-6 space-y-5">
                {{-- Level --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-200 mb-2">
                        <i class="fas fa-layer-group mr-2"></i>Level Hierarki *
                    </label>
                    <select name="level" id="level" required
                            class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                        <option value="">Pilih Level</option>
                        @for($i = 0; $i <= 7; $i++)
                            <option value="{{ $i }}">Level {{ $i }}</option>
                        @endfor
                    </select>
                </div>

                {{-- Title --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-200 mb-2">
                        <i class="fas fa-heading mr-2"></i>Nama Posisi *
                    </label>
                    <input type="text" name="title" id="title" required
                           placeholder="Contoh: CEO, Manager, Staff"
                           class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>

                {{-- Position Name --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-200 mb-2">
                        <i class="fas fa-tag mr-2"></i>Nama Lengkap / Departemen
                    </label>
                    <input type="text" name="position_name" id="position_name"
                           placeholder="Contoh: Department of Human Resources"
                           class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>

                {{-- Assign User --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-200 mb-2">
                        <i class="fas fa-user mr-2"></i>Tugaskan Ke Staff
                    </label>
                    <select name="user_id" id="user_id"
                            class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                        <option value="">Belum ditugaskan</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">
                                {{ $user->name }} @if($user->role)({{ $user->role->display_name ?? $user->role->name }})@endif
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Parent Position --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-200 mb-2">
                        <i class="fas fa-level-up-alt mr-2"></i>Posisi Atasan
                    </label>
                    <select name="parent_id" id="parent_id"
                            class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                        <option value="">Tidak ada (Root Level)</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos->id }}">
                                {{ str_repeat('— ', $pos->level) }}{{ $pos->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Active Status --}}
                <div class="flex items-center gap-3 p-4 bg-white/5 rounded-lg border border-white/10">
                    <input type="checkbox" name="is_active" id="is_active" value="1" checked
                           class="w-5 h-5 text-sky-500 bg-white/5 border-white/20 rounded focus:ring-sky-500">
                    <label for="is_active" class="text-sm font-semibold text-gray-200 cursor-pointer">
                        <i class="fas fa-toggle-on mr-2 text-green-400"></i>
                        Posisi Aktif
                    </label>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="sticky bottom-0 bg-gradient-to-r from-gray-800 to-gray-900 p-6 rounded-b-2xl border-t border-white/10 flex gap-3">
                <button type="button" onclick="closeModal()" 
                        class="flex-1 px-6 py-3 bg-white/10 hover:bg-white/20 text-white rounded-lg font-medium transition-all duration-300">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white rounded-lg font-semibold transition-all duration-300 shadow-lg">
                    <i class="fas fa-save mr-2"></i>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div id="deleteModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden items-center justify-center z-50">
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl max-w-md w-full mx-4 border border-red-500/30 shadow-2xl">
        <div class="p-6">
            <div class="text-center mb-6">
                <div class="mx-auto w-16 h-16 bg-red-500/20 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-exclamation-triangle text-3xl text-red-400"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Hapus Posisi?</h3>
                <p class="text-gray-300" id="deleteMessage"></p>
            </div>
            <div class="flex gap-3">
                <button onclick="closeDeleteModal()" 
                        class="flex-1 px-6 py-3 bg-white/10 hover:bg-white/20 text-white rounded-lg font-medium">
                    Batal
                </button>
                <form id="deleteForm" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full px-6 py-3 bg-red-500 hover:bg-red-600 text-white rounded-lg font-semibold">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
// Modal Functions
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Tambah Posisi Baru';
    document.getElementById('positionForm').action = '{{ route("admin.structural.store") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('positionForm').reset();
    document.getElementById('is_active').checked = true;
    document.getElementById('positionModal').classList.remove('hidden');
   document.getElementById('positionModal').classList.add('flex');
}

function openEditModal(id) {
    document.getElementById('modalTitle').textContent = 'Edit Posisi';
    document.getElementById('positionForm').action = `/admin/structural/${id}`;
    document.getElementById('formMethod').value = 'PUT';
    
    // Fetch position data via AJAX
    fetch(`/admin/structural/${id}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('level').value = data.level || '';
            document.getElementById('title').value = data.title || '';
            document.getElementById('position_name').value = data.position_name || '';
            document.getElementById('user_id').value = data.user_id || '';
            document.getElementById('parent_id').value = data.parent_id || '';
            document.getElementById('is_active').checked = data.is_active;
            
            document.getElementById('positionModal').classList.remove('hidden');
            document.getElementById('positionModal').classList.add('flex');
        });
}

function closeModal() {
    document.getElementById('positionModal').classList.add('hidden');
    document.getElementById('positionModal').classList.remove('flex');
}

function confirmDelete(id, title) {
    document.getElementById('deleteMessage').textContent = `Yakin ingin menghapus posisi "${title}"? Tindakan ini tidak dapat dibatalkan.`;
    document.getElementById('deleteForm').action = `/admin/structural/${id}`;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteModal').classList.add('flex');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.getElementById('deleteModal').classList.remove('flex');
}

// Search & Filter
document.getElementById('searchInput').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    document.querySelectorAll('.position-card').forEach(card => {
        const title = card.dataset.title.toLowerCase();
        const user = card.dataset.user.toLowerCase();
        const matches = title.includes(searchTerm) || user.includes(searchTerm);
        card.style.display = matches ? 'block' : 'none';
    });
});

document.getElementById('levelFilter').addEventListener('change', function(e) {
    const level = e.target.value;
    document.querySelectorAll('.level-group').forEach(group => {
        if (level === '' || group.dataset.level === level) {
            group.style.display = 'block';
        } else {
            group.style.display = 'none';
        }
    });
});

// Drag & Drop for reordering
document.querySelectorAll('.position-container').forEach(container => {
    new Sortable(container, {
        animation: 150,
        handle: '.cursor-move',
        ghostClass: 'opacity-50',
        onEnd: function(evt) {
            // Get new order
            const items = [];
            container.querySelectorAll('.position-card').forEach((card, index) => {
                items.push({
                    id: card.dataset.id,
                    order: index
                });
            });

            // Send AJAX request to update order
            fetch('{{ route("admin.structural.reorder") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ items: items })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Show success toast
                    console.log('Order updated successfully');
                }
            });
        }
    });
});

// Close modals on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
        closeDeleteModal();
    }
});

// Close modals on background click  
document.getElementById('positionModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
</script>
@endpush
@endsection
    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            {{-- Header --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-bold text-white mb-2">
                        <i class="fas fa-sitemap mr-3"></i>Struktural EMS Management
                    </h1>
                    <p class="text-gray-300">Manage organizational hierarchy and positions</p>
                </div>
                <a href="{{ route('admin.structural.create') }}"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white rounded-lg font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                    <i class="fas fa-plus mr-2"></i>
                    Add New Position
                </a>
            </div>

            {{-- Success/Error Messages --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-500/20 border border-green-500 text-green-100 rounded-lg">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-500/20 border border-red-500 text-red-100 rounded-lg">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Positions Table --}}
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl shadow-2xl border border-white/20 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-white/5 border-b border-white/10">
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-200 uppercase tracking-wider">
                                    Level
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-200 uppercase tracking-wider">
                                    Position
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-200 uppercase tracking-wider">
                                    Assigned To
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-200 uppercase tracking-wider">
                                    Parent
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-200 uppercase tracking-wider">
                                    Status
                                </th>
                                <th
                                    class="px-6 py-4 text-right text-xs font-semibold text-gray-200 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @forelse($positions as $position)
                                <tr class="hover:bg-white/5 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-500/20 text-purple-200 border border-purple-500/30">
                                            Level {{ $position->level }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            {{-- Indentation for hierarchy --}}
                                            @if($position->parent_id)
                                                <span class="text-gray-500 mr-2">
                                                    <i class="fas fa-level-up-alt fa-rotate-90 mr-1"></i>
                                                </span>
                                            @endif
                                            <div>
                                                <div class="text-sm font-semibold text-white">{{ $position->title }}</div>
                                                @if($position->position_name)
                                                    <div class="text-xs text-gray-400">{{ $position->position_name }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($position->user)
                                            <div class="flex items-center">
                                                <img src="{{ $position->user->profile_image ? asset('storage/' . $position->user->profile_image) : asset('profile.jpg') }}"
                                                    alt="{{ $position->user->name }}"
                                                    class="w-8 h-8 rounded-full mr-2 border-2 border-white/20">
                                                <div>
                                                    <div class="text-sm text-white">{{ $position->user->name }}</div>
                                                    @if($position->user->role)
                                                        <div class="text-xs text-gray-400">
                                                            {{ $position->user->role->display_name ?? $position->user->role->name }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400 italic">Not assigned</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($position->parent)
                                            <span class="text-sm text-gray-300">{{ $position->parent->title }}</span>
                                        @else
                                            <span class="text-sm text-gray-500 italic">Root level</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($position->is_active)
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-500/20 text-green-200 border border-green-500/30">
                                                <i class="fas fa-check-circle mr-1"></i>Active
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-500/20 text-gray-300 border border-gray-500/30">
                                                <i class="fas fa-times-circle mr-1"></i>Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.structural.edit', $position) }}"
                                                class="inline-flex items-center px-3 py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-lg transition-all duration-300 shadow-lg text-xs">
                                                <i class="fas fa-edit mr-1"></i>Edit
                                            </a>
                                            <form action="{{ route('admin.structural.destroy', $position) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    onclick="return confirm('Are you sure you want to delete this position? This cannot be undone.')"
                                                    class="inline-flex items-center px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-all duration-300 shadow-lg text-xs">
                                                    <i class="fas fa-trash mr-1"></i>Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-400">
                                            <i class="fas fa-inbox text-4xl mb-4 opacity-50"></i>
                                            <p class="text-lg font-medium">No positions found</p>
                                            <p class="text-sm mt-2">Click "Add New Position" to create your first organizational
                                                position.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 border border-white/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-300">Total Positions</p>
                            <p class="text-3xl font-bold text-white mt-1">{{ $positions->count() }}</p>
                        </div>
                        <i class="fas fa-sitemap text-4xl text-sky-500/50"></i>
                    </div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 border border-white/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-300">Assigned</p>
                            <p class="text-3xl font-bold text-white mt-1">{{ $positions->whereNotNull('user_id')->count() }}
                            </p>
                        </div>
                        <i class="fas fa-user-check text-4xl text-green-500/50"></i>
                    </div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 border border-white/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-300">Vacant</p>
                            <p class="text-3xl font-bold text-white mt-1">{{ $positions->whereNull('user_id')->count() }}
                            </p>
                        </div>
                        <i class="fas fa-user-slash text-4xl text-amber-500/50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection