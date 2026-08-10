<?php

namespace App\Http\Controllers;

use App\Models\StickerPack;
use App\Models\UserStickerFavorite;
use App\Models\UserRecentSticker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class StickerController extends Controller
{
    private $apiKey;
    private $giphyUrl = 'https://api.giphy.com/v1/stickers';

    public function __construct()
    {
        $this->apiKey = env('GIPHY_API_KEY');
    }

    private function getApiKey()
    {
        // If GIPHY integration is disabled by admin, return null
        $giphyEnabled = Cache::remember('giphy_enabled', 3600, function () {
            return \Illuminate\Support\Facades\Schema::hasTable('settings') 
                ? (\Illuminate\Support\Facades\DB::table('settings')->where('key', 'giphy_enabled')->value('value') !== 'false')
                : true;
        });

        if (!$giphyEnabled) {
            return null;
        }

        return $this->apiKey;
    }

    /**
     * Get trending stickers from GIPHY.
     */
    public function trending(Request $request)
    {
        $limit = $request->get('limit', 20);
        $offset = $request->get('offset', 0);
        $apiKey = $this->getApiKey();

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Sticker sementara tidak tersedia.',
                'error' => ['code' => 'API_DISABLED', 'details' => 'Giphy integration is disabled or GIPHY_API_KEY is not set.']
            ], 200); // Return success true/false according to prompt, with status 200/500
        }

        $cacheKey = "stickers_trending_{$limit}_{$offset}";

        $data = Cache::remember($cacheKey, 300, function () use ($limit, $offset, $apiKey) {
            try {
                $response = Http::timeout(5)->withoutVerifying()->get("{$this->giphyUrl}/trending", [
                    'api_key' => $apiKey,
                    'limit' => $limit,
                    'offset' => $offset,
                    'rating' => 'g'
                ]);

                if ($response->successful()) {
                    return $response->json();
                }
            } catch (\Exception $e) {
                Log::error('GIPHY Trending API Error: ' . $e->getMessage());
            }
            return null;
        });

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Sticker sementara tidak tersedia. Silakan coba lagi.',
                'error' => ['code' => 'API_ERROR', 'details' => 'Failed to retrieve data from GIPHY.']
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Request berhasil',
            'data' => $data['data'] ?? [],
            'pagination' => $data['pagination'] ?? []
        ]);
    }

    /**
     * Search stickers from GIPHY.
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:1'
        ]);

        $q = $request->get('q');
        $limit = $request->get('limit', 20);
        $offset = $request->get('offset', 0);
        $apiKey = $this->getApiKey();

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Sticker sementara tidak tersedia.',
                'error' => ['code' => 'API_DISABLED', 'details' => 'Giphy integration is disabled.']
            ], 200);
        }

        $cacheKey = "stickers_search_" . md5($q) . "_{$limit}_{$offset}";

        $data = Cache::remember($cacheKey, 300, function () use ($q, $limit, $offset, $apiKey) {
            try {
                $response = Http::timeout(5)->withoutVerifying()->get("{$this->giphyUrl}/search", [
                    'api_key' => $apiKey,
                    'q' => $q,
                    'limit' => $limit,
                    'offset' => $offset,
                    'rating' => 'g',
                    'lang' => 'id'
                ]);

                if ($response->successful()) {
                    return $response->json();
                }
            } catch (\Exception $e) {
                Log::error('GIPHY Search API Error: ' . $e->getMessage());
            }
            return null;
        });

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Sticker sementara tidak tersedia. Silakan coba lagi.',
                'error' => ['code' => 'API_ERROR', 'details' => 'Failed to retrieve data from GIPHY.']
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Request berhasil',
            'data' => $data['data'] ?? [],
            'pagination' => $data['pagination'] ?? []
        ]);
    }

    /**
     * Get quick categories.
     */
    public function categories()
    {
        $categories = [
            ['name' => 'Fire', 'emoji' => '🔥', 'query' => 'fire'],
            ['name' => 'Funny', 'emoji' => '😂', 'query' => 'funny'],
            ['name' => 'Love', 'emoji' => '❤️', 'query' => 'love'],
            ['name' => 'Sad', 'emoji' => '😭', 'query' => 'sad'],
            ['name' => 'Angry', 'emoji' => '😡', 'query' => 'angry'],
            ['name' => 'Celebration', 'emoji' => '🎉', 'query' => 'celebration'],
            ['name' => 'Reaction', 'emoji' => '👍', 'query' => 'thumbs up'],
            ['name' => 'Hello', 'emoji' => '👋', 'query' => 'hello'],
            ['name' => 'EMS', 'emoji' => '🏥', 'query' => 'ambulance medical doctor'],
            ['name' => 'Meme', 'emoji' => '🤣', 'query' => 'meme']
        ];

        return response()->json([
            'success' => true,
            'message' => 'Request berhasil',
            'data' => $categories
        ]);
    }

    /**
     * Get sticker by GIPHY ID.
     */
    public function show($id)
    {
        $apiKey = $this->getApiKey();
        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Sticker sementara tidak tersedia.',
                'error' => ['code' => 'API_DISABLED', 'details' => 'Giphy integration is disabled.']
            ], 200);
        }

        $cacheKey = "sticker_show_{$id}";

        $data = Cache::remember($cacheKey, 86400, function () use ($id, $apiKey) {
            try {
                $response = Http::timeout(5)->withoutVerifying()->get("https://api.giphy.com/v1/gifs/{$id}", [
                    'api_key' => $apiKey
                ]);

                if ($response->successful()) {
                    return $response->json();
                }
            } catch (\Exception $e) {
                Log::error('GIPHY Show API Error: ' . $e->getMessage());
            }
            return null;
        });

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Sticker tidak ditemukan.',
                'error' => ['code' => 'NOT_FOUND', 'details' => 'Failed to find sticker with ID ' . $id]
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Request berhasil',
            'data' => $data['data'] ?? []
        ]);
    }

    /**
     * Get list of custom sticker packs.
     */
    public function packs()
    {
        $packs = StickerPack::where('is_active', true)
            ->with(['stickers' => function ($q) {
                $q->where('is_active', true)->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Request berhasil',
            'data' => $packs
        ]);
    }

    /**
     * Toggle a sticker as favorite.
     */
    public function toggleFavorite(Request $request)
    {
        $request->validate([
            'source' => 'required|string',
            'sticker_id' => 'required|string',
            'sticker_url' => 'required|string'
        ]);

        $userId = Auth::id();
        $source = $request->get('source');
        $stickerId = $request->get('sticker_id');
        $stickerUrl = $request->get('sticker_url');

        $fav = UserStickerFavorite::where('user_id', $userId)
            ->where('source', $source)
            ->where('sticker_id', $stickerId)
            ->first();

        if ($fav) {
            $fav->delete();
            return response()->json([
                'success' => true,
                'message' => 'Sticker dihapus dari favorit',
                'data' => ['is_favorite' => false]
            ]);
        } else {
            UserStickerFavorite::create([
                'user_id' => $userId,
                'source' => $source,
                'sticker_id' => $stickerId,
                'sticker_url' => $stickerUrl
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Sticker ditambahkan ke favorit',
                'data' => ['is_favorite' => true]
            ]);
        }
    }

    /**
     * Get user's favorite stickers.
     */
    public function getFavorites()
    {
        $favorites = UserStickerFavorite::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Request berhasil',
            'data' => $favorites
        ]);
    }

    /**
     * Get user's recently used stickers.
     */
    public function getRecents()
    {
        $recents = UserRecentSticker::where('user_id', Auth::id())
            ->orderBy('used_at', 'desc')
            ->limit(30)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Request berhasil',
            'data' => $recents
        ]);
    }
}
