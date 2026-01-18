<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Color;
use Illuminate\Validation\ValidationException;

class ColorController extends Controller
{
    public function index(Request $request)
    {
        $query = Color::query();
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        $colors = $query->orderBy('created_at', 'desc')->get();
        return response()->json($colors);
    }

    public function store(Request $request)
    {
        try {
            $request->validate(
                [
                    'name' => 'required|string|max:255',
                    'hex_code' => 'required|string|size:7',
                    'status' => 'required',
                ],
                [
                    'name.required' => 'Tên màu sắc không được để trống.',
                    'hex_code.required' => 'Mã màu không được để trống.',
                    'hex_code.size' => 'Mã màu phải đúng định dạng 7 ký tự.',
                    'status.required' => 'Trạng thái không được để trống.',
                ]
            );

            $color = Color::create([
                'name' => $request->name,
                'hex_code' => $request->hex_code,
                'status' => $request->status,
            ]);

            return response()->json(['message' => 'Tạo mới màu sắc thành công', 'color' => $color], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating color',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $color = Color::find($id);
        if (!$color) {
            return response()->json(['message' => 'Màu sắc không tồn tại'], 404);
        }
        return response()->json($color);
    }

    public function update(Request $request, $id)
    {
        try {
            $color = Color::find($id);
            if (!$color) {
                return response()->json(['message' => 'Màu sắc không tồn tại'], 404);
            }

            $request->validate(
                [
                    'name' => 'required|string|max:255',
                    'hex_code' => 'required|string|size:7',
                    'status' => 'required',
                ],
                [
                    'name.required' => 'Tên màu sắc không được để trống.',
                    'hex_code.required' => 'Mã màu không được để trống.',
                    'hex_code.size' => 'Mã màu phải đúng định dạng 7 ký tự.',
                    'status.required' => 'Trạng thái không được để trống.',
                ]
            );

            $color->name = $request->name;
            $color->hex_code = $request->hex_code;
            $color->status = $request->status;

            $color->save();

            return response()->json(['message' => 'Cập nhật màu sắc thành công', 'color' => $color]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating color',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        $color = Color::find($id);
        if (!$color) {
            return response()->json(['message' => 'Màu sắc không tồn tại'], 404);
        }

        $color->status = 0;
        $color->save();

        return response()->json(['message' => 'Xoá màu sắc thành công']);
    }
}
