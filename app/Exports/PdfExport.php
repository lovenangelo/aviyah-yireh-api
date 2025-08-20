<?php

namespace App\Exports;

use Barryvdh\DomPDF\Facade\Pdf;

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

    public function export()
    {
        if (!$this->template) {
            throw new \InvalidArgumentException("PDF template view is not set.");
        }

        $pdf = Pdf::loadView($this->template, $this->data);
        return $pdf->download($this->fileName);
    }

}
