<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

use App\Http\Helpers\JSONUtilities;

class Room extends Model
{
    /**
     * [get]
     * @param  array  $data [description]
     * @return [JSON]       [description]
     */
    public static function get(array $data)
    {
        $results = DB::table('rooms')
            ->where('venue_id', $data['venue_id'])
            ->where('name', $data['name'])
            ->get();

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
     * [getRooms]
     * @param  array  $data [description]
     * @return [JSON]       [description]
     */
    public static function getRooms(array $data)
    {
        $results = DB::table('rooms')
            ->where('venue_id', $data['venue_id'])
            ->get();

        if (count($results) == 0) {
            return JSONUtilities::returnError('No record exists');
        }

        return JSONUtilities::returnData($results);
    }

    /**
     * [insert]
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    public static function insert(array $data)
    {
        $success = DB::table('rooms')->insert($data);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Room successfully created.'));
        } else {
            return JSONUtilities::returnError('Could not insert room.');
        }
    }

    /**
     * [edit]
     * @param  [type] $primaryKey [description]
     * @param  array  $data       [description]
     * @return [type]             [description]
     */
    public static function edit($primaryKey, array $data)
    {
        $success = DB::table('rooms')
            ->where('venue_id', $primaryKey['venue_id'])
            ->where('name', $primaryKey['name'])
            ->update($data);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Room successfully updated.'));
        } else {
            return JSONUtilities::returnError('Could not update room.');
        }
    }
    
    public static function remove(array $data)
    {
        $success = DB::table('rooms')
            ->where('venue_id', $data['venue_id'])
            ->where('name', $data['room_name'])
            ->delete();
            
        if (!$success) {
            return JSONUtilities::returnError('Room does not exist.');
        } 
        
        return JSONUtilities::returnData(array('message' => 'Room successfully deleted.'));
    }
}
