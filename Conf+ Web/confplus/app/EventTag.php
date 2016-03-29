<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

use App\Http\Helpers\JSONUtilities;

class EventTag extends Model
{
    /**
     * [create]
     * @param  [array] $data [Event data containing a new event]
     * @return [JSON]       [A JSON string containing a success or error body]
     */
    public static function insert(array $data) {
        $success = DB::table('events_tag')->insert($data);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Event tag successfully created.'));
        } else {
            return JSONUtilities::returnError('Could not insert event tag.');
        }
    }
}
