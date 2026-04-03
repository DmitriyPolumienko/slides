<?php

namespace App\Services\Export;

use App\Models\Presentation;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfExportService
{
    public function export(Presentation $presentation): string
    {
        $presentation->load(['slides.slots', 'theme', 'project', 'language', 'masterTemplate.versions']);

        $pdf = Pdf::loadView('exports.pdf', ['presentation' => $presentation])
            ->setPaper('a4', 'landscape');

        $filename = 'presentation_' . $presentation->id . '_' . time() . '.pdf';
        $path = storage_path('app/exports/' . $filename);

        if (!is_dir(storage_path('app/exports'))) {
            mkdir(storage_path('app/exports'), 0755, true);
        }

        $pdf->save($path);
        return $path;
    }
}
