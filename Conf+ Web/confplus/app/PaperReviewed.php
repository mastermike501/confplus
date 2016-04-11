<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

use App\Http\Helpers\JSONUtilities;

class PaperReviewed extends Model
{
    /**
     * [getByEmail]
     * @param  array  $data [description]
     * @return [JSON]       [description]
     */
    public static function getByEmail(array $data)
    {
        $results = DB::table('paper_reviewed')
            ->where('email', $data['email'])
            ->get();

        if (count($results) == 0) {
            return JSONUtilities::returnError('No record exists');
        }

        return JSONUtilities::returnData($results);
    }
    
    /**
     * [getByPaperId]
     * @param  array  $data [description]
     * @return [JSON]       [description]
     */
    public static function getByPaperId(array $data)
    {
        $results = DB::table('paper_reviewed')
            ->where('paper_id', $data['paper_id'])
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
        $success = DB::table('paper_reviewed')->insert($data);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Paper reviewed successfully created.'));
        } else {
            return JSONUtilities::returnError('Could not insert paper reviewed.');
        }
    }
}
