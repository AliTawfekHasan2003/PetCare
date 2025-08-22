<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetRequest;
use App\Models\AdoptionRequest;
use App\Models\Animal;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:admin');
    }

    /**
     * @OA\Get(
     *   path="/admin/statistics",
     *   description="Get statistics",
     *   tags={"Admin - Statistics"},
     *   security={{"bearer_token": {} }},
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *   ),
     * )
     */

    public function index(GetRequest $request)
    {

        $all_animals = Animal::all()->count();
        $animals_now = Animal::where('status', 'accepted')
        ->whereHas('adoption_requests', function($query){
            return $query->where('status', '!=', 'accepted');
        })->count();

        $all_adoption_requests = AdoptionRequest::all()->count();

        return response()->json([
                'all_animals_for_adoption' => $all_animals,
                'animals_for_adoption_now' => $animals_now,
                'all_adoption_requests' => $all_adoption_requests,
          ], 200);
    }
}
