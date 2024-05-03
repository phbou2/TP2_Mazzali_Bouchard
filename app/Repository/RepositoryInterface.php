<?php

namespace App\Repository;

interface RepositoryInterface
{
    public function create(array $content);
    //public function getAll($perPage = 0);
    public function getById($id);
    public function update($id, array $content);
    //public function delete($id);
    public function checkIfAdmin($request);
}

?>