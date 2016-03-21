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
<<<<<<< HEAD
        if (count($results) != 1) {
            return JSONUtilities::returnError('More than one record exists. Contact backend support.');
        }
=======
        // if (count($results) != 1) {
        //     return JSONUtilities::returnError('More than one record exists. Contact backend support.');
        // }
>>>>>>> rest-backend

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
<<<<<<< HEAD
            return JSONUtilities::returnData(array('data' => 'User successfully created.'));
=======
            return JSONUtilities::returnData(array('message' => 'User successfully created.'));
>>>>>>> rest-backend
        } else {
            return JSONUtilities::returnError('Could not insert user.');
        }
    }

    /**
     * [edit]
<<<<<<< HEAD
     * @param  [string] $primaryKey [description]
=======
     * @param  [string] $primaryKey [user primary key]
>>>>>>> rest-backend
     * @param  [array] $data [User data to update]
     * @return [JSON]       [A JSON string containing a success or error body]
     */
    public static function edit($primaryKey, array $data) {
        $success = DB::table('users')
            ->where('email', $primaryKey)
            ->update($data);

        if ($success) {
<<<<<<< HEAD
            return JSONUtilities::returnData(array('data' => 'User successfully updated.'));
=======
            return JSONUtilities::returnData(array('message' => 'User successfully updated.'));
>>>>>>> rest-backend
        } else {
            return JSONUtilities::returnError('Could not update user.');
        }
    }

}
