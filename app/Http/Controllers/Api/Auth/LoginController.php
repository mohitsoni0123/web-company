<?php

namespace App\Http\Controllers\Api\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LogoutResponse;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController as FortifyAuthenticatedSessionController;
use App\Models\User;
use DB;
class LoginController extends Controller
{

    public function index(Request $request)
    {  

       $user = app(\Laravel\Fortify\Contracts\CreatesNewUsers::class)->create($request->all());

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user,
        ],200);
    }

    public function login(Request $request)
    {
        $request->validate([
            Fortify::username() => 'required|string',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt($request->only(Fortify::username(), 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = User::where(Fortify::username(), $request->{Fortify::username()})->first();

        return response()->json([
            'token' => $user->createToken('api-token')->plainTextToken,
            'user' => $user,
        ]);
    }


    public function destroy(Request $request)
    {

 //        $hashed = hash('sha256', $request->bearerToken());
 // $data = DB::table('personal_access_tokens')->where('token', $hashed)->first();
 //        return response()->json([
 //        'token_sent' => $request->bearerToken(),
 //        'user' => $data,
 //        ]);
        $token = $request->user();
         //print_r($token); die;
     if ($token) {
            $token->currentAccessToken()->delete();
            Auth::guard('web')->logout();
            return response()->json(['message' => 'Logged out'],200);
        }

    return app(LogoutResponse::class);
    }
     
}
