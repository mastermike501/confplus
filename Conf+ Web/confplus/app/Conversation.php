<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

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
        
        if (count($results1) == 0) {
            return JSONUtilities::returnError('No messages in this conversation');
        }

        return JSONUtilities::returnData($results);
    }
    
    public static function getByUser(array $data) {
       $results = DB::table('participants')
            ->where('email', $data['email'])
            ->get();
        
        if (count($results1) == 0) {
            return JSONUtilities::returnError('No conversations for this user');
        }

        return JSONUtilities::returnData($results);
    }
}
