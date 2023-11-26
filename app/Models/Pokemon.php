<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pokemon extends Model
{
    /**
     * @OA\Schema(
     *     schema="Pokemon",
     *     @OA\Property(property="id", type="integer"),
     *     @OA\Property(property="name", type="string"),
     *     @OA\Property(property="type", type="string"),
     *     @OA\Property(property="color", type="string"),
     *     @OA\Property(property="image_url", type="string", format="url"),
     *     @OA\Property(property="level", type="integer", nullable=true),
     *     @OA\Property(property="description", type="string", nullable=true),
     *     @OA\Property(property="created_at", type="string", format="date-time"),
     *     @OA\Property(property="updated_at", type="string", format="date-time"),
     * )
     */


    use HasFactory;

    protected $table = 'pokemons';

    protected $fillable = [
        'name',
        'type',
        'color',
        'image_url',
        'level',
        'description',
        'user_id'
    ];
}
