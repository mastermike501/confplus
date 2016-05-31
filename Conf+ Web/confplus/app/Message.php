<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;
use Carbon\Carbon;

use App\Http\Helpers\JSONUtilities;

class Message extends Model
{
    /**
     * [insert]
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    public static function insert(array $data)
    {
        $format = 'Y-m-d H:i';
        $data['date'] = Carbon::createFromFormat($format, gmdate($format));
        
        $success = DB::table('messages')->insert($data);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Message successfully created.'));
        } else {
            return JSONUtilities::returnError('Could not insert message.');
        }
    }
    
    public static function getLatest(array $data) {
        $results = DB::select(
            DB::raw('
            SELECT a.* FROM `messages` a 
                LEFT JOIN `messages` b
                    ON a.conversation_id = b.conversation_id AND a.date < b.date
            WHERE b.date IS NULL AND a.conversation_id = ' . $data['conversation_id']
            )
        );
        
        return JSONUtilities::returnData($results[0]);
    }
}