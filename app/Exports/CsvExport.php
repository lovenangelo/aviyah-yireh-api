<?php

namespace App\Exports;

use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExport extends BaseExporter
{
    private function fileNameGenerator(): string
    {
        return date('Y-m-d_H-i-s').'_export.csv';
    }

    public function export(): StreamedResponse
    {

        $fileName = isset($this->fileName) ? $this->fileName : $this->fileNameGenerator();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');

            if (! empty($this->data)) {
                fputcsv($handle, array_keys($this->data[0]));
            } else {
                throw new \InvalidArgumentException('CSV export requires non-empty data. Call setData() before export.');
            }

            foreach ($this->data as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }
}
