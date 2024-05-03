<?php

namespace App\Repository\Eloquent;


use App\Repository\AuthRepositoryInterface;
use App\Models\User;

class AuthRepository implements AuthRepositoryInterface
{
    public function login($credentials)
    {
        if (Auth::attempt($credentials))
        {
            return Auth::user()->createToken('tokenName');
        }else {
            return null;
        }
    }
    public function createUser($user)
    {
        $newUser = new User();
        $newUser->login = $user['login'];
        $newUser->password = bcrypt($user['password']);
        $newUser->email = $user['email'];
        $newUser->last_name = $user['last_name'];
        $newUser->first_name = $user['first_name'];
        $newUser->role_id = $user['role_id'];
        $newUser->save();
    }

    public function deleteUserToken($user)
    {
        $user->user()->currentAccessToken()->delete();
    }
}

?>