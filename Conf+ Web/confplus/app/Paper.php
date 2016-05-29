<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;
use Storage;

use App\Http\Helpers\JSONUtilities;
use App\Http\Helpers\FormatUtilities;

class Paper extends Model
{
    private static $timecolumns = [
        'publish_date' => 'd-m-Y',
        'latest_submit_date' => 'd-m-Y'
    ];

    /**
     * [get]
     * @param  array  $data [description]
     * @return [JSON]       [description]
     */
    public static function getPaperDetails(array $data)
    {
        $results = DB::table('papers')
            ->where('paper_id', $data['paper_id'])
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

    public static function getPaperDataUrl(array $data)
    {
        $paperDataUrl = [];
        $localStorage = Storage::disk('local');

        $paperPath = 'papers/' . 'paper_' . $data['paper_id'] . '.txt';

        if ($localStorage->exists($paperPath)) {
            $dataUrl = $localStorage->get($paperPath);
            $paperDataUrl['paper_data_url'] = $dataUrl;
        }

        return JSONUtilities::returnData($paperDataUrl);
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

        $dataUrl = $data['paper_data_url'];
        unset($data['paper_data_url']); //remove this from the array

        $id = DB::table('papers')->insertGetId($data);

        $localStorage = Storage::disk('local');

        $paperPath = 'papers/' . 'paper_' . $id . '.txt';

        //remove an earlier version of poster, if exists
        if ($localStorage->exists($paperPath)) {
            $localStorage->delete($paperPath);
        }

        $success = $localStorage->put($paperPath, $dataUrl);

        if ($success) {
            return JSONUtilities::returnData(array('id' => $id));
        } else {
            return JSONUtilities::returnError('Could not insert paper.');
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

        if (array_key_exists('paper_data_url', $data)) {
            $dataUrl = $data['paper_data_url'];
            unset($data['paper_data_url']); //remove this from the array

            $localStorage = Storage::disk('local');

            $paperPath = 'papers/' . 'paper_' . $primaryKey['paper_id'] . '.txt';

            //remove an earlier version of poster, if exists
            if ($localStorage->exists($paperPath)) {
                $localStorage->delete($paperPath);
            }

            $localStorage->put($paperPath, $dataUrl);
        }

        $success = DB::table('papers')
            ->where('paper_id', $primaryKey['paper_id'])
            ->update($data);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Paper successfully updated.'));
        } else {
            return JSONUtilities::returnError('Could not update paper.');
        }
    }

    public static function remove(array $data)
    {
        $success = DB::table('papers')
            ->where('paper_id', $data['paper_id'])
            ->delete();
            
        if (!$success) {
            return JSONUtilities::returnError('Paper does not exist.');
        } 
        
        $localStorage = Storage::disk('local');

        $paperPath = 'papers/' . 'paper_' . $data['paper_id'] . '.txt';

        //remove an earlier version of poster, if exists
        if ($localStorage->exists($paperPath)) {
            $localStorage->delete($paperPath);
        }
        
        return JSONUtilities::returnData(array('message' => 'Paper successfully deleted.'));
    }

    /**
     * [getByTag]
     * @param  array  $data [description]
     * @return [JSON]       [description]
     */
    public static function getByTag(array $data) {
        $results = DB::table('papers')
            ->join('papers_tag', 'papers.paper_id', '=', 'papers_tag.paper_id')
            ->where('tag_name', $data['tag_name'])
            ->get();

        return JSONUtilities::returnData($results);
    }

    /**
     * [getByAuthor]
     * @param  array  $data [description]
     * @return [JSON]       [description]
     */
    public static function getByAuthor(array $data)
    {
        $results1 = DB::table('paper_authored')
            ->select('paper_id')
            ->where('email', $data['email'])
            ->get();
            
        if (count($results1) == 0) {
            return JSONUtilities::returnError('No papers exist for this author.');
        }
        
        $results1 = collect($results1)->flatten();
        
        $results2 = DB::table('papers')
            ->whereIn('paper_id', $results1)
            ->get();
            
        return JSONUtilities::returnData($results2);
    }

    /**
     * [getByReviewer]
     * @param  array  $data [description]
     * @return [JSON]       [description]
     */
    public static function getByReviewer(array $data)
    {
        $results1 = DB::table('paper_reviewed')
            ->select('paper_id')
            ->where('email', $data['email'])
            ->get();
            
        if (count($results1) == 0) {
            return JSONUtilities::returnError('No papers exist for this reviewer.');
        }
        
        $results1 = collect($results1)->flatten();
        
        $results2 = DB::table('papers')
            ->whereIn('paper_id', $results1)
            ->get();
            
        return JSONUtilities::returnData($results2);
    }
    
    public static function getByEvent(array $data) {
        $results1 = DB::table('paper_submitted')
            ->select('paper_id')
            ->where('event_id', $data['event_id'])
            ->get();

        if (count($results1) == 0) {
            return JSONUtilities::returnError('No papers exist for this event.');
        }

        $results1 = collect($results1)->flatten();

        $results2 = DB::table('papers')
            ->whereIn('paper_id', $results1)
            ->get();

        return JSONUtilities::returnData($results2);
    }
    
    public static function getPaperForEvent(array $data) {
        $results1 = DB::table('papers')
            ->where('paper_id', $data['paper_id'])
            ->get();
        
        if (count($results1) == 0) {
            return JSONUtilities::returnError('No papers exist for this event.');
        }
        
        $results1 = $results1[0];
        
        $results2 = DB::table('papers_tag')
            ->select('tag_name')
            ->where('paper_id', $data['paper_id'])
            ->get();
        
        $paperTags = array_flatten($results2);
        
        $results3 = DB::table('paper_authored')
            ->select('email')
            ->where('paper_id', $data['paper_id'])
            ->get();
            
        $paperAuthors = array_flatten($results3);
        
        $results4 = DB::table('paper_reviewed')
            ->select('email')
            ->where('paper_id', $data['paper_id'])
            ->where('event_id', $data['event_id'])
            ->whereNotIn('comment', [
                '[system] [rejected]',
                '[system] [coi]'
            ])
            ->get();
            
        $paperReviewers = array_flatten($results4);
        
        $results1['tags'] = $paperTags;
        $results1['authors'] = $paperAuthors;
        $results1['reviewers'] = $paperReviewers;
        
        return JSONUtilities::returnData($results1);
    }
}
