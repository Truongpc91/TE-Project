<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePermissionRequest extends FormRequest
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
            'canonical' => 'required|unique:permissions,canonical,'.$this->id.'',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'         =>  'Bạn chưa nhập tên ngôn ngữ !',
            'canonical.required'    =>  'Bạn chưa nhập canonical !',
            'canonical.unique'      =>  'Đường dẫn của bạn đã tồn tại vui lòng chọn đường dẫn khác !',
        ];
    }
}
