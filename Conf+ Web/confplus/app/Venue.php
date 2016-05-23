<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;
use Storage;

use App\Http\Helpers\JSONUtilities;

class Venue extends Model
{
    public static function addVenueMap(array $data) {
        $localStorage = Storage::disk('local');
    
        $path = 'venue_maps/' . 'venue_map_' . $data['venue_id'] . '.txt';
    
        if ($localStorage->exists($path)) {
            $localStorage->delete($path);
        }
    
        $success = $localStorage->put($path, $data['image_data_url']);
    
        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Venue map successfully uploaded.'));
        } else {
            return JSONUtilities::returnError('Could not upload venue map.');
        }
    }
    
    public static function getVenueMap(array $data) {
        $localStorage = Storage::disk('local');
    
        $path = 'venue_maps/' . 'venue_map_' . $data['venue_id'] . '.txt';
    
        if (!$localStorage->exists($path)) {
            return JSONUtilities::returnError('Could not find venue map.');
        }
    
        $dataUrl = $localStorage->get($path);
    
        return JSONUtilities::returnData(array('image_data_url' => $dataUrl));
    }
    
    /**
     * [get]
     * @param  array  $data [description]
     * @return [JSON]       [description]
     */
    public static function get(array $data)
    {
        $results = DB::select('select * from venues where venue_id = ?', [$data['venue_id']]);

        if (count($results) == 0) {
            return JSONUtilities::returnError('No record exists');
        }

        //there must ever be only one instance of this record
        if (count($results) > 1) {
            return JSONUtilities::returnError('More than one record exists. Contact backend support.');
        }

        return JSONUtilities::returnData($results);
    }

    public static function insert(array $data)
    {
        $id = DB::table('venues')->insertGetId($data);

        return JSONUtilities::returnData(array('id' => $id));
    }

    public static function edit($primaryKey, array $data)
    {
        $success = DB::table('venues')
            ->where('venue_id', $primaryKey)
            ->update($data);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Venue successfully updated.'));
        } else {
            return JSONUtilities::returnError('Could not update venue.');
        }
    }
    
    public static function remove(array $data)
    {
        $success = DB::table('venues')
            ->where('venue_id', $data['venue_id'])
            ->delete();
            
        if (!$success) {
            return JSONUtilities::returnError('Venue does not exist.');
        } 
        
        return JSONUtilities::returnData(array('message' => 'Venue successfully deleted.'));
    }
    
    public static function getByLocation(array $data)
    {
        $query = DB::table('venues')
            ->where('country', $data['country']);
        
        if (array_key_exists('state', $data)) {
            $query->where('state', $data['state']);
        }
        if (array_key_exists('city', $data)) {
            $query->where('city', $data['city']);
        }
        
        $results = $query->get();

        if (count($results) == 0) {
            return JSONUtilities::returnError('No record exists');
        }

        return JSONUtilities::returnData($results);
    }
    
    public static function getAvailableRooms(array $data)
    {
        $results = DB::table('rooms')
            ->whereNotExists(function ($query) use ($data) {
                $query->select(DB::raw(1))
                    ->from('sessions')
                    ->where('sessions.start_date', '>=', $data['from_date'])
                    ->where('sessions.end_date', '<=', $data['to_date'])
                    ->where('rooms.venue_id', 'sessions.venue_id')
                    ->where('rooms.name', 'sessions.room_name');
            })
            ->get();

        if (count($results) == 0) {
            return JSONUtilities::returnError('No rooms are available at the given time period.');
        }

        return JSONUtilities::returnData($results);
    }
}
