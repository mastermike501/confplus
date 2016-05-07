<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

use DB;

use App\Http\Helpers\JSONUtilities;

class PaperTag extends Model
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
                'paper_id' => $data['paper_id'],
                'tag_name' => $tag
            ];
        }
        
        $success = DB::table('papers_tag')->insert($tagArray);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Paper tag successfully created.'));
        } else {
            return JSONUtilities::returnError('Could not insert paper tag.');
        }
    }
    
    public static function delete(array $data)
    {
        $tags = explode(',', $data['tag_names']);
        
        $tags = array_map(function ($item) {
            return trim($item);
        }, $tags);
        
        $success = DB::table('papers_tag')
            ->where('paper_id', $data['paper_id'])
            ->whereIn('tag_name', $tags)
            ->delete();
        
        return JSONUtilities::returnData(array('message' => 'Paper tags successfully deleted.'));
    }
    
    public static function getPaperTags(array $data) {
        $results = DB::table('paper_tag')
            ->where('paper_id', $data['paper_id'])
            ->get();

        if (count($results) == 0) {
            return JSONUtilities::returnError('No record exists');
        }
        
        $results = collect($results)->pluck('tag_name')->all();
        
        return JSONUtilities::returnData($results);
    }
}
