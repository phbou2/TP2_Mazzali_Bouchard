<?php

namespace App\Repository\Eloquent;


use App\Models\User;
use App\Repository\UserRepositoryInterface;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{

    /**
    * ExampleRepository constructor.
    *
    * @param Example $model
    */
   public function __construct(User $model)
   {
       parent::__construct($model);
   }
}

?>