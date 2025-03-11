<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RatingRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'rating' => 'required|integer|between:1,5',
        ];
    }

    public function messages()
    {
        return [
            'rating.required' => '値を入力してください',
            'rating.integer' => '値が不正です',
            'rating.between' => '1から5の値を入力してください',
        ];
    }
}
