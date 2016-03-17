<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;
use Storage;

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
        // if (count($results) != 1) {
        //     return JSONUtilities::returnError('More than one record exists. Contact backend support.');
        // }

        return JSONUtilities::returnData($results);
    }

    /**
     * [create]
     * @param  [array] $data [Event data containing a new event]
     * @return [JSON]       [A JSON string containing a success or error body]
     */
    public static function insert(array $data) {
        $success = DB::table('events')->insert($data);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Event successfully created.'));
        } else {
            return JSONUtilities::returnError('Could not insert event.');
        }
    }

    /**
     * [edit]
     * @param  [number] $primaryKey [event primary key]
     * @param  [array] $data [Event data to update]
     * @return [JSON]       [A JSON string containing a success or error body]
     */
    public static function edit($primaryKey, array $data) {
        $success = DB::table('events')
            ->where('event_id', $primaryKey)
            ->update($data);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Event successfully updated.'));
        } else {
            return JSONUtilities::returnError('Could not update event.');
        }
    }

    /**
     * [uploadPoster]
     * @param  array  $data [Poster data to upload]
     * @return [JSON]       [A JSON string containing a success or error body]]
     */
    public static function uploadPoster(array $data) {
        $localStorage = Storage::disk('local');

        //example path: posters/poster_628.txt
        $posterPath = 'posters/' . 'poster_' . $data['event_id'] . '.txt';

        //remove an earlier version of poster, if exists
        if ($localStorage->exists($posterPath)) {
            $localStorage->delete($posterPath);
        }

        $success = $localStorage->put($posterPath, $data['poster_data_url']);

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
        $posterPath = 'posters/' . 'poster_' . $data['event_id'] . '.txt';

        //return an error if poster is not found
        if (!$localStorage->exists($posterPath)) {
            return JSONUtilities::returnError('Could not find event poster.');
        }

        $dataUrl = $localStorage->get($posterPath);

        return JSONUtilities::returnData(array('data_url' => $dataUrl));
    }

}
