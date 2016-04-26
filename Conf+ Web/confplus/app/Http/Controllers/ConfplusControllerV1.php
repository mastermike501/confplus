<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Billing;
use App\Event;
use App\EventTag;
use App\Paper;
use App\PaperAuthored;
use App\PaperReviewed;
use App\PaperTag;
use App\Payment;
use App\Resource;
use App\Room;
use App\Seat;
use App\Session;
use App\Ticket;
use App\TicketRecord;
use App\UserTag;
use App\User;
use App\Venue;

use App\Http\Helpers\JSONUtilities;

class ConfplusControllerV1 extends Controller
{
    private $requestMethods = array(
        'test',
        'getUser', //tested
        'createUser', //tested
        'updateUser', //tested
        'getEvent', //tested
        'createEvent', //tested
        'updateEvent', //tested
        'uploadPoster', //tested
        'getPoster', //tested
        'getTicketCategories', //tested
        'createTicketCategory', //tested
        'updateTicketCategory', //tested
        'makePayment', //tested
        'getPaper', //tested
        'createPaper', //tested
        'updatePaper', //tested
        'getRoom', //tested
        'getRooms', //tested
        'createRoom', //tested
        'updateRoom', //tested
        'getVenue', //tested
        'createVenue', //tested
        'updateVenue', //tested
        'getSession', //tested
        'getSessions', //tested
        'createSession', //tested
        'updateSession', //tested
        'addUserTag', //tested
        'addEventTag', //tested
        'addPaperTag', //tested
        'getUsersByTag', //tested
        'getEventsByTag', //tested
        'getPapersByTag', //tested
        'getResource', //tested
        'createResource', //tested
        'updateResource', //tested
        'getResourcesByRoom', //tested
        'addSessionAttendee', 
        'getEventAttendees',
        'getSessionAttendees',
        'addPaperAuthor', //tested
        'getPaperAuthors', //tested
        'getPapersByAuthor', //tested
        'getBillingInfo', //tested
        'createBillingInfo', //tested
        'updateBillingInfo', //tested
        'getPapersByReviewer', //tested
        'getReviewersByPaperId', //tested
        'addPaperReviewed', //tested
        'createSeat', //tested
        'getSeatsInRoom' //tested
    );

    public function store(Request $request)
    {
        $methodName = $request->input('method');

        if (in_array($methodName, $this->requestMethods)) {
            try {
                return $this->$methodName($request);
            } catch (\Illuminate\Database\QueryException $e) {
                return JSONUtilities::returnError($e->getMessage());
            }
        }

        return JSONUtilities::returnError('Method ' . $methodName . ' not found.');
    }

    private function test(Request $request)
    {
        var_dump($request->only(['a', 'b', 'c']));
    }

