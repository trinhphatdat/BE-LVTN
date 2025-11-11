<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
        $rules = [
            'role_id' => 'required',
            'status' => 'required',
            'fullname' => 'required|min:4',
            'email' => 'required|email|unique:users,email,' . $this->route('user'),
            'phone_number' => 'required|regex:/^0[0-9]{9,10}$/|unique:users,phone_number,' . $this->route('user'),
        ];
        if ($this->boolean('change_password')) {
            $rules['password'] = 'required|confirmed|min:6';
        }
        return $rules;
    }
    public function messages(): array
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
            'role_id' => 'Vai trò',
            'status' => 'Tình trạng',
            'fullname' => 'Tên người dùng',
            'email' => 'Email',
            'phone_number' => 'Số điện thoại',
            'password' => 'Mật khẩu',
        ];
    }
}
