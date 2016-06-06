<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

use App\Http\Helpers\JSONUtilities;

class PaperSubmitted extends Model
{
    public static function insert(array $data) {
        $data['status'] = 'unassigned';
        
        $success = DB::table('paper_submitted')->insert($data);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Paper submission successfully created.'));
        } else {
            return JSONUtilities::returnError('Could not insert paper submission.');
        }
    }
    
    public static function edit(array $data) {
        $data1 = array_except($data, ['paper_id', 'event_id']);
        
        $success = DB::table('paper_submitted')
            ->where('paper_id', $data['paper_id'])
            ->where('event_id', $data['event_id'])
            ->update($data1);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Paper submission successfully updated.'));
        } else {
            return JSONUtilities::returnError('Could not update paper submission.');
        }
    }
    
    public static function get(array $data) {
        $results = DB::table('paper_submitted')
            ->where('paper_id', $data['paper_id'])
            ->where('event_id', $data['event_id'])
            ->get();

        return JSONUtilities::returnData($results);
    }
    
    public static function getBestPaperForEvent(array $data) {
        $results = DB::table('paper_submitted')
            ->where('event_id', $data['event_id'])
            ->where('best_paper', 'true')
            ->get();
            
        return JSONUtilities::returnData($results);
    }
}
