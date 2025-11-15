<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartItemController extends Controller
{

    public function index() {}

    public function store(Request $request)
    {
        $productVariant = ProductVariant::with('product')->findOrFail($request->product_variant_id);

        if ($productVariant->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Số lượng sản phẩm trong kho không đủ',
                'available_stock' => $productVariant->stock
            ], 400);
        }
        $price = $productVariant->product->price;
        $existingCartItem = CartItem::where('cart_id', $request->cart_id)
            ->where('product_variant_id', $request->product_variant_id)
            ->first();

        if ($existingCartItem) {
            // Nếu đã tồn tại, cập nhật số lượng
            $newQuantity = $existingCartItem->quantity + $request->quantity;

            // Kiểm tra lại stock với số lượng mới
            if ($productVariant->stock < $newQuantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tổng số lượng vượt quá số lượng trong kho',
                    'available_stock' => $productVariant->stock,
                    'current_in_cart' => $existingCartItem->quantity
                ], 400);
            }

            $existingCartItem->quantity = $newQuantity;
            $existingCartItem->total_price = $price * $newQuantity;
            $existingCartItem->save();

            $cartItem = $existingCartItem;
        } else {
            // Nếu chưa tồn tại, tạo mới
            $cartItem = CartItem::create([
                'cart_id' => $request->cart_id,
                'product_variant_id' => $request->product_variant_id,
                'quantity' => $request->quantity,
                'price' => $price,
                'total_price' => $price * $request->quantity,
            ]);
        }
        return response()->json(['message' => 'Thêm sản phẩm vào giỏ hàng thành công', 'data' => $cartItem], 201);
    }

    public function show(string $cart_id)
    {
        $items = CartItem::with(['productVariant.size', 'productVariant.color', 'productVariant.product'])
            ->where('cart_id', $cart_id)
            ->get();
        return response()->json([
            'count' => $items->count(),
            'total_money' => $items->sum(function ($item) {
                return $item->price * $item->quantity;
            }),
            'data' => $items,
        ], 200);
    }

    public function update(Request $request, string $id)
    {
        $item = CartItem::find($id);
        if (!$item) {
            return response()->json(['message' => 'Không tìm thấy sản phẩm'], 404);
        }
        $item->quantity = $request->input('quantity', $item->quantity);
        $item->total_price = $item->price * $item->quantity;
        $item->save();

        return response()->json(['message' => 'Số lượng sản phẩm đã được cập nhật', 'data' => $item], 200);
    }

    public function destroy(string $id)
    {
        $cartItem = CartItem::find($id);
        if (!$cartItem) {
            return response()->json(['message' => 'Không tìm thấy sản phẩm'], 404);
        }
        $cartItem->delete();
        return response()->json(['message' => 'Xoá sản phẩm khỏi giỏ hàng thành công']);
    }
    public function increment(string $id)
    {
        $item = CartItem::find($id);
        if (!$item) {
            return response()->json(['message' => 'Không tìm thấy sản phẩm'], 404);
        }

        $productVariant = ProductVariant::find($item->product_variant_id);
        if ($productVariant->stock < $item->quantity + 1) {
            return response()->json([
                'message' => 'Số lượng sản phẩm trong kho không đủ',
                'available_stock' => $productVariant->stock
            ], 400);
        }

        $item->increment('quantity', 1);
        $item->total_price = $item->price * $item->quantity;
        $item->save();

        return response()->json(['message' => 'Số lượng sản phẩm đã được cập nhật', 'data' => $item], 200);
    }
    public function decrement(string $id)
    {
        $item = CartItem::find($id); // dùng find thay vì findOrFail để tránh exception
        if (!$item) {
            return response()->json(['message' => 'Không tìm thấy sản phẩm'], 404);
        }
        if ($item->quantity > 1) {
            $item->decrement('quantity', 1);
            $item->total_price = $item->price * $item->quantity;
            $item->save();
            return response()->json(['message' => 'Số lượng sản phẩm đã được cập nhật', 'data' => $item], 200);
        }
        // else {
        //     $item->delete();
        //     return response()->json(['message' => 'Sản phẩm đã được xóa khỏi giỏ hàng'], 200);
        // }
    }
}
