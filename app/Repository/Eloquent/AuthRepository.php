<?php

namespace App\Repository\Eloquent;


use App\Repository\AuthRepositoryInterface;

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
}

?>