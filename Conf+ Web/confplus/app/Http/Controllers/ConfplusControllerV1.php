<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Billing;
use App\Conversation;
use App\Event;
use App\EventRole;
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
        'uploadPoster',
        'getPoster',
        'getTickets', //tested
        'createTicket', //tested
        'updateTicket', //tested
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
        'createTicketRecord', //tested
        'addSessionAttendee', //tested
        'getEventAttendees', //tested
        'getSessionAttendees', //tested
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
        'getSeatsInRoom', //tested
        'getEventsAttending',
        
        'getUpcomingEventsByCountry',
        'validateTicket',
        'getEventRolesForEvent', //tested
        'getConversationsByUser',
        'getConversation',
        'getEventsByKeyword', //tested
        'getEventsManaged', //tested
        'getVenuesByLocation',
        'getUserTags', //tested
        'getEventTags', //tested
        'getPaperTags', //tested
        // 'acceptPaper'
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

    /**
     * @api {post} / getUser
     * @apiGroup User
     * @apiName getUser
     *
     * @apiParam email The email of the user.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.email
     * @apiSuccess data.username
     * @apiSuccess data.password
     * @apiSuccess data.title
     * @apiSuccess data.first_name
     * @apiSuccess data.last_name
     * @apiSuccess data.dob
     * @apiSuccess data.street
     * @apiSuccess data.city
     * @apiSuccess data.state
     * @apiSuccess data.country
     * @apiSuccess data.verified
     * @apiSuccess data.fb_id
     * @apiSuccess data.linkedin_id
     * @apiSuccess data.active
     * @apiSuccess data.upgraded
     * @apiSuccess data.review
     */
    private function getUser(Request $request)
    {
        $required = array('email');

        if ($request->has($required)) {
            return User::get($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / createUser
     * @apiGroup User
     * @apiName createUser
     *
     * @apiParam email The email of the user. Must be unique.
     * @apiParam password The password of the user.
     * @apiParam username The username of the user. Must be unique.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function createUser(Request $request)
    {
        $required = array('email', 'password', 'username');

        if ($request->has($required)) {
            return User::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / updateUser
     * @apiGroup User
     * @apiName updateUser
     *
     * @apiParam email The email of the user.
     * @apiParam [title]
     * @apiParam [first_name]
     * @apiParam [last_name]
     * @apiParam [dob] Format: yyyy-mm-dd hh:mm
     * @apiParam [street]
     * @apiParam [city]
     * @apiParam [state]
     * @apiParam [country]
     * @apiParam [verified]
     * @apiParam [fb_id]
     * @apiParam [linkedin_id]
     * @apiParam [active]
     * @apiParam [upgraded]
     * @apiParam [review]
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
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

    /**
     * @api {post} / getEvent
     * @apiGroup Event
     * @apiName getEvent
     *
     * @apiParam event_id The event_id of the event.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.event_id
     * @apiSuccess data.name
     * @apiSuccess data.type
     * @apiSuccess data.from_date
     * @apiSuccess data.to_date
     * @apiSuccess data.description
     * @apiSuccess data.url
     * @apiSuccess data.poster_url
     * @apiSuccess data.paper_deadline
     * @apiSuccess data.language
     * @apiSuccess data.reminder
     * @apiSuccess data.venue_id
     */
    private function getEvent(Request $request)
    {
        $required = array('event_id');

        if ($request->has($required)) {
            return Event::get($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / createEvent
     * @apiGroup Event
     * @apiName createEvent
     *
     * @apiParam name The name of the event.
     * @apiParam type The type of the event. Must be either [event | conference]
     * @apiParam from_date The date that the event starts. Format: yyyy-mm-dd hh:mm
     * @apiParam to_date The date that the event ends. Format: yyyy-mm-dd hh:mm
     * @apiParam description A description of the event that provides additional information about the event.
     * @apiParam venue_id The venue of the event.
     * @apiParam email The email of the user creating the event.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.<data> Returns the event that was created. Refer to getEvent method for attributes.
     */
    private function createEvent(Request $request)
    {
        $required = array('name', 'type', 'from_date', 'to_date', 'description', 'venue_id', 'email');

        if ($request->has($required)) {
            
            $types = ['event', 'conference'];
            $type = $request->input('type');
            
            if (in_array($type, $types)) {
                return Event::insert($request->except(['method']));
            }
            
            return JSONUtilities::returnError('Type must be "event" or "conference".');
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / updateEvent
     * @apiGroup Event
     * @apiName updateEvent
     *
     * @apiParam event_id The event id of the event.
     * @apiParam [name]
     * @apiParam [type]
     * @apiParam [from_date] Format: yyyy-mm-dd hh:mm
     * @apiParam [to_date] Format: yyyy-mm-dd hh:mm
     * @apiParam [description]
     * @apiParam [poster_url] Data URL formst
     * @apiParam [paper_deadline] Format: yyyy-mm-dd hh:mm
     * @apiParam [language]
     * @apiParam [reminder]
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
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

    /**
     * @api {post} / uploadPoster
     * @apiGroup Event
     * @apiName uploadPoster
     *
     * @apiParam event_id The event_id of the event.
     * @apiParam poster_data_url The poster data url.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.poster_data_url Poster data url.
     */
    private function uploadPoster(Request $request)
    {
        $required = array('event_id', 'poster_data_url');

        if (!$request->has($required)) {
            return JSONUtilities::returnRequirementsError($required);
        }

        return Event::uploadPoster($request->except(['method']));
    }
    
    /**
     * @api {post} / getPoster
     * @apiGroup Event
     * @apiName getPoster
     *
     * @apiParam event_id The event_id of the event.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.poster_data_url The poster data url.
     */
    private function getPoster(Request $request)
    {
        $required = array('event_id');

        if ($request->has($required)) {
            return Event::getPoster($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / getTickets
     * @apiGroup Ticket
     * @apiName getTickets
     *
     * @apiParam event_id The event_id of the event.
     * @apiParam title The title of the session that this ticket will be linked to.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.event_id
     * @apiSuccess data.title
     * @apiSuccess data.name The name of the ticket.
     * @apiSuccess data.class
     * @apiSuccess data.type
     * @apiSuccess data.price
     * @apiSuccess data.description A brief description of the ticket.
     * @apiSuccess data.start_date
     * @apiSuccess data.end_date
     * @apiSuccess data.quantity
     * @apiSuccess data.num_purchased
     */
    private function getTickets(Request $request)
    {
        $required = array('event_id', 'title');

        if ($request->has($required)) {
            return Ticket::getTypes($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / createTicket
     * @apiGroup Ticket
     * @apiName createTicket
     *
     * @apiParam event_id The event_id of the event.
     * @apiParam title The title of the session that this ticket will be linked to.
     * @apiParam name The name of the ticket.
     * @apiParam class The class of the ticket.
     * @apiParam type The type of the ticket.
     * @apiParam price The price of the ticket. Format: XX.xx
     * @apiParam description A brief description of the ticket.
     * @apiParam start_date The start date of the ticket. Format: yyyy-mm-dd hh:mm
     * @apiParam end_date The end date of the ticket. Format: yyyy-mm-dd hh:mm
     * @apiParam quantity The number of tickets of this category.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function createTicket(Request $request)
    {
        $required = array('event_id', 'title', 'name', 'class',
            'type', 'price', 'description', 'start_date', 'end_date', 'quantity');

        if ($request->has($required)) {
            return Ticket::insertSingle($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / updateTicket
     * @apiGroup Ticket
     * @apiName updateTicket
     *
     * @apiParam event_id The event id of the event.
     * @apiParam title The title of the session.
     * @apiParam name The name of the ticket.
     * @apiParam class The class of the ticket.
     * @apiParam type The type of the ticket.
     * @apiParam [price]
     * @apiParam [description]
     * @apiParam [start_date]
     * @apiParam [end_date]
     * @apiParam [quantity]
     * @apiParam [num_purchased]
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function updateTicket(Request $request)
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

    /**
     * @api {post} / makePayment
     * @apiGroup Payment
     * @apiName makePayment
     *
     * @apiParam email The name of the payee.
     * @apiParam type The type of the payment. Eg: upgrade, ticket purchase, etc.
     * @apiParam amount The amount that is being paid. Format: XX.xx
     * @apiParam payment_date The date that the payment is made. Format: yyyy-mm-dd hh:mm
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function makePayment(Request $request)
    {
        $required = array('email', 'type', 'amount', 'payment_date');

        if ($request->has($required)) {
            return Payment::insert($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / getPaper
     * @apiGroup Paper
     * @apiName getPaper
     *
     * @apiParam paper_id The paper id of the paper.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.paper_id
     * @apiSuccess data.title
     * @apiSuccess data.publish_date Format: yyyy-mm-dd hh:mm
     * @apiSuccess data.latest_submit_date Format: yyyy-mm-dd hh:mm
     * @apiSuccess data.status
     * @apiSuccess data.accept
     * @apiSuccess data.final_rate Rating given to this paper.
     * @apiSuccess data.url
     */
    private function getPaper(Request $request)
    {
        $required = array('paper_id');

        if ($request->has($required)) {
            return Paper::get($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / createPaper
     * @apiGroup Paper
     * @apiName createPaper
     *
     * @apiParam title The title of the paper.
     * @apiParam publish_date The publish date of the paper. Format: yyyy-mm-dd hh:mm
     * @apiParam latest_submit_date The latest submit date date of the paper. Format: yyyy-mm-dd hh:mm
     * @apiParam paper_data_url The paper in a data URL format.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function createPaper(Request $request)
    {
        $required = array('title', 'publish_date', 'latest_submit_date', 'paper_data_url');

        if ($request->has($required)) {
            return Paper::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / updatePaper
     * @apiGroup Paper
     * @apiName updatePaper
     *
     * @apiParam paper_id The event id of the event.
     * @apiParam [title] The title of the paper.
     * @apiParam [publish_date] The publish date of the paper. Format: yyyy-mm-dd hh:mm
     * @apiParam [latest_submit_date] The latest submit date date of the paper. Format: yyyy-mm-dd hh:mm
     * @apiParam [paper_data_url] The paper in a data URL format.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
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

    /**
     * @api {post} / getRoom
     * @apiGroup Room
     * @apiName getRoom
     * @apiDescription Gets a particular room of a venue.
     *
     * @apiParam venue_id The venue id of the venue.
     * @apiParam name The name of the room.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.venue_id
     * @apiSuccess data.name
     * @apiSuccess data.type The type of the room. Eg: concert hall, room, stadium, lecture hall, etc.
     * @apiSuccess data.capacity Room's capacity
     */
    private function getRoom(Request $request)
    {
        $required = array('venue_id', 'name');

        if ($request->has($required)) {
            return Room::get($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / getRooms
     * @apiGroup Room
     * @apiName getRooms
     * @apiDescription Gets all rooms of a venue.
     *
     * @apiParam venue_id The venue id of the venue.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.venue_id
     * @apiSuccess data.name
     * @apiSuccess data.type The type of the room. Eg: concert hall, room, stadium, lecture hall, etc.
     * @apiSuccess data.capacity The room's capacity.
     */
    private function getRooms(Request $request)
    {
        $required = array('venue_id');

        if ($request->has($required)) {
            return Room::getRooms($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / createRoom
     * @apiGroup Room
     * @apiName createRoom
     *
     * @apiParam venue_id The venue that the room is to be associated with.
     * @apiParam name The name of the room. Must be unique per venue.
     * @apiParam type The type of the room. Eg: concert hall, room, stadium, lecture hall, etc.
     * @apiParam capacity The room's capacity.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function createRoom(Request $request)
    {
        $required = array('venue_id', 'name', 'type', 'capacity');

        if ($request->has($required)) {
            return Room::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / updateRoom
     * @apiGroup Room
     * @apiName updateRoom
     *
     * @apiParam venue_id The venue id of the venue.
     * @apiParam name The name of the room.
     * @apiParam [type] The type of the room. Eg: concert hall, room, stadium, lecture hall, etc.
     * @apiParam [capacity] The room's capacity.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
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

    /**
     * @api {post} / getVenue
     * @apiGroup Venue
     * @apiName getVenue
     *
     * @apiParam venue_id The venue id of the venue.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.venue_id
     * @apiSuccess data.name The name of the venue.
     * @apiSuccess data.type The type of the venue.
     * @apiSuccess data.street
     * @apiSuccess data.city
     * @apiSuccess data.state
     * @apiSuccess data.country
     * @apiSuccess data.longitude
     * @apiSuccess data.latitude
     */
    private function getVenue(Request $request)
    {
        $required = array('venue_id');

        if ($request->has($required)) {
            return Venue::get($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / createVenue
     * @apiGroup Venue
     * @apiName createVenue
     *
     * @apiParam name The name of the venue.
     * @apiParam type The type of the venue.
     * @apiParam street
     * @apiParam city
     * @apiParam state
     * @apiParam country
     * @apiParam longitude
     * @apiParam latitude
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function createVenue(Request $request)
    {
        $required = array('name', 'type', 'street', 'city', 'state', 'country', 'longitude', 'latitude');

        if ($request->has($required)) {
            return Venue::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / updateVenue
     * @apiGroup Venue
     * @apiName updateVenue
     *
     * @apiParam venue_id The venue id of the venue.
     * @apiParam [name] The name of the venue.
     * @apiParam [type] The type of the venue.
     * @apiParam [street]
     * @apiParam [city]
     * @apiParam [state]
     * @apiParam [country]
     * @apiParam [longitude]
     * @apiParam [latitude]
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
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

    /**
     * @api {post} / getSession
     * @apiGroup Session
     * @apiName getSession
     *
     * @apiParam event_id The event id that this session is associated with.
     * @apiParam title The title of the session.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.event_id
     * @apiSuccess data.title
     * @apiSuccess data.speaker_email
     * @apiSuccess data.start_time Format: yyyy-mm-dd hh:mm
     * @apiSuccess data.end_time
     * @apiSuccess data.venue_id
     * @apiSuccess data.room_name
     */
    private function getSession(Request $request)
    {
        $required = array('event_id', 'title');

        if ($request->has($required)) {
            return Session::get($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / getSessions
     * @apiGroup Session
     * @apiName getSessions
     *
     * @apiParam event_id The event id that this session is associated with.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.<data> Refer to getSession method for attributes.
     */
    private function getSessions(Request $request)
    {
        $required = array('event_id');

        if ($request->has($required)) {
            return Session::getSessions($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / createSession
     * @apiGroup Venue
     * @apiName createSession
     *
     * @apiParam event_id The name of the venue.
     * @apiParam title The type of the venue.
     * @apiParam start_time Format: yyyy-mm-dd hh:mm
     * @apiParam end_time Format: yyyy-mm-dd hh:mm
     * @apiParam venue_id The id of the venue.
     * @apiParam room_name The name of the room.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function createSession(Request $request)
    {
        $required = array('event_id', 'title', 'start_time', 'end_time', 'venue_id', 'room_name');

        if ($request->has($required)) {
            return Session::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / updateSession
     * @apiGroup Session
     * @apiName updateSession
     *
     * @apiParam event_id The name of the venue.
     * @apiParam title The type of the venue.
     * @apiParam [start_time] Format: yyyy-mm-dd hh:mm
     * @apiParam [end_time] Format: yyyy-mm-dd hh:mm
     * @apiParam [venue_id] The id of the venue.
     * @apiParam [room_name] The name of the room.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
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

    /**
     * @api {post} / addUserTag
     * @apiGroup User
     * @apiName addUserTag
     *
     * @apiParam email The email of the user.
     * @apiParam tag_name The tag to associate this user with.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function addUserTag(Request $request)
    {
        $required = array('email', 'tag_name');

        if ($request->has($required)) {
            return UserTag::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / addEventTag
     * @apiGroup Event
     * @apiName addEventTag
     *
     * @apiParam event_id The event id of the event.
     * @apiParam tag_name The tag to associate this event with.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function addEventTag(Request $request)
    {
        $required = array('event_id', 'tag_name');

        if ($request->has($required)) {
            return EventTag::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / addPaperTag
     * @apiGroup Paper
     * @apiName addPaperTag
     *
     * @apiParam paper_id The paper id of the paper.
     * @apiParam tag_name The tag to associate this paper with.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function addPaperTag(Request $request)
    {
        $required = array('paper_id', 'tag_name');

        if ($request->has($required)) {
            return PaperTag::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / getUsersByTag
     * @apiGroup User
     * @apiName getUsersByTag
     *
     * @apiParam tag_name The tag to associate this paper with.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.<data> Refer to getUser method for attributes.
     */
    private function getUsersByTag(Request $request)
    {
        $required = array('tag_name');

        if ($request->has($required)) {
            return User::getByTag($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / getEventsByTag
     * @apiGroup Event
     * @apiName getEventsByTag
     *
     * @apiParam tag_name The tag to associate this paper with.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.<data> Refer to getEvent method for attributes.
     */
    private function getEventsByTag(Request $request)
    {
        $required = array('tag_name');

        if ($request->has($required)) {
            return Event::getByTag($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / getPapersByTag
     * @apiGroup Paper
     * @apiName getPapersByTag
     *
     * @apiParam tag_name The tag to associate this paper with.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.<data> Refer to getPaper method for attributes.
     */
    private function getPapersByTag(Request $request)
    {
        $required = array('tag_name');

        if ($request->has($required)) {
            return Paper::getByTag($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / getResource
     * @apiGroup Resource
     * @apiName getResource
     *
     * @apiParam venue_id The venue that the room is to be associated with.
     * @apiParam room_name The name of the room.
     * @apiParam name The name of the resource. Must be unique per room.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.venue_id
     * @apiSuccess data.room_name
     * @apiSuccess data.name
     * @apiSuccess data.type
     * @apiSuccess data.number
     */
    private function getResource(Request $request)
    {
        $required = array('venue_id', 'room_name', 'name');

        if ($request->has($required)) {
            return Resource::get($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / createResource
     * @apiGroup Resource
     * @apiName createResource
     *
     * @apiParam venue_id The venue that the room is to be associated with.
     * @apiParam room_name The name of the room.
     * @apiParam name The name of the resource. Must be unique per room.
     * @apiParam type The type of resource. Eg: Stationery, electronics, etc.
     * @apiParam number Number of this resource in this room.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function createResource(Request $request)
    {
        $required = array('venue_id', 'room_name', 'name', 'type', 'number');

        if ($request->has($required)) {
            return Resource::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / updateResource
     * @apiGroup Resource
     * @apiName updateResource
     *
     * @apiParam venue_id The id of the venue.
     * @apiParam room_name The name of the room in the given venue.
     * @apiParam name The name of the resource to update.
     * @apiParam [type]
     * @apiParam [number]
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
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

    /**
     * @api {post} / getResourcesByRoom
     * @apiGroup Resource
     * @apiName getResourcesByRoom
     *
     * @apiParam venue_id The venue that the room is to be associated with.
     * @apiParam room_name The name of the room.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.venue_id
     * @apiSuccess data.room_name
     * @apiSuccess data.name
     * @apiSuccess data.type
     * @apiSuccess data.number
     */
    private function getResourcesByRoom(Request $request)
    {
        $required = array('venue_id', 'room_name');

        if ($request->has($required)) {
            return Resource::getByRoom($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / createTicketRecord
     * @apiGroup TicketRecord
     * @apiName createTicketRecord
     *
     * @apiParam event_id The event_id of the event.
     * @apiParam title The title of the session that this ticket will be linked to.
     * @apiParam ticket_name The name of the ticket.
     * @apiParam class The class of the ticket.
     * @apiParam type The type of the ticket.
     * @apiParam venue_id The id of the venue.
     * @apiParam room_name The name of the room in the given venue.
     * @apiParam seat_num The seat number.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function createTicketRecord(Request $request)
    {
        $required = array('event_id', 'title', 'ticket_name', 'class', 'type', 'venue_id', 'room_name', 'seat_num');

        if ($request->has($required)) {
            return TicketRecord::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / addSessionAttendee
     * @apiGroup TicketRecord
     * @apiName addSessionAttendee
     *
     * @apiParam event_id The event_id of the event.
     * @apiParam title The title of the session that this ticket will be linked to.
     * @apiParam ticket_name The name of the ticket.
     * @apiParam class The class of the ticket.
     * @apiParam type The type of the ticket.
     * @apiParam venue_id The id of the venue.
     * @apiParam room_name The name of the room in the given venue.
     * @apiParam seat_num The seat number.
     * @apiParam email The email of the attendee.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
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

    /**
     * @api {post} / getEventAttendees
     * @apiGroup TicketRecord
     * @apiName getEventAttendees
     *
     * @apiParam event_id The event_id of the event.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.<data> Refer to getUser method for attributes.
     */
    private function getEventAttendees(Request $request)
    {
        $required = array('event_id');

        if ($request->has($required)) {
            return TicketRecord::getEventAttendees($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / getSessionAttendees
     * @apiGroup TicketRecord
     * @apiName getSessionAttendees
     *
     * @apiParam event_id The event_id of the event.
     * @apiParam title The title of the session.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.<data> Refer to getUser method for attributes.
     */
    private function getSessionAttendees(Request $request)
    {
        $required = array('event_id', 'title');

        if ($request->has($required)) {
            return TicketRecord::getSessionAttendees($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / addPaperAuthor
     * @apiGroup Paper
     * @apiName addPaperAuthor
     *
     * @apiParam email The email of the author.
     * @apiParam paper_id The id of the paper to associate the author with.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function addPaperAuthor(Request $request)
    {
        $required = array('email', 'paper_id');

        if ($request->has($required)) {
            return PaperAuthored::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / getPaperAuthors
     * @apiGroup Paper
     * @apiName getPaperAuthors
     *
     * @apiParam paper_id The id of the paper.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.<data> Refer to getUser method for attributes.
     */
    private function getPaperAuthors(Request $request)
    {
        $required = array('paper_id');

        if ($request->has($required)) {
            return PaperAuthored::get($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / getPapersByAuthor
     * @apiGroup Paper
     * @apiName getPapersByAuthor
     *
     * @apiParam email The email of an author.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.<data> Refer to getPaper method for attributes.
     */
    private function getPapersByAuthor(Request $request)
    {
        $required = array('email');

        if ($request->has($required)) {
            return Paper::getByAuthor($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / getBillingInfo
     * @apiGroup BillingInfo
     * @apiName getBillingInfo
     *
     * @apiParam email The email of a user.
     * @apiParam card# The card number of the user's credit card.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.<data> Refer to getPaper method for attributes.
     */
    private function getBillingInfo(Request $request)
    {
        $required = array('email', 'card#');

        if ($request->has($required)) {
            return Billing::get($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / createBillingInfo
     * @apiGroup BillingInfo
     * @apiName createBillingInfo
     *
     * @apiParam email The email of a user.
     * @apiParam card# The card number of the user's credit card.
     * @apiParam card_type The card type of the credit card. Eg: Visa, Mastercard
     * @apiParam expiry_date The credit card's expiry date. Format: yyyy-mm-dd hh:mm
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function createBillingInfo(Request $request)
    {
        $required = array('email', 'card#', 'card_type', 'expiry_date');

        if ($request->has($required)) {
            return Billing::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / updateBillingInfo
     * @apiGroup BillingInfo
     * @apiName updateBillingInfo
     *
     * @apiParam email The email of a user.
     * @apiParam card# The card number of the user's credit card.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
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

    /**
     * @api {post} / getPapersByReviewer
     * @apiGroup Paper
     * @apiName getPapersByReviewer
     *
     * @apiParam email The email of a reviewer.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.<data> Refer to getPaper method for attributes.
     */
    private function getPapersByReviewer(Request $request)
    {
        $required = array('email');

        if ($request->has($required)) {
            return Paper::getByReviewer($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / getReviewersByPaperId
     * @apiGroup Paper
     * @apiName getReviewersByPaperId
     *
     * @apiParam paper_id The id of a paper.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.<data> Refer to getUser method for attributes.
     */
    private function getReviewersByPaperId(Request $request)
    {
        $required = array('paper_id');

        if ($request->has($required)) {
            return User::getReviewersByPaperId($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / addPaperReviewed
     * @apiGroup Paper
     * @apiName addPaperReviewed
     *
     * @apiParam email The email of a reviewer.
     * @apiParam card# The id of a paper.
     * @apiParam comment The comment on a paper by the reviewer.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function addPaperReviewed(Request $request)
    {
        $required = array('email', 'paper_id', 'comment');

        if ($request->has($required)) {
            return PaperReviewed::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / createSeat
     * @apiGroup Seat
     * @apiName createSeat
     *
     * @apiParam venue_id The id of a venue.
     * @apiParam name The name of a room in the venue.
     * @apiParam seat_num The seat number to insert.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function createSeat(Request $request)
    {
        $required = array('venue_id', 'name', 'seat_num');

        if ($request->has($required)) {
            return Seat::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / getSeatsInRoom
     * @apiGroup Seat
     * @apiName getSeatsInRoom
     *
     * @apiParam venue_id The id of a venue.
     * @apiParam name The name of a room in the venue.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.venue_id
     * @apiSuccess data.name
     * @apiSuccess data.seat_num
     */
    private function getSeatsInRoom(Request $request)
    {
        $required = array('venue_id', 'name');

        if ($request->has($required)) {
            return Seat::getSeatsInRoom($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / getEventsAttending
     * @apiGroup User
     * @apiName getEventsAttending
     *
     * @apiParam email The email of the user.
     * @apiParam criteria Criteria for searching. Values: [all, past, future]
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.event_id
     * @apiSuccess data.name
     * @apiSuccess data.type
     * @apiSuccess data.from_date
     * @apiSuccess data.to_date
     * @apiSuccess data.description
     * @apiSuccess data.url
     * @apiSuccess data.poster_url
     * @apiSuccess data.paper_deadline
     * @apiSuccess data.language
     * @apiSuccess data.reminder
     */
    private function getEventsAttending(Request $request)
    {
        $required = array('email', 'criteria');

        if ($request->has($required)) {
            $criteria = $request->input('criteria');
            $values = ['all', 'past', 'future'];
            
            if (in_array($criteria, $values)) {
                return User::getEventsAttending($request->only($required));
            }
            
            return JSONUtilities::returnError('Criteria values allowed: ' . implode(' | ', $values));
            
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @apiIgnore Untested
     * @api {post} / getUpcomingEventsByCountry
     * @apiGroup Event
     * @apiName getUpcomingEventsByCountry
     *
     * @apiParam country The country to get events in.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.<data> Refer to getEvent method for attributes.
     */
    private function getUpcomingEventsByCountry(Request $request)
    {
        $required = array('country');

        if ($request->has($required)) {
            return Event::getUpcomingByCountry($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @apiIgnore Untested
     * @api {post} / validateTicket
     * @apiGroup TicketRecord
     * @apiName validateTicket
     *
     * @apiParam ticket_id The id of the ticket.
     * @apiParam email The email of the user.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function validateTicket(Request $request)
    {
        $required = array('ticket_id', 'email');

        if ($request->has($required)) {
            return TicketRecord::validateTicket($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / getEventRolesForEvent
     * @apiGroup EventRole
     * @apiName getEventRolesForEvent
     *
     * @apiParam event_id The id of the event.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.email
     * @apiSuccess data.event_id
     * @apiSuccess data.role_name
     */
    private function getEventRolesForEvent(Request $request)
    {
        $required = array('event_id');

        if ($request->has($required)) {
            return EventRole::get($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    
    /**
     * @apiIgnore Untested
     * @api {post} / getConversationsByUser
     * @apiGroup Message
     * @apiName getConversationsByUser
     *
     * @apiParam email The email of the user.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.email
     * @apiSuccess data.conversation_id
     */
    private function getConversationsByUser(Request $request)
    {
        $required = array('email');

        if ($request->has($required)) {
            return Conversation::getByUser($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @apiIgnore Untested
     * @api {post} / getConversation
     * @apiGroup Message
     * @apiName getConversation
     *
     * @apiParam conversation_id The id of the conversation.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.sender_email
     * @apiSuccess data.conversation_id
     * @apiSuccess data.date
     * @apiSuccess data.content
     */
    private function getConversation(Request $request)
    {
        $required = array('conversation_id');

        if ($request->has($required)) {
            return Conversation::get($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @api {post} / getEventsByKeyword
     * @apiGroup Event
     * @apiName getEventsByKeyword
     *
     * @apiDescription Searches names and descriptions of events that contain a given keyword.
     *
     * @apiParam keyword The keyword to use for searching.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.<data> Refer to getEvent method for attributes.
     */
    private function getEventsByKeyword(Request $request)
    {
        $required = array('keyword');

        if ($request->has($required)) {
            return Event::getByKeyword($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @api {post} / getEventsManaged
     * @apiGroup Event
     * @apiName getEventsManaged
     *
     * @apiParam email The name of the event manager.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.<data> Refer to getEvent method for attributes.
     */
    private function getEventsManaged(Request $request)
    {
        $required = array('email');

        if ($request->has($required)) {
            return EventRole::getEventsManaged($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @api {post} / getVenuesByLocation
     * @apiGroup Venue
     * @apiName getVenuesByLocation
     *
     * @apiParam country The country to search in.
     * @apiParam [state] The state to search in.
     * @apiParam [city] The city to search in.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.<data> Refer to getEvent method for attributes.
     */
    private function getVenuesByLocation(Request $request)
    {
        $required = array('country');

        if ($request->has($required)) {
            return Venue::getByLocation($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @api {post} / getUserTags
     * @apiGroup User
     * @apiName getUserTags
     *
     * @apiParam email The email of the user.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the tag names.
     */
    private function getUserTags(Request $request)
    {
        $required = array('email');

        if ($request->has($required)) {
            return UserTag::getUserTags($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @api {post} / getEventTags
     * @apiGroup Event
     * @apiName getEventTags
     *
     * @apiParam event_id The id of the event.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the tag names.
     */
    private function getEventTags(Request $request)
    {
        $required = array('event_id');

        if ($request->has($required)) {
            return EventTag::getEventTags($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @api {post} / getPaperTags
     * @apiGroup Paper
     * @apiName getPaperTags
     *
     * @apiParam paper_id The id of the paper.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the tag names.
     */
    private function getPaperTags(Request $request)
    {
        $required = array('paper_id');

        if ($request->has($required)) {
            return PaperTag::getPaperTags($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
}
