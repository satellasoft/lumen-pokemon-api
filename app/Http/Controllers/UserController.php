<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * @OA\Tag(
 *     name="users",
 *     description="Manager users"
 * )
 */

 /**
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 * )
 */

class UserController extends Controller
{
    /**
     * @OA\POST(
     *     path="/api/user",
     *     tags={"users"},
     *     summary="Create new user and return your bearer auth",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *         ),
     *     ),
     *     @OA\Response(response="201", description="Ok. Return Bearer unique token"),
     *     @OA\Response(response="422", description="Email already exists"),
     *     @OA\Response(response="500", description="Error during create new user")
     * )
     */
    public function create(Request $request)
    {
        $token = $request->bearerToken();

        if ($token !== env('APP_PROTECTED_BEARER'))
            return response()->json(['Token not found'], 422);

        $this->validateForm($request);

        $user = new User();

        $user->name = $request->name;
        $user->email = $request->email;
        $user->api_token = Str::random(45);

        if (!$user->save())
            return response()->json('Error during create new user', 500);

        return response()->json(sprintf('User created! Token: %s', $user->api_token), 201);
    }

    private function validateForm(Request $request)
    {
        $rules = [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
        ];

        $messages = [
            'name.required'  => 'Name is required',
            'name.string'    =>  'Name must be string',
            'email.required' => 'Email is required',
            'email.email'    => 'Email it not valid. Please provide a valid email',
            'email.unique'   => 'Email already using'
        ];

        return $this->validate($request, $rules, $messages);
    }
}
