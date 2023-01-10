<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteMaster extends Model
{
    use HasFactory;

    protected $table = 'site_master';

    protected $fillable = [
        'site_name',
        'site_url',
    ];
}
