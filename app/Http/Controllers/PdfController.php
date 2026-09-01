<?php

namespace App\Http\Controllers;

use App\Models\Attestation;
use App\Models\Devis;
use App\Services\PdfDocumentService;
use Illuminate\Support\Str;

class PdfController extends Controller
{
    public function __construct(protected PdfDocumentService $pdfDocumentService) {}

    public function devis(Devis $devis)
    {
        return $this->pdfDocumentService->downloadView(
            'pdf.devis',
            ['devis' => $devis],
            'devis-'.Str::slug($devis->numero_devis).'.pdf',
            'a4',
            'portrait'
        );
    }

    public function attestation(Attestation $attestation)
    {
        return $this->pdfDocumentService->downloadView(
            'pdf.attestation',
            ['attestation' => $attestation],
            'attestation-'.Str::slug($attestation->numero_attestation).'.pdf',
            'a4',
            'landscape'
        );
    }
}
