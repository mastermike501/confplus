<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

use App\Http\Helpers\JSONUtilities;

class SessionAttended extends Model
{
    /**
     * [get]
     * @param  array  $data [description]
     * @return [JSON]       [description]
     */
    public static function get(array $data)
    {
        $results = DB::table('session_attended')
            ->where('event_id', $data['event_id'])
            ->where('title', $data['title'])
            ->where('speaker_email', $data['speaker_email'])
            ->get();

        return JSONUtilities::returnData($results);
    }
}
