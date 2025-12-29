<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\StaffRole;
use App\Models\UserRenameBatch;
use App\Models\UserRenameLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BatchRenameUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:batch-rename 
                            {--file= : Path to CSV/JSON file with old_name,new_name mapping}
                            {--batch-name= : Name for this batch operation}
                            {--similarity-threshold=85 : Minimum similarity score (0-100) to auto-match names}
                            {--dry-run : Show what would be renamed without actually renaming}
                            {--force : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Batch rename users based on similarity matching with mapping data';

    protected float $similarityThreshold = 85;
    protected array $mappingData = []; // Format: ['old_name' => ['new_name' => '...', 'role_name' => '...']]
    protected bool $dryRun = false;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting Batch User Rename Process...');
        $this->newLine();

        // Parse options
        $this->similarityThreshold = (float) $this->option('similarity-threshold');
        $this->dryRun = $this->option('dry-run');
        
        // Load mapping data from file
        $filePath = $this->option('file');
        if (!$filePath) {
            $this->error('❌ File path is required. Use --file option.');
            return 1;
        }

        if (!file_exists($filePath)) {
            $this->error("❌ File not found: {$filePath}");
            return 1;
        }

        // Load mapping data
        if (!$this->loadMappingData($filePath)) {
            return 1;
        }

        $this->info("📋 Loaded " . count($this->mappingData) . " name mappings");
        $this->newLine();

        // Find matches for all users
        $matches = $this->findMatches();
        $this->info("🔍 Found " . count($matches) . " potential matches");
        $this->newLine();

        // Show preview
        $this->displayPreview($matches);

        // Confirm before proceeding
        if (!$this->dryRun && !$this->option('force')) {
            if (!$this->confirm('Do you want to proceed with the rename?', true)) {
                $this->info('❌ Operation cancelled.');
                return 0;
            }
        }

        // Create batch record
        $batch = $this->createBatch($matches);

        // Display additional info: duplicates and unmapped names (always shown)
        $this->displayAdditionalInfo($matches);

        if ($this->dryRun) {
            $this->info('✅ Dry-run completed. No changes were made.');
            return 0;
        }

        // Process renames
        $result = $this->processRenames($batch, $matches);

        // Display summary
        $this->displaySummary($batch, $result);

        return 0;
    }

    /**
     * Load mapping data from CSV or JSON file
     */
    protected function loadMappingData(string $filePath): bool
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        try {
            if ($extension === 'csv') {
                return $this->loadCsvFile($filePath);
            } elseif ($extension === 'json') {
                return $this->loadJsonFile($filePath);
            } else {
                $this->error("❌ Unsupported file format. Please use CSV or JSON.");
                return false;
            }
        } catch (\Exception $e) {
            $this->error("❌ Error loading file: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Load mapping from CSV file
     * Expected format: old_name,new_name,role_name (header row optional)
     * Format 2 kolom juga didukung: old_name,new_name (role_name akan di-parse dari jabatan di new_name)
     */
    protected function loadCsvFile(string $filePath): bool
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            $this->error("❌ Cannot open CSV file: {$filePath}");
            return false;
        }

        $isFirstRow = true;
        $hasHeader = false;
        
        while (($row = fgetcsv($handle)) !== false) {
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            // Skip header row if present
            if ($isFirstRow && strtolower(trim($row[0])) === 'old_name') {
                $hasHeader = true;
                $isFirstRow = false;
                continue;
            }

            if (count($row) >= 2) {
                $oldName = trim($row[0]);
                $newNameFull = trim($row[1]);
                
                // Parse nama dan role dari format "Nama - Jabatan (Dept)" atau "Nama - Role"
                $parsed = $this->parseNameAndRole($newNameFull);
                $newName = $parsed['name'];
                $roleName = $parsed['role_name'];
                
                // Jika ada kolom ketiga, gunakan sebagai role_name
                if (count($row) >= 3 && !empty(trim($row[2]))) {
                    $roleName = trim($row[2]);
                }
                
                if (!empty($oldName) && !empty($newName)) {
                    $this->mappingData[$oldName] = [
                        'new_name' => $newName,
                        'role_name' => $roleName,
                    ];
                }
            }

            $isFirstRow = false;
        }

        fclose($handle);
        return true;
    }

    /**
     * Parse nama dan role dari string yang mengandung jabatan
     * Format: "Nama - Dokter Spesialis (Dept)" -> ['name' => 'Nama', 'role_name' => 'dokter_spesialis']
     */
    protected function parseNameAndRole(string $fullString): array
    {
        // Default: hanya nama, tidak ada role
        $result = [
            'name' => $fullString,
            'role_name' => null,
        ];

        // Pattern: "Nama - Jabatan (Dept)" atau "Nama - Jabatan"
        if (preg_match('/^(.+?)\s*-\s*(.+?)(?:\s*\(.+?\))?$/i', $fullString, $matches)) {
            $result['name'] = trim($matches[1]);
            $jabatan = trim($matches[2]);
            $result['role_name'] = $this->mapJabatanToRoleName($jabatan);
        } else {
            // Tidak ada format "-", berarti hanya nama
            $result['name'] = trim($fullString);
        }

        return $result;
    }

    /**
     * Map jabatan/deskripsi ke role name
     */
    protected function mapJabatanToRoleName(string $jabatan): ?string
    {
        $jabatanLower = mb_strtolower(trim($jabatan));
        
        // Mapping dari berbagai format jabatan ke role name
        $mapping = [
            'dokter spesialis' => 'dokter_spesialis',
            'dokter umum' => 'dokter_umum',
            'co-ass' => 'co_ass',
            'co ass' => 'co_ass',
            'perawat' => 'perawat',
            'trainee' => 'trainee',
            'paramedic' => 'perawat', // Kompatibilitas
        ];

        // Check exact match
        if (isset($mapping[$jabatanLower])) {
            return $mapping[$jabatanLower];
        }

        // Check partial match
        foreach ($mapping as $key => $roleName) {
            if (str_contains($jabatanLower, $key)) {
                return $roleName;
            }
        }

        return null; // Tidak ditemukan mapping
    }

    /**
     * Load mapping from JSON file
     * Expected format: {"old_name": "new_name", ...} or [{"old_name": "...", "new_name": "..."}, ...]
     */
    protected function loadJsonFile(string $filePath): bool
    {
        $content = file_get_contents($filePath);
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error("❌ Invalid JSON: " . json_last_error_msg());
            return false;
        }

        // Handle object format: {"old_name": "new_name"}
        if (isset($data[0]) && is_array($data[0])) {
            // Array format: [{"old_name": "...", "new_name": "..."}]
            foreach ($data as $item) {
                if (isset($item['old_name']) && isset($item['new_name'])) {
                    $this->mappingData[$item['old_name']] = $item['new_name'];
                }
            }
        } else {
            // Object format: {"old_name": "new_name"}
            foreach ($data as $oldName => $newName) {
                if (is_string($newName)) {
                    $this->mappingData[$oldName] = $newName;
                }
            }
        }

        return true;
    }

    /**
     * Find matches for users using similarity matching
     */
    protected function findMatches(): array
    {
        $users = User::all();
        $matches = [];

        foreach ($users as $user) {
            $bestMatch = $this->findBestMatch($user->name);
            
            if ($bestMatch) {
                $matches[] = [
                    'user' => $user,
                    'old_name' => $user->name,
                    'new_name' => $bestMatch['new_name'],
                    'role_name' => $bestMatch['role_name'] ?? null,
                    'similarity' => $bestMatch['similarity'],
                    'match_type' => $bestMatch['match_type'],
                ];
            }
        }

        return $matches;
    }

    /**
     * Find the best matching name from mapping data
     */
    protected function findBestMatch(string $userName): ?array
    {
        $normalizedUserName = $this->normalizeName($userName);
        $bestMatch = null;
        $bestSimilarity = 0;

        foreach ($this->mappingData as $oldName => $mapping) {
            // Support both old format (string) and new format (array)
            if (is_string($mapping)) {
                $newName = $mapping;
                $roleName = null;
            } else {
                $newName = $mapping['new_name'] ?? $oldName;
                $roleName = $mapping['role_name'] ?? null;
            }

            $normalizedOldName = $this->normalizeName($oldName);
            
            // Exact match
            if ($normalizedUserName === $normalizedOldName || $userName === $oldName) {
                return [
                    'new_name' => $newName,
                    'role_name' => $roleName,
                    'similarity' => 100,
                    'match_type' => 'exact',
                ];
            }

            // Calculate similarity
            $similarity = $this->calculateSimilarity($normalizedUserName, $normalizedOldName);
            
            if ($similarity > $bestSimilarity && $similarity >= $this->similarityThreshold) {
                $bestSimilarity = $similarity;
                $bestMatch = [
                    'new_name' => $newName,
                    'role_name' => $roleName,
                    'similarity' => $similarity,
                    'match_type' => 'similar',
                ];
            }
        }

        return $bestMatch;
    }

    /**
     * Normalize name for comparison
     */
    protected function normalizeName(string $name): string
    {
        // Convert to lowercase, trim, remove extra spaces
        $name = mb_strtolower(trim($name));
        $name = preg_replace('/\s+/', ' ', $name);
        
        // Remove common prefixes/suffixes if needed
        $name = preg_replace('/^(dr\.?|dr\s)/i', '', $name);
        $name = preg_replace('/\s+$/', '', $name);
        
        return $name;
    }

    /**
     * Format name dengan proper case (title case)
     * Menangani huruf besar kecil dengan benar
     * Contoh: "CARISSA BLANCHE" -> "Carissa Blanche", "john doe" -> "John Doe"
     */
    protected function formatNameProperCase(string $name): string
    {
        // Trim dan normalize spaces
        $name = trim($name);
        $name = preg_replace('/\s+/', ' ', $name);
        
        // Split nama menjadi kata-kata
        $words = explode(' ', $name);
        $formattedWords = [];
        
        // Handle special prefixes and suffixes
        $prefixes = ['dr', 'dr.', 'dr.', 'mr', 'mr.', 'mrs', 'mrs.', 'ms', 'ms.'];
        $suffixes = ['jr', 'jr.', 'sr', 'sr.', 'ii', 'iii', 'iv'];
        $specialConnectors = ['de', 'da', 'di', 'del', 'della', 'van', 'von', 'der', 'den', 'le', 'la', 'el'];
        
        foreach ($words as $index => $word) {
            $wordLower = mb_strtolower($word);
            $wordCleaned = preg_replace('/[^a-zA-Z]/', '', $wordLower);
            
            // Handle prefixes (usually at start)
            if ($index === 0 && in_array($wordCleaned, $prefixes)) {
                $formattedWords[] = mb_strtolower($word) . (str_ends_with($word, '.') ? '' : '.');
                continue;
            }
            
            // Handle suffixes (usually at end)
            if ($index === count($words) - 1 && in_array($wordCleaned, $suffixes)) {
                $formattedWords[] = ucfirst(mb_strtolower($word));
                continue;
            }
            
            // Handle special connectors (usually in middle)
            if ($index > 0 && $index < count($words) - 1 && in_array($wordCleaned, $specialConnectors)) {
                $formattedWords[] = mb_strtolower($word);
                continue;
            }
            
            // Handle initials (single letter with optional dot)
            if (preg_match('/^[A-Za-z]\.?$/', $word)) {
                $formattedWords[] = mb_strtoupper($word[0]) . (str_contains($word, '.') ? '.' : '');
                continue;
            }
            
            // Handle words with hyphens (Mc-, O', etc.)
            if (str_contains($word, '-')) {
                $parts = explode('-', $word);
                $formattedParts = [];
                foreach ($parts as $part) {
                    if (preg_match('/^(mc|mac)(.+)$/i', mb_strtolower($part), $matches)) {
                        // Mc- or Mac- prefix: McCartney -> McCartney (capitalize Mc and first letter of next part)
                        $prefix = ucfirst($matches[1]);
                        $suffix = ucfirst(mb_strtolower($matches[2]));
                        $formattedParts[] = $prefix . $suffix;
                    } else {
                        $formattedParts[] = ucfirst(mb_strtolower($part));
                    }
                }
                $formattedWords[] = implode('-', $formattedParts);
                continue;
            }
            
            // Handle Mc- prefix without hyphen (McCartney, McDonald, etc.)
            if (preg_match('/^(mc|mac)(.+)$/i', mb_strtolower($word), $matches)) {
                $prefix = ucfirst($matches[1]);
                $suffix = ucfirst(mb_strtolower($matches[2]));
                $formattedWords[] = $prefix . $suffix;
                continue;
            }
            
            // Handle words with apostrophes (O'Brien, D'Angelo, etc.)
            if (str_contains($word, "'")) {
                $parts = explode("'", $word);
                $formattedParts = [];
                foreach ($parts as $i => $part) {
                    if ($i === 0) {
                        $formattedParts[] = ucfirst(mb_strtolower($part));
                    } else {
                        $formattedParts[] = ucfirst(mb_strtolower($part));
                    }
                }
                $formattedWords[] = implode("'", $formattedParts);
                continue;
            }
            
            // Default: capitalize first letter, lowercase rest
            $formattedWords[] = ucfirst(mb_strtolower($word));
        }
        
        return implode(' ', $formattedWords);
    }

    /**
     * Calculate similarity between two strings using multiple algorithms
     */
    protected function calculateSimilarity(string $str1, string $str2): float
    {
        // Use similar_text for better accuracy
        similar_text($str1, $str2, $similarity);
        
        // Also consider Levenshtein distance
        $maxLength = max(strlen($str1), strlen($str2));
        if ($maxLength > 0) {
            $levenshteinDistance = levenshtein($str1, $str2);
            $levenshteinSimilarity = (1 - ($levenshteinDistance / $maxLength)) * 100;
            
            // Combine both scores (weighted average)
            $combined = ($similarity * 0.6) + ($levenshteinSimilarity * 0.4);
            return round($combined, 2);
        }

        return round($similarity, 2);
    }

    /**
     * Display preview of matches
     */
    protected function displayPreview(array $matches): void
    {
        $this->info('📊 Preview of matches:');
        $this->newLine();

        $headers = ['ID', 'Current Name', 'New Name', 'Role', 'Similarity', 'Match Type'];
        $rows = [];

        foreach ($matches as $match) {
            $formattedNewName = $this->formatNameProperCase($match['new_name']);
            $rows[] = [
                $match['user']->id,
                $match['old_name'],
                $formattedNewName,
                $match['role_name'] ?? ($match['user']->role?->display_name ?? '-'),
                $match['similarity'] . '%',
                $match['match_type'],
            ];
        }

        $this->table($headers, $rows);
        $this->newLine();
    }

    /**
     * Create batch record
     */
    protected function createBatch(array $matches): UserRenameBatch
    {
        $mappingData = [];
        foreach ($matches as $match) {
            $mappingData[$match['old_name']] = [
                'new_name' => $match['new_name'],
                'role_name' => $match['role_name'] ?? null,
            ];
        }

        return UserRenameBatch::create([
            'batch_name' => $this->option('batch-name') ?? 'Batch Rename ' . now()->format('Y-m-d H:i:s'),
            'description' => 'Batch rename operation with ' . count($matches) . ' users',
            'total_users' => count($matches),
            'status' => $this->dryRun ? 'pending' : 'processing',
            'mapping_data' => $mappingData,
        ]);
    }

    /**
     * Process all renames
     */
    protected function processRenames(UserRenameBatch $batch, array $matches): array
    {
        $this->info('🔄 Processing renames...');
        $this->newLine();

        $successful = 0;
        $failed = 0;
        $progressBar = $this->output->createProgressBar(count($matches));
        $progressBar->start();

        DB::beginTransaction();

        try {
            foreach ($matches as $match) {
                $log = UserRenameLog::create([
                    'batch_id' => $batch->id,
                    'user_id' => $match['user']->id,
                    'old_name' => $match['old_name'],
                    'new_name' => $match['new_name'],
                    'similarity_score' => $match['similarity'],
                    'match_type' => $match['match_type'],
                    'status' => 'pending',
                ]);

                try {
                    // Format name dengan proper case (title case)
                    $formattedName = $this->formatNameProperCase($match['new_name']);
                    
                    // Update user name
                    $match['user']->name = $formattedName;
                    
                    // Update role_id if role_name is provided
                    if (!empty($match['role_name'])) {
                        $role = StaffRole::where('name', $match['role_name'])->first();
                        if ($role) {
                            $match['user']->role_id = $role->id;
                        } else {
                            $this->warn("⚠️  Role '{$match['role_name']}' not found for user {$match['user']->id} ({$match['old_name']})");
                        }
                    }
                    
                    $match['user']->save();

                    // Update log
                    $log->status = 'success';
                    $log->renamed_at = now();
                    $log->save();

                    $successful++;
                } catch (\Exception $e) {
                    $log->status = 'failed';
                    $log->error_message = $e->getMessage();
                    $log->save();

                    $failed++;
                }

                $progressBar->advance();
            }

            // Update batch status
            $batch->successful_renames = $successful;
            $batch->failed_renames = $failed;
            $batch->status = ($failed === 0) ? 'completed' : 'completed_with_errors';
            $batch->processed_at = now();
            $batch->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $batch->status = 'failed';
            $batch->save();
            $this->error("❌ Error processing batch: " . $e->getMessage());
        }

        $progressBar->finish();
        $this->newLine(2);

        return [
            'successful' => $successful,
            'failed' => $failed,
        ];
    }

    /**
     * Display summary
     */
    protected function displaySummary(UserRenameBatch $batch, array $result): void
    {
        $this->info('✅ Batch rename completed!');
        $this->newLine();

        $this->table(
            ['Metric', 'Value'],
            [
                ['Batch ID', $batch->id],
                ['Batch Name', $batch->batch_name],
                ['Total Users', $batch->total_users],
                ['Successful', $batch->successful_renames],
                ['Failed', $batch->failed_renames],
                ['Status', $batch->status],
                ['Processed At', $batch->processed_at?->format('Y-m-d H:i:s')],
            ]
        );

        if ($result['failed'] > 0) {
            $this->warn("⚠️  {$result['failed']} renames failed. Check the logs for details.");
        }
    }

    /**
     * Display additional info: duplicate names and unmapped names
     */
    protected function displayAdditionalInfo(array $matches): void
    {
        $this->newLine();
        $this->info('📊 Additional Information:');
        $this->newLine();

        // 1. Check for duplicate new_names (multiple users akan di-rename ke nama yang sama)
        $duplicates = $this->findDuplicateNames($matches);
        if (!empty($duplicates)) {
            $this->warn('⚠️  DUPLICATE NAMES (Multiple users akan di-rename ke nama yang sama):');
            $this->newLine();
            
            $headers = ['New Name', 'Count', 'Users (Old Names)'];
            $rows = [];
            
            foreach ($duplicates as $newName => $users) {
                $rows[] = [
                    $newName,
                    count($users),
                    implode(', ', array_slice($users, 0, 5)) . (count($users) > 5 ? '... (+' . (count($users) - 5) . ' more)' : ''),
                ];
            }
            
            $this->table($headers, $rows);
            $this->newLine();
        }

        // 2. Check for unmapped names (ada di mapping file tapi tidak ada di database)
        $unmapped = $this->findUnmappedNames($matches);
        if (!empty($unmapped)) {
            $this->warn('⚠️  UNMAPPED NAMES (Ada di mapping file tapi tidak ada user di database):');
            $this->newLine();
            
            $headers = ['Old Name', 'Expected New Name', 'Role'];
            $rows = [];
            
            foreach ($unmapped as $oldName => $data) {
                $rows[] = [
                    $oldName,
                    $data['new_name'],
                    $data['role_name'] ?? '-',
                ];
            }
            
            $this->table($headers, $rows);
            $this->newLine();
        }

        // Summary
        if (empty($duplicates) && empty($unmapped)) {
            $this->info('✅ Tidak ada nama yang kembar dan semua nama di mapping file sudah punya akun.');
        }
    }

    /**
     * Find duplicate new_names in matches
     */
    protected function findDuplicateNames(array $matches): array
    {
        $nameGroups = [];
        
        foreach ($matches as $match) {
            $newName = $match['new_name'];
            if (!isset($nameGroups[$newName])) {
                $nameGroups[$newName] = [];
            }
            $nameGroups[$newName][] = $match['old_name'];
        }

        // Filter hanya yang punya lebih dari 1 user
        return array_filter($nameGroups, function($users) {
            return count($users) > 1;
        });
    }

    /**
     * Find unmapped names (ada di mapping file tapi tidak ada di database)
     */
    protected function findUnmappedNames(array $matches): array
    {
        // Collect all user IDs that were matched
        $matchedUserIds = [];
        foreach ($matches as $match) {
            $matchedUserIds[] = $match['user']->id;
        }

        // Get all users from database
        $allUsers = User::all();

        // Find names in mapping data that weren't matched to any user
        $unmapped = [];
        
        foreach ($this->mappingData as $oldName => $mapping) {
            $wasMatched = false;
            
            // Check if any user in database matches this old_name from mapping
            foreach ($allUsers as $user) {
                $normalizedUserName = $this->normalizeName($user->name);
                $normalizedOldName = $this->normalizeName($oldName);
                
                // Check exact match first
                if ($normalizedUserName === $normalizedOldName || $user->name === $oldName) {
                    $wasMatched = true;
                    break;
                }
                
                // Check similarity match
                $similarity = $this->calculateSimilarity($normalizedUserName, $normalizedOldName);
                if ($similarity >= $this->similarityThreshold) {
                    $wasMatched = true;
                    break;
                }
            }
            
            // If not matched, add to unmapped list
            if (!$wasMatched) {
                // Parse new_name and role_name
                $newNameFull = is_array($mapping) ? ($mapping['new_name'] ?? $oldName) : $mapping;
                $parsed = $this->parseNameAndRole($newNameFull);
                $newName = $parsed['name'];
                $roleName = is_array($mapping) ? ($mapping['role_name'] ?? $parsed['role_name']) : $parsed['role_name'];
                
                $unmapped[$oldName] = [
                    'new_name' => $newName,
                    'role_name' => $roleName,
                ];
            }
        }

        return $unmapped;
    }
}
