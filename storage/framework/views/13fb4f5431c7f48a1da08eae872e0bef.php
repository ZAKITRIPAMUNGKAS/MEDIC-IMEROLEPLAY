

<?php $__env->startSection('title', 'Daftar Formulir - Portal Medis'); ?>

<?php $__env->startSection('content'); ?>
<div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700"></div>
    <div class="absolute inset-0 bg-black bg-opacity-20"></div>

    <div class="relative max-w-7xl w-full mx-auto">
        <!-- Header Section -->
        <div class="backdrop-blur-xl border-2 border-sky-400/60 rounded-2xl shadow-2xl p-6 md:p-8 mb-6" style="background-color: rgba(7, 89, 133, 0.9);">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">Daftar Formulir Medis</h1>
                    <p class="text-sky-200 text-lg">Kelola semua formulir dari pemain dan publik</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-gray-300 text-sm">Total Formulir</p>
                        <p class="text-2xl font-bold text-white"><?php echo e($forms->total()); ?></p>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <form method="GET" action="<?php echo e(route('staff.forms')); ?>" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari nama karakter atau deskripsi..." class="w-full bg-white/30 backdrop-blur-xl text-white placeholder-slate-200 border-2 border-sky-400/40 rounded-xl px-4 py-3 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm font-medium shadow-xl transition-all duration-200">
                </div>
                <div>
                    <select name="status" class="w-full bg-white/30 backdrop-blur-xl text-white border-2 border-sky-400/40 rounded-xl px-4 py-3 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm font-medium shadow-xl transition-all duration-200">
                        <option value="" class="bg-slate-800 text-slate-300">Semua Status</option>
                        <option value="pending" class="bg-slate-800 text-slate-300" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Pending</option>
                        <option value="approved" class="bg-slate-800 text-slate-300" <?php echo e(request('status') == 'approved' ? 'selected' : ''); ?>>Approved</option>
                        <option value="rejected" class="bg-slate-800 text-slate-300" <?php echo e(request('status') == 'rejected' ? 'selected' : ''); ?>>Rejected</option>
                    </select>
                </div>
                <div>
                    <select name="type" class="w-full bg-white/30 backdrop-blur-xl text-white border-2 border-sky-400/40 rounded-xl px-4 py-3 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm font-medium shadow-xl transition-all duration-200">
                        <option value="" class="bg-slate-800 text-slate-300">Jenis Surat</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->isAdmin() || in_array(optional(auth()->user()->role)->name, ['dokter_umum', 'dokter_spesialis']) || (optional(auth()->user()->role)->level ?? 0) >= 5): ?>
                        <option value="janji_temu" class="bg-slate-800 text-slate-300" <?php echo e(request('type') == 'janji_temu' ? 'selected' : ''); ?>>Janji Temu</option>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <option value="surat_kesehatan" class="bg-slate-800 text-slate-300" <?php echo e(request('type') == 'surat_kesehatan' ? 'selected' : ''); ?>>Surat Kesehatan</option>
                        <option value="operasi_plastik" class="bg-slate-800 text-slate-300" <?php echo e(request('type') == 'operasi_plastik' ? 'selected' : ''); ?>>Operasi Plastik</option>
                        <option value="tes_psikologi" class="bg-slate-800 text-slate-300" <?php echo e(request('type') == 'tes_psikologi' ? 'selected' : ''); ?>>Tes Psikologi</option>
                    </select>
                </div>
                <div>
                    <select name="category" class="w-full bg-white/30 backdrop-blur-xl text-white border-2 border-sky-400/40 rounded-xl px-4 py-3 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm font-medium shadow-xl transition-all duration-200">
                        <option value="" class="bg-slate-800 text-slate-300">Semua Kategori</option>
                        <option value="konsultasi" class="bg-slate-800 text-slate-300" <?php echo e(request('category') == 'konsultasi' ? 'selected' : ''); ?>>Konsultasi</option>
                        <option value="pemeriksaan" class="bg-slate-800 text-slate-300" <?php echo e(request('category') == 'pemeriksaan' ? 'selected' : ''); ?>>Pemeriksaan</option>
                        <?php if(auth()->user()->isAdmin() || in_array(optional(auth()->user()->role)->name, ['dokter_umum', 'dokter_spesialis'])): ?>
                        <option value="janji_temu" class="bg-slate-800 text-slate-300" <?php echo e(request('category') == 'janji_temu' ? 'selected' : ''); ?>>Janji Temu</option>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <option value="karakter_kill" class="bg-slate-800 text-slate-300" <?php echo e(request('category') == 'karakter_kill' ? 'selected' : ''); ?>>Karakter Kill</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-sky-500 to-cyan-500 text-white py-3 px-4 rounded-xl font-medium text-sm hover:from-sky-600 hover:to-cyan-600 transition-all duration-200 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                    <a href="<?php echo e(route('staff.forms')); ?>" class="flex-1 bg-white/30 text-white py-3 px-4 rounded-xl font-medium text-sm hover:bg-white/40 transition-all duration-200 text-center border-2 border-sky-400/40 shadow-xl">
                        <i class="fas fa-times mr-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="backdrop-blur-xl border-2 border-sky-400/60 rounded-xl shadow-2xl p-6" style="background-color: rgba(7, 89, 133, 0.9);">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-amber-500/20 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-clock text-amber-400 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-300 text-sm">Pending</p>
                        <p class="text-2xl font-bold text-white"><?php echo e($stats['pending']); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="backdrop-blur-xl border-2 border-sky-400/60 rounded-xl shadow-2xl p-6" style="background-color: rgba(7, 89, 133, 0.9);">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-sky-500/20 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-check-circle text-sky-400 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-300 text-sm">Approved</p>
                        <p class="text-2xl font-bold text-white"><?php echo e($stats['approved']); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="backdrop-blur-xl border-2 border-sky-400/60 rounded-xl shadow-2xl p-6" style="background-color: rgba(7, 89, 133, 0.9);">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-red-500/20 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-times-circle text-red-400 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-300 text-sm">Rejected</p>
                        <p class="text-2xl font-bold text-white"><?php echo e($stats['rejected']); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="backdrop-blur-xl border-2 border-sky-400/60 rounded-xl shadow-2xl p-6" style="background-color: rgba(7, 89, 133, 0.9);">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-cyan-500/20 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-chart-line text-cyan-400 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-300 text-sm">Hari Ini</p>
                        <p class="text-2xl font-bold text-white"><?php echo e($stats['today']); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Forms List -->
        <div class="backdrop-blur-xl border-2 border-sky-400/60 rounded-2xl shadow-2xl overflow-hidden" style="background-color: rgba(7, 89, 133, 0.9);">
            <div class="px-6 py-4 border-b-2 border-sky-400/50" style="background-color: rgba(14, 165, 233, 0.3);">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-list mr-2 text-sky-400"></i>Daftar Formulir
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="border-b-2 border-sky-400/50" style="background-color: rgba(14, 165, 233, 0.4);">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-sky-200 uppercase tracking-wider">Formulir</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-sky-200 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-sky-200 uppercase tracking-wider">Diproses Oleh</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-sky-200 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-sky-200 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sky-400/30">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $forms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $form): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-sky-700/40 transition-colors border-b border-sky-400/30">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center mr-4">
                                            <i class="fas fa-file-medical text-white text-lg"></i>
                                        </div>
                                        <div>
                                            <p class="text-white font-semibold text-lg"><?php echo e($form->character_name); ?></p>
                                            <p class="text-gray-300 text-sm">
                                                <?php echo e(Str::of($form->form_type)->replace('_',' ')->title()); ?>

                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($form->form_type === 'tes_psikologi'): ?>
                                                    <?php
                                                        $data = is_array($form->form_data ?? null) ? $form->form_data : [];
                                                        $answers = [];
                                                        $hasAny = false;
                                                        for ($i = 1; $i <= 10; $i++) {
                                                            $key = 'stress' . $i;
                                                            $raw = $data[$key] ?? null;
                                                            if ($raw !== null && $raw !== '') { $hasAny = true; }
                                                            $val = is_numeric($raw) ? (int)$raw : 0;
                                                            if (in_array($i, [4,5,7,9], true)) { $val = 4 - $val; }
                                                            $answers[] = $val;
                                                        }
                                                        $total = array_sum($answers);
                                                        $level = $total <= 13 ? 'Rendah' : ($total <= 26 ? 'Sedang' : 'Tinggi');
                                                        $cls = [
                                                            'Rendah' => 'bg-green-500/20 text-green-300 border border-green-500/30',
                                                            'Sedang' => 'bg-yellow-500/20 text-yellow-300 border border-yellow-500/30',
                                                            'Tinggi' => 'bg-red-500/20 text-red-300 border border-red-500/30',
                                                        ][$level] ?? 'bg-white/10 text-white border border-white/20';
                                                    ?>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasAny): ?>
                                                        <span class="ml-2 px-2 py-0.5 rounded-full text-[11px] font-bold align-middle <?php echo e($cls); ?>">Stres: <?php echo e($level); ?> (<?php echo e($total); ?>)</span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </p>
                                            <p class="text-gray-400 text-xs max-w-md truncate" title="<?php echo e($form->description); ?>"><?php echo e($form->description); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <?php
                                        $isAppointment = in_array($form->form_type, ['penyakit_dalam', 'spesialis_anak', 'spesialis_bedah', 'spesialis_mata', 'spesialis_saraf', 'spesialis_urologi', 'spesialis_tht', 'spesialis_ortopedi']);
                                    ?>
                                    
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($form->status === 'pending'): ?>
                                        <div class="inline-flex items-center px-3 py-1 bg-yellow-500/20 text-yellow-300 rounded-full text-sm font-medium border border-yellow-500/30">
                                            <i class="fas fa-clock mr-2"></i>Pending
                                        </div>
                                    <?php elseif($form->status === 'approved'): ?>
                                        <div class="inline-flex items-center px-3 py-1 bg-green-500/20 text-green-300 rounded-full text-sm font-medium border border-green-500/30">
                                            <i class="fas fa-check-circle mr-2"></i><?php echo e($isAppointment ? 'Sudah Ditemui' : 'Approved'); ?>

                                        </div>
                                    <?php else: ?>
                                        <div class="inline-flex items-center px-3 py-1 bg-red-500/20 text-red-300 rounded-full text-sm font-medium border border-red-500/30">
                                            <i class="fas fa-times-circle mr-2"></i><?php echo e($isAppointment ? 'Tolak Formulir' : 'Rejected'); ?>

                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($form->processedBy && $form->status !== 'pending'): ?>
                                        <div class="flex flex-col">
                                            <span class="font-semibold text-white"><?php echo e($form->processedBy->name); ?></span>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($form->processed_at): ?>
                                                <span class="text-xs text-sky-400"><?php echo e($form->processed_at->format('d/m/Y H:i')); ?></span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-slate-400">-</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="text-white font-medium"><?php echo e($form->created_at->format('d M Y')); ?></p>
                                        <p class="text-gray-400 text-sm"><?php echo e($form->created_at->setTimezone('Asia/Jakarta')->format('H:i')); ?> WIB</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <?php
                                        $canApprove = $form->status === 'pending' && $user->canApproveForm($form->form_type);
                                    ?>
                                    <div class="flex flex-col sm:flex-row gap-2">
                                        <a href="<?php echo e(route('staff.forms.show', $form->id)); ?>" class="inline-flex items-center justify-center px-3 py-2 bg-gradient-to-r from-sky-500 to-cyan-500 text-white text-xs sm:text-sm font-bold rounded-xl hover:from-sky-600 hover:to-cyan-600 transition-all duration-300 transform hover:scale-105 shadow-lg">
                                            <i class="fas fa-eye mr-2"></i>Lihat
                                        </a>
                                        
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($form->status === 'pending'): ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canApprove): ?>
                                                <form method="POST" action="<?php echo e(route('staff.forms.approve', $form->id)); ?>" onsubmit="return confirm('<?php echo e($isAppointment ? 'Yakin ingin menandai janji temu sudah ditemui?' : 'Yakin ingin menyetujui formulir ini?'); ?>');" class="inline">
                                                    <?php echo csrf_field(); ?>
                                                    <button class="w-full inline-flex items-center justify-center px-3 py-2 bg-gradient-to-r from-green-500 to-emerald-500 text-white text-xs sm:text-sm font-bold rounded-xl hover:from-green-600 hover:to-emerald-600 transition-all duration-300 transform hover:scale-105 shadow-lg">
                                                        <i class="fas fa-check mr-2"></i><?php echo e($isAppointment ? 'Sudah Ditemui' : 'Setujui'); ?>

                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <button disabled class="w-full inline-flex items-center justify-center px-3 py-2 bg-gray-500/50 text-gray-300 text-xs sm:text-sm font-bold rounded-xl cursor-not-allowed opacity-60" title="Level role Anda tidak mencukupi untuk menyetujui formulir ini">
                                                    <i class="fas fa-lock mr-2"></i><?php echo e($isAppointment ? 'Sudah Ditemui' : 'Setujui'); ?>

                                                </button>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            
                                            <form method="POST" action="<?php echo e(route('staff.forms.reject', $form->id)); ?>" onsubmit="return confirm('Yakin ingin menolak formulir ini?');" class="inline">
                                                <?php echo csrf_field(); ?>
                                                <button class="w-full inline-flex items-center justify-center px-3 py-2 bg-gradient-to-r from-red-500 to-pink-500 text-white text-xs sm:text-sm font-bold rounded-xl hover:from-red-600 hover:to-pink-600 transition-all duration-300 transform hover:scale-105 shadow-lg">
                                                    <i class="fas fa-times mr-2"></i>Tolak
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-sky-300 font-medium text-sm">Selesai</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-500/20 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                                        </div>
                                        <p class="text-gray-300 text-lg font-medium">Belum ada formulir</p>
                                        <p class="text-gray-400 text-sm">Formulir yang masuk akan muncul di sini</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($forms->hasPages()): ?>
                <div class="px-6 py-4 border-t-2 border-sky-400/50" style="background-color: rgba(14, 165, 233, 0.3);">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-300">
                            Menampilkan <?php echo e($forms->firstItem()); ?> - <?php echo e($forms->lastItem()); ?> dari <?php echo e($forms->total()); ?> formulir
                        </div>
                        <div class="flex items-center space-x-2">
                            <?php echo e($forms->appends(request()->query())->links()); ?>

                        </div>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    // Auto-sync Jenis Surat dan Kategori dropdown
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.querySelector('select[name="type"]');
        const categorySelect = document.querySelector('select[name="category"]');
        
        if (typeSelect && categorySelect) {
            typeSelect.addEventListener('change', function() {
                if (this.value === 'janji_temu') {
                    // Set kategori ke janji_temu jika opsi tersedia
                    const janjiTemuOption = categorySelect.querySelector('option[value="janji_temu"]');
                    if (janjiTemuOption) {
                        categorySelect.value = 'janji_temu';
                    }
                }
            });
        }
    });
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\website\EMS-IME\public_html\resources\views/staff/forms.blade.php ENDPATH**/ ?>