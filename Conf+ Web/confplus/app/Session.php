<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

use App\Http\Helpers\JSONUtilities;
use App\Http\Helpers\FormatUtilities;

class Session extends Model
{
    private static $timecolumns = [
        'start_time' => 'd-m-Y H:i',
        'end_time' => 'd-m-Y H:i'
    ];
    
    /**
     * [get]
     * @param  array  $data [description]
     * @return [JSON]       [description]
     */
    public static function get(array $data)
    {
        $results = DB::table('sessions')
            ->where('event_id', $data['event_id'])
            ->where('title', $data['title'])
            ->get();

        if (count($results) == 0) {
            return JSONUtilities::returnError('No record exists');
        }

        //there must ever be only one instance of this record
        if (count($results) > 1) {
            return JSONUtilities::returnError('More than one record exists. Contact backend support.');
        }

        return JSONUtilities::returnData($results);
    }
    
    /**
     * [getSessions]
     * @param  array  $data [description]
     * @return [JSON]       [description]
     */
    public static function getSessions(array $data)
    {
        $results = DB::table('sessions')
            ->where('event_id', $data['event_id'])
            ->get();

        if (count($results) == 0) {
            return JSONUtilities::returnError('No record exists');
        }

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
        
        $success = DB::table('sessions')->insert($data);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Session successfully created.'));
        } else {
            return JSONUtilities::returnError('Could not insert session.');
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
        
        $success = DB::table('sessions')
            ->where('event_id', $primaryKey['event_id'])
            ->where('title', $primaryKey['title'])
            ->update($data);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Session successfully updated.'));
        } else {
            return JSONUtilities::returnError('Could not update session. ' .$success);
        }
    }
}
