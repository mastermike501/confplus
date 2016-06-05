<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

use App\Http\Helpers\JSONUtilities;
use App\Http\Helpers\FormatUtilities;

class Billing extends Model
{
    private static $timecolumns = [
        'expiry_date' => 'd-m-Y'
    ];
    
    /**
     * [get]
     * @param  array  $data [description]
     * @return [JSON]       [description]
     */
    public static function get(array $data)
    {
        $results = DB::table('billings')
            ->where('email', $data['email'])
            ->where('card#', $data['card#'])
            ->get();

        return JSONUtilities::returnData($results);
    }
    
    public static function getInfo(array $data)
    {
        $results = DB::table('billings')
            ->where('email', $data['email'])
            ->get();

        return JSONUtilities::returnData($results);
    }
    
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
        
        $success = DB::table('billings')->insert($data);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Billing info successfully created.'));
        } else {
            return JSONUtilities::returnError('Could not insert billing info.');
        }
    }
    
    /**
     * [edit]
     * @param  [type] $primaryKey [description]
     * @param  array  $data       [description]
     * @return [type]             [description]
     */
    public static function edit($primaryKey, array $data)
    {
        $success = FormatUtilities::getDateTime(self::$timecolumns, $data);
        
        if (!$success) {
            return JSONUtilities::returnError(FormatUtilities::displayTimecolumnFormats(self::$timecolumns));
        }
        
        $success = DB::table('billings')
            ->where('email', $primaryKey['email'])
            ->where('card#', $primaryKey['card#'])
            ->update($data);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Billing info successfully updated.'));
        } else {
            return JSONUtilities::returnError('Could not update billing info.');
        }
    }
}
