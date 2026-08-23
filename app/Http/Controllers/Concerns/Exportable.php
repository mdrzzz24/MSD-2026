<?php

namespace App\Http\Controllers\Concerns;

use Symfony\Component\HttpFoundation\StreamedResponse;

trait Exportable
{
    /**
     * Generate a CSV download stream.
     */
    protected function csvDownload(array $headers, array $rows, string $filename): StreamedResponse
    {
        $callback = function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Multi-row headers: pass [[row1], [row2], ...]; a flat array is
            // still treated as a single header row (backward compatible).
            $headerRows = (!empty($headers) && is_array($headers[0])) ? $headers : [$headers];
            foreach ($headerRows as $headerRow) {
                fputcsv($handle, $headerRow);
            }

            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
