<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Event;
use App\EventAttended;
use App\EventTag;
use App\PaperAuthored;
use App\PaperTag;
use App\Payment;
use App\Resource;
use App\Room;
use App\Session;
use App\Ticket;
use App\UserTag;
use App\User;
use App\Venue;

use App\Http\Helpers\JSONUtilities;

class ConfplusControllerV1 extends Controller
{
    private $requestMethods = array(
        'test' => 'test',
        'getUser' => 'getUser', //tested
        'createUser' => 'createUser', //tested
        'updateUser' => 'updateUser', //tested
        'getEvent' => 'getEvent', //tested
        'createEvent' => 'createEvent', //tested
        'updateEvent' => 'updateEvent', //tested
        'uploadPoster' => 'uploadPoster', //tested
        'getPoster' => 'getPoster', //tested
        'getTicketTypes' => 'getTicketTypes', //tested, need to change
        'createSingleTicketType' => 'createSingleTicketType', //tested, need to change
        'updateTicketType' => 'updateTicketType', //tested, need to change
        'purchaseTicket' => 'purchaseTicket',
        'makePayment' => 'makePayment', //tested
        // 'getPaper' => 'getPaper',
        // 'createPaper' => 'createPaper',
        // 'updatePaper' => 'updatePaper',
        'getRoom' => 'getRoom', //tested
        'createRoom' => 'createRoom', //tested
        'updateRoom' => 'updateRoom', //tested
        'getVenue' => 'getVenue', //tested
        'createVenue' => 'createVenue', //tested
        'updateVenue' => 'updateVenue', //tested
        'getSession' => 'getSession', //tested
        'createSession' => 'createSession', //tested
        'updateSession' => 'updateSession', //tested, failed
        'addUserTag' => 'addUserTag', //tested
        'addEventTag' => 'addEventTag', //tested
        'addPaperTag' => 'addPaperTag',
        'getUsersByTag' => 'getUsersByTag', //tested
        'getEventsByTag' => 'getEventsByTag', //tested
        'getPapersByTag' => 'getPapersByTag',
        'getResource' => 'getResource', //tested
        'createResource' => 'createResource', //tested
        'updateResource' => 'updateResource', //tested
        'getResourcesByRoom' => 'getResourcesByRoom',
        'getEventAttendees' => 'getEventAttendees',
        'getSessionAttendees' => 'getSessionAttendees',
        'addPaperAuthor' => 'addPaperAuthor',
        'getPaperAuthors' => 'getPaperAuthors'
    );

    public function store(Request $request)
    {
        $methodName = $request->input('method');

        if (array_key_exists($methodName, $this->requestMethods)) {
            return $this->$methodName($request);
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
            return JSONUtilities::returnError('[' . implode(', ', $required) . '] not found');
        }
    }

    private function createUser(Request $request)
    {
        $required = array('email', 'password');

        if ($request->has($required)) {
            return User::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnError('[' . implode(', ', $required) . '] not found');
        }
    }