    private function getUser(Request $request)
    {
        $required = array('email');

        if ($request->has($required)) {
            return User::get($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    private function createUser(Request $request)
    {
        $required = array('email', 'password', 'username');

        if ($request->has($required)) {
            return User::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    private function updateUser(Request $request)
    {
        $required = array('email');

        if (!$request->has($required)) {
            return JSONUtilities::returnRequirementsError($required);
        }

        $data = $request->except(['method', 'email']);

        if (!empty($data)) {
            return User::edit($request->only($required), $data);
        } else {
            return JSONUtilities::returnError('No data to update');
        }
    }

    private function getEvent(Request $request)
    {
        $required = array('event_id');
        
        if ($request->has($required)) {
            return Event::get($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    private function createEvent(Request $request)
    {
        $required = array('name', 'type', 'from_date', 'to_date', 'description', 'paper_deadline');

        if ($request->has($required)) {
            return Event::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    private function updateEvent(Request $request)
    {
        $required = array('event_id');

        if (!$request->has($required)) {
            return JSONUtilities::returnRequirementsError($required);
        }

        $data = $request->except(array_merge(['method'], $required));

        if (!empty($data)) {
            return Event::edit($request->only($required), $data);
        } else {
            return JSONUtilities::returnError('No data to update');
        }
    }

    private function uploadPoster(Request $request)
    {
        $required = array('event_id', 'poster_data_url');

        if ($request->has($required)) {
            return Event::uploadPoster($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    private function getPoster(Request $request)
    {
        $required = array('event_id');

        if ($request->has($required)) {
            return Event::getPoster($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    private function getTicketCategories(Request $request)
    {
        $required = array('event_id', 'title');

        if ($request->has($required)) {
            return Ticket::getTypes($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    private function createTicketCategory(Request $request)
    {
        $required = array('event_id', 'title', 'name', 'class',
            'type', 'price', 'description', 'start_date', 'end_date', 'quantity', 'num_purchased');

        if ($request->has($required)) {
            return Ticket::insertSingle($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    private function updateTicketCategory(Request $request)
    {
        $required = array('event_id', 'title', 'name', 'class', 'type');

        if (!$request->has($required)) {
            return JSONUtilities::returnRequirementsError($required);
        }

        $data = $request->except(array_merge(['method'], $required));

        if (!empty($data)) {
            return Ticket::edit($request->only($required), $data);
        } else {
            return JSONUtilities::returnError('No data to update');
        }
    }

    private function purchaseTicket(Request $request)
    {
        return JSONUtilities::returnError('purchaseTicket not implemented.');
        // 
        $required = array('event_id', 'email', 'role', 'seat_no');

        if (!$request->has($required)) {
            return JSONUtilities::returnRequirementsError($required);
        }

        //$ticketSuccess = TicketType::purchaseTicket($request->only($required));

        if (!is_array($ticketSuccess)) {
            return $ticketSuccess;
        }

        //$eventAttendedSuccess = EventAttended::insert($request->only($required));

        if (!is_array($eventAttendedSuccess)) {
            return $eventAttendedSuccess;
        }

        return JSONUtilities::returnData(array_merge($ticketSuccess, $eventAttendedSuccess));
    }

    private function makePayment(Request $request)
    {
        $required = array('email', 'type', 'amount', 'payment_date');

        if ($request->has($required)) {
            return Payment::insert($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    private function getPaper(Request $request)
    {
        $required = array('paper_id');

        if ($request->has($required)) {
            return Paper::get($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    private function createPaper(Request $request)
    {
        $required = array('title', 'publish_date', 'latest_submit_date', 'paper_data_url');

        if ($request->has($required)) {
            return Paper::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    private function updatePaper(Request $request)
    {
        $required = array('paper_id');

        if (!$request->has($required)) {
            return JSONUtilities::returnRequirementsError($required);
        }

        $data = $request->except(array_merge(['method'], $required));

        if (!empty($data)) {
            return Paper::edit($request->only($required), $data);
        } else {
            return JSONUtilities::returnError('No data to update');
        }
    }

    private function getRoom(Request $request)
    {
        $required = array('venue_id', 'name');

        if ($request->has($required)) {
            return Room::get($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    private function getRooms(Request $request)
    {
        $required = array('venue_id');

        if ($request->has($required)) {
            return Room::getRooms($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    private function createRoom(Request $request)
    {
        $required = array('venue_id', 'name', 'type', 'capacity');

        if ($request->has($required)) {
            return Room::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    private function updateRoom(Request $request)
    {
        $required = array('venue_id', 'name');

        if (!$request->has($required)) {
            return JSONUtilities::returnRequirementsError($required);
        }

        $data = $request->except(array_merge(['method'], $required));

        if (!empty($data)) {
            return Room::edit($request->only($required), $data);
        } else {
            return JSONUtilities::returnError('No data to update');
        }
    }

    private function getVenue(Request $request)
    {
        $required = array('venue_id');

        if ($request->has($required)) {
            return Venue::get($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    private function createVenue(Request $request)
    {
        $required = array('name', 'type', 'street', 'city', 'state', 'country', 'longitude', 'latitude');

        if ($request->has($required)) {
            return Venue::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    private function updateVenue(Request $request)
    {
        $required = array('venue_id');

        if (!$request->has($required)) {
            return JSONUtilities::returnRequirementsError($required);
        }

        $data = $request->except(array_merge(['method'], $required));

        if (!empty($data)) {
            return Venue::edit($request->input('venue_id'), $data);
        } else {
            return JSONUtilities::returnError('No data to update');
        }
    }

    private function getSession(Request $request)
    {
        $required = array('event_id', 'title');

        if ($request->has($required)) {
            return Session::get($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    private function getSessions(Request $request)
    {
        $required = array('event_id');

        if ($request->has($required)) {
            return Session::getSessions($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    private function createSession(Request $request)
    {
        $required = array('event_id', 'title', 'start_time', 'end_time');

        if ($request->has($required)) {
            return Session::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    private function updateSession(Request $request)
    {
        $required = array('event_id', 'title');

        if (!$request->has($required)) {
            return JSONUtilities::returnRequirementsError($required);
        }

        $data = $request->except(array_merge(['method'], $required));

        if (!empty($data)) {
            return Session::edit($request->only($required), $data);
        } else {
            return JSONUtilities::returnError('No data to update');
        }
    }

    private function addUserTag(Request $request)
    {
        $required = array('email', 'tag_name');

        if ($request->has($required)) {
            return UserTag::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    private function addEventTag(Request $request)
    {
        $required = array('event_id', 'tag_name');

        if ($request->has($required)) {
            return EventTag::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    private function addPaperTag(Request $request)
    {
        $required = array('paper_id', 'tag_name');

        if ($request->has($required)) {
            return PaperTag::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    private function getUsersByTag(Request $request)
    {
        $required = array('tag_name');

        if ($request->has($required)) {
            return User::getByTag($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    private function getEventsByTag(Request $request)
    {
        $required = array('tag_name');

        if ($request->has($required)) {
            return Event::getByTag($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    private function getPapersByTag(Request $request)
    {
        $required = array('tag_name');

        if ($request->has($required)) {
            return Paper::getByTag($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    private function getResource(Request $request)
    {
        $required = array('venue_id', 'room_name', 'name');

        if ($request->has($required)) {
            return Resource::get($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    private function createResource(Request $request)
    {
        $required = array('venue_id', 'room_name', 'name', 'type', 'number');

        if ($request->has($required)) {
            return Resource::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    private function updateResource(Request $request)
    {
        $required = array('venue_id', 'room_name', 'name');

        if (!$request->has($required)) {
            return JSONUtilities::returnRequirementsError($required);
        }

        $data = $request->except(array_merge(['method'], $required));

        if (!empty($data)) {
            return Resource::edit($request->only($required), $data);
        } else {
            return JSONUtilities::returnError('No data to update');
        }
    }

    private function getResourcesByRoom(Request $request)
    {
        $required = array('venue_id', 'room_name');

        if ($request->has($required)) {
            return Resource::getByRoom($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    private function addSessionAttendee(Request $request)
    {
        $required = array('event_id', 'title', 'ticket_name', 'class', 'type', 'venue_id', 'room_name', 'seat_num', 'email');
        
        if (!$request->has($required)) {
            return JSONUtilities::returnRequirementsError($required);
        }

        $data = $request->only('email');

        if (!empty($data)) {
            return TicketRecord::addSessionAttendee($request->only($required), $data);
        } else {
            return JSONUtilities::returnError('No data to update');
        }
    }

    private function getEventAttendees(Request $request)
    {
        $required = array('event_id');

        if ($request->has($required)) {
            return TicketRecord::getEventAttendees($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    private function getSessionAttendees(Request $request)
    {
        $required = array('event_id', 'title');

        if ($request->has($required)) {
            return TicketRecord::getSessionAttendees($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    private function addPaperAuthor(Request $request)
    {
        $required = array('email', 'paper_id');

        if ($request->has($required)) {
            return PaperAuthored::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    private function getPaperAuthors(Request $request)
    {
        $required = array('paper_id');

        if ($request->has($required)) {
            return PaperAuthored::get($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    private function getPapersByAuthor(Request $request)
    {
        $required = array('email');

        if ($request->has($required)) {
            return Paper::getByAuthor($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    private function getBillingInfo(Request $request)
    {
        $required = array('email', 'card#');

        if ($request->has($required)) {
            return Billing::get($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    private function createBillingInfo(Request $request)
    {
        $required = array('email', 'card#', 'card_type', 'expiry_date');

        if ($request->has($required)) {
            return Billing::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    private function updateBillingInfo(Request $request)
    {
        $required = array('email', 'card#');

        if (!$request->has($required)) {
            return JSONUtilities::returnRequirementsError($required);
        }

        $data = $request->except(array_merge(['method'], $required));

        if (!empty($data)) {
            return Billing::edit($request->only($required), $data);
        } else {
            return JSONUtilities::returnError('No data to update');
        }
    }
    
     private function getPapersByReviewer(Request $request)
    {
        $required = array('email');

        if ($request->has($required)) {
            return Paper::getByReviewer($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    private function getReviewersByPaperId(Request $request)
    {
        $required = array('paper_id');

        if ($request->has($required)) {
            return User::getReviewersByPaperId($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    private function addPaperReviewed(Request $request)
    {
        $required = array('email', 'paper_id', 'comment');

        if ($request->has($required)) {
            return PaperReviewed::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    private function createSeat(Request $request)
    {
        $required = array('venue_id', 'name', 'seat_num');

        if ($request->has($required)) {
            return Seat::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    private function getSeatsInRoom(Request $request)
    {
        $required = array('venue_id', 'name');

        if ($request->has($required)) {
            return Seat::getSeatsInRoom($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
}
