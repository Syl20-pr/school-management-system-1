<?php

namespace App\Services\Reports;

use Barryvdh\DomPDF\Facade\Pdf;

class PdfReportService
{
    /**
     * Génère un PDF à partir d'une vue Blade et renvoie le contenu binaire.
     */
    public function render(string $view, array $data = [], string $paper = 'a4', string $orientation = 'portrait'): string
    {
        return Pdf::loadView($view, $data)->setPaper($paper, $orientation)->output();
    }

    /**
     * Crée un ZIP contenant plusieurs PDFs (nom_fichier => contenu_pdf_binaire) et renvoie:
     * - path du zip si réussi
     * - null sinon
     */
    public function zipPdfs(array $files, string $zipFilename): ?string
    {
        $tempDir = storage_path('app/public/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $zipPath = $tempDir . '/' . $zipFilename;
        $zip = new \ZipArchive();
        $status = $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        if ($status !== true) {
            \Log::error("Zip creation failed. Status: {$status}");
            return null;
        }

        foreach ($files as $fileName => $binary) {
            $zip->addFromString($fileName, $binary);
        }

        $zip->close();
        return file_exists($zipPath) ? $zipPath : null;
    }
}
