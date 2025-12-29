<?php

namespace App\Helpers;

class ImageHelper
{
    /**
     * Compress and resize image
     * 
     * @param string $sourcePath Path to source image
     * @param string $destinationPath Path to save compressed image
     * @param int $maxWidth Maximum width (default: 500)
     * @param int $maxHeight Maximum height (default: 500)
     * @param int $quality JPEG quality 0-100 (default: 85)
     * @return bool Success status
     */
    public static function compressImage($sourcePath, $destinationPath, $maxWidth = 500, $maxHeight = 500, $quality = 85)
    {
        // Check if GD extension is available
        if (!extension_loaded('gd')) {
            \Log::warning('GD extension not loaded, skipping image compression');
            return false;
        }

        // Get image info
        $imageInfo = getimagesize($sourcePath);
        if (!$imageInfo) {
            \Log::error('Invalid image file', ['path' => $sourcePath]);
            return false;
        }

        $originalWidth = $imageInfo[0];
        $originalHeight = $imageInfo[1];
        $mimeType = $imageInfo['mime'];

        // Calculate new dimensions maintaining aspect ratio
        $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
        $newWidth = (int)($originalWidth * $ratio);
        $newHeight = (int)($originalHeight * $ratio);

        // If image is smaller than max dimensions, use original dimensions
        if ($newWidth > $originalWidth || $newHeight > $originalHeight) {
            $newWidth = $originalWidth;
            $newHeight = $originalHeight;
        }

        // Create image resource based on mime type
        switch ($mimeType) {
            case 'image/jpeg':
                $sourceImage = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $sourceImage = imagecreatefrompng($sourcePath);
                break;
            case 'image/gif':
                $sourceImage = imagecreatefromgif($sourcePath);
                break;
            case 'image/webp':
                if (function_exists('imagecreatefromwebp')) {
                    $sourceImage = imagecreatefromwebp($sourcePath);
                } else {
                    \Log::warning('WebP not supported');
                    return false;
                }
                break;
            default:
                \Log::error('Unsupported image type', ['mime' => $mimeType]);
                return false;
        }

        if (!$sourceImage) {
            \Log::error('Failed to create image resource', ['path' => $sourcePath]);
            return false;
        }

        // Create new image with new dimensions
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for PNG and GIF
        if ($mimeType == 'image/png' || $mimeType == 'image/gif') {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        // Resize image
        imagecopyresampled(
            $newImage,
            $sourceImage,
            0, 0, 0, 0,
            $newWidth,
            $newHeight,
            $originalWidth,
            $originalHeight
        );

        // Create directory if it doesn't exist
        $destinationDir = dirname($destinationPath);
        if (!is_dir($destinationDir)) {
            mkdir($destinationDir, 0755, true);
        }

        // Save compressed image
        $success = false;
        switch ($mimeType) {
            case 'image/jpeg':
                $success = imagejpeg($newImage, $destinationPath, $quality);
                break;
            case 'image/png':
                // PNG compression level 0-9 (we use 6 for balance)
                $pngQuality = 6;
                $success = imagepng($newImage, $destinationPath, $pngQuality);
                break;
            case 'image/gif':
                $success = imagegif($newImage, $destinationPath);
                break;
            case 'image/webp':
                if (function_exists('imagewebp')) {
                    $success = imagewebp($newImage, $destinationPath, $quality);
                } else {
                    // Fallback to JPEG if WebP not supported
                    $success = imagejpeg($newImage, $destinationPath, $quality);
                }
                break;
        }

        // Free memory
        imagedestroy($sourceImage);
        imagedestroy($newImage);

        if ($success) {
            \Log::info('Image compressed successfully', [
                'source' => $sourcePath,
                'destination' => $destinationPath,
                'original_size' => filesize($sourcePath),
                'compressed_size' => filesize($destinationPath),
                'dimensions' => "{$newWidth}x{$newHeight}"
            ]);
        } else {
            \Log::error('Failed to save compressed image', ['destination' => $destinationPath]);
        }

        return $success;
    }

    /**
     * Compress uploaded image file
     * 
     * @param \Illuminate\Http\UploadedFile $file Uploaded file
     * @param string $destinationPath Destination path
     * @param int $maxWidth Maximum width (default: 500)
     * @param int $maxHeight Maximum height (default: 500)
     * @param int $quality JPEG quality 0-100 (default: 85)
     * @return string|false Path to compressed image or false on failure
     */
    public static function compressUploadedImage($file, $destinationPath, $maxWidth = 500, $maxHeight = 500, $quality = 85)
    {
        // Create temporary path for uploaded file
        $tempPath = $file->getRealPath();
        
        if (!file_exists($tempPath)) {
            \Log::error('Temporary file not found', ['path' => $tempPath]);
            return false;
        }

        // Compress image
        $success = self::compressImage($tempPath, $destinationPath, $maxWidth, $maxHeight, $quality);

        if ($success) {
            return $destinationPath;
        }

        // If compression fails, return original file path
        return $tempPath;
    }
}

