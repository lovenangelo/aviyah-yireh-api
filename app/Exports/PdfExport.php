<?php

namespace App\Exports;

use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Exporter class for exporting PDF Files
 *
 * This class is a PDF Export Functionality built around barryvdh's Laravel DOMPDF Package
 * https://github.com/barryvdh/laravel-dompdf
 */

class PdfExport extends BaseExporter
{

    /**
     * The view file that will be used as a template
     */
    protected $template;

    /**
     * Set the name of the view file to be used as a template.
     */
    public function setTemplate(string $view): void
    {
        $this->template = $view;
    }

    public function export(): mixed
    {
        if (empty($this->template)) {
            throw new \InvalidArgumentException("PDF template view is not set. Call setTemplate() before export.");
        }

        if (empty($this->data)) {
            throw new \InvalidArgumentException("PDF data is not set. Call setData() before export.");
        }

        $pdf = Pdf::loadView($this->template, $this->data);
        return $pdf->download($this->fileName);
    }

}
