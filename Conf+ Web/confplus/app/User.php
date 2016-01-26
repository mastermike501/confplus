<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
	private static $users = array('Amy', 'Bob', 'Carmen', 'Darren', 'Elijah');

    public static function get()
    {
    	return User::$users;
    }

    public static function create(array $attributes = [])
    {
    	// User::$users[] = $attributes['name'];
    }
}
