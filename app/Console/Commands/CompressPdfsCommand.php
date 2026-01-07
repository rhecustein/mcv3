<?php

namespace App\Console\Commands;

use App\Jobs\Maintenance\CompressPdfsJob;
use App\Services\PdfCompressionService;
use Illuminate\Console\Command;

class CompressPdfsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pdf:compress
                            {--days=30 : Compress PDFs older than this many days}
                            {--tenant= : Specific tenant ID (optional)}
                            {--quality= : Compression quality (screen|ebook|printer|prepress)}
                            {--limit=1000 : Maximum PDFs to compress per run}
                            {--queue : Run as background job instead of synchronously}
                            {--check : Just check if Ghostscript is installed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compress old PDF certificates to save storage space';

    /**
     * Execute the console command.
     */
    public function handle(PdfCompressionService $compressionService): int
    {
        // Check Ghostscript installation
        if ($this->option('check')) {
            return $this->checkGhostscript($compressionService);
        }

        // Verify Ghostscript is installed
        if (!$compressionService->isGhostscriptInstalled()) {
            $this->error('Ghostscript is not installed!');
            $this->info('Install with: sudo apt-get install ghostscript (Ubuntu/Debian)');
            $this->info('Or: brew install ghostscript (macOS)');
            return self::FAILURE;
        }

        $days = (int) $this->option('days');
        $tenant = $this->option('tenant');
        $quality = $this->option('quality');
        $limit = (int) $this->option('limit');
        $useQueue = $this->option('queue');

        // Validate quality
        if ($quality && !in_array($quality, ['screen', 'ebook', 'printer', 'prepress'])) {
            $this->error('Invalid quality. Use: screen, ebook, printer, or prepress');
            return self::FAILURE;
        }

        $this->info('PDF Compression Settings:');
        $this->table(
            ['Setting', 'Value'],
            [
                ['Days Old', $days],
                ['Tenant', $tenant ?? 'All tenants'],
                ['Quality', $quality ?? 'Auto (based on age)'],
                ['Limit', $limit],
                ['Mode', $useQueue ? 'Background Job' : 'Synchronous'],
            ]
        );

        if (!$this->confirm('Proceed with compression?', true)) {
            $this->info('Cancelled.');
            return self::SUCCESS;
        }

        if ($useQueue) {
            // Dispatch as background job
            CompressPdfsJob::dispatch($days, $tenant, $quality, $limit);

            $this->info('✅ PDF compression job queued successfully!');
            $this->info('Monitor progress with: php artisan queue:monitor');
        } else {
            // Run synchronously
            $this->info('Compressing PDFs... This may take a while.');

            $bar = $this->output->createProgressBar($limit);
            $bar->start();

            CompressPdfsJob::dispatchSync($days, $tenant, $quality, $limit);

            $bar->finish();
            $this->newLine(2);

            $this->info('✅ PDF compression completed!');
            $this->info('Check logs for details: storage/logs/laravel.log');
        }

        return self::SUCCESS;
    }

    /**
     * Check Ghostscript installation
     */
    private function checkGhostscript(PdfCompressionService $compressionService): int
    {
        $this->info('Checking Ghostscript installation...');

        if ($compressionService->isGhostscriptInstalled()) {
            $version = $compressionService->getGhostscriptVersion();

            $this->info("✅ Ghostscript is installed!");
            $this->info("Version: {$version}");

            $this->newLine();
            $this->info('Compression quality levels:');
            $this->table(
                ['Quality', 'DPI', 'Use Case', 'File Size'],
                [
                    ['screen', '72', 'Screen viewing only', 'Smallest'],
                    ['ebook', '150', 'E-books, web viewing', 'Small'],
                    ['printer', '300', 'Office printing', 'Medium'],
                    ['prepress', '300', 'Professional printing', 'Largest'],
                ]
            );

            return self::SUCCESS;
        }

        $this->error('❌ Ghostscript is NOT installed!');
        $this->newLine();
        $this->info('Installation instructions:');
        $this->info('Ubuntu/Debian: sudo apt-get install ghostscript');
        $this->info('macOS: brew install ghostscript');
        $this->info('Windows: Download from https://www.ghostscript.com/');

        return self::FAILURE;
    }
}
