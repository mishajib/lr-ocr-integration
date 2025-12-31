<?php

namespace App\Actions;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Spatie\PdfToImage\Pdf;
use thiagoalessio\TesseractOCR\TesseractOCR;

class PdfToOcRAction
{
    public function ocrPdf($pdfPath)
    {
        $pdf = new Pdf($pdfPath);
        $fullText = '';

        if (!File::exists(storage_path('app/temp'))) {
            // Create temp directory
            File::makeDirectory(storage_path('app/temp'));
        }
        for ($page = 1; $page <= $pdf->getNumberOfPages(); $page++) {
            $imagePathDir = storage_path('app/temp/page-' . $page . '.jpg');
            $pdf->setPage($page)
                ->setResolution(300)  // Higher DPI for accuracy
                ->saveImage($imagePathDir);

            $text = new TesseractOCR($imagePathDir)
                ->lang('eng', 'Arabic')
                ->psm(6)
                ->run();

            $fullText .= $text . "\n\n--- Page {$page} ---\n\n";

            unlink($imagePathDir);  // Cleanup
        }

        Storage::deleteDirectory('temp');  // Full cleanup
        return $fullText;
    }
}
