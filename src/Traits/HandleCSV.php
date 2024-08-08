<?php

namespace Internetguru\CsvTable\Traits;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

trait HandleCSV
{
    public function readCSV(string $path, $delimiter = ','): array|bool
    {
        // Determine if the path is a URL
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $content = $this->fetchCSVFromUrl($path);
        } else {
            $content = $this->fetchCSVFromFile($path);
        }

        if (! $content) {
            return false;
        }

        $header = null;
        $data = [];
        $lines = explode(PHP_EOL, $content);

        foreach ($lines as $line) {
            if (empty(trim($line))) {
                continue;
            }

            $row = str_getcsv($line, $delimiter);
            if (! $header) {
                $header = $row;
            } else {
                $data[] = array_combine($header, $row);
            }
        }

        return $data;
    }

    private function fetchCSVFromFile($filePath)
    {
        if (! file_exists($filePath) || ! is_readable($filePath)) {
            return false;
        }

        return file_get_contents($filePath);
    }

    private function fetchCSVFromUrl($url)
    {
        $response = Http::get($url);

        if ($response->failed()) {
            return false;
        }

        return $response->body();
    }

    public function generateCSV(array $data, $delimiter = ',')
    {
        $output = fopen('php://temp/maxmemory:' . (5 * 1024 * 1024), 'r+');
        fputcsv($output, array_keys($data[0]), $delimiter);

        foreach ($data as $row) {
            fputcsv($output, $row, $delimiter);
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return $csvContent;
    }

    public function responseCsv(string $csv, bool $download = true, string $downloadName = 'data.csv'): Response
    {
        return $download
            ? response($csv)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="' . $downloadName . '"')
            : response($csv)
                ->header('Content-Type', 'text/plain');
    }
}