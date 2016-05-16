<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

use DB;

use App\Http\Helpers\JSONUtilities;

class EventRole extends Model
{
    public static function get(array $data) {
        $results = DB::select('select * from event_roles where event_id = ?', [$data['event_id']]);

        if (count($results) == 0) {
            return JSONUtilities::returnError('No record exists');
        }

        return JSONUtilities::returnData($results);
    }
    
    public static function insert(array $data) {
        $success = DB::table('event_roles')->insert($data);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Event role successfully created.'));
        } else {
            return JSONUtilities::returnError('Could not insert event role.');
        }
    }
    
    public static function getEventsManaged(array $data) {
        $results1 = DB::table('event_roles')
            ->select('event_id')
            ->where('email', $data['email'])
            ->where('role_name', 'manager')
            ->get();
        
        if (count($results1) == 0) {
            return JSONUtilities::returnError('No results');
        }
        
        //put results into a single dimension array
        $results1 = collect($results1)->flatten();
        
        $results2 = DB::table('events')
            ->whereIn('event_id', $results1)
            ->get();

        if (count($results2) == 0) {
            return JSONUtilities::returnError('No record exists');
        }

        return JSONUtilities::returnData($results2);
    }
    
    public static function edit(array $data) {
        $success = DB::table('event_roles')
            ->where('email', $data['email'])
            ->where('event_id', $data['event_id'])
            ->update([
                'role_name' => $data['role_name']
            ]);
        
        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Event role successfully edited.'));
        } else {
            return JSONUtilities::returnError('Could not edit event role.');
        }
    }
}
