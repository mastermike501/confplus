<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

use App\Http\Helpers\JSONUtilities;

class User extends Model
{
    /**
     * [get]
     * @param  [array] $data [User data containing an email]
     * @return [JSON]       [A JSON string containing a success or error body]
     */
    public static function get(array $data) {
        $results = DB::select('select * from users where email = ?', [$data['email']]);

        //there must ever be only one instance of this record
        if (count($results) != 1) {
            return JSONUtilities::returnError('More than one record exists. Contact backend support.');
        }

        return JSONUtilities::returnData($results);
    }

    /**
     * [insert]
     * @param  [array] $data [User data containing user data]
     * @return [JSON]       [A JSON string containing a success or error body]
     */
    public static function insert(array $data) {
        $success = DB::table('users')->insert($data);

        if ($success) {
            return JSONUtilities::returnData(array('data' => 'User successfully created.'));
        } else {
            return JSONUtilities::returnError('Could not insert user.');
        }
    }

    /**
     * [edit]
     * @param  [string] $primaryKey [description]
     * @param  [array] $data [User data to update]
     * @return [JSON]       [A JSON string containing a success or error body]
     */
    public static function edit($primaryKey, array $data) {
        $success = DB::table('users')
            ->where('email', $primaryKey)
            ->update($data);

        if ($success) {
            return JSONUtilities::returnData(array('data' => 'User successfully updated.'));
        } else {
            return JSONUtilities::returnError('Could not update user.');
        }
    }

}
