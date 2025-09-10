<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdoptionRequest;
use App\Http\Requests\AnimalRequest;
use App\Http\Requests\GetRequest;
use App\Http\Resources\AdoptionRequestResource;
use App\Http\Resources\AnimalResource;
use App\Models\AdoptionRequest as ModelsAdoptionRequest;
use App\Models\Animal;
use App\Models\Attachment;
use Illuminate\Http\Request;

use function Laravel\Prompts\form;

class AnimalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only('store', 'store_adoption_request');
    }

    /**
     * @OA\Get(
     *   path="/animals",
     *   description="Get all animals",
     *   operationId="get_ll_animals",
     *   tags={"User - Animals"},
     *   @OA\Parameter(
     *     in="query",
     *     name="category_id",
     *     required=false,
     *     @OA\Schema(type="integer"),
     *   ),
     *   @OA\Parameter(
     *     in="query",
     *     name="breed_id",
     *     required=false,
     *     @OA\Schema(type="integer"),
     *   ),
     *  @OA\Parameter(
     *     in="query",
     *     name="gender",
     *     required=false,
     *     @OA\Schema(type="string",enum={"male", "female"})
     *   ),
     *  @OA\Parameter(
     *     in="query",
     *     name="size",
     *     required=false,
     *     @OA\Schema(type="string",enum={"small", "medium", "large"})
     *   ),
     *  @OA\Parameter(
     *     in="query",
     *     name="health_status",
     *     required=false,
     *     @OA\Schema(type="string",enum={"healthy" ,"injured" ,"sick"})
     *   ),
     *   @OA\Parameter(
     *     in="query",
     *     name="per_page",
     *     required=false,
     *     @OA\Schema(type="integer"),
     *   ),
     *   @OA\Parameter(
     *     in="query",
     *     name="with_paginate",
     *     required=false,
     *     @OA\Schema(type="integer",enum={0, 1})
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *   ),
     * )
     */

    public function index(GetRequest $request)
    {
        $q = Animal::query()
        ->where('status', 'accepted')
        ->whereDoesntHave('adoption_requests', function($q){
            $q->where('status', 'accepted');
        }) ->with(['category', 'breed', 'attachments', 'user'])
        ->latest();
    
        if ($request->category_id) {
            $q->where('category_id', $request->category_id);
        }

        if ($request->breed_id) {
            $q->where('breed_id', $request->breed_id);
        }
        if ($request->gender) {
            $q->where('gender', $request->gender);
        }

        if ($request->size) {
            $q->where('size', $request->size);
        }

        if ($request->health_status) {
            $q->where('health_status', $request->health_status);
        }

        if ($request->with_paginate === '0')
            $animal = $q->get();
        else
            $animal = $q->paginate($request->per_page ?? 10);

        return AnimalResource::collection($animal);
    }

    /**
     * @OA\Get(
     *   path="/animals/{id}",
     *   description="Get specific animal",
     *   @OA\Parameter(
     *     in="path",
     *     name="id",
     *     required=true,
     *     @OA\Schema(type="string"),
     *   ),
     *   operationId="show_animal",
     *   tags={"User - Animals"},
     *   @OA\Response(
     *     response=200,
     *     description="Success"
     *   ),
     * )
     */
    public function show(Animal $animal)
    {
        if($animal->status != 'accepted')  
            return response()->json(['this animal is not accepted'], 400);

        $animal->load(['category', 'breed', 'attachments', 'user']);

        return response()->json(new AnimalResource($animal));
    }

    /**
     * @OA\Post(
     * path="/animals",
     * tags={"User - Animals"},
     * security={{"bearer_token": {} }},
     * description="Create new animal.",
     * operationId="create_animal",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              required={"name","category_id","breed_id","primary_color", "address", "gender", "size", "profile_image", "cover_image", "health_status", "birth_date"},
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="category_id", type="integer"),
     *              @OA\Property(property="breed_id", type="integer"),
     *              @OA\Property(property="primary_color", type="string"),
     *              @OA\Property(property="secondary_color", type="string"),
     *              @OA\Property(property="address", type="string"),
     *              @OA\Property(property="weight", type="float"),
     *              @OA\Property(property="gender", type="string", enum={"male", "female"}),
     *              @OA\Property(property="size", type="string", enum={"small", "medium", "large"}),
     *              @OA\Property(property="desc", type="string"),
     *              @OA\Property(property="profile_image", type="file"),
     *              @OA\Property(property="cover_image", type="file"),
     *              @OA\Property(property="birth_date", type="date"),
     *              @OA\Property(property="health_status", type="string", enum={"healthy" ,"injured" ,"sick", "unknown"}),
     *              @OA\Property(property="attachments[0][title]", type="string"),
     *              @OA\Property(property="attachments[0][file]", type="file"),
     *           )
     *       )
     *   ),
     * @OA\Response(
     *     response=200,
     *     description="successful operation",
     *  ),
     *  )
     */

    public function store(AnimalRequest $request)
    {
        $profile_image = upload_file($request->profile_image, 'profile_image', 'animals');
        $cover_image = upload_file($request->profile_image, 'cover_image', 'animals');


        $animal = Animal::create([
            'user_id' => auth()->id(),
            'category_id' => $request->category_id,
            'breed_id' => $request->breed_id,
            'status' => 'pending',
            'primary_color' => $request->primary_color,
            'secondary_color' => $request->secondary_color,
            'profile_image' =>  $profile_image,
            'cover_image' =>  $cover_image,
            'weight' =>  $request->weight,
            'address' =>  $request->address,
            'gender' =>  $request->gender,
            'size' =>  $request->size,
            'desc' =>  $request->desc,
            'health_status' =>  $request->health_status,
            'name' => $request->name,
            'birth_date' =>  $request->birth_date,
        ]);

        if ($request->attachments) {

            foreach ($request->attachments as $attachment) {
                $link = upload_file($attachment['file'], 'animal_attachment', 'attachments');
                Attachment::create([
                    'animal_id' => $animal->id,
                    'title' => $attachment['title'],
                    'file' => $link,
                ]);
            }
        }

        return new AnimalResource($animal->load(['category', 'attachments', 'breed']));
    }

    /**
     * @OA\post(
     *   path="/animals/{id}/adoption-request",
     *   description="sent adoption request for specific animal",
     *   operationId="adoption-request",
     *   tags={"User - Animals"},
     *   security={{"bearer_token": {} }},
     *   @OA\Parameter(
     *     in="path",
     *     name="id",
     *     required=true,
     *     @OA\Schema(type="string"),
     *   ),
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              @OA\Property(
     *                     property="address",
     *                     type="string",
     *                 ),

     *                 @OA\Property(
     *                     property="family_members_count",
     *                     type="integer",
     *                     example=4,
     *                     description="Number of family members"
     *                 ),
     *                 @OA\Property(
     *                     property="has_children",
     *                     type="integer",enum={0, 1},                 
     *                     description="Whether there are children in the house"
     *                 ),
     *                 @OA\Property(
     *                     property="children_ages[0]",
     *                     type="integer",
     *                     example=10,
     *                     description="Ages of children, if any"
     *                 ),
     *                 @OA\Property(
     *                     property="job_title",
     *                     type="string",
     *                     example="Software Engineer",
     *                     description="Job title"
     *                 ),
     *                 @OA\Property(
     *                     property="company_name",
     *                     type="string",
     *                     example="Tech Corp",
     *                     description="Name of the company or organization"
     *                 ),
     *                 @OA\Property(
     *                     property="work_hours_per_day",
     *                     type="integer",
     *                     example=8,
     *                     description="Number of work hours per day"
     *                 ),
     *                 @OA\Property(
     *                     property="work_type",
     *                     type="string",
     *                     enum={"remote", "on_site", "hybrid"},
     *                     example="remote",
     *                     description="Work type"
     *                 ),
     *                 @OA\Property(
     *                     property="housing_type",
     *                     type="string",
     *                     enum={"apartment", "house", "villa"},
     *                     example="house",
     *                     description="Type of housing"
     *                 ),
     *                 @OA\Property(
     *                     property="is_rented",
     *                     type="integer",enum={0, 1},                    
     *                     description="Is the housing rented?"
     *                 ),
     *                 @OA\Property(
     *                     property="landlord_name",
     *                     type="string",
     *                     example="John Doe",
     *                     description="Landlord name (if rented)"
     *                 ),
     *                 @OA\Property(
     *                     property="landlord_phone",
     *                     type="string",
     *                     example="+1234567890",
     *                     description="Landlord phone (if rented)"
     *                 ),
     *                 @OA\Property(
     *                     property="landlord_allows_pets",
     *                     type="integer",enum={0, 1},                      
     *                     description="Does landlord allow pets?"
     *                 ),
     *                 @OA\Property(
     *                     property="has_garden",
     *                     type="integer",enum={0, 1},                     
     *                     description="Is there a garden at home?"
     *                 ),
     *                 @OA\Property(
     *                     property="has_patience",
     *                     type="integer",enum={0, 1},                     
     *                     description="Do you have patience for the animal?"
     *                 ),
     *                 @OA\Property(
     *                     property="can_handle_issues",
     *                     type="integer",enum={0, 1},                     
     *                     description="Are you ready to handle health/behavior issues?"
     *                 ),
     *                 @OA\Property(
     *                     property="hours_with_pet_daily",
     *                     type="integer",
     *                     example=4,
     *                     description="Hours spent daily with the animal"
     *                 ),
     *                 @OA\Property(
     *                     property="someone_home_24_7",
     *                     type="integer",enum={0, 1},                 
     *                     description="Is someone present at home 24/7?"
     *                 ),
     *                 @OA\Property(
     *                     property="can_be_with_pet_when_sick",
     *                     type="integer",enum={0, 1},                 
     *                     description="Can you stay with the animal if it gets sick?"
     *                 ),
     *                 @OA\Property(
     *                     property="agreed_to_terms",
     *                     type="integer",enum={0, 1},                     
     *                     description="Has the user agreed to the terms?"
     *                 ),
     *                 @OA\Property(
     *                     property="notes",
     *                     type="string",
     *                     example="I have experience with pets and a big garden.",
     *                     description="Additional notes"
     *                 ),       
     *         )
     *      )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *   ),
     * )
     */

    public function store_adoption_request(Animal $animal, AdoptionRequest $request)
    {

        $adoptionRequest = ModelsAdoptionRequest::create([
            'animal_id' => $animal->id,
            'user_id' => auth()->id(),
            'status' => 'pending',
            
    
            'family_members_count' => $request->family_members_count,
            'address' =>  $request->address,
            'has_children' => $request->has_children,
            'children_ages' => json_encode($request->children_ages ?? []),
        
            'job_title' => $request->job_title,
            'company_name' => $request->company_name, 
            'work_hours_per_day' => $request->work_hours_per_day,
            'work_type' => $request->work_type, 
        
            'housing_type' => $request->housing_type, 
            'is_rented' => $request->is_rented,
            'landlord_name' => $request->landlord_name, 
            'landlord_phone' => $request->landlord_phone,
            'landlord_allows_pets' => $request->landlord_allows_pets, 
            'has_garden' => $request->has_garden,
        
            'has_patience' => $request->has_patience,
            'can_handle_issues' => $request->can_handle_issues,
            'hours_with_pet_daily' => $request->hours_with_pet_daily, 
            'someone_home_24_7' => $request->someone_home_24_7, 
            'can_be_with_pet_when_sick' => $request->can_be_with_pet_when_sick, 
        
            'agreed_to_terms' => $request->agreed_to_terms, 
            'notes' => $request->notes,
        ]);
    
        return response()->json(new AdoptionRequestResource($adoptionRequest->load(['user', 'animal', 'animal.category', 'animal.breed', 'animal.attachments'])));
    }
}
