<?php

namespace App\Http\Controllers;


use Validator;
use App\User;
use Illuminate\Http\Request;

class MobileAPIController extends Controller
{
    public  function sign_up(Request $request)
    {
        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->picture = $request->input('picture');
        $user->password = bcrypt($request->input('password'));

        if(User::where('email', $user->email)->first())
        {
            $response['success'] = 0;
            $response['message'] = 'Email already exist';
            return response()->json($response, 200);
        }
        else
        {
            $user->save();
        }

        //getting the user id to store the display picture
        $user_id = $user->id;

        //decode base64 picture back to image
        $picture = $request->input('picture');
        file_put_contents('images/display_pictures/'.$user_id.'.png', base64_decode($picture));
        $picture_url = 'http://example.com/images/display_pictures/'.$user_id.'.png?';
        $user->picture_url = $picture_url;
        $user->save();

        //response
        $response['success'] = 1;
        $response['user_id'] = $user_id;
        return response()->json($response, 200);
    }

    public function login(Request $request)
    {
        $email = $request->input('email');
        $password =  bcrypt($request->input('password'));
        $user= User::where('email', $email)->where('password', $password)->first();

        if($user)
        {
            $response['success'] = 1;
            return response()->json($response, 200);
        }
        else
        {
            $response['success'] = 0;
            $response['message'] = 'Invalid Email or Password';
            return response()->json($response, 200);
        }
    }

}

