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
        $tags = explode(',', $data['tag_names']);
        
        $tagArray = [];
        
        foreach ($tags as $tag) {
            $tagArray[] = [
                'event_id' => $data['event_id'],
                'tag_name' => $tag
            ];
        }
        
        $success = DB::table('events_tag')->insert($tagArray);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Event tags successfully created.'));
        } else {
            return JSONUtilities::returnError('Could not insert event tags.');
        }
    }
    
    public static function remove(array $data)
    {
        $tags = explode(',', $data['tag_names']);
        
        $tags = array_map(function ($item) {
            return trim($item);
        }, $tags);
        
        $tags = collect($tags)->flatten();
        
        $success = DB::table('events_tag')
            ->where('event_id', $data['event_id'])
            ->whereIn('tag_name', $tags)
            ->delete();
        
        return JSONUtilities::returnData(array('message' => 'Event tags successfully deleted.'));
    }
    
    public static function getEventTags(array $data) {
        $results = DB::table('events_tag')
            ->where('event_id', $data['event_id'])
            ->get();

        if (count($results) == 0) {
            return JSONUtilities::returnError('No record exists');
        }
        
        $results = collect($results)->pluck('tag_name')->all();
        
        return JSONUtilities::returnData($results);
    }
}
