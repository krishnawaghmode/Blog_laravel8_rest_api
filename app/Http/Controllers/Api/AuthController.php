<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Hash;
use App\Models\User;

class AuthController extends Controller
{

    //Register User
    public function register(Request $request)
    {
        // $request->validate([
        //      'name' => 'required|min:2|max:100',
        //      'email' => 'required|email|unique:users',
        //      'password' => 'required|min:6|max:100',
        //      'confirm_password' => 'required|same:password'
        // ]);

        $validator = Validator::make($request->all(),[
             'name' => 'required|min:2|max:100',
             'email' => 'required|email|unique:users',
             'password' => 'required|min:6|max:100',
             'confirm_password' => 'required|same:password'
        ]);

        if($validator->fails()){

            return response()->json([
              'message' => 'Validations fails',
              'errors' => $validator->errors()
           ],422);

        }


      $user = User::create([
             'name'=>$request->name,
             'email'=>$request->email,
             'password'=>Hash::make($request->password)
      ]);
        
       
       return response()->json([
              'message' => 'Registration successfull',
              'data' => $user
           ],200);

    }



    // Login User
     public function login(Request $request)
     {

       $validator = Validator::make($request->all(),[
             'email' => 'required|email',
             'password' => 'required'
        ]);

        if($validator->fails()){

            return response()->json([
              'message' => 'Validations fails',
              'errors' => $validator->errors()
           ],422);

        }
        
        $user = User::where('email',$request->email)->first();

        if($user){

            if(Hash::check($request->password,$user->password)){

                $token = $user->createToken('auth-token')->plainTextToken;

                return response()->json([
                   'message' => 'Login successfull',
                   'token'   => $token,
                   'data'    =>$user
                ],200);

            }else{
                return response()->json([
                   'message' => 'Incorrect credentials',
              ],400);
            }

        }else{

            return response()->json([
              'message' => 'Incorrect credentials',
           ],400);

        }

    }

     //current user data fetch
     public function user(Request $request)
     {
         return response()->json([
                   'message' => 'User successfull fetched',
                   'data'    =>$request->user()
         ],200);

     }


     //User Logout
     public function logout(Request $request)
     {

        $request->user()->currentAccessToken()->delete();
         return response()->json([
                   'message' => 'User successfull logged out',
         ],200);

     }
}
