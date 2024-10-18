<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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

    public function prepareForValidation()
    {
        $this->merge([
            'is_changed' => $this->boolean('is_changed'),]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'is_changed' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'name' => 'required|string|max:30',
            'postal_code' => 'required|regex:/^\d{3}-\d{4}$/',
            'address' => 'required|string|max:50',
            'building_name' => 'nullable|string|max:50',
        ];
    }

    public function messages()
    {
        return [
            'image.image' => '画像ファイルを選択してください',
            'image.mimes' => 'jpegまたはpng形式の画像ファイルを選択してください',
            'image.max' => 'ファイルサイズは2MB以内にしてください',
            'name.required' => '名前を入力してください',
            'postal_code.required' => '郵便番号を入力してください',
            'postal_code.regex' => '郵便番号はXXX-XXXX（半角ハイフンあり）の形式で入力してください',
            'address.required' => '住所を入力してください',
            'address.max' => '住所は50文字以内で入力してください',
            'building_name.max' => '建物名は50文字以内で入力してください',
        ];
    }
}
