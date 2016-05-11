<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

use App\Http\Helpers\JSONUtilities;

class PaperSubmitted extends Model
{
    public static function insert(array $data) {
        $success = DB::table('paper_submitted')->insert($data);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Paper submission successfully created.'));
        } else {
            return JSONUtilities::returnError('Could not insert paper submission.');
        }
    }
}
