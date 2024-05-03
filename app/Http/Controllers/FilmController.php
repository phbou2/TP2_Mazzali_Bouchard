<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\FilmResource;
use App\Models\Film;
use App\Repository\FilmRepositoryInterface;

class FilmController extends Controller
{   
    const FILM_RULES = [
        'title' => 'required|string',
        'release_year' => 'required|integer',
        'length' => 'required|integer',
        'description' => 'required|string',
        'rating' => 'required|string',
        'language_id' => 'required|integer',
        'special_features' => 'nullable|string',
        'image' => 'nullable|image',
    ];

    private FilmRepositoryInterface $filmRepository;

    public function __construct(FilmRepositoryInterface $filmRepository)
    {
        $this->filmRepository = $filmRepository;
    }

    public function create($request)
    {
        try{
            if (!$this->filmRepository->checkIfAdmin())
            {
                return response()->json(['error' => 'Only admins can create films'], FORBIDDEN);
            }

            $validator = Validator::make($request->all(), self::FILM_RULES);
    
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], INVALID_DATA);
            }

            $this->filmRepository->create($request->all());
            return response()->json(['message' => 'Film created successfully'], CREATED);
        
        }catch (\Exception $e)
        {
            return response()->json(['error' => 'Failed to create film: ' . $e->getMessage()], SERVER_ERROR);
        }

    }
    public function update(Request $request, $id)
    {
        try{
            if (!$this->filmRepository->checkIfAdmin()){
                return response()->json(['error' => 'Only admins can create films'], FORBIDDEN);
            }

            $film = $this->filmRepository->getById($id);

            if (!$film) {
                return response()->json(['error' => 'Film not found'], NOT_FOUND);
            }

            $validator = Validator::make($request->all(), self::FILM_RULES);
    
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], INVALID_DATA);
            }

            $this->filmRepository->update($id, $request->all());

            return response()->json(['message' => 'Film updated successfully'], OK);
 
        }catch (\Exception $e)
        {
            return response()->json(['error' => 'Failed to update film: ' . $e->getMessage()], SERVER_ERROR);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            if (!$this->filmRepository->checkIfAdmin()) {
                return response()->json(['error' => 'Only admins can delete films'], FORBIDDEN);
            }
    
            $film = $this->filmRepository->getById($id);
            if (!$film) {
                return response()->json(['error' => 'Film not found'], NOT_FOUND);
            }

            $validator = Validator::make($request->all(), self::FILM_RULES);
    
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], INVALID_DATA);
            }
    
            $this->filmRepository->delete($id);
    
            return response()->json(['message' => 'Film deleted successfully'], OK);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete film: ' . $e->getMessage()], SERVER_ERROR);
        }
    }
}

