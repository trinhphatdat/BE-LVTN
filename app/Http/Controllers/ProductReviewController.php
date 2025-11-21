<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductReview;
use Illuminate\Support\Facades\Validator;

class ProductReviewController extends Controller
{
    public function index()
    {
        $reviews = ProductReview::with(['user', 'product', 'productVariant'])
            ->where('status', true)
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($reviews);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $review = ProductReview::create([
            'user_id' => $request->user_id,
            'product_id' => $request->product_id,
            'product_variant_id' => $request->product_variant_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'status' => true, // Duyệt ngay khi đã đăng nhập
        ]);

        $review->load(['user', 'product', 'productVariant.size', 'productVariant.color']);

        return response()->json([
            'message' => 'Đánh giá đã được gửi thành công',
            'data' => $review
        ], 201);
    }

    public function show(string $product_id)
    {
        $reviews = ProductReview::with(['user', 'product', 'productVariant.size', 'productVariant.color'])
            ->where('product_id', $product_id)
            ->where('status', true)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalReviews = $reviews->count();
        $averageRating = $totalReviews > 0 ? round($reviews->avg('rating'), 1) : 0;

        return response()->json([
            'data' => $reviews,
            'total_reviews' => $totalReviews,
            'average_rating' => $averageRating,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $productReview = ProductReview::find($id);
        if (!$productReview) {
            return response()->json(['message' => 'Đánh giá không tồn tại'], 404);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'sometimes|integer|min:1|max:5',
            'comment' => 'sometimes|string|max:500',
            'status' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $productReview->update($request->all());
        $productReview->load(['user', 'product', 'productVariant']);

        return response()->json($productReview);
    }

    public function destroy(string $id)
    {
        $productReview = ProductReview::find($id);
        if (!$productReview) {
            return response()->json(['message' => 'Đánh giá không tồn tại'], 404);
        }
        $productReview->delete();
        return response()->json(['message' => 'Xoá đánh giá thành công']);
    }
}
