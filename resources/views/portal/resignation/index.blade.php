@extends('layouts.app')

@section('title', 'Pengajuan Resign — Portal Alta Hospital')

@section('content')
<div class="min-h-screen pt-20 pb-12" style="background: linear-gradient(135deg, #0b1329 0%, #0c2461 50%, #0b1329 100%);">
    <div class="max-w-4xl mx-auto px-4">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-orange-500/20 border border-orange-500/40 flex items-center justify-center text-orange-400">
                        <i class="fas fa-file-signature text-lg"></i>
                    </span>
                    Pengajuan Resign Staf
                </h1>
                <p class="text-white/60 text-sm mt-1">Portal pengunduran diri, kalkulasi denda IE, dan verifikasi bukti administrasi</p>
            </div>
            @if(!$request || in_array($request?->status, ['completed', 'rejected', 'cancelled']))
            <a href="{{ route('portal.resignation.create') }}"
               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-400 hover:to-red-500 text-white text-sm font-semibold rounded-xl shadow-lg shadow-red-900/30 transition-all duration-200">
                <i class="fas fa-plus"></i> Ajukan Resign Baru
            </a>
            @endif
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
        <div class="mb-5 p-4 bg-emerald-500/20 border border-emerald-500/40 rounded-2xl text-emerald-300 text-sm flex items-center gap-3 shadow-lg shadow-emerald-950/30">
            <i class="fas fa-check-circle text-lg shrink-0"></i> 
            <span>{{ session('success') }}</span>
        </div>
        @endif
        @if(session('info'))
        <div class="mb-5 p-4 bg-sky-500/20 border border-sky-500/40 rounded-2xl text-sky-300 text-sm flex items-center gap-3 shadow-lg shadow-sky-950/30">
            <i class="fas fa-info-circle text-lg shrink-0"></i> 
            <span>{{ session('info') }}</span>
        </div>
        @endif
        @if(session('error'))
        <div class="mb-5 p-4 bg-rose-500/20 border border-rose-500/40 rounded-2xl text-rose-300 text-sm flex items-center gap-3 shadow-lg shadow-rose-950/30">
            <i class="fas fa-exclamation-triangle text-lg shrink-0"></i> 
            <span>{{ session('error') }}</span>
        </div>
        @endif
        @if($errors->any())
        <div class="mb-5 p-4 bg-rose-500/20 border border-rose-500/40 rounded-2xl text-rose-300 text-sm shadow-lg shadow-rose-950/30">
            <div class="font-bold flex items-center gap-2 mb-2">
                <i class="fas fa-exclamation-circle"></i> Terdapat kendala pada berkas yang diunggah:
            </div>
            <ul class="list-disc list-inside space-y-1 text-xs">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if($request)

        {{-- Banner Sanksi PTDH (Jika tipe PTDH) --}}
        @if($request->isPtdh())
        <div class="mb-6 p-5 bg-gradient-to-r from-rose-950/70 via-red-900/50 to-amber-950/70 border-2 border-rose-500/60 rounded-2xl shadow-2xl relative overflow-hidden">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-rose-500/20 border border-rose-500/50 flex items-center justify-center text-rose-300 shrink-0 text-2xl shadow-inner">
                    <i class="fas fa-gavel"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2.5 flex-wrap mb-1">
                        <h2 class="text-base sm:text-lg font-extrabold text-white tracking-wide">Pemberhentian Tidak Dengan Hormat (PTDH)</h2>
                        <span class="px-2.5 py-0.5 rounded-full bg-rose-500/30 text-rose-200 border border-rose-500/50 text-[11px] font-black uppercase tracking-wider">
                            Sanksi IE
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-rose-100/80 leading-relaxed">
                        Divisi Industrial &amp; Employee Relations (IE) telah menerbitkan surat keputusan dan kalkulasi denda <strong>PTDH</strong> untuk Anda. Silakan selesaikan pembayaran denda dan unggah <strong>4 berkas bukti administrasi</strong> di bawah ini agar proses administrasi dapat diselesaikan.
                    </p>

                    <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-xs">
                        <div class="bg-black/40 p-2.5 rounded-xl border border-white/10">
                            <span class="text-white/40 block text-[10px] uppercase font-semibold">Akumulasi Gapok</span>
                            <span class="text-white font-bold text-xs sm:text-sm">$ {{ number_format($request->base_salary, 0, ',', '.') }}</span>
                        </div>
                        <div class="bg-black/40 p-2.5 rounded-xl border border-white/10">
                            <span class="text-white/40 block text-[10px] uppercase font-semibold">Denda Dasar ({{ $request->fine_percentage }}%)</span>
                            <span class="text-white font-bold text-xs sm:text-sm">$ {{ number_format($request->base_fine_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="bg-black/40 p-2.5 rounded-xl border border-rose-500/30">
                            <span class="text-rose-300/80 block text-[10px] uppercase font-semibold">Biaya Tambahan PTDH</span>
                            <span class="text-rose-300 font-extrabold text-xs sm:text-sm">$ {{ number_format($request->ptdh_additional_fee, 0, ',', '.') }}</span>
                        </div>
                        <div class="bg-gradient-to-r from-amber-500/20 to-rose-500/20 p-2.5 rounded-xl border-2 border-amber-500/50">
                            <span class="text-amber-300 block text-[10px] uppercase font-black">Total Wajib Bayar</span>
                            <span class="text-amber-300 font-black text-sm sm:text-base">$ {{ number_format($request->fine_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Progress Flow Alur Resign / PTDH --}}
        <div class="mb-6 bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-5 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-white/70 text-xs uppercase tracking-wider font-bold flex items-center gap-2">
                    <i class="fas fa-stream text-orange-400"></i> Alur Tahapan {{ $request->isPtdh() ? 'Proses PTDH' : 'Proses Resign' }}
                </h3>
                <span class="text-xs px-2.5 py-1 rounded-full font-semibold border
                    @if($request->status === 'completed') bg-emerald-500/20 text-emerald-300 border-emerald-500/40
                    @elseif($request->status === 'proof_submitted') bg-sky-500/20 text-sky-300 border-sky-500/40
                    @elseif(in_array($request->status, ['pending_proof', 'proof_revision'])) bg-purple-500/20 text-purple-300 border-purple-500/40
                    @elseif($request->status === 'pending_ie') bg-orange-500/20 text-orange-300 border-orange-500/40
                    @else bg-yellow-500/20 text-yellow-300 border-yellow-500/40 @endif">
                    {{ $request->status_label }}
                </span>
            </div>

            @php
                $stepSubmit   = true;
                $stepPnd      = in_array($request->status, ['approved_pnd', 'pending_ie', 'pending_proof', 'proof_submitted', 'proof_revision', 'completed']);
                $stepIeDenda  = in_array($request->status, ['pending_proof', 'proof_submitted', 'proof_revision', 'completed']);
                $stepProof    = in_array($request->status, ['proof_submitted', 'completed']);
                $stepFinal    = $request->status === 'completed';

                $pipeline = [
                    ['label'=>'1. Submit', 'icon'=>'fa-paper-plane', 'done'=>$stepSubmit],
                    ['label'=>'2. Verifikasi PND', 'icon'=>'fa-user-check', 'done'=>$stepPnd],
                    ['label'=>'3. Denda IE', 'icon'=>'fa-calculator', 'done'=>$stepIeDenda],
                    ['label'=>'4. Upload Bukti', 'icon'=>'fa-cloud-upload-alt', 'done'=>$stepProof, 'active'=>$request->canUploadProof()],
                    ['label'=>'5. Not Active', 'icon'=>'fa-flag-checkered', 'done'=>$stepFinal],
                ];
            @endphp

            <div style="position: relative; width: 100%; padding: 8px 0 4px 0;">
                @php
                    $completedSteps = 1;
                    if ($stepPnd) $completedSteps = 2;
                    if ($stepIeDenda) $completedSteps = 3;
                    if ($stepProof) $completedSteps = 4;
                    if ($stepFinal) $completedSteps = 5;
                    $fillPercent = (($completedSteps - 1) / 4) * 100;
                @endphp

                {{-- Background Track & Active Progress Bar --}}
                <div style="position: absolute; top: 26px; left: 8%; right: 8%; height: 4px; background: rgba(255, 255, 255, 0.12); border-radius: 9999px; z-index: 0; pointer-events: none;">
                    <div style="width: {{ $fillPercent }}%; height: 100%; background: linear-gradient(90deg, #10b981 0%, #06b6d4 100%); border-radius: 9999px; box-shadow: 0 0 10px rgba(16, 185, 129, 0.6); transition: width 0.4s ease;"></div>
                </div>

                {{-- Step Items Horizontal Row --}}
                <div style="display: flex; flex-direction: row; justify-content: space-between; align-items: flex-start; position: relative; z-index: 1; width: 100%;">
                    @foreach($pipeline as $idx => $step)
                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center; text-align: center; padding: 0 2px;">
                        <div style="width: 36px; height: 36px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 13px; transition: all 0.3s ease; margin-bottom: 6px; backdrop-filter: blur(8px);
                            @if($step['done'])
                                background: rgba(16, 185, 129, 0.25); border: 1.5px solid #34d399; color: #6ee7b7; box-shadow: 0 0 12px rgba(52, 211, 153, 0.4);
                            @elseif(!empty($step['active']))
                                background: rgba(168, 85, 247, 0.3); border: 2px solid #c084fc; color: #e9d5ff; box-shadow: 0 0 14px rgba(192, 132, 252, 0.55);
                            @else
                                background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15); color: rgba(255, 255, 255, 0.35);
                            @endif">
                            <i class="fas {{ $step['icon'] }}"></i>
                        </div>
                        <div style="font-size: 11px; line-height: 1.25; font-weight: 600;
                            @if($step['done']) color: #6ee7b7;
                            @elseif(!empty($step['active'])) color: #e9d5ff; text-shadow: 0 0 8px rgba(192, 132, 252, 0.5);
                            @else color: rgba(255, 255, 255, 0.45); @endif">
                            {{ $step['label'] }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- SECTION KHUSUS: FORMULIR UPLOAD BUKTI RESIGN (Saat status pending_proof atau proof_revision) --}}
        @if($request->canUploadProof())
        <div class="mb-6 bg-gradient-to-br from-purple-900/30 via-slate-900/80 to-purple-950/40 backdrop-blur-xl border-2 border-purple-500/40 rounded-2xl p-6 shadow-2xl relative overflow-hidden">
            <div class="absolute -top-16 -right-16 w-48 h-48 bg-purple-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="flex items-start gap-4 mb-5">
                <div class="w-12 h-12 rounded-2xl bg-purple-500/20 border border-purple-500/40 flex items-center justify-center text-purple-300 shrink-0 text-xl shadow-inner">
                    <i class="fas fa-cloud-upload-alt"></i>
                </div>
                <div>
                    <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                        Tahap Wajib: Formulir Upload Bukti Resign
                    </h2>
                    <p class="text-purple-200/80 text-sm mt-1 leading-relaxed">
                        IE telah menyetujui kalkulasi denda. Sesuai SOP administrasi, silakan isi formulir dan lampirkan <strong>4 berkas bukti wajib</strong> di bawah ini sebelum penonaktifan akun diverifikasi.
                    </p>
                </div>
            </div>

            {{-- Banner Catatan Revisi dari IE (Jika diminta isi ulang) --}}
            @if($request->status === 'proof_revision' && $request->proof_revision_notes)
            <div class="mb-6 p-4 bg-rose-500/20 border-2 border-rose-500/60 rounded-xl text-rose-200 text-sm flex items-start gap-3 shadow-lg">
                <i class="fas fa-exclamation-circle text-rose-400 text-lg mt-0.5 shrink-0"></i>
                <div>
                    <p class="font-bold text-rose-300 uppercase tracking-wide text-xs mb-1">Catatan Revisi dari Divisi IE (Wajib Diperbaiki):</p>
                    <p class="text-white text-sm font-medium leading-relaxed">{{ $request->proof_revision_notes }}</p>
                    <p class="text-rose-300/70 text-xs mt-2 italic">Silakan periksa kembali dan unggah ulang 4 berkas yang sesuai dengan arahan IE di atas.</p>
                </div>
            </div>
            @endif

            <form action="{{ route('portal.resignation.upload-proof') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    {{-- 1. Foto Kantong --}}
                    <div class="bg-white/5 border border-white/10 rounded-xl p-4 hover:border-purple-400/50 transition-all flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-sm font-bold text-white flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-lg bg-purple-500/30 text-purple-300 text-xs flex items-center justify-center font-extrabold">1</span>
                                    Foto Kantong <span class="text-rose-400">*</span>
                                </label>
                                <span class="text-[10px] uppercase font-semibold text-purple-300/80 bg-purple-500/20 px-2 py-0.5 rounded">Full Layar</span>
                            </div>
                            <p class="text-xs text-white/50 mb-3">Screenshot full layar yang menampilkan seluruh isi inventori kantong karakter.</p>
                        </div>
                        
                        <div class="space-y-2">
                            <input type="file" name="pocket_proof" id="pocket_proof" accept="image/*" required
                                   onchange="previewProofImage(this, 'preview_pocket')"
                                   class="block w-full text-xs text-white/70 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-500 file:cursor-pointer bg-white/5 rounded-lg border border-white/10 focus:outline-none">
                            <div id="preview_pocket" class="hidden mt-2 relative rounded-lg overflow-hidden border border-purple-500/40 max-h-40 bg-black/40">
                                <img src="" alt="Preview Kantong" class="w-full h-36 object-cover">
                            </div>
                        </div>
                    </div>

                    {{-- 2. Foto Kunci --}}
                    <div class="bg-white/5 border border-white/10 rounded-xl p-4 hover:border-purple-400/50 transition-all flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-sm font-bold text-white flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-lg bg-purple-500/30 text-purple-300 text-xs flex items-center justify-center font-extrabold">2</span>
                                    Foto Kunci <span class="text-rose-400">*</span>
                                </label>
                                <span class="text-[10px] uppercase font-semibold text-purple-300/80 bg-purple-500/20 px-2 py-0.5 rounded">Kunci Tercabut</span>
                            </div>
                            <p class="text-xs text-white/50 mb-3">Screenshot untuk memastikan seluruh kunci kendaraan atau fasilitas medis telah tercabut.</p>
                        </div>
                        
                        <div class="space-y-2">
                            <input type="file" name="key_proof" id="key_proof" accept="image/*" required
                                   onchange="previewProofImage(this, 'preview_key')"
                                   class="block w-full text-xs text-white/70 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-500 file:cursor-pointer bg-white/5 rounded-lg border border-white/10 focus:outline-none">
                            <div id="preview_key" class="hidden mt-2 relative rounded-lg overflow-hidden border border-purple-500/40 max-h-40 bg-black/40">
                                <img src="" alt="Preview Kunci" class="w-full h-36 object-cover">
                            </div>
                        </div>
                    </div>

                    {{-- 3. Foto Surat Resign / SK PTDH --}}
                    <div class="bg-white/5 border border-white/10 rounded-xl p-4 hover:border-purple-400/50 transition-all flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-sm font-bold text-white flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-lg bg-purple-500/30 text-purple-300 text-xs flex items-center justify-center font-extrabold">3</span>
                                    Foto Surat {{ $request->isPtdh() ? 'Keputusan PTDH' : 'Resign' }} <span class="text-rose-400">*</span>
                                </label>
                                <span class="text-[10px] uppercase font-semibold text-purple-300/80 bg-purple-500/20 px-2 py-0.5 rounded">{{ $request->isPtdh() ? 'SK PTDH' : 'Surat Resmi' }}</span>
                            </div>
                            <p class="text-xs text-white/50 mb-3">
                                @if($request->isPtdh())
                                Screenshot surat keputusan PTDH atau bukti pemberitahuan resmi dari IE.
                                @else
                                Screenshot surat permohonan pengunduran diri yang telah Anda susun.
                                @endif
                            </p>
                        </div>
                        
                        <div class="space-y-2">
                            <input type="file" name="letter_proof" id="letter_proof" accept="image/*" required
                                   onchange="previewProofImage(this, 'preview_letter')"
                                   class="block w-full text-xs text-white/70 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-500 file:cursor-pointer bg-white/5 rounded-lg border border-white/10 focus:outline-none">
                            <div id="preview_letter" class="hidden mt-2 relative rounded-lg overflow-hidden border border-purple-500/40 max-h-40 bg-black/40">
                                <img src="" alt="Preview Surat" class="w-full h-36 object-cover">
                            </div>
                        </div>
                    </div>

                    {{-- 4. Foto Billing Denda --}}
                    <div class="bg-white/5 border border-white/10 rounded-xl p-4 hover:border-purple-400/50 transition-all flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-sm font-bold text-white flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-lg bg-purple-500/30 text-purple-300 text-xs flex items-center justify-center font-extrabold">4</span>
                                    Foto Billing Denda {{ $request->isPtdh() ? 'PTDH' : 'Resign' }} <span class="text-rose-400">*</span>
                                </label>
                                <span class="text-[10px] uppercase font-semibold text-purple-300/80 bg-purple-500/20 px-2 py-0.5 rounded">Bukti Pembayaran</span>
                            </div>
                            <p class="text-xs text-white/50 mb-3">Screenshot bukti transfer / billing pelunasan denda {{ $request->isPtdh() ? 'PTDH' : 'resign' }} (${{ number_format($request->fine_amount, 0, ',', '.') }}).</p>
                        </div>
                        
                        <div class="space-y-2">
                            <input type="file" name="fine_proof" id="fine_proof" accept="image/*" required
                                   onchange="previewProofImage(this, 'preview_fine')"
                                   class="block w-full text-xs text-white/70 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-500 file:cursor-pointer bg-white/5 rounded-lg border border-white/10 focus:outline-none">
                            <div id="preview_fine" class="hidden mt-2 relative rounded-lg overflow-hidden border border-purple-500/40 max-h-40 bg-black/40">
                                <img src="" alt="Preview Billing Denda" class="w-full h-36 object-cover">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="pt-3 border-t border-purple-500/30 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-xs text-purple-200/60 flex items-center gap-2">
                        <i class="fas fa-shield-alt text-purple-400"></i>
                        Format: JPG, PNG, WEBP (Maksimal 10MB per berkas foto).
                    </div>
                    <button type="submit"
                            onclick="return confirm('Pastikan ke-4 foto bukti sudah sesuai dan jelas. Kirim sekarang ke Divisi IE?');"
                            class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white text-sm font-extrabold rounded-xl shadow-lg shadow-purple-900/40 transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-paper-plane"></i> Kirim Seluruh Bukti Resign ke IE
                    </button>
                </div>
            </form>
        </div>
        @endif

        {{-- SECTION TAMPILAN BUKTI TERKIRIM (Jika status proof_submitted atau completed) --}}
        @if($request->hasAllProofs())
        <div class="mb-6 bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-5 shadow-xl">
            <div class="flex items-center justify-between mb-4 border-b border-white/10 pb-3">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fas fa-images text-cyan-400"></i> Berkas Bukti Resign Terunggah
                </h3>
                <span class="text-xs text-white/50">
                    Dikirim: {{ $request->proof_submitted_at?->format('d M Y, H:i') ?? 'Tersimpan' }}
                </span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                {{-- Kantong --}}
                <div class="group relative rounded-xl overflow-hidden border border-white/15 bg-black/40 text-center">
                    <img src="{{ $request->pocket_proof_url }}" alt="Foto Kantong" class="w-full h-28 object-cover group-hover:scale-105 transition-transform duration-300">
                    <div class="p-2 bg-slate-900/90 border-t border-white/10">
                        <p class="text-[11px] font-bold text-white truncate">Foto Kantong</p>
                        <a href="{{ $request->pocket_proof_url }}" target="_blank" class="text-[10px] text-cyan-300 hover:underline">
                            <i class="fas fa-external-link-alt"></i> Lihat Penuh
                        </a>
                    </div>
                </div>
                {{-- Kunci --}}
                <div class="group relative rounded-xl overflow-hidden border border-white/15 bg-black/40 text-center">
                    <img src="{{ $request->key_proof_url }}" alt="Foto Kunci" class="w-full h-28 object-cover group-hover:scale-105 transition-transform duration-300">
                    <div class="p-2 bg-slate-900/90 border-t border-white/10">
                        <p class="text-[11px] font-bold text-white truncate">Foto Kunci</p>
                        <a href="{{ $request->key_proof_url }}" target="_blank" class="text-[10px] text-cyan-300 hover:underline">
                            <i class="fas fa-external-link-alt"></i> Lihat Penuh
                        </a>
                    </div>
                </div>
                {{-- Surat --}}
                <div class="group relative rounded-xl overflow-hidden border border-white/15 bg-black/40 text-center">
                    <img src="{{ $request->letter_proof_url }}" alt="Foto Surat Resign" class="w-full h-28 object-cover group-hover:scale-105 transition-transform duration-300">
                    <div class="p-2 bg-slate-900/90 border-t border-white/10">
                        <p class="text-[11px] font-bold text-white truncate">Surat Resign</p>
                        <a href="{{ $request->letter_proof_url }}" target="_blank" class="text-[10px] text-cyan-300 hover:underline">
                            <i class="fas fa-external-link-alt"></i> Lihat Penuh
                        </a>
                    </div>
                </div>
                {{-- Billing --}}
                <div class="group relative rounded-xl overflow-hidden border border-white/15 bg-black/40 text-center">
                    <img src="{{ $request->fine_proof_url }}" alt="Foto Billing Denda" class="w-full h-28 object-cover group-hover:scale-105 transition-transform duration-300">
                    <div class="p-2 bg-slate-900/90 border-t border-white/10">
                        <p class="text-[11px] font-bold text-white truncate">Billing Denda</p>
                        <a href="{{ $request->fine_proof_url }}" target="_blank" class="text-[10px] text-cyan-300 hover:underline">
                            <i class="fas fa-external-link-alt"></i> Lihat Penuh
                        </a>
                    </div>
                </div>
            </div>

            @if($request->status === 'proof_submitted')
            <div class="mt-4 p-3.5 bg-sky-500/10 border border-sky-500/30 rounded-xl text-sky-200 text-xs flex items-center justify-between">
                <span class="flex items-center gap-2">
                    <i class="fas fa-spinner fa-spin text-sky-400"></i>
                    Bukti berhasil dikirim. Divisi IE sedang melakukan cross-check berkas sebelum penonaktifan akhir.
                </span>
            </div>
            @endif
        </div>
        @endif

        {{-- Detail Card Pengajuan --}}
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden shadow-2xl">
            <div class="px-6 py-4 border-b border-white/10 flex flex-wrap items-center justify-between gap-2 bg-white/3">
                <div class="flex items-center gap-3">
                    <span class="text-white font-bold text-base">{{ $request->status_label }}</span>
                    @if($request->status === 'completed')
                    <span class="px-3 py-1 bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 rounded-full text-xs font-bold">
                        <i class="fas fa-user-slash mr-1"></i> Not Active
                    </span>
                    @endif
                </div>

                @if($request->status === 'pending_pnd')
                <form method="POST" action="{{ route('portal.resignation.cancel-own') }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan resign ini?');" class="inline">
                    @csrf
                    <button type="submit" class="text-xs text-rose-300 hover:text-rose-200 bg-rose-500/20 hover:bg-rose-500/30 px-3.5 py-1.5 rounded-lg border border-rose-500/30 transition-all flex items-center gap-1.5 font-semibold">
                        <i class="fas fa-times-circle"></i> Batalkan Pengajuan Saya
                    </button>
                </form>
                @endif
            </div>

            <div class="p-6 space-y-5 text-sm">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <span class="text-white/40 text-xs uppercase block mb-0.5">Nama Anggota</span>
                        <span class="text-white font-semibold">{{ $request->applicant_name }}</span>
                    </div>
                    <div>
                        <span class="text-white/40 text-xs uppercase block mb-0.5">Jabatan Terakhir</span>
                        <span class="text-white/90 font-medium">{{ $request->position }}</span>
                    </div>
                    <div>
                        <span class="text-white/40 text-xs uppercase block mb-0.5">Tanggal Surat</span>
                        <span class="text-white/90">{{ $request->letter_date?->format('d M Y') }}</span>
                    </div>
                    <div>
                        <span class="text-white/40 text-xs uppercase block mb-0.5">Angkatan / Batch</span>
                        <span class="text-white/90">{{ $request->batch ?? '—' }}</span>
                    </div>
                </div>

                {{-- Alasan IC & OOC --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-white/5 rounded-xl border border-white/10">
                        <p class="text-xs text-white/40 uppercase tracking-wider mb-1 font-semibold flex items-center gap-1.5">
                            <i class="fas fa-comment-alt text-amber-400"></i> Alasan IC (In-Character)
                        </p>
                        <p class="text-white/80 leading-relaxed text-xs sm:text-sm">{{ $request->reason_ic }}</p>
                    </div>
                    <div class="p-4 bg-white/5 rounded-xl border border-white/10">
                        <p class="text-xs text-white/40 uppercase tracking-wider mb-1 font-semibold flex items-center gap-1.5">
                            <i class="fas fa-user-shield text-sky-400"></i> Alasan OOC (Out-Of-Character)
                        </p>
                        <p class="text-white/80 leading-relaxed text-xs sm:text-sm">{{ $request->reason_ooc }}</p>
                    </div>
                </div>

                {{-- Info Denda --}}
                @if($request->status !== 'pending_pnd')
                <div class="p-4 bg-orange-500/10 rounded-xl border border-orange-500/20">
                    <p class="text-xs text-orange-300 uppercase tracking-wider mb-2 font-bold flex items-center gap-1.5">
                        <i class="fas fa-coins text-orange-400"></i> {{ $request->isPtdh() ? 'Rincian Denda PTDH (IE)' : 'Rincian Denda Resign (IE)' }}
                    </p>
                    @if($request->isPtdh())
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-center">
                        <div class="p-2 rounded-lg bg-black/20">
                            <p class="text-white/40 text-[11px] mb-0.5">Akumulasi Gapok</p>
                            <p class="text-white font-bold text-xs sm:text-sm">$ {{ number_format($request->base_salary, 0, ',', '.') }}</p>
                        </div>
                        <div class="p-2 rounded-lg bg-black/20">
                            <p class="text-white/40 text-[11px] mb-0.5">Denda Dasar ({{ $request->fine_percentage }}%)</p>
                            <p class="text-white font-bold text-xs sm:text-sm">$ {{ number_format($request->base_fine_amount, 0, ',', '.') }}</p>
                        </div>
                        <div class="p-2 rounded-lg bg-black/20">
                            <p class="text-rose-300/80 text-[11px] mb-0.5">Biaya Tambahan PTDH</p>
                            <p class="text-rose-300 font-extrabold text-xs sm:text-sm">$ {{ number_format($request->ptdh_additional_fee, 0, ',', '.') }}</p>
                        </div>
                        <div class="p-2 rounded-lg bg-gradient-to-r from-amber-500/20 to-orange-500/20 border border-amber-500/30">
                            <p class="text-amber-300 text-[11px] mb-0.5 font-bold">Total Denda PTDH</p>
                            <p class="text-amber-300 font-black text-xs sm:text-sm">$ {{ number_format($request->fine_amount, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @else
                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div class="p-2 rounded-lg bg-black/20">
                            <p class="text-white/40 text-[11px] mb-0.5">Akumulasi Gaji Pokok</p>
                            <p class="text-white font-bold text-sm">$ {{ number_format($request->base_salary, 0, ',', '.') }}</p>
                        </div>
                        <div class="p-2 rounded-lg bg-black/20">
                            <p class="text-white/40 text-[11px] mb-0.5">Persentase Denda</p>
                            <p class="text-orange-300 font-bold text-sm">{{ $request->fine_percentage }}%</p>
                        </div>
                        <div class="p-2 rounded-lg bg-black/20">
                            <p class="text-white/40 text-[11px] mb-0.5">Total Denda Resign</p>
                            <p class="text-orange-400 font-extrabold text-sm">$ {{ number_format($request->fine_amount, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @endif
                    <div class="mt-3 text-center">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $request->fine_paid ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30' }}">
                            <i class="fas {{ $request->fine_paid ? 'fa-check-circle' : 'fa-hourglass-half' }}"></i>
                            {{ $request->fine_paid ? 'Kalkulasi Denda Telah Dikonfirmasi IE' : 'Menunggu Penetapan & Pelunasan Denda' }}
                        </span>
                    </div>
                </div>
                @endif

                {{-- Catatan Verifikasi --}}
                <div class="space-y-2">
                    @if($request->pnd_notes)
                    <div class="p-3 bg-blue-500/10 rounded-xl border border-blue-500/20 text-xs text-blue-200 flex items-start gap-2">
                        <span class="font-bold text-blue-300 shrink-0">Catatan PND:</span> 
                        <span>{{ $request->pnd_notes }}</span>
                    </div>
                    @endif
                    @if($request->ie_notes)
                    <div class="p-3 bg-orange-500/10 rounded-xl border border-orange-500/20 text-xs text-orange-200 flex items-start gap-2">
                        <span class="font-bold text-orange-300 shrink-0">Catatan IE:</span> 
                        <span>{{ $request->ie_notes }}</span>
                    </div>
                    @endif
                </div>

                {{-- Status Selesai Log Info --}}
                @if($request->status === 'completed')
                <div class="p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-xl text-emerald-200 text-xs flex items-center justify-between">
                    <div>
                        <p class="font-bold text-emerald-300 mb-0.5">Proses Pengunduran Diri Selesai</p>
                        <p class="text-white/70">Akun telah berstatus Not Active pada {{ $request->final_deactivated_at?->format('d M Y H:i') ?? '-' }}. Riwayat administrasi tersimpan permanen di Log Resign.</p>
                    </div>
                    <i class="fas fa-archive text-2xl text-emerald-400 shrink-0 ml-4"></i>
                </div>
                @endif
            </div>
        </div>

        @else
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-12 text-center shadow-xl">
            <div class="w-16 h-16 rounded-2xl bg-orange-500/10 border border-orange-500/30 flex items-center justify-center text-orange-400 mx-auto mb-4 text-2xl">
                <i class="fas fa-file-signature"></i>
            </div>
            <h3 class="text-lg font-bold text-white mb-1">Belum Ada Pengajuan Resign Aktif</h3>
            <p class="text-white/50 text-sm max-w-md mx-auto mb-6">Jika Anda bermaksud mengajukan pengunduran diri dari posisi di rumah sakit, silakan ajukan melalui tombol di bawah.</p>
            <a href="{{ route('portal.resignation.create') }}"
               class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-orange-500 to-red-600 text-white text-sm font-bold rounded-xl hover:from-orange-400 hover:to-red-500 transition-all shadow-lg shadow-red-900/40">
                <i class="fas fa-plus"></i> Ajukan Resign Sekarang
            </a>
        </div>
        @endif

    </div>
</div>

<script>
function previewProofImage(input, previewId) {
    const previewContainer = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = previewContainer.querySelector('img');
            img.src = e.target.result;
            previewContainer.classList.remove('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        previewContainer.classList.add('hidden');
    }
}
</script>
@endsection
