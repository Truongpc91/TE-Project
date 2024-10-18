<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCartRequest extends FormRequest
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
            'fullName'   => 'required',
            'phone'     => 'required',
            'email'     => 'required|email',
            'address'   => 'required',

        ];
    }

    public function messages(): array
    {
        return [
            'fullName.required'  => 'Bạn chưa nhập vào họ tên !',
            'phone.required'    => 'Bạn chưa nhập vào số điện thoại',
            'email.required'    => 'Bạn chưa nhập vào email',
            'email.email'       => 'Email không đúng định dạng ! Vui lòng thử lại',
            'address.required'  => 'Bạn chưa nhập vào địa chỉ',
        ];
    }
}
