<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(title="API PetCare", version="1.0")
 *
 *  @OA\Server(
 *      url="https://petcare-production-cc6b.up.railway.app/api",
 *  )
 *  @OA\Server(
 *      url="http://127.0.0.1:8000/api",
 *  )
 * @OA\Server(
 *      url="https://petcare-production-3202.up.railway.app/api",
 *  )
 *
 * @OAS\SecurityScheme(
 *      securityScheme="bearer_token",
 *      type="http",
 *      scheme="bearer"
 * )
*/

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
