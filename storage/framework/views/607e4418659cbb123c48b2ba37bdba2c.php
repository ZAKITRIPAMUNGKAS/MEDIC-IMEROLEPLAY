

<?php $__env->startSection('title', 'Formulir Berhasil Dikirim - Portal Medis MOTIONLIFE'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700 relative overflow-hidden flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <!-- Background Effects -->
    <div class="absolute inset-0 bg-black/20"></div>
    <div class="absolute inset-0" style="background-image: radial-gradient(circle at 20% 20%, rgba(14, 165, 233, 0.1) 0%, transparent 50%), radial-gradient(circle at 80% 80%, rgba(6, 182, 212, 0.1) 0%, transparent 50%);"></div>
    
    <!-- Animated Elements -->
    <div class="absolute top-20 left-20 w-16 h-16 border-2 border-sky-400 rotate-45 animate-float opacity-30"></div>
    <div class="absolute top-40 right-20 w-12 h-12 border-2 border-cyan-400 rotate-12 animate-pulse opacity-40" style="animation-delay: 1s;"></div>
    <div class="absolute bottom-40 left-1/4 w-8 h-8 border-2 border-blue-400 rotate-45 animate-float opacity-50" style="animation-delay: 2s;"></div>
    <div class="absolute bottom-20 right-1/3 w-10 h-10 border-2 border-sky-500 rotate-12 animate-pulse opacity-30" style="animation-delay: 0.5s;"></div>

    <div class="relative z-10 max-w-4xl w-full">
        <div class="card bg-white/98 backdrop-blur-lg shadow-2xl border border-white/30 rounded-3xl p-12 text-center animate-fade-in-up overflow-hidden">
            <!-- Success Icon with Enhanced Design -->
            <div class="relative mb-8">
                <div class="w-28 h-28 bg-gradient-to-br from-green-500 via-emerald-500 to-teal-500 rounded-full flex items-center justify-center mx-auto shadow-2xl animate-pulse-slow relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-green-400 to-emerald-400 rounded-full animate-ping opacity-20"></div>
                    <i class="fas fa-check text-white text-5xl relative z-10"></i>
                </div>
                <!-- Success Ring -->
                <div class="absolute inset-0 w-28 h-28 mx-auto border-4 border-green-300 rounded-full animate-ping opacity-30"></div>
            </div>

            <!-- Enhanced Success Message -->
            <div class="mb-8">
                <h1 class="text-5xl md:text-6xl font-black bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 bg-clip-text text-transparent mb-4">
                    Formulir Berhasil Dikirim!
                </h1>
                <div class="w-32 h-1 bg-gradient-to-r from-green-500 to-teal-500 mx-auto mb-6 rounded-full"></div>
                <p class="text-2xl text-slate-700 mb-4 font-medium">
                    Terima kasih, <span class="font-bold text-sky-600 bg-sky-50 px-3 py-1 rounded-full"><?php echo e($form->character_name); ?></span>
                </p>
                <p class="text-lg text-slate-600 font-medium">
                    Tim medis kami akan segera memproses permintaan Anda dengan sepenuh hati 💚
                </p>
            </div>

            <!-- Psychology Test Results -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($form->form_type, ['tes_psikologi', 'surat_psikolog']) && is_array($form->form_data) && isset($form->form_data['suggestions']) && !empty($form->form_data['suggestions'])): ?>
            <div class="bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 rounded-3xl p-8 mb-8 border border-indigo-200 shadow-lg text-left animate-fade-in-up" style="animation-delay: 0.3s;">
                <div class="flex items-center justify-center mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center mr-3 shadow-lg">
                        <i class="fas fa-brain text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-indigo-900">Hasil Analisis Psikologis</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <!-- Scores -->
                    <div class="space-y-6">
                        <h4 class="font-bold text-lg text-indigo-800 border-b border-indigo-200 pb-2">Skor Evaluasi</h4>
                        
                        <!-- PSS -->
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-medium text-indigo-700">Tingkat Stres (PSS-10)</span>
                                <span class="text-sm font-bold text-indigo-900"><?php echo e($form->form_data['pss_score'] ?? 0); ?>/40</span>
                            </div>
                            <div class="w-full bg-indigo-200 rounded-full h-2.5">
                                <?php 
                                    $pss_percent = (($form->form_data['pss_score'] ?? 0) / 40) * 100;
                                    $pss_color = $pss_percent > 65 ? 'bg-red-500' : ($pss_percent > 35 ? 'bg-yellow-500' : 'bg-green-500');
                                ?>
                                <div class="<?php echo e($pss_color); ?> h-2.5 rounded-full transition-all duration-1000" style="width: <?php echo e($pss_percent); ?>%"></div>
                            </div>
                            <p class="text-xs text-indigo-500 mt-1">Semakin rendah semakin baik</p>
                        </div>

                        <!-- RSES -->
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-medium text-indigo-700">Harga Diri (RSES)</span>
                                <span class="text-sm font-bold text-indigo-900"><?php echo e($form->form_data['rses_score'] ?? 0); ?>/40</span>
                            </div>
                            <div class="w-full bg-indigo-200 rounded-full h-2.5">
                                <?php 
                                    $rses_percent = (($form->form_data['rses_score'] ?? 0) / 40) * 100;
                                    $rses_color = $rses_percent < 40 ? 'bg-red-500' : ($rses_percent < 60 ? 'bg-yellow-500' : 'bg-green-500');
                                ?>
                                <div class="<?php echo e($rses_color); ?> h-2.5 rounded-full transition-all duration-1000" style="width: <?php echo e($rses_percent); ?>%"></div>
                            </div>
                             <p class="text-xs text-indigo-500 mt-1">Semakin tinggi semakin positif</p>
                        </div>
                        
                        <!-- Personality Traits Summary -->
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($form->form_data['bfi_scores'])): ?>
                        <div>
                             <h4 class="font-bold text-sm text-indigo-800 mb-2">Dominansi Kepribadian</h4>
                             <div class="flex flex-wrap gap-2">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $form->form_data['bfi_scores']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trait => $score): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($score >= 3.5): ?>
                                    <span class="px-2 py-1 bg-indigo-100 text-indigo-700 text-xs rounded-lg font-semibold">
                                        <?php echo e(ucfirst($trait)); ?>

                                    </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                             </div>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <!-- Suggestions -->
                    <div class="bg-white/60 p-6 rounded-2xl border border-indigo-100 shadow-sm">
                        <h4 class="font-bold text-lg text-indigo-800 mb-4 flex items-center">
                            <i class="fas fa-lightbulb text-yellow-500 mr-2"></i> Rekomendasi
                        </h4>
                        <ul class="space-y-3">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $form->form_data['suggestions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $suggestion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="flex items-start text-indigo-900 text-sm leading-relaxed">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3 flex-shrink-0"></i>
                                <span><?php echo e($suggestion); ?></span>
                            </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </ul>
                    </div>
                </div>

                <div class="bg-indigo-900/5 rounded-xl p-4 text-center border border-indigo-100/50">
                    <p class="text-indigo-800 text-sm italic font-medium">
                        "Setiap langkah kecil menuju kesehatan mental adalah kemenangan yang besar."
                    </p>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <!-- Testimoni Form Section - Dipindahkan ke atas -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$form->testimoni): ?>
            <div class="bg-gradient-to-br from-amber-50 via-yellow-50 to-orange-50 rounded-3xl p-8 mb-8 border-2 border-amber-200 shadow-lg">
                <div class="flex items-center justify-center mb-6">
                    <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-500 rounded-2xl flex items-center justify-center mr-3 shadow-lg">
                        <i class="fas fa-comment-dots text-white text-lg"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900">Saran dan Masukan</h3>
                </div>
                <p class="text-center text-slate-600 mb-6 text-lg">
                    Kami sangat menghargai pendapat Anda! Bagikan pengalaman Anda dengan layanan kami.
                </p>
                
                <form method="POST" action="<?php echo e(route('public.form.testimoni', $form->id)); ?>" id="testimoniForm" class="space-y-6">
                    <?php echo csrf_field(); ?>
                    <div>
                        <label for="rating" class="block text-sm font-bold text-slate-700 mb-3 text-lg">
                            Rating <span class="text-red-500">*</span>
                        </label>
                        <div class="flex justify-center gap-2 mb-2" id="ratingStars">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 5; $i >= 1; $i--): ?>
                                <input type="radio" name="rating" id="rating<?php echo e($i); ?>" value="<?php echo e($i); ?>" class="hidden" required>
                                <label for="rating<?php echo e($i); ?>" class="cursor-pointer text-4xl text-gray-300 hover:text-yellow-400 transition-colors duration-200 rating-star" data-rating="<?php echo e($i); ?>">
                                    <i class="fas fa-star"></i>
                                </label>
                            <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <p class="text-center text-sm text-slate-500 mt-2" id="ratingText">Pilih rating Anda</p>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['rating'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-sm mt-1 text-center"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div>
                        <label for="testimoni" class="block text-sm font-bold text-slate-700 mb-3 text-lg">
                            Saran dan Masukan <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            id="testimoni" 
                            name="testimoni" 
                            rows="5" 
                            class="w-full px-4 py-3 rounded-xl border-2 border-amber-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-300 text-slate-700 placeholder-slate-400 transition-all duration-200 resize-none"
                            placeholder="Bagikan pengalaman Anda dengan layanan kami. Saran dan masukan Anda sangat berarti untuk perbaikan kami ke depannya..."
                            required
                            maxlength="500"
                        ><?php echo e(old('testimoni')); ?></textarea>
                        <div class="flex justify-between items-center mt-2">
                            <p class="text-xs text-slate-500">Minimal 10 karakter</p>
                            <p class="text-xs text-slate-500"><span id="charCount">0</span>/500 karakter</p>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['testimoni'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="flex justify-center">
                        <button 
                            type="submit" 
                            class="px-8 py-4 bg-gradient-to-r from-amber-500 to-orange-500 text-white text-lg font-bold rounded-2xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 flex items-center gap-3"
                            id="submitTestimoniBtn"
                        >
                            <i class="fas fa-paper-plane"></i>
                            <span>Kirim Saran & Masukan</span>
                        </button>
                    </div>
                </form>
            </div>
            <?php else: ?>
            <div class="bg-gradient-to-br from-green-50 via-emerald-50 to-teal-50 rounded-3xl p-8 mb-8 border-2 border-green-200 shadow-lg">
                <div class="flex items-center justify-center mb-4">
                    <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center mr-3 shadow-lg">
                        <i class="fas fa-check-circle text-white text-lg"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900">Terima Kasih!</h3>
                </div>
                <p class="text-center text-slate-600 text-lg">
                    Saran dan masukan Anda telah kami terima. Kami sangat menghargai kontribusi Anda untuk perbaikan layanan kami.
                </p>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <!-- Enhanced Form Details -->
            <div class="bg-gradient-to-br from-sky-50 via-cyan-50 to-blue-50 rounded-3xl p-8 mb-8 text-left border border-sky-200 shadow-lg">
                <div class="flex items-center justify-center mb-6">
                    <div class="w-10 h-10 bg-gradient-to-br from-sky-500 to-cyan-500 rounded-2xl flex items-center justify-center mr-3 shadow-lg">
                        <i class="fas fa-file-alt text-white text-lg"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900">Detail Formulir</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="group p-5 bg-white/90 rounded-2xl border border-sky-100 hover:border-sky-200 transition-all duration-300 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-hashtag text-sky-500 mr-3"></i>
                                    <span class="text-slate-600 font-semibold">ID Formulir:</span>
                                </div>
                                <span class="font-mono text-lg font-bold text-sky-600 bg-sky-100 px-3 py-1 rounded-full">
                                    #<?php echo e(str_pad($form->id, 6, '0', STR_PAD_LEFT)); ?>

                                </span>
                            </div>
                        </div>
                        <div class="group p-5 bg-white/90 rounded-2xl border border-sky-100 hover:border-sky-200 transition-all duration-300 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-user text-sky-500 mr-3"></i>
                                    <span class="text-slate-600 font-semibold">Nama Karakter:</span>
                                </div>
                                <span class="font-bold text-slate-900"><?php echo e($form->character_name); ?></span>
                            </div>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($form->citizen_id): ?>
                        <div class="group p-5 bg-white/90 rounded-2xl border border-sky-100 hover:border-sky-200 transition-all duration-300 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-id-card text-sky-500 mr-3"></i>
                                    <span class="text-slate-600 font-semibold">ID Warga:</span>
                                </div>
                                <span class="font-bold text-slate-900"><?php echo e($form->citizen_id); ?></span>
                            </div>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="space-y-4">
                        <div class="group p-5 bg-white/90 rounded-2xl border border-sky-100 hover:border-sky-200 transition-all duration-300 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-stethoscope text-sky-500 mr-3"></i>
                                    <span class="text-slate-600 font-semibold">Jenis Layanan:</span>
                                </div>
                                <span class="font-bold text-slate-900"><?php echo e(ucfirst(str_replace('_', ' ', $form->form_type))); ?></span>
                            </div>
                        </div>
                        <div class="group p-5 bg-white/90 rounded-2xl border border-sky-100 hover:border-sky-200 transition-all duration-300 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-clock text-sky-500 mr-3"></i>
                                    <span class="text-slate-600 font-semibold">Status:</span>
                                </div>
                                <span class="px-4 py-2 bg-amber-100 text-amber-800 text-sm font-bold rounded-full border border-amber-200 animate-pulse">
                                    <i class="fas fa-hourglass-half mr-1"></i>Menunggu Review
                                </span>
                            </div>
                        </div>
                        <div class="group p-5 bg-white/90 rounded-2xl border border-sky-100 hover:border-sky-200 transition-all duration-300 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-alt text-sky-500 mr-3"></i>
                                    <span class="text-slate-600 font-semibold">Tanggal Kirim:</span>
                                </div>
                                <span class="font-bold text-slate-900"><?php echo e($form->created_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i')); ?> WIB</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Next Steps -->
            <div class="bg-gradient-to-br from-sky-100 via-cyan-100 to-blue-100 rounded-3xl p-8 mb-8 border border-sky-200 shadow-lg">
                <div class="flex items-center justify-center mb-6">
                    <div class="w-10 h-10 bg-gradient-to-br from-sky-500 to-cyan-500 rounded-2xl flex items-center justify-center mr-3 shadow-lg">
                        <i class="fas fa-route text-white text-lg"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900">Langkah Selanjutnya</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="group text-center p-6 bg-white/90 rounded-2xl border border-sky-200 hover:border-sky-300 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                        <div class="relative mb-4">
                            <div class="w-16 h-16 bg-gradient-to-br from-sky-500 to-cyan-500 rounded-2xl flex items-center justify-center mx-auto shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-search text-white text-xl"></i>
                            </div>
                            <div class="absolute -top-2 -right-2 w-8 h-8 bg-sky-400 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                1
                            </div>
                        </div>
                        <h4 class="font-bold text-slate-900 mb-2 text-lg">Review Formulir</h4>
                        <p class="text-slate-600 text-sm leading-relaxed">Tim medis akan meninjau formulir Anda dalam 24 jam</p>
                    </div>
                    <div class="group text-center p-6 bg-white/90 rounded-2xl border border-sky-200 hover:border-sky-300 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                        <div class="relative mb-4">
                            <div class="w-16 h-16 bg-gradient-to-br from-cyan-500 to-blue-500 rounded-2xl flex items-center justify-center mx-auto shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-bell text-white text-xl"></i>
                            </div>
                            <div class="absolute -top-2 -right-2 w-8 h-8 bg-cyan-400 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                2
                            </div>
                        </div>
                        <h4 class="font-bold text-slate-900 mb-2 text-lg">Notifikasi</h4>
                        <p class="text-slate-600 text-sm leading-relaxed">Anda akan menerima notifikasi melalui sistem</p>
                    </div>
                    <div class="group text-center p-6 bg-white/90 rounded-2xl border border-sky-200 hover:border-sky-300 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                        <div class="relative mb-4">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-2xl flex items-center justify-center mx-auto shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-cogs text-white text-xl"></i>
                            </div>
                            <div class="absolute -top-2 -right-2 w-8 h-8 bg-blue-400 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                3
                            </div>
                        </div>
                        <h4 class="font-bold text-slate-900 mb-2 text-lg">Proses Layanan</h4>
                        <p class="text-slate-600 text-sm leading-relaxed">Layanan akan diproses sesuai dengan permintaan Anda</p>
                    </div>
                </div>
            </div>

            <!-- Enhanced Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-6 justify-center mb-8">
                <a href="<?php echo e(route('public.index')); ?>" class="group relative px-8 py-4 bg-gradient-to-r from-sky-500 to-cyan-500 text-white text-lg font-bold rounded-2xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-sky-600 to-cyan-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative flex items-center justify-center">
                        <i class="fas fa-home mr-3 text-xl"></i>
                        <span>Kembali ke Beranda</span>
                    </div>
                </a>
                <a href="<?php echo e(route('public.form', $form->form_type)); ?>" class="group relative px-8 py-4 bg-white text-sky-600 text-lg font-bold rounded-2xl border-2 border-sky-500 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-sky-50 to-cyan-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative flex items-center justify-center">
                        <i class="fas fa-plus mr-3 text-xl"></i>
                        <span>Ajukan Formulir Lain</span>
                    </div>
                </a>
            </div>

            <!-- Enhanced Contact Info -->
            <div class="mt-8 pt-8 border-t border-sky-200">
                <div class="bg-gradient-to-r from-sky-50 via-cyan-50 to-blue-50 rounded-2xl p-8 border border-sky-200 shadow-lg">
                    <div class="flex flex-col sm:flex-row items-center justify-center mb-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-sky-500 to-cyan-500 rounded-2xl flex items-center justify-center mr-4 shadow-lg mb-4 sm:mb-0">
                            <i class="fas fa-headset text-white text-2xl"></i>
                        </div>
                        <div class="text-center sm:text-left">
                            <h4 class="text-2xl font-bold text-slate-900 mb-2">Butuh Bantuan?</h4>
                            <p class="text-slate-600 text-lg">
                                Tim medis kami siap membantu Anda 24/7
                            </p>
                        </div>
                    </div>
                    <div class="text-center">
                        <p class="text-slate-600 mb-4">
                            Hubungi tim medis melalui sistem internal atau 
                            <a href="https://discord.com/channels/1357345255728480356/1357367699369492501" class="text-sky-600 hover:text-sky-700 font-bold transition-colors underline decoration-2 underline-offset-2">
                                klik di sini
                            </a> untuk bantuan langsung.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                            <div class="flex items-center text-sky-600">
                                <i class="fas fa-phone mr-2"></i>
                                <span class="font-semibold">Hotline: 24/7</span>
                            </div>
                            <div class="flex items-center text-sky-600">
                                <i class="fas fa-envelope mr-2"></i>
                                <span class="font-semibold">Email Support</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Rating stars interaction
    const ratingStars = document.querySelectorAll('.rating-star');
    const ratingInputs = document.querySelectorAll('input[name="rating"]');
    const ratingText = document.getElementById('ratingText');
    const ratingTexts = {
        1: 'Sangat Tidak Puas',
        2: 'Tidak Puas',
        3: 'Cukup Puas',
        4: 'Puas',
        5: 'Sangat Puas'
    };

    ratingStars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = parseInt(this.getAttribute('data-rating'));
            document.getElementById('rating' + rating).checked = true;
            updateStars(rating);
            ratingText.textContent = ratingTexts[rating];
        });

        star.addEventListener('mouseenter', function() {
            const rating = parseInt(this.getAttribute('data-rating'));
            highlightStars(rating);
        });
    });

    document.getElementById('ratingStars').addEventListener('mouseleave', function() {
        const checked = document.querySelector('input[name="rating"]:checked');
        if (checked) {
            updateStars(parseInt(checked.value));
        } else {
            resetStars();
        }
    });

    function updateStars(rating) {
        ratingStars.forEach((star, index) => {
            const starRating = 5 - index;
            if (starRating <= rating) {
                star.classList.remove('text-gray-300');
                star.classList.add('text-yellow-400');
            } else {
                star.classList.remove('text-yellow-400');
                star.classList.add('text-gray-300');
            }
        });
    }

    function highlightStars(rating) {
        ratingStars.forEach((star, index) => {
            const starRating = 5 - index;
            if (starRating <= rating) {
                star.classList.remove('text-gray-300');
                star.classList.add('text-yellow-400');
            } else {
                star.classList.remove('text-yellow-400');
                star.classList.add('text-gray-300');
            }
        });
    }

    function resetStars() {
        ratingStars.forEach(star => {
            star.classList.remove('text-yellow-400');
            star.classList.add('text-gray-300');
        });
    }

    // Character counter
    const testimoniTextarea = document.getElementById('testimoni');
    const charCount = document.getElementById('charCount');
    
    if (testimoniTextarea && charCount) {
        testimoniTextarea.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = length;
            
            if (length < 10) {
                charCount.classList.add('text-red-500');
                charCount.classList.remove('text-slate-500');
            } else {
                charCount.classList.remove('text-red-500');
                charCount.classList.add('text-slate-500');
            }
        });
    }

    // Form validation
    const testimoniForm = document.getElementById('testimoniForm');
    if (testimoniForm) {
        testimoniForm.addEventListener('submit', function(e) {
            const rating = document.querySelector('input[name="rating"]:checked');
            const testimoni = testimoniTextarea.value.trim();

            if (!rating) {
                e.preventDefault();
                alert('Silakan pilih rating terlebih dahulu.');
                return false;
            }

            if (testimoni.length < 10) {
                e.preventDefault();
                alert('Saran dan masukan minimal 10 karakter.');
                return false;
            }

            // Disable button to prevent double submission
            const submitBtn = document.getElementById('submitTestimoniBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengirim...';
        });
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\website\EMS-IME\public_html\resources\views/public/form-success.blade.php ENDPATH**/ ?>