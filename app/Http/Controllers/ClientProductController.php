<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ClientProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        // Lọc theo khoảng giá
        if ($request->has('min_price') && $request->has('max_price')) {
            $minPrice = $request->input('min_price');
            $maxPrice = $request->input('max_price');

            $query->whereBetween('price', [$minPrice, $maxPrice]);
        }

        // Sắp xếp theo giá
        if ($request->has('sort')) {
            $sortOrder = $request->input('sort');

            if ($sortOrder === 'asc') {
                $query->orderBy('price', 'asc');
            } else if ($sortOrder === 'desc') {
                $query->orderBy('price', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(10);

        return response()->json($products);
    }
    public function show($id)
    {
        $product = Product::with(['productVariants.color', 'productVariants.size', 'productVariants.productImages'])
            ->findOrFail($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        return response()->json($product);
    }
    public function search(Request $request)
    {
        $keyword = $request->input('q');
        $products = Product::where('title', 'like', '%' . $keyword . '%')->get();

        return response()->json($products);
    }
}
