<?php

namespace App\Http\Controllers;

use App\Models\Attestation;
use App\Models\Devis;
use App\Services\PdfDocumentService;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class PdfController extends Controller
{
    public function __construct(protected PdfDocumentService $pdfDocumentService) {}

    public function devis(Devis $devis): Response
    {
        return $this->pdfDocumentService->downloadView(
            'pdf.devis',
            ['devis' => $devis],
            'devis-'.Str::slug($devis->numero_devis).'.pdf',
            'a4',
            'portrait'
        );
    }

    public function certificat(Attestation $attestation): Response
    {
        return $this->pdfDocumentService->downloadView(
            'pdf.certificat',
            ['attestation' => $attestation],
            'certificat-'.Str::slug($attestation->numero_attestation).'.pdf',
            'a4',
            'landscape'
        );
    }

    public function attestation(Attestation $attestation): Response
    {
        return $this->pdfDocumentService->downloadView(
            'pdf.attestation',
            ['attestation' => $attestation],
            'attestation-'.Str::slug($attestation->numero_attestation).'.pdf',
            'a4',
            'portrait'
        );
    }
}
