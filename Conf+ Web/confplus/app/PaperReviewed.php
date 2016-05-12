<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

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
        $updateData = array_only($data, ['comment', 'rate']);
        
        $rateAvg = DB::table('paper_reviewed')
            ->where('email', $data['email'])
            ->where('paper_id', $data['paper_id'])
            ->where('event_id', $data['event_id'])
            ->avg('rate');
            
        $tolerance = DB::table('events')
            ->select('review_tolerance')
            ->where('event_id', $data['event_id'])
            ->get();
        
        $exceedsTolerance = (abs(intval($data['rate']) - intval($rateAvg[0])) > intval($tolerance[0]));
        
        if ($exceedsTolerance) {
            $updateData['flag'] = '[system] [exceeeds tolerance]';
        }
        
        $success = DB::table('paper_reviewed')
            ->where('email', $data['email'])
            ->where('paper_id', $data['paper_id'])
            ->where('event_id', $data['event_id'])
            ->update($updateData);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Review successfully added.'));
        } else {
            return JSONUtilities::returnError('Could not add review.');
        }
    }
    
    public static function remove(array $data)
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
    
    public static function requestToReview(array $data)
    {
        $data['comment'] = '[system] [requesting]';
        
        $success = DB::table('paper_reviewed')->insert($data);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Request successfully added.'));
        } else {
            return JSONUtilities::returnError('Could not insert request.');
        }
    }
    
    public static function getRequestsToReview(array $data)
    {
        $query = DB::table('paper_reviewed')
            ->select('email')
            ->where('event_id', $data['event_id'])
            ->where('comment', '[system] [requesting]')
            ->get();

        if (array_key_exists('paper_id', $data)) {
            $query->where('paper_id', $data['paper_id']);
        }

        $results1 = $query->get();

        if (count($results1) == 0) {
            return JSONUtilities::returnError('No record exists');
        }
        
        $results1 = collect($results1)->flatten();
        
        $results2 = DB::table('users')
            ->whereIn('email', $results1)
            ->get();

        if (count($results2) == 0) {
            return JSONUtilities::returnError('No record exists');
        }

        return JSONUtilities::returnData($results2);
    }
    
    public static function acceptPaper(array $data) {
        $types = ['accepted', 'rejected', 'coi'];
        $data['accept'] = strtolower($data['accept']);
        
        if (!in_array($data['accept'], $types)) {
            return JSONUtilities::returnError('Accept must be "accepted", "rejected" or "coi".');
        }
        
        $comment = '[system] ' . $data['accept'];
        
        $success = DB::table('paper_reviewed')
            ->where('email', $data['email'])
            ->where('paper_id', $data['paper_id'])
            ->where('event_id', $data['event_id'])
            ->update('comment', $comment);
        
        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Acceptance successfully added.'));
        } else {
            return JSONUtilities::returnError('Could not insert acceptance.');
        }
    }
}
