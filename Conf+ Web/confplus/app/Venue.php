<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

use App\Http\Helpers\JSONUtilities;

class Venue extends Model
{
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

        if ($success) {
            return JSONUtilities::returnData(array('id' => $id));
        } else {
            return JSONUtilities::returnError('Could not insert venue.');
        }
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
    
    public static function delete(array $data)
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
    
}
