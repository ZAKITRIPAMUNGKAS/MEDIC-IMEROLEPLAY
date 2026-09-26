<?php
/**
 * Image Optimizer Script - MEDIC IME ROLEPLAY
 * Run once on server to compress large images using PHP GD
 * Usage: php optimize_images.php  OR  visit /optimize_images.php (then DELETE after use)
 * 
 * SECURITY: This script has no auth - DELETE IT after running!
 */

if (php_sapi_name() !== 'cli') {
    // Basic key protection when accessed via browser
    $key = $_GET['key'] ?? '';
    if ($key !== 'ime_optimize_2024') {
        die('Access denied. Add ?key=ime_optimize_2024 to URL');
    }
}

set_time_limit(300); // 5 minutes
ini_set('memory_limit', '256M');

$imageDir = __DIR__ . '/public/images/';
$backupDir = __DIR__ . '/public/images/originals_backup/';

if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
}

$targets = [
    // [filename, max_width, quality_jpg, quality_webp]
    ['REGULASI_IME_MEDICAL_CENTER.jpg', 1200, 75, 80],
    ['logo_ems.webp', 400, 85, 80],
    ['logoime.webp', 400, 85, 80],
    ['gambar 2.png', 1200, 85, 80],
    ['hero.png', 1400, 85, 80],
    ['foto dokter.png', 600, 85, 80],
    ['foto_dokter.jpg', 600, 75, 80],
    ['motionlife-logo.png', 400, 85, 80],
    ['ilham.png', 400, 85, 80],
];

$results = [];

foreach ($targets as [$file, $maxWidth, $qualJpg, $qualWebp]) {
    $filepath = $imageDir . $file;
    
    if (!file_exists($filepath)) {
        $results[] = "SKIP (not found): $file";
        continue;
    }
    
    $originalSize = filesize($filepath);
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    
    // Backup original
    copy($filepath, $backupDir . $file);
    
    // Get image dimensions
    $info = getimagesize($filepath);
    if (!$info) {
        $results[] = "SKIP (not image): $file";
        continue;
    }
    [$origWidth, $origHeight] = $info;
    
    // Calculate new dimensions
    if ($origWidth > $maxWidth) {
        $newWidth = $maxWidth;
        $newHeight = (int)($origHeight * ($maxWidth / $origWidth));
    } else {
        $newWidth = $origWidth;
        $newHeight = $origHeight;
    }
    
    // Load source image
    $src = null;
    switch ($info[2]) {
        case IMAGETYPE_JPEG: $src = imagecreatefromjpeg($filepath); break;
        case IMAGETYPE_PNG:  $src = imagecreatefrompng($filepath); break;
        case IMAGETYPE_GIF:  $src = imagecreatefromgif($filepath); break;
        case IMAGETYPE_WEBP: $src = imagecreatefromwebp($filepath); break;
        default:
            $results[] = "SKIP (unsupported type): $file";
            continue 2;
    }
    
    if (!$src) {
        $results[] = "ERROR (load failed): $file";
        continue;
    }
    
    // Create resized image
    $dst = imagecreatetruecolor($newWidth, $newHeight);
    
    // Preserve transparency for PNG
    if ($info[2] === IMAGETYPE_PNG) {
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
        imagefilledrectangle($dst, 0, 0, $newWidth, $newHeight, $transparent);
    }
    
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
    imagedestroy($src);
    
    // Save with compression
    switch ($info[2]) {
        case IMAGETYPE_JPEG:
            imagejpeg($dst, $filepath, $qualJpg);
            break;
        case IMAGETYPE_PNG:
            // PNG quality is 0-9 (compression level)
            imagepng($dst, $filepath, 8);
            break;
        case IMAGETYPE_GIF:
            imagegif($dst, $filepath);
            break;
        case IMAGETYPE_WEBP:
            imagewebp($dst, $filepath, $qualWebp);
            break;
    }
    
    imagedestroy($dst);
    
    $newSize = filesize($filepath);
    $saved = round(($originalSize - $newSize) / 1024 / 1024, 2);
    $pct = round((1 - $newSize / $originalSize) * 100);
    
    $results[] = "OK: $file | {$origWidth}x{$origHeight} → {$newWidth}x{$newHeight} | " 
               . round($originalSize/1024/1024, 2) . "MB → " 
               . round($newSize/1024/1024, 2) . "MB | saved {$saved}MB ({$pct}%)";
}

// Also optimize icon-512.png (507 KB is too big for a PWA icon)
$iconFile = __DIR__ . '/public/icon-512.png';
if (file_exists($iconFile)) {
    copy($iconFile, $backupDir . 'icon-512.png');
    $info = getimagesize($iconFile);
    if ($info && $info[2] === IMAGETYPE_PNG) {
        $src = imagecreatefrompng($iconFile);
        $dst = imagecreatetruecolor(512, 512);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, 512, 512, $info[0], $info[1]);
        imagedestroy($src);
        imagepng($dst, $iconFile, 9);
        imagedestroy($dst);
        $results[] = "OK: icon-512.png compressed";
    }
}

echo "<pre style='font-family:monospace;padding:20px'>\n";
echo "=== IMAGE OPTIMIZATION COMPLETE ===\n\n";
foreach ($results as $r) echo $r . "\n";
echo "\n=== BACKUPS SAVED TO: $backupDir ===\n";
echo "\n⚠️  DELETE THIS FILE AFTER RUNNING!\n";
echo "</pre>";
