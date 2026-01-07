

<?php $__env->startSection('title', 'Hubungi Kami - Portal Medis MPK-BA'); ?>

<?php $__env->startSection('content'); ?>
    <div class="relative min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 overflow-hidden">
        <!-- Background Gradients (Matches form.blade.php) -->
        <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900"></div>
        <div class="absolute inset-0 bg-black bg-opacity-30"></div>

        <!-- Decorative Orbs -->
        <div
            class="absolute top-[-10%] right-[-5%] w-[500px] h-[500px] bg-gradient-to-br from-blue-500/20 to-cyan-400/20 rounded-full blur-3xl pointer-events-none">
        </div>
        <div
            class="absolute bottom-[-10%] left-[-5%] w-[500px] h-[500px] bg-gradient-to-tr from-indigo-500/20 to-purple-400/20 rounded-full blur-3xl pointer-events-none">
        </div>

        <div class="relative z-10 max-w-5xl w-full mx-auto">
            <!-- Header -->
            <div class="text-center mb-12">
                <a href="<?php echo e(url('/')); ?>"
                    class="inline-flex items-center justify-center w-12 h-12 bg-white/10 backdrop-blur-md rounded-xl shadow-lg border border-white/20 hover:bg-white/20 transition-all text-white mb-8">
                    <i class="fas fa-arrow-left"></i>
                </a>

                <h1 class="text-4xl sm:text-5xl font-bold text-white mb-4 tracking-tight">
                    Hubungi Kami
                </h1>
                <p class="text-blue-100/80 text-lg max-w-2xl mx-auto font-medium">
                    Silakan pilih layanan bantuan yang Anda butuhkan
                </p>
            </div>

            <!-- Choice Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8 max-w-4xl mx-auto">
                <!-- Live Chat Card -->
                <a href="<?php echo e(route('chat.livechat')); ?>"
                    class="group glass-effect rounded-2xl p-8 sm:p-10 hover:bg-white/10 transition-all duration-300 transform hover:-translate-y-2 relative overflow-hidden border border-white/10 shadow-2xl">
                    <div
                        class="absolute inset-0 bg-gradient-to-b from-blue-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                    </div>

                    <div class="relative z-10 flex flex-col items-center text-center">
                        <div
                            class="w-24 h-24 bg-gradient-to-br from-blue-500 to-cyan-400 rounded-2xl flex items-center justify-center text-white mb-6 shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-comments text-4xl"></i>
                        </div>

                        <h2 class="text-2xl font-bold text-white mb-3 flex items-center gap-2">
                            Live Chat
                            <span
                                class="px-2.5 py-0.5 bg-green-500/20 border border-green-500/30 text-green-400 text-xs font-bold rounded-full uppercase tracking-wider">
                                Online
                            </span>
                        </h2>

                        <p class="text-blue-100/70 text-sm leading-relaxed mb-8">
                            Konsultasi langsung dengan tim medis kami secara realtime, cepat, dan anonim.
                        </p>

                        <div
                            class="w-full py-4 bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold rounded-xl backdrop-blur-sm transition-all flex items-center justify-center gap-2 group-hover:shadow-lg group-hover:shadow-blue-500/20">
                            Mulai Chat
                            <i
                                class="fas fa-arrow-right text-sm transform group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                </a>

                <!-- Feedback Card -->
                <a href="<?php echo e(route('feedback.form')); ?>"
                    class="group glass-effect rounded-2xl p-8 sm:p-10 hover:bg-white/10 transition-all duration-300 transform hover:-translate-y-2 relative overflow-hidden border border-white/10 shadow-2xl">
                    <div
                        class="absolute inset-0 bg-gradient-to-b from-indigo-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                    </div>

                    <div class="relative z-10 flex flex-col items-center text-center">
                        <div
                            class="w-24 h-24 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-2xl flex items-center justify-center text-white mb-6 shadow-lg shadow-indigo-500/30 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-paper-plane text-4xl"></i>
                        </div>

                        <h2 class="text-2xl font-bold text-white mb-3">
                            Laporan & Masukan
                        </h2>

                        <p class="text-blue-100/70 text-sm leading-relaxed mb-8">
                            Kirimkan kritik, saran, atau laporan masalah teknis kepada tim pengembang.
                        </p>

                        <div
                            class="w-full py-4 bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold rounded-xl backdrop-blur-sm transition-all flex items-center justify-center gap-2 group-hover:shadow-lg group-hover:shadow-indigo-500/20">
                            Kirim Sekarang
                            <i
                                class="fas fa-arrow-right text-sm transform group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Footer Info -->
            <div class="mt-12 text-center">
                <p class="text-blue-200/60 text-sm flex items-center justify-center gap-2">
                    <i class="fas fa-shield-alt"></i>
                    <span>Privasi & Keamanan Data Terjamin</span>
                </p>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\website\EMS-IME\public_html\resources\views/chat/index.blade.php ENDPATH**/ ?>