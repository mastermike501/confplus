<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Billing;
use App\COI;
use App\Conversation;
use App\Event;
use App\EventRate;
use App\EventRole;
use App\EventTag;
use App\Message;
use App\Paper;
use App\PaperAuthored;
use App\PaperReviewed;
use App\PaperSubmitted;
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

use Mail;

class ConfplusControllerV1 extends Controller
{
    private $requestMethods = array(
        'test',
        'testMail',
        
        'login',
        'getUser', //tested
        'createUser', //tested
        'updateUser', //tested
        'getEvent', //tested
        'createEvent', //tested
        'updateEvent', //tested
        'deleteEvent',
        'uploadPoster',
        'getPoster',
        'getTicket',
        'getTickets', //tested
        'createTicket', //tested
        'updateTicket', //tested
        'deleteTicket',
        'makePayment', //tested
        'getPaperDetails', //tested
        'getPaperDataUrl',
        'createPaper', //tested
        'updatePaper', //tested
        'deletePaper',
        'getRoom', //tested
        'getRooms', //tested
        'createRoom', //tested
        'updateRoom', //tested
        'deleteRoom',
        'getVenue', //tested
        'createVenue', //tested
        'updateVenue', //tested
        'deleteVenue',
        'getSession', //tested
        'getSessions', //tested
        'createSession', //tested
        'updateSession', //tested
        'deleteSession',
        'addUserTag', //tested
        'addEventTag', //tested
        'addPaperTag', //tested
        'getUsersByTag', //tested
        'deleteUserTag',
        'deleteEventTag',
        'deletePaperTag',
        'getEventsByTag', //tested
        'getPapersByTag', //tested
        'getResource', //tested
        'createResource', //tested
        'updateResource', //tested
        'deleteResource',
        'getResourcesByRoom', //tested
        'createTicketRecord', //tested
        'addSessionAttendee', //tested
        'getEventAttendees', //tested
        'getSessionAttendees', //tested
        'addPaperAuthor', //tested
        'getPaperAuthors', //tested
        'getPapersByAuthor', //tested
        'deletePaperAuthor',
        'getBillingInfo', //tested
        'createBillingInfo', //tested
        'updateBillingInfo', //tested
        'getPapersByReviewer', //tested
        'getReviewersByPaperId', //tested
        'addReviewer',
        'addReview', //tested
        'deleteReview',
        'createSeat', //tested
        'getSeatsInRoom', //tested
        'getEventsAttending',
        
        'getUpcomingEventsByCountry',
        'validateTicket',
        'getEventRolesForEvent', //tested
        'createConversation',
        'getConversationsByUser',
        'getConversation',
        'sendMessage',
        'getEventsByKeyword', //tested
        'getEventsManaged', //tested
        'getVenuesByLocation',
        'getUserTags', //tested
        'getEventTags', //tested
        'getPaperTags', //tested
        'getPaymentHistory',
        'requestToReview',
        'getRequestsToReview',
        'acceptPaper',
        
        'addEventRole',
        'getSeatsAndOccupants',
        'addPaperToEvent',
        'getPapersSubmittedToEvent',
        
        'createCOI',
        'getCOIOfReviewer',
        'getCOIOfAuthor',
        
        'getUserTicketsForEvent',
        'getTicketAndUser',
        
        'removeUserFromConversation',
        'getEventTickets',
        
        'editEventRole',
        'deleteEventRole',
        
        'inviteToEvent',
        'changePassword',
        'changeProfileImage',
        'getProfileImage',
        'addVenueMap',
        'getVenueMap',
        'getConversationParticipants',
        'addConversationForPaperReviewed',
        'addConversationForSession',
        
        'getAvailableRooms',
        'createSeats',
        'createTicketRecords',
        
        'addEventRating',
        'getEventRating',
        
        'updatePaperSubmitted',
        'getPaperSubmitted',
        'getConversationsByUserForEvent'        
    );
    
