<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Services\ActivityLogger;
use Illuminate\Support\Str;

trait ExportsReportCsv
{
    protected function exportCsv(string $reportName, array $headers, iterable $rows)
    {
        ActivityLogger::log('export', "Exported {$reportName} report", null, ['report' => $reportName]);

        $filename = Str::slug($reportName) . '_' . now()->format('Y-m-d') . '.csv';

        $callback = function () use ($headers, $rows) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            foreach ($rows as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
