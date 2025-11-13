<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;

if (!function_exists('now')) {
    function now()
    {
        return Carbon::now();
    }
}

class QrEncryptHelper
{
    public static function encrypt($input)
    {
        return base64_encode(Crypt::encryptString($input));
    }

    public static function decrypt($input)
    {
        try {
            return Crypt::decryptString(base64_decode($input));
        } catch (\Exception $e) {
            return null;
        }
    }
}

class ImageOptimizer
{
    /**
     * Optimize image for PDF generation to reduce file size
     *
     * @param string $imagePath Full path to the image
     * @param int $maxWidth Maximum width in pixels
     * @param int $quality Quality (1-100, lower = smaller file)
     * @return string|null Base64 encoded data URL or null on failure
     */
    public static function optimizeForPdf($imagePath, $maxWidth = 600, $quality = 75)
    {
        if (!file_exists($imagePath)) {
            return null;
        }

        $imageInfo = getimagesize($imagePath);
        if ($imageInfo === false) {
            return null;
        }

        $sourceWidth = $imageInfo[0];
        $sourceHeight = $imageInfo[1];
        $mimeType = $imageInfo['mime'];

        // Calculate new dimensions maintaining aspect ratio
        if ($sourceWidth > $maxWidth) {
            $targetWidth = $maxWidth;
            $targetHeight = (int)(($maxWidth / $sourceWidth) * $sourceHeight);
        } else {
            $targetWidth = $sourceWidth;
            $targetHeight = $sourceHeight;
        }

        // Create image resource from source
        $sourceImage = null;
        switch ($mimeType) {
            case 'image/jpeg':
                $sourceImage = imagecreatefromjpeg($imagePath);
                break;
            case 'image/png':
                $sourceImage = imagecreatefrompng($imagePath);
                break;
            case 'image/gif':
                $sourceImage = imagecreatefromgif($imagePath);
                break;
            default:
                return null;
        }

        if (!$sourceImage) {
            return null;
        }

        // Create new image with target dimensions
        $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);

        // Preserve transparency for PNG
        if ($mimeType === 'image/png') {
            imagealphablending($targetImage, false);
            imagesavealpha($targetImage, true);
            $transparent = imagecolorallocatealpha($targetImage, 0, 0, 0, 127);
            imagefill($targetImage, 0, 0, $transparent);
        }

        // Resize image
        imagecopyresampled(
            $targetImage, $sourceImage,
            0, 0, 0, 0,
            $targetWidth, $targetHeight,
            $sourceWidth, $sourceHeight
        );

        // Output to buffer
        ob_start();
        if ($mimeType === 'image/png') {
            // PNG quality is 0-9 (9 = no compression, 0 = max compression)
            // Convert 0-100 quality to 0-9 scale (inverted)
            $pngQuality = (int)(9 - (($quality / 100) * 9));
            imagepng($targetImage, null, $pngQuality);
        } else {
            imagejpeg($targetImage, null, $quality);
        }
        $imageData = ob_get_clean();

        // Clean up
        imagedestroy($sourceImage);
        imagedestroy($targetImage);

        // Return as data URL for PDF embedding
        return 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
    }

    /**
     * Get cached optimized image or create new one
     *
     * @param string $imagePath
     * @param int $maxWidth
     * @param int $quality
     * @return string|null
     */
    public static function getCachedOptimized($imagePath, $maxWidth = 600, $quality = 75)
    {
        $cacheKey = 'pdf_img_' . md5($imagePath . $maxWidth . $quality);

        // Try to get from cache (24 hours)
        $cached = cache()->remember($cacheKey, 86400, function () use ($imagePath, $maxWidth, $quality) {
            return self::optimizeForPdf($imagePath, $maxWidth, $quality);
        });

        return $cached;
    }
}