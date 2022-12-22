<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameImage extends Model
{
    use HasFactory;

    protected $table = 'game_image';

    protected $fillable = [
        'game_id',
        'image_type',
        'img_url',
    ];

    // メイン画像
    public const MAIN_IMG = 1;
    // サブ画像
    public const SUB_IMG = 2;
}
