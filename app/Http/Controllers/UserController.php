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
 /**
 * @OA\Get(
 *     path="/user/{id}",
 *     summary="Retrieve the authenticated user's data",
 *     tags={"User"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Successful retrieval of user data",
 *         @OA\JsonContent(ref="#/components/schemas/UserResource")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Server Error"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation Error"
 *     )
 * )
 */
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
 /**
 * @OA\Path(
 *     path="/user/{id}/update",
 *     summary="Updates user's password",
 *     tags={"User"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="password", type="string", format="password", example="oldPassword123"),
 *             @OA\Property(property="new_password", type="string", format="password", example="newPassword123"),
 *             @OA\Property(property="confirm_password", type="string", format="password", example="newPassword123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Password updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Password changed")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized or Password mismatch"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error or Passwords do not match",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="object", example={"password": "The password must be at least 8 characters."})
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Server error"
 *     )
 * )
 */
    public function updatePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer',
                'password' => 'required|string|min:8',
                'new_password' => 'required|string|min:8',
                'confirm_password' => 'required|string|min:8|same:new_password',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], INVALID_DATA);
            }

            $user = $this->userRepository->getById($request->user_id);

            if (!$user) {
                return response()->json(['error' => 'User not found'], NOT_FOUND);
            }

            if ($request->user()->id !== $user->id) {
                return response()->json(['error' => 'You can only update your own password'], FORBIDDEN);
            }

            if (!Hash::check($request->password, $user->password)) {
                return response()->json(['error' => 'Incorrect current password'], UNAUTHORIZED);
            }

            $user->password = bcrypt($request->password);

            $this->userRepository->update($request->user()->id, $user);

            return response()->json(['message' => 'Password updated successfully'], OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update password: ' . $e->getMessage()], SERVER_ERROR);
        }
    }
}
