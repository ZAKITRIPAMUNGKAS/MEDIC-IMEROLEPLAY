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
     * 
     * @param string $userName  The name from our database (e.g. "Aiko Fukushima")
     * @param array $players  The player objects from FiveM
     * @return bool
     */
    public function isPlayerOnlineByName(string $userName, array $players): bool
    {
        if (empty($userName)) return false;

        $normalizedDbName = strtolower(trim($userName));

        foreach ($players as $player) {
            if (isset($player['name'])) {
                $onlineNameLower = strtolower($player['name']);

                // 1. Exact Match
                if ($onlineNameLower === $normalizedDbName) return true;

                // 2. Contains (Database name is part of FiveM name)
                // e.g. "Aiko Fukushima" inside "MEDIC - Aiko Fukushima #863"
                if (str_contains($onlineNameLower, $normalizedDbName)) return true;

                // 3. Reversed contains (FiveM name is part of Database name - less common)
                // e.g. "Aiko" inside "Aiko Fukushima"
                // Hanya jika nama cukup unik (> 4 karakter)
                if (strlen($onlineNameLower) > 4 && str_contains($normalizedDbName, $onlineNameLower)) return true;
                
                // 4. Handle "Character Name" inside square brackets if used [Aiko]
                // This is a common roleplay convention.
            }
        }

        return false;
    }
}
