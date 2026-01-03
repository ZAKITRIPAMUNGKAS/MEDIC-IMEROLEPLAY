<div class="{{ $pageMode ? 'h-full' : '' }}">
    @if($pageMode)
        {{-- Full Page Mode - Always show chat interface --}}
        <div class="h-full flex flex-col bg-white">
            @if($showFeedbackForm)
                {{-- Feedback Form --}}
                <div class="flex-1 overflow-y-auto p-6">
                    <div class="max-w-2xl mx-auto">
                        <div class="flex items-center gap-2 mb-6 pb-4 border-b border-slate-200">
                            <button wire:click="toggleFeedbackForm" type="button"
                                class="text-slate-600 hover:text-slate-800 transition-colors">
                                <i class="fas fa-arrow-left"></i>
                            </button>
                            <h4 class="font-bold text-slate-800 text-lg">Laporan & Masukan</h4>
                        </div>

                        @if($feedbackSuccess)
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-300 rounded-xl p-4 mb-4 shadow-sm">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-check-circle text-white text-xl"></i>
                                    </div>
                                    <div>
                                        <h5 class="font-bold text-green-900 text-sm mb-1">Terima kasih!</h5>
                                        <p class="text-green-700 text-xs leading-relaxed">
                                            Feedback Anda telah dikirim secara anonim. Kami akan meninjau sesegera mungkin.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <form wire:submit.prevent="submitFeedback" class="space-y-4">
                            <!-- Type Selection -->
                            <div class="text-left">
                                <label class="block text-xs font-bold text-slate-700 mb-2">Pilih Tipe</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <button type="button" wire:click="$set('feedbackType', 'masukan')"
                                        class="px-4 py-3 rounded-xl border-2 text-sm font-medium transition-all
                                            {{ $feedbackType === 'masukan' ? 'border-green-500 bg-green-50 text-green-700' : 'border-slate-300 text-slate-600 hover:border-green-400' }}">
                                        <i class="fas fa-lightbulb mr-1"></i> Masukan
                                    </button>
                                    <button type="button" wire:click="$set('feedbackType', 'laporan')"
                                        class="px-4 py-3 rounded-xl border-2 text-sm font-medium transition-all
                                            {{ $feedbackType === 'laporan' ? 'border-red-500 bg-red-50 text-red-700' : 'border-slate-300 text-slate-600 hover:border-red-400' }}">
                                        <i class="fas fa-exclamation-triangle mr-1"></i> Laporan
                                    </button>
                                </div>
                                @error('feedbackType') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Subject Field -->
                            <div class="text-left">
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Subjek</label>
                                <input type="text" wire:model="feedbackSubject"
                                    class="w-full px-4 py-2.5 rounded-lg border-2 border-slate-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none transition-all text-sm"
                                    placeholder="Ringkasan singkat...">
                                @error('feedbackSubject') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Message Field -->
                            <div class="text-left">
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pesan</label>
                                <textarea wire:model="feedbackMessage" rows="4"
                                    class="w-full px-4 py-2.5 rounded-lg border-2 border-slate-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none transition-all text-sm resize-none"
                                    placeholder="Jelaskan feedback Anda secara detail..."></textarea>
                                @error('feedbackMessage') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Image Upload Field -->
                            <div class="text-left">
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    <i class="fas fa-image mr-1"></i> Lampiran Gambar (Opsional)
                                </label>
                                <input type="file" wire:model="feedbackImage" accept="image/*"
                                    class="w-full px-4 py-2.5 rounded-lg border-2 border-slate-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none transition-all text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
                                @error('feedbackImage') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                
                                @if ($feedbackImage)
                                    <div class="mt-2 text-xs text-slate-600 flex items-center gap-2">
                                        <i class="fas fa-check-circle text-green-500"></i>
                                        <span>{{ $feedbackImage->getClientOriginalName() }}</span>
                                    </div>
                                @endif
                                
                                <p class="text-xs text-slate-500 mt-1">Max 5MB (JPG, PNG, GIF)</p>
                            </div>

                            <!-- Anonymous Info -->
                            <div class="bg-sky-50 border border-sky-200 rounded-lg p-3">
                                <div class="flex items-start gap-2">
                                    <i class="fas fa-user-secret text-sky-600 text-sm mt-0.5"></i>
                                    <p class="text-sky-700 text-xs leading-relaxed">
                                        Feedback dikirim secara <strong>anonim</strong>. Identitas Anda tidak akan tercatat.
                                    </p>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit"
                                class="w-full py-3 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove>
                                    <i class="fas fa-paper-plane mr-2"></i> Kirim Feedback Anonim
                                </span>
                                <span wire:loading>
                                    <i class="fas fa-spinner fa-spin mr-2"></i> Mengirim...
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            @else
                {{-- Chat Interface - Show always in page mode --}}
                <div class="flex-1 flex flex-col h-full">
                    {{-- Chat Header --}}
                    <div class="bg-gradient-to-r from-sky-500 to-cyan-500 text-white p-4 flex items-center justify-between shadow-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-user-md"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-sm">Tim Medis</h3>
                                <p class="text-xs text-sky-100">Online - Siap Membantu</p>
                            </div>
                        </div>
                        @if($hasSession)
                            <button wire:click="endSession" class="text-white hover:bg-white/20 rounded-lg px-3 py-2 transition-all text-sm">
                                <i class="fas fa-times mr-1"></i> Akhiri
                            </button>
                        @else
                            <button wire:click="toggleFeedbackForm" type="button" class="text-white hover:bg-white/20 rounded-lg px-3 py-2 transition-all text-xs">
                                <i class="fas fa-comment-dots mr-1"></i> Feedback
                            </button>
                        @endif
                    </div>

                    {{-- No Session Info Banner --}}
                    @if(!$hasSession)
                        <div class="bg-gradient-to-r from-blue-50 to-cyan-50 border-b-2 border-blue-200 p-4">
                            <div class="flex items-start gap-3 max-w-2xl mx-auto">
                                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-user-secret text-white"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-blue-900 text-sm mb-1">Chat Anonim</h4>
                                    <p class="text-blue-700 text-xs leading-relaxed">
                                        Ketik pesan Anda di bawah untuk memulai chat secara anonim dengan tim medis kami. Identitas Anda akan dijaga kerahasiaannya.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Messages Container --}}
                    <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-slate-50" id="messages" wire:poll.5s="loadMessages">
                        @forelse($chatMessages as $msg)
                            <div class="flex {{ $msg->is_staff_reply ? 'justify-start' : 'justify-end' }}">
                                <div class="max-w-[75%] {{ $msg->is_staff_reply ? 'bg-white' : 'bg-sky-500 text-white' }} rounded-2xl px-4 py-3 shadow-sm">
                                    @if($msg->is_staff_reply)
                                        <p class="text-xs text-slate-500 font-medium mb-1">
                                            <i class="fas fa-user-md mr-1"></i> Staff Medis
                                        </p>
                                    @endif
                                    
                                    {{-- Display Attachment if exists --}}
                                    @if($msg->attachment_path)
                                        @if($msg->attachment_type === 'image')
                                            <div class="mb-2">
                                                <img src="{{ Storage::url($msg->attachment_path) }}" 
                                                    alt="Attachment" 
                                                    class="rounded-lg max-w-full max-h-64 cursor-pointer hover:opacity-90 transition-opacity"
                                                    onclick="window.open('{{ Storage::url($msg->attachment_path) }}', '_blank')">
                                            </div>
                                        @else
                                            <div class="mb-2 flex items-center gap-2 p-2 {{ $msg->is_staff_reply ? 'bg-slate-100' : 'bg-sky-600' }} rounded-lg">
                                                <i class="fas fa-file-pdf text-lg {{ $msg->is_staff_reply ? 'text-red-500' : 'text-white' }}"></i>
                                                <a href="{{ Storage::url($msg->attachment_path) }}" 
                                                    target="_blank" 
                                                    class="flex-1 text-xs font-medium {{ $msg->is_staff_reply ? 'text-slate-700 hover:text-sky-600' : 'text-white hover:underline' }} truncate">
                                                    {{ basename($msg->attachment_path) }}
                                                </a>
                                                <i class="fas fa-download text-xs {{ $msg->is_staff_reply ? 'text-slate-400' : 'text-sky-100' }}"></i>
                                            </div>
                                        @endif
                                    @endif
                                    
                                    @if($msg->message)
                                        <p class="text-sm leading-relaxed whitespace-pre-wrap {{ $msg->is_staff_reply ? 'text-slate-700' : 'text-white' }}">{{ $msg->message }}</p>
                                    @endif
                                    
                                    <p class="text-[10px] mt-1 {{ $msg->is_staff_reply ? 'text-slate-400' : 'text-sky-100' }}">
                                        {{ $msg->created_at->format('H:i') }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12 text-slate-400">
                                <i class="fas fa-comments text-4xl mb-3"></i>
                                <p class="text-sm">Belum ada pesan. Mulai percakapan!</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Message Input --}}
                    <div class="bg-white border-t border-slate-200 p-4">
                        {{-- File Preview --}}
                        @if($attachment)
                            <div class="mb-3 flex items-center gap-2 p-3 bg-sky-50 border border-sky-200 rounded-lg">
                                <i class="fas fa-paperclip text-sky-600"></i>
                                <span class="flex-1 text-xs text-slate-700 font-medium truncate">{{ $attachment->getClientOriginalName() }}</span>
                                <button type="button" wire:click="$set('attachment', null)" 
                                    class="text-red-500 hover:text-red-700 transition-colors">
                                    <i class="fas fa-times text-sm"></i>
                                </button>
                            </div>
                        @endif
                        
                        {{-- Error Messages --}}
                        @error('message')
                            <div class="mb-2 text-xs text-red-500 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                        @error('attachment')
                            <div class="mb-2 text-xs text-red-500 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                        
                        <form wire:submit.prevent="sendMessage" class="flex gap-2">
                            {{-- File Upload Button --}}
                            <label for="attachment-input" 
                                class="flex items-center justify-center w-12 h-12 bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-sky-600 rounded-xl cursor-pointer transition-all border border-slate-200">
                                <i class="fas fa-paperclip text-lg"></i>
                                <input type="file" id="attachment-input" wire:model="attachment" class="hidden"
                                    accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx">
                            </label>
                            
                            <input type="text" wire:model="message"
                                class="flex-1 px-4 py-3 rounded-xl border-2 border-slate-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none text-sm"
                                placeholder="Ketik pesan atau lampirkan file..."
                                autocomplete="off">
                            <button type="submit"
                                class="px-6 py-3 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white font-semibold rounded-xl transition-all">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    @else
        {{-- Floating Widget Mode - Redirect Button --}}
        <div class="fixed bottom-6 right-6 z-50">
            <a href="{{ route('chat.page') }}" 
                class="flex items-center gap-3 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white px-6 py-4 rounded-2xl shadow-2xl hover:shadow-3xl transition-all transform hover:-translate-y-1 group">
                <div class="relative">
                    <i class="fas fa-comments text-2xl"></i>
                    
                    {{-- Status Dot (Online) --}}
                    <span class="absolute -top-1 -right-1 w-3 h-3 bg-green-400 rounded-full border-2 border-white {{ !$hasUnreadMessages || $isOpen ? 'block' : 'hidden' }}"></span>
                    
                    {{-- Unread Message Badge (Red Dot) --}}
                    @if($hasUnreadMessages && !$isOpen)
                        <span class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-red-500 rounded-full border-2 border-white animate-bounce flex items-center justify-center">
                            <span class="w-1.5 h-1.5 bg-white rounded-full"></span>
                        </span>
                    @endif
                </div>
                <span class="font-bold text-sm hidden sm:block">Live Chat</span>
            </a>
        </div>
    @endif
    
    <script>
        // Auto-scroll to bottom when new messages arrive
        document.addEventListener('livewire:init', () => {
            Livewire.hook('message.processed', (message, component) => {
                const messagesContainer = document.getElementById('messages');
                if (messagesContainer) {
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                }
            });
        });
    </script>
</div>