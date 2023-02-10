<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BooksItem extends Model
{
    use HasFactory;

    protected $table = 'books_item';

    // sizeの種類
    // コミック
    // 文庫
    // 単行本
    // 図鑑
    // 絵本


    protected $fillable = [
        'title',
        'size',
        'price',
        'sales_date',
        'large_image_url',
        'item_url',
        'affiliate_url',
        'author',
        'publisherName',
        'item_caption',
        'series_name',
        'contents',
        'review_count',
        'review_average',
    ];

    // 取得しない項目
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
