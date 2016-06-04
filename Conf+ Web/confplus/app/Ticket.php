<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

use App\Http\Helpers\JSONUtilities;
use App\Http\Helpers\FormatUtilities;

class Ticket extends Model
{
    private static $timecolumns = [
        'start_date' => 'd-m-Y H:i',
        'end_date' => 'd-m-Y H:i'
    ];
    
    public static function get(array $data) {
        
        $results = DB::table('tickets')
            ->where('event_id', $data['event_id'])
            ->where('title', $data['title'])
            ->where('name', $data['name'])
            ->where('class', $data['class'])
            ->where('type', $data['type'])
            ->get();

        if (count($results) == 0) {
            return JSONUtilities::returnError('No record exists');
        }

        return JSONUtilities::returnData($results);
    }
    
    /**
     * [getTypes]
     * @param  array  $data [description]
     * @return [JSON]       [description]
     */
    public static function getTypes(array $data) {
        
        $results = DB::table('tickets')
            ->where('event_id', $data['event_id'])
            ->where('title', $data['title'])
            ->get();

        if (count($results) == 0) {
            return JSONUtilities::returnError('No record exists');
        }

        return JSONUtilities::returnData($results);
    }
    
    public static function getByEvent(array $data) {
        
        $results1 = DB::table('sessions')
            ->where('event_id', $data['event_id'])
            ->get();
        
        $results2 = array_map(function($item) use ($data){
            $tickets = DB::table('tickets')
                ->where('event_id', $data['event_id'])
                ->where('title', $item['title'])
                ->get();
            
            $item['tickets'] = $tickets;
            
            return $item;
        }, $results1);
        
        return JSONUtilities::returnData($results2);
    }

    public static function insert(array $data) {
        $success = FormatUtilities::getDateTime(self::$timecolumns, $data);
        
        if (!$success) {
            return JSONUtilities::returnError(FormatUtilities::displayTimecolumnFormats(self::$timecolumns));
        }
        
        $success = DB::table('tickets')->insert($data);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Ticket successfully created.'));
        } else {
            return JSONUtilities::returnError('Could not insert ticket.');
        }
    }
    
    public static function edit($primaryKey, array $data) {
        $success = FormatUtilities::getDateTime(self::$timecolumns, $data);
        
        if (!$success) {
            return JSONUtilities::returnError(FormatUtilities::displayTimecolumnFormats(self::$timecolumns));
        }
        
        $success = DB::table('tickets')
            ->where('event_id', $primaryKey['event_id'])
            ->where('title', $primaryKey['title'])
            ->where('name', $primaryKey['name'])
            ->where('class', $primaryKey['class'])
            ->where('type', $primaryKey['type'])
            ->update($data);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Ticket successfully updated.'));
        } else {
            return JSONUtilities::returnError('Could not update ticket.');
        }
    }
    
    public static function remove(array $data)
    {
        $success = DB::table('tickets')
            ->where('event_id', $data['event_id'])
            ->where('title', $data['title'])
            ->where('name', $data['name'])
            ->where('class', $data['class'])
            ->where('type', $data['type'])
            ->delete();
            
        if (!$success) {
            return JSONUtilities::returnError('Ticket does not exist.');
        } 
        
        return JSONUtilities::returnData(array('message' => 'Ticket successfully deleted.'));
    }  
}
