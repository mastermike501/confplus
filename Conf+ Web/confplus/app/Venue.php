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

        //there must ever be only one instance of this record
        if (count($results) != 1) {
            return JSONUtilities::returnError('More than one record exists. Contact backend support.');
        }
        // if (count($results) != 1) {
        //     return JSONUtilities::returnError('More than one record exists. Contact backend support.');
        // }

        return JSONUtilities::returnData($results);
    }

    public static function insert(array $data)
    {
        $success = DB::table('venues')->insert($data);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Venue successfully created.'));
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
}
