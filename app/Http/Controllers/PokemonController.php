<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="pokemons",
 *     description="Manager Pokemons"
 * )
 */

/**
 * @OA\SecurityScheme(
 *     securityScheme="bearerPokemonAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 * )
 */

class PokemonController extends Controller
{

    private string $token;
    private User $user;

    public function __construct(Request $request)
    {
        $this->token = $request->bearerToken();
        $this->user = User::where('api_token', $this->token)->first();
    }

    /**
     * @OA\GET(
     *     path="/api/pokemons",
     *     tags={"pokemons"},
     *     summary="Get all pokemons",
     *     security={{"bearerPokemonAuth": {}}},
     *     @OA\Response(
     *         response="404",
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Ok",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Pokemon")
     *         )
     *     )
     * )
     */
    public function index()
    {
        if (!$this->user)
            return response()->json(['message' => 'User not found'], 404);

        return response()->json($this->user->pokemons);
    }

    /**
     * @OA\GET(
     *     path="/api/pokemons/{id}",
     *     tags={"pokemons"},
     *     summary="Get a specific pokemon by ID",
     *     security={{"bearerPokemonAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the pokemon to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="404", description="User not found or Pokemon not found"),
     *     @OA\Response(response="200", 
     *         description="OK", 
     *         @OA\JsonContent(
     *             type="object",
     *             ref="#/components/schemas/Pokemon"
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        if (!$this->user)
            return response()->json(['message' => 'User not found'], 404);

        return response()->json($this->user->pokemons()->find($id), 200);
    }
    /**
     * @OA\Post(
     *     path="/api/pokemons",
     *     tags={"pokemons"},
     *     summary="Create a new Pokemon",
     *     security={{"bearerPokemonAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="type", type="string"),
     *             @OA\Property(property="color", type="string"),
     *             @OA\Property(property="image_url", type="string", format="url"),
     *             @OA\Property(property="level", type="integer", nullable=true),
     *             @OA\Property(property="description", type="string", nullable=true),
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Pokemon created",
     *         @OA\JsonContent(ref="#/components/schemas/Pokemon")
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        if (!$this->user)
            return response()->json(['message' => 'User not found'], 404);

        $this->validate($request, [
            'name' => 'required|string',
            'type' => 'required|string',
            'color' => 'required|string',
            'image_url' => 'required|url',
            'level' => 'integer|nullable',
            'description' => 'string|nullable'
        ]);

        $pokemon = new Pokemon($request->all());
        $pokemon->user_id = $this->user->id;
        $pokemon->save();

        return response()->json($pokemon, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/pokemons/{id}",
     *     tags={"pokemons"},
     *     summary="Update a Pokemon",
     *     security={{"bearerPokemonAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Pokemon to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="type", type="string"),
     *             @OA\Property(property="color", type="string"),
     *             @OA\Property(property="image_url", type="string", format="url"),
     *             @OA\Property(property="level", type="integer", nullable=true),
     *             @OA\Property(property="description", type="string", nullable=true),
     *         ),
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Pokemon not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Pokemon updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     * )
     */
    public function update(Request $request, $id)
    {
        if (!$this->user)
            return response()->json(['message' => 'User not found'], 404);

        $pokemon = $this->user->pokemons()->find($id);

        if (!$pokemon)
            return response()->json(['message' => 'Pokémon not found'], 404);

        $this->validate($request, [
            'name' => 'required|string',
            'type' => 'required|string',
            'color' => 'required|string',
            'image_url' => 'required|url',
            'level' => 'integer|nullable',
            'description' => 'string|nullable',
        ]);

        $pokemon->update($request->all());

        return response()->json(['message' => 'Pokémon updated']);
    }

    /**
     * @OA\Delete(
     *     path="/api/pokemons/{id}",
     *     tags={"pokemons"},
     *     summary="Delete a Pokemon",
     *     security={{"bearerPokemonAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Pokemon to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Pokemon not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Pokemon deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     * )
     */
    public function destroy($id)
    {
        if (!$this->user)
            return response()->json(['message' => 'User not found'], 404);

        $pokemon = $this->user->pokemons()->find($id);

        if (!$pokemon)
            return response()->json(['message' => 'Pokemon not found'], 404);

        $pokemon->delete();

        return response()->json(['message' => 'Pokemon deleted']);
    }
}
