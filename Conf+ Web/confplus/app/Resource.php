<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

use App\Http\Helpers\JSONUtilities;

class Resource extends Model
{
    /**
     * [get]
     * @param  array  $data [description]
     * @return [JSON]       [description]
     */
    public static function get(array $data)
    {
        $results = DB::table('resources')
            ->where('venue_id', $data['venue_id'])
            ->where('room_name', $data['room_name'])
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
     * [insert]
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    public static function insert(array $data)
    {
        $success = DB::table('resources')->insert($data);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Resource successfully created.'));
        } else {
            return JSONUtilities::returnError('Could not insert resource.');
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
        $success = DB::table('resources')
            ->where('venue_id', $primaryKey['venue_id'])
            ->where('room_name', $primaryKey['room_name'])
            ->where('name', $primaryKey['name'])
            ->update($data);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Resource successfully updated.'));
        } else {
            return JSONUtilities::returnError('Could not update resource.');
        }
    }

    public static function remove(array $data)
    {
        $success = DB::table('resource')
            ->where('venue_id', $data['venue_id'])
            ->where('room_name', $data['room_name'])
            ->where('name', $data['name'])
            ->delete();
            
        if (!$success) {
            return JSONUtilities::returnError('Resource does not exist.');
        } 
        
        return JSONUtilities::returnData(array('message' => 'Resource successfully deleted.'));
    }

    /**
     * [getByRoom]
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    public static function getByRoom(array $data)
    {
        $results = DB::table('resources')
            ->where('venue_id', $data['venue_id'])
            ->where('room_name', $data['room_name'])
            ->get();

        return JSONUtilities::returnData($results);
    }
}
