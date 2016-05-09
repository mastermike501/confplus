<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

use DB;

use App\Http\Helpers\JSONUtilities;

class UserTag extends Model
{
    /**
     * [create]
     * @param  [array] $data [Event data containing a new event]
     * @return [JSON]       [A JSON string containing a success or error body]
     */
    public static function insert(array $data) {
        $tags = explode(',', $data['tag_names']);
        
        $tagArray = [];
        
        foreach ($tags as $tag) {
            $tagArray[] = [
                'email' => $data['email'],
                'tag_name' => $tag
            ];
        }
        
        $success = DB::table('users_tag')->insert($tagArray);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'User tags successfully created.'));
        } else {
            return JSONUtilities::returnError('Could not insert user tags.');
        }
    }
    
    public static function remove(array $data)
    {
        $tags = explode(',', $data['tag_names']);
        
        $tags = array_map(function ($item) {
            return trim($item);
        }, $tags);
        
        $success = DB::table('users_tag')
            ->where('email', $data['email'])
            ->whereIn('tag_name', $tags)
            ->delete();
        
        return JSONUtilities::returnData(array('message' => 'User tags successfully deleted.'));
    }
    
    public static function getUserTags(array $data) {
        $results = DB::table('users_tag')
            ->where('email', $data['email'])
            ->get();

        if (count($results) == 0) {
            return JSONUtilities::returnError('No record exists');
        }
        
        $results = collect($results)->pluck('tag_name')->all();
        
        return JSONUtilities::returnData($results);
    }
}
