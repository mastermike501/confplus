<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

use App\Http\Helpers\JSONUtilities;

class EventAttended extends Model
{

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
