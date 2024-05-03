<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Critic;
use App\Repository\CriticRepositoryInterface;

class CriticController extends Controller
{
    const CRITIC_RULES = [
        'user_id' => 'required|integer',
        'film_id' => 'required|integer',
        'score' => 'required|integer',
        'comment' => 'required|string',
    ];
    

    private CriticRepositoryInterface $criticRepository;

    public function __construct(CriticRepositoryInterface $criticRepository)
    {
        $this->criticRepository = $criticRepository;
    }
    
    public function store(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), self::CRITIC_RULES);
        
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], INVALID_DATA);
            }

            //changer par le repositoty
            $critic = $this->criticRepository->getByFilmAndUser($request->film_id, $request->user()->id);

            if ($critic) {
                return response()->json(['error' => 'Vous avez déjà critiqué ce film'], FORBIDDEN);
            }

            $this->criticRepository->create($request->all());
            
            return response()->json(['message' => 'Critic created successfully'], CREATED);

        }catch (\Exception $e)
        {
            return response()->json(['error' => 'Failed to create critic: ' . $e->getMessage()], SERVER_ERROR);
        }
    }
}
