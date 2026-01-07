<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ClientProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['brand', 'productVariants.color', 'productVariants.size']);

        // Lọc theo khoảng giá (dựa trên min_price và max_price của product)
        if ($request->has('min_price') && $request->has('max_price')) {
            $minPrice = $request->input('min_price');
            $maxPrice = $request->input('max_price');

            // Product có giá nằm trong khoảng filter
            $query->where(function ($q) use ($minPrice, $maxPrice) {
                $q->where('min_price', '>=', $minPrice)
                    ->where('min_price', '<=', $maxPrice)
                    ->orWhere('max_price', '>=', $minPrice)
                    ->where('max_price', '<=', $maxPrice)
                    ->orWhere(function ($q2) use ($minPrice, $maxPrice) {
                        $q2->where('min_price', '<=', $minPrice)
                            ->where('max_price', '>=', $maxPrice);
                    });
            });
        }

        // Lọc theo brand
        if ($request->has('brand_id')) {
            $query->where('brand_id', $request->input('brand_id'));
        }

        // Lọc theo product_type
        if ($request->has('product_type')) {
            $query->where('product_type', $request->input('product_type'));
        }

        // Lọc sản phẩm có discount
        if ($request->has('has_discount') && $request->input('has_discount') == 1) {
            $query->where('has_discount', 1);
        }

        // Lọc theo size (phải check trong variants)
        if ($request->has('size_id')) {
            $query->whereHas('productVariants', function ($q) use ($request) {
                $q->where('size_id', $request->input('size_id'));
            });
        }

        // Lọc theo color (phải check trong variants)
        if ($request->has('color_id')) {
            $query->whereHas('productVariants', function ($q) use ($request) {
                $q->where('color_id', $request->input('color_id'));
            });
        }

        // Sắp xếp
        if ($request->has('sort')) {
            $sortOrder = $request->input('sort');

            if ($sortOrder === 'price_asc') {
                $query->orderBy('min_price', 'asc');
            } else if ($sortOrder === 'price_desc') {
                $query->orderBy('max_price', 'desc');
            } else if ($sortOrder === 'discount') {
                $query->orderBy('max_discount', 'desc');
            } else if ($sortOrder === 'newest') {
                $query->orderBy('created_at', 'desc');
            } else if ($sortOrder === 'oldest') {
                $query->orderBy('created_at', 'asc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(4);

        return response()->json($products);
    }
    public function getAllShirts()
    {
        $products = Product::all();
        return response()->json($products);
    }

    public function show($id)
    {
        $product = Product::with([
            'brand',
            'productVariants.color',
            'productVariants.size'
        ])
            ->findOrFail($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product);
    }

    public function search(Request $request)
    {
        $keyword = $request->input('q');

        $products = Product::with(['brand', 'productVariants.color', 'productVariants.size'])
            ->where('title', 'like', '%' . $keyword . '%')
            ->get();

        return response()->json($products);
    }

    public function getFilters()
    {
        // Lấy thông tin để hiển thị filters
        return response()->json([
            'price_range' => [
                'min' => Product::min('min_price') ?? 0,
                'max' => Product::max('max_price') ?? 0,
            ],
            'max_discount' => Product::max('max_discount') ?? 0,
        ]);
    }
}
