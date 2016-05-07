<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

use App\Http\Helpers\JSONUtilities;
use Illuminate\Support\Collection;

class PaperAuthored extends Model
{
    /**
     * [get]
     * @param  array  $data [description]
     * @return [JSON]       [description]
     */
    public static function get(array $data)
    {
        $results1 = DB::table('paper_authored')
            ->select('email')
            ->where('paper_id', $data['paper_id'])
            ->get();

        if (count($results1) == 0) {
            return JSONUtilities::returnError('No such paper exists');
        }
        
        //put results into a single dimension array
        $results1 = collect($results1)->flatten();
        
        //retrieve events that were attended by user
        $results2 = DB::table('users')
            ->whereIn('email', $results1)
            ->get();

        if (count($results2) == 0) {
            return JSONUtilities::returnError('No record exists');
        }

        return JSONUtilities::returnData($results2);
    }

    /**
     * [insert]
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    public static function insert(array $data)
    {
        $success = DB::table('paper_authored')->insert($data);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Author successfully created.'));
        } else {
            return JSONUtilities::returnError('Could not insert author.');
        }
    }
    
    public static function delete(array $data)
    {
        $success = DB::table('paper_authored')
            ->where('email', $data['email'])
            ->where('paper_id', $data['paper_id'])
            ->delete();
            
        if (!$success) {
            return JSONUtilities::returnError('Paper does not exist.');
        } 
        
        return JSONUtilities::returnData(array('message' => 'Author successfully deleted.'));
    }
}
