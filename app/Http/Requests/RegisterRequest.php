<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'fullname' => 'required|min:4',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|regex:/^0[0-9]{9,10}$/|unique:users,phone_number',
            'province_id' => 'required',
            'district_id' => 'required',
            'ward_id' => 'required',
            'address' => 'required',
            'password' => 'required|confirmed|min:6',
        ];
    }
    public function messages()
    {
        return [
            'required' => ':attribute không được để trống',
            'min' => ':attribute phải có ít nhất :min ký tự',
            'email' => ':attribute phải là một địa chỉ email hợp lệ',
            'unique' => ':attribute đã tồn tại trong hệ thống',
            'regex' => ':attribute phải hợp lệ',
            'confirmed' => ':attribute xác nhận không khớp',
        ];
    }
    public function attributes()
    {
        return [
            'fullname' => 'Tên người dùng',
            'email' => 'Email',
            'phone_number' => 'Số điện thoại',
            'province_id' => 'Tỉnh/Thành phố',
            'district_id' => 'Quận/Huyện',
            'ward_id' => 'Phường/Xã',
            'address' => 'Địa chỉ',
            'password' => 'Mật khẩu',
        ];
    }
}
