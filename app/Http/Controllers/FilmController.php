<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\FilmResource;
use App\Models\Film;
use App\Repository\FilmRepositoryInterface;

class FilmController extends Controller
{   
    private FilmRepositoryInterface $filmRepository;

    public function __construct(FilmRepositoryInterface $filmRepository)
    {
        $this->filmRepository = $filmRepository;
    }

    public function index()
    {
        return $this->$filmRepository->getAll();        
    }

    public function create($request)
    {
        try{
            if ($this->$filmRepository->checkIfAdmin())
            {
                if ($this->$filmRepository->validateData())
                {
                    $this->$filmRepository->create($request->all());
                    return response()->json(['message' => 'Film created successfully'], CREATED);
                }else {
                    return response()->json(['error' => 'Invalid data'], INVALID_DATA);
                }
            }else {
                return response()->json(['error' => 'Only admins can create films'], FORBIDDEN);
            }
        }catch (\Exception $e)
        {
            return response()->json(['error' => 'Failed to create film: ' . $e->getMessage()], SERVER_ERROR);
        }

    }
    public function update(Request $request, $id)
    {
        try{
            if ($this->$filmRepository->checkIfAdmin())
            {
                if ($this->$filmRepository->validateData())
                {
                    $film = $this->filmRepository->getById($id);
                    if (!$film) {
                        return response()->json(['error' => 'Film not found'], NOT_FOUND);
                    }

                    $this->filmRepository->update($id, $request->all());

                    return response()->json(['message' => 'Film updated successfully'], OK);
                    
                }else {
                    return response()->json(['error' => 'Invalid data'], INVALID_DATA);
                }
            }else {
                return response()->json(['error' => 'Only admins can create films'], FORBIDDEN);
            }
        }catch (\Exception $e)
        {
            return response()->json(['error' => 'Failed to create film: ' . $e->getMessage()], SERVER_ERROR);
        }
    }
}

