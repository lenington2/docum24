<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser as PdfParser;
use PhpOffice\PhpWord\IOFactory as WordFactory;
use PhpOffice\PhpSpreadsheet\IOFactory as SpreadsheetFactory;

class TextExtractorService
{
    public function extract(string $path, string $mimeType): ?string
    {
        $fullPath = Storage::disk('local')->path($path);

        try {
            return match(true) {
                str_contains($mimeType, 'pdf')          => $this->extractPdf($fullPath),
                str_contains($mimeType, 'word') ||
                str_contains($mimeType, 'wordprocessingml') => $this->extractWord($fullPath),
                str_contains($mimeType, 'excel') ||
                str_contains($mimeType, 'spreadsheetml') ||
                str_contains($mimeType, 'spreadsheet')  => $this->extractExcel($fullPath),
                str_contains($mimeType, 'powerpoint') ||
                str_contains($mimeType, 'presentationml') => $this->extractPowerPoint($fullPath),
                str_contains($mimeType, 'text/plain')   => file_get_contents($fullPath),
                default                                  => null,
            };
        } catch (\Exception $e) {
            return null;
        }
    }

    private function extractPdf(string $path): string
    {
        $parser = new PdfParser();
        $pdf    = $parser->parseFile($path);
        return $pdf->getText();
    }

    private function extractWord(string $path): string
    {
        $phpWord  = WordFactory::load($path);
        $text     = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $text .= $element->getText() . "\n";
                } elseif (method_exists($element, 'getElements')) {
                    foreach ($element->getElements() as $child) {
                        if (method_exists($child, 'getText')) {
                            $text .= $child->getText() . "\n";
                        }
                    }
                }
            }
        }

        return $text;
    }

    private function extractExcel(string $path): string
    {
        $spreadsheet = SpreadsheetFactory::load($path);
        $text        = '';

        foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
            $text .= "--- {$sheet->getTitle()} ---\n";
            foreach ($sheet->getRowIterator() as $row) {
                $cells = [];
                foreach ($row->getCellIterator() as $cell) {
                    $val = $cell->getValue();
                    if ($val !== null && $val !== '') {
                        $cells[] = $val;
                    }
                }
                if (!empty($cells)) {
                    $text .= implode(' | ', $cells) . "\n";
                }
            }
        }

        return $text;
    }

    private function extractPowerPoint(string $path): string
    {
        $presentation = SpreadsheetFactory::load($path);
        $text         = '';

        foreach ($presentation->getWorksheetIterator() as $slide) {
            $text .= "--- Slide ---\n";
            foreach ($slide->getRowIterator() as $row) {
                foreach ($row->getCellIterator() as $cell) {
                    $val = $cell->getValue();
                    if ($val !== null && $val !== '') {
                        $text .= $val . "\n";
                    }
                }
            }
        }

        return $text;
    }
}
