<?php
/**
 * Auto-fix permission & folder structure untuk Laravel di cPanel Shared Hosting
 * Akses via browser: https://medicalcenterime.my.id/fix-permission.php
 */

header('Content-Type: text/html; charset=utf-8');

$baseDir = dirname(__DIR__); // Root folder project
$results = [];

$directories = [
    $baseDir . '/storage',
    $baseDir . '/storage/app',
    $baseDir . '/storage/app/public',
    $baseDir . '/storage/framework',
    $baseDir . '/storage/framework/cache',
    $baseDir . '/storage/framework/cache/data',
    $baseDir . '/storage/framework/sessions',
    $baseDir . '/storage/framework/views',
    $baseDir . '/storage/logs',
    $baseDir . '/bootstrap/cache',
];

// 1. Buat folder jika belum ada dan set chmod 0775 / 0777
foreach ($directories as $dir) {
    $rel = str_replace($baseDir . '/', '', $dir);
    if (!is_dir($dir)) {
        if (@mkdir($dir, 0777, true)) {
            $results[] = ["status" => "success", "msg" => "Folder <code>$rel</code> berhasil dibuat."];
        } else {
            $results[] = ["status" => "error", "msg" => "Gagal membuat folder <code>$rel</code>."];
        }
    } else {
        $results[] = ["status" => "info", "msg" => "Folder <code>$rel</code> sudah ada."];
    }

    // Set permission rekursif
    @chmod($dir, 0777);

    // Test write
    $testFile = $dir . '/test_write_' . time() . '.tmp';
    if (@file_put_contents($testFile, 'OK') !== false) {
        @unlink($testFile);
        $results[] = ["status" => "success", "msg" => "&nbsp;&nbsp;↳ Hak tulis (Writable) pada <code>$rel</code>: <b>OK</b>"];
    } else {
        $results[] = ["status" => "warning", "msg" => "&nbsp;&nbsp;↳ Hak tulis pada <code>$rel</code>: <b>GAGAL</b> (Coba ubah ke 777 di cPanel File Manager)"];
    }
}

// 2. Bersihkan file cache lama jika ada
$cacheFiles = glob($baseDir . '/bootstrap/cache/*.php');
if ($cacheFiles) {
    foreach ($cacheFiles as $cf) {
        if (basename($cf) !== '.gitignore') {
            @unlink($cf);
        }
    }
    $results[] = ["status" => "info", "msg" => "File bootstrap cache lama berhasil dibersihkan."];
}

$viewFiles = glob($baseDir . '/storage/framework/views/*.php');
if ($viewFiles) {
    foreach ($viewFiles as $vf) {
        @unlink($vf);
    }
    $results[] = ["status" => "info", "msg" => "File compiled views lama berhasil dibersihkan."];
}

// 3. Buat file log kosong jika belum ada
$logFile = $baseDir . '/storage/logs/laravel.log';
if (!file_exists($logFile)) {
    @file_put_contents($logFile, "");
    @chmod($logFile, 0666);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fix Storage & Cache Permission</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #0f172a; color: #f8fafc; padding: 2rem; }
        .container { max-width: 700px; margin: 0 auto; background: #1e293b; border-radius: 12px; padding: 2rem; box-shadow: 0 10px 25px rgba(0,0,0,0.3); border: 1px solid #334155; }
        h1 { font-size: 1.5rem; margin-bottom: 1rem; color: #38bdf8; display: flex; align-items: center; gap: 0.5rem; }
        ul { list-style: none; padding: 0; margin: 1.5rem 0; }
        li { padding: 0.5rem 0.75rem; border-radius: 6px; margin-bottom: 0.35rem; font-size: 0.95rem; }
        li.success { background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.2); }
        li.info { background: rgba(56, 189, 248, 0.1); color: #7dd3fc; }
        li.warning { background: rgba(234, 179, 8, 0.15); color: #facc15; }
        li.error { background: rgba(239, 68, 68, 0.15); color: #f87171; }
        code { background: #0f172a; padding: 2px 6px; border-radius: 4px; font-size: 0.85em; color: #f1f5f9; }
        .btn { display: inline-block; background: #0284c7; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; margin-top: 1rem; }
        .btn:hover { background: #0369a1; }
        .alert-del { margin-top: 1.5rem; padding: 0.75rem; background: rgba(239, 68, 68, 0.1); border: 1px dashed #ef4444; border-radius: 8px; font-size: 0.85rem; color: #fca5a5; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🛠️ Status Permission Folder Storage & Cache</h1>
        <p style="color: #94a3b8; font-size: 0.9rem;">Skrip ini memastikan seluruh subfolder <code>storage</code> dan <code>bootstrap/cache</code> memiliki hak akses tulis (Writable) untuk Laravel.</p>
        
        <ul>
            <?php foreach ($results as $r): ?>
                <li class="<?= $r['status'] ?>"><?= $r['msg'] ?></li>
            <?php endforeach; ?>
        </ul>

        <a href="/" class="btn">← Coba Buka Halaman Utama Website</a>

        <div class="alert-del">
            ⚠️ <b>Tips Keamanan:</b> Setelah website berjalan normal, hapus file <code>public/fix-permission.php</code> ini dari cPanel File Manager.
        </div>
    </div>
</body>
</html>
