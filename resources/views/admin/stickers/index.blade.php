@extends('layouts.app')

@section('title', 'Manajemen Stiker - Portal Medis iMe Roleplay')

@section('content')
<div class="min-h-screen bg-slate-950 text-slate-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto space-y-8">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white flex items-center gap-2">
                    <i class="fas fa-sticky-note text-indigo-400"></i> Manajemen Stiker & GIF
                </h1>
                <p class="text-slate-400 text-sm mt-1">Kelola stiker kustom dan integrasi GIPHY untuk internal chat staf medis.</p>
            </div>
            <a href="{{ route('chat.page') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-white rounded-xl text-sm font-bold transition-all">
                <i class="fas fa-comments"></i> Buka Chat
            </a>
        </div>

        <!-- Alert Notifications -->
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm flex items-center gap-3">
                <i class="fas fa-check-circle text-emerald-400 text-lg"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Side: Giphy Configuration & Add Pack -->
            <div class="space-y-8 lg:col-span-1">
                <!-- GIPHY Config Card -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-6">
                    <h2 class="text-lg font-bold text-white flex items-center gap-2 border-b border-slate-800 pb-3">
                        <i class="fas fa-plug text-sky-400"></i> Integrasi GIPHY
                    </h2>
                    
                    <form action="{{ route('admin.stickers.toggle-giphy') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="block text-sm font-bold text-slate-200">GIPHY Stickers</span>
                                <span class="text-xs text-slate-400">Izinkan staf mencari & mengirim stiker dari GIPHY</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="giphy_enabled" value="1" {{ $giphyEnabled ? 'checked' : '' }} class="sr-only peer" onchange="this.form.submit()">
                                <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-300 after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sky-500"></div>
                            </label>
                        </div>
                    </form>
                    
                    <div class="p-4 rounded-xl bg-slate-950 border border-slate-800/80 text-xs text-slate-400 space-y-2">
                        <span class="block font-bold text-slate-300">Status Kunci API GIPHY:</span>
                        @if(env('GIPHY_API_KEY'))
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                <i class="fas fa-lock"></i> Terkonfigurasi (.env)
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                <i class="fas fa-exclamation-circle"></i> Belum Dikonfigurasi
                            </span>
                            <span class="block mt-1 leading-relaxed">Tambahkan <code class="text-slate-200">GIPHY_API_KEY</code> pada file <code class="text-slate-200">.env</code> Anda agar pencarian stiker online dapat berfungsi.</span>
                        @endif
                    </div>
                </div>

                <!-- Create Pack Card -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-6">
                    <h2 class="text-lg font-bold text-white flex items-center gap-2 border-b border-slate-800 pb-3">
                        <i class="fas fa-folder-plus text-indigo-400"></i> Buat Sticker Pack
                    </h2>
                    
                    <form action="{{ route('admin.stickers.store-pack') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-400 mb-2">Nama Pack</label>
                            <input type="text" name="name" required placeholder="Contoh: EMS Pack"
                                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-400 mb-2">Deskripsi</label>
                            <textarea name="description" placeholder="Stiker ekspresi tim medis..." rows="2"
                                      class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-400 mb-2">Urutan Tampil (Sort Order)</label>
                            <input type="number" name="sort_order" value="0" min="0"
                                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-sm font-bold transition-all shadow-lg shadow-indigo-900/30">
                            <i class="fas fa-save mr-1.5"></i> Simpan Pack Baru
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Side: Sticker Packs List & Upload Stickers -->
            <div class="lg:col-span-2 space-y-8">
                @forelse($packs as $pack)
                    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-6">
                        <!-- Pack Header -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-slate-800 pb-4 gap-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-xl font-bold text-white">{{ $pack->name }}</h3>
                                    <span class="px-2 py-0.5 bg-slate-800 border border-slate-700 text-slate-300 rounded text-xs">
                                        {{ $pack->stickers_count }} Stiker
                                    </span>
                                    @if(!$pack->is_active)
                                        <span class="px-2 py-0.5 bg-rose-500/10 border border-rose-500/20 text-rose-400 rounded text-xs font-bold">
                                            Nonaktif
                                        </span>
                                    @endif
                                </div>
                                <p class="text-slate-400 text-xs mt-1">{{ $pack->description ?? 'Tidak ada deskripsi.' }}</p>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex items-center gap-2">
                                <button onclick="toggleEditModal({{ $pack->id }}, '{{ addslashes($pack->name) }}', '{{ addslashes($pack->description) }}', {{ $pack->sort_order }}, {{ $pack->is_active ? 'true' : 'false' }})"
                                        class="p-1.5 bg-slate-800 hover:bg-slate-700 border border-slate-700 hover:text-indigo-400 rounded-lg text-slate-300 text-xs transition-all">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <form action="{{ route('admin.stickers.destroy-pack', $pack->id) }}" method="POST" onsubmit="return confirm('Hapus pack stiker ini beserta seluruh stiker di dalamnya?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 bg-slate-850 hover:bg-rose-500/20 border border-slate-800 hover:text-rose-400 rounded-lg text-slate-400 text-xs transition-all">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Upload Form -->
                        <form action="{{ route('admin.stickers.upload', $pack->id) }}" method="POST" enctype="multipart/form-data" class="p-4 rounded-xl bg-slate-950 border border-slate-805 space-y-4">
                            @csrf
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-300">Unggah Stiker Kustom baru</label>
                                    <span class="text-[11px] text-slate-400">Mendukung format PNG, JPG, JPEG, WEBP, atau GIF (max 2MB per file)</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="file" name="stickers[]" multiple required accept="image/*" class="text-xs text-slate-400 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-800 file:text-slate-200 hover:file:bg-slate-700 cursor-pointer">
                                    <button type="submit" class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 shadow-lg shadow-indigo-900/10">
                                        <i class="fas fa-upload"></i> Upload
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Stickers Grid -->
                        @if($pack->stickers->count() > 0)
                            <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-4">
                                @foreach($pack->stickers as $stk)
                                    <div class="group bg-slate-950 border border-slate-850 hover:border-slate-800 p-2 rounded-xl flex flex-col items-center justify-center relative transition-all">
                                        <img src="{{ $stk->file_url }}" alt="{{ $stk->name }}" class="w-12 h-12 object-contain rounded-lg">
                                        
                                        <!-- Hover Delete Button -->
                                        <form action="{{ route('admin.stickers.destroy-sticker', $stk->id) }}" method="POST" onsubmit="return confirm('Hapus stiker ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="absolute -top-1.5 -right-1.5 p-1 bg-rose-600 hover:bg-rose-500 text-white text-[9px] rounded-full opacity-0 group-hover:opacity-100 transition-opacity shadow">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6 border border-dashed border-slate-800 rounded-xl">
                                <i class="fas fa-images text-slate-700 text-2xl mb-2"></i>
                                <p class="text-xs text-slate-500">Belum ada stiker di pack ini. Unggah beberapa file stiker di atas.</p>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-8 text-center shadow-xl">
                        <i class="fas fa-folder-open text-slate-700 text-3xl mb-3"></i>
                        <h4 class="font-bold text-white text-base">Belum Ada Sticker Pack</h4>
                        <p class="text-slate-400 text-xs mt-1 max-w-sm mx-auto">Silakan buat sticker pack kustom pertama Anda menggunakan formulir di sebelah kiri.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

