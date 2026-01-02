

<?php $__env->startSection('title', 'Laporan & Masukan - Portal Medis MPK-BA'); ?>

<?php $__env->startSection('content'); ?>
    <div class="relative min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900"></div>
        <div class="absolute inset-0 bg-black bg-opacity-30"></div>

        <div class="relative max-w-4xl w-full mx-auto">
            <div class="glass-effect rounded-2xl elegant-shadow-lg p-4 sm:p-6 md:p-8 lg:p-12">
                <div class="text-center mb-6 sm:mb-8 md:mb-10">
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-2">
                        Laporan & Masukan
                    </h1>
                    <p class="text-blue-100 text-sm sm:text-base font-medium">
                        Kirimkan detail laporan atau masukan Anda di bawah ini
                    </p>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
                    <div
                        class="mb-6 p-4 bg-green-500/20 border border-green-400/50 rounded-xl backdrop-blur-sm animate-fade-in-up">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-green-500/30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-300 text-lg"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-green-200 font-bold mb-1">Berhasil Terkirim!</h4>
                                <p class="text-green-100 text-sm leading-relaxed"><?php echo e(session('success')); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <form action="<?php echo e(route('feedback.submit')); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>

                    <div class="border-b border-white/10 pb-6 mb-8">
                        <h3 class="text-xl font-semibold text-white mb-6">Jenis Pesan</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            <div>
                                <label for="type" class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                    Pilih Jenis <span class="text-red-400">*</span>
                                </label>
                                <select id="type" name="type" class="form-select <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    required>
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="laporan" <?php if(old('type') == 'laporan'): ?> selected <?php endif; ?>
                                        class="bg-slate-900 text-white font-bold">Laporan</option>
                                    <option value="masukan" <?php if(old('type') == 'masukan'): ?> selected <?php endif; ?>
                                        class="bg-slate-900 text-white font-bold">Masukan</option>
                                </select>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-2 text-xs text-red-400 flex items-center gap-2">
                                        <i class="fas fa-exclamation-circle"></i> <?php echo e($message); ?>

                                    </p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div>
                                <label for="subject" class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                    Subjek <span class="text-red-400">*</span>
                                </label>
                                <input type="text" id="subject" name="subject" value="<?php echo e(old('subject')); ?>"
                                    class="form-input <?php $__errorArgs = ['subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    placeholder="Contoh: Masalah pada sistem" required>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-2 text-xs text-red-400 flex items-center gap-2">
                                        <i class="fas fa-exclamation-circle"></i> <?php echo e($message); ?>

                                    </p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="border-b border-white/10 pb-6 mb-8">
                        <h3 class="text-xl font-semibold text-white mb-6">Detail Pesan</h3>
                        <div class="space-y-4 sm:space-y-6">
                            <div>
                                <label for="message" class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                    Pesan <span class="text-red-400">*</span>
                                </label>
                                <textarea id="message" name="message" rows="5"
                                    class="form-input resize-none <?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    placeholder="Jelaskan laporan atau masukan Anda secara detail..."
                                    required><?php echo e(old('message')); ?></textarea>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-2 text-xs text-red-400 flex items-center gap-2">
                                        <i class="fas fa-exclamation-circle"></i> <?php echo e($message); ?>

                                    </p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div>
                                <label for="image" class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                    Lampiran Gambar <span
                                        class="text-blue-200/60 font-normal text-sm ml-1">(Opsional)</span>
                                </label>
                                <input type="file" id="image" name="image" accept="image/*"
                                    class="form-input file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 file:cursor-pointer"
                                    onchange="previewImage(this)">
                                <p class="mt-2 text-xs text-blue-200/60">PNG, JPG, atau GIF (Max. 5MB)</p>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-2 text-xs text-red-400 flex items-center gap-2">
                                        <i class="fas fa-exclamation-circle"></i> <?php echo e($message); ?>

                                    </p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <!-- Image Preview -->
                                <div id="imagePreview" class="hidden mt-4">
                                    <div class="relative inline-block">
                                        <img id="preview" src="" alt="Preview"
                                            class="rounded-xl border border-white/20 max-h-48">
                                        <button type="button" onclick="removeImage()"
                                            class="absolute -top-2 -right-2 w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition-all shadow-lg">
                                            <i class="fas fa-times text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pb-6 mb-8">
                        <h3 class="text-xl font-semibold text-white mb-6">Informasi Pengirim</h3>
                        <div>
                            <label for="name" class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                Nama Anda <span class="text-blue-200/60 font-normal text-sm ml-1">(Opsional)</span>
                            </label>
                            <input type="text" id="name" name="name" value="<?php echo e(old('name')); ?>" class="form-input"
                                placeholder="Masukkan nama Anda">
                            <p class="mt-2 text-xs text-blue-200/60 flex items-center gap-2">
                                <i class="fas fa-info-circle text-blue-400"></i>
                                Jika dikosongkan, laporan akan dikirim secara anonim
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 pt-4">
                        <a href="<?php echo e(route('chat.page')); ?>"
                            class="flex-1 px-6 py-3 bg-white/10 border border-white/20 text-white font-bold text-sm rounded-xl hover:bg-white/20 transition-all duration-300 text-center flex items-center justify-center gap-2">
                            <i class="fas fa-arrow-left"></i>
                            Kembali
                        </a>
                        <button type="submit"
                            class="flex-1 px-6 py-3 bg-gradient-to-r from-sky-600 to-blue-600 text-white font-bold text-sm rounded-xl hover:shadow-lg hover:shadow-sky-500/30 transform hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-2">
                            <i class="fas fa-paper-plane"></i>
                            Kirim Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        function previewImage(input) {
            const preview = document.getElementById('preview');
            const previewContainer = document.getElementById('imagePreview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function  (e) {                 preview.src = e.target.result;                 previewContainer.classList.remove('hidden');             }
                 reader.readAsDataURL(input.files[0]);         }     }
         function removeImage() {         const input = document.getElementById('image');         const preview = document.getElementById('preview');         const previewContainer = document.getElementById('imagePreview');
             input.value = '';         preview.src = '';         previewContainer.classList.add('hidden');     }
    </script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fade-in-up 0.5s ease-out;
        }
    </style>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\website\EMS-IME\public_html\resources\views/feedback/index.blade.php ENDPATH**/ ?>