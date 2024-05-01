<?php

namespace App\Repository\Eloquent;


use App\Models\Critic;
use App\Repository\CriticRepositoryInterface;

class CriticRepository extends BaseRepository implements CriticRepositoryInterface
{

    /**
    * ExampleRepository constructor.
    *
    * @param Example $model
    */
   public function __construct(Critic $model)
   {
       parent::__construct($model);
   }
}

?>