    public function store(Request $request)
    {
        $methodName = $request->input('method');

        if (in_array($methodName, $this->requestMethods)) {
            try {
                $dataArray = $request->all();
                
                $key = 'AHWQQPOAEkUoMjMPGep4za0PVaIOFyKt';
                $secret = 'KsBg70irVEho4FojGBHa301mlsKut0lD';
                
                if (!array_key_exists('api_key', $dataArray)) {
                    return JSONUtilities::returnError('The API key is missing.');
                }
                
                if (!array_key_exists('app_secret', $dataArray)) {
                    return JSONUtilities::returnError('The app secret is missing.');
                }
                
                $apiKey = $dataArray['api_key'];
                $appSecret = $dataArray['app_secret'];
                
                if (($key == $apiKey) && ($secret == $appSecret)) {
                    unset($dataArray['api_key']);
                    unset($dataArray['app_secret']);
                    
                    $request->replace($dataArray);
                    
                    return $this->$methodName($request);
                } else {
                    return JSONUtilities::returnError('The API key or app secret is not valid.');
                }
                
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

    private function testMail(Request $request)
    {
        $subject = 'Confplus Test Email';
        $to = $request->input('email');
        
        $data = [
            'title' => 'Registration',
            'body' => 'What a wonderful piece of text filling for the body!',
            'showButton' => true,
            'buttonName' => 'Click me',
            'buttonUrl' => 'https://google.com'
        ];
        
        $result = Mail::send('email_template', $data, function($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
        });
        
        if ($result) {
            return JSONUtilities::returnData(['message' => 'Mail sent.']);
        } else {
            return JSONUtilities::returnError('Error in sending mail.');
        }
    }

    /**
     * @api {post} / login
     * @apiGroup User
     * @apiName login
     *
     * @apiParam email The email of the user.
     * @apiParam password The password of the user.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.message Indicated successful login.
     */
    private function login(Request $request)
    {
        $required = array('email', 'password');
        
        if ($request->has($required)) {
            return User::login($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
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
     * @apiSuccess data.privacy
     * @apiSuccess data.payee
     * @apiSuccess data.cardNum
     * @apiSuccess data.contact_num
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
     * @apiParam privacy The event privacy. Must be either [public | private]
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.<data> Returns the event that was created. Refer to getEvent method for attributes.
     */
    private function createEvent(Request $request)
    {
        $required = array('name', 'type', 'from_date', 'to_date', 'description', 'venue_id', 'email', 'privacy');

        if ($request->has($required)) {
            
            $types = ['event', 'conference'];
            $type = $request->input('type');
            
            if (!in_array($type, $types)) {
                return JSONUtilities::returnError('Type must be "event" or "conference".');
            }
            
            $privacies = ['public', 'private'];
            $privacy = $request->input('privacy');
            
            if (!in_array($privacy, $privacies)) {
                return JSONUtilities::returnError('Privacy must be "public" or "private".');
            }
            
            return Event::insert($request->except(['method']));
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
     * @api {post} / deleteEvent
     * @apiGroup Event
     * @apiName deleteEvent
     *
     * @apiParam event_id The id of the event.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function deleteEvent(Request $request)
    {
        $required = array('event_id');

        if (!$request->has($required)) {
            return JSONUtilities::returnRequirementsError($required);
        }
        
        return Event::remove($request->except(['method']));
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
     * @api {post} / getTicket
     * @apiGroup Ticket
     * @apiName getTicket
     *
     * @apiParam event_id The event_id of the event.
     * @apiParam title The title of the session that this ticket will be linked to.
     * @apiParam name The name of the ticket.
     * @apiParam class The class of the ticket.
     * @apiParam type The type of the ticket.
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
    private function getTicket(Request $request)
    {
        $required = array('event_id', 'title', 'name', 'class', 'type');

        if ($request->has($required)) {
            return Ticket::get($request->except(['method']));
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
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.<data> Refer to getTicket method for attributes.
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
            return Ticket::insert($request->except(['method']));
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

    /**
     * @api {post} / deleteTicket
     * @apiGroup Ticket
     * @apiName deleteTicket
     *
     * @apiParam event_id The id of the event.
     * @apiParam title The title of the session.
     * @apiParam name The name of the ticket.
     * @apiParam class The class of the ticket.
     * @apiParam type The type of the ticket.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function deleteTicket(Request $request)
    {
        $required = array('event_id', 'title', 'name', 'class', 'type');

        if (!$request->has($required)) {
            return JSONUtilities::returnRequirementsError($required);
        }
        
        return Ticket::remove($request->except(['method']));
    }

    private function purchaseTicket(Request $request)
    {
        $required = array('event_id', 'title', 'ticket_name', 'class', 'type', 'venue_id', 'room_name', 'seat_num', 'email');

        if (!$request->has($required)) {
            return JSONUtilities::returnRequirementsError($required);
        }

        return TicketRecord::purchaseTicket($request->except(['method']));
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
     * @api {post} / getPaperDetails
     * @apiGroup Paper
     * @apiName getPaperDetails
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
    private function getPaperDetails(Request $request)
    {
        $required = array('paper_id');

        if ($request->has($required)) {
            return Paper::getPaperDetails($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @api {post} / getPaperDataUrl
     * @apiGroup Paper
     * @apiName getPaperDataUrl
     *
     * @apiParam paper_id The paper id of the paper.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.paper_data_url The data url of the paper.
     */
    private function getPaperDataUrl(Request $request)
    {
        $required = array('paper_id');

        if ($request->has($required)) {
            return Paper::getPaperDataUrl($request->except(['method']));
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
     * @apiSuccess data.id Id of the paper.
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
     * @api {post} / deletePaper
     * @apiGroup Paper
     * @apiName deletePaper
     *
     * @apiParam paper_id The event id of the event.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function deletePaper(Request $request)
    {
        $required = array('paper_id');

        if (!$request->has($required)) {
            return JSONUtilities::returnRequirementsError($required);
        }
        
        return Paper::remove($request->except(['method']));
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
     * @api {post} / deleteRoom
     * @apiGroup Room
     * @apiName deleteRoom
     *
     * @apiParam venue_id The venue that the room is to be associated with.
     * @apiParam room_name The name of the room.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function deleteRoom(Request $request)
    {
        $required = array('venue_id', 'room_name');

        if (!$request->has($required)) {
            return JSONUtilities::returnRequirementsError($required);
        }
        
        return Room::remove($request->except(['method']));
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
     * @apiSuccess data.id Id of the venue.
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
     * @api {post} / deleteVenue
     * @apiGroup Venue
     * @apiName deleteVenue
     *
     * @apiParam venue_id The id of the venue.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function deleteVenue(Request $request)
    {
        $required = array('venue_id');

        if (!$request->has($required)) {
            return JSONUtilities::returnRequirementsError($required);
        }
        
        return Venue::remove($request->except(['method']));
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
     * @apiSuccess data.privacy
     * @apiSuccess data.conversation_id The id of the conversation that attendees of the session can subscribe to.
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
     * @api {post} / deleteSession
     * @apiGroup Session
     * @apiName deleteSession
     *
     * @apiParam event_id The id of the event.
     * @apiParam title The title of the session.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function deleteSession(Request $request)
    {
        $required = array('event_id', 'title');

        if (!$request->has($required)) {
            return JSONUtilities::returnRequirementsError($required);
        }
        
        return Session::remove($request->except(['method']));
    }

    /**
     * @api {post} / addUserTag
     * @apiGroup User
     * @apiName addUserTag
     *
     * @apiParam email The email of the user.
     * @apiParam tag_names The comma delimited string of tags to associate this user with.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function addUserTag(Request $request)
    {
        $required = array('email', 'tag_names');

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
     * @apiParam tag_names The comma delimited string of tags to associate this event with.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function addEventTag(Request $request)
    {
        $required = array('event_id', 'tag_names');

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
     * @apiParam tag_names The comma delimited string of tags to associate this paper with.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function addPaperTag(Request $request)
    {
        $required = array('paper_id', 'tag_names');

        if ($request->has($required)) {
            return PaperTag::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / deleteUserTag
     * @apiGroup User
     * @apiName deleteUserTag
     *
     * @apiParam email The email of the user.
     * @apiParam tag_names A comma delimited array of tags to delete.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function deleteUserTag(Request $request)
    {
        $required = array('email', 'tag_names');

        if (!$request->has($required)) {
            return JSONUtilities::returnRequirementsError($required);
        }
        
        return UserTag::remove($request->except(['method']));
    }

    /**
     * @api {post} / deleteEventTag
     * @apiGroup Event
     * @apiName deleteEventTag
     *
     * @apiParam event_id The id of the event.
     * @apiParam tag_names A comma delimited array of tags to delete.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function deleteEventTag(Request $request)
    {
        $required = array('event_id', 'tag_names');

        if (!$request->has($required)) {
            return JSONUtilities::returnRequirementsError($required);
        }
        
        return EventTag::remove($request->except(['method']));
    }
    
    /**
     * @api {post} / deletePaperTag
     * @apiGroup Paper
     * @apiName deletePaperTag
     *
     * @apiParam paper_id The id of the paper.
     * @apiParam tag_names A comma delimited array of tags to delete.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function deletePaperTag(Request $request)
    {
        $required = array('paper_id', 'tag_names');

        if (!$request->has($required)) {
            return JSONUtilities::returnRequirementsError($required);
        }
        
        return PaperTag::remove($request->except(['method']));
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
     * @api {post} / deleteResource
     * @apiGroup Resource
     * @apiName deleteResource
     *
     * @apiParam venue_id The venue that the room is to be associated with.
     * @apiParam room_name The name of the room.
     * @apiParam name The name of the resource.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function deleteResource(Request $request)
    {
        $required = array('venue_id', 'room_name', 'name');

        if (!$request->has($required)) {
            return JSONUtilities::returnRequirementsError($required);
        }
        
        return Resource::remove($request->except(['method']));
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
     * @apiParam [venue_id] The id of the venue.
     * @apiParam [room_name] The name of the room in the given venue.
     * @apiParam [seat_num] The seat number.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function createTicketRecord(Request $request)
    {
        $required = array('event_id', 'title', 'ticket_name', 'class', 'type');

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
     * @api {post} / deletePaperAuthor
     * @apiGroup Paper
     * @apiName deletePaperAuthor
     *
     * @apiParam email The email of the author.
     * @apiParam paper_id The id of the paper.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function deletePaperAuthor(Request $request)
    {
        $required = array('email', 'paper_id');

        if (!$request->has($required)) {
            return JSONUtilities::returnRequirementsError($required);
        }
        
        return PaperAuthored::remove($request->except(['method']));
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
     * @apiParam [event_id] The id of an event. 
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
     * @api {post} / addReviewer
     * @apiGroup Paper
     * @apiName addReviewer
     *
     * @apiParam email The email of a reviewer.
     * @apiParam paper_id The id of a paper.
     * @apiParam event_id The id of an event.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function addReviewer(Request $request)
    {
        $required = array('email', 'paper_id', 'event_id');

        if ($request->has($required)) {
            return PaperReviewed::addReviewer($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / addReview
     * @apiGroup Paper
     * @apiName addReview
     *
     * @apiParam email The email of a reviewer.
     * @apiParam paper_id The id of a paper.
     * @apiParam event_id The id of an event.
     * @apiParam comment The comment on a paper by the reviewer.
     * @apiParam rate The rate given to the paper.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function addReview(Request $request)
    {
        $required = array('email', 'paper_id', 'event_id', 'comment', 'rate');

        if (!$request->has($required)) {
            return JSONUtilities::returnRequirementsError($required);
        }

        $data = $request->except(array_merge(['method'], $required));

        if (!empty($data)) {
            return PaperReviewed::addReview($request->only($required), $data);
        } else {
            return JSONUtilities::returnError('No data to update');
        }
    }

    /**
     * @api {post} / deleteReview
     * @apiGroup Paper
     * @apiName deleteReview
     *
     * @apiParam email The email of the reviewer.
     * @apiParam paper_id The id of the paper.
     * @apiParam event_id The id of the event.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function deleteReview(Request $request)
    {
        $required = array('email', 'paper_id', 'event_id');

        if ($request->has($required)) {
            return PaperReviewed::remove($request->only($required));
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
     * @api {post} / createConversation
     * @apiGroup Message
     * @apiName createConversation
     *
     * @apiParam emails A comma delimited string of emails.
     * @apiParam [name] The name of the conversation.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.id The id of the conversation.
     */
    private function createConversation(Request $request)
    {
        $required = array('emails');

        if ($request->has($required)) {
            return Conversation::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @api {post} / getConversationsByUser
     * @apiGroup Message
     * @apiName getConversationsByUser
     *
     * @apiParam email The email of the user.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.email
     * @apiSuccess data.conversation_id
     * @apiSuccess data.date
     * @apiSuccess data.content The latest message of the conversation.
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
     * @api {post} / sendMessage
     * @apiGroup Message
     * @apiName sendMessage
     *
     * @apiParam sender_email The email of the sender.
     * @apiParam conversation_id The id of the conversation.
     * @apiParam content The content of the message.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function sendMessage(Request $request)
    {
        $required = array('sender_email', 'conversation_id', 'content');

        if ($request->has($required)) {
            return Message::insert($request->except(['method']));
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

    /**
     * @api {post} / getPaymentHistory
     * @apiGroup Payment
     * @apiName getPaymentHistory
     *
     * @apiParam email The email of the user.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.email
     * @apiSuccess data.payment_id The id of the payment.
     * @apiSuccess data.type The description of what the payment was for.
     * @apiSuccess data.amount The amount of payment.
     * @apiSuccess data.payment_date The date of the payment.
     */
    private function getPaymentHistory(Request $request)
    {
        $required = array('email');

        if ($request->has($required)) {
            return Payment::getHistory($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @api {post} / requestToReview
     * @apiGroup Paper
     * @apiName requestToReview
     *
     * @apiParam email The email of the user requesting to review.
     * @apiParam paper_id The id of a paper.
     * @apiParam event_id The id of an event.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function requestToReview(Request $request)
    {
        $required = array('email', 'paper_id', 'event_id');

        if ($request->has($required)) {
            return PaperReviewed::requestToReview($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @api {post} / getRequestsToReview
     * @apiGroup Paper
     * @apiName getRequestsToReview
     *
     * @apiParam event_id The id of the event.
     * @apiParam [paper_id] The id of the paper.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.<data> Refer to getUser method for attributes.
     */
    private function getRequestsToReview(Request $request)
    {
        $required = array('event_id');

        if ($request->has($required)) {
            return PaperReviewed::get($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @api {post} / addEventRole
     * @apiGroup Event
     * @apiName addEventRole
     *
     * @apiParam email The email of the user.
     * @apiParam event_id The id of the event.
     * @apiParam role_name The name of the role..
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function addEventRole(Request $request)
    {
        $required = array('email', 'event_id', 'role_name');

        if ($request->has($required)) {
            return EventRole::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @api {post} / getSeatsAndOccupants
     * @apiGroup Ticket
     * @apiName getSeatsAndOccupants
     *
     * @apiParam event_id The id of the event.
     * @apiParam title The title of the session.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.event_id The event_id of the event.
     * @apiSuccess data.title The title of the session that this ticket will be linked to.
     * @apiSuccess data.ticket_name The name of the ticket.
     * @apiSuccess data.class The class of the ticket.
     * @apiSuccess data.type The type of the ticket.
     * @apiSuccess data.venue_id The id of the venue.
     * @apiSuccess data.room_name The name of the room in the given venue.
     * @apiSuccess data.seat_num The seat number.
     */
    private function getSeatsAndOccupants(Request $request)
    {
        $required = array('event_id', 'title');

        if ($request->has($required)) {
            return TicketRecord::getSeatsAndOccupants($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @api {post} / acceptPaper
     * @apiGroup Paper
     * @apiName acceptPaper
     *
     * @apiParam accept Value indicating whether reviewer accepts paper. Must be [accepted | rejected | coi]
     * @apiParam email The email of a reviewer.
     * @apiParam paper_id The id of a paper.
     * @apiParam event_id The id of an event.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function acceptPaper(Request $request)
    {
        $required = array('accept', 'email', 'paper_id', 'event_id');
        
        if ($request->has($required)) {
            return PaperReviewed::acceptPaper($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @api {post} / addPaperToEvent
     * @apiGroup Paper
     * @apiName addPaperToEvent
     *
     * @apiParam paper_id The id of the paper.
     * @apiParam event_id The id of the event.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function addPaperToEvent(Request $request)
    {
        $required = array('paper_id', 'event_id');

        if ($request->has($required)) {
            return PaperSubmitted::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @api {post} / getPapersSubmittedToEvent
     * @apiGroup Paper
     * @apiName getPapersSubmittedToEvent
     *
     * @apiParam event_id The id of the event.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.<data> Refer to getPaper method for attributes.
     */
    private function getPapersSubmittedToEvent(Request $request)
    {
        $required = array('event_id');

        if ($request->has($required)) {
            return Paper::getByEvent($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }

    /**
     * @api {post} / createCOI
     * @apiGroup COI
     * @apiName createCOI
     *
     * @apiParam reviewer The email of the reviewer.
     * @apiParam author The email of the author.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function createCOI(Request $request)
    {
        $required = array('reviewer', 'author');

        if ($request->has($required)) {
            return COI::insert($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @api {post} / getCOIOfReviewer
     * @apiGroup COI
     * @apiName getCOIOfReviewer
     *
     * @apiParam reviewer The email of the reviewer.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.reviewer The email of the reviewer.
     * @apiSuccess data.author The email of the author.
     */
    private function getCOIOfReviewer(Request $request)
    {
        $required = array('reviewer');

        if ($request->has($required)) {
            return COI::getByReviewer($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @api {post} / getCOIOfAuthor
     * @apiGroup COI
     * @apiName getCOIOfAuthor
     *
     * @apiParam author The email of the author.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.reviewer The email of the reviewer.
     * @apiSuccess data.author The email of the author.
     */
    private function getCOIOfAuthor(Request $request)
    {
        $required = array('author');

        if ($request->has($required)) {
            return COI::getByAuthor($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @api {post} / getTicketAndUser
     * @apiGroup TicketRecord
     * @apiName getTicketAndUser
     *
     * @apiParam ticket_id The id of the ticket record.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.<data> Refer to getTicket method for attributes.
     */
    private function getTicketAndUser(Request $request)
    {
        $required = array('ticket_id');

        if ($request->has($required)) {
            return TicketRecord::getTicketAndUser($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @api {post} / getUserTicketsForEvent
     * @apiGroup User
     * @apiName getUserTicketsForEvent
     *
     * @apiParam event_id The id of the event.
     * @apiParam email The email of the user.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.<data> Refer to getTicket method for attributes.
     */
    private function getUserTicketsForEvent(Request $request)
    {
        $required = array('event_id', 'email');

        if ($request->has($required)) {
            return TicketRecord::getUserTicketsForEvent($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @api {post} / removeUserFromConversation
     * @apiGroup Message
     * @apiName removeUserFromConversation
     *
     * @apiParam conversation_id The id of the conversation.
     * @apiParam email The email of the user.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.message Indicated successful login.
     */
    private function removeUserFromConversation(Request $request)
    {
        $required = array('conversation_id', 'email');
        
        if ($request->has($required)) {
            return Conversation::removeUser($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @api {post} / getEventTickets
     * @apiGroup Ticket
     * @apiName getEventTickets
     *
     * @apiParam event_id The event_id of the event.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.<data> Refer to getTicket method for attributes.
     */
    private function getEventTickets(Request $request)
    {
        $required = array('event_id');

        if ($request->has($required)) {
            return Ticket::getByEvent($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @api {post} / editEventRole
     * @apiGroup Event
     * @apiName editEventRole
     *
     * @apiParam email The email of the user.
     * @apiParam event_id The id of the event.
     * @apiParam role_name The name of the role.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function editEventRole(Request $request)
    {
        $required = array('email', 'event_id', 'role_name');

        if ($request->has($required)) {
            return EventRole::edit($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @api {post} / deleteEventRole
     * @apiGroup Event
     * @apiName deleteEventRole
     *
     * @apiParam email The email of the reviewer.
     * @apiParam paper_id The id of the paper.
     * @apiParam event_id The id of the event.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function deleteEventRole(Request $request)
    {
        $required = array('email', 'event_id');

        if ($request->has($required)) {
            return EventRole::remove($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @api {post} / inviteToEvent
     * @apiGroup Event
     * @apiName inviteToEvent
     *
     * @apiParam inviter The email of the inviter.
     * @apiParam emails A comma delimited string of emails to send the invitation to.
     * @apiParam event_url The URL of the event.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.message Indicates successful sending of email(s).
     */
    private function inviteToEvent(Request $request)
    {
        $required = array('inviter', 'emails', 'event_url');
        
        if (!$request->has($required)) {
            return JSONUtilities::returnRequirementsError($required);
        }
        
        $subject = 'Event Invitation';
        $emails = explode(',', $request->input('emails'));
        $emailArray = array_map(function($item) {
            return trim($item);
        }, $emails);
        
        $body = '
            <p>Hi there! You have been invited by '. $request->input('inviter') .' to attend an event!</p>
            <p>To know more about the event, click the button below!</p>
        ';
        
        $data = [
            'title' => 'Invitation to Event',
            'body' => $body,
            'showButton' => true,
            'buttonName' => 'Event Details',
            'buttonUrl' => $request->input('event_url')
        ];
        
        $result = Mail::send('email_template', $data, function($message) use ($emailArray, $subject) {
            $message->to($emailArray)->subject($subject);
        });
        
        if ($result) {
            return JSONUtilities::returnData(['message' => 'Mail sent.']);
        } else {
            return JSONUtilities::returnError('Error in sending mail.');
        }
    }
    
    /**
     * @api {post} / changePassword
     * @apiGroup User
     * @apiName changePassword
     *
     * @apiParam email The email of the user.
     * @apiParam old_password The old password of the user.
     * @apiParam new_password The new password of the user.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function changePassword(Request $request)
    {
        $required = array('email', 'old_password', 'new_password');

        if ($request->has($required)) {
            return User::changePassword($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @api {post} / changeProfileImage
     * @apiGroup User
     * @apiName changeProfileImage
     *
     * @apiParam email The email of the user.
     * @apiParam image_data_url The data url of the image.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.message Indicated successful change.
     */
    private function changeProfileImage(Request $request)
    {
        $required = array('email', 'image_data_url');
        
        if ($request->has($required)) {
            return User::changeProfileImage($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @api {post} / getProfileImage
     * @apiGroup User
     * @apiName getProfileImage
     *
     * @apiParam email The email of the user.
     * @apiParam first_name
     * @apiParam last_name
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.image_data_url The data url of the profile image.
     */
    private function getProfileImage(Request $request)
    {
        $required = array('email');
        
        if ($request->has($required)) {
            return User::getProfileImage($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
     /**
     * @api {post} / addVenueMap
     * @apiGroup Venue
     * @apiName addVenueMap
     *
     * @apiParam venue_id The id of the venue.
     * @apiParam image_data_url The data url of the image.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.message Indicated successful change.
     */
    private function addVenueMap(Request $request)
    {
        $required = array('venue_id', 'image_data_url');
        
        if ($request->has($required)) {
            return Venue::addVenueMap($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @api {post} / getVenueMap
     * @apiGroup Venue
     * @apiName getVenueMap
     *
     * @apiParam venue_id The id of the venue.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.image_data_url The data url of the venue image.
     */
    private function getVenueMap(Request $request)
    {
        $required = array('venue_id');
        
        if ($request->has($required)) {
            return Venue::getVenueMap($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @api {post} / getConversationParticipants
     * @apiGroup Message
     * @apiName getConversationParticipants
     *
     * @apiParam conversation_id The email of the user.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.conversation_id The id of the conversation.
     * @apiSuccess data.<userData> Refer to getUser method for attributes.
     */
    private function getConversationParticipants(Request $request)
    {
        $required = array('conversation_id');

        if ($request->has($required)) {
            return Conversation::getConversationParticipants($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @api {post} / addConversationForPaperReviewed
     * @apiGroup PaperReviewed
     * @apiName addConversationForPaperReviewed
     *
     * @apiParam reviewer The email of the reviewer.
     * @apiParam paper_id The id of the paper.
     * @apiParam event_id The id of the event.
     * @apiParam moderator The email of the conversation moderator.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.conversation_id The id of the conversation.
     */
    private function addConversationForPaperReviewed(Request $request)
    {
        $required = array('reviewer', 'paper_id', 'event_id', 'moderator');

        if ($request->has($required)) {
            return PaperReviewed::addConversation($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @api {post} / addConversationForSession
     * @apiGroup Session
     * @apiName addConversationForSession
     *
     * @apiParam event_id The id of the event.
     * @apiParam title The title of the session.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.conversation_id The id of the conversation.
     */
    private function addConversationForSession(Request $request)
    {
        $required = array('reviewer', 'paper_id', 'event_id', 'moderator');

        if ($request->has($required)) {
            return Session::addConversation($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @api {post} / getAvailableRooms
     * @apiGroup Venue
     * @apiName getAvailableRooms
     *
     * @apiParam from_date The starting datetime. Inclusive.
     * @apiParam to_date The ending datetime. Inclusive.
     * @apiParam venue_id The id of the venue.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.<data> Refer to getRoom method for attributes.
     */
    private function getAvailableRooms(Request $request)
    {
        $required = array('from_date', 'to_date', 'venue_id');

        if ($request->has($required)) {
            return Venue::getAvailableRooms($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @api {post} / createSeats
     * @apiGroup Seat
     * @apiName createSeats
     *
     * @apiParam venue_id The id of a venue.
     * @apiParam name The name of a room in the venue.
     * @apiParam seat_nums A comma delimited string of seat numbers to insert.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function createSeats(Request $request)
    {
        $required = array('venue_id', 'name', 'seat_nums');

        if ($request->has($required)) {
            return Seat::insertSeats($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @api {post} / createTicketRecords
     * @apiGroup TicketRecord
     * @apiName createTicketRecords
     *
     * @apiParam event_id The event_id of the event.
     * @apiParam title The title of the session that this ticket will be linked to.
     * @apiParam ticket_name The name of the ticket.
     * @apiParam class The class of the ticket.
     * @apiParam type The type of the ticket.
     * @apiParam venue_id The id of the venue.
     * @apiParam room_name The name of the room in the given venue.
     * @apiParam seat_nums A comma delimited string of seat numbers to associate this ticket category with.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function createTicketRecords(Request $request)
    {
        $required = array('event_id', 'title', 'ticket_name', 'class', 'type', 'venue_id', 'room_name', 'seat_nums');

        if ($request->has($required)) {
            return TicketRecord::insertRecords($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @api {post} / addEventRating
     * @apiGroup Event
     * @apiName addEventRating
     *
     * @apiParam email The email of the user.
     * @apiParam event_id The id of the event.
     * @apiParam rate The rating given by the user. Must be between 1 and 5 inclusive.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.message Indicated successful login.
     */
    private function addEventRating(Request $request)
    {
        $required = array('email', 'event_id', 'rate');
        
        if ($request->has($required)) {
            return EventRate::insert($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @api {post} / getEventRating
     * @apiGroup Event
     * @apiName getEventRating
     *
     * @apiParam event_id The id of the event.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON array containing the following data:
     * @apiSuccess data.rate The rate value.
     * @apiSuccess data.count The number of times the rate was given.
     */
    private function getEventRating(Request $request)
    {
        $required = array('event_id');
        
        if ($request->has($required)) {
            return EventRate::get($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @api {post} / updatePaperSubmitted
     * @apiGroup Paper
     * @apiName updatePaperSubmitted
     *
     * @apiParam paper_id The id of the paper.
     * @apiParam event_id The id of the event.
     * @apiParam status The status of the paper.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.message Message denoting success.
     */
    private function updatePaperSubmitted(Request $request)
    {
        $required = array('paper_id', 'event_id', 'status');

        if ($request->has($required)) {
            return PaperSubmitted::edit($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @api {post} / getPaperSubmitted
     * @apiGroup Paper
     * @apiName getPaperSubmitted
     *
     * @apiParam paper_id The id of the paper.
     * @apiParam event_id The id of the event.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.paper_id
     * @apiSuccess data.event_id
     * @apiSuccess data.status
     */
    private function getPaperSubmitted(Request $request)
    {
        $required = array('paper_id', 'event_id');

        if ($request->has($required)) {
            return PaperSubmitted::get($request->except(['method']));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
    
    /**
     * @api {post} / getConversationsByUserForEvent
     * @apiGroup Message
     * @apiName getConversationsByUserForEvent
     *
     * @apiParam email The email of the user.
     * @apiParam event_id The id of the event.
     *
     * @apiSuccess success Returns true upon success.
     * @apiSuccess data JSON containing the following data:
     * @apiSuccess data.<data> Refer to getConversationsByUser for attributes.
     */
    private function getConversationsByUserForEvent(Request $request)
    {
        $required = array('email', 'event_id');

        if ($request->has($required)) {
            return Conversation::getConversationsByUserForEvent($request->only($required));
        } else {
            return JSONUtilities::returnRequirementsError($required);
        }
    }
}
