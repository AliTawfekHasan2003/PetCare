<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnimalRequest;
use App\Http\Requests\GetRequest;
use App\Http\Resources\AnimalResource;
use App\Models\Animal;
use App\Models\Attachment;
use Illuminate\Http\Request;

use function Laravel\Prompts\form;

class AnimalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only('store');
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
        $q = Animal::query()->where('status', 'accepted')->with(['category', 'breed', 'attachments']);

        if($request->category_id){
            $q->where('category_id', $request->category_id);
        }

        if($request->breed_id){
            $q->where('breed_id', $request->breed_id);
        }
        if($request->gender){
            $q->where('gender', $request->gender);
        }

        if($request->size){
            $q->where('size', $request->size);
        }

        if($request->health_status){
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
        $animal->load(['category', 'breed', 'attachments']);

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
            'secondary_color' => $request->secondary_color ,
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

        if($request->attachments){

            foreach($request->attachments as $attachment)
            {
                $link = upload_file($attachment['file'], 'animal_attachment' ,'attachments');
                Attachment::create([
                'animal_id' => $animal->id,
                 'title' => $attachment['title'],
                 'file' => $link,
                ]);       
            }
        }

        return new AnimalResource($animal->load(['category', 'attachments', 'breed']));
    }
}
