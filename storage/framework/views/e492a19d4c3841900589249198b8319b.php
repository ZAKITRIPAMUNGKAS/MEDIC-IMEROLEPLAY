

<?php $__env->startSection('title', 'Live Chat - Portal Medis MPK-BA'); ?>

<?php $__env->startSection('content'); ?>
    <style>
        /* Hide global footer for this full-screen page */
        footer {
            display: none !important;
        }
    </style>
    <div class="fixed top-16 left-0 right-0 bottom-0 flex items-center justify-center p-4 overflow-hidden bg-slate-900 z-0">
        <!-- Background Gradients -->
        <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900"></div>
        <div class="absolute inset-0 bg-black bg-opacity-30"></div>

        <!-- Decorative Orbs -->
        <div
            class="absolute top-[-10%] right-[-5%] w-[500px] h-[500px] bg-gradient-to-br from-blue-500/20 to-cyan-400/20 rounded-full blur-3xl pointer-events-none">
        </div>
        <div
            class="absolute bottom-[-10%] left-[-5%] w-[500px] h-[500px] bg-gradient-to-tr from-indigo-500/20 to-purple-400/20 rounded-full blur-3xl pointer-events-none">
        </div>

        <div class="relative z-10 max-w-6xl w-full flex flex-col h-full">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6 flex-shrink-0">
                <div class="flex items-center gap-4">
                    <a href="<?php echo e(route('chat.page')); ?>"
                        class="inline-flex items-center justify-center w-10 h-10 bg-white/10 backdrop-blur-md rounded-xl shadow-lg border border-white/20 hover:bg-white/20 transition-all text-white">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-white flex items-center gap-3">
                            <i class="fas fa-comments text-blue-400"></i>
                            Live Chat Medis
                        </h1>
                        <p class="text-blue-100/70 text-sm hidden sm:block">
                            Chat anonim dengan tim medis kami
                        </p>
                    </div>
                </div>

                <div
                    class="flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-md rounded-full shadow-lg border border-white/10">
                    <span
                        class="w-2.5 h-2.5 bg-green-500 rounded-full animate-pulse shadow-[0_0_10px_rgba(34,197,94,0.5)]"></span>
                    <span class="text-xs font-bold text-white tracking-wide">ONLINE</span>
                </div>
            </div>

            <!-- Chat Container (Glass Effect) -->
            <div
                class="flex-1 glass-effect rounded-2xl elegant-shadow-lg overflow-hidden flex flex-col relative border border-white/10">
                <!-- Grid Pattern Overlay -->
                <div class="absolute inset-0 z-0 opacity-[0.05]"
                    style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 20px 20px;"></div>

                <div class="relative z-10 h-full">
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('chat-widget', ['pageMode' => true]);

$key = null;

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3458577593-0', null);

$__html = app('livewire')->mount($__name, $__params, $key);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                </div>
            </div>

            <!-- Footer Info -->
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4 flex-shrink-0">
                <div class="glass-effect px-4 py-3 rounded-xl border border-white/10 flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-500/20 rounded-lg flex items-center justify-center text-blue-400">
                        <i class="fas fa-user-secret"></i>
                    </div>
                    <div>
                        <p class="text-white font-bold text-sm">100% Anonim</p>
                    </div>
                </div>

                <div class="glass-effect px-4 py-3 rounded-xl border border-white/10 flex items-center gap-3">
                    <div class="w-8 h-8 bg-green-500/20 rounded-lg flex items-center justify-center text-green-400">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div>
                        <p class="text-white font-bold text-sm">Respon Cepat</p>
                    </div>
                </div>

                <div class="glass-effect px-4 py-3 rounded-xl border border-white/10 flex items-center gap-3">
                    <div class="w-8 h-8 bg-purple-500/20 rounded-lg flex items-center justify-center text-purple-400">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div>
                        <p class="text-white font-bold text-sm">Aman & Privat</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\website\EMS-IME\public_html\resources\views/chat/livechat.blade.php ENDPATH**/ ?>