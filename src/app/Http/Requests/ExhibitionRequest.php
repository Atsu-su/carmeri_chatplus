<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
            'name' => 'required|string|max:30',
            'brand' => 'nullable|string|max:30',
            'category_id' => 'required|array',
            'category_id.*' => 'required|integer',
            'price' => 'required|integer|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:3072',
            'condition_id' => 'required',
            'description' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '商品名を入力して下さい',
            'name.max' => '商品名は30文字以内で入力して下さい',
            'brand.max' => 'ブランド名は30文字以内で入力して下さい',
            'category_id.required' => 'カテゴリーを選択して下さい',
            'price.required' => '価格を入力して下さい',
            'price.integer' => '価格は数字で入力して下さい',
            'price.min' => '価格は0円以上として下さい',
            'image.required' => '画像を選択して下さい',
            'image.image' => '画像ファイルを選択して下さい',
            'image.mimes' => 'jpegまたはpng形式の画像ファイルを選択して下さい',
            'image.max' => 'ファイルサイズは3MB以内にして下さい',
            'condition_id.required' => '商品の状態を選択して下さい',
            'description.required' => '商品の説明を入力して下さい',
            'description.max' => '商品の説明は255文字以内で入力して下さい',
        ];
    }
}
