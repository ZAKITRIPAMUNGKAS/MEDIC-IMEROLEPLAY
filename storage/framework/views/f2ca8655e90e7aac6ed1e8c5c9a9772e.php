

<?php $__env->startSection('title', 'Tambah Struktur Organisasi'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container mx-auto px-4 py-8">
        <div class="mb-6">
            <a href="<?php echo e(route('admin.organizational-structure.index')); ?>" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Tambah Struktur Organisasi Baru</h1>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc list-inside">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <form action="<?php echo e(route('admin.organizational-structure.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama/Label (Opsional)
                    </label>
                    <input type="text" name="name" id="name" value="<?php echo e(old('name')); ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Contoh: Struktur EMS 2026">
                </div>

                <div class="mb-4">
                    <label for="hospital_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipe Hospital <span class="text-red-500">*</span>
                    </label>
                    <select name="hospital_type" id="hospital_type" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="ems" <?php echo e(old('hospital_type') === 'ems' ? 'selected' : ''); ?>>EMS (Emergency Medical
                            Services)</option>
                        <option value="roxwood" <?php echo e(old('hospital_type') === 'roxwood' ? 'selected' : ''); ?>>Roxwood Hospital
                        </option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="structure_data" class="block text-sm font-medium text-gray-700 mb-2">
                        Data Struktur (JSON) <span class="text-red-500">*</span>
                    </label>
                    <textarea name="structure_data" id="structure_data" rows="15" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono text-sm"><?php echo e(old('structure_data', '{}')); ?></textarea>
                    <p class="text-sm text-gray-500 mt-1">
                        Format JSON untuk hierarki struktur organisasi.
                        <button type="button" onclick="showExample()" class="text-blue-600 hover:underline">
                            Lihat contoh format
                        </button>
                    </p>
                </div>

                <div class="mb-4">
                    <label for="required_names" class="block text-sm font-medium text-gray-700 mb-2">
                        Daftar Nama yang Wajib Ditampilkan (Opsional)
                    </label>
                    <textarea name="required_names" id="required_names" rows="10"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Masukkan satu nama per baris&#10;Oliver Januari&#10;Joseph Preistley&#10;Jehan L. Keenan"><?php echo e(old('required_names')); ?></textarea>
                    <p class="text-sm text-gray-500 mt-1">
                        Nama-nama yang harus muncul di chart meskipun role mereka adalah admin. Satu nama per baris.
                    </p>
                </div>

                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" <?php echo e(old(' is_active') ? 'checked' : ''); ?>

                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Aktifkan struktur ini (struktur lain dengan tipe yang sama
                            akan dinonaktifkan)</span>
                    </label>
                </div>

                <div class="flex gap-3">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold">
                        <i class="fas fa-save mr-2"></i>Simpan
                    </button>
                    <a href="<?php echo e(route('admin.organizational-structure.index')); ?>"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg font-semibold">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showExample() {
            const example = {
                "level_0": {
                    "title": "CEO",
                    "positions": {
                        "Chief Executive Officer": "Oliver Januari"
                    }
                },
                "level_1": {
                    "title": "High Command",
                    "positions": {
                        "Deputy CEO": "Joseph Preistley",
                        "Chief Medical Officer": "Jehan L. Keenan"
                    }
                }
            };

            document.getElementById('structure_data').value = JSON.stringify(example, null, 2);
            alert('Contoh format telah dimasukkan ke textarea. Anda bisa memodifikasinya sesuai kebutuhan.');
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\website\EMS-IME\public_html\resources\views/admin/organizational-structure/create.blade.php ENDPATH**/ ?>