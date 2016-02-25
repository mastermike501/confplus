<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    public static function store(array $data = [])
    {
    	// return response()->json();
        // return response()->json(
        //     array(
        //         'success' => true,
        //         'user' => $request->input('name')
        //     )
        // );
    }

    public static function show($id)
    {
    	// return User::$users;
    }
}
