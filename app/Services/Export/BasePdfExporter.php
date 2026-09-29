<?php

namespace App\Services\Export;

use App\Services\Export\Concerns\NormalizesTableFilters;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdfWrapper;
use Symfony\Component\HttpFoundation\StreamedResponse;

abstract class BasePdfExporter
{
    use NormalizesTableFilters;

    protected array $filters;

    protected string $orientation = 'landscape';

    protected string $paperSize = 'a4';

    /**
     * @param  array<string, mixed>  $filters
     */
    public function __construct(array $filters = [])
    {
        $this->filters = $this->normalizeFilters($filters);
    }

    abstract protected function getViewName(): string;

    /**
     * @return array<string, mixed>
     */
    abstract protected function getViewData(): array;

    abstract protected function getFilename(): string;

    protected function createPdf(): DomPdfWrapper
    {
        $data = array_merge(
            $this->getViewData(),
            ['filters' => $this->filters]
        );

        return Pdf::loadView($this->getViewName(), $data)
            ->setPaper($this->paperSize, $this->orientation)
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => 'sans-serif',
            ]);
    }

    /**
     * Livewire hanya mengubah respons unduhan browser untuk StreamedResponse
     * atau BinaryFileResponse. DomPDF sendiri mengembalikan Response biasa,
     * yang membuat Livewire mencoba mengirim ulang PDF sebagai payload JSON
     * ("Malformed UTF-8 characters"). Bungkus manual ke StreamedResponse.
     */
    public function download(): StreamedResponse
    {
        $output = $this->createPdf()->output();
        $filename = $this->getFilename().'.pdf';

        return response()->streamDownload(
            fn () => print ($output),
            $filename,
            [
                'Content-Type' => 'application/pdf',
                'Cache-Control' => 'max-age=0',
            ]
        );
    }

    public function stream(): StreamedResponse
    {
        $output = $this->createPdf()->output();
        $filename = $this->getFilename().'.pdf';

        return response()->streamDownload(
            fn () => print ($output),
            $filename,
            ['Content-Type' => 'application/pdf'],
            'inline'
        );
    }

    public function saveToFile(string $absolutePath): string
    {
        $directory = dirname($absolutePath);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $this->createPdf()->save($absolutePath);

        return $absolutePath;
    }
}
