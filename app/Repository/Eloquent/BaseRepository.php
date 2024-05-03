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

    public function create(array $attributes): Model
    {
        return $this->model->create($attributes);
    }
 
    public function getById($id): ?Model
    {
        return $this->model->find($id);
    }

    public function update($id, $values): bool
    {
        return $this->model->where('id', $id)->update($values);
    }

    public function delete($id): bool
    {
        return $this->model->destroy($id) > 0;
    }

    public function checkIfAdmin($request)
    {
        if ($request->user() && $request->user()->role_id == ADMIN)
        {
            return true;
        } else {
            return false;
        }
    }
}

?>