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
        'user_id',
        'is_disabled'
    ];

    public function games()
    {
        return $this->belongsTo('App\Models\Games', 'games_id', 'id');
    }

    /**
     * 有効なレコードのみ取得
     */
    public function scopeActive($query)
    {
        return $query->where('is_disabled', false);
    }


}
