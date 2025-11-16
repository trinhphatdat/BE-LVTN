<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use App\Notifications\ResetPasswordNotification;


class AuthController extends Controller
{

    public function __construct()
    {
        // $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function register(RegisterRequest $request)
    {
        try {

            $userData = $request->except(['password', 'password_confirmation']);
            $userData['password'] = Hash::make($request['password']);
            $userData['role_id'] = 3;
            $userData['status'] = 1;

            $user = User::create($userData);

            // Tạo giỏ hàng cho người dùng mới
            Cart::create([
                'user_id' => $user->id,
                'status' => 1,
            ]);
            return response()->json(['message' => 'Thêm mới tài khoản thành công'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Thêm mới tài khoản thất bại', 'error' => $e->getMessage()], 500);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Dữ liệu không hợp lệ', 'errors' => $e->errors()], 422);
        }
    }

    public function login(LoginRequest $request)
    {
        $account = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($account)) {
            return response()->json(['error' => 'Fail'], 401);
        }
        if (!Auth::user()->status) {
            return response()->json(['error' => 'Tài khoản đã bị khóa'], 403);
        }
        $data = [
            'random' => rand() . time(),
            'exp' => time() + config('jwt.refresh_ttl'),
        ];

        $refreshToken = JWTAuth::getJWTProvider()->encode($data);
        return $this->respondWithToken($token, $refreshToken);
    }

    public function respondWithToken($token, $refreshToken)
    {
        return response()->json([
            'access_token' => $token,
            'refresh_token' => $refreshToken,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ]);
    }

    public function profile()
    {
        $user = Auth::user();
        // Lấy cart_id của user
        $cart = Cart::where('user_id', $user->id)->first();
        return response()->json([
            'user' => $user,
            'cart_id' => $cart ? $cart->id : null
        ], 200);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh(Request $request)
    {
        $refreshToken = $request['refresh_token'];
        return response()->json($refreshToken);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $user = User::where('email', $request->email)->first();

        $token = Password::broker()->createToken($user);

        $user->notify(new ResetPasswordNotification($token, $user->email));

        return response()->json(['message' => 'Đã gửi email đặt lại mật khẩu!'], 200);
    }

    public function resetPassword(Request $request)
    {
        $request->validate(
            [
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|confirmed|min:6',
            ],
            [
                'token.required' => 'Token không được để trống.',
                'email.required' => 'Email không được để trống.',
                'email.email' => 'Email không hợp lệ.',
                'password.required' => 'Mật khẩu không được để trống.',
                'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
                'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            ]
        );

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = bcrypt($password);
                $user->setRememberToken(Str::random(60));
                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Đặt lại mật khẩu thành công!'], 200);
        } else {
            return response()->json(['message' => 'Token không hợp lệ hoặc đã hết hạn!'], 400);
        }
    }
}
