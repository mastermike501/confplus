<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

use DB;
use Hash;
use Mail;
use Storage;

use App\Http\Helpers\JSONUtilities;
use App\Http\Helpers\FormatUtilities;
use App\Http\Helpers\EmailUtilities;

class User extends Model
{
    private static $timecolumns = [
        'dob' => 'd-m-Y'
    ];
    
    public static function changeProfileImage(array $data) {
        $localStorage = Storage::disk('local');
    
        $path = 'profile_images/' . 'profile_image_' . $data['email'] . '.txt';
    
        if ($localStorage->exists($path)) {
            $localStorage->delete($path);
        }
    
        $success = $localStorage->put($path, $data['image_data_url']);
    
        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Profile image successfully uploaded.'));
        } else {
            return JSONUtilities::returnError('Could not upload profile image.');
        }
    }
    
    public static function getProfileImage(array $data) {
        $localStorage = Storage::disk('local');
    
        $path = 'profile_images/' . 'profile_image_' . $data['email'] . '.txt';
    
        if (!$localStorage->exists($path)) {
            return JSONUtilities::returnError('Could not find profile image.');
        }
    
        $dataUrl = $localStorage->get($path);
        
        $results = DB::table('users')
            ->select('first_name', 'last_name')
            ->where('email', $data['email'])
            ->get();
        
        $results['image_data_url'] = $dataUrl;
    
        return JSONUtilities::returnData($results);
    }
    
    public static function changePassword(array $data) {
        $results = DB::table('users')
            ->select('password', 'active')
            ->where('email', $data['email'])
            ->get();
        
        if (count($results) == 0) {
            return JSONUtilities::returnError('Email does not exist.');
        }
        
        if ($results[0]['active'] == '0') {
            return JSONUtilities::returnError('Email exists. User inactive.');
        }
        
        if (!Hash::check($data['old_password'], $results[0]['password'])) {
            return JSONUtilities::returnError('Incorrect password.');
        }
        
        $newPassword = Hash::make($data['new_password']);
        
        $success = DB::table('users')
            ->where('email', $data['email'])
            ->update([
                'password' => $newPassword
            ]);
        
        $emailConfig = [
            'to' => $data['email'],
            'subject' => 'Password Change'
        ];
        
        $body = '<p>You password has been changed. If you did not request
            this, contact confplus.confplus@gmail.com for assistance.</p>
            <p>Have a great day!</p>';
        $emailData = [
            'title' => 'Password Change',
            'body' => $body
        ];
        
        EmailUtilities::sendEmail($emailData, $emailConfig);
        
        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Password successfully changed.'));
        } else {
            return JSONUtilities::returnError('Could not change password.');
        }
    }
    
    public static function login(array $data)
    {
        $results = DB::table('users')
            ->select('password', 'active')
            ->where('email', $data['email'])
            ->get();
        
        if (count($results) == 0) {
            return JSONUtilities::returnError('Email does not exist.');
        }
        
        if ($results[0]['active'] == '0') {
            return JSONUtilities::returnError('Email exists. User inactive.');
        }
        
        if (Hash::check($data['password'], $results[0]['password'])) {
            return JSONUtilities::returnData(array('message' => 'Login successful.'));
        }
        
        return JSONUtilities::returnError('Password is incorrect.');
    }
    
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
        
        $data['password'] = Hash::make($data['password']);
        
        $success = DB::table('users')->insert($data);

        $emailConfig = [
            'to' => $data['email'],
            'subject' => 'Welcome to Confplus!'
        ];
        
        $body = '<p>Welcome to Confplus! We hope you have an awesome time using our
            event management capabilities to suit your needs.</p>
            <p>Have a great day!</p>';
        $emailData = [
            'title' => 'Welcome!',
            'body' => $body
        ];
        
        EmailUtilities::sendEmail($emailData, $emailConfig);
        
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
            ->whereNull('active')
            ->orWhere('active', '1')
            ->get();

        return JSONUtilities::returnData($results);
    }

    /**
     * [getByPaperId]
     * @param  array  $data [description]
     * @return [JSON]       [description]
     */
    public static function getReviewersByPaperId(array $data)
    {
        $query = DB::table('paper_reviewed')
            ->select('email')
            ->where('paper_id', $data['paper_id']);
        
        if (array_key_exists('event_id', $data)) {
            $query->where('event_id', $data['event_id']);
        }
        
        $results1 = $query->get();
        
        if (count($results1) == 0) {
            return JSONUtilities::returnError('No results');
        }
        
        $results1 = collect($results1)->flatten();
        
        $results2 = DB::table('users')
            ->whereIn('email', $results1)
            ->where('active', '1')
            ->whereNull('active')
            ->get();

        if (count($results2) == 0) {
            return JSONUtilities::returnError('No record exists');
        }

        return JSONUtilities::returnData($results2);
    }
    
    public static function getEventsAttending(array $data)
    {
        $results1 = DB::table('ticket_record')
            ->select('event_id')
            ->distinct()
            ->where('email', $data['email'])
            ->get();
        
        if (count($results1) == 0) {
            return JSONUtilities::returnError('No results');
        }
        
        //put results into a single dimension array
        $results1 = collect($results1)->flatten();
        
        //retrieve events that were attended by user
        $query = DB::table('events')
            ->whereIn('event_id', $results1); 
        
        switch ($data['criteria']) {
            case 'past':
                $query->where('to_date', '>', DB::raw('CURRENT_TIMESTAMP'));
                break;
            
            case 'future':
                $query->where('to_date', '<', DB::raw('CURRENT_TIMESTAMP'));
                break;
            
            case 'all':
                //do nothing
                break;
            
            default:
                //do nothing
                break;
        }
        
        $results2 = $query->get();

        if (count($results2) == 0) {
            return JSONUtilities::returnError('No record exists');
        }

        return JSONUtilities::returnData($results2);
    }

}
