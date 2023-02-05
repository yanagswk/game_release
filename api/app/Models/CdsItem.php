<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CdsItem extends Model
{
    use HasFactory;

    protected $table = 'cds_item';

    protected $fillable = [
        'title',
        'artist_name',
        'artist_name_kana',
        'label',
        'play_list',
        'size',
        'price',
        'sales_date',
        'large_image_url',
        'item_url',
        'affiliate_url',
        'item_caption',
        'review_count',
        'review_average',
        'disabled',
    ];

    // 取得しない項目
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
