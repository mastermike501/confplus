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
        
        $comment = '[system] [' . $data['accept'] . ']';
        
        $success = DB::table('paper_reviewed')
            ->where('email', $data['email'])
            ->where('paper_id', $data['paper_id'])
            ->where('event_id', $data['event_id'])
            ->update(['comment' => $comment]);
        
        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Acceptance successfully added.'));
        } else {
            return JSONUtilities::returnError('Could not insert acceptance.');
        }
    }
    
    public static function addConversation(array $data)
    {   
        //get the emails of the paper
        $results1 = DB::table('paper_authored')
            ->select('email')
            ->where('paper_id', $data['paper_id'])
            ->get();
        
        //check if any results were returned
        if (count($results1) == 0) {
            return JSONUtilities::returnError('No paper exists.');
        }
        
        //flatten into 1D array
        $results1 = array_flatten($results1);
        
        $title = DB::table('papers')
            ->select('title')
            ->where('paper_id', $data['paper_id'])
            ->get();
        
        //create conversation name
        $name = 'Discussion for paper "' . $title['title'] . '"';
        
        //create a conversation
        $id = DB::table('conversations')
            ->insertGetId(['name' => $name]);
        
        //empty participant list
        $participants = [];
        
        //add authors to the participants list
        foreach ($results1 as $email) {
            $participants[] = [
                'email' => $email,
                'conversation_id' => $id
            ];
        }
        
        //add moderator and reviewer to the partipant list
        $participants[] = [
            'email' => $data['moderator'],
            'conversation_id' => $id
        ];
        $participants[] = [
            'email' => $data['reviewer'],
            'conversation_id' => $id
        ];
        
        //add participants to the conversation
        $success = DB::table('participants')
            ->insert($participants);

        $success = DB::table('paper_reviewed')
            ->where('email', $data['reviewer'])
            ->where('paper_id', $data['paper_id'])
            ->where('event_id', $data['event_id'])
            ->update([
                'conversation_id' => $id
            ]);

        if ($success) {
            return JSONUtilities::returnData(array('conversation_id' => $id));
        } else {
            return JSONUtilities::returnError('Could not insert conversation.');
        }
    }
}
