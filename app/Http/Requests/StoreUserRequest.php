<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'email' => 'required|string|email|unique:users|max:255',
            'name' => 'required|string|',
            'password' => 'required|string|min:6',
            're_password' => 'required|string|same:password',
            'image' => 'required',
            'user_catalogue_id' => 'required|integer|gt:0',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'        =>  'Bạn chưa nhập email !',
            'email.email'           =>  'Chưa đúng định dạng email, Ví dụ: abc@gmail.com !',
            'email.unique'          =>  'Email này đã tồn tại trong hệ thống !',
            'email.max'             =>  'Email của bạn quá 255 kí tự !',
            'name.required'         =>  'Bạn chưa nhập họ tên !',
            'password.required'     =>  'Bạn chưa nhập mật khẩu !',
            'password.min'          =>  'Mật khẩu tối thiểu 6 kí tự ! ',
            're_password.require'   =>  'Bạn chưa nhập nhập lại mật khẩu !',
            're_password.same'      =>  'Mật khẩu nhập lại không trùng khớp !',
            'image.required'        =>  'Bạn chưa thêm ảnh !',
            'user_catalogue_id.gt'  =>  'Chưa chọn nhóm thành viên!'
        ];
    }
}
