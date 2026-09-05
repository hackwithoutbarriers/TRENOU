<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class PdfDocumentService
{
    public function downloadView(string $view, array $data, string $filename, string $paper = 'a4', string $orientation = 'portrait'): Response
    {
        $pdf = Pdf::loadView($view, $data)
            ->setPaper($paper, $orientation);

        return $pdf->download($filename);
    }
}
