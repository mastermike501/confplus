<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

use App\Http\Helpers\JSONUtilities;

class Seat extends Model
{
    /**
     * [insert]
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    public static function insert(array $data)
    {
        $success = DB::table('seats')->insert($data);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Seat successfully created.'));
        } else {
            return JSONUtilities::returnError('Could not insert seat.');
        }
    }
    
    public static function insertSeats(array $data)
    {
        $seats = explode(',', $data['seat_nums']);
        
        $seats = array_map(function ($seat_num) use ($data) {
            return [
                'venue_id' => $data['venue_id'],
                'room_name' => $data['name'],
                'seat_num' => trim($seat_num)
            ];
        }, $seats);
        
        $success = DB::table('seats')->insert($seats);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Seats successfully created.'));
        } else {
            return JSONUtilities::returnError('Could not insert seats.');
        }
    }
    
    /**
     * [getSeatsInRoom]
     * @param  array  $data [description]
     * @return [JSON]       [description]
     */
    public static function getSeatsInRoom(array $data)
    {
        $results = DB::table('seats')
            ->where('venue_id', $data['venue_id'])
            ->where('name', $data['name'])
            ->get();

        if (count($results) == 0) {
            return JSONUtilities::returnError('No record exists');
        }

        return JSONUtilities::returnData($results);
    }
}
