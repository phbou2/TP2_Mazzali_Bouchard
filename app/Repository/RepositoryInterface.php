<?php

namespace App\Repository;

interface RepositoryInterface
{
    public function create(array $content);
    public function getById($id);
    public function update($id, array $content);
    public function delete($id);
    public function checkIfAdmin($request);
}

?>