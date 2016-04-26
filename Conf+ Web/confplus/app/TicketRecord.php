<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

use App\Http\Helpers\JSONUtilities;

class TicketRecord extends Model
{
    /**
     * [getEventAttendees]
     * @param  array  $data [description]
     * @return [JSON]       [description]
     */
    public static function getEventAttendees(array $data)
    {
        $results = DB::table('users')
            ->join('ticket_record', 'users.email', '=', 'ticket_record.email')
            ->where('event_id', $data['event_id'])
            ->get();

        if (count($results) == 0) {
            return JSONUtilities::returnError('No record exists');
        }

        return JSONUtilities::returnData($results);
    }
    
    /**
     * [getSessionAttendees]
     * @param  array  $data [description]
     * @return [JSON]       [description]
     */
    public static function getSessionAttendees(array $data)
    {
        $results = DB::table('users')
            ->join('ticket_record', 'users.email', '=', 'ticket_record.email')
            ->where('event_id', $data['event_id'])
            ->where('ticket_record.title', $data['title'])
            ->get();

        if (count($results) == 0) {
            return JSONUtilities::returnError('No record exists');
        }

        return JSONUtilities::returnData($results);
    }

     /**
     * [insert]
     * @param  [array] $data [User data containing user data]
     * @return [JSON]       [A JSON string containing a success or error body]
     */
    public static function insert(array $data) {
        $success = DB::table('ticket_record')->insert($data);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Ticket record successfully created.'));
        } else {
            return JSONUtilities::returnError('Could not insertticket_recorduser.');
        }
    }

    /**
     * [addSessionAttendee]
     * @param  [type] $primaryKey [description]
     * @param  array  $data       [description]
     * @return [type]             [description]
     */
    public static function addSessionAttendee($primaryKey, array $data)
    {
        $success = DB::table('ticket_record')
            ->where('event_id', $primaryKey['event_id'])
            ->where('title', $primaryKey['title'])
            ->where('ticket_name', $primaryKey['ticket_name'])
            ->where('class', $primaryKey['class'])
            ->where('type', $primaryKey['type'])
            ->where('venue_id', $primaryKey['venue_id'])
            ->where('room_name', $primaryKey['room_name'])
            ->where('seat_num', $primaryKey['seat_num'])
            ->update($data);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Session attendee successfully added.'));
        } else {
            return JSONUtilities::returnError('Could not add session attendee.');
        }
    }
}
