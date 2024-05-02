<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
/**
* @OA\Post(
*   path="/api/signin",
*   tags={"Auth"},
*   summary="Logs in a user",
*   description="Throttling: 5 requests per minute",
*   @OA\Response(
*     response=201,
*     description="Created"
*   ),
*   @OA\Response(
*     response=422,
*     description="Invalid data"
*   ),
*   @OA\Response(
*     response=500,
*     description="Server error"
*   ),
*   @OA\RequestBody(
*     @OA\MediaType(
*       mediaType="application/json",
*       @OA\Schema(
*         @OA\Property(
*           property="login",
*           type="string"
*         ),
*         @OA\Property(
*           property="password",
*           type="string"
*         )
*       )
*     )
*   )
* )
*/
    public function login(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'login' => ['required', 'string'],
                'password' => ['required', 'string'],
            ]);
    
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->first()], INVALID_DATA);
            }


            $credentials = [
                'login' => $request['login'],
                'password' => $request['password'],
            ];
    
            if (Auth::attempt($credentials)) {
                $token = Auth::user()->createToken('tokenName');
    
                return response()->json(['token' => $token->plainTextToken], OK);
            }else {
                return response()->json(['error' => 'invalid password'], INVALID_DATA);
            }
        }catch (\Exception $e){
            return response()->json(['error' => 'Failed to login user: ' . $e->getMessage()], SERVER_ERROR);
        }
    }
/**
* @OA\Post(
*   path="/api/signup",
*   tags={"Auth"},
*   summary="Registers a new user",
*   description="Throttling: 5 requests per minute",
*   @OA\Response(
*     response=201,
*     description="Created"
*   ),
*   @OA\Response(
*     response=422,
*     description="Invalid data"
*   ),
*   @OA\Response(
*     response=500,
*     description="Server error"
*   ),
*   @OA\RequestBody(
*     @OA\MediaType(
*       mediaType="application/json",
*       @OA\Schema(
*         @OA\Property(
*           property="login",
*           type="string"
*         ),
*         @OA\Property(
*           property="password",
*           type="string"
*         ),
*         @OA\Property(
*           property="email",
*           type="string",
*           format="email"
*         ),
*         @OA\Property(
*           property="last_name",
*           type="string"
*         ),
*         @OA\Property(
*           property="first_name",
*           type="string"
*         )
*       )
*     )
*   )
* )
*
*/        
    public function register(Request $user)
    {
        try{
            $validator = Validator::make($user->all(), [
                'login' => ['required', 'string', 'max:50'],
                'password' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:50', 'unique:users'],
                'last_name' => ['required', 'string', 'max:50'],
                'first_name' => ['required', 'string', 'max:50'],
                'role_id' => ['required', 'int', 'max:2'],
            ]);
        
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->first()], INVALID_DATA);
            }
            
            $newUser = new User();
            $newUser->login = $user['login'];
            $newUser->password = bcrypt($user['password']);
            $newUser->email = $user['email'];
            $newUser->last_name = $user['last_name'];
            $newUser->first_name = $user['first_name'];
            $newUser->role_id = $user['role_id'];
            $newUser->save();

            return response()->json(['message' => 'User registered succesfully'], CREATED);
            
        }catch (\Exception $e)
        {
            return response()->json(['error' => 'Failed to register user: ' . $e->getMessage()], SERVER_ERROR);
        }
    }
/**
* @OA\Post(
*   path="/api/signout",
*   tags={"Auth"},
*   summary="Logs out the user",
*   description="Throttling: 5 requests per minute",
*   @OA\Response(
*     response=204,
*     description="No content"
*   ),
*   @OA\Response(
*     response=500,
*     description="Server error"
*   )
* )
*/
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'User logged out successfully'], NO_CONTENT);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to logout user: ' . $e->getMessage()], SERVER_ERROR);
        }
    }
}
