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

    // 発売前
    public const BEFORE_RELEASE = 1;
    // 発売後
    public const AFTER_RELEASE = 2;


    protected $fillable = [
        'title',
        'page_url',
        'genre',
        'genre_detail',
        'label',
        'author',
        'item_url',
        'series',
        'page',
        'size',
        'description',
        'size',
        'release_date',
        'publisher',
        'isbn',
        'rakuten_affiliate_url',
        'amazon_affiliate_url',
        'disabled',
    ];

    // 取得しない項目
    protected $hidden = [
        'created_at',
        'updated_at',
    ];


    /**
     * スコープ 抽出制限
     *
     * @param [type] $limit
     * @param [type] $offset
     * @return void
     */
    public function scopePage($query, int $limit, int $offset)
    {
        return $query->limit($limit)->offset($offset);
    }

}
