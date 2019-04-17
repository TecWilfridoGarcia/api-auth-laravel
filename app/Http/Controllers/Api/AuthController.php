<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        if ($validator->fail()){
            return response(['errors'=>$validator->errors()->all()],422);
        }
        $request['password']=Hash::make($request['password']);
        $user = User::create($request->toArray());

        $token = $user->createToken('Laravel Password Grant Client')->accesToken;
        $response = ['token' => $token];
        return response($response, 200);
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if($user){
            if(Hash::check($request->password, $user->password)){
                $token = $user->createToken('Laravel Password Giant Client')->accessToken;
                $response = ['token' =>$token];
                return response($response, 200);
            }else{
                $response = "Password missmatch";
                return response($response, 422);
            }
        }else{
            $response ='User does not exist';
            return response($response, 422);
        }
    }

    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();
    
        $response = 'You have been succesfully logged out!';
        return response($response, 200);
    }
}
