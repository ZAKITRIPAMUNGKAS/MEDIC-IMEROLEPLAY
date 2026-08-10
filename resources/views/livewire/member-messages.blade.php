<div class="w-full" wire:poll.5s x-data="{ stickerPickerOpen: false }">
    <!-- Streak Milestone Notification/Banner if any -->
    <div x-data="{ showMilestone: false, milestoneCount: 0 }" 
         @streak-milestone.window="milestoneCount = $event.detail.count; showMilestone = true; setTimeout(() => { showMilestone = false; }, 5000)"
         x-show="showMilestone"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-250"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="fixed bottom-24 right-8 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-bold px-6 py-4 rounded-2xl shadow-2xl border border-amber-400/40 z-[9999] flex items-center gap-3 select-none"
         style="display: none;">
        <span class="text-3xl animate-bounce">🔥</span>
        <div>
            <h4 class="text-sm font-extrabold tracking-wide uppercase">Streak Milestone Tercapai!</h4>
            <p class="text-xs text-amber-100 font-medium">Luar biasa! Streak interaksi kalian mencapai <span x-text="milestoneCount"></span> hari.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6" style="min-height: 600px; height: auto;">
        
        {{-- Sidebar List (1 Column) --}}
        <div class="border border-white/20 rounded-2xl shadow-xl flex flex-col lg:col-span-1 overflow-hidden"
            style="min-height: 450px; max-height: 620px; height: 620px; background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(16px);">
            
            {{-- Search Header --}}
            <div class="p-4 border-b border-white/10 shrink-0" style="background: rgba(0, 0, 0, 0.3);">
                <h2 class="font-bold text-white text-base mb-2.5 flex items-center justify-between">
                    <span><i class="fas fa-list-ul mr-2 text-sky-400"></i> Percakapan Aktif</span>
                    <span class="text-xs px-2.5 py-0.5 rounded-full text-sky-300 font-semibold border border-sky-400/40" style="background: rgba(56, 189, 248, 0.15);">
                        {{ count($members) }} Kontak
                    </span>
                </h2>
                <div class="relative">
                    <input type="text" 
                        wire:model.live.debounce.300ms="searchQuery"
                        placeholder="Cari kontak chat..." 
                        class="w-full text-white placeholder-sky-300/70 text-xs rounded-xl pl-9 pr-4 py-2 border border-white/20 focus:outline-none focus:ring-2 focus:ring-sky-400 transition-all"
                        style="background: rgba(255, 255, 255, 0.08);"
                    />
                    <div class="absolute left-3 top-2.5 text-sky-400">
                        <i class="fas fa-search text-xs"></i>
                    </div>
                </div>
            </div>

            {{-- Members List --}}
            <div class="flex-1 overflow-y-auto custom-scrollbar">
                @if(count($members) > 0)
                    <div class="divide-y divide-white/10">
                        @foreach($members as $memberItem)
                            @php $isSelected = ($selectedUserId == $memberItem->id); @endphp
                            <div wire:click="selectUser({{ $memberItem->id }})"
                                class="p-3.5 cursor-pointer transition-all flex items-center gap-3 group"
                                style="{{ $isSelected ? 'background: rgba(14, 165, 233, 0.25); border-left: 4px solid #38bdf8;' : 'border-left: 4px solid transparent; background: transparent;' }}">
                                
                                {{-- Avatar with Online Badge --}}
                                <div class="relative shrink-0">
                                    <img src="{{ $memberItem->profile_image_url }}" 
                                        onerror="{{ $memberItem->profile_image_on_error }}"
                                        alt="{{ $memberItem->name }}" 
                                        class="w-11 h-11 rounded-xl object-cover border border-white/30 shadow-md"
                                    />
                                    @if($memberItem->isOnline())
                                        <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-emerald-400 border-2 border-slate-900 rounded-full shadow-[0_0_8px_rgba(52,211,153,0.8)]"></span>
                                    @else
                                        <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-slate-500 border-2 border-slate-900 rounded-full"></span>
                                    @endif
                                </div>

                                {{-- User Info --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start mb-0.5">
                                        <h3 class="font-bold text-sm truncate transition-colors {{ $isSelected ? 'text-sky-300' : 'text-white group-hover:text-sky-300' }}">
                                            {{ $memberItem->name }}
                                        </h3>
                                        
                                        <div class="flex items-center gap-1 shrink-0 ml-1">
                                            @if($memberItem->streak_count > 0)
                                                <span class="text-xs" title="Streak {{ $memberItem->streak_count }} Hari">🔥{{ $memberItem->streak_count }}</span>
                                            @endif
                                            @if($memberItem->unread_count > 0)
                                                <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-md shrink-0 ml-1 animate-bounce">
                                                    {{ $memberItem->unread_count }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-center text-xs">
                                        <p class="text-sky-200 text-[11px] truncate mr-2 font-medium">
                                            {{ $memberItem->role->display_name ?? 'Staff' }}
                                        </p>
                                        @if($memberItem->latest_message_time)
                                            <span class="text-[10px] text-sky-300/70 whitespace-nowrap">
                                                {{ $memberItem->latest_message_time->diffForHumans(null, true) }}
                                            </span>
                                        @endif
                                    </div>
                                    @if($memberItem->latest_message_text)
                                        <p class="text-xs text-sky-100/80 truncate mt-1">
                                            {{ $memberItem->latest_message_text }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="h-full flex flex-col items-center justify-center text-sky-200 p-8 text-center">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center mb-3 border border-white/20" style="background: rgba(255, 255, 255, 0.08);">
                            <i class="fas fa-comments text-xl text-sky-300"></i>
                        </div>
                        <p class="text-sm font-semibold text-white mb-1">Belum Ada Percakapan Aktif</p>
                        <p class="text-xs text-sky-200 mb-4">Pilih staf di Direktori Anggota untuk memulai percakapan baru.</p>
                        <a href="{{ route('staff.members.index') }}" class="px-4 py-2 bg-gradient-to-r from-sky-500 to-cyan-500 text-white font-bold rounded-xl text-xs shadow-md hover:from-sky-600 hover:to-cyan-600 transition">
                            <i class="fas fa-user-plus mr-1"></i> Buka Direktori Anggota
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Chat Area (2 Columns) --}}
        <div class="border border-white/20 rounded-2xl shadow-xl flex flex-col lg:col-span-2 overflow-hidden relative"
            style="min-height: 450px; max-height: 620px; height: 620px; background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(16px);">
            
            @if($activeUser)
                {{-- Chat Header --}}
                <div class="p-4 border-b border-white/10 flex justify-between items-center shadow-md shrink-0" style="background: rgba(0, 0, 0, 0.3);">
                    <div class="flex items-center gap-3 min-w-0">
                        <img src="{{ $activeUser->profile_image_url }}" 
                            onerror="{{ $activeUser->profile_image_on_error }}"
                            alt="{{ $activeUser->name }}" 
                            class="w-11 h-11 rounded-xl object-cover border border-white/30 shadow-md shrink-0"
                        />
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-white text-base tracking-wide truncate">
                                    {{ $activeUser->name }}
                                </h3>
                                @if($streakCount > 0)
                                    <div class="flex items-center gap-1 bg-amber-500/10 border border-amber-500/30 px-2 py-0.5 rounded-full text-amber-400 font-bold text-[10px] select-none animate-pulse" title="Streak {{ $streakCount }} Hari">
                                        <span>🔥</span>
                                        <span>{{ $streakCount }} {{ $streakCount === 1 ? 'Day' : 'Days' }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 text-xs">
                                <span class="text-sky-300 font-medium">{{ $activeUser->role->display_name ?? 'Staff' }}</span>
                                <span class="text-sky-400">•</span>
                                @if($activeUser->isOnline())
                                    <span class="text-emerald-300 font-semibold flex items-center">
                                        <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full mr-1.5 animate-pulse"></span>
                                        Online
                                    </span>
                                @else
                                    <span class="text-sky-200/70">
                                        Terakhir dilihat {{ $activeUser->last_seen_at ? $activeUser->last_seen_at->diffForHumans() : 'beberapa waktu lalu' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="shrink-0 flex gap-2">
                        <a href="{{ route('staff.members.show', $activeUser->id) }}" 
                            class="px-3 py-1.5 text-white hover:text-sky-300 rounded-xl text-xs transition border border-white/20 flex items-center gap-1.5"
                            style="background: rgba(255, 255, 255, 0.08);"
                            title="Lihat Profil">
                            <i class="fas fa-user-circle text-sky-400"></i> Lihat Profil
                        </a>
                    </div>
                </div>

                {{-- Messages Body --}}
                <div class="flex-1 overflow-y-auto p-4 space-y-4 custom-scrollbar" style="background: rgba(0, 0, 0, 0.2);" id="chat-messages-container" x-data x-init="$nextTick(() => { $el.scrollTop = $el.scrollHeight });" @message-sent.window="$nextTick(() => { $el.scrollTop = $el.scrollHeight })">
                    @if(count($chatMessages) > 0)
                        @php $lastDate = null; @endphp
                        @foreach($chatMessages as $msg)
                            @php 
                                $msgDate = $msg->created_at->format('d M Y');
                                $showDateSeparator = ($msgDate !== $lastDate);
                                $lastDate = $msgDate;
                            @endphp

                            @if($showDateSeparator)
                                <div class="flex justify-center my-3">
                                    <span class="text-sky-200 text-[10px] font-bold px-3 py-1 rounded-full border border-white/10" style="background: rgba(0, 0, 0, 0.4);">
                                        {{ $msgDate }}
                                    </span>
                                </div>
                            @endif

                            @if((int)$msg->sender_id === (int)auth()->id())
                                {{-- Sent Message (Right Side) --}}
                                <div class="flex justify-end items-end gap-2">
                                    <div class="max-w-[75%]">
                                        <div class="text-[11px] font-bold text-cyan-300 mb-1 text-right flex items-center justify-end gap-1.5">
                                            <span>Anda ({{ auth()->user()->name }})</span>
                                            <i class="fas fa-user-circle text-[10px] text-cyan-400"></i>
                                        </div>
                                        
                                        @if($msg->message_type === 'sticker')
                                            <!-- Render Sticker (No bubble background) -->
                                            <div class="relative group select-none">
                                                <img src="{{ $msg->sticker_url }}" alt="Stiker" class="w-32 h-32 object-contain rounded-xl">
                                                <button @click="$dispatch('toggle-fav-sticker', { source: '{{ $msg->sticker_source }}', id: '{{ $msg->sticker_id }}', url: '{{ $msg->sticker_url }}' })" 
                                                        class="absolute bottom-1 right-1 p-1 bg-black/60 hover:bg-black/90 text-white rounded-lg text-[10px] opacity-0 group-hover:opacity-100 transition-opacity" title="Simpan ke Favorit">
                                                    ⭐
                                                </button>
                                            </div>
                                        @else
                                            <div class="p-3.5 text-white rounded-2xl rounded-tr-none shadow-lg text-xs sm:text-sm break-words whitespace-pre-line leading-relaxed border border-sky-400/30"
                                                 style="background: linear-gradient(135deg, #0ea5e9, #0284c7);">
                                                {{ $msg->message }}
                                            </div>
                                        @endif
                                        
                                        <div class="flex justify-end items-center gap-1.5 mt-1 text-[10px] text-sky-300">
                                            <span>{{ $msg->created_at->format('H:i') }}</span>
                                            @if($msg->is_read)
                                                <i class="fas fa-check-double text-emerald-300" title="Dibaca"></i>
                                            @else
                                                <i class="fas fa-check text-sky-300/80" title="Terkirim"></i>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                {{-- Received Message (Left Side) --}}
                                <div class="flex justify-start items-end gap-2">
                                    <div class="max-w-[75%]">
                                        <div class="text-[11px] font-bold text-sky-300 mb-1 flex items-center gap-1.5">
                                            <i class="fas fa-user text-[10px] text-sky-400"></i>
                                            <span>{{ $activeUser->name }}</span>
                                        </div>
                                        
                                        @if($msg->message_type === 'sticker')
                                            <!-- Render Sticker (No bubble background) -->
                                            <div class="relative group select-none">
                                                <img src="{{ $msg->sticker_url }}" alt="Stiker" class="w-32 h-32 object-contain rounded-xl">
                                                <button @click="$dispatch('toggle-fav-sticker', { source: '{{ $msg->sticker_source }}', id: '{{ $msg->sticker_id }}', url: '{{ $msg->sticker_url }}' })" 
                                                        class="absolute bottom-1 left-1 p-1 bg-black/60 hover:bg-black/90 text-white rounded-lg text-[10px] opacity-0 group-hover:opacity-100 transition-opacity" title="Simpan ke Favorit">
                                                    ⭐
                                                </button>
                                            </div>
                                        @else
                                            <div class="p-3.5 text-white rounded-2xl rounded-tl-none shadow-lg text-xs sm:text-sm break-words whitespace-pre-line leading-relaxed border border-sky-500/30"
                                                 style="background: rgba(15, 23, 42, 0.9); box-shadow: 0 4px 12px rgba(0,0,0,0.3);">
                                                {{ $msg->message }}
                                            </div>
                                        @endif
                                        
                                        <div class="mt-1 text-[10px] text-sky-300">
                                            {{ $msg->created_at->format('H:i') }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @else
                        <div class="h-full flex flex-col items-center justify-center text-sky-200 opacity-75">
                            <div class="w-14 h-14 rounded-full flex items-center justify-center mb-3 border border-white/20" style="background: rgba(255, 255, 255, 0.08);">
                                <i class="fas fa-paper-plane text-2xl text-sky-300"></i>
                            </div>
                            <p class="text-sm font-semibold text-white mb-1">Mulai Percakapan dengan {{ $activeUser->name }}</p>
                            <p class="text-xs text-sky-200">Ketik pesan di bawah untuk berkirim pesan pribadi secara langsung.</p>
                        </div>
                    @endif
                </div>

                <!-- Streak Warning Indicator -->
                @if($streakAlmostBroken)
                    <div class="px-4 py-2 bg-amber-500/10 border-t border-amber-500/20 text-amber-300 text-xs flex items-center gap-2 select-none shrink-0">
                        <span>🔥</span>
                        <span>Streak kamu hampir putus! Kirim pesan hari ini untuk mempertahankannya.</span>
                    </div>
                @endif

                {{-- Chat Input Form --}}
                <div class="p-3.5 border-t border-white/10 shrink-0 relative" style="background: rgba(0, 0, 0, 0.3);">
                    
                    <!-- Sticker Picker Pop-up widget -->
                    <div x-show="stickerPickerOpen" @click.outside="stickerPickerOpen = false"
                         x-data="{
                            activeTab: 'trending',
                            searchQuery: '',
                            stickers: [],
                            recents: [],
                            favorites: [],
                            categories: [],
                            packs: [],
                            loading: false,
                             error: null,
                            
                            init() {
                                this.loadTrending();
                                this.loadCategories();
                                this.loadPacks();
                                this.loadFavorites();
                                this.loadRecents();
                                
                                this.$watch('searchQuery', value => {
                                    if (this.debouncedSearch) clearTimeout(this.debouncedSearch);
                                    this.debouncedSearch = setTimeout(() => {
                                        if (value.trim().length > 0) {
                                            this.activeTab = 'search';
                                            this.search(value);
                                        } else {
                                            this.activeTab = 'trending';
                                            this.loadTrending();
                                        }
                                    }, 400);
                                });
                            },
                            
                            loadTrending() {
                                this.loading = true;
                                fetch('/api/stickers/trending')
                                    .then(res => res.json())
                                    .then(res => {
                                        if (res.success) {
                                            this.stickers = res.data;
                                            this.error = null;
                                        } else {
                                            this.error = res.message;
                                        }
                                        this.loading = false;
                                    });
                            },
                            
                            loadCategories() {
                                fetch('/api/stickers/categories')
                                    .then(res => res.json())
                                    .then(res => {
                                        if (res.success) this.categories = res.data;
                                    });
                            },
                            
                            loadPacks() {
                                fetch('/api/stickers/packs')
                                    .then(res => res.json())
                                    .then(res => {
                                        if (res.success) this.packs = res.data;
                                    });
                            },
                            
                            loadFavorites() {
                                fetch('/api/stickers/favorites')
                                    .then(res => res.json())
                                    .then(res => {
                                        if (res.success) this.favorites = res.data;
                                    });
                            },
                            
                            loadRecents() {
                                fetch('/api/stickers/recents')
                                    .then(res => res.json())
                                    .then(res => {
                                        if (res.success) this.recents = res.data;
                                    });
                            },
                            
                            search(query) {
                                this.loading = true;
                                fetch('/api/stickers/search?q=' + encodeURIComponent(query))
                                    .then(res => res.json())
                                    .then(res => {
                                        if (res.success) {
                                            this.stickers = res.data;
                                            this.error = null;
                                        } else {
                                            this.error = res.message;
                                        }
                                        this.loading = false;
                                    });
                            },
                            
                            selectCategory(query) {
                                this.searchQuery = query;
                                this.activeTab = 'search';
                                this.search(query);
                            },
                            
                            toggleFavorite(source, id, url) {
                                fetch('/api/stickers/favorites', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=&quot;csrf-token&quot;]').getAttribute('content')
                                    },
                                    body: JSON.stringify({ source: source, sticker_id: id, sticker_url: url })
                                })
                                .then(res => res.json())
                                .then(res => {
                                    this.loadFavorites();
                                });
                            },
                            
                            sendSticker(source, id, url) {
                                $wire.sendSticker(source, id, url);
                                this.stickerPickerOpen = false;
                                // Refresh recents shortly after
                                setTimeout(() => { this.loadRecents(); }, 800);
                            }
                         }" 
                         x-init="init()"
                         @toggle-fav-sticker.window="toggleFavorite($event.detail.source, $event.detail.id, $event.detail.url)"
                         class="absolute bottom-16 right-0 w-80 sm:w-96 h-96 flex flex-col bg-slate-900 border border-white/20 rounded-2xl shadow-2xl z-50 overflow-hidden"
                         style="display: none;"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 scale-95">
                         
                         <!-- Picker Header / Search -->
                         <div class="p-3 border-b border-white/10 shrink-0 bg-slate-950/40">
                             <div class="relative">
                                 <input type="text" x-model="searchQuery" placeholder="Cari stiker di GIPHY..." 
                                        class="w-full bg-slate-950 border border-white/20 rounded-xl pl-9 pr-4 py-1.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-sky-400">
                                 <div class="absolute left-3 top-2 text-slate-500 text-xs">
                                     <i class="fas fa-search"></i>
                                 </div>
                             </div>
                         </div>

                         <!-- Picker Tabs navigation -->
                         <div class="flex items-center gap-1 p-2 border-b border-white/5 overflow-x-auto shrink-0 bg-slate-950/20 text-xs select-none custom-scrollbar">
                             <button @click="activeTab = 'recents'; loadRecents()" :class="activeTab === 'recents' ? 'bg-sky-500 text-white font-bold' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800'" class="px-2.5 py-1 rounded-lg transition-all shrink-0">🕘 Recents</button>
                             <button @click="activeTab = 'favorites'; loadFavorites()" :class="activeTab === 'favorites' ? 'bg-sky-500 text-white font-bold' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800'" class="px-2.5 py-1 rounded-lg transition-all shrink-0">⭐ Favorites</button>
                             <button @click="activeTab = 'trending'; loadTrending()" :class="activeTab === 'trending' ? 'bg-sky-500 text-white font-bold' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800'" class="px-2.5 py-1 rounded-lg transition-all shrink-0">🔥 Trending</button>
                             
                             <!-- Custom Packs Tabs -->
                             <template x-for="pk in packs" :key="pk.id">
                                 <button @click="activeTab = 'pack_' + pk.id" :class="activeTab === 'pack_' + pk.id ? 'bg-sky-500 text-white font-bold' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800'" class="px-2.5 py-1 rounded-lg transition-all shrink-0" x-text="pk.name"></button>
                             </template>
                         </div>

                         <!-- Stickers Viewport -->
                         <div class="flex-1 overflow-y-auto p-3 custom-scrollbar">
                             
                             <!-- Loading State -->
                             <div x-show="loading" class="h-full flex items-center justify-center text-xs text-slate-500">
                                 <i class="fas fa-spinner animate-spin mr-2"></i> Loading...
                             </div>

                             <!-- Error State -->
                             <div x-show="error" class="h-full flex flex-col items-center justify-center text-xs text-rose-400 p-4 text-center">
                                 <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
                                 <span x-text="error"></span>
                             </div>

                             <!-- Content depending on Active Tab -->
                             <div x-show="!loading && !error" class="h-full">
                                 
                                 <!-- Recents -->
                                 <div x-show="activeTab === 'recents'">
                                     <template x-if="recents.length > 0">
                                         <div class="grid grid-cols-4 gap-2">
                                             <template x-for="r in recents" :key="r.id">
                                                 <button @click="sendSticker(r.source, r.sticker_id, r.sticker_url)" class="hover:bg-slate-800 p-1.5 rounded-xl transition-all flex items-center justify-center">
                                                     <img :src="r.sticker_url" class="w-16 h-16 object-contain" />
                                                 </button>
                                             </template>
                                         </div>
                                     </template>
                                     <template x-if="recents.length === 0">
                                         <div class="h-full flex flex-col items-center justify-center text-slate-500 text-center py-12">
                                             <span class="text-2xl mb-1">🕘</span>
                                             <p class="text-xs">Belum ada stiker yang sering digunakan.</p>
                                         </div>
                                     </template>
                                 </div>

                                 <!-- Favorites -->
                                 <div x-show="activeTab === 'favorites'">
                                     <template x-if="favorites.length > 0">
                                         <div class="grid grid-cols-4 gap-2">
                                             <template x-for="f in favorites" :key="f.id">
                                                 <div class="relative group hover:bg-slate-800 p-1.5 rounded-xl transition-all flex items-center justify-center">
                                                     <button @click="sendSticker(f.source, f.sticker_id, f.sticker_url)" class="w-full h-full flex items-center justify-center">
                                                         <img :src="f.sticker_url" class="w-16 h-16 object-contain" />
                                                     </button>
                                                     <button @click="toggleFavorite(f.source, f.sticker_id, f.sticker_url)" class="absolute top-0 right-0 p-1 bg-black/70 hover:bg-black text-rose-400 rounded-full text-[8px] opacity-0 group-hover:opacity-100 transition-opacity">
                                                         <i class="fas fa-times"></i>
                                                     </button>
                                                 </div>
                                             </template>
                                         </div>
                                     </template>
                                     <template x-if="favorites.length === 0">
                                         <div class="h-full flex flex-col items-center justify-center text-slate-500 text-center py-12">
                                             <span class="text-2xl mb-1">⭐</span>
                                             <p class="text-xs">Klik ikon bintang pada stiker di chat untuk menambahkannya ke favorit.</p>
                                         </div>
                                     </template>
                                 </div>

                                 <!-- Trending & Search -->
                                 <div x-show="activeTab === 'trending' || activeTab === 'search'">
                                     <!-- Quick Categories inside GIPHY panels -->
                                     <template x-if="activeTab === 'trending' && categories.length > 0">
                                         <div class="flex items-center gap-1.5 mb-3 overflow-x-auto pb-1 text-[10px] select-none custom-scrollbar">
                                             <template x-for="cat in categories" :key="cat.name">
                                                 <button @click="selectCategory(cat.query)" class="bg-slate-950/60 hover:bg-slate-800 text-slate-200 border border-white/10 px-2 py-0.5 rounded-full transition-all shrink-0">
                                                     <span x-text="cat.emoji + ' ' + cat.name"></span>
                                                 </button>
                                             </template>
                                         </div>
                                     </template>

                                     <template x-if="stickers.length > 0">
                                         <div class="grid grid-cols-4 gap-2">
                                             <template x-for="stk in stickers" :key="stk.id">
                                                 <button @click="sendSticker('giphy', stk.id, stk.images.fixed_height.url)" class="hover:bg-slate-800 p-1.5 rounded-xl transition-all flex items-center justify-center">
                                                     <img :src="stk.images.fixed_height_small.url" class="w-16 h-16 object-contain" />
                                                 </button>
                                             </template>
                                         </div>
                                     </template>
                                     
                                     <!-- Powered by Giphy Attribution -->
                                     <div class="flex items-center justify-center py-4 shrink-0 opacity-40 select-none">
                                         <img src="https://developers.giphy.com/branch/master/static/header-logo-0f40837cb5ce04d4a971569d2b00f135.gif" alt="Powered by GIPHY" class="h-4 object-contain">
                                     </div>
                                 </div>

                                 <!-- Custom Packs Viewports -->
                                 <template x-for="pk in packs" :key="'pack_view_' + pk.id">
                                     <div x-show="activeTab === 'pack_' + pk.id">
                                         <template x-if="pk.stickers.length > 0">
                                             <div class="grid grid-cols-4 gap-2">
                                                 <template x-for="st in pk.stickers" :key="st.id">
                                                     <button @click="sendSticker('custom', st.id, st.file_url)" class="hover:bg-slate-800 p-1.5 rounded-xl transition-all flex items-center justify-center">
                                                         <img :src="st.file_url" class="w-16 h-16 object-contain" />
                                                     </button>
                                                 </template>
                                             </div>
                                         </template>
                                         <template x-if="pk.stickers.length === 0">
                                             <div class="h-full flex flex-col items-center justify-center text-slate-500 text-center py-12">
                                                 <span class="text-2xl mb-1">📦</span>
                                                 <p class="text-xs">Pack ini belum memiliki stiker.</p>
                                             </div>
                                         </template>
                                     </div>
                                 </template>

                             </div>
                         </div>
                    </div>

                    <form wire:submit.prevent="sendMessage" class="flex items-center gap-2">
                        
                        <!-- Toggle Sticker Button -->
                        <button type="button" @click="stickerPickerOpen = !stickerPickerOpen" class="text-sky-300 hover:text-white transition-all text-lg px-2" title="Kirim Stiker">
                            🎟️
                        </button>

                        <input type="text" 
                            wire:model="messageText"
                            placeholder="Ketik pesan pribadi..." 
                            class="flex-1 text-white placeholder-sky-300/70 text-xs sm:text-sm rounded-xl px-4 py-2.5 border border-white/20 focus:outline-none focus:ring-2 focus:ring-sky-400 transition-all"
                            style="background: rgba(255, 255, 255, 0.08);"
                            autocomplete="off"
                        />
                        <button type="submit" 
                            class="px-5 py-2.5 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white font-bold rounded-xl text-xs sm:text-sm transition-all shadow-md flex items-center gap-2 shrink-0">
                            <span>Kirim</span>
                            <i class="fas fa-paper-plane text-xs"></i>
                        </button>
                    </form>
                </div>

            @else
                {{-- Empty State (No Active User Selected) --}}
                <div class="h-full flex flex-col items-center justify-center text-sky-200 p-8 text-center">
                    <div class="w-20 h-20 rounded-full flex items-center justify-center mb-4 border border-white/20 shadow-lg" style="background: rgba(255, 255, 255, 0.08);">
                        <i class="fas fa-comments text-3xl text-sky-300"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-1">Pilih Kontak Percakapan</h3>
                    <p class="text-xs text-sky-200 max-w-sm">
                        Pilih staf di sebelah kiri untuk melihat pesan atau mulai bertukar pesan secara pribadi dan real-time.
                    </p>
                </div>
            @endif

        </div>
    </div>
</div>

{{-- Prevent Livewire "This page has expired" (419 Error Bypass) & CSRF Keepalive --}}
<script>
    document.addEventListener('livewire:initialized', () => {
        if (window.Livewire) {
            Livewire.hook('request', ({ fail }) => {
                fail(({ status, preventDefault }) => {
                    if (status === 419) {
                        preventDefault();
                        window.location.reload();
                    }
                });
            });
        }
    });

    // Auto CSRF Token Refresh every 4 minutes to prevent session expiration
    setInterval(function() {
        fetch('/csrf-token', { cache: 'no-store' })
            .then(r => r.json())
            .then(data => {
                if (data && data.csrf_token) {
                    document.querySelectorAll('input[name="_token"]').forEach(el => el.value = data.csrf_token);
                    const meta = document.querySelector('meta[name="csrf-token"]');
                    if (meta) meta.setAttribute('content', data.csrf_token);
                }
            }).catch(() => {});
    }, 240000);
</script>
