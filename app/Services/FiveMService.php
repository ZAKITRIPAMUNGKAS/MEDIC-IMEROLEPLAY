<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FiveMService
{
    protected $ip;
    protected $port;

    public function __construct()
    {
        $this->ip = config('services.fivem.ip');
        $this->port = config('services.fivem.port', '30120');
    }

    /**
     * Get all online player data from FiveM server
     * 
     * @return array
     */
    public function getOnlinePlayersData(): array
    {
        if (empty($this->ip)) {
            Log::debug('[FiveMService] Server IP not configured, skipping status recheck.');
            return [];
        }

        $url = "http://{$this->ip}:{$this->port}/players.json";

        try {
            $response = Http::timeout(5)->get($url);

            if ($response->successful()) {
                return $response->json() ?? [];
            }

            Log::warning("[FiveMService] Failed to fetch players from FiveM: HTTP {$response->status()}");
        } catch (\Exception $e) {
            Log::warning("[FiveMService] Error connecting to FiveM server ({$url}): " . $e->getMessage());
        }

        return [];
    }

    /**
     * Check if a specific player ID is online (Identifier based)
     * 
     * @param string $playerId  The ID to check (citizen_id or staff_id)
     * @param array $players  The player objects from FiveM
     * @return bool
     */
    public function isPlayerOnlineByIdentifier(string $playerId, array $players): bool
    {
        $normalizedId = strtolower(trim($playerId));
        
        foreach ($players as $player) {
            if (isset($player['identifiers']) && is_array($player['identifiers'])) {
                foreach ($player['identifiers'] as $onlineId) {
                    $onlineIdLower = strtolower($onlineId);
                    if ($onlineIdLower === $normalizedId || str_contains($onlineIdLower, ":$normalizedId")) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Check if a specific player is online (Name based fallback)
     * Supports matching names with prefixes like "MEDIC - Aiko"
     * This uses Smart Matching (Multi-Keyword AND) to handle common surnames Safely.
     * 
     * @param string $userName  The name from our database (e.g. "Aiko Fukushima")
     * @param array $players  The player objects from FiveM
     * @return bool
     */
    public function isPlayerOnlineByName(string $userName, array $players): bool
    {
        if (empty($userName)) return false;

        // 1. Clean and normalize the DB name
        // e.g. "Aiko Fukushima" -> ["aiko", "fukushima"]
        $dbNameParts = $this->getCleanKeywords($userName);
        if (empty($dbNameParts)) return false;

        foreach ($players as $player) {
            if (isset($player['name'])) {
                // 2. Clean the online name (remove prefixes like [MEDIC], RH -, etc.)
                $onlineNameLower = strtolower($player['name']);
                
                // 3. Multi-Keyword Matching (Must match most significant parts of the name)
                // Logic: A player is online if THE ONLINE NAME contains ALL words from the DB Name.
                // This ensures "Aiko Fukushima" doesn't match "Aiko Sato".
                $allMatched = true;
                foreach ($dbNameParts as $keyword) {
                    if (!str_contains($onlineNameLower, $keyword)) {
                        $allMatched = false;
                        break;
                    }
                }

                if ($allMatched) return true;

                // 4. Reverse Contains (Online name parts match DB name)
                // If game name is just "Aiko", it will match "Aiko Fukushima" only if Aiko is unique (>4 chars)
                $onlineNameParts = $this->getCleanKeywords($player['name']);
                $meaningfulMatch = 0;
                foreach($onlineNameParts as $part) {
                    if (strlen($part) > 2 && str_contains(strtolower($userName), $part)) {
                        $meaningfulMatch++;
                    }
                }
                
                // If online name is substantially part of DB name (e.g. "Aiko" is part of "Aiko Fukushima")
                // We use a safe threshold: Online name must be substantial.
                if ($meaningfulMatch >= 1 && count($onlineNameParts) >= 1) {
                    // Logic check: only if DB name is very specific
                    if (strlen($player['name']) >= 4 && str_contains(strtolower($userName), strtolower($player['name']))) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Internal helper to clean up names and split into keywords
     */
    private function getCleanKeywords(string $name): array
    {
        // 1. Convert to lowercase
        $name = strtolower($name);

        // 2. Remove common RP prefixes/suffixes in brackets [MEDIC], (EMS), {ID}
        $name = preg_replace('/\[.*?\]|\(.*?\)|{.*?}/', '', $name);

        // 3. Remove common text prefixes
        $prefixes = ['medic - ', 'rh - ', 'ems - ', 'dr. ', 'dr ', 'nurse '];
        $name = str_replace($prefixes, '', $name);

        // 4. Remove special characters (keep spaces and alphanumeric)
        $name = preg_replace('/[^a-z0-9\s]/', ' ', $name);

        // 5. Split and filter empty/short words (keep words > 2 chars)
        $parts = explode(' ', $name);
        return array_values(array_filter($parts, function($val) {
            return strlen(trim($val)) >= 3;
        }));
    }
}
