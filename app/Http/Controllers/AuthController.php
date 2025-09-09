<?php

namespace App\Http\Controllers;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

use App\Models\VerificationCode;
use App\Models\User;

use App\Http\Resources\UserResource;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['logout','get_profile','edit_profile']);
    }
    
    /**
     * @OA\Post(
     * path="/register",
     * tags={"User - Auth"},
     * description="Register by enter name,email,phone.",
     * operationId="Register",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              required={"name","email","password"},
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="email",format="email", type="string"),
     *              @OA\Property(property="phone", type="string"),
     *              @OA\Property(property="password", type="string"),
     *              @OA\Property(property="password_confirmation", type="string"),
     *           )
     *       )
     *   ),
     * @OA\Response(
     *     response=200,
     *     description="successful operation",
     *  ),
     *  )
    */
    public function register(Request $request)
    {
        $request->validate([
            'name'          => ['required', 'string'],
            'email'         => ['required', 'string', 'email', 'unique:users'],
            'phone'         => ['required', 'string', 'min:8', 'unique:users'],
            'password'      => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::create([
            'name'        => $request->name,
            'email'            => $request->email,
            'phone'            => $request->phone,
            'password'         => Hash::make($request->password),
        ]);
        $user->assignRole('user');

        $token = $user->createToken('Sanctum', [])->plainTextToken;
        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ], 200);

        return response()->json(null, 200);
    }

    /**
     * @OA\Post(
     * path="/login",
     * description="Login by password and (email or phone)",
     * operationId="authLogin",
     * tags={"User - Auth"},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              required={"email_or_phone","password"},
     *              @OA\Property(property="email_or_phone" ,type="string"),
     *              @OA\Property(property="password", type="password"),
     *           )
     *       )
     *   ),
     * @OA\Response(
     *     response=200,
     *     description="successful operation",
     *  ),
     *  )
    */
    public function login(Request $request)
    {
        $request->validate( [
            'email_or_phone'    => ['required'],
            'password' => ['required','min:6'],
        ]);

        $user = User::where('email', $request->email_or_phone)->orWhere('phone', $request->email_or_phone)->first();

        if(!$user || !Hash::check($request->password, $user->password))
        {
            return response()->json([
                'message' => 'email or phone or password is incorrect.',
                'errors' => [
                    'email_or_phone' => ['email or password is incorrect.']
                ]
            ], 422);    
        }
            $token = $user->createToken('Sanctum', [])->plainTextToken;
            
            return response()->json([
                'user' => new UserResource($user),
                'token' => $token,
            ], 200);
        }

    /**
     * @OA\Post(
     * path="/dashboard-login",
     * description="Login by password and (email or phone) to dashboard",
     * operationId="authAdminLogin",
     * tags={"Admin - Auth"},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              required={"email_or_phone","password"},
     *              @OA\Property(property="email_or_phone" ,type="string"),
     *              @OA\Property(property="password", type="password"),
     *           )
     *       )
     *   ),
     * @OA\Response(
     *     response=200,
     *     description="successful operation",
     *  ),
     *  )
    */
    public function dashboard_login(Request $request)
    {
        $request->validate( [
            'email_or_phone'    => ['required'],
            'password' => ['required','min:6'],
        ]);

        $user = User::where('email', $request->email_or_phone)->orWhere('phone', $request->email_or_phone)->first();

        if(!$user || !Hash::check($request->password, $user->password) || !$user->hasRole('admin'))
        {
            return response()->json([
                'message' => 'email or phone or password is incorrect.',
                'errors' => [
                    'email_or_phone' => ['email or password is incorrect.']
                ]
            ], 422);    
        }
            $token = $user->createToken('Sanctum', [])->plainTextToken;
            
            return response()->json([
                'user' => new UserResource($user),
                'token' => $token,
            ], 200);
        }

    /**
     * @OA\Get(
     * path="/user",
     * description="Get your profile",
     * operationId="get_profile",
     * tags={"User - Auth"},
     * security={{"bearer_token":{}}},
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *  ),
     *  )
    */
    public function get_profile(Request $request)
    {
        $user = to_user(Auth::user());

        return response()->json(new UserResource($user),200);
    }

    /**
     * @OA\Post(
     * path="/user",
     * description="Edit your profile",
     *  tags={"User - Auth"},
     *  security={{"bearer_token": {} }},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="phone", type="string"),
     *              @OA\Property(property="email", type="steing"),
     *              @OA\Property(property="_method", type="string", format="string", example="PUT"),
     *           )
     *       )
     *   ),
     *     @OA\Response(
     *         response="200",
     *    description="Success"
     *     ),
     * )
    */

    public function edit_profile(Request $request){
        $user = to_user(Auth::user());

        $request->validate([
            'name'                  => ['required', 'string'],
            'phone'                 => ['required', 'min:8', Rule::unique('users', 'phone')->ignore($user->id)],
           'email'               => ['required', 'string', 'email', Rule::unique('users', 'email')->ignore($user->id)],
        ]);
                
        $user->update([
            'name'               => $request->name,
            'email'              => $request->email,
            'phone'              => $request->phone,
        ]);

        return response()->json(new UserResource($user),200);
    }

    /**
     * @OA\Post(
     * path="/logout",
     * description="Logout authorized user",
     * operationId="authLogout",
     * tags={"User - Auth"},
     * security={{"bearer_token":{}}},
     * @OA\Response(
     *    response=200,
     *    description="successful operation"
     *     ),
     * )
    */

    public function logout(Request $request)
    {
        $user = to_user(Auth::user());
        to_token($user->currentAccessToken())->delete();
    }
}
