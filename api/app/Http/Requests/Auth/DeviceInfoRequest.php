<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class DeviceInfoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'device_id' => 'required'
        ];
    }

    /**
     * バリデーションのエラーレスポンス
     *
     * これをオーバーライドしないと、
     * ステータスコード200のHTMLのレスポンスが返却されてしまう
     * (参考) https://www.tairaengineer-note.com/laravel-api-validation-to-request/
     *
     * @param Validator $validator
     * @return void
     */
    protected function failedValidation(Validator $validator)
    {
        $res = response()->json([
            'errors' => $validator->errors(),
            ],
            422);
        throw new HttpResponseException($res);
    }
}
