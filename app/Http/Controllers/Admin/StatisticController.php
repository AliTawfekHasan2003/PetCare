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
        $website_animals = Animal::where('status', 'accepted')
        ->whereDoesntHave('adoption_requests', function($q){
            $q->where('status', 'accepted'); // استبعاد الحيوانات التي لديها أي طلب مقبول
        })
        ->get();
    
        $pending_animals = Animal::where('status', 'pending')->count();
        $rejected_animals = Animal::where('status', 'rejected')->count();
        $accepted_animals = Animal::where('status', 'accepted')->count();

        $all_adoption_requests = AdoptionRequest::all()->count();
        $pending_adoption_requests = AdoptionRequest::where('status', 'pending')->count();
        $accepted_adoption_requests = AdoptionRequest::where('status', 'accepted')->count();
        $rejected_adoption_requests = AdoptionRequest::where('status', 'rejected')->count();

        return response()->json([
                'all_adoption_requests' => $all_adoption_requests,
                'pending_adoption_requests' => $pending_adoption_requests,
                'accepted_adoption_requests' => $accepted_adoption_requests,
                'rejected_adoption_requests' => $rejected_adoption_requests,
                'website_animals' => $website_animals,
                'all_pending_animals' => $pending_animals,
                'all_rejected_animals' => $rejected_animals,
                'all_accepted_animals' => $accepted_animals,
          ], 200);
    }
}
