<?php

namespace App\Repository\Eloquent;


use App\Models\Film;
use App\Repository\FilmRepositoryInterface;
use App\Repository\Eloquent\BaseRepository;
use Illuminate\Support\Facades\Validator;

class FilmRepository extends BaseRepository implements FilmRepositoryInterface
{

    /**
    * ExampleRepository constructor.
    *
    * @param Example $model
    */
   public function __construct(Film $model)
   {
       parent::__construct($model);
   }

}

?>