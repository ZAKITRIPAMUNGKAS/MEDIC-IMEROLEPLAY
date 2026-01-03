@extends('layouts.app')

@section('title', 'Pengaturan Telegram Bot - Portal Medis MPK-BA')

@section('content')
<div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700"></div>
    <div class="absolute inset-0 bg-black bg-opacity-20"></div>

    <div class="relative max-w-4xl w-full mx-auto">
        <!-- Header -->
        <div class="mb-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white/10 backdrop-blur-sm rounded-2xl mb-4">
                <i class="fab fa-telegram text-4xl text-white"></i>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Pengaturan Telegram Bot</h1>
            <p class="text-sky-200">Terima notifikasi instant untuk chat dan feedback baru</p>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-lg shadow-sm animate-in slide-in-from-top-2 duration-300">
                <div class="flex items-center gap-3">
                    <i class="fas fa-check-circle text-xl"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-lg shadow-sm animate-in slide-in-from-top-2 duration-300">
                <div class="flex items-center gap-3">
                    <i class="fas fa-exclamation-circle text-xl"></i>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Main Card -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <!-- Status Banner -->
            <div class="bg-gradient-to-r {{ $settings->enabled ? 'from-green-500 to-emerald-500' : 'from-slate-400 to-slate-500' }} p-4">
                <div class="flex items-center justify-between text-white">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                            <i class="fas {{ $settings->enabled ? 'fa-check-circle' : 'fa-times-circle' }} text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium opacity-90">Status Notifikasi</p>
                            <p class="text-lg font-bold">{{ $settings->enabled ? 'Aktif' : 'Nonaktif' }}</p>
                        </div>
                    </div>
                    <div class="text-right hidden sm:block">
                        <p class="text-xs opacity-90">Admin Terdaftar</p>
                        <p class="text-2xl font-bold">{{ count($settings->chat_ids_array) }}</p>
                    </div>
                </div>
            </div>

            <!-- Setup Guide (Collapsible) -->
            <div class="border-b border-slate-200">
                <button type="button" onclick="toggleGuide()" 
                    class="w-full px-6 py-4 flex items-center justify-between hover:bg-slate-50 transition-colors group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-book-open text-blue-500 text-lg"></i>
                        <span class="font-semibold text-slate-700">Panduan Setup Telegram Bot</span>
                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full font-medium">3 Langkah Mudah</span>
                    </div>
                    <i class="fas fa-chevron-down text-slate-400 transition-transform duration-200" id="guideIcon"></i>
                </button>

                <div id="setupGuide" class="hidden border-t border-slate-100">
                    <div class="p-6 bg-gradient-to-br from-blue-50 to-cyan-50">
                        <div class="grid gap-4">
                            <!-- Step 1 -->
                            <div class="flex gap-4 p-4 bg-white rounded-xl shadow-sm border border-blue-100">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-full flex items-center justify-center font-bold shadow-md">1</div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-bold text-slate-800 mb-2">Buat Bot Telegram</h3>
                                    <ol class="text-sm text-slate-600 space-y-1 list-disc ml-4">
                                        <li>Buka Telegram dan cari <code class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded font-mono text-xs">@BotFather</code></li>
                                        <li>Kirim perintah <code class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded font-mono text-xs">/newbot</code></li>
                                        <li>Ikuti instruksi dan berikan nama bot Anda</li>
                                        <li>Copy <strong>Bot Token</strong> yang diberikan (contoh: <code class="font-mono text-xs">1234567890:ABCD...</code>)</li>
                                    </ol>
                                </div>
                            </div>

                            <!-- Step 2 -->
                            <div class="flex gap-4 p-4 bg-white rounded-xl shadow-sm border border-blue-100">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-gradient-to-br from-cyan-500 to-cyan-600 text-white rounded-full flex items-center justify-center font-bold shadow-md">2</div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-bold text-slate-800 mb-2">Dapatkan Chat ID</h3>
                                    <ol class="text-sm text-slate-600 space-y-1 list-disc ml-4">
                                        <li>Di Telegram, cari <code class="bg-cyan-100 text-cyan-700 px-2 py-0.5 rounded font-mono text-xs">@getidsbot</code></li>
                                        <li>Kirim perintah <code class="bg-cyan-100 text-cyan-700 px-2 py-0.5 rounded font-mono text-xs">/start</code></li>
                                        <li>Bot akan memberikan <strong>Chat ID</strong> Anda (angka, contoh: <code class="font-mono text-xs">123456789</code>)</li>
                                        <li>Untuk multiple admin: minta setiap admin mendapatkan Chat ID mereka</li>
                                    </ol>
                                </div>
                            </div>

                            <!-- Step 3 -->
                            <div class="flex gap-4 p-4 bg-white rounded-xl shadow-sm border border-green-100">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 text-white rounded-full flex items-center justify-center font-bold shadow-md">3</div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-bold text-slate-800 mb-2">Konfigurasi di Bawah</h3>
                                    <p class="text-sm text-slate-600">
                                        Masukkan Bot Token dan Chat ID ke form di bawah ini, lalu aktifkan notifikasi. Jangan lupa klik <strong>Simpan Pengaturan</strong> dan <strong>Test Notifikasi</strong>!
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Form -->
            <div class="p-6">
                <form action="{{ route('admin.telegram.update') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Bot Token -->
                    <div>
                        <label class="flex items-center gap-2 text-sm font-bold text-slate-700 mb-3">
                            <i class="fas fa-robot text-blue-500"></i>
                            Bot Token
                            <span class="text-xs font-normal text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" name="bot_token" id="botToken" 
                                value="{{ old('bot_token', $settings->bot_token) }}"
                                class="w-full px-4 py-3 pr-20 rounded-xl border-2 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none transition-all font-mono text-sm"
                                placeholder="1234567890:ABCdefGHIjklMNOpqrsTUVwxyz">
                            @if($settings->bot_token)
                                <button type="button" onclick="copyToClipboard('botToken')" 
                                    class="absolute right-2 top-1/2 -translate-y-1/2 px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg text-xs font-medium transition-colors">
                                    <i class="fas fa-copy mr-1"></i> Copy
                                </button>
                            @endif
                        </div>
                        <p class="mt-2 text-xs text-slate-500 flex items-start gap-2">
                            <i class="fas fa-info-circle mt-0.5 text-blue-500"></i>
                            <span>Dapatkan dari <strong>@BotFather</strong> di Telegram setelah membuat bot baru</span>
                        </p>
                    </div>

                    <!-- Chat IDs -->
                    <div>
                        <label class="flex items-center gap-2 text-sm font-bold text-slate-700 mb-3">
                            <i class="fas fa-users text-green-500"></i>
                            Chat ID Admin
                            <span class="text-xs font-normal text-red-500">*</span>
                            <span class="ml-auto text-xs font-normal text-slate-500">
                                {{ count($settings->chat_ids_array) > 0 ? count($settings->chat_ids_array) . ' admin terdaftar' : 'Belum ada admin' }}
                            </span>
                        </label>
                        <div class="relative">
                            <input type="text" name="chat_ids" id="chatIds"
                                value="{{ old('chat_ids', $settings->chat_ids) }}"
                                class="w-full px-4 py-3 pr-20 rounded-xl border-2 border-slate-200 focus:border-green-500 focus:ring-4 focus:ring-green-100 outline-none transition-all font-mono text-sm"
                                placeholder="123456789">
                            @if($settings->chat_ids)
                                <button type="button" onclick="copyToClipboard('chatIds')" 
                                    class="absolute right-2 top-1/2 -translate-y-1/2 px-3 py-1.5 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg text-xs font-medium transition-colors">
                                    <i class="fas fa-copy mr-1"></i> Copy
                                </button>
                            @endif
                        </div>
                        <p class="mt-2 text-xs text-slate-500 flex items-start gap-2">
                            <i class="fas fa-lightbulb mt-0.5 text-amber-500"></i>
                            <span>Untuk <strong>multiple admin</strong>, pisahkan dengan <strong>koma</strong> tanpa spasi: <code class="bg-slate-100 px-1.5 py-0.5 rounded font-mono">123456789,987654321,555666777</code></span>
                        </p>
                    </div>

                    <div class="border-t border-slate-200 pt-6">
                        <!-- Master Switch -->
                        <div class="mb-6">
                            <label class="flex items-center gap-4 p-4 bg-gradient-to-r from-slate-50 to-slate-100 rounded-xl cursor-pointer border-2 border-slate-200 hover:border-blue-300 transition-all">
                                <input type="hidden" name="enabled" value="0">
                                <input type="checkbox" name="enabled" id="enabled" value="1" 
                                    {{ old('enabled', $settings->enabled) ? 'checked' : '' }}
                                    class="w-6 h-6 text-blue-600 rounded-lg focus:ring-4 focus:ring-blue-200 cursor-pointer">
                                <div class="flex-1">
                                    <span class="font-bold text-slate-800 text-base flex items-center gap-2">
                                        <i class="fas fa-power-off"></i>
                                        Aktifkan Notifikasi Telegram
                                    </span>
                                    <p class="text-xs text-slate-500 mt-1">Bot akan mengirim notifikasi untuk semua event yang dipilih di bawah</p>
                                </div>
                            </label>
                        </div>

                        <!-- Notification Types -->
                        <div>
                            <p class="text-sm font-bold text-slate-700 mb-3 flex items-center gap-2">
                                <i class="fas fa-bell"></i>
                                Pilih Jenis Notifikasi
                            </p>
                            
                            <div class="space-y-3">
                                <!-- Chat Notification -->
                                <label class="flex items-center gap-3 p-4 bg-sky-50 rounded-xl cursor-pointer border-2 border-sky-200 hover:border-sky-400 transition-all group">
                                    <input type="hidden" name="notify_chat" value="0">
                                    <input type="checkbox" name="notify_chat" id="notify_chat" value="1"
                                        {{ old('notify_chat', $settings->notify_chat) ? 'checked' : '' }}
                                        class="w-5 h-5 text-sky-600 rounded focus:ring-4 focus:ring-sky-200 cursor-pointer">
                                    <div class="flex-1">
                                        <span class="font-semibold text-slate-800 flex items-center gap-2">
                                            <i class="fas fa-comments text-sky-600"></i>
                                            Chat Baru
                                        </span>
                                        <p class="text-xs text-slate-600 mt-0.5">Notifikasi saat ada pesan masuk di Live Chat</p>
                                    </div>
                                    <i class="fas fa-check-circle text-sky-600 opacity-0 group-has-[:checked]:opacity-100 transition-opacity"></i>
                                </label>

                                <!-- Feedback Notification -->
                                <label class="flex items-center gap-3 p-4 bg-amber-50 rounded-xl cursor-pointer border-2 border-amber-200 hover:border-amber-400 transition-all group">
                                    <input type="hidden" name="notify_feedback" value="0">
                                    <input type="checkbox" name="notify_feedback" id="notify_feedback" value="1"
                                        {{ old('notify_feedback', $settings->notify_feedback) ? 'checked' : '' }}
                                        class="w-5 h-5 text-amber-600 rounded focus:ring-4 focus:ring-amber-200 cursor-pointer">
                                    <div class="flex-1">
                                        <span class="font-semibold text-slate-800 flex items-center gap-2">
                                            <i class="fas fa-comment-dots text-amber-600"></i>
                                            Feedback (Laporan & Masukan)
                                        </span>
                                        <p class="text-xs text-slate-600 mt-0.5">Notifikasi saat ada laporan atau masukan baru</p>
                                    </div>
                                    <i class="fas fa-check-circle text-amber-600 opacity-0 group-has-[:checked]:opacity-100 transition-opacity"></i>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-slate-200">
                        <button type="submit"
                            class="flex-1 px-6 py-4 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-bold rounded-xl shadow-lg transition-all hover:shadow-xl hover:-translate-y-0.5 flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i>
                            Simpan Pengaturan
                        </button>
                    </div>
                </form>

                <!-- Test Button -->
                <form action="{{ route('admin.telegram.test') }}" method="POST" class="mt-3">
                    @csrf
                    <button type="submit"
                        class="w-full px-6 py-4 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-bold rounded-xl shadow-lg transition-all hover:shadow-xl hover:-translate-y-0.5 flex items-center justify-center gap-2">
                        <i class="fas fa-paper-plane"></i>
                        Kirim Test Notifikasi
                    </button>
                    <p class="text-center text-xs text-slate-500 mt-2">
                        <i class="fas fa-info-circle"></i> Pastikan sudah simpan pengaturan sebelum test
                    </p>
                </form>
            </div>

            <!-- Footer Info -->
            <div class="bg-slate-50 px-6 py-4 border-t border-slate-200">
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2 text-slate-600">
                        <i class="fas fa-shield-alt text-blue-500"></i>
                        <span>Bot Token dan Chat ID disimpan dengan aman</span>
                    </div>
                    <div class="hidden sm:flex items-center gap-2 text-slate-500">
                        <i class="fab fa-telegram"></i>
                        <span class="text-xs">Powered by Telegram Bot API</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Tips -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl p-4 text-white">
                <div class="flex items-start gap-3">
                    <i class="fas fa-lightbulb text-yellow-300 text-xl mt-1"></i>
                    <div>
                        <h4 class="font-bold mb-1">Tips Keamanan</h4>
                        <p class="text-sm text-sky-100">Jangan bagikan Bot Token kepada siapapun. Token ini seperti password untuk mengontrol bot Anda.</p>
                    </div>
                </div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl p-4 text-white">
                <div class="flex items-start gap-3">
                    <i class="fas fa-question-circle text-cyan-300 text-xl mt-1"></i>
                    <div>
                        <h4 class="font-bold mb-1">Butuh Bantuan?</h4>
                        <p class="text-sm text-sky-100">Chat ID salah? Pastikan Anda sudah kirim /start ke bot Anda terlebih dahulu agar bisa menerima pesan.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleGuide() {
    const guide = document.getElementById('setupGuide');
    const icon = document.getElementById('guideIcon');
    
    if (guide.classList.contains('hidden')) {
        guide.classList.remove('hidden');
        icon.classList.add('rotate-180');
    } else {
        guide.classList.add('hidden');
        icon.classList.remove('rotate-180');
    }
}

function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    element.select();
    element.setSelectionRange(0, 99999); // For mobile
    
    navigator.clipboard.writeText(element.value).then(() => {
        // Show temporary success message
        const button = event.target.closest('button');
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check mr-1"></i> Copied!';
        button.classList.add('bg-green-200', 'text-green-800');
        button.classList.remove('bg-blue-100', 'text-blue-700', 'bg-green-100', 'text-green-700');
        
        setTimeout(() => {
            button.innerHTML = originalHTML;
            button.classList.remove('bg-green-200', 'text-green-800');
            if (elementId === 'botToken') {
                button.classList.add('bg-blue-100', 'text-blue-700');
            } else {
                button.classList.add('bg-green-100', 'text-green-700');
            }
        }, 2000);
    });
}
</script>
@endsection
