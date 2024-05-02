<?php

namespace App\Repository\Eloquent;

use App\Repository\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;

define('ADMIN', 2);

class BaseRepository implements RepositoryInterface
{
    protected $model;

    public function __construct(Model $model)     
    {         
        $this->model = $model;
    }

    /**
    * @param array $attributes
    *
    * @return Model
    */
    public function create(array $attributes): Model
    {
        return $this->model->create($attributes);
    }
 
    /**
    * @param $id
    * @return Model
    */
    public function getById($id): ?Model
    {
        return $this->model->find($id);
    }

    public function checkIfAdmin($request)
    {
        if ($request->user() && $request->user()->role_id == ADMIN)
        {
            return true;
        }else {
            return false;
        }
    }
}
?>