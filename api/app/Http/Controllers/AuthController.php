<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\RegisterDeviceInfoRequest;
use App\Models\UserInfo;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * デバイスIDが登録されていなければ、登録
     *
     * @param RegisterDeviceInfoRequest $request
     * @return void
     */
    public function registerDeviceInfo(RegisterDeviceInfoRequest $request)
    {
        $device_id = $request->input('device_id');
        $message = null;

        $user_info = UserInfo::where('device_id', $device_id);

        // デバイスIDが登録されていなければ、登録
        if (!$user_info->exists()) {
            UserInfo::create([
                'device_id' => $device_id
            ]);
            $message = 'add device_id';
        }
        return response()->json([
            'message'   => $message ?? 'device_id is already registered',
        ], 200);
    }
}
