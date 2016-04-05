<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

use App\Http\Helpers\JSONUtilities;

class EventAttended extends Model
{
    /**
     * [get]
     * @param  array  $data [description]
     * @return [JSON]       [description]
     */
    public static function get(array $data)
    {
        $results = DB::table('event_attended')
            ->where('event_id', $data['event_id'])
            ->get();

        return JSONUtilities::returnData($results);
    }

    /**
     * [insert]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public static function insert($data) {
        $success = DB::table('event_attended')->insert($data);

        if ($success) {
            return array('message' => 'Event attended successfully created.');
        } else {
            return JSONUtilities::returnError('Could not insert event attended.');
        }
    }


}
