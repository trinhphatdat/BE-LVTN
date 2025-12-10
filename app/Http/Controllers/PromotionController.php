<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\Request;
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
                    'code' => 'required',
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
                    'code.required' => 'Mã khuyến mãi là bắt buộc.',
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
                'code' => $request->code,
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
                    'code' => 'required',
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
                    'code.required' => 'Mã khuyến mãi là bắt buộc.',
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
                'code' => $request->code,
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

    /**
     * Kiểm tra và áp dụng mã khuyến mãi
     */
    public function checkPromotionCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'order_total' => 'required|numeric|min:0'
        ]);

        $promotion = Promotion::where('code', $request->code)
            ->where('status', true)
            ->first();

        if (!$promotion) {
            return response()->json([
                'success' => false,
                'message' => 'Mã khuyến mãi không tồn tại hoặc đã hết hạn'
            ], 404);
        }

        // Kiểm tra thời gian hiệu lực
        $now = Carbon::now();
        if ($now->lt(Carbon::parse($promotion->start_date)) || $now->gt(Carbon::parse($promotion->end_date))) {
            return response()->json([
                'success' => false,
                'message' => 'Mã khuyến mãi đã hết hạn hoặc chưa đến thời gian sử dụng'
            ], 400);
        }

        // Kiểm tra giá trị đơn hàng tối thiểu
        if ($request->order_total < $promotion->min_order_value) {
            return response()->json([
                'success' => false,
                'message' => "Đơn hàng phải có giá trị tối thiểu " . number_format($promotion->min_order_value, 0, ',', '.') . "₫ để áp dụng mã này"
            ], 400);
        }

        // Kiểm tra số lần sử dụng
        if ($promotion->usage_limit && $promotion->used_count >= $promotion->usage_limit) {
            return response()->json([
                'success' => false,
                'message' => 'Mã khuyến mãi đã hết lượt sử dụng'
            ], 400);
        }

        // Tính số tiền được giảm
        $discountAmount = 0;
        $shippingDiscount = 0;

        if ($promotion->discount_type === 'percentage') {
            $discountAmount = ($request->order_total * $promotion->discount_value) / 100;
        } elseif ($promotion->discount_type === 'fixed_amount') {
            $discountAmount = $promotion->discount_value;
        } elseif ($promotion->discount_type === 'free_shipping') {
            $shippingDiscount = $request->shipping_fee ?? 0;
        }

        return response()->json([
            'success' => true,
            'message' => 'Áp dụng mã khuyến mãi thành công',
            'data' => [
                'promotion_id' => $promotion->id,
                'code' => $promotion->code,
                'name' => $promotion->name,
                'discount_type' => $promotion->discount_type,
                'discount_value' => $promotion->discount_value,
                'discount_amount' => round($discountAmount),
                'shipping_discount' => round($shippingDiscount),
                'min_order_value' => $promotion->min_order_value,
            ]
        ]);
    }
}
