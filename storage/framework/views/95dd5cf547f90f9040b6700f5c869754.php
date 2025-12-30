<div class="fixed bottom-4 right-4 z-50 flex flex-col items-end" wire:poll.15s="loadMessages">

    <!-- Chat Box -->
    <div x-show="$wire.isOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="mb-4 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col"
         style="height: 500px; display: none;">

        <!-- Header -->
        <div class="bg-gradient-to-r from-sky-500 to-cyan-500 p-4 text-white flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-comment-medical text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-base">Live Chat Medis</h3>
                    <p class="text-xs text-sky-100 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 bg-green-400 rounded-full"></span>
                        Online
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasSession): ?>
                    <button wire:click="endSession" title="Akhiri Sesi"
                        class="text-white/80 hover:text-white transition-colors p-1.5 hover:bg-white/10 rounded-lg">
                        <i class="fas fa-power-off text-sm"></i>
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <button wire:click="toggleChat" class="text-white/80 hover:text-white transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        <!-- Chat Content -->
        <div class="flex-1 overflow-y-auto p-4 bg-slate-50" id="chat-messages">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$hasSession): ?>
                <!-- Welcome Screen -->
                <div class="h-full flex flex-col justify-center items-center text-center px-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-sky-100 to-cyan-100 rounded-2xl flex items-center justify-center mb-4">
                        <i class="fas fa-user-md text-3xl text-sky-600"></i>
                    </div>
                    
                    <h4 class="font-bold text-slate-800 text-lg mb-2">Selamat Datang</h4>
                    <p class="text-slate-600 text-sm mb-6 leading-relaxed">
                        Mulai chat dengan tim medis kami
                    </p>

                    <form wire:submit.prevent="startChat" class="w-full space-y-3">
                        <div class="text-left">
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Anda</label>
                            <input type="text" wire:model="name"
                                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none transition-all text-sm"
                                placeholder="Masukkan nama..." <?php if(auth()->guard()->check()): ?> readonly <?php endif; ?>>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        
                        <button type="submit"
                            class="w-full py-2.5 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white font-semibold rounded-lg shadow-md transition-all">
                            Mulai Chat
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <!-- Messages -->
                <div class="space-y-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $chatMessages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex <?php echo e($msg->is_staff_reply ? 'justify-start' : 'justify-end'); ?>">
                            <div class="flex flex-col <?php echo e($msg->is_staff_reply ? 'items-start' : 'items-end'); ?> max-w-[75%]">
                                <span class="text-[10px] text-slate-500 mb-1 px-1">
                                    <?php echo e($msg->is_staff_reply ? ($msg->user->name ?? 'Tim Medis') : 'Anda'); ?>

                                </span>
                                <div class="px-4 py-2.5 rounded-2xl text-sm <?php echo e($msg->is_staff_reply 
                                    ? 'bg-white border border-slate-200 text-slate-700' 
                                    : 'bg-gradient-to-r from-sky-500 to-cyan-500 text-white'); ?>">
                                    <?php echo e($msg->message); ?>

                                </div>
                                <span class="text-[9px] text-slate-400 mt-0.5 px-1">
                                    <?php echo e($msg->created_at->format('H:i')); ?>

                                </span>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <!-- Footer -->
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasSession): ?>
            <div class="p-3 bg-white border-t border-slate-200">
                <form wire:submit.prevent="sendMessage" class="flex gap-2">
                    <input type="text" wire:model="message"
                        class="flex-1 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-sky-200 focus:border-sky-400 outline-none text-sm"
                        placeholder="Ketik pesan..." autofocus>
                    <button type="submit"
                        class="w-10 h-10 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white rounded-lg flex items-center justify-center transition-all"
                        wire:loading.attr="disabled">
                        <i class="fas fa-paper-plane text-sm"></i>
                    </button>
                </form>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <!-- Toggle Button -->
    <button wire:click="toggleChat"
        class="w-14 h-14 bg-gradient-to-br from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white rounded-full shadow-lg hover:shadow-xl transition-all flex items-center justify-center">
        <i class="fas <?php echo e($isOpen ? 'fa-times' : 'fa-comment-alt'); ?> text-xl"></i>
    </button>

    <script>
        const chatContainer = document.getElementById('chat-messages');
        if (chatContainer) {
            const observer = new MutationObserver(() => {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            });
            observer.observe(chatContainer, { childList: true, subtree: true });
        }
    </script>
</div><?php /**PATH D:\website\EMS-IME\public_html\resources\views/livewire/chat-widget.blade.php ENDPATH**/ ?>