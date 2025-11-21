<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function update(ProfileUpdateRequest $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            // Cập nhật thông tin cơ bản
            $user->fullname = $request->fullname;
            $user->email = $request->email;
            $user->phone_number = $request->phone_number;
            $user->address = $request->address;

            // Nếu có đổi mật khẩu thì mã hóa và lưu lại
            if ($request->boolean('change_password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            return response()->json(['message' => 'Cập nhật user thành công'], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Cập nhật user không thành công',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
