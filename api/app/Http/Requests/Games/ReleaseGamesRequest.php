<?php

namespace App\Http\Requests\Games;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ReleaseGamesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // return false;
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
            'hardware'  => 'nullable|in:All,PS4,PS5,Switch',
            'limit'     => 'numeric|nullable',
            'offset'    => 'numeric|nullable',
            'is_released'   => 'required|boolean'
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
