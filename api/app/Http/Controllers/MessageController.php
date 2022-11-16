<?php

namespace App\Http\Controllers;

use App\Http\Requests\Message\ContactFormRequest;
use App\Models\Contacts;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('deviceCheck');
    }


    /**
     * お問い合せ内容保存
     *
     * @param Request $request
     * @return void
     */
    public function sendContactForm(ContactFormRequest $request)
    {
        $message = $request->input('message');
        $user_id = $request->input('user_id');

        Contacts::create([
            'user_id' => $user_id,
            'message' => $message
        ]);

        return response()->json([
            'message'   => 'success',
        ], 200);
    }
}
