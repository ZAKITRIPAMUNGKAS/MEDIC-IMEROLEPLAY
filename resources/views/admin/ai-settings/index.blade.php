@extends('layouts.app')

@section('title', 'Pengaturan AI (Gemini) - Portal Medis')

@section('content')
<div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    {{-- Background gradient --}}
    <div class="absolute inset-0 bg-gradient-to-br from-violet-900 via-purple-800 to-indigo-700"></div>
    <div class="absolute inset-0 bg-black bg-opacity-20"></div>

    {{-- Floating particles decoration --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-10 w-32 h-32 bg-violet-400 rounded-full opacity-10 blur-3xl animate-pulse"></div>
        <div class="absolute bottom-20 right-10 w-48 h-48 bg-indigo-400 rounded-full opacity-10 blur-3xl animate-pulse" style="animation-delay:1s"></div>
        <div class="absolute top-1/2 left-1/2 w-64 h-64 bg-purple-400 rounded-full opacity-5 blur-3xl animate-pulse" style="animation-delay:0.5s"></div>
    </div>

    <div class="relative max-w-4xl w-full mx-auto">

        {{-- Header --}}
        <div class="mb-8 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white/10 backdrop-blur-sm rounded-3xl mb-4 shadow-2xl ring-1 ring-white/20">
                <svg class="w-10 h-10 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Pengaturan Google Gemini AI</h1>
            <p class="text-purple-200">Konfigurasi API Key untuk integrasi kecerdasan buatan Gemini</p>
        </div>

        {{-- Alert Messages --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-lg shadow-sm">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-lg shadow-sm">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/></svg>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        {{-- Main Card --}}
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">

            {{-- Status Banner --}}
            <div class="bg-gradient-to-r {{ $settings->enabled ? 'from-violet-500 to-purple-600' : 'from-slate-400 to-slate-500' }} p-5">
                <div class="flex items-center justify-between text-white">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center ring-1 ring-white/30">
                            @if($settings->enabled)
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                            @else
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/></svg>
                            @endif
                        </div>
                        <div>
                            <p class="text-xs font-medium opacity-80">Status Integrasi AI</p>
                            <p class="text-xl font-bold">{{ $settings->enabled ? 'Aktif' : 'Nonaktif' }}</p>
                        </div>
                    </div>
                    <div class="text-right hidden sm:block">
                        <p class="text-xs opacity-80">Model Aktif</p>
                        <p class="text-lg font-semibold">{{ $settings->model ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Form Body --}}
            <form method="POST" action="{{ route('admin.ai-settings.update') }}" id="ai-settings-form">
                @csrf
                @method('PUT')

                <div class="p-6 space-y-6">

                    {{-- Provider Info --}}
                    <div class="flex items-center gap-4 p-4 bg-gradient-to-r from-violet-50 to-purple-50 rounded-xl border border-violet-100">
                        <div class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center shrink-0">
                            <img src="https://www.gstatic.com/lamda/images/gemini_favicon_f069958c85030456e93de685481c559f160ea06.svg"
                                 alt="Gemini"
                                 class="w-7 h-7"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
                            <div style="display:none" class="w-7 h-7 items-center justify-center">
                                <svg class="w-7 h-7 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-800">Google Gemini AI</p>
                            <p class="text-sm text-slate-500">Dapatkan API Key gratis di
                                <a href="https://aistudio.google.com/app/apikey" target="_blank" class="text-violet-600 hover:underline font-medium">
                                    Google AI Studio →
                                </a>
                            </p>
                        </div>
                        <input type="hidden" name="provider" value="gemini">
                    </div>

                    {{-- Enable Toggle --}}
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-200">
                        <div>
                            <label class="font-semibold text-slate-700 block">Aktifkan Integrasi AI</label>
                            <p class="text-sm text-slate-500 mt-0.5">Nyalakan untuk menggunakan fitur AI di aplikasi</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="enabled" value="0">
                            <input type="checkbox" id="enabled-toggle" name="enabled" value="1"
                                class="sr-only peer"
                                {{ $settings->enabled ? 'checked' : '' }}>
                            <div class="w-14 h-7 bg-slate-300 peer-focus:ring-4 peer-focus:ring-violet-200 rounded-full peer
                                        peer-checked:bg-violet-600 transition-colors duration-200 after:content-[''] after:absolute
                                        after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-6 after:w-6
                                        after:transition-all peer-checked:after:translate-x-7"></div>
                        </label>
                    </div>

                    {{-- API Key --}}
                    <div>
                        <label for="api_key" class="block text-sm font-semibold text-slate-700 mb-2">
                            API Key
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 0 1 21.75 8.25Z" />
                                </svg>
                            </div>
                            <input type="password"
                                id="api_key"
                                name="api_key"
                                placeholder="{{ $settings->api_key ? '••••••••••••••••' . substr($settings->api_key, -4) : 'Masukkan Gemini API Key Anda...' }}"
                                class="w-full pl-11 pr-12 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-violet-400 focus:border-violet-400 outline-none transition-all text-slate-700 bg-white font-mono text-sm"
                                autocomplete="new-password">
                            <button type="button"
                                id="toggle-api-key-visibility"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 transition-colors"
                                title="Tampilkan/Sembunyikan API Key">
                                <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                </svg>
                                <svg id="eye-off-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/>
                                </svg>
                            </button>
                        </div>
                        @if($settings->api_key)
                            <p class="mt-1.5 text-xs text-slate-500">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                                    API Key sudah tersimpan. Kosongkan field ini jika tidak ingin mengubahnya.
                                </span>
                            </p>
                        @else
                            <p class="mt-1.5 text-xs text-slate-500">API Key belum dikonfigurasi.</p>
                        @endif
                    </div>

                    {{-- Model Selection --}}
                    <div>
                        <label for="model" class="block text-sm font-semibold text-slate-700 mb-2">
                            Model Gemini
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5m0 15V21m3.75-18v1.5m0 15V21m-9-1.5h10.5a2.25 2.25 0 0 0 2.25-2.25V6.75a2.25 2.25 0 0 0-2.25-2.25H6.75A2.25 2.25 0 0 0 4.5 6.75v10.5a2.25 2.25 0 0 0 2.25 2.25Zm.75-12h9v9h-9v-9Z" />
                                </svg>
                            </div>
                            <select id="model" name="model"
                                class="w-full pl-11 pr-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-violet-400 focus:border-violet-400 outline-none transition-all text-slate-700 bg-white appearance-none">
                                @foreach($geminiModels as $value => $label)
                                    <option value="{{ $value }}" {{ ($settings->model ?? 'gemini-3.6-flash') === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                                </svg>
                            </div>
                        </div>
                        <p class="mt-1.5 text-xs text-slate-500">Pilih model yang sesuai kebutuhan. Gemini 3.6 Flash paling cepat dan stabil.</p>
                    </div>

                    {{-- Info Box --}}
                    <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl">
                        <div class="flex gap-3">
                            <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495ZM10 5a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 5Zm0 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/>
                            </svg>
                            <div class="text-sm text-amber-800">
                                <p class="font-semibold mb-1">Keamanan API Key</p>
                                <p class="text-amber-700">API Key disimpan terenkripsi di database. Pastikan tidak membagikan API Key Anda kepada siapapun.</p>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Form Footer --}}
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3">
                    {{-- Test Button --}}
                    <form method="POST" action="{{ route('admin.ai-settings.test') }}" class="w-full sm:w-auto">
                        @csrf
                        <button type="submit"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-xl font-medium text-sm hover:bg-slate-100 hover:border-violet-400 hover:text-violet-700 transition-all duration-200 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z"/>
                            </svg>
                            Test Koneksi
                        </button>
                    </form>

                    {{-- Save Button --}}
                    <button type="submit" form="ai-settings-form"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 text-white rounded-xl font-semibold text-sm transition-all duration-200 shadow-lg hover:shadow-violet-500/30 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16v2a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-2M12 12V4m0 0L8 8m4-4 4 4"/>
                        </svg>
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>

        {{-- Gemini Setup Guide (Collapsible) --}}
        <div class="mt-6 bg-white/10 backdrop-blur-sm rounded-2xl border border-white/20 overflow-hidden">
            <button type="button"
                onclick="document.getElementById('setup-guide').classList.toggle('hidden')"
                class="w-full flex items-center justify-between px-6 py-4 text-white hover:bg-white/5 transition-colors">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                    </svg>
                    <span class="font-semibold">Panduan Setup Google Gemini API</span>
                </div>
                <svg class="w-5 h-5 text-purple-200 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                </svg>
            </button>

            <div id="setup-guide" class="hidden px-6 pb-6 space-y-4">
                @foreach([
                    ['step' => '1', 'title' => 'Buka Google AI Studio', 'desc' => 'Kunjungi <a href="https://aistudio.google.com/app/apikey" target="_blank" class="text-purple-200 hover:underline">aistudio.google.com/app/apikey</a> dan login dengan akun Google Anda.'],
                    ['step' => '2', 'title' => 'Buat API Key Baru', 'desc' => 'Klik tombol <strong>"Create API Key"</strong> dan pilih project Google Cloud Anda (atau buat project baru).'],
                    ['step' => '3', 'title' => 'Salin API Key', 'desc' => 'Salin API Key yang dihasilkan. Format biasanya dimulai dengan <code class="bg-white/10 px-1 rounded">AIza...</code>'],
                    ['step' => '4', 'title' => 'Paste di Form', 'desc' => 'Tempel API Key di field "API Key" di atas, pilih model, aktifkan toggle, lalu klik <strong>Simpan</strong>.'],
                    ['step' => '5', 'title' => 'Test Koneksi', 'desc' => 'Klik tombol <strong>"Test Koneksi"</strong> untuk memastikan API Key berfungsi dengan baik.'],
                ] as $item)
                    <div class="flex gap-4">
                        <div class="w-8 h-8 bg-violet-500 text-white rounded-full flex items-center justify-center font-bold text-sm shrink-0">
                            {{ $item['step'] }}
                        </div>
                        <div>
                            <p class="text-white font-semibold">{{ $item['title'] }}</p>
                            <p class="text-purple-200 text-sm mt-0.5">{!! $item['desc'] !!}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</div>

<script>
    // Toggle API key visibility
    const apiKeyInput = document.getElementById('api_key');
    const toggleBtn = document.getElementById('toggle-api-key-visibility');
    const eyeIcon = document.getElementById('eye-icon');
    const eyeOffIcon = document.getElementById('eye-off-icon');

    toggleBtn.addEventListener('click', function () {
        const isPassword = apiKeyInput.type === 'password';
        apiKeyInput.type = isPassword ? 'text' : 'password';
        eyeIcon.classList.toggle('hidden', isPassword);
        eyeOffIcon.classList.toggle('hidden', !isPassword);
    });
</script>
@endsection
