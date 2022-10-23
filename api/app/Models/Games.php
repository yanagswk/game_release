<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Games extends Model
{
    use HasFactory;

    protected $table = 'games';

    protected $fillable = [
        'title',
        'hardware',
        'price',
        'sales_date',
        'large_image_url',
        'item_url',
        'label',
        'item_caption',
        'review_count',
        'review_average',
    ];

    // 取得しない項目
    protected $hidden = [
        'created_at',
        'updated_at',
    ];



}
