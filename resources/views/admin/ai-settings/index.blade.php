@extends('layouts.app')

@section('title', 'Pengaturan AI (Gemini) - Portal Medis')

@section('content')
<style>
    /* Styling khusus agar kebal dari benturan CSS global dan tidak ada text yang tertimpa */
    .ai-settings-page {
        position: relative;
        min-height: 100vh;
        padding: 2.5rem 1rem;
        background: radial-gradient(ellipse at top, #0c4a6e 0%, #032b43 50%, #071726 100%);
    }
    .ai-card-shell {
        max-width: 860px;
        margin: 0 auto;
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.45), 0 0 0 1px rgba(255, 255, 255, 0.1);
        overflow: hidden;
    }
    .ai-field-wrapper {
        position: relative;
        width: 100%;
    }
    .ai-field-icon-left {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        width: 20px;
        height: 20px;
        color: #64748b;
        pointer-events: none;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .ai-field-icon-right {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2;
    }
    .ai-field-input {
        width: 100% !important;
        height: 50px !important;
        padding-left: 48px !important;
        padding-right: 48px !important;
        padding-top: 0 !important;
        padding-bottom: 0 !important;
        font-size: 14px !important;
        line-height: 50px !important;
        border-radius: 12px !important;
        border: 1.5px solid #cbd5e1 !important;
        background-color: #ffffff !important;
        color: #0f172a !important;
        outline: none !important;
        box-sizing: border-box !important;
        transition: border-color 0.2s, box-shadow 0.2s !important;
    }
    .ai-field-input-masked {
        -webkit-text-security: disc !important;
        text-security: disc !important;
        font-family: monospace !important;
    }
    .ai-field-input-masked.ai-revealed {
        -webkit-text-security: none !important;
        text-security: none !important;
    }
    .ai-field-input:focus {
        border-color: #0284c7 !important;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15) !important;
    }
    .ai-field-select {
        width: 100% !important;
        height: 50px !important;
        padding-left: 48px !important;
        padding-right: 48px !important;
        font-size: 14px !important;
        line-height: normal !important;
        border-radius: 12px !important;
        border: 1.5px solid #cbd5e1 !important;
        background-color: #ffffff !important;
        color: #0f172a !important;
        outline: none !important;
        box-sizing: border-box !important;
        cursor: pointer !important;
        appearance: none !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        transition: border-color 0.2s, box-shadow 0.2s !important;
    }
    .ai-field-select:focus {
        border-color: #0284c7 !important;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15) !important;
    }
    .ai-field-select option {
        color: #0f172a !important;
        background: #ffffff !important;
        padding: 10px !important;
    }

    /* Custom Toggle Switch */
    .ai-toggle-btn {
        position: relative;
        display: inline-block;
        width: 54px;
        height: 30px;
        cursor: pointer;
        user-select: none;
        margin: 0;
    }
    .ai-toggle-btn input {
        opacity: 0;
        width: 0;
        height: 0;
        position: absolute;
    }
    .ai-toggle-slider {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #cbd5e1;
        border-radius: 999px;
        transition: background-color 0.25s ease;
    }
    .ai-toggle-slider::before {
        position: absolute;
        content: "";
        height: 24px;
        width: 24px;
        left: 3px;
        bottom: 3px;
        background-color: #ffffff;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.25);
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .ai-toggle-btn input:checked + .ai-toggle-slider {
        background-color: #0284c7;
    }
    .ai-toggle-btn input:checked + .ai-toggle-slider::before {
        transform: translateX(24px);
    }
</style>

<div class="ai-settings-page">
    <div class="max-w-4xl mx-auto">

        {{-- Header Breadcrumb & Judul --}}
        <div class="mb-6 text-center">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-sky-500/10 border border-sky-400/20 text-sky-300 text-xs font-semibold mb-3">
                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                <span>PANEL ADMINISTRATOR</span>
            </div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight flex items-center justify-center gap-3">
                <svg class="w-8 h-8 text-cyan-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z"/>
                </svg>
                Pengaturan Google Gemini AI
            </h1>
            <p class="text-sky-200 text-sm mt-1">Konfigurasi API Key & Model kecerdasan buatan untuk asisten medis staf</p>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="mb-5 p-4 bg-emerald-950/70 border border-emerald-500/50 text-emerald-200 rounded-xl flex items-center gap-3 backdrop-blur-md shadow-lg">
                <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                <div class="text-sm font-medium">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-5 p-4 bg-rose-950/70 border border-rose-500/50 text-rose-200 rounded-xl flex items-center gap-3 backdrop-blur-md shadow-lg">
                <svg class="w-5 h-5 text-rose-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/></svg>
                <div class="text-sm font-medium">{{ session('error') }}</div>
            </div>
        @endif

        @if(session('warning'))
            <div class="mb-5 p-4 bg-amber-950/80 border border-amber-500/60 text-amber-200 rounded-xl flex items-center gap-3 backdrop-blur-md shadow-lg">
                <svg class="w-5 h-5 text-amber-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495ZM10 5a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 5Zm0 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/></svg>
                <div class="text-sm font-medium">{{ session('warning') }}</div>
            </div>
        @endif

        {{-- Main Settings Card Shell --}}
        <div class="ai-card-shell">

            {{-- Top Status Banner --}}
            <div class="px-6 py-4 {{ $settings->enabled ? 'bg-gradient-to-r from-teal-600 via-emerald-600 to-sky-600' : 'bg-gradient-to-r from-slate-700 via-slate-800 to-slate-900' }} text-white flex items-center justify-between gap-4 flex-wrap">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-white/15 backdrop-blur-sm border border-white/20 flex items-center justify-center shrink-0">
                        @if($settings->enabled)
                            <svg class="w-6 h-6 text-emerald-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                        @else
                            <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        @endif
                    </div>
                    <div>
                        <div class="text-[11px] font-semibold tracking-wider uppercase opacity-80">Status Integrasi AI</div>
                        <div class="text-base font-bold flex items-center gap-2">
                            <span>{{ $settings->enabled ? 'Aktif Berjalan' : 'Nonaktif (Belum Aktif)' }}</span>
                            <span class="w-2 h-2 rounded-full {{ $settings->enabled ? 'bg-emerald-300 animate-ping' : 'bg-slate-400' }}"></span>
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <div class="text-[11px] font-semibold tracking-wider uppercase opacity-80">Model AI Terpilih</div>
                    <div class="text-sm font-bold bg-black/25 px-3 py-1 rounded-lg inline-block border border-white/10 mt-0.5">
                        {{ $settings->model ?? 'gemini-3.5-flash' }}
                    </div>
                </div>
            </div>

            {{-- Form Fields --}}
            <form method="POST" action="{{ route('admin.ai-settings.update') }}" id="ai-settings-form" class="p-6 sm:p-8 space-y-6" autocomplete="off">
                @csrf
                @method('PUT')

                {{-- Info Provider Gemini --}}
                <div class="flex items-center gap-4 p-4 bg-sky-50/70 rounded-xl border border-sky-100">
                    <div class="w-12 h-12 bg-white rounded-xl shadow-sm border border-sky-100 flex items-center justify-center shrink-0">
                        <img src="https://www.gstatic.com/lamda/images/gemini_favicon_f069958c85030456e93de685481c559f160ea06.svg"
                             alt="Gemini"
                             class="w-7 h-7"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
                        <div style="display:none" class="w-7 h-7 items-center justify-center text-sky-600">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/></svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-slate-800 text-sm">Google Gemini AI Engine</p>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Dapatkan API Key resmi gratis langsung melalui
                            <a href="https://aistudio.google.com/app/apikey" target="_blank" rel="noopener noreferrer" class="text-sky-600 font-bold hover:underline inline-flex items-center gap-1">
                                Google AI Studio <i class="fas fa-external-link-alt text-[10px]"></i>
                            </a>
                        </p>
                    </div>
                    <input type="hidden" name="provider" value="gemini">
                </div>

                {{-- Toggle Saklar Aktifkan AI --}}
                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-200">
                    <div>
                        <label for="ai-toggle-checkbox" class="font-bold text-slate-800 text-sm block cursor-pointer">
                            Aktifkan Integrasi AI
                        </label>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Nyalakan agar tombol asisten medis "Tanya AI" muncul di portal seluruh staf
                        </p>
                    </div>

                    <input type="hidden" name="enabled" value="0">
                    <label class="ai-toggle-btn">
                        <input type="checkbox" id="ai-toggle-checkbox" name="enabled" value="1" {{ $settings->enabled ? 'checked' : '' }}>
                        <span class="ai-toggle-slider"></span>
                    </label>
                </div>

                {{-- Input API Key --}}
                <div>
                    <label for="api_key" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Google Gemini API Key <span class="text-rose-500 font-bold">*</span>
                    </label>
                    <div class="ai-field-wrapper">
                        {{-- Icon Key Kiri --}}
                        <div class="ai-field-icon-left">
                            <i class="fas fa-key text-slate-400"></i>
                        </div>

                        {{-- Input Text (type="text" dengan CSS Mask agar tidak dideteksi sebagai password akun oleh Chrome/browser) --}}
                        <input type="text"
                            id="api_key"
                            name="api_key"
                            class="ai-field-input ai-field-input-masked"
                            placeholder="{{ $settings->api_key ? '••••••••••••••••••••••••••••••••' : 'Masukkan Gemini API Key (AIzaSy...)' }}"
                            autocomplete="off"
                            autocorrect="off"
                            autocapitalize="off"
                            spellcheck="false"
                            data-lpignore="true"
                            data-1p-ignore="true"
                            data-form-type="other">

                        {{-- Tombol Toggle Lihat Password Kanan --}}
                        <div class="ai-field-icon-right">
                            <button type="button"
                                id="toggle-api-key-visibility"
                                class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors"
                                title="Lihat/Sembunyikan API Key">
                                <i id="eye-icon" class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                    </div>

                    @if($settings->api_key)
                        <div class="mt-2 flex items-center gap-1.5 text-xs text-emerald-600 font-medium">
                            <i class="fas fa-check-circle"></i>
                            <span>API Key sudah tersimpan dengan aman (terenkripsi). Kosongkan field ini jika tidak ingin mengubahnya.</span>
                        </div>
                    @else
                        <div class="mt-2 flex items-center gap-1.5 text-xs text-amber-600 font-medium">
                            <i class="fas fa-exclamation-circle"></i>
                            <span>API Key belum diisi. Masukkan API Key Anda untuk mulai menggunakan AI.</span>
                        </div>
                    @endif
                </div>

                {{-- Pilihan Model Gemini --}}
                <div>
                    <label for="model" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Pilihan Model AI Default
                    </label>
                    <div class="ai-field-wrapper">
                        <div class="ai-field-icon-left">
                            <i class="fas fa-microchip text-slate-400"></i>
                        </div>

                        <select id="model" name="model" class="ai-field-select">
                            @foreach($geminiModels as $value => $label)
                                <option value="{{ $value }}" {{ ($settings->model ?? 'gemini-3.5-flash') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>

                        <div class="ai-field-icon-right pointer-events-none">
                            <i class="fas fa-chevron-down text-xs text-slate-400"></i>
                        </div>
                    </div>
                    <p class="mt-1.5 text-xs text-slate-500">
                        Rekomendasi: <b>Gemini 3.5 Flash</b> untuk kecepatan respons dan kuota gratis yang sangat stabil.
                    </p>
                </div>

                {{-- Kotak Peringatan Keamanan --}}
                <div class="p-4 bg-amber-50/80 border border-amber-200 rounded-xl flex items-start gap-3">
                    <i class="fas fa-shield-alt text-amber-500 text-lg mt-0.5 shrink-0"></i>
                    <div class="text-xs text-amber-900 leading-relaxed">
                        <span class="font-bold block text-sm mb-0.5">Keamanan Data & Privasi</span>
                        API Key disimpan secara terenkripsi di server (AES-256-CBC) dan tidak pernah dibagikan ke client browser publik.
                    </div>
                </div>

                {{-- Form Footer Actions (Inside Form) --}}
                <div class="pt-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3">
                    {{-- Tombol Test --}}
                    <button type="button" onclick="submitTestConnection()"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-xl font-bold text-xs hover:bg-slate-100 hover:border-sky-400 hover:text-sky-700 transition-all shadow-sm">
                        <i class="fas fa-plug text-sky-500"></i>
                        <span>Test Koneksi API</span>
                    </button>

                    {{-- Tombol Simpan --}}
                    <button type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-gradient-to-r from-sky-500 via-sky-600 to-cyan-600 hover:from-sky-600 hover:to-cyan-700 text-white rounded-xl font-bold text-xs transition-all shadow-md shadow-sky-600/30 active:scale-95">
                        <i class="fas fa-save"></i>
                        <span>Simpan Pengaturan</span>
                    </button>
                </div>

            </form>

            {{-- Form Terpisah untuk Test Koneksi (Membawa API Key yang diketik) --}}
            <form method="POST" action="{{ route('admin.ai-settings.test') }}" id="ai-test-form" style="display:none;">
                @csrf
                <input type="hidden" name="api_key" id="test_api_key">
                <input type="hidden" name="model" id="test_model">
            </form>

        </div>

        {{-- Accordion Panduan Setup --}}
        <div class="mt-6 bg-sky-950/60 backdrop-blur-md rounded-2xl border border-sky-400/20 overflow-hidden shadow-xl">
            <button type="button"
                onclick="document.getElementById('setup-guide').classList.toggle('hidden'); document.getElementById('guide-arrow').classList.toggle('rotate-180')"
                class="w-full flex items-center justify-between px-6 py-4 text-white hover:bg-white/5 transition-colors">
                <div class="flex items-center gap-3">
                    <i class="fas fa-book-medical text-cyan-400 text-base"></i>
                    <span class="font-bold text-sm text-sky-100">Panduan Praktis Mendapatkan API Key Google Gemini (Gratis)</span>
                </div>
                <i id="guide-arrow" class="fas fa-chevron-down text-sky-300 transition-transform duration-200 text-xs"></i>
            </button>

            <div id="setup-guide" class="hidden px-6 pb-6 pt-2 space-y-3.5 border-t border-sky-400/10">
                @foreach([
                    ['num' => '1', 'title' => 'Kunjungi Google AI Studio', 'desc' => 'Buka <a href="https://aistudio.google.com/app/apikey" target="_blank" class="text-cyan-300 font-bold underline">aistudio.google.com/app/apikey</a> lalu login menggunakan akun Google.'],
                    ['num' => '2', 'title' => 'Klik tombol Create API Key', 'desc' => 'Pilih <strong>"Create API Key"</strong> di Google AI Studio (bisa menggunakan project bawaan Google Cloud).'],
                    ['num' => '3', 'title' => 'Salin Kunci API', 'desc' => 'Klik ikon salin (copy) pada API Key yang berawalan <code>AIzaSy...</code>.'],
                    ['num' => '4', 'title' => 'Tempel & Simpan', 'desc' => 'Tempelkan di kotak <strong>Google Gemini API Key</strong> di atas, aktifkan saklar, lalu klik <strong>Simpan Pengaturan</strong>.'],
                    ['num' => '5', 'title' => 'Uji Koneksi', 'desc' => 'Klik <strong>Test Koneksi API</strong> untuk memastikan server berhasil terhubung ke Google AI.'],
                ] as $step)
                    <div class="flex items-start gap-3.5 bg-white/5 p-3 rounded-xl border border-white/5">
                        <div class="w-6 h-6 rounded-full bg-sky-500 text-white font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                            {{ $step['num'] }}
                        </div>
                        <div class="text-xs text-slate-300">
                            <span class="font-bold text-white block mb-0.5">{{ $step['title'] }}</span>
                            <span class="text-sky-200/90">{!! $step['desc'] !!}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</div>

<script>
    // Submit test koneksi dengan membawa nilai dari input
    function submitTestConnection() {
        const apiKey = document.getElementById('api_key').value;
        const model = document.getElementById('model').value;
        
        document.getElementById('test_api_key').value = apiKey;
        document.getElementById('test_model').value = model;
        document.getElementById('ai-test-form').submit();
    }

    // Toggle visibilitas API key via CSS mask (tanpa memicu password manager)
    const apiKeyInput = document.getElementById('api_key');
    const toggleBtn = document.getElementById('toggle-api-key-visibility');
    const eyeIcon = document.getElementById('eye-icon');

    if (toggleBtn && apiKeyInput && eyeIcon) {
        toggleBtn.addEventListener('click', function () {
            const isRevealed = apiKeyInput.classList.toggle('ai-revealed');
            eyeIcon.className = isRevealed ? 'fas fa-eye-slash text-sm' : 'fas fa-eye text-sm';
        });
    }
</script>
@endsection
