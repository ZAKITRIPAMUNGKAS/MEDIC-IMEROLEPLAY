

<?php $__env->startSection('title', 'Live Chat Dashboard - Portal Medis MPK-BA'); ?>

<?php $__env->startSection('content'); ?>
    <div class="relative min-h-[calc(100vh-64px)] py-4 px-4 sm:px-6 lg:px-8">
        <!-- Background Gradients -->
        <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700"></div>
        <div class="absolute inset-0 bg-black bg-opacity-20"></div>

        <div class="relative w-full px-2 text-white">
            <!-- Header Section - Redesigned -->
            <div class="backdrop-blur-xl border-2 border-sky-400/60 rounded-3xl shadow-2xl mb-4 overflow-hidden"
                style="background: linear-gradient(135deg, rgba(7, 89, 133, 0.95) 0%, rgba(14, 116, 144, 0.9) 100%);">

                <!-- Decorative Elements -->
                <div
                    class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-cyan-400/10 to-transparent rounded-full blur-3xl">
                </div>
                <div
                    class="absolute bottom-0 left-0 w-48 h-48 bg-gradient-to-tr from-sky-400/10 to-transparent rounded-full blur-2xl">
                </div>

                <div class="relative p-6 sm:p-8">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                        <!-- Left Section -->
                        <div class="flex items-start gap-5">
                            <!-- Icon Container -->
                            <div
                                class="hidden sm:flex items-center justify-center w-16 h-16 bg-gradient-to-br from-sky-400 to-cyan-500 rounded-2xl shadow-lg border-2 border-white/20">
                                <i class="fas fa-comments text-2xl text-white"></i>
                            </div>

                            <!-- Text Content -->
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">
                                        Live Chat Dashboard
                                    </h1>
                                    <span
                                        class="px-3 py-1 bg-gradient-to-r from-green-400 to-emerald-500 text-white text-xs font-bold rounded-full shadow-lg animate-pulse">
                                        LIVE
                                    </span>
                                </div>
                                <p class="text-sky-100 text-sm flex items-center gap-2">
                                    <i class="fas fa-shield-alt text-xs"></i>
                                    Kelola pesan dan konsultasi medis secara real-time
                                </p>
                            </div>
                        </div>

                        <!-- Right Section - Stats Cards -->
                        <div class="flex items-center gap-4">
                            <!-- System Status -->
                            <div class="bg-white/10 backdrop-blur-sm px-4 py-3 rounded-xl border border-white/20 shadow-lg">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 bg-green-500/30 rounded-full flex items-center justify-center border-2 border-green-400">
                                        <i class="fas fa-check-circle text-green-300 text-lg"></i>
                                    </div>
                                    <div>
                                        <span class="text-[10px] text-sky-200 font-medium block">System Status</span>
                                        <span class="flex items-center text-sm font-bold text-white">
                                            <span
                                                class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse shadow-[0_0_8px_rgba(74,222,128,0.8)]"></span>
                                            Online
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Stats -->
                            <div
                                class="hidden xl:flex bg-white/10 backdrop-blur-sm px-4 py-3 rounded-xl border border-white/20 shadow-lg">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 bg-sky-500/30 rounded-full flex items-center justify-center border-2 border-sky-300">
                                        <i class="fas fa-clock text-sky-200 text-lg"></i>
                                    </div>
                                    <div>
                                        <span class="text-[10px] text-sky-200 font-medium block">Response Time</span>
                                        <span class="text-sm font-bold text-white flex items-center gap-1">
                                            <i class="fas fa-bolt text-yellow-300 text-xs"></i>
                                            ~2 menit
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat Interface -->
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin-chat');

$key = null;

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3823312114-0', null);

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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\website\EMS-IME\public_html\resources\views\admin\chat\index.blade.php ENDPATH**/ ?>