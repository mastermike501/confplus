<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

use App\Http\Helpers\JSONUtilities;

class PaperAuthored extends Model
{
    /**
     * [get]
     * @param  array  $data [description]
     * @return [JSON]       [description]
     */
    public static function get(array $data)
    {
        $results = DB::table('paper_authored')
            ->where('paper_id', $data['paper_id'])
            ->get();

        return JSONUtilities::returnData($results);
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
}
