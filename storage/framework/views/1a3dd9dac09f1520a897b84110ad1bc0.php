

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8">
            <h1 class="text-3xl font-black text-slate-900">Role Permissions Management</h1>
            <p class="text-slate-600 mt-2">Manage permissions for each role. Toggle switches to grant or revoke permissions.</p>
        </div>

        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
                
                <div class="bg-gradient-to-r from-sky-500 to-cyan-500 p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold"><?php echo e($role->display_name); ?></h3>
                            <p class="text-sky-100 text-sm mt-1">Level <?php echo e($role->level); ?></p>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-shield-alt text-2xl"></i>
                        </div>
                    </div>
                </div>

                
                <div class="p-6 space-y-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $allPermissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permissionKey => $permissionLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-center justify-between p-3 rounded-lg hover:bg-slate-50 transition-colors
                                <?php echo e(in_array($permissionKey, $role->permissions ?? []) ? 'bg-green-50 border border-green-200' : 'bg-slate-50 border border-slate-200'); ?>">
                        <div class="flex-1">
                            <p class="font-semibold text-sm text-slate-800"><?php echo e($permissionLabel); ?></p>
                            <p class="text-xs text-slate-500 mt-0.5"><?php echo e($permissionKey); ?></p>
                        </div>
                        
                        
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   class="sr-only peer"
                                   data-role-id="<?php echo e($role->id); ?>"
                                   data-permission="<?php echo e($permissionKey); ?>"
                                   onchange="togglePermission(this)"
                                   <?php echo e(in_array($permissionKey, $role->permissions ?? []) ? 'checked' : ''); ?>>
                            <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-cyan-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-green-500 peer-checked:to-emerald-500"></div>
                        </label>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <div class="px-6 pb-6">
                    <div class="bg-slate-100 rounded-lg p-3">
                        <p class="text-xs text-slate-600">
                            <i class="fas fa-info-circle mr-1"></i>
                            <?php echo e($role->description); ?>

                        </p>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <div class="mt-8 bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-yellow-600 mt-1 mr-3"></i>
                <div>
                    <h4 class="font-bold text-yellow-800">Important Notes</h4>
                    <ul class="text-sm text-yellow-700 mt-2 space-y-1 list-disc list-inside">
                        <li>Permission changes take effect immediately</li>
                        <li><strong>reply_livechat</strong> controls who can reply to user chat messages</li>
                        <li><strong>manage_attendance_advanced</strong> allows force checkout and manual attendance</li>
                        <li>Admin role should always have all permissions</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePermission(checkbox) {
    const roleId = checkbox.getAttribute('data-role-id');
    const permission = checkbox.getAttribute('data-permission');
    const wasChecked = checkbox.checked;
    
    // Disable checkbox during request
    checkbox.disabled = true;
    
    fetch(`/admin/roles/${roleId}/toggle-permission`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ permission: permission })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI based on action
            const container = checkbox.closest('.flex');
            if (data.has_permission) {
                container.classList.remove('bg-slate-50', 'border-slate-200');
                container.classList.add('bg-green-50', 'border', 'border-green-200');
            } else {
                container.classList.remove('bg-green-50', 'border-green-200');
                container.classList.add('bg-slate-50', 'border', 'border-slate-200');
            }
            
            // Show success notification
            showNotification(data.message, 'success');
        } else {
            // Revert checkbox on error
            checkbox.checked = !wasChecked;
            showNotification('Failed to update permission', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        checkbox.checked = !wasChecked;
        showNotification('An error occurred', 'error');
    })
    .finally(() => {
        checkbox.disabled = false;
    });
}

function showNotification(message, type) {
    // You can use your existing notification system
    // For now, simple alert
    const icon = type === 'success' ? '✓' : '✗';
    const color = type === 'success' ? 'green' : 'red';
    
    // Create toast notification
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white bg-${color}-500 flex items-center gap-2 animate-fade-in-down`;
    toast.innerHTML = `<span class="text-lg">${icon}</span><span>${message}</span>`;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>

<style>
@keyframes fade-in-down {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-down {
    animation: fade-in-down 0.3s ease-out;
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\website\EMS-IME\public_html\resources\views/admin/roles/permissions.blade.php ENDPATH**/ ?>