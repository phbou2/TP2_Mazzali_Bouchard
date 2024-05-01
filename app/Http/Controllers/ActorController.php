<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Actor;
use App\Repository\ActorRepositoryInterface;

class ActorController extends Controller
{
    private ActorRepositoryInterface $actorRepository;

    public function __construct(ActorRepositoryInterface $actorRepository)
    {
        $this->actorRepository = $actorRepository;
    }
}
