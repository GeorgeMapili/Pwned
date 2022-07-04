<?php

namespace App\Exceptions;

use Exception;

class UserEmailException extends Exception
{
    /**
     * Report the exception.
     *
     * @return bool|null
     */
    public function report()
    {
        //
    }
 
    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response()->json([
            'status'        => false,
            'message'       => 'Email has already been registered. Please try again.'
        ],422);
    }
}
