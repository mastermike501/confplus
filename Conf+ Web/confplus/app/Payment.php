<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

use App\Http\Helpers\JSONUtilities;

class Payment extends Model
{
    /**
     * [insert]
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    public static function insert(array $data)
    {
        $success = DB::table('payments')->insert($data);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Payment successfully created.'));
        } else {
            return JSONUtilities::returnError('Could not insert payment.');
        }
    }
}
