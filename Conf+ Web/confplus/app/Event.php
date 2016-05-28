<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

use DB;
use Storage;

use App\Http\Helpers\JSONUtilities;
use App\Http\Helpers\FormatUtilities;

class Event extends Model
{
    private static $timecolumns = [
        'from_date' => 'd-m-Y H:i',
        'to_date' => 'd-m-Y H:i',
        'paper_deadline' => 'd-m-Y H:i'
    ];

    /**
     * [get]
     * @param  [array] $data [Event data containing an event_id]
     * @return [JSON]        [A JSON string containing a success or error body]
     */
    public static function get(array $data) {
        $results = DB::select('select * from events where event_id = ?', [$data['event_id']]);

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
     * [create]
     * @param  [array] $data [Event data containing a new event]
     * @return [JSON]       [A JSON string containing a success or error body]
     */
    public static function insert(array $data) {
        $success = FormatUtilities::getDateTime(self::$timecolumns, $data);

        if (!$success) {
            return JSONUtilities::returnError(FormatUtilities::displayTimecolumnFormats(self::$timecolumns));
        }
        
        $manager = $data['email'];
        unset($data['email']);

        $id = DB::table('events')->insertGetId($data);
        
        DB::table('event_roles')
            ->insert([
                'email' => $manager,
                'event_id' => $id,
                'role_name' => 'manager'
            ]);
       
       return JSONUtilities::returnData(array('id' => $id));
    }

    /*
     * [edit]
     * @param  [number] $primaryKey [event primary key]
     * @param  [array] $data [Event data to update]
     * @return [JSON]       [A JSON string containing a success or error body]
     */
    public static function edit($primaryKey, array $data) {

        $success = FormatUtilities::getDateTime(self::$timecolumns, $data);

        if (!$success) {
            return JSONUtilities::returnError(FormatUtilities::displayTimecolumnFormats(self::$timecolumns));
        }
        
        $success = DB::table('events')
            ->where('event_id', $primaryKey['event_id'])
            ->update($data);
       
        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Event successfully updated.'));
        } else {
            return JSONUtilities::returnError('Could not update event.');
        }
    }

    public static function remove(array $data)
    {
        $success = DB::table('event')
            ->where('event_id', $data['event_id'])
            ->delete();
            
        if (!$success) {
            return JSONUtilities::returnError('Event does not exist.');
        } 
        
        $localStorage = Storage::disk('local');

        $posterPath = 'posters/' . 'poster_' . $data['event_id'] . '.txt';

        if ($localStorage->exists($posterPath)) {
            $localStorage->delete($posterPath);
        }
        
        return JSONUtilities::returnData(array('message' => 'Paper successfully deleted.'));
    }

    /**
     * [uploadPoster]
     * @param  array  $data [Poster data to upload]
     * @return [JSON]       [A JSON string containing a success or error body]]
     */
    public static function uploadPoster(array $data) {
        // return JSONUtilities::returnError('uploadPoster not implemented');
    
        $localStorage = Storage::disk('local');
    
        //example path: posters/poster_628.txt
        $path = 'posters/' . 'poster_' . $data['event_id'] . '.txt';
    
        //remove an earlier version of poster, if exists
        if ($localStorage->exists($path)) {
            $localStorage->delete($path);
        }
    
        $success = $localStorage->put($path, $data['poster_data_url']);
    
        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Event poster successfully uploaded.'));
        } else {
            return JSONUtilities::returnError('Could not upload event poster.');
        }
    }
    
    /**
     * [getPoster]
     * @param  array  $data [Poster data to retrieve]
     * @return [JSON]       [A JSON string containing a success or error body]]
     */
    public static function getPoster(array $data) {
        $localStorage = Storage::disk('local');
    
        //example path: posters/poster_628.txt
        $path = 'posters/' . 'poster_' . $data['event_id'] . '.txt';
    
        //return an error if poster is not found
        if (!$localStorage->exists($path)) {
            return JSONUtilities::returnError('Could not find event poster.');
        }
    
        $dataUrl = $localStorage->get($path);
    
        return JSONUtilities::returnData(array('poster_data_url' => $dataUrl));
    }

    /**
     * [getByTag]
     * @param  array  $data [description]
     * @return [JSON]       [description]
     */
    public static function getByTag(array $data) {
        $results = DB::table('events')
            ->join('events_tag', 'events.event_id', '=', 'events_tag.event_id')
            ->where('tag_name', $data['tag_name'])
            ->get();

        return JSONUtilities::returnData($results);
    }
    
    public static function getUpcomingByCountry(array $data) {
        $localStorage = Storage::disk('local');
        
        $results1 = DB::table('venues')
            ->select('venue_id')
            ->distinct()
            ->where('country', $data['country'])
            ->get();
        
        if (count($results1) == 0) {
            return JSONUtilities::returnError('No such venues exist in this country');
        }
        
        //put results into a single dimension array
        $results1 = collect($results1)->flatten();
        
        $results2 = DB::table('events')
            ->whereIn('venue_id', $results1)
            ->where('to_date', '>', DB::raw('CURRENT_TIMESTAMP'))
            ->get();

        if (count($results2) == 0) {
            return JSONUtilities::returnError('No record exists');
        }
        
        $results2 = array_map(function($event) use ($localStorage) {
            $path = 'posters/' . 'poster_' . $event['event_id'] . '.txt';
        
            //return an error if poster is not found
            if ($localStorage->exists($path)) {
                $event['poster_data_url'] = $localStorage->get($path);
            } else {
                $event['poster_data_url'] = null;
            }
            
            $eventTags = DB::table('events_tag')
                ->select('tag_name')
                ->where('event_id', $event['event_id'])
                ->get();
            
            $event['tags'] = array_flatten($eventTags);
            
            return $event;
            
        }, $results2);
        
        return JSONUtilities::returnData($results2);
    }

    public static function getByKeyword(array $data) {
        
        $keyword = '%' . $data['keyword'] . '%';
        
        $results = DB::table('events')
            ->where('name', 'like', $keyword)
            ->orWhere('description', 'like', $keyword)
            ->get();

        return JSONUtilities::returnData($results);
    }
}
