<div class="w-full">
    <div class="flex flex-col lg:flex-row gap-4 lg:gap-6" wire:poll.30s="loadSessions"
        style="min-height: 600px; max-height: calc(100vh - 12rem);">

        <!-- Sidebar List -->
        <div class="w-full lg:w-1/2 backdrop-blur-xl border-2 border-sky-400/60 rounded-2xl shadow-2xl flex flex-col"
            style="background-color: rgba(7, 89, 133, 0.8); min-height: 500px;">

            <!-- Sidebar Header -->
            <div class="p-4 border-b border-sky-400/30 bg-black/10 shrink-0">
                <h2 class="font-bold text-white text-lg mb-3">Daftar Chat</h2>
                <div class="flex p-1 bg-black/20 rounded-xl">
                    <button wire:click="$set('filterStatus', 'open')"
                        class="flex-1 px-3 py-1.5 text-xs font-bold rounded-lg transition-all {{ $filterStatus === 'open' ? 'bg-gradient-to-r from-sky-500 to-cyan-500 text-white shadow-lg' : 'text-sky-200 hover:text-white hover:bg-white/5' }}">
                        Aktif
                    </button>
                    <button wire:click="$set('filterStatus', 'closed')"
                        class="flex-1 px-3 py-1.5 text-xs font-bold rounded-lg transition-all {{ $filterStatus === 'closed' ? 'bg-gradient-to-r from-sky-500 to-cyan-500 text-white shadow-lg' : 'text-sky-200 hover:text-white hover:bg-white/5' }}">
                        Selesai
                    </button>
                    <button wire:click="$set('filterStatus', 'all')"
                        class="flex-1 px-3 py-1.5 text-xs font-bold rounded-lg transition-all {{ $filterStatus === 'all' ? 'bg-gradient-to-r from-sky-500 to-cyan-500 text-white shadow-lg' : 'text-sky-200 hover:text-white hover:bg-white/5' }}">
                        Semua
                    </button>
                </div>
            </div>

            <!-- List -->
            <div class="flex-1 overflow-y-auto custom-scrollbar">
                @if(count($sessions) > 0)
                    <div class="divide-y divide-sky-400/20">
                        @foreach($sessions as $session)
                            <div wire:click="selectSession({{ $session->id }})"
                                class="p-4 cursor-pointer transition-all hover:bg-white/10 group {{ $activeSessionId === $session->id ? 'bg-white/10 border-l-4 border-sky-400' : 'border-l-4 border-transparent' }}">
                                <div class="flex justify-between items-start mb-1">
                                    <h3
                                        class="font-bold text-white group-hover:text-sky-200 transition-colors {{ $activeSessionId === $session->id ? 'text-sky-300' : '' }}">
                                        {{ $session->anonymous_name }}
                                    </h3>
                                    @if(!$session->is_read)
                                        <span
                                            class="w-2.5 h-2.5 bg-red-400 rounded-full shadow-[0_0_8px_rgba(248,113,113,0.6)] animate-pulse"></span>
                                    @endif
                                </div>
                                <p class="text-xs text-sky-200/80 truncate mb-2">
                                    {{ $session->messages->last()?->message ?? 'Belum ada pesan' }}
                                </p>
                                <div class="flex justify-between items-center text-[10px] text-sky-300/60 font-medium">
                                    <span>{{ $session->updated_at->diffForHumans() }}</span>
                                    <span
                                        class="px-2 py-0.5 rounded-full border {{ $session->status === 'open' ? 'bg-green-500/20 text-green-300 border-green-500/30' : 'bg-slate-500/30 text-slate-300 border-slate-500/30' }}">
                                        {{ $session->status === 'open' ? 'Berjalan' : 'Selesai' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="h-full flex flex-col items-center justify-center text-sky-300/50 p-8">
                        <div class="w-16 h-16 bg-white/5 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-inbox text-3xl"></i>
                        </div>
                        <p class="text-center font-medium">Tidak ada chat {{ $filterStatus === 'open' ? 'aktif' : '' }} saat
                            ini</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Chat Area -->
        <div class="w-full lg:w-1/2 backdrop-blur-xl border-2 border-sky-400/60 rounded-2xl shadow-2xl flex flex-col"
            style="background-color: rgba(7, 89, 133, 0.8); min-height: 500px;">

            @if($activeSession)
                <!-- Chat Header -->
                <div
                    class="p-4 border-b border-sky-400/30 bg-black/10 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 shadow-lg z-10 shrink-0">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-11 h-11 bg-gradient-to-br from-cyan-400 to-sky-600 rounded-xl shadow-lg flex items-center justify-center text-white font-bold text-xl border border-white/20 shrink-0">
                            {{ substr($activeSession->anonymous_name, 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-bold text-white text-lg tracking-wide truncate">
                                {{ $activeSession->anonymous_name }}
                            </h3>
                            <div class="flex items-center gap-2 text-xs text-sky-200">
                                <i class="fas fa-clock text-[10px]"></i>
                                Dimulai {{ $activeSession->created_at->format('d M H:i') }}
                            </div>
                        </div>
                    </div>
                    <div class="shrink-0">
                        @if($activeSession->status === 'open')
                            <button wire:click="closeSession"
                                class="w-full sm:w-auto px-4 py-2 bg-red-500/20 hover:bg-red-500/40 text-red-300 hover:text-red-100 rounded-lg text-sm font-semibold transition-all border border-red-500/30 flex items-center justify-center shadow-lg active:scale-95">
                                <i class="fas fa-check-circle mr-2"></i>Selesai & Tutup
                            </button>
                        @else
                            <span
                                class="px-4 py-2 bg-white/10 text-gray-300 rounded-lg text-sm font-semibold border border-white/10 flex items-center">
                                <i class="fas fa-lock mr-2"></i>Sesi Ditutup
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Messages -->
                <div class="flex-1 overflow-y-auto p-4 sm:p-6 relative" id="admin-chat-messages">
                    <!-- Background Pattern -->
                    <div class="absolute inset-0 opacity-10 pointer-events-none"
                        style="background-image: radial-gradient(#bae6fd 1px, transparent 1px); background-size: 24px 24px;">
                    </div>

                    <div class="space-y-4 sm:space-y-6 relative z-10 pb-4">
                        @foreach($chatMessages as $msg)
                                    <div class="flex {{ $msg->is_staff_reply ? 'justify-end' : 'justify-start' }} group">
                                        <div
                                            class="flex flex-col {{ $msg->is_staff_reply ? 'items-end' : 'items-start' }} max-w-[85%] sm:max-w-[75%]">
                                            <span class="text-[10px] text-sky-200/70 mb-1 px-1 font-medium">
                                                {{ $msg->is_staff_reply ? ($msg->user->name ?? 'Staff') : $activeSession->anonymous_name }}
                                            </span>
                                            <div class="px-4 sm:px-5 py-3 sm:py-3.5 rounded-2xl shadow-md transition-all duration-300 text-sm leading-7 whitespace-pre-wrap border
                                                                                                                        {{ $msg->is_staff_reply
                            ? 'bg-gradient-to-br from-sky-500 to-cyan-600 text-white rounded-tr-none border-sky-400/50 shadow-sky-500/20'
                            : 'bg-white/10 backdrop-blur-sm text-white rounded-tl-none border-white/20' }}">
                                                {{-- Display Attachment if exists --}}
                                                @if($msg->attachment_path)
                                                    @if($msg->attachment_type === 'image')
                                                        <div class="mb-2">
                                                            <a href="{{ Storage::url($msg->attachment_path) }}" target="_blank">
                                                                <img src="{{ Storage::url($msg->attachment_path) }}" alt="Attachment"
                                                                    class="rounded-lg max-w-full max-h-48 cursor-pointer hover:opacity-90 transition-opacity">
                                                            </a>
                                                        </div>
                                                    @else
                                                        <div class="mb-2 flex items-center gap-2 p-2 bg-black/20 rounded-lg">
                                                            <i class="fas fa-file-pdf text-lg text-white"></i>
                                                            <a href="{{ Storage::url($msg->attachment_path) }}" target="_blank"
                                                                class="flex-1 text-xs font-medium text-white hover:underline truncate">
                                                                {{ basename($msg->attachment_path) }}
                                                            </a>
                                                            <i class="fas fa-download text-xs text-white/70"></i>
                                                        </div>
                                                    @endif
                                                @endif

                                                {{ $msg->message }}
                                            </div>
                                            <span
                                                class="text-[10px] text-sky-300/50 mt-1 px-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                {{ $msg->created_at->format('H:i') }}
                                            </span>
                                        </div>
                                    </div>
                        @endforeach
                    </div>
                </div>

                <!-- Reply Input -->
                @if($activeSession->status === 'open')
                    <div class="p-3 sm:p-4 bg-black/20 border-t border-sky-400/30 backdrop-blur-md shrink-0">
                        {{-- File Preview --}}
                        @if($attachment)
                            <div
                                class="mb-3 flex items-center gap-2 p-3 bg-sky-900/40 border border-sky-400/30 rounded-lg backdrop-blur-sm">
                                <i class="fas fa-paperclip text-sky-400"></i>
                                <span
                                    class="flex-1 text-xs text-sky-100 font-medium truncate">{{ $attachment->getClientOriginalName() }}</span>
                                <button type="button" wire:click="$set('attachment', null)"
                                    class="text-red-400 hover:text-red-300 transition-colors">
                                    <i class="fas fa-times text-sm"></i>
                                </button>
                            </div>
                        @endif

                        {{-- Error Messages --}}
                        @error('replyMessage')
                            <div class="mb-2 text-xs text-red-400 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                        @error('attachment')
                            <div class="mb-2 text-xs text-red-400 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror

                        <form wire:submit.prevent="sendReply" class="flex gap-3 sm:gap-4 items-end">
                            {{-- File Upload Button --}}
                            <label for="admin-attachment"
                                class="w-11 h-11 sm:w-12 sm:h-12 bg-white/10 hover:bg-white/20 text-sky-200 hover:text-white rounded-xl flex items-center justify-center cursor-pointer transition-all border border-sky-400/30 shrink-0">
                                <i class="fas fa-paperclip text-lg"></i>
                                <input type="file" id="admin-attachment" wire:model="attachment" class="hidden"
                                    accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx">
                            </label>

                            <div class="flex-1 relative">
                                <textarea wire:model="replyMessage" rows="1" id="chat-textarea"
                                    class="w-full bg-white/10 border border-sky-400/30 rounded-xl px-3 sm:px-4 py-2 sm:py-3 text-white placeholder-sky-200/40 focus:ring-2 focus:ring-sky-400 focus:border-transparent outline-none resize-none min-h-[44px] sm:min-h-[50px] transition-all text-sm sm:text-base"
                                    placeholder="Ketik balasan untuk {{ $activeSession->anonymous_name }}..."></textarea>
                            </div>
                            <button type="submit" wire:loading.attr="disabled"
                                class="w-11 h-11 sm:w-12 sm:h-12 bg-gradient-to-br from-sky-500 to-cyan-500 hover:from-sky-400 hover:to-cyan-400 text-white rounded-xl flex items-center justify-center shadow-lg shadow-sky-500/30 transition-all hover:-translate-y-1 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed shrink-0">
                                <i class="fas fa-paper-plane text-base sm:text-lg"></i>
                            </button>
                        </form>
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="flex-1 flex flex-col items-center justify-center text-sky-200/50 p-8">
                    <div class="mb-6 relative">
                        <div class="absolute inset-0 bg-sky-400 blur-2xl opacity-20 rounded-full animate-pulse"></div>
                        <i class="fas fa-comments text-5xl sm:text-7xl relative z-10 drop-shadow-xl"></i>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold text-white mb-2 text-center">Belum ada chat dipilih</h3>
                    <p class="text-base sm:text-lg text-sky-200/70 max-w-md text-center">Pilih salah satu percakapan dari
                        daftar di sebelah kiri untuk mulai membalas pesan.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Scripts and Styles inside root element -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            const container = document.getElementById('admin-chat-messages');
            const textarea = document.getElementById('chat-textarea');

            const scrollBottom = () => {
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            };

            // Scroll on load
            setTimeout(scrollBottom, 100);

            // Scroll on new message
            if (container) {
                const observer = new MutationObserver(() => {
                    scrollBottom();
                });
                observer.observe(container, { childList: true, subtree: true });
            }

            // Auto resize textarea
            if (textarea) {
                textarea.addEventListener('input', function () {
                    this.style.height = 'auto';
                    this.style.height = Math.min(this.scrollHeight, 150) + 'px';
                    if (this.value === '') {
                        this.style.height = '';
                    }
                });
            }
        });

        // Handle event from Livewire
        document.addEventListener('chat-scroll-bottom', () => {
            const container = document.getElementById('admin-chat-messages');
            if (container) {
                setTimeout(() => {
                    container.scrollTop = container.scrollHeight;
                }, 100);
            }
        });
    </script>

    <style>
        /* Custom Scrollbar for Glass UI */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(56, 189, 248, 0.3);
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(56, 189, 248, 0.5);
        }
    </style>
</div>