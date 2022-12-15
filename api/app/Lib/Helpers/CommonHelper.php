<?php

namespace App\Helpers;

use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CommonHelper
{

    /**
     * @param array $array
     *
     * @return void
     */
    public static function sanitizeArray(array&$array)
    {

        if (empty($array)) {
            return;
        }

        foreach ($array as $key => $value) {

            $key = strip_tags($key);

            if (is_array($value)) {
                $array[$key] = static::sanitizeArray($value);
            } else {
                if (is_bool($value) || is_numeric($value)) {
                    continue;
                }

                $array[$key] = trim(strip_tags($value));
            }
        }
    }

    /**
     * @param string $view
     * @param array $data
     * @param string|null $fileName
     * @param string|null $path
     * @param string|null $dataKey
     * @param string $orientation='portrait'
     *
     * @return mixed
     */
    public static function generatePdf(string $view, array $data, ?string $fileName = null, ?string $path = null, ?string $dataKey = 'data', string $orientation = 'portrait')
    {

        view()->share($dataKey, $data);

        $pdf = Pdf::loadView($view, $data)->setPaper('a4', $orientation);

        if ($path) {
            Storage::disk('public')->put($path, $pdf->output());

            return $path;
        } else {
            return $pdf->download($fileName ?? strtotime(date('d.m.Y H:i:s')) . ".pdf");
        }
    }

    /**
     * @param string $file
     * @param string $separator
     * @param mixed "
     *
     * @return array
     */
    public static function parseCsv(string $file, string $separator = ";"): array
    {
        try {
            $data = [];
            $open = fopen($file, "r");

            while (($row = fgetcsv($open, 1000, $separator)) !== false) {
                $data[] = mb_convert_encoding($row, 'UTF-8', 'Windows-1251');
            }

            fclose($open);

            return $data;
        } catch (Throwable $th) {
            throw new Exception($th);
        }
    }
}
