<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

use App\Http\Helpers\JSONUtilities;

class PaperReviewed extends Model
{
    public static function addReviewer(array $data)
    {
        $data['comment'] = '[system] [unreviewed]';
        
        $success = DB::table('paper_reviewed')->insert($data);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Reviewer successfully added.'));
        } else {
            return JSONUtilities::returnError('Could not insert reviewer.');
        }
    }
    
    public static function addReview(array $data)
    {
        $success = DB::table('paper_reviewed')
            ->where('email', $data['email'])
            ->where('paper_id', $data['paper_id'])
            ->where('event_id', $data['event_id'])
            ->update($data);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Review successfully added.'));
        } else {
            return JSONUtilities::returnError('Could not add review.');
        }
    }
    
    public static function delete(array $data)
    {
        $success = DB::table('paper_reviewed')
            ->where('email', $data['email'])
            ->where('paper_id', $data['paper_id'])
            ->where('event_id', $data['event_id'])
            ->delete();

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Paper reviewed successfully deleted.'));
        } else {
            return JSONUtilities::returnError('Could not delete paper reviewed.');
        }
    }
}