    private function updateUser(Request $request)
    {
        $required = array('email');

        if (!$request->has($required)) {
            return JSONUtilities::returnError('[email] not found');
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
        if ($request->has('event_id')) {
            return Event::get($request->except(['method']));
        } else {
            return JSONUtilities::returnError('[event_id] not found');
        }
    }

    private function createEvent(Request $request)
    {
        $required = array('name', 'type', 'from_date', 'to_date', 'description', 'paper_deadline');

        if ($request->has($required)) {
            return Event::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnError('[' . implode(', ', $required) . '] not found');
        }
    }

    private function updateEvent(Request $request)
    {
        $required = array('event_id');

        if (!$request->has($required)) {
            return JSONUtilities::returnError('[event_id] not found');
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
            return JSONUtilities::returnError('[' . implode(', ', $required) . '] not found');
        }
    }

    private function getPoster(Request $request)
    {
        $required = array('event_id');

        if ($request->has($required)) {
            return Event::getPoster($request->except(['method']));
        } else {
            return JSONUtilities::returnError('[' . implode(', ', $required) . '] not found');
        }
    }

    private function getTicketTypes(Request $request)
    {
        $required = array('event_id');

        if ($request->has($required)) {
            return Ticket::get($request->except(['method']));
        } else {
            return JSONUtilities::returnError('[' . implode(', ', $required) . '] not found');
        }
    }

    private function createSingleTicketType(Request $request)
    {
        $required = array('event_id', 'name');

        if ($request->has($required)) {
            return Ticket::insertSingle($request->except(['method']));
        } else {
            return JSONUtilities::returnError('[' . implode(', ', $required) . '] not found');
        }
    }

    private function updateTicketType(Request $request)
    {
        $required = array('event_id', 'name');

        if (!$request->has($required)) {
            return JSONUtilities::returnError('[' . implode(', ', $required) . '] not found');
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
        $required = array('event_id', 'email', 'role', 'seat_no');

        if (!$request->has($required)) {
            return JSONUtilities::returnError('[' . implode(', ', $required) . '] not found');
        }

        $ticketSuccess = TicketType::purchaseTicket($request->only($required));

        if (!is_array($ticketSuccess)) {
            return $ticketSuccess;
        }

        $eventAttendedSuccess = EventAttended::insert($request->only($required));

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
            return JSONUtilities::returnError('[' . implode(', ', $required) . '] not found');
        }
    }

    private function getPaper(Request $request)
    {
        $required = array('paper_id');

        if ($request->has($required)) {
            return User::get($request->except(['method']));
        } else {
            return JSONUtilities::returnError('[' . implode(', ', $required) . '] not found');
        }
    }

    private function createPaper(Request $request)
    {
        $required = array('title', 'publish_date', 'latest_sub_date', 'status');

        if ($request->has($required)) {
            return User::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnError('[' . implode(', ', $required) . '] not found');
        }
    }

    private function updatePaper(Request $request)
    {
        $required = array('paper_id');

        if (!$request->has($required)) {
            return JSONUtilities::returnError('[' . implode(', ', $required) . '] not found');
        }

        $data = $request->except(array_merge(['method'], $required));

        if (!empty($data)) {
            return User::edit($request->only($required), $data);
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
            return JSONUtilities::returnError('[' . implode(', ', $required) . '] not found');
        }
    }

    private function createRoom(Request $request)
    {
        $required = array('venue_id', 'name', 'type', 'capacity');

        if ($request->has($required)) {
            return Room::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnError('[' . implode(', ', $required) . '] not found');
        }
    }

    private function updateRoom(Request $request)
    {
        $required = array('venue_id', 'name');

        if (!$request->has($required)) {
            return JSONUtilities::returnError('[' . implode(', ', $required) . '] not found');
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
            return JSONUtilities::returnError('[' . implode(', ', $required) . '] not found');
        }
    }

    private function createVenue(Request $request)
    {
        $required = array('name', 'type', 'has_room', 'street', 'city', 'state', 'country', 'longitude', 'latitude');

        if ($request->has($required)) {
            return Venue::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnError('[' . implode(', ', $required) . '] not found');
        }
    }

    private function updateVenue(Request $request)
    {
        $required = array('venue_id');

        if (!$request->has($required)) {
            return JSONUtilities::returnError('[' . implode(', ', $required) . '] not found');
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
        $required = array('event_id', 'title', 'speaker_email');

        if ($request->has($required)) {
            return Session::get($request->only($required));
        } else {
            return JSONUtilities::returnError('[' . implode(', ', $required) . '] not found');
        }
    }

    private function createSession(Request $request)
    {
        $required = array('event_id', 'title', 'speaker_email', 'start_time', 'end_time');

        if ($request->has($required)) {
            return Session::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnError('[' . implode(', ', $required) . '] not found');
        }
    }

    private function updateSession(Request $request)
    {
        $required = array('event_id', 'title', 'speaker_email');

        if (!$request->has($required)) {
            return JSONUtilities::returnError('[' . implode(', ', $required) . '] not found');
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
            return JSONUtilities::returnError('[' . implode(', ', $required) . '] not found');
        }
    }

    private function addEventTag(Request $request)
    {
        $required = array('event_id', 'tag_name');

        if ($request->has($required)) {
            return EventTag::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnError('[' . implode(', ', $required) . '] not found');
        }
    }

    private function addPaperTag(Request $request)
    {
        $required = array('paper_id', 'tag_name');

        if ($request->has($required)) {
            return PaperTag::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnError('[' . implode(', ', $required) . '] not found');
        }
    }

    private function getUsersByTag(Request $request)
    {
        $required = array('tag_name');

        if ($request->has($required)) {
            return User::getByTag($request->only($required));
        } else {
            return JSONUtilities::returnError('[' . implode(', ', $required) . '] not found');
        }
    }

    private function getEventsByTag(Request $request)
    {
        $required = array('tag_name');

        if ($request->has($required)) {
            return Event::getByTag($request->only($required));
        } else {
            return JSONUtilities::returnError('[' . implode(', ', $required) . '] not found');
        }
    }

    private function getPapersByTag(Request $request)
    {
        $required = array('tag_name');

        if ($request->has($required)) {
            return Paper::getByTag($request->only($required));
        } else {
            return JSONUtilities::returnError('[' . implode(', ', $required) . '] not found');
        }
    }

    private function getResource(Request $request)
    {
        $required = array('venue_id', 'room_name', 'name');

        if ($request->has($required)) {
            return Resource::get($request->only($required));
        } else {
            return JSONUtilities::returnError('[' . implode(', ', $required) . '] not found');
        }
    }

    private function createResource(Request $request)
    {
        $required = array('venue_id', 'room_name', 'name', 'type', 'number');

        if ($request->has($required)) {
            return Resource::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnError('[' . implode(', ', $required) . '] not found');
        }
    }

    private function updateResource(Request $request)
    {
        $required = array('venue_id', 'room_name', 'name');

        if (!$request->has($required)) {
            return JSONUtilities::returnError('[' . implode(', ', $required) . '] not found');
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
            return JSONUtilities::returnError('[' . implode(', ', $required) . '] not found');
        }
    }

    private function getEventAttendees(Request $request)
    {
        $required = array('event_id');

        if ($request->has($required)) {
            return EventAttended::get($request->only($required));
        } else {
            return JSONUtilities::returnError('[' . implode(', ', $required) . '] not found');
        }
    }

    private function getSessionAttendees(Request $request)
    {
        $required = array('event_id', 'title', 'speaker_email');

        if ($request->has($required)) {
            return SessionAttended::get($request->only($required));
        } else {
            return JSONUtilities::returnError('[' . implode(', ', $required) . '] not found');
        }
    }

    private function addPaperAuthor(Request $request)
    {
        $required = array('email', 'paper_id');

        if ($request->has($required)) {
            return PaperAuthored::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnError('[' . implode(', ', $required) . '] not found');
        }
    }

    private function getPaperAuthors(Request $request)
    {
        $required = array('paper_id');

        if ($request->has($required)) {
            return PaperAuthored::get($request->only($required));
        } else {
            return JSONUtilities::returnError('[' . implode(', ', $required) . '] not found');
        }
    }
}
