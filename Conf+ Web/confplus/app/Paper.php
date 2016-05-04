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
        'latest_sub_date' => 'd-m-Y'
    ];

    /**
     * [get]
     * @param  array  $data [description]
     * @return [JSON]       [description]
     */
    public static function get(array $data)
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

        $localStorage = Storage::disk('local');

        $paperPath = 'papers/' . 'paper_' . $data['paper_id'] . '.txt';

        if ($localStorage->exists($paperPath)) {
            $dataUrl = $localStorage->get($paperPath);
            $results[0]['paper_data_url'] = $dataUrl;
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
        $results = DB::table('papers')
            ->join('paper_authored', 'papers.paper_id', '=', 'paper_authored.paper_id')
            ->where('email', $data['email'])
            ->get();

        if (count($results) == 0) {
            return JSONUtilities::returnError('No record exists');
        }

        $localStorage = Storage::disk('local');

        $resultsLength = count($results);

        foreach ($results as &$paper) {
            $paperPath = 'papers/' . 'paper_' . $paper['paper_id'] . '.txt';

            if ($localStorage->exists($paperPath)) {
                $dataUrl = $localStorage->get($paperPath);
                $paper['paper_data_url'] = $dataUrl;
            }
        }

        unset($paper);

        return JSONUtilities::returnData($results);
    }

    /**
     * [getByReviewer]
     * @param  array  $data [description]
     * @return [JSON]       [description]
     */
    public static function getByReviewer(array $data)
    {
        $results = DB::table('papers')
            ->join('paper_reviewed', 'papers.paper_id', '=', 'paper_reviewed.paper_id')
            ->where('email', $data['email'])
            ->get();

        if (count($results) == 0) {
            return JSONUtilities::returnError('No record exists');
        }

        $localStorage = Storage::disk('local');

        $resultsLength = count($results);

        foreach ($results as &$paper) {
            $paperPath = 'papers/' . 'paper_' . $paper->paper_id . '.txt';

            if ($localStorage->exists($paperPath)) {
                $dataUrl = $localStorage->get($paperPath);
                $paper->paper_data_url = $dataUrl;
            }
        }

        unset($paper);

        return JSONUtilities::returnData($results);
    }
}
