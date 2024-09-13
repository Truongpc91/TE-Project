<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLanguageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|',
            // 'canonical' => 'required|unique:post_language,canonical,'.$this->id,
            'image' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'         =>  'Bạn chưa nhập tên ngôn ngữ !',
            // 'canonical.required'    =>  'Bạn chưa nhập canonical !',
            'image.required'        =>  'Bạn chưa thêm ảnh cho ngôn ngữ !',
        ];
    }
}
