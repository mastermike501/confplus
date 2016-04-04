<?php

namespace App\Http\Helpers;

use Carbon\Carbon;

class FormatUtilities
{
    private static $dateSeparator = '-';

    public static function getDateTime(array $timecolumns, array &$data)
    {
        foreach ($timecolumns as $column => $format) {
            if (array_key_exists($column, $data)) {
                if ($data[$column][2] == self::$dateSeparator && $data[$column][5] == self::$dateSeparator) {
                    $data[$column] = Carbon::createFromFormat($format, $data[$column]);
                } else {
                    return false;
                }
            }
        }

        return true;
    }

    public static function displayTimecolumnFormats(array $timecolumns)
    {
        $messages = ['Cannot parse time'];
        foreach ($timecolumns as $column => $format) {
            $messages[] = $column . ' => ' . $format;
        }

        return implode('. ', $messages);
    }
}
