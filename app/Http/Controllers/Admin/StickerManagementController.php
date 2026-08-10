<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StickerPack;
use App\Models\Sticker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class StickerManagementController extends Controller
{
    public function index()
    {
        $packs = StickerPack::withCount('stickers')->orderBy('sort_order')->get();
        
        // Get GIPHY config
        $giphyEnabled = Cache::rememberForever('giphy_enabled', function () {
            if (\Schema::hasTable('settings')) {
                $dbVal = DB::table('settings')->where('key', 'giphy_enabled')->value('value');
                if ($dbVal !== null) {
                    return $dbVal !== 'false';
                }
            }
            return true;
        });

        return view('admin.stickers.index', compact('packs', 'giphyEnabled'));
    }

    public function storePack(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'integer|min:0'
        ]);

        StickerPack::create([
            'name' => $request->name,
            'description' => $request->description,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => true
        ]);

        return redirect()->back()->with('success', 'Sticker Pack berhasil dibuat!');
    }

    public function updatePack(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean'
        ]);

        $pack = StickerPack::findOrFail($id);
        $pack->update([
            'name' => $request->name,
            'description' => $request->description,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->back()->with('success', 'Sticker Pack berhasil diperbarui!');
    }

    public function destroyPack($id)
    {
        $pack = StickerPack::findOrFail($id);
        $pack->delete();
        return redirect()->back()->with('success', 'Sticker Pack berhasil dihapus!');
    }

    public function uploadStickers(Request $request, $packId)
    {
        $request->validate([
            'stickers' => 'required|array',
            'stickers.*' => 'required|image|mimes:png,jpg,jpeg,gif,webp|max:2048'
        ]);

        $pack = StickerPack::findOrFail($packId);

        foreach ($request->file('stickers') as $file) {
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            // Move file to public/uploads/stickers
            $file->move(public_path('uploads/stickers'), $filename);
            $url = asset('uploads/stickers/' . $filename);

            Sticker::create([
                'pack_id' => $pack->id,
                'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                'file_url' => $url,
                'file_type' => $file->getClientOriginalExtension(),
                'is_animated' => in_array($file->getClientOriginalExtension(), ['gif', 'webp']),
                'is_active' => true,
                'sort_order' => Sticker::where('pack_id', $packId)->count()
            ]);
        }

        return redirect()->back()->with('success', 'Stiker berhasil diunggah!');
    }

    public function destroySticker($id)
    {
        $sticker = Sticker::findOrFail($id);
        
        // Extract filename from URL
        $path = parse_url($sticker->file_url, PHP_URL_PATH);
        if ($path) {
            $filename = basename($path);
            $filePath = public_path('uploads/stickers/' . $filename);
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        $sticker->delete();
        return redirect()->back()->with('success', 'Stiker berhasil dihapus!');
    }

    public function toggleGiphy(Request $request)
    {
        $enabled = $request->get('enabled') === 'true' || $request->has('giphy_enabled');
        
        Cache::forever('giphy_enabled', $enabled);
        
        if (\Schema::hasTable('settings')) {
            DB::table('settings')->updateOrInsert(
                ['key' => 'giphy_enabled'],
                ['value' => $enabled ? 'true' : 'false', 'updated_at' => now()]
            );
        }

        return redirect()->back()->with('success', 'Integrasi GIPHY berhasil diperbarui!');
    }
}
