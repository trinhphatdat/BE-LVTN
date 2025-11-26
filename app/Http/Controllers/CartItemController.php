<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartItemController extends Controller
{

    public function index() {}

    public function store(Request $request)
    {
        $productVariant = ProductVariant::with(['product', 'size', 'color'])
            ->findOrFail($request->product_variant_id);

        // Kiểm tra stock
        if ($productVariant->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Số lượng sản phẩm trong kho không đủ',
                'available_stock' => $productVariant->stock
            ], 400);
        }

        // Lấy giá từ variant (đã tính giảm giá)
        $price = $productVariant->price;

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
            $existingCartItem->price = $price; // Cập nhật giá mới nhất
            $existingCartItem->total_price = $price * $newQuantity;
            $existingCartItem->save();

            $cartItem = $existingCartItem;
        } else {
            // Nếu chưa tồn tại, tạo mới
            $cartItem = CartItem::create([
                'cart_id' => $request->cart_id,
                'product_variant_id' => $request->product_variant_id,
                'quantity' => $request->quantity,
                'price' => $price, // Giá đã tính giảm từ variant
                'total_price' => $price * $request->quantity,
            ]);
        }

        // Load relationships để trả về đầy đủ thông tin
        $cartItem->load(['productVariant.size', 'productVariant.color', 'productVariant.product']);

        return response()->json([
            'message' => 'Thêm sản phẩm vào giỏ hàng thành công',
            'data' => $cartItem
        ], 201);
    }

    public function show(string $cart_id)
    {
        $items = CartItem::with([
            'productVariant.size',
            'productVariant.color',
            'productVariant.product.brand'
        ])
            ->where('cart_id', $cart_id)
            ->get();

        return response()->json([
            'count' => $items->sum('quantity'),
            'total_money' => $items->sum('total_price'),
            'data' => $items,
        ], 200);
    }

    public function destroy(string $id)
    {
        $cartItem = CartItem::find($id);

        if (!$cartItem) {
            return response()->json(['message' => 'Không tìm thấy sản phẩm'], 404);
        }

        $cartItem->delete();

        return response()->json(['message' => 'Xoá sản phẩm khỏi giỏ hàng thành công'], 200);
    }

    public function increment(string $id)
    {
        $item = CartItem::with('productVariant')->find($id);

        if (!$item) {
            return response()->json(['message' => 'Không tìm thấy sản phẩm'], 404);
        }

        $productVariant = $item->productVariant;

        // Kiểm tra stock
        if ($productVariant->stock < $item->quantity + 1) {
            return response()->json([
                'message' => 'Số lượng sản phẩm trong kho không đủ',
                'available_stock' => $productVariant->stock
            ], 400);
        }

        $item->quantity += 1;
        // Cập nhật giá mới nhất từ variant
        $item->price = $productVariant->price;
        $item->total_price = $item->price * $item->quantity;
        $item->save();

        $item->load(['productVariant.size', 'productVariant.color', 'productVariant.product']);

        return response()->json([
            'message' => 'Tăng số lượng sản phẩm thành công',
            'data' => $item
        ], 200);
    }

    public function decrement(string $id)
    {
        $item = CartItem::with('productVariant')->find($id);

        if (!$item) {
            return response()->json(['message' => 'Không tìm thấy sản phẩm'], 404);
        }

        if ($item->quantity > 1) {
            $item->quantity -= 1;
            // Cập nhật giá mới nhất từ variant
            $item->price = $item->productVariant->price;
            $item->total_price = $item->price * $item->quantity;
            $item->save();

            $item->load(['productVariant.size', 'productVariant.color', 'productVariant.product']);

            return response()->json([
                'message' => 'Giảm số lượng sản phẩm thành công',
                'data' => $item
            ], 200);
        }

        return response()->json([
            'message' => 'Số lượng tối thiểu là 1. Vui lòng xóa sản phẩm nếu không muốn mua.'
        ], 400);
    }

    // Thêm couple: Thêm 2 variants riêng biệt như bình thường
    public function storeCouple(Request $request)
    {
        $maleVariant = ProductVariant::with(['product', 'size', 'color'])
            ->findOrFail($request->male_variant_id);

        $femaleVariant = ProductVariant::with(['product', 'size', 'color'])
            ->findOrFail($request->female_variant_id);

        // Kiểm tra stock của cả 2 variants
        if ($maleVariant->stock < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Size nam không đủ hàng'
            ], 400);
        }

        if ($femaleVariant->stock < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Size nữ không đủ hàng'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Thêm variant nam
            $existingMaleItem = CartItem::where('cart_id', $request->cart_id)
                ->where('product_variant_id', $request->male_variant_id)
                ->first();

            if ($existingMaleItem) {
                // Nếu đã có, tăng số lượng
                if ($maleVariant->stock < $existingMaleItem->quantity + 1) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Size nam không đủ hàng trong kho'
                    ], 400);
                }
                $existingMaleItem->quantity += 1;
                $existingMaleItem->price = $maleVariant->price;
                $existingMaleItem->total_price = $maleVariant->price * $existingMaleItem->quantity;
                $existingMaleItem->save();
                $maleCartItem = $existingMaleItem;
            } else {
                // Nếu chưa có, tạo mới
                $maleCartItem = CartItem::create([
                    'cart_id' => $request->cart_id,
                    'product_variant_id' => $request->male_variant_id,
                    'quantity' => 1,
                    'price' => $maleVariant->price,
                    'total_price' => $maleVariant->price,
                ]);
            }

            // Thêm variant nữ
            $existingFemaleItem = CartItem::where('cart_id', $request->cart_id)
                ->where('product_variant_id', $request->female_variant_id)
                ->first();

            if ($existingFemaleItem) {
                // Nếu đã có, tăng số lượng
                if ($femaleVariant->stock < $existingFemaleItem->quantity + 1) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Size nữ không đủ hàng trong kho'
                    ], 400);
                }
                $existingFemaleItem->quantity += 1;
                $existingFemaleItem->price = $femaleVariant->price;
                $existingFemaleItem->total_price = $femaleVariant->price * $existingFemaleItem->quantity;
                $existingFemaleItem->save();
                $femaleCartItem = $existingFemaleItem;
            } else {
                // Nếu chưa có, tạo mới
                $femaleCartItem = CartItem::create([
                    'cart_id' => $request->cart_id,
                    'product_variant_id' => $request->female_variant_id,
                    'quantity' => 1,
                    'price' => $femaleVariant->price,
                    'total_price' => $femaleVariant->price,
                ]);
            }

            DB::commit();

            // Load relationships
            $maleCartItem->load(['productVariant.size', 'productVariant.color', 'productVariant.product']);
            $femaleCartItem->load(['productVariant.size', 'productVariant.color', 'productVariant.product']);

            return response()->json([
                'message' => 'Thêm bộ couple vào giỏ hàng thành công',
                'data' => [
                    'male_item' => $maleCartItem,
                    'female_item' => $femaleCartItem,
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
