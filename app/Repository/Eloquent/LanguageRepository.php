<?php

namespace App\Repository\Eloquent;


use App\Models\Language;
use App\Repository\LanguageRepositoryInterface;

class LanguageRepository extends BaseRepository implements LanguageRepositoryInterface
{

    /**
    * ExampleRepository constructor.
    *
    * @param Example $model
    */
   public function __construct(Language $model)
   {
       parent::__construct($model);
   }
}

?>