<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Resources\LanguageResource;
use App\Models\User;
use App\Models\Language;
use Illuminate\Support\Facades\Validator;
use App\Repository\UserRepositoryInterface;

class UserController extends Controller
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function show(request $request){
        try{
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], INVALID_DATA);
            }

            $user = $this->userRepository->getById($request->user_id);

            if (!$user){
                return response()->json(['error' => 'User not found'], NOT_FOUND);
            }

            if ($request->user()->id !== $user->id){
                return response()->json(['error' => 'You can only view your user information'], FORBIDDEN);
            }

            return response()->json(new UserResource($user), OK);


        }catch (\Exception $e){
            return response()->json(['error' => 'Failed to show user: ' . $e->getMessage()], SERVER_ERROR);
        }
    }
}
