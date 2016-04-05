<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

use App\Http\Helpers\JSONUtilities;
use App\Http\Helpers\FormatUtilities;

class Billing extends Model
{
    private static $timecolumns = [
        'expiry_date' => 'm-Y'
    ];
    
    
}
