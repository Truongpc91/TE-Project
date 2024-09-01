<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserCatalogueRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'         =>  'Bạn chưa nhập Tên Nhóm Thành viên !',
            'name.string'           =>  'Tên Nhóm thành viên không được quá 255 kí tự !'
        ];
    }
}
