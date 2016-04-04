<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

use App\Http\Helpers\JSONUtilities;
use App\Http\Helpers\FormatUtilities;

class Payment extends Model
{
    private static $timecolumns = [
        'payment_date' => 'd-m-Y H:i'
    ];
    
    /**
     * [insert]
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    public static function insert(array $data)
    {
        $success = FormatUtilities::getDateTime(self::$timecolumns, $data);
        
        if (!$success) {
            return JSONUtilities::returnError(FormatUtilities::displayTimecolumnFormats(self::$timecolumns));
        }
        
        $success = DB::table('payments')->insert($data);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Payment successfully created.'));
        } else {
            return JSONUtilities::returnError('Could not insert payment.');
        }
    }
}
