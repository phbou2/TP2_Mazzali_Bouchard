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

    public function getByFilmAndUser($filmId, $userId)
    {
        return Critic::where('film_id', $filmId)
            ->where('user_id', $userId)
            ->first();
    }

}

?>