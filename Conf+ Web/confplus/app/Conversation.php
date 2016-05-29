<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

use DB;
use Storage;

use App\Http\Helpers\JSONUtilities;

class Conversation extends Model
{
    public static function insert(array $data)
    {   
        //if there is no given name for the conversation
        if (!array_key_exists('name', $data)) {
            $name = 'New conversation';
        } else {
            $name = $data['name'];
        }
        
        $id = DB::table('conversations')
            ->insertGetId(['name' => $name]);
        
        $emails = explode(',', $data['emails']);
        
        if (count($emails) < 2) {
            return JSONUtilities::returnError('A conversation requires 2 or more users.');
        }
        
        $participants = [];
        
        foreach ($emails as $email) {
            $participants[] = [
                'email' => trim($email),
                'conversation_id' => $id
            ];
        }
        
        $success = DB::table('participants')
            ->insert($participants);

        if ($success) {
            return JSONUtilities::returnData(array('id' => $id));
        } else {
            return JSONUtilities::returnError('Could not insert conversation.');
        }
    }
    
    public static function get(array $data) {
       $results = DB::table('messages')
            ->where('conversation_id', $data['conversation_id'])
            ->orderBy('date')
            ->get();
        
        if (count($results) == 0) {
            return JSONUtilities::returnError('No messages in this conversation');
        }

        return JSONUtilities::returnData($results);
    }
    
    public static function getByUser(array $data) {
        $results1 = DB::table('participants')
            ->select('conversation_id')
            ->where('email', $data['email'])
            ->get();
            
        if (count($results1) == 0) {
            return JSONUtilities::returnError('No conversations for this user');
        }
        
        $results1 = array_flatten($results1);
        
        $latestMessages = DB::raw('(
            SELECT a.* FROM `messages` a 
                LEFT JOIN `messages` b
                    ON a.conversation_id = b.conversation_id AND a.date < b.date
                WHERE b.date IS NULL AND a.conversation_id IN (' . implode(',', $results1) . ')
        ) LatestMessages');
        
        $results2 = DB::table('conversations')
            ->join($latestMessages, function($join) {
                $join->on('conversations.conversation_id', '=', 'LatestMessages.conversation_id');
            })
            ->get();

        return JSONUtilities::returnData($results2);
    }
    
    public static function removeUser(array $data) {
        $success = DB::table('participants')
            ->where('conversation_id', $data['conversation_id'])
            ->where('email', $data['email'])
            ->delete();
            
        if (!$success) {
            return JSONUtilities::returnError('Conversation or user does not exist.');
        } 
        
        return JSONUtilities::returnData(array('message' => 'User removed from conversation.'));
    }
    
    public static function getConversationParticipants(array $data) {
        $results1 = DB::table('participants')
            ->select('email')
            ->where('conversation_id', $data['conversation_id'])
            ->get();
            
        if (count($results1) == 0) {
            return JSONUtilities::returnError('No participants in this conversation');
        }
        
        $results1 = array_flatten($results1);
        
        $results2 = DB::table('users')
            ->whereIn('email', $results1)
            ->get();
        
        $localStorage = Storage::disk('local');
    
        foreach ($results2 as &$user) {
            $path = 'profile_images/' . 'profile_image_' . $user['email'] . '.txt';
    
            if ($localStorage->exists($path)) {
                $user['image_data_url'] = $localStorage->get($path);
            } else {
                $user['image_data_url'] = null;
            }
        }
        
        unset($user);
        
        return JSONUtilities::returnData($results2);
    }
    
    public static function getConversationsByUserForEvent(array $data) {
        
        $results1 = DB::table('participants')
            ->select('conversation_id')
            ->where('email', $data['email'])
            ->get();

        $results1 = array_flatten($results1);
        
        $latestMessages = DB::raw('(
            SELECT a.* FROM `messages` a 
                LEFT JOIN `messages` b
                    ON a.conversation_id = b.conversation_id AND a.date < b.date
                WHERE b.date IS NULL AND a.conversation_id IN (' . implode(',', $results1) . ')
        ) LatestMessages');
        
        $results2 = DB::table('conversations')
            ->join($latestMessages, function($join) {
                $join->on('conversations.conversation_id', '=', 'LatestMessages.conversation_id');
            })
            ->where('conversations.event_id', $data['event_id'])
            ->get();

        return JSONUtilities::returnData($results2);
    }
}

// DB::table('participants')->join('conversations', 'participants.conversation_id', '=', 'conversations.conversation_id')->join('sessions', function($join) use ($data) {$join->on('conversations.conversation_id', '=', 'sessions.conversation_id')->where('sessions.event_id', $data['event_id'])->whereNotNull('sessions.conversation_id');})->where('participants.email', $data['email'])->select('conversations.conversation_id as conversation_id', 'conversations.name as name')->get();