<!-- Edit Pack Modal -->
<div id="editModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center hidden">
    <div class="bg-slate-900 border border-slate-800 w-full max-w-md rounded-2xl p-6 shadow-2xl relative animate-fade-in">
        <button onclick="closeEditModal()" class="absolute top-4 right-4 text-slate-400 hover:text-white transition-colors">
            <i class="fas fa-times"></i>
        </button>
        
        <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-1.5">
            <i class="fas fa-edit text-indigo-400"></i> Edit Sticker Pack
        </h3>

        <form id="editForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-xs font-bold uppercase text-slate-400 mb-2">Nama Pack</label>
                <input type="text" name="name" id="editName" required
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-400 mb-2">Deskripsi</label>
                <textarea name="description" id="editDescription" rows="2"
                          class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:outline-none"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-400 mb-2">Urutan Tampil (Sort Order)</label>
                <input type="number" name="sort_order" id="editSortOrder" min="0"
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:outline-none">
            </div>

            <div class="flex items-center justify-between py-2">
                <div>
                    <span class="block text-sm font-bold text-slate-200">Status Aktif</span>
                    <span class="text-xs text-slate-400">Nonaktifkan untuk menyembunyikan pack</span>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" id="editIsActive" value="1" class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-300 after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-500"></div>
                </label>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-bold">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-indigo-900/20">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleEditModal(id, name, description, sortOrder, isActive) {
        const form = document.getElementById('editForm');
        form.action = `/admin/stickers/pack/` + id;
        
        document.getElementById('editName').value = name;
        document.getElementById('editDescription').value = description;
        document.getElementById('editSortOrder').value = sortOrder;
        document.getElementById('editIsActive').checked = isActive;
        
        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }
</script>
@endsection
