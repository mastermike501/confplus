<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

use App\Http\Helpers\JSONUtilities;
use App\Http\Helpers\FormatUtilities;

class User extends Model
{
    private static $timecolumns = [
        'dob' => 'd-m-Y'
    ];
    
    /**
     * [get]
     * @param  [array] $data [User data containing an email]
     * @return [JSON]       [A JSON string containing a success or error body]
     */
    public static function get(array $data) {
        $results = DB::select('select * from users where email = ?', [$data['email']]);

        if (count($results) == 0) {
            return JSONUtilities::returnError('No record exists');
        }

        //there must ever be only one instance of this record
        if (count($results) > 1) {
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
        $success = FormatUtilities::getDateTime(self::$timecolumns, $data);
        
        if (!$success) {
            return JSONUtilities::returnError(FormatUtilities::displayTimecolumnFormats(self::$timecolumns));
        }
        
        $success = DB::table('users')->insert($data);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'User successfully created.'));
        } else {
            return JSONUtilities::returnError('Could not insert user.');
        }
    }

    /**
     * [edit]
     * @param  [string] $primaryKey [user primary key]
     * @param  [array] $data [User data to update]
     * @return [JSON]       [A JSON string containing a success or error body]
     */
    public static function edit($primaryKey, array $data) {
        $success = FormatUtilities::getDateTime(self::$timecolumns, $data);
        
        if (!$success) {
            return JSONUtilities::returnError(FormatUtilities::displayTimecolumnFormats(self::$timecolumns));
        }
        
        $success = DB::table('users')
            ->where('email', $primaryKey['email'])
            ->update($data);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'User successfully updated.'));
        } else {
            return JSONUtilities::returnError('Could not update user.');
        }
    }

    /**
     * [getByTag]
     * @param  array  $data [description]
     * @return [JSON]       [description]
     */
    public static function getByTag(array $data) {
        $results = DB::table('users')
            ->join('users_tag', 'users.email', '=', 'users_tag.email')
            ->where('tag_name', $data['tag_name'])
            ->get();

        return JSONUtilities::returnData($results);
    }

}
