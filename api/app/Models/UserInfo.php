<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    use HasFactory;

    protected $table = 'user_info';

    protected $fillable = [
        'device_id',
        'nickname'
    ];

    /**
     * ユーザーid取得
     *
     * @param [type] $query
     * @param [type] $device_id
     * @return void
     */
    public function scopeUserId($query, string $device_id)
    {
        return $query->where('device_id', $device_id)
            ->pluck('id')
            ->first();
    }
}
