@extends('layouts.app')

@section('title', 'Pengaturan Telegram Bot - Portal Medis MPK-BA')

@section('content')
<div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700"></div>
    <div class="absolute inset-0 bg-black bg-opacity-20"></div>

    <div class="relative max-w-5xl w-full mx-auto text-white">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-white mb-2">⚙️ Pengaturan Telegram Bot</h1>
            <p class="text-sky-200">Konfigurasi notifikasi Telegram untuk chat dan feedback baru</p>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-center gap-2">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-center gap-2">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
        @endif

        <!-- Setup Instructions -->
        <div class="bg-gradient-to-r from-blue-50 to-cyan-50 border-2 border-blue-200 rounded-2xl p-6 mb-6">
            <h2 class="text-xl font-bold text-blue-900 mb-4 flex items-center gap-2">
                <i class="fas fa-info-circle"></i>
                Cara Setup Telegram Bot
            </h2>
            
            <ol class="space-y-3 text-slate-700">
                <li class="flex gap-3">
                    <span class="flex-shrink-0 w-7 h-7 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold">1</span>
                    <div>
                        <strong>Buat Bot via BotFather:</strong><br>
                        Buka Telegram, cari <code class="bg-white px-2 py-0.5 rounded">@BotFather</code>, kirim <code class="bg-white px-2 py-0.5 rounded">/newbot</code>, ikuti instruksi, lalu <strong>copy Bot Token</strong> yang diberikan.
                    </div>
                </li>
                <li class="flex gap-3">
                    <span class="flex-shrink-0 w-7 h-7 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold">2</span>
                    <div>
                        <strong>Dapatkan Chat ID:</strong><br>
                        Di Telegram, cari <code class="bg-white px-2 py-0.5 rounded">@getidsbot</code>, kirim <code class="bg-white px-2 py-0.5 rounded">/start</code>, bot akan kirim <strong>Chat ID</strong> Anda (contoh: 123456789).
                    </div>
                </li>
                <li class="flex gap-3">
                    <span class="flex-shrink-0 w-7 h-7 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold">3</span>
                    <div>
                        <strong>Isi Form di Bawah:</strong><br>
                        Masukkan Bot Token dan Chat ID, lalu aktifkan notifikasi. Untuk multiple admin, pisahkan Chat ID dengan koma (contoh: 123,456,789).
                    </div>
                </li>
            </ol>
        </div>

        <!-- Settings Form -->
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-6">
            <form action="{{ route('admin.telegram.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Bot Token -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        🤖 Bot Token
                    </label>
                    <input type="text" name="bot_token" value="{{ old('bot_token', $settings->bot_token) }}"
                        class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all font-mono text-sm"
                        placeholder="1234567890:ABCdefGHIjklMNOpqrsTUVwxyz">
                    <p class="mt-1 text-xs text-slate-500">Token dari @BotFather setelah membuat bot</p>
                </div>

                <!-- Chat IDs -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        👥 Chat ID (Admin)
                    </label>
                    <input type="text" name="chat_ids" value="{{ old('chat_ids', $settings->chat_ids) }}"
                        class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all font-mono text-sm"
                        placeholder="123456789">
                    <p class="mt-1 text-xs text-slate-500">Chat ID dari @getidsbot. Untuk multiple admin, pisahkan dengan koma (123,456,789)</p>
                </div>

                <!-- Enable/Disable -->
                <div class="flex items-center gap-3 p-4 bg-slate-50 rounded-lg">
                    <input type="hidden" name="enabled" value="0">
                    <input type="checkbox" name="enabled" id="enabled" value="1" 
                        {{ old('enabled', $settings->enabled) ? 'checked' : '' }}
                        class="w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-200">
                    <label for="enabled" class="font-semibold text-slate-700 cursor-pointer">
                        ✅ Aktifkan Notifikasi Telegram
                    </label>
                </div>

                <!-- Notification Types -->
                <div class="space-y-3">
                    <p class="text-sm font-semibold text-slate-700">📢 Jenis Notifikasi:</p>
                    
                    <div class="flex items-center gap-3 p-3 bg-sky-50 rounded-lg border border-sky-200">
                        <input type="hidden" name="notify_chat" value="0">
                        <input type="checkbox" name="notify_chat" id="notify_chat" value="1"
                            {{ old('notify_chat', $settings->notify_chat) ? 'checked' : '' }}
                            class="w-4 h-4 text-sky-600 rounded focus:ring-2 focus:ring-sky-200">
                        <label for="notify_chat" class="text-slate-700 cursor-pointer flex items-center gap-2">
                            <i class="fas fa-comments text-sky-600"></i>
                            Notifikasi Chat Baru
                        </label>
                    </div>

                    <div class="flex items-center gap-3 p-3 bg-amber-50 rounded-lg border border-amber-200">
                        <input type="hidden" name="notify_feedback" value="0">
                        <input type="checkbox" name="notify_feedback" id="notify_feedback" value="1"
                            {{ old('notify_feedback', $settings->notify_feedback) ? 'checked' : '' }}
                            class="w-4 h-4 text-amber-600 rounded focus:ring-2 focus:ring-amber-200">
                        <label for="notify_feedback" class="text-slate-700 cursor-pointer flex items-center gap-2">
                            <i class="fas fa-comment-dots text-amber-600"></i>
                            Notifikasi Feedback Baru
                        </label>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3 pt-4">
                    <button type="submit"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white font-semibold rounded-lg shadow-lg transition-all hover:shadow-xl flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i>
                        Simpan Pengaturan
                    </button>
                </div>
            </form>

            <!-- Test Button -->
            <form action="{{ route('admin.telegram.test') }}" method="POST" class="mt-4">
                @csrf
                <button type="submit"
                    class="w-full px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white font-semibold rounded-lg shadow-lg transition-all hover:shadow-xl flex items-center justify-center gap-2">
                    <i class="fas fa-paper-plane"></i>
                    Kirim Test Notifikasi
                </button>
            </form>
        </div>

        <!-- Current Status -->
        <div class="mt-6 bg-slate-50 rounded-xl p-4 border border-slate-200">
            <h3 class="text-sm font-semibold text-slate-700 mb-3">📊 Status Saat Ini:</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full {{ $settings->enabled ? 'bg-green-500' : 'bg-red-500' }}"></div>
                    <span class="text-slate-600">
                        Status: <strong class="{{ $settings->enabled ? 'text-green-600' : 'text-red-600' }}">
                            {{ $settings->enabled ? 'Aktif' : 'Nonaktif' }}
                        </strong>
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-robot text-blue-500"></i>
                    <span class="text-slate-600">
                        Bot: <strong>{{ $settings->bot_token ? 'Terkonfigurasi' : 'Belum diisi' }}</strong>
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-users text-green-500"></i>
                    <span class="text-slate-600">
                        Admin: <strong>{{ count($settings->chat_ids_array) }} orang</strong>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
