<?php

namespace App\Http\Middleware;

use App\Models\UserInfo;
use Closure;
use Illuminate\Http\Request;

/**
 * デバイスチェック ミドルウェア
 */
class DeviceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $device_id = $request->input('device_id');

        // デバイスIDが存在するか
        $user_id = UserInfo::where('device_id', $device_id)
            ->pluck('id')
            ->first();
        if (!$user_id) {
            return response()->json([
                'message'   => 'user not found'
            ], 400);
        }
        $request->merge([
            // ユーザーidをコントローラーで使えるように
            'user_id' => $user_id
        ]);
        return $next($request);
    }
}
