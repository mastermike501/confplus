<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

use App\Http\Helpers\JSONUtilities;

class TicketType extends Model
{
    /**
     * [get]
     * @param  array  $data [description]
     * @return [JSON]       [description]
     */
    public static function get(array $data) {
        $results = DB::select('select * from ticket_type where event_id = ?', [$data['event_id']]);

        //there must ever be only one instance of this record
        // if (count($results) != 1) {
        //     return JSONUtilities::returnError('More than one record exists. Contact backend support.');
        // }

        return JSONUtilities::returnData($results);
    }

    /**
     * [insertSingle]
     * @param  array  $data [description]
     * @return [JSON]       [description]
     */
    public static function insertSingle(array $data) {
        $success = DB::table('ticket_type')->insert($data);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Ticket type successfully created.'));
        } else {
            return JSONUtilities::returnError('Could not insert ticket type.');
        }
    }

    /**
     * [purchaseTicket]
     * @param  array  $data [description]
     * @return [JSON|array]             [description]
     */
    public static function purchaseTicket(array $data) {
        return JSONUtilities::returnError('purchaseTicket not implemented.');

        $results = DB::table('ticket_type')
            ->select('quantity', 'num_purchased', 'price')
            ->where('event_id', $data[0])
            ->where('name', $data[1])
            ->get();

        if ($results['num_purchased'] >= $results['quantity']) {
            return JSONUtilities::returnError('There are no more tickets of this type.');
        }

        $price = $results['price']; //what do we do with this variable?
        $results['num_purchased']++;

        //update ticket_type table
        DB::table('ticket_type')
            ->where('event_id', $data[0])
            ->where('name', $data[1])
            ->update(['num_purchased' => $results['num_purchased']]);

        //insert new data into event_attended table
        DB::table('event_attended')->insert($data);

        return array('price' => $price);
    }

    /**
     * [edit]
     * @param  [array] $primaryKey [description]
     * @param  array  $data       [description]
     * @return [JSON]             [description]
     */
    public static function edit($primaryKey, array $data) {
        $success = DB::table('ticket_type')
            ->where('event_id', $primaryKey[0])
            ->where('name', $primaryKey[1])
            ->update($data);

        if ($success) {
            return JSONUtilities::returnData(array('message' => 'Ticket type successfully updated.'));
        } else {
            return JSONUtilities::returnError('Could not update ticket type.');
        }
    }
}
