<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

use App\Http\Helpers\JSONUtilities;

class Conversation extends Model
{
    public static function get(array $data) {
       $results = DB::table('messages')
            ->where('conversation_id', $data['conversation_id'])
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
