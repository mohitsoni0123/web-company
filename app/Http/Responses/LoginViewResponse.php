<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginViewResponse as LoginViewResponseContract;

class LoginViewResponse implements LoginViewResponseContract
{
    public function toResponse($request)
    {
       // return view('auth.login'); // Make sure this Blade view exists
         return response()->json([
            'message' => 'Please check Token',
        ]);
    }
}
