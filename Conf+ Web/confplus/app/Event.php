<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

use App\Http\Helpers\JSONUtilities;

class Event extends Model
{
    /**
     * [get]
     * @param  [array] $data [Event data containing an event_id]
     * @return [JSON]        [A JSON string containing a success or error body]
     */
    public static function get(array $data) {
        $results = DB::select('select * from events where event_id = ?', [$data['event_id']]);

        //there must ever be only one instance of this record
        if (count($results) != 1) {
            return JSONUtilities::returnError('More than one record exists. Contact backend support.');
        }

        return JSONUtilities::returnData($results);
    }

    /**
     * [create]
     * @param  [array] $data [User data containing event data]
     * @return [JSON]       [A JSON string containing a success or error body]
     */
    public static function insert(array $data) {
        // var_dump($data);
    }

    /**
     * [edit]
     * @param  [array] $data [Event data to update]
     * @return [JSON]       [A JSON string containing a success or error body]
     */
    public static function edit(array $data) {
        // var_dump($data);
    }

}
