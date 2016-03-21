<?php

namespace App\Http\Helpers;

class JSONUtilities
{
	/**
	 * [returnError]
	 * @param  [string] $message [Error message]
	 * @return [JSON]          [description]
	 */
	public static function returnError($message)
    {
        return response()->json(
            array(
                'success' => 'false',
                'message' => 'Error: ' . $message
            )
        );
    }

	/**
	 * [returnData]
	 * @param  array  $data [data]
	 * @return [JSON]       [description]
	 */
	public static function returnData(array $data)
	{
		return response()->json(
			array(
				'success' => 'true',
				'data' => response()->json($data)
			)
		);
	}
}
