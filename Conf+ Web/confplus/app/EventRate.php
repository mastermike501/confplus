<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

use App\Http\Helpers\JSONUtilities;

class EventRate extends Model
{
    public static function insert(array $data)
    {
        if ($data['rate'] < 1 || $data['rate'] > 5) {
            return JSONUtilities::returnError('Rate must be between 1 and 5 inclusive.');
        }
        
        $success = DB::table('event_rate')->insert($data);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Rating successfully created.'));
        } else {
            return JSONUtilities::returnError('Could not insert rating.');
        }
    }
    
    public static function get(array $data)
    {
        $results = DB::table('event_rate')
            ->select(DB::raw('rate, COUNT(1) AS `count`'))
            ->where('event_id', $data['event_id'])
            ->groupBy('rate')
            ->get();

        return JSONUtilities::returnData($results);
    }
}
