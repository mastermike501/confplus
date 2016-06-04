<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

use App\Http\Helpers\JSONUtilities;
use App\Http\Helpers\FormatUtilities;

class Session extends Model
{
    private static $timecolumns = [
        'start_time' => 'd-m-Y H:i',
        'end_time' => 'd-m-Y H:i'
    ];
    
    /**
     * [get]
     * @param  array  $data [description]
     * @return [JSON]       [description]
     */
    public static function get(array $data)
    {
        $results = DB::table('sessions')
            ->where('event_id', $data['event_id'])
            ->where('title', $data['title'])
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
     * [getSessions]
     * @param  array  $data [description]
     * @return [JSON]       [description]
     */
    public static function getSessions(array $data)
    {
        $results = DB::table('sessions')
            ->where('event_id', $data['event_id'])
            ->get();

        if (count($results) == 0) {
            return JSONUtilities::returnError('No record exists');
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
        $success = FormatUtilities::getDateTime(self::$timecolumns, $data);
        
        if (!$success) {
            return JSONUtilities::returnError(FormatUtilities::displayTimecolumnFormats(self::$timecolumns));
        }
        
        $success = DB::table('sessions')->insert($data);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Session successfully created.'));
        } else {
            return JSONUtilities::returnError('Could not insert session.');
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
        $success = FormatUtilities::getDateTime(self::$timecolumns, $data);
        
        if (!$success) {
            return JSONUtilities::returnError(FormatUtilities::displayTimecolumnFormats(self::$timecolumns));
        }
        
        $success = DB::table('sessions')
            ->where('event_id', $primaryKey['event_id'])
            ->where('title', $primaryKey['title'])
            ->update($data);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Session successfully updated.'));
        } else {
            return JSONUtilities::returnError('Could not update session. ' .$success);
        }
    }
    
    public static function remove(array $data)
    {
        $success = DB::table('sessions')
            ->where('event_id', $data['event_id'])
            ->where('title', $data['title'])
            ->delete();
            
        if (!$success) {
            return JSONUtilities::returnError('Session does not exist.');
        } 
        
        return JSONUtilities::returnData(array('message' => 'Session successfully deleted.'));
    }
    
    public static function addConversation(array $data)
    {   
        $results1 = DB::table('sessions')
            ->select('title', 'speaker_email')
            ->where('event_id', $data['event_id'])
            ->where('title', $data['title'])
            ->get();
            
        //create conversation name
        $name = 'Discussion for session "' . $results1['title'] . '"';
        
        //create a conversation
        $id = DB::table('conversations')
            ->insertGetId(['name' => $name]);
        
        $results2 = DB::table('ticket_record')
            ->select('email')
            ->where('event_id', $data['event_id'])
            ->where('title', $data['title'])
            ->whereNotNull('email')
            ->get();
        
        $results2 = array_flatten($results2);
        
        //empty participant list
        $participants = [];
        
        //add speaker to list
        $participants[] = [
            'email' => $results1['speaker_email'],
            'conversation_id' => $id
        ];
        
        //add session attendees to the participants list
        foreach ($results2 as $email) {
            $participants[] = [
                'email' => $email,
                'conversation_id' => $id
            ];
        }
        
        //add participants to the conversation
        $success = DB::table('participants')
            ->insert($participants);

        if ($success) {
            return JSONUtilities::returnData(array('conversation_id' => $id));
        } else {
            return JSONUtilities::returnError('Could not insert conversation.');
        }
    }
    
    public static function getSessionForEvent(array $data) {
        $results = DB::table('sessions')
            ->where('event_id', $data['event_id'])
            ->where('is_event', 'true')
            ->get();
        
        return JSONUtilities::returnData($results);
    }
    
    public static function getEventEntryForEvent(array $data) {
        $results = DB::table('sessions')
            ->where('event_id', $data['event_id'])
            ->where('is_event', 'false')
            ->get();
        
        return JSONUtilities::returnData($results);
    }
    
    public static function getSessionForEventByUser(array $data) {
        $results1 = DB::table('ticket_record')
            ->select('title')
            ->where('event_id', $data['event_id'])
            ->where('email', $data['email'])
            ->get();
        
        $titles = array_flatten($results1);
        
        $results2 = DB::table('sessions')
            ->where('event_id', $data['event_id'])
            ->where('is_event', 'true')
            ->get();
        
        $results3 = array_map(function($item) use ($titles) {
            if (in_array($item['title'], $titles)) {
                $item['user_attending'] = 'true';
            } else {
                $item['user_attending'] = 'false';
            }
            return $item;
        }, $results2);
        
        return JSONUtilities::returnData($results3);
    }
}
