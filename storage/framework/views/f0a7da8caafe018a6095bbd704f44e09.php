

<?php $__env->startSection('content'); ?>
    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-bold text-white mb-2">
                        <i class="fas fa-sitemap mr-3"></i>Struktural EMS Management
                    </h1>
                    <p class="text-gray-300">Manage organizational hierarchy and positions</p>
                </div>
                <a href="<?php echo e(route('admin.structural.create')); ?>"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white rounded-lg font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                    <i class="fas fa-plus mr-2"></i>
                    Add New Position
                </a>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
                <div class="mb-6 p-4 bg-green-500/20 border border-green-500 text-green-100 rounded-lg">
                    <i class="fas fa-check-circle mr-2"></i><?php echo e(session('success')); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
                <div class="mb-6 p-4 bg-red-500/20 border border-red-500 text-red-100 rounded-lg">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo e($errors->first()); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
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
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $positions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $position): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-white/5 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-500/20 text-purple-200 border border-purple-500/30">
                                            Level <?php echo e($position->level); ?>

                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($position->parent_id): ?>
                                                <span class="text-gray-500 mr-2">
                                                    <i class="fas fa-level-up-alt fa-rotate-90 mr-1"></i>
                                                </span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <div>
                                                <div class="text-sm font-semibold text-white"><?php echo e($position->title); ?></div>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($position->position_name): ?>
                                                    <div class="text-xs text-gray-400"><?php echo e($position->position_name); ?></div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($position->user): ?>
                                            <div class="flex items-center">
                                                <img src="<?php echo e($position->user->profile_image ? asset('storage/' . $position->user->profile_image) : asset('profile.jpg')); ?>"
                                                    alt="<?php echo e($position->user->name); ?>"
                                                    class="w-8 h-8 rounded-full mr-2 border-2 border-white/20">
                                                <div>
                                                    <div class="text-sm text-white"><?php echo e($position->user->name); ?></div>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($position->user->role): ?>
                                                        <div class="text-xs text-gray-400">
                                                            <?php echo e($position->user->role->display_name ?? $position->user->role->name); ?>

                                                        </div>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-sm text-gray-400 italic">Not assigned</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($position->parent): ?>
                                            <span class="text-sm text-gray-300"><?php echo e($position->parent->title); ?></span>
                                        <?php else: ?>
                                            <span class="text-sm text-gray-500 italic">Root level</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($position->is_active): ?>
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-500/20 text-green-200 border border-green-500/30">
                                                <i class="fas fa-check-circle mr-1"></i>Active
                                            </span>
                                        <?php else: ?>
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-500/20 text-gray-300 border border-gray-500/30">
                                                <i class="fas fa-times-circle mr-1"></i>Inactive
                                            </span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="<?php echo e(route('admin.structural.edit', $position)); ?>"
                                                class="inline-flex items-center px-3 py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-lg transition-all duration-300 shadow-lg text-xs">
                                                <i class="fas fa-edit mr-1"></i>Edit
                                            </a>
                                            <form action="<?php echo e(route('admin.structural.destroy', $position)); ?>" method="POST"
                                                class="inline">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit"
                                                    onclick="return confirm('Are you sure you want to delete this position? This cannot be undone.')"
                                                    class="inline-flex items-center px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-all duration-300 shadow-lg text-xs">
                                                    <i class="fas fa-trash mr-1"></i>Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
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
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            
            <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 border border-white/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-300">Total Positions</p>
                            <p class="text-3xl font-bold text-white mt-1"><?php echo e($positions->count()); ?></p>
                        </div>
                        <i class="fas fa-sitemap text-4xl text-sky-500/50"></i>
                    </div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 border border-white/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-300">Assigned</p>
                            <p class="text-3xl font-bold text-white mt-1"><?php echo e($positions->whereNotNull('user_id')->count()); ?>

                            </p>
                        </div>
                        <i class="fas fa-user-check text-4xl text-green-500/50"></i>
                    </div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 border border-white/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-300">Vacant</p>
                            <p class="text-3xl font-bold text-white mt-1"><?php echo e($positions->whereNull('user_id')->count()); ?>

                            </p>
                        </div>
                        <i class="fas fa-user-slash text-4xl text-amber-500/50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\website\EMS-IME\public_html\resources\views/admin/structural/index.blade.php ENDPATH**/ ?>