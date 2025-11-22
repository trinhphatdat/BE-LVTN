<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Promotion;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class PromotionController extends Controller
{
    public function index()
    {
        Promotion::where('status', true)
            ->where('end_date', '<', Carbon::now())
            ->update(['status' => false]);
        $promotions = Promotion::all();
        return response()->json($promotions);
    }

    public function store(Request $request)
    {
        try {
            $request->validate(
                [
                    'name' => 'required|string|max:255',
                    'description' => 'nullable|string',
                    'url_image' => 'required|image',
                    'discount_type' => 'required|string',
                    'discount_value' => 'required|numeric',
                    'min_order_value' => 'nullable|numeric',
                    'usage_limit' => 'nullable|integer',
                    'start_date' => 'required|date',
                    'end_date' => 'required|date',
                    'status' => 'required',
                ],
                [
                    'name.required' => 'Tên khuyến mãi là bắt buộc.',
                    'url_image.required' => 'Hình ảnh khuyến mãi là bắt buộc.',
                    'url_image.image' => 'Hình ảnh khuyến mãi phải là một tệp hình ảnh hợp lệ.',
                    'discount_type.required' => 'Loại giảm giá là bắt buộc.',
                    'discount_value.required' => 'Giá trị giảm giá là bắt buộc.',
                    'start_date.required' => 'Ngày bắt đầu là bắt buộc.',
                    'end_date.required' => 'Ngày kết thúc là bắt buộc.',

                    'status.required' => 'Trạng thái khuyến mãi là bắt buộc.',
                ]
            );

            $path = $request->file('url_image')->store('promotions', 'public');

            $promotion = Promotion::create([
                'name' => $request->name,
                'url_image' => $path,
                'description' => $request->description,
                'discount_type' => $request->discount_type,
                'discount_value' => $request->discount_value,
                'min_order_value' => $request->min_order_value,
                'usage_limit' => $request->usage_limit,
                'used_count' => 0,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status,
            ]);
            return response()->json([
                'message' => 'Khuyến mãi được tạo thành công',
                'data' => $promotion,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi khi tạo khuyến mãi',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(string $id)
    {
        $promotion = Promotion::find($id);
        if (!$promotion) {
            return response()->json(['message' => 'Không tìm thấy khuyến mãi'], 404);
        }
        return response()->json($promotion);
    }

    public function update(Request $request, string $id)
    {
        try {
            $promotion = Promotion::find($id);
            if (!$promotion) {
                return response()->json(['message' => 'Không tìm thấy khuyến mãi'], 404);
            }

            $request->validate(
                [
                    'name' => 'required|string|max:255',
                    'description' => 'nullable|string',
                    'url_image' => 'image',
                    'discount_type' => 'required|string',
                    'discount_value' => 'required|numeric',
                    'min_order_value' => 'nullable|numeric',
                    'usage_limit' => 'nullable|integer',
                    'start_date' => 'required|date',
                    'end_date' => 'required|date',
                    'status' => 'required',
                ],
                [
                    'name.required' => 'Tên khuyến mãi là bắt buộc.',
                    'url_image.image' => 'Hình ảnh khuyến mãi phải là một tệp hình ảnh hợp lệ.',
                    'discount_type.required' => 'Loại giảm giá là bắt buộc.',
                    'discount_value.required' => 'Giá trị giảm giá là bắt buộc.',
                    'start_date.required' => 'Ngày bắt đầu là bắt buộc.',
                    'end_date.required' => 'Ngày kết thúc là bắt buộc.',
                    'status.required' => 'Trạng thái khuyến mãi là bắt buộc.',
                ]
            );

            $dataToUpdate = [
                'name' => $request->name,
                'description' => $request->description,
                'discount_type' => $request->discount_type,
                'discount_value' => $request->discount_value,
                'min_order_value' => $request->min_order_value,
                'usage_limit' => $request->usage_limit,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status,
            ];

            if ($request->hasFile('url_image')) {
                if ($promotion->url_image && Storage::disk('public')->exists($promotion->url_image)) {
                    Storage::disk('public')->delete($promotion->url_image);
                }
                $dataToUpdate['url_image'] = $request->file('url_image')->store('promotions', 'public');
            }

            $promotion->update($dataToUpdate);

            return response()->json(['message' => 'Cập nhật khuyến mãi thành công', 'data' => $promotion]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi khi cập nhật khuyến mãi',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        $promotion = Promotion::find($id);
        if (!$promotion) {
            return response()->json(['message' => 'Không tìm thấy khuyến mãi'], 404);
        }

        if ($promotion->url_image && Storage::disk('public')->exists($promotion->url_image)) {
            Storage::disk('public')->delete($promotion->url_image);
        }

        $promotion->delete();

        return response()->json(['message' => 'Khuyến mãi đã được xóa thành công']);
    }
}
