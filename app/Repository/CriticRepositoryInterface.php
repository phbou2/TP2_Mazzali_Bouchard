<?php

namespace App\Repository;

use App\Repository\RepositoryInterface;


interface CriticRepositoryInterface extends RepositoryInterface
{
    public function getByFilmAndUser($filmId, $userId);
}

?>