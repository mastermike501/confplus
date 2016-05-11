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
    
    public static function validateTicket(array $data)
    {
        $results = DB::table('ticket_record')
            ->where('record_id', $data['ticket_id'])
            ->where('email', $data['email'])
            ->get();
            
        if (count($results) == 0) {
            return JSONUtilities::returnError('No record exists');
        }
        
        return JSONUtilities::returnData(array('message' => 'Ticket exists.'));
    }
    
    public static function getSeatsAndOccupants(array $data)
    {
        $results = DB::table('ticket_record')
            ->where('event_id', $data['event_id'])
            ->where('title', $data['title'])
            ->get();
            
        return JSONUtilities::returnData($results);
    }
    
    public static function purchaseTicket(array $data)
    {
        // $results0 = DB::table('ticket_record')
        //     ->where('event_id', $data['event_id'])
        //     ->where('title', $data['title'])
        //     ->where('ticket_name', $data['ticket_name'])
        //     ->where('class', $data['class'])
        //     ->where('type', $data['type'])
        //     ->where('venue_id', $data['venue_id'])
        //     ->where('room_name', $data['room_name'])
        //     ->where('seat_num', $data['seat_num'])
        //     ->whereNull('email')
        //     ->get();
            
        // if (count($results0) == 0) {
        //     return JSONUtilities::returnError('This ticket has been taken.');
        // }
        
        // $success = DB::table('ticket_record')
        //     ->where('event_id', $data['event_id'])
        //     ->where('title', $data['title'])
        //     ->where('ticket_name', $data['ticket_name'])
        //     ->where('class', $data['class'])
        //     ->where('type', $data['type'])
        //     ->where('venue_id', $data['venue_id'])
        //     ->where('room_name', $data['room_name'])
        //     ->where('seat_num', $data['seat_num'])
        //     ->update(['email' => $data['email']]);
            
        // if (!$success) {
        //     return JSONUtilities::returnError('Could not link user with ticket.');
        // }
        
        // $results1 = DB::table('tickets')
        //     ->where('event_id', $data['event_id'])
        //     ->where('title', $data['title'])
        //     ->where('name', $data['ticket_name'])
        //     ->where('class', $data['class'])
        //     ->where('type', $data['type'])
        //     ->get();

        // $ticketType = $results1[0];

        // if ($ticketType['num_purchased'] >= $ticketType['quantity']) {
        //     return JSONUtilities::returnError('There are no more tickets of this type.');
        // }
        
        // $results2 = DB::table('events')
        //     ->select('payee', 'card#')
        //     ->where('event_id', $data['event_id'])
        //     ->get();
        
        // $eventPayment = $results2[0];
        
        // $success = DB::table('payments')
        //     ->insert([
        //         'email' => $data['email'],
        //         'type' => 'ticket purchase',
        //         'payee' => $eventPayment['payee'],
        //         'card#' => $eventPayment['card#'],
        //         'amount' => 
        //     ]);

        // //okay
        // $ticketType['num_purchased']++;
        // $success = DB::table('tickets')
        //     ->where('event_id', $ticketType['event_id'])
        //     ->where('title', $ticketType['title'])
        //     ->where('name', $ticketType['name'])
        //     ->where('class', $ticketType['class'])
        //     ->where('type', $ticketType['type'])
        //     ->update(['num_purchased' => $ticketType['num_purchased']]);
    }
}
