<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TelegramSetting;
use App\Services\TelegramService;
use Illuminate\Http\Request;

class TelegramSettingController extends Controller
{
    public function index()
    {
        $settings = TelegramSetting::getSettings();
        return view('admin.telegram.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'bot_token' => 'nullable|string',
            'chat_ids' => 'nullable|string',
            'enabled' => 'boolean',
            'notify_chat' => 'boolean',
            'notify_feedback' => 'boolean',
        ]);

        $settings = TelegramSetting::getSettings();
        $settings->update($validated);

        return redirect()->route('admin.telegram.index')
            ->with('success', 'Pengaturan Telegram berhasil disimpan!');
    }

    public function test()
    {
        $settings = TelegramSetting::getSettings();

        if (!$settings->enabled || empty($settings->bot_token) || empty($settings->chat_ids)) {
            return back()->with('error', 'Telegram belum dikonfigurasi dengan benar. Pastikan Bot Token dan Chat ID sudah diisi dan diaktifkan.');
        }

        try {
            $telegram = new TelegramService();
            $success = $telegram->sendTestNotification();

            if ($success) {
                return back()->with('success', 'Test notifikasi berhasil dikirim! Cek Telegram Anda.');
            } else {
                return back()->with('error', 'Gagal mengirim notifikasi. Periksa Bot Token dan Chat ID.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
