<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookSeriesMaster extends Model
{
    use HasFactory;

    protected $table = 'book_series_master';

    protected $fillable = [
        'series',
        'author',
    ];
}
