<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

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
    
    public static function getEventTags(array $data) {
        $results = DB::table('event_tag')
            ->where('event_id', $data['event_id'])
            ->get();

        if (count($results) == 0) {
            return JSONUtilities::returnError('No record exists');
        }
        
        $results = collect($results)->pluck('tag_name')->all();
        
        return JSONUtilities::returnData($results);
    }
}
