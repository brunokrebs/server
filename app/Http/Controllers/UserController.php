<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

use App\User;
use Hash;

class UserController extends Controller
{
    public function adduser(Request $request) {
        
        $user = User::where( 'sub', $request->sub )->first();
        
        if(!$user) {

            $user = new User();

            $user->nickname = $request->nickname;
            $user->name = $request->name;
            $user->sub = $request->sub;
            $user->api_token = $request->api_token;

            $user->save();

        }

        $user->api_token = $request->api_token;
        $user->save();

        return response()
            ->json([
                'done' => $user->api_token
            ]);
        
    }

    //Not working yet
    // public function logout(Request $request)
    // {
    //     //$user = $request->user();
    //     $user = Auth::user();
    //     $user->api_token = null;
    //     $user->save();
        
    //     Auth::logout($user);

    //     return response()
    //         ->json([
    //             'done' => true
    //         ]);
    // }
}
