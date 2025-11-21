<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
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
            'fullname' => 'required|min:4',
            'email' => 'required|email|unique:users,email,' . $this->user()->id,
            'phone_number' => 'nullable|regex:/^0[0-9]{9,10}$/|unique:users,phone_number,' . $this->user()->id,
        ];
        if ($this->isMethod('post')) {
            $rules['password'] = 'required|confirmed|min:6';
        }
        //khi update thì không bắt buộc nhập mật khẩu
        else if ($this->isMethod('put') || $this->isMethod('patch')) {
            //Nếu trường password có giá trị => đã click chọn đổi mật khẩu
            if ($this->boolean('change_password')) {
                $rules['current_password'] = ['required', 'current_password:api'];
                $rules['password'] = 'required|confirmed|min:6';
            }
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
            'current_password' => ':attribute không đúng',
        ];
    }
    public function attributes()
    {
        return [
            'fullname' => 'Tên người dùng',
            'email' => 'Email',
            'phone_number' => 'Số điện thoại',
            'password' => 'Mật khẩu',
            'current_password' => 'Mật khẩu hiện tại',
            'password_confirmation' => 'Xác nhận mật khẩu',
        ];
    }
}
