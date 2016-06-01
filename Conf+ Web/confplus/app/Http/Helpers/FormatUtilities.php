<?php

namespace App\Http\Helpers;

use Carbon\Carbon;

class FormatUtilities
{
    private static $dateSeparator = '-';
    private static $format = 'Y-m-d H:i:s';
    
    public static function getDateTime(array $timecolumns, array &$data)
    {
        foreach ($timecolumns as $column => $format) {
            if (array_key_exists($column, $data) && !is_null($data[$column])) {
                try{
                    $data[$column] = Carbon::createFromFormat(self::$format, trim($data[$column]));
                } catch (InvalidArgumentException $e) {
                    return JSONUtilities::returnError(self::displayTimecolumnFormats($timecolumns));
                }
            }
        }

        return true;
    }

    public static function displayTimecolumnFormats(array $timecolumns)
    {
        $messages = ['Cannot parse time'];
        foreach ($timecolumns as $column => $format) {
            $messages[] = $column . ' => ' . self::$format;
        }

        return implode('. ', $messages);
    }
    
    public static function convertToTinyInt(array $columns, array &$data) {
        foreach ($columns as $column) {
            if (array_key_exists($column, $data) && !is_null($data[$column])) {
                if ($data[$column]) {
                    $data[$column] = 1;
                } else {
                    $data[$column] = 0;
                }
            }
        }
    }
}
