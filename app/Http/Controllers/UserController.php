<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UserRequest;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
// use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        // $users = User::with('role', '!=', 1)->get();
        $users = User::all();
        return response()->json($users, 200);
    }

    public function store(CreateUserRequest $request)
    {
        try {
            $userData = $request->except(['password', 'password_confirmation']);
            $userData['password'] = Hash::make($request['password']);
            $user = User::create($userData);

            //Nếu tài khoản là khách hàng
            if ($user->role_id == 3) {
                // Tạo giỏ hàng cho người dùng mới
                Cart::create([
                    'user_id' => $user->id,
                    'status_id' => 1,
                ]);
            }
            return response()->json(['message' => 'Thêm mới user thành công'], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Thêm mới user không thành công',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json($user);
    }

    public function update(UpdateUserRequest $request, string $id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['message' => 'User không tồn tại'], 404);
            }

            // Cập nhật thông tin cơ bản
            $user->role_id = $request->role_id;
            $user->status_id = $request->status_id;
            $user->fullname = $request->fullname;
            $user->email = $request->email;
            $user->phone_number = $request->phone_number;

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

    public function destroy(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Lấy cart của user
        $cart = Cart::where('user_id', $id)->first();
        //Xoá items trong cart trước
        CartItem::where('cart_id', $cart->id)->delete();
        $cart->delete();
        $user->delete();

        return response()->json(['message' => 'Xoá user thành công'], 200);
    }
}
