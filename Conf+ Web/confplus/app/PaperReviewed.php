<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

use App\Http\Helpers\JSONUtilities;

class PaperReviewed extends Model
{
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
