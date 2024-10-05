<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
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
            'email' => 'required|string|email|unique:customers,email, '.$this->id.'|max:255',
            'name' => 'required|string|',
            'customer_catalogue_id' => 'required|integer|gt:0',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'            =>  'Bạn chưa nhập email !',
            'email.email'               =>  'Chưa đúng định dạng email, Ví dụ: abc@gmail.com !',
            'email.unique'              =>  'Email này đã tồn tại trong hệ thống !',
            'email.max'                 =>  'Email của bạn quá 255 kí tự !',
            'name.required'             =>  'Bạn chưa nhập họ tên !',
            'customer_catalogue_id.gt'  =>  'Chưa chọn nhóm khách hàng!'
        ];
    }
}
