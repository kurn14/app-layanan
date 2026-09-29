<?php

namespace App\Services;

use App\Models\DtsenCertificate;
use App\Models\PbiReactivation;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentTemplateService
{
    /**
     * Generate a valid standalone PDF for the official downloadable form via DomPDF.
     */
    public static function createFormPdf(string $title, string $version = '1.0'): string
    {
        return Pdf::loadView('documents.downloadable-form', [
            'title' => $title,
            'version' => $version,
        ])
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => 'sans-serif',
            ])
            ->output();
    }

    /**
     * Generate official SK DTSEN letter PDF.
     */
    public static function createDtsenCertificatePdf(DtsenCertificate $certificate): string
    {
        return Pdf::loadView('documents.dtsen-certificate', [
            'certificate' => $certificate,
        ])
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => 'serif',
            ])
            ->output();
    }

    /**
     * Generate official PBI-JK Recommendation letter PDF.
     */
    public static function createPbiRecommendationPdf(PbiReactivation $pbi): string
    {
        return Pdf::loadView('documents.pbi-recommendation', [
            'pbi' => $pbi,
        ])
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => 'serif',
            ])
            ->output();
    }
}
