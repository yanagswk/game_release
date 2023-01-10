<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameArticle extends Model
{
    use HasFactory;

    protected $table = 'game_article';

    protected $fillable = [
        'site_id',
        'site_url',
        'title',
        'genre',
        'top_image_url',
        'post_date',
    ];

    public function site_master()
    {
        return $this->hasOne('App\Models\SiteMaster', 'id', 'site_id');
    }
}
