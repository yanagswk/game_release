<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavoriteGames extends Model
{
    use HasFactory;

    protected $table = 'favorite_games';

    protected $fillable = [
        'games_id',
        'user_id'
    ];
}
