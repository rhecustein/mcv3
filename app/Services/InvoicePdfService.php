<?php

namespace App\Services;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InvoicePdfService
{
    /**
     * Generate PDF for invoice
     */
    public function generate(Invoice $invoice): \Barryvdh\DomPDF\PDF
    {
        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice->load(['tenant', 'lineItems']),
        ]);

        // Configure PDF options
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('defaultFont', 'sans-serif');

        return $pdf;
    }

    /**
     * Generate and save PDF to storage
     */
    public function generateAndSave(Invoice $invoice, string $disk = 'local'): string
    {
        $pdf = $this->generate($invoice);

        $filename = "invoices/{$invoice->invoice_number}.pdf";

        Storage::disk($disk)->put($filename, $pdf->output());

        return $filename;
    }

    /**
     * Download PDF
     */
    public function download(Invoice $invoice, ?string $filename = null): \Symfony\Component\HttpFoundation\Response
    {
        $pdf = $this->generate($invoice);

        $filename = $filename ?? "Invoice-{$invoice->invoice_number}.pdf";

        return $pdf->download($filename);
    }

    /**
     * Stream PDF to browser
     */
    public function stream(Invoice $invoice): \Symfony\Component\HttpFoundation\Response
    {
        $pdf = $this->generate($invoice);

        return $pdf->stream("Invoice-{$invoice->invoice_number}.pdf");
    }

    /**
     * Get PDF as base64 string
     */
    public function toBase64(Invoice $invoice): string
    {
        $pdf = $this->generate($invoice);

        return base64_encode($pdf->output());
    }

    /**
     * Generate multiple invoices as ZIP
     */
    public function generateBulk(array $invoices): string
    {
        $zip = new \ZipArchive();
        $zipFilename = 'invoices_' . now()->format('Y-m-d_His') . '.zip';
        $zipPath = storage_path("app/temp/{$zipFilename}");

        // Create temp directory if it doesn't exist
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        if ($zip->open($zipPath, \ZipArchive::CREATE) === true) {
            foreach ($invoices as $invoice) {
                $pdf = $this->generate($invoice);
                $zip->addFromString(
                    "Invoice-{$invoice->invoice_number}.pdf",
                    $pdf->output()
                );
            }
            $zip->close();
        }

        return $zipPath;
    }
}
