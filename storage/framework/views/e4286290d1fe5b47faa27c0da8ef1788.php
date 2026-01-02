<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 h-[calc(100vh-200px)]" wire:poll.15s="loadFeedback">
    <!-- Left Sidebar - Feedback List -->
    <div
        class="lg:col-span-1 bg-white/95 backdrop-blur-sm rounded-2xl shadow-xl border border-sky-200/50 overflow-hidden flex flex-col">
        <!-- Filter Section -->
        <div class="p-4 border-b border-slate-200 bg-gradient-to-r from-sky-50 to-cyan-50">
            <h3 class="font-bold text-slate-800 text-sm mb-3 flex items-center gap-2">
                <i class="fas fa-filter text-sky-600"></i>
                Filter
            </h3>

            <div class="space-y-2">
                <!-- Status Filter -->
                <select wire:model.live="filterStatus"
                    style="color: #0f172a !important;"
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none text-sm bg-white">
                    <option value="all">Semua Status</option>
                    <option value="new">Baru</option>
                    <option value="reviewed">Ditinjau</option>
                    <option value="resolved">Selesai</option>
                </select>

                <!-- Type Filter -->
                <select wire:model.live="filterType"
                    style="color: #0f172a !important;"
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none text-sm bg-white">
                    <option value="all">Semua Tipe</option>
                    <option value="kritik">Laporan</option>
                    <option value="saran">Masukan</option>
                </select>
            </div>
        </div>

        <!-- Feedback List -->
        <div class="flex-1 overflow-y-auto p-2">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $feedbackList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feedback): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div wire:key="feedback-<?php echo e($feedback->id); ?>" 
                         wire:click="selectFeedback(<?php echo e($feedback->id); ?>)" 
                         wire:loading.class="opacity-50 bg-slate-100"
                         wire:target="selectFeedback(<?php echo e($feedback->id); ?>)"
                         class="p-3 mb-2 rounded-lg cursor-pointer transition-all border relative overflow-hidden
                                <?php echo e($selectedFeedback && $selectedFeedback->id === $feedback->id
                ? 'bg-sky-50 border-sky-300 shadow-md ring-1 ring-sky-200'
                : 'bg-white border-slate-200 hover:bg-slate-50 hover:border-slate-300'); ?>">

                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-slate-800 text-sm truncate"><?php echo e($feedback->subject); ?></h4>
                                <p class="text-xs text-slate-600 flex items-center gap-1 mt-0.5">
                                    <i class="fas fa-user text-[10px]"></i>
                                    <?php echo e($feedback->display_name); ?>

                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 flex-wrap">
                            <!-- Type Badge -->
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold
                                    <?php echo e($feedback->type === 'kritik'
                ? 'bg-red-100 text-red-700'
                : 'bg-green-100 text-green-700'); ?>">
                                <i
                                    class="fas <?php echo e($feedback->type === 'kritik' ? 'fa-exclamation-triangle' : 'fa-lightbulb'); ?>"></i>
                                <?php echo e(ucfirst($feedback->type)); ?>

                            </span>

                            <!-- Status Badge -->
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold
                                    <?php echo e($feedback->status === 'new' ? 'bg-blue-100 text-blue-700' : ''); ?>

                                    <?php echo e($feedback->status === 'reviewed' ? 'bg-yellow-100 text-yellow-700' : ''); ?>

                                    <?php echo e($feedback->status === 'resolved' ? 'bg-green-100 text-green-700' : ''); ?>">
                                <?php echo e($feedback->status === 'new' ? 'Baru' : ''); ?>

                                <?php echo e($feedback->status === 'reviewed' ? 'Ditinjau' : ''); ?>

                                <?php echo e($feedback->status === 'resolved' ? 'Selesai' : ''); ?>

                            </span>

                            <!-- Date -->
                            <span class="text-[10px] text-slate-500 ml-auto">
                                <?php echo e($feedback->created_at->diffForHumans()); ?>

                            </span>
                        </div>
                    </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="flex flex-col items-center justify-center h-full text-center p-6">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-3">
                        <i class="fas fa-inbox text-2xl text-slate-400"></i>
                    </div>
                    <p class="text-slate-500 text-sm">Tidak ada feedback</p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <!-- Right Panel - Feedback Detail -->
    <div
        class="lg:col-span-2 bg-white/95 backdrop-blur-sm rounded-2xl shadow-xl border border-sky-200/50 overflow-hidden flex flex-col">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedFeedback): ?>
            <!-- Header -->
            <div class="p-4 border-b border-slate-200 bg-gradient-to-r from-sky-50 to-cyan-50">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="font-bold text-slate-800 text-lg mb-1"><?php echo e($selectedFeedback->subject); ?></h3>
                        <div class="flex items-center gap-3 flex-wrap">
                            <span class="text-xs text-slate-600 flex items-center gap-1">
                                <i class="fas fa-user"></i>
                                <?php echo e($selectedFeedback->display_name); ?>

                            </span>
                            <span class="text-xs text-slate-500">
                                <?php echo e($selectedFeedback->created_at->format('d M Y, H:i')); ?>

                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <!-- Type Badge -->
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                                <?php echo e($selectedFeedback->type === 'kritik'
            ? 'bg-red-100 text-red-700 border border-red-200'
            : 'bg-green-100 text-green-700 border border-green-200'); ?>">
                            <i
                                class="fas <?php echo e($selectedFeedback->type === 'kritik' ? 'fa-exclamation-triangle' : 'fa-lightbulb'); ?>"></i>
                            <?php echo e(ucfirst($selectedFeedback->type)); ?>

                        </span>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="flex-1 overflow-y-auto p-4 space-y-4">
                <!-- Message -->
                <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                    <h4 class="font-semibold text-slate-700 text-sm mb-2 flex items-center gap-2">
                        <i class="fas fa-message text-sky-600"></i>
                        Pesan
                    </h4>
                    <div class="text-slate-800 text-base leading-7 font-medium tracking-wide prose prose-sm max-w-none">
                        <?php echo $selectedFeedback->message; ?>

                    </div>
                </div>

                <!-- Attachment Image -->
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedFeedback->image): ?>
                    <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                        <h4 class="font-semibold text-slate-700 text-sm mb-2 flex items-center gap-2">
                            <i class="fas fa-image text-sky-600"></i>
                            Lampiran
                        </h4>
                        <div class="relative group">
                            <img src="<?php echo e(asset('storage/' . $selectedFeedback->image)); ?>" 
                                 alt="Lampiran Feedback" 
                                 class="rounded-lg max-h-64 object-cover border border-slate-200 cursor-pointer hover:opacity-90 transition-opacity"
                                 onclick="window.open(this.src, '_blank')">
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                                <span class="bg-black/50 text-white text-xs px-2 py-1 rounded backdrop-blur-sm">
                                    <i class="fas fa-external-link-alt mr-1"></i> Klik untuk memperbesar
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <!-- Status Info -->
                <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                    <h4 class="font-semibold text-slate-700 text-sm mb-3 flex items-center gap-2">
                        <i class="fas fa-info-circle text-sky-600"></i>
                        Status
                    </h4>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <span class="text-xs text-slate-500 block mb-1">Status Saat Ini</span>
                            <span
                                class="inline-flex px-3 py-1 rounded-full text-xs font-semibold
                                    <?php echo e($selectedFeedback->status === 'new' ? 'bg-blue-100 text-blue-700 border border-blue-200' : ''); ?>

                                    <?php echo e($selectedFeedback->status === 'reviewed' ? 'bg-yellow-100 text-yellow-700 border border-yellow-200' : ''); ?>

                                    <?php echo e($selectedFeedback->status === 'resolved' ? 'bg-green-100 text-green-700 border border-green-200' : ''); ?>">
                                <?php echo e($selectedFeedback->status === 'new' ? 'Baru' : ''); ?>

                                <?php echo e($selectedFeedback->status === 'reviewed' ? 'Ditinjau' : ''); ?>

                                <?php echo e($selectedFeedback->status === 'resolved' ? 'Selesai' : ''); ?>

                            </span>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedFeedback->reviewed_at): ?>
                            <div>
                                <span class="text-xs text-slate-500 block mb-1">Ditinjau Oleh</span>
                                <span
                                    class="text-sm text-slate-700 font-medium"><?php echo e($selectedFeedback->reviewer?->name ?? 'N/A'); ?></span>
                                <span
                                    class="text-xs text-slate-500 block"><?php echo e($selectedFeedback->reviewed_at->format('d M Y, H:i')); ?></span>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <!-- Admin Notes -->
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedFeedback->notes || $selectedFeedback->status !== 'new'): ?>
                    <div class="bg-amber-50 rounded-lg p-4 border border-amber-200">
                        <h4 class="font-semibold text-amber-900 text-sm mb-2 flex items-center gap-2">
                            <i class="fas fa-sticky-note text-amber-600"></i>
                            Catatan Admin
                        </h4>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedFeedback->status === 'resolved'): ?>
                            <p class="text-amber-800 text-sm leading-relaxed whitespace-pre-wrap">
                                <?php echo e($selectedFeedback->notes ?? 'Tidak ada catatan'); ?></p>
                        <?php else: ?>
                            <textarea wire:model="adminNotes" rows="3"
                                class="w-full px-3 py-2 rounded-lg border border-amber-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none text-sm bg-white"
                                placeholder="Tambahkan catatan untuk feedback ini..."></textarea>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <!-- Actions -->
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                <div class="flex items-center gap-2 flex-wrap">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedFeedback->status === 'new'): ?>
                        <button wire:click="markAsReviewed"
                            class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold rounded-lg transition-all flex items-center gap-2">
                            <i class="fas fa-eye"></i>
                            Tandai Ditinjau
                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedFeedback->status !== 'resolved'): ?>
                        <button wire:click="markAsResolved"
                            class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-semibold rounded-lg transition-all flex items-center gap-2">
                            <i class="fas fa-check-circle"></i>
                            Tandai Selesai
                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedFeedback->status !== 'new'): ?>
                        <button wire:click="markAsNew"
                            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded-lg transition-all flex items-center gap-2">
                            <i class="fas fa-undo"></i>
                            Kembalikan ke Baru
                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <button wire:click="deleteFeedback(<?php echo e($selectedFeedback->id); ?>)"
                        onclick="return confirm('Yakin ingin menghapus feedback ini?')"
                        class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-lg transition-all flex items-center gap-2 ml-auto">
                        <i class="fas fa-trash"></i>
                        Hapus
                    </button>
                </div>
            </div>
        <?php else: ?>
            <!-- Empty State -->
            <div class="flex-1 flex flex-col items-center justify-center text-center p-8">
                <div
                    class="w-24 h-24 bg-gradient-to-br from-sky-100 to-cyan-100 rounded-2xl flex items-center justify-center mb-4">
                    <i class="fas fa-comment-dots text-4xl text-sky-600"></i>
                </div>
                <h3 class="font-bold text-slate-700 text-lg mb-2">Pilih Feedback</h3>
                <p class="text-slate-500 text-sm max-w-sm">
                    Pilih salah satu feedback dari daftar di sebelah kiri untuk melihat detail dan melakukan tindakan.
                </p>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div><?php /**PATH D:\website\EMS-IME\public_html\resources\views\livewire\feedback-list.blade.php ENDPATH**/ ?>