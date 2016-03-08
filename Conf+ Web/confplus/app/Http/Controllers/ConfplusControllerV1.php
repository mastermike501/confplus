<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use App\Event;

use App\Http\Helpers\JSONUtilities;

class ConfplusControllerV1 extends Controller
{
    public function store(Request $request)
    {
        $methodName = $request->input('method');

        switch ($methodName) {
            case 'get_user':

                if ($request->has('email')) {
                    return User::get($request->except(['method']));
                } else {
                    return JSONUtilities::returnError('[email] not found');
                }

                break;

            case 'create_user':
                $required = array('email', 'password');

                if ($request->has($required)) {
                    return User::insert($request->except(['method']));
                } else {
                    return JSONUtilities::returnError('[' . implode(', ', $required) . '] not found');
                }

                break;

            case 'update_user':

                $data = $request->except(['method']);

                if (!empty($data)) {
                    return User::edit($data);
                } else {
                    return JSONUtilities::returnError('No data to update');
                }

                break;

            case 'get_event':

                if ($request->has('event_id')) {
                    return Event::get($request->except(['method']));
                } else {
                    return JSONUtilities::returnError('[event_id] not found');
                }

                break;

            case 'create_event':
                $required = array('event_id', 'name', 'type', 'from_date', 'to_date', 'description', 'url', 'paper_deadline');

                if ($request->has($required)) {
                    return Event::insert($request->except(['method']));
                } else {
                    return JSONUtilities::returnError('[' . implode(', ', $required) . '] not found');
                }

                break;

            case 'update_user':

                $data = $request->except(['method']);

                if (!empty($data)) {
                    return Event::edit($data);
                } else {
                    return JSONUtilities::returnError('No data to update');
                }

                break;

            default:
                return JSONUtilities::returnError('Method ' . $methodName . ' not found.');
                break;
        }

    }
}
