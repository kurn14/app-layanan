<?php

namespace App\Services\Export;

use App\Services\Export\Concerns\NormalizesTableFilters;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

abstract class BaseExcelExporter
{
    use NormalizesTableFilters;

    protected Spreadsheet $spreadsheet;

    protected array $filters;

    protected string $title = 'Laporan';

    protected ?string $subtitle = null;

    /**
     * @param  array<string, mixed>  $filters
     */
    public function __construct(array $filters = [])
    {
        $this->filters = $this->normalizeFilters($filters);
        $this->spreadsheet = new Spreadsheet;
    }

    /**
     * @return array<int, string>
     */
    abstract protected function getHeaders(): array;

    /**
     * @return iterable<int, array<int, mixed>>
     */
    abstract protected function getRows(): iterable;

    abstract protected function getFilename(): string;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function download(): StreamedResponse
    {
        $this->build();
        $writer = new Xlsx($this->spreadsheet);
        $filename = $this->getFilename().'.xlsx';

        return response()->streamDownload(
            fn () => $writer->save('php://output'),
            $filename,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'max-age=0',
            ]
        );
    }

    public function saveToFile(string $absolutePath): string
    {
        $this->build();
        $directory = dirname($absolutePath);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $writer = new Xlsx($this->spreadsheet);
        $writer->save($absolutePath);

        return $absolutePath;
    }

    public function build(): void
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setTitle(mb_substr($this->getTitle(), 0, 31));

        $headers = $this->getHeaders();
        $colCount = count($headers);
        if ($colCount === 0) {
            return;
        }

        // 1. Tulis Kop Surat
        $startRow = $this->writeKopSurat($sheet, $colCount);

        // 2. Tulis Header Kolom
        $headerRow = $startRow;
        foreach ($headers as $index => $header) {
            $sheet->setCellValue([$index + 1, $headerRow], $header);
        }

        $lastColLetter = Coordinate::stringFromColumnIndex($colCount);
        $this->applyHeaderStyle($sheet, "A{$headerRow}:{$lastColLetter}{$headerRow}");

        // 3. Tulis Baris Data
        $currentRow = $headerRow + 1;
        $rowCount = 0;
        foreach ($this->getRows() as $rowData) {
            $colIndex = 1;
            foreach ($rowData as $cellValue) {
                $sheet->setCellValue([$colIndex, $currentRow], $cellValue);
                $colIndex++;
            }
            $currentRow++;
            $rowCount++;
        }

        $lastDataRow = max($currentRow - 1, $headerRow);

        // 4. Styling Borders & Zebra Rows
        if ($rowCount > 0) {
            $this->applyTableBorders($sheet, "A{$headerRow}:{$lastColLetter}{$lastDataRow}");
            $this->applyZebraRowStyles($sheet, $headerRow + 1, $lastDataRow, $colCount);
        }

        // 5. Auto-size Kolom
        $this->autoFitColumns($sheet, $colCount);
    }

    /**
     * Tulis Kop Surat Resmi Pemerintah Kabupaten Blitar / Dinas Sosial.
     *
     * @return int Baris berikutnya yang siap diisi
     */
    protected function writeKopSurat(Worksheet $sheet, int $colCount): int
    {
        $lastColLetter = Coordinate::stringFromColumnIndex(max($colCount, 1));

        // Baris 1: PEMERINTAH KABUPATEN BLITAR
        $sheet->mergeCells("A1:{$lastColLetter}1");
        $sheet->setCellValue('A1', 'PEMERINTAH KABUPATEN BLITAR');
        $sheet->getStyle('A1')->getFont()->setSize(11)->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Baris 2: DINAS SOSIAL
        $sheet->mergeCells("A2:{$lastColLetter}2");
        $sheet->setCellValue('A2', 'DINAS SOSIAL — SISTEM SAPA SOSIAL');
        $sheet->getStyle('A2')->getFont()->setSize(13)->setBold(true)->getColor()->setRGB('1E3A8A');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Baris 3: Judul Laporan
        $sheet->mergeCells("A3:{$lastColLetter}3");
        $sheet->setCellValue('A3', mb_strtoupper($this->getTitle()));
        $sheet->getStyle('A3')->getFont()->setSize(11)->setBold(true);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Baris 4: Informasi Filter / Waktu Cetak
        $infoText = 'Dicetak: '.now()->timezone('Asia/Jakarta')->translatedFormat('d F Y H:i').' WIB';
        if ($this->subtitle) {
            $infoText = $this->subtitle.' | '.$infoText;
        }
        $sheet->mergeCells("A4:{$lastColLetter}4");
        $sheet->setCellValue('A4', $infoText);
        $sheet->getStyle('A4')->getFont()->setSize(9)->setItalic(true)->getColor()->setRGB('4B5563');
        $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Baris kosong pemisah
        return 6;
    }

    protected function applyHeaderStyle(Worksheet $sheet, string $cellRange): void
    {
        $sheet->getStyle($cellRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 10,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E3A8A'], // Navy Blue
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '94A3B8'],
                ],
            ],
        ]);
        $sheet->getRowDimension(explode(':', $cellRange)[0][1] ?? 6)->setRowHeight(26);
    }

    protected function applyTableBorders(Worksheet $sheet, string $cellRange): void
    {
        $sheet->getStyle($cellRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CBD5E1'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
    }

    protected function applyZebraRowStyles(Worksheet $sheet, int $startRow, int $endRow, int $colCount): void
    {
        $lastColLetter = Coordinate::stringFromColumnIndex($colCount);

        for ($r = $startRow; $r <= $endRow; $r++) {
            $sheet->getRowDimension($r)->setRowHeight(20);
            if ($r % 2 === 0) {
                $sheet->getStyle("A{$r}:{$lastColLetter}{$r}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F8FAFC'],
                    ],
                ]);
            }
        }
    }

    protected function autoFitColumns(Worksheet $sheet, int $colCount): void
    {
        for ($col = 1; $col <= $colCount; $col++) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }
    }
}
