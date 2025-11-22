<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function index()
    {
        $product = Product::with([
            'brand',
            'productVariants' => function ($query) {
                $query->where('status', 1);
            },
            'productVariants.size',
            'productVariants.color'
        ])
            ->whereHas('productVariants', function ($query) {
                $query->where('status', 1);
            })
            ->get();

        return response()->json($product);
    }

    public function show(string $id)
    {
        try {
            $product = Product::with([
                'brand:id,name',
                'productVariants' => function ($query) {
                    $query->where('status', 1);
                },
                'productVariants.size:id,name',
                'productVariants.color:id,name,hex_code'
            ])
                ->findOrFail($id);

            return response()->json($product, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Product not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $product = Product::findOrFail($id);

            $validated = $request->validate(
                [
                    'brand_id' => 'required',
                    'title' => 'required|string|max:255',
                    'description' => 'nullable|string',
                    'gender' => 'required|in:male,female,unisex',
                    'material' => 'nullable|string|max:255',
                    'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                    'variants' => 'required|json',
                ],
                [
                    'brand_id.required' => 'Thương hiệu là bắt buộc.',
                    'title.required' => 'Tên sản phẩm là bắt buộc.',
                    'gender.required' => 'Loại sản phẩm là bắt buộc.',
                    'thumbnail.image' => 'Ảnh đại diện phải là một tệp hình ảnh hợp lệ.',
                    'thumbnail.mimes' => 'Ảnh đại diện phải có định dạng: jpeg, png, jpg, gif.',
                    'thumbnail.max' => 'Kích thước ảnh đại diện không được vượt quá 2MB.',
                    'variants.required' => 'Thông tin biến thể sản phẩm là bắt buộc.',
                ]
            );

            DB::beginTransaction();

            try {
                // Xoá hình cũ thêm hình mới nếu có
                $thumbnailPath = $product->thumbnail;
                if ($request->hasFile('thumbnail')) {
                    if ($thumbnailPath && Storage::disk('public')->exists($thumbnailPath)) {
                        Storage::disk('public')->delete($thumbnailPath);
                    }
                    $thumbnailPath = $request->file('thumbnail')->store('products/thumbnails', 'public');
                }

                $newVariants = json_decode($validated['variants'], true);

                // lấy biến thể hiện tại + format key
                $existingVariants = $product->productVariants->keyBy(function ($variant) {
                    return $variant->size_id . '-' . $variant->color_id;
                });

                $processedKeys = [];
                $allPrices = [];
                $allDiscounts = [];

                foreach ($newVariants as $index => $variantData) {
                    $key = $variantData['size_id'] . '-' . $variantData['color_id'];
                    $processedKeys[] = $key;

                    $discount = $variantData['discount'] ?? 0;
                    $price = $variantData['original_price'] - ($variantData['original_price'] * $discount / 100);

                    $allPrices[] = $price;
                    $allDiscounts[] = $discount;

                    $imageUrl = null;

                    if ($request->hasFile("variant_image_{$index}")) {
                        $imageUrl = $request->file("variant_image_{$index}")->store('products/variants', 'public');
                    }

                    if ($existingVariants->has($key)) {
                        $existingVariant = $existingVariants->get($key);

                        $updateData = [
                            'stock' => $variantData['stock'] ?? 0,
                            'original_price' => $variantData['original_price'],
                            'discount' => $discount,
                            'price' => $price,
                            'status' => 1,
                        ];

                        if ($imageUrl) {
                            if ($existingVariant->image_url && Storage::disk('public')->exists($existingVariant->image_url)) {
                                Storage::disk('public')->delete($existingVariant->image_url);
                            }
                            $updateData['image_url'] = $imageUrl;
                        }

                        $existingVariant->update($updateData);
                    } else {
                        ProductVariant::create([
                            'product_id' => $product->id,
                            'size_id' => $variantData['size_id'],
                            'color_id' => $variantData['color_id'],
                            'stock' => $variantData['stock'] ?? 0,
                            'original_price' => $variantData['original_price'],
                            'discount' => $discount,
                            'price' => $price,
                            'image_url' => $imageUrl,
                            'status' => 1,
                        ]);
                    }
                }

                foreach ($existingVariants as $key => $variant) {
                    if (!in_array($key, $processedKeys)) {
                        $variant->update(['status' => 0]);
                        // Xoá hình của biến thể không dùng nữa
                        // if ($variant->image_url && Storage::disk('public')->exists($variant->image_url)) {
                        //     Storage::disk('public')->delete($variant->image_url);
                        // }
                    }
                }

                // Gán lại giá trị cho bảng product
                $minPrice = min($allPrices);
                $maxPrice = max($allPrices);
                $maxDiscount = max($allDiscounts);
                $hasDiscount = $maxDiscount > 0 ? 1 : 0;

                $product->update([
                    'brand_id' => $validated['brand_id'],
                    'title' => $validated['title'],
                    'description' => $validated['description'],
                    'gender' => $validated['gender'],
                    'material' => $validated['material'],
                    'thumbnail' => $thumbnailPath,
                    'min_price' => $minPrice,
                    'max_price' => $maxPrice,
                    'has_discount' => $hasDiscount,
                    'max_discount' => $maxDiscount,
                ]);

                DB::commit();

                return response()->json([
                    'message' => 'Product updated successfully',
                    'data' => $product->load(['brand', 'productVariants.size', 'productVariants.color'])
                ], 200);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $product = Product::findOrFail($id);

            DB::beginTransaction();

            try {
                //Xoá hình biến thể
                foreach ($product->productVariants as $variant) {
                    if ($variant->image_url && Storage::disk('public')->exists($variant->image_url)) {
                        Storage::disk('public')->delete($variant->image_url);
                    }
                    $variant->delete();
                }

                //Xoá thumbnail ở product
                if ($product->thumbnail && Storage::disk('public')->exists($product->thumbnail)) {
                    Storage::disk('public')->delete($product->thumbnail);
                }

                $product->delete();

                DB::commit();

                return response()->json([
                    'message' => 'Product deleted successfully'
                ], 200);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate(
                [
                    'brand_id' => 'required',
                    'title' => 'required|string|max:255',
                    'description' => 'nullable|string',
                    'gender' => 'required|in:male,female,unisex',
                    'material' => 'nullable|string|max:255',
                    'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                    'variants' => 'required|json',
                ],
                [
                    'brand_id.required' => 'Thương hiệu là bắt buộc.',
                    'title.required' => 'Tên sản phẩm là bắt buộc.',
                    'gender.required' => 'Loại sản phẩm là bắt buộc.',
                    'thumbnail.required' => 'Ảnh đại diện là bắt buộc.',
                    'thumbnail.image' => 'Ảnh đại diện phải là một tệp hình ảnh hợp lệ.',
                    'thumbnail.mimes' => 'Ảnh đại diện phải có định dạng: jpeg, png, jpg, gif.',
                    'thumbnail.max' => 'Kích thước ảnh đại diện không được vượt quá 2MB.',
                    'variants.required' => 'Thông tin biến thể sản phẩm là bắt buộc.',
                ]
            );

            DB::beginTransaction();

            try {
                $thumbnailPath = null;
                if ($request->hasFile('thumbnail')) {
                    $thumbnailPath = $request->file('thumbnail')->store('products/thumbnails', 'public');
                }

                // Parse variants to calculate summary fields
                $variants = json_decode($validated['variants'], true);
                $allPrices = [];
                $allDiscounts = [];

                foreach ($variants as $variantData) {
                    $discount = $variantData['discount'] ?? 0;
                    $price = $variantData['original_price'] - ($variantData['original_price'] * $discount / 100);
                    $allPrices[] = $price;
                    $allDiscounts[] = $discount;
                }

                $minPrice = min($allPrices);
                $maxPrice = max($allPrices);
                $maxDiscount = max($allDiscounts);
                $hasDiscount = $maxDiscount > 0 ? 1 : 0;

                $product = Product::create([
                    'brand_id' => $validated['brand_id'],
                    'title' => $validated['title'],
                    'description' => $validated['description'],
                    'gender' => $validated['gender'],
                    'material' => $validated['material'],
                    'thumbnail' => $thumbnailPath,
                    'min_price' => $minPrice,
                    'max_price' => $maxPrice,
                    'has_discount' => $hasDiscount,
                    'max_discount' => $maxDiscount,
                    'status' => 1,
                ]);

                foreach ($variants as $index => $variantData) {
                    $discount = $variantData['discount'] ?? 0;
                    $price = $variantData['original_price'] - ($variantData['original_price'] * $discount / 100);

                    $imageUrl = null;

                    if ($request->hasFile("variant_image_{$index}")) {
                        $imageUrl = $request->file("variant_image_{$index}")->store('products/variants', 'public');
                    }

                    ProductVariant::create([
                        'product_id' => $product->id,
                        'size_id' => $variantData['size_id'],
                        'color_id' => $variantData['color_id'],
                        'stock' => $variantData['stock'] ?? 0,
                        'original_price' => $variantData['original_price'],
                        'discount' => $discount,
                        'price' => $price,
                        'image_url' => $imageUrl,
                        'status' => 1,
                    ]);
                }

                DB::commit();

                return response()->json([
                    'message' => 'Product created successfully',
                    'data' => $product->load(['brand', 'productVariants.size', 'productVariants.color'])
                ], 201);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create product',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
