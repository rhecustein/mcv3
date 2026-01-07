<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class PdfCompressionService
{
    /**
     * Compression quality levels
     */
    const QUALITY_SCREEN = 'screen';      // 72 dpi - smallest file, lowest quality
    const QUALITY_EBOOK = 'ebook';        // 150 dpi - medium file, good for ebooks
    const QUALITY_PRINTER = 'printer';    // 300 dpi - larger file, good for printing
    const QUALITY_PREPRESS = 'prepress';  // 300 dpi - highest quality

    /**
     * Compress PDF using Ghostscript
     *
     * @param string $inputPath Path to input PDF
     * @param string $outputPath Path to output compressed PDF
     * @param string $quality Quality level (screen|ebook|printer|prepress)
     * @return array ['success' => bool, 'original_size' => int, 'compressed_size' => int, 'ratio' => int, 'method' => string]
     */
    public function compressPdf(
        string $inputPath,
        string $outputPath,
        string $quality = self::QUALITY_EBOOK
    ): array {
        try {
            // Check if Ghostscript is installed
            if (!$this->isGhostscriptInstalled()) {
                Log::warning('Ghostscript not installed, skipping compression');
                return [
                    'success' => false,
                    'error' => 'Ghostscript not installed',
                ];
            }

            // Get original file size
            $originalSize = Storage::disk('public')->size($inputPath);

            // Full paths
            $fullInputPath = Storage::disk('public')->path($inputPath);
            $fullOutputPath = Storage::disk('public')->path($outputPath);

            // Ensure output directory exists
            $outputDir = dirname($fullOutputPath);
            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0755, true);
            }

            // Ghostscript command
            $command = sprintf(
                'gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/%s -dNOPAUSE -dQUIET -dBATCH -sOutputFile=%s %s',
                escapeshellarg($quality),
                escapeshellarg($fullOutputPath),
                escapeshellarg($fullInputPath)
            );

            Log::info('Compressing PDF', [
                'input' => $inputPath,
                'output' => $outputPath,
                'quality' => $quality,
                'original_size' => $originalSize,
            ]);

            // Execute compression
            $result = Process::timeout(120)->run($command);

            if ($result->failed()) {
                Log::error('PDF compression failed', [
                    'error' => $result->errorOutput(),
                    'command' => $command,
                ]);

                return [
                    'success' => false,
                    'error' => $result->errorOutput(),
                ];
            }

            // Get compressed file size
            $compressedSize = Storage::disk('public')->size($outputPath);

            // Calculate compression ratio
            $ratio = 0;
            if ($originalSize > 0) {
                $ratio = round((($originalSize - $compressedSize) / $originalSize) * 100);
            }

            Log::info('PDF compression successful', [
                'original_size' => $originalSize,
                'compressed_size' => $compressedSize,
                'ratio' => $ratio . '%',
            ]);

            return [
                'success' => true,
                'original_size' => $originalSize,
                'compressed_size' => $compressedSize,
                'ratio' => $ratio,
                'method' => 'ghostscript',
                'quality' => $quality,
            ];

        } catch (\Exception $e) {
            Log::error('PDF compression exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Compress PDF in-place (replaces original)
     *
     * @param string $pdfPath Path to PDF file
     * @param string $quality Quality level
     * @return array Compression result
     */
    public function compressPdfInPlace(string $pdfPath, string $quality = self::QUALITY_EBOOK): array
    {
        $tempPath = $pdfPath . '.tmp';

        $result = $this->compressPdf($pdfPath, $tempPath, $quality);

        if ($result['success']) {
            // Only replace if compression actually reduced file size
            if ($result['compressed_size'] < $result['original_size']) {
                // Backup original (optional)
                // Storage::disk('public')->copy($pdfPath, $pdfPath . '.original');

                // Replace with compressed version
                Storage::disk('public')->delete($pdfPath);
                Storage::disk('public')->move($tempPath, $pdfPath);

                Log::info('PDF replaced with compressed version', [
                    'path' => $pdfPath,
                    'savings' => $result['original_size'] - $result['compressed_size'],
                ]);
            } else {
                // Compressed version is larger, keep original
                Storage::disk('public')->delete($tempPath);

                Log::info('Compressed version larger than original, keeping original', [
                    'path' => $pdfPath,
                ]);

                $result['success'] = false;
                $result['error'] = 'Compressed version is larger than original';
            }
        }

        return $result;
    }

    /**
     * Check if Ghostscript is installed
     */
    public function isGhostscriptInstalled(): bool
    {
        $result = Process::run('gs --version');
        return $result->successful();
    }

    /**
     * Get Ghostscript version
     */
    public function getGhostscriptVersion(): ?string
    {
        $result = Process::run('gs --version');

        if ($result->successful()) {
            return trim($result->output());
        }

        return null;
    }

    /**
     * Format bytes to human-readable size
     */
    public function formatBytes(?int $bytes): string
    {
        if ($bytes === null || $bytes === 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < 4; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get recommended quality based on PDF age
     *
     * @param int $daysOld Age of PDF in days
     * @return string Quality level
     */
    public function getRecommendedQuality(int $daysOld): string
    {
        if ($daysOld <= 30) {
            return self::QUALITY_PRINTER; // Recent PDFs, keep high quality
        } elseif ($daysOld <= 90) {
            return self::QUALITY_EBOOK; // 1-3 months, medium quality
        } else {
            return self::QUALITY_SCREEN; // Old PDFs, maximum compression
        }
    }
}
