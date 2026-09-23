<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class TrainingProgram extends Model
{
    protected $table = 'training_programs';

    protected $fillable = [
        'key',
        'title',
        'short_title',
        'organizer',
        'desc',
        'requirement',
        'badge',
        'badge_color',
        'icon',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'sort_order'  => 'integer',
    ];

    /**
     * Default program definitions
     */
    public static function defaultPrograms(): array
    {
        return [
            'operasi' => [
                'key'         => 'operasi',
                'title'       => 'FORMULIR PENDAFTARAN PELATIHAN OPERASI FASE XIII',
                'short_title' => 'Pelatihan Operasi',
                'organizer'   => 'Divisi PND (People & Development) - MOT',
                'desc'        => 'Pendaftaran Pelatihan Operasi dibuka pada 5–7 September 2026. Kegiatan pelatihan akan dilaksanakan pada 8 September 2026 oleh Department People & Development bagian MOT (Medical of Trainer) sebagai upaya meningkatkan pengetahuan dan keterampilan peserta terkait prosedur operasi dan keselamatan pasien.',
                'requirement' => 'Semua Staf Medis',
                'badge'       => 'PND - MOT',
                'badge_color' => 'emerald',
                'icon'        => 'fa-procedures',
                'is_active'   => true,
                'sort_order'  => 1,
            ],
            'surat-menyurat' => [
                'key'         => 'surat-menyurat',
                'title'       => 'Formulir Pendaftaran Surat Menyurat',
                'short_title' => 'Pelatihan Surat Menyurat',
                'organizer'   => 'Divisi PND (People & Development)',
                'desc'        => 'Pelatihan administrasi dan penulisan surat menyurat resmi IME Medical Center. Persyaratan minimal jabatan adalah Co-Ass.',
                'requirement' => 'Minimal Co-Ass',
                'badge'       => 'Min. Co-Ass',
                'badge_color' => 'blue',
                'icon'        => 'fa-envelope-open-text',
                'is_active'   => true,
                'sort_order'  => 2,
            ],
            'visum-hidup' => [
                'key'         => 'visum-hidup',
                'title'       => 'PENDAFTARAN PELATIHAN VISUM HIDUP',
                'short_title' => 'Pelatihan Visum Hidup',
                'organizer'   => 'MSL bersama People & Development Department',
                'desc'        => 'PELATIHAN VISUM HIDUP yang diselenggarakan oleh Medical Science & Laboratory bersama People & Development Department – IME Medical Center. Semua Dokter Umum WAJIB mengikuti dan opsional bagi dokter spesialis. Peserta akan mendapatkan sertifikat.',
                'requirement' => 'Wajib Dokter Umum / Opsional Spesialis',
                'badge'       => 'MSL & PND',
                'badge_color' => 'purple',
                'icon'        => 'fa-notes-medical',
                'is_active'   => true,
                'sort_order'  => 3,
            ],
            'rekam-medis' => [
                'key'         => 'rekam-medis',
                'title'       => 'PENDAFTARAN PELATIHAN REKAM MEDIS',
                'short_title' => 'Pelatihan Rekam Medis',
                'organizer'   => 'Medical Science & Laboratory (MSL) – IME Medical Center',
                'desc'        => 'Pelatihan Rekam Medis diselenggarakan oleh divisi Medical Science & Laboratory (MSL) IME Medical Center. Pelatihan ini mencakup tata cara pengisian rekam medis, pengarsipan data klinis pasien, dan standar dokumentasi medis sesuai prosedur rumah sakit.',
                'requirement' => 'Semua Staf Medis',
                'badge'       => 'MSL',
                'badge_color' => 'cyan',
                'icon'        => 'fa-file-medical-alt',
                'is_active'   => true,
                'sort_order'  => 4,
            ],
            'pemulsaran-jenazah' => [
                'key'         => 'pemulsaran-jenazah',
                'title'       => 'PENDAFTARAN PELATIHAN PEMULSARAN JENAZAH',
                'short_title' => 'Pelatihan Pemulsaran Jenazah',
                'organizer'   => 'People & Development Department – IME Medical Center',
                'desc'        => 'Pelatihan Pemulsaran Jenazah diselenggarakan oleh Divisi People & Development IME Medical Center. Peserta akan mempelajari prosedur penanganan jenazah secara profesional sesuai dengan standar medis dan etika yang berlaku.',
                'requirement' => 'Semua Staf Medis',
                'badge'       => 'PND',
                'badge_color' => 'amber',
                'icon'        => 'fa-ribbon',
                'is_active'   => true,
                'sort_order'  => 5,
            ],
        ];
    }

    protected static function getJsonStoragePath(): string
    {
        return storage_path('app/training_programs.json');
    }

    /**
     * Get all training programs from DB or JSON fallback
     */
    public static function getAllPrograms(): array
    {
        $defaults = self::defaultPrograms();

        // 1. Try DB if table exists and accessible
        try {
            if (Schema::hasTable('training_programs')) {
                $dbRecords = self::orderBy('sort_order')->get()->keyBy('key');
                if ($dbRecords->isNotEmpty()) {
                    $merged = [];
                    foreach ($defaults as $k => $def) {
                        if ($dbRecords->has($k)) {
                            $rec = $dbRecords->get($k);
                            $def['title']       = $rec->title ?? $def['title'];
                            $def['short_title'] = $rec->short_title ?? $def['short_title'];
                            $def['organizer']   = $rec->organizer ?? $def['organizer'];
                            $def['desc']        = $rec->desc ?? $def['desc'];
                            $def['requirement'] = $rec->requirement ?? ($def['requirement'] ?? null);
                            $def['badge']       = $rec->badge ?? $def['badge'];
                            $def['badge_color'] = $rec->badge_color ?? $def['badge_color'];
                            $def['icon']        = $rec->icon ?? $def['icon'];
                            $def['is_active']   = (bool) $rec->is_active;
                        }
                        $def['route'] = route('portal.training.form', $k);
                        $merged[$k] = $def;
                    }
                    return array_values($merged);
                }
            }
        } catch (\Throwable $e) {
            // Fallback to JSON
        }

        // 2. Try JSON storage
        $jsonPath = self::getJsonStoragePath();
        if (File::exists($jsonPath)) {
            try {
                $json = json_decode(File::get($jsonPath), true);
                if (is_array($json)) {
                    foreach ($json as $k => $item) {
                        if (isset($defaults[$k])) {
                            $defaults[$k] = array_merge($defaults[$k], $item);
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Ignore JSON error and keep defaults
            }
        }

        foreach ($defaults as $k => &$def) {
            $def['route'] = route('portal.training.form', $k);
        }

        return array_values($defaults);
    }

    /**
     * Save / Update a program schedule by key
     */
    public static function saveProgram(string $key, array $data): bool
    {
        $defaults = self::defaultPrograms();
        if (!isset($defaults[$key])) {
            return false;
        }

        // Clean values
        $updateData = [
            'key'         => $key,
            'title'       => trim($data['title'] ?? $defaults[$key]['title']),
            'short_title' => trim($data['short_title'] ?? $defaults[$key]['short_title']),
            'organizer'   => trim($data['organizer'] ?? $defaults[$key]['organizer']),
            'desc'        => trim($data['desc'] ?? $defaults[$key]['desc']),
            'requirement' => trim($data['requirement'] ?? ($defaults[$key]['requirement'] ?? '')),
            'badge'       => trim($data['badge'] ?? $defaults[$key]['badge']),
            'badge_color' => trim($data['badge_color'] ?? $defaults[$key]['badge_color']),
            'icon'        => trim($data['icon'] ?? $defaults[$key]['icon']),
            'is_active'   => isset($data['is_active']) ? (bool) $data['is_active'] : true,
            'sort_order'  => (int) ($data['sort_order'] ?? $defaults[$key]['sort_order']),
        ];

        // 1. Try save to Database if table exists
        try {
            if (Schema::hasTable('training_programs')) {
                self::updateOrCreate(['key' => $key], $updateData);
            }
        } catch (\Throwable $e) {
            // Continue to JSON fallback
        }

        // 2. Always persist to JSON storage as well for guaranteed persistence
        try {
            $jsonPath = self::getJsonStoragePath();
            $currentData = [];
            if (File::exists($jsonPath)) {
                $currentData = json_decode(File::get($jsonPath), true) ?: [];
            }
            $currentData[$key] = $updateData;

            if (!File::isDirectory(dirname($jsonPath))) {
                File::makeDirectory(dirname($jsonPath), 0755, true);
            }
            File::put($jsonPath, json_encode($currentData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } catch (\Throwable $e) {
            // Failed writing file
        }

        return true;
    }
}
