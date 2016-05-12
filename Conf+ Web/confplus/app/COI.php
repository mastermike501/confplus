<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

use App\Http\Helpers\JSONUtilities;

class COI extends Model
{
    public static function insert(array $data) {
        $success = DB::table('coi')->insert($data);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'COI successfully created.'));
        } else {
            return JSONUtilities::returnError('Could not insert COI.');
        }
    }
    
    public static function getByReviewer(array $data) {
        $results = DB::table('coi')
            ->where('reviewer', $data['reviewer'])
            ->get();

        if (count($results) == 0) {
            return JSONUtilities::returnError('No record exists');
        }

        return JSONUtilities::returnData($results);
    }
    
    public static function getByAuthor(array $data) {
        $results = DB::table('coi')
            ->where('author', $data['author'])
            ->get();

        if (count($results) == 0) {
            return JSONUtilities::returnError('No record exists');
        }

        return JSONUtilities::returnData($results);
    }
}
