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

   public function validateData($request)
   {
        $rules = [
            'title' => 'required|string',
            'release_year' => 'required|integer',
            'length' => 'required|integer',
            'description' => 'required|string',
            'rating' => 'required|string',
            'language_id' => 'required|exists:languages,id',
            'special_features' => 'nullable|string',
            'image' => 'nullable|image',
        ];

        $validator = Validator::make($request->all(), $rules);

        return $validator->passes();
   }

}

?>