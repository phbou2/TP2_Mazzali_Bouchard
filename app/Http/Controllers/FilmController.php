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
    /**
 * @OA\Post(
 *     path="/createfilm",
 *     operationId="createFilm",
 *     tags={"Film"},
 *     summary="Create a new film",
 *     description="Allows creation of a new film in the database, only accessible by admins.",
 *     security={{"bearerAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         description="Data needed to create a new film",
 *         @OA\JsonContent(
 *             required={"title", "description", "release_year"},
 *             @OA\Property(property="title", type="string", example="Example Film Title"),
 *             @OA\Property(property="description", type="string", example="Description of the film."),
 *             @OA\Property(property="release_year", type="integer", example=2021)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Film created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Film created successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden - Only admins can create films",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Only admins can create films")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="object", example={"title": ["The title field is required."]})
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to create film",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Failed to create film: [Error Message]")
 *         )
 *     )
 * )
 */

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

    /**
 * @OA\Put(
 *     path="/updatefilm/{id}",
 *     operationId="updateFilm",
 *     tags={"Film"},
 *     summary="Update an existing film",
 *     description="Updates an existing film in the database, accessible only by admins.",
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the film to update",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         description="Data needed to update the film",
 *         @OA\JsonContent(
 *             required={"title", "description", "release_year"},
 *             @OA\Property(property="title", type="string", example="Updated Film Title"),
 *             @OA\Property(property="description", type="string", example="Updated description of the film."),
 *             @OA\Property(property="release_year", type="integer", example=2022)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Film updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Film updated successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden - Only admins can update films",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Only admins can update films")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Not Found - Film not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Film not found")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="object", example={"title": ["The title field is required."]})
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to update film",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Failed to update film: [Error Message]")
 *         )
 *     )
 * )
 */

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

/**
 * @OA\Delete(
 *     path="/deletefilm/{id}",
 *     operationId="deleteFilm",
 *     tags={"Film"},
 *     summary="Delete an existing film",
 *     description="Deletes a film from the database, accessible only by admins.",
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the film to delete",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Film deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Film deleted successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden - Only admins can delete films",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Only admins can delete films")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Not Found - Film not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Film not found")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error on input",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="object", example={"some_field": ["Validation message for the field"]})
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to delete film",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Failed to delete film: [Error Message]")
 *         )
 *     )
 * )
 */ 

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