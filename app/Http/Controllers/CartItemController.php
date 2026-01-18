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

        // Kiểm tra product_type
        $isCouple = $productVariant->product->product_type === 'couple';

        // Kiểm tra xung đột: Nếu sản phẩm thường thì không được có couple trong giỏ và ngược lại
        $existingItems = CartItem::with('productVariant.product')
            ->where('cart_id', $request->cart_id)
            ->get();

        foreach ($existingItems as $item) {
            $existingIsCouple = $item->productVariant?->product?->product_type === 'couple';
            if ($isCouple !== $existingIsCouple) {
                return response()->json([
                    'success' => false,
                    'message' => $isCouple
                        ? 'Không thể thêm sản phẩm couple khi giỏ hàng đã có sản phẩm thường. Vui lòng thanh toán hoặc xóa sản phẩm hiện có.'
                        : 'Không thể thêm sản phẩm thường khi giỏ hàng đã có sản phẩm couple. Vui lòng thanh toán hoặc xóa sản phẩm couple.'
                ], 400);
            }
        }

        // Sản phẩm couple không được thêm theo cách thông thường
        if ($isCouple) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng sử dụng chức năng thêm couple để thêm sản phẩm này.'
            ], 400);
        }

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
            $existingCartItem->price = $price;
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
        DB::beginTransaction();
        try {
            $cartItem = CartItem::with('productVariant.product')->find($id);

            if (!$cartItem) {
                return response()->json(['message' => 'Không tìm thấy sản phẩm'], 404);
            }

            $isCouple = $cartItem->productVariant?->product?->product_type === 'couple';

            // Nếu là sản phẩm couple, tìm và xóa item còn lại cùng product_id
            if ($isCouple) {
                $productId = $cartItem->productVariant->product_id;

                // Xóa tất cả cart items có cùng product_id (cả 2 variants của couple)
                CartItem::whereHas('productVariant', function ($query) use ($productId) {
                    $query->where('product_id', $productId);
                })
                    ->where('cart_id', $cartItem->cart_id)
                    ->delete();

                DB::commit();
                return response()->json([
                    'message' => 'Xóa bộ couple khỏi giỏ hàng thành công'
                ], 200);
            }

            // Sản phẩm thường - xóa bình thường
            $cartItem->delete();

            DB::commit();

            return response()->json([
                'message' => 'Xóa sản phẩm khỏi giỏ hàng thành công'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function increment(string $id)
    {
        $item = CartItem::with('productVariant.product')->find($id);

        if (!$item) {
            return response()->json(['message' => 'Không tìm thấy sản phẩm'], 404);
        }

        // Không cho phép tăng số lượng sản phẩm couple
        if ($item->productVariant?->product?->product_type === 'couple') {
            return response()->json([
                'message' => 'Không thể thay đổi số lượng sản phẩm couple. Mỗi bộ couple chỉ có 1 áo nam và 1 áo nữ.'
            ], 400);
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
        $item = CartItem::with('productVariant.product')->find($id);

        if (!$item) {
            return response()->json(['message' => 'Không tìm thấy sản phẩm'], 404);
        }

        // Không cho phép giảm số lượng sản phẩm couple
        if ($item->productVariant?->product?->product_type === 'couple') {
            return response()->json([
                'message' => 'Không thể thay đổi số lượng sản phẩm couple. Mỗi bộ couple chỉ có 1 áo nam và 1 áo nữ.'
            ], 400);
        }

        if ($item->quantity > 1) {
            $item->quantity -= 1;
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

    public function storeCouple(Request $request)
    {
        $maleVariant = ProductVariant::with(['product', 'size', 'color'])
            ->findOrFail($request->male_variant_id);

        $femaleVariant = ProductVariant::with(['product', 'size', 'color'])
            ->findOrFail($request->female_variant_id);

        // Kiểm tra cả 2 variants phải cùng product và product đó phải là couple
        if ($maleVariant->product_id !== $femaleVariant->product_id) {
            return response()->json([
                'success' => false,
                'message' => 'Hai variants phải thuộc cùng một sản phẩm'
            ], 400);
        }

        if ($maleVariant->product->product_type !== 'couple') {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm này không phải là sản phẩm couple'
            ], 400);
        }

        // Kiểm tra xung đột: Không được có sản phẩm thường trong giỏ
        $existingItems = CartItem::with('productVariant.product')
            ->where('cart_id', $request->cart_id)
            ->get();

        $hasNormalProduct = false;
        $hasCoupleProduct = false;

        foreach ($existingItems as $item) {
            $itemProductType = $item->productVariant?->product?->product_type;

            if ($itemProductType === 'couple') {
                $hasCoupleProduct = true;
            } else {
                $hasNormalProduct = true;
            }
        }

        // Không cho phép thêm couple nếu đã có sản phẩm thường
        if ($hasNormalProduct) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể thêm sản phẩm couple khi giỏ hàng đã có sản phẩm thường. Vui lòng thanh toán hoặc xóa sản phẩm hiện có.'
            ], 400);
        }

        // ✨ RÀNG BUỘC MỚI: Không cho phép thêm couple nếu đã có couple khác
        if ($hasCoupleProduct) {
            return response()->json([
                'success' => false,
                'message' => 'Giỏ hàng chỉ được chứa 1 bộ couple. Vui lòng thanh toán hoặc xóa bộ couple hiện tại trước khi thêm bộ mới.'
            ], 400);
        }

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
            // Nếu 2 variants giống nhau thì kiểm tra stock đủ cho 2 sản phẩm
            if ($request->male_variant_id === $request->female_variant_id) {
                if ($maleVariant->stock < 2) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Không đủ hàng trong kho cho bộ couple này (cần 2 sản phẩm)'
                    ], 400);
                }
            }

            // Tạo mới 2 cart items
            $maleCartItem = CartItem::create([
                'cart_id' => $request->cart_id,
                'product_variant_id' => $request->male_variant_id,
                'quantity' => 1,
                'price' => $maleVariant->price,
                'total_price' => $maleVariant->price,
            ]);

            $femaleCartItem = CartItem::create([
                'cart_id' => $request->cart_id,
                'product_variant_id' => $request->female_variant_id,
                'quantity' => 1,
                'price' => $femaleVariant->price,
                'total_price' => $femaleVariant->price,
            ]);

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
