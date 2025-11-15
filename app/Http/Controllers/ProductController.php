<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function index()
    {
        $product = Product::with(['brand', 'productVariants.size', 'productVariants.color', 'productVariants.productImages'])->get();
        return response()->json($product);
    }

    public function show(string $id)
    {
        try {
            $product = Product::with([
                'brand:id,name',
                'productVariants.size:id,name',
                'productVariants.color:id,name,hex_code',
                'productVariants.productImages:id,product_variant_id,image_url'
            ])->findOrFail($id);

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

            $validated = $request->validate([
                'brand_id' => 'required|exists:brands,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'gender' => 'required|in:male,female,unisex',
                'material' => 'nullable|string|max:255',
                'original_price' => 'required|numeric|min:0',
                'discount' => 'nullable|numeric|min:0|max:100',
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'variants' => 'required|json',
                'variant_images' => 'nullable|json',
            ]);

            DB::beginTransaction();

            try {
                $discount = $validated['discount'] ?? 0;
                $price = $validated['original_price'] - ($validated['original_price'] * $discount / 100);

                // Upload new thumbnail if provided
                $thumbnailPath = $product->thumbnail;
                if ($request->hasFile('thumbnail')) {
                    if ($thumbnailPath && Storage::disk('public')->exists($thumbnailPath)) {
                        Storage::disk('public')->delete($thumbnailPath);
                    }
                    $thumbnailPath = $request->file('thumbnail')->store('products/thumbnails', 'public');
                }

                // Update product
                $product->update([
                    'brand_id' => $validated['brand_id'],
                    'title' => $validated['title'],
                    'description' => $validated['description'],
                    'gender' => $validated['gender'],
                    'material' => $validated['material'],
                    'original_price' => $validated['original_price'],
                    'discount' => $discount,
                    'price' => $price,
                    'thumbnail' => $thumbnailPath,
                ]);

                // **CẬP NHẬT THÔNG MINH VARIANTS**
                $newVariants = json_decode($validated['variants'], true);
                $variantImages = $request->has('variant_images') ? json_decode($validated['variant_images'], true) : [];

                // Lấy danh sách variant hiện tại
                $existingVariants = $product->productVariants->keyBy(function ($variant) {
                    return $variant->size_id . '-' . $variant->color_id;
                });

                $processedKeys = [];

                foreach ($newVariants as $index => $variantData) {
                    $key = $variantData['size_id'] . '-' . $variantData['color_id'];
                    $processedKeys[] = $key;

                    if ($existingVariants->has($key)) {
                        // **CẬP NHẬT variant có sẵn**
                        $existingVariant = $existingVariants->get($key);
                        $existingVariant->update([
                            'stock' => $variantData['stock'] ?? 0,
                            'status' => 1,
                        ]);

                        // Cập nhật ảnh nếu có ảnh mới
                        if (isset($variantImages[$index]) && !empty($variantImages[$index])) {
                            // Xóa ảnh cũ
                            foreach ($existingVariant->productImages as $image) {
                                if (Storage::disk('public')->exists($image->image_url)) {
                                    Storage::disk('public')->delete($image->image_url);
                                }
                                $image->delete();
                            }

                            // Thêm ảnh mới
                            foreach ($variantImages[$index] as $imageKey) {
                                if ($request->hasFile("variant_image_{$imageKey}")) {
                                    $imagePath = $request->file("variant_image_{$imageKey}")->store('products/variants', 'public');
                                    ProductImage::create([
                                        'product_variant_id' => $existingVariant->id,
                                        'image_url' => $imagePath,
                                    ]);
                                }
                            }
                        }
                    } else {
                        // **TẠO MỚI variant**
                        $productVariant = ProductVariant::create([
                            'product_id' => $product->id,
                            'size_id' => $variantData['size_id'],
                            'color_id' => $variantData['color_id'],
                            'stock' => $variantData['stock'] ?? 0,
                            'status' => 1,
                        ]);

                        // Upload ảnh cho variant mới
                        if (isset($variantImages[$index]) && !empty($variantImages[$index])) {
                            foreach ($variantImages[$index] as $imageKey) {
                                if ($request->hasFile("variant_image_{$imageKey}")) {
                                    $imagePath = $request->file("variant_image_{$imageKey}")->store('products/variants', 'public');
                                    ProductImage::create([
                                        'product_variant_id' => $productVariant->id,
                                        'image_url' => $imagePath,
                                    ]);
                                }
                            }
                        }
                    }
                }

                // **XÓA các variant không còn tồn tại** (chuyển status = 0 thay vì xóa)
                foreach ($existingVariants as $key => $variant) {
                    if (!in_array($key, $processedKeys)) {
                        // Không xóa hẳn, chỉ đánh dấu là không khả dụng
                        $variant->update(['status' => 0]);
                    }
                }

                DB::commit();

                return response()->json([
                    'message' => 'Product updated successfully',
                    'data' => $product->load(['brand', 'productVariants.size', 'productVariants.color', 'productVariants.productImages'])
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
                // Delete all variant images
                foreach ($product->productVariants as $variant) {
                    foreach ($variant->productImages as $image) {
                        if (Storage::disk('public')->exists($image->image_url)) {
                            Storage::disk('public')->delete($image->image_url);
                        }
                        $image->delete();
                    }
                    $variant->delete();
                }

                // Delete thumbnail
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
            $validated = $request->validate([
                'brand_id' => 'required|exists:brands,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'gender' => 'required|in:male,female,unisex',
                'material' => 'nullable|string|max:255',
                'original_price' => 'required|numeric|min:0',
                'discount' => 'nullable|numeric|min:0|max:100',
                'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'variants' => 'required|json',
                'variant_images' => 'nullable|json', // Ảnh cho từng biến thể
            ]);

            // Calculate price after discount
            $discount = $validated['discount'] ?? 0;
            $price = $validated['original_price'] - ($validated['original_price'] * $discount / 100);

            // Upload thumbnail
            $thumbnailPath = null;
            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('products/thumbnails', 'public');
            }

            // Create product
            $product = Product::create([
                'brand_id' => $validated['brand_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'gender' => $validated['gender'],
                'material' => $validated['material'],
                'original_price' => $validated['original_price'],
                'discount' => $discount,
                'price' => $price,
                'thumbnail' => $thumbnailPath,
                'status' => 1,
            ]);

            // Create product variants
            $variants = json_decode($validated['variants'], true);
            $variantImages = $request->has('variant_images') ? json_decode($validated['variant_images'], true) : [];

            foreach ($variants as $index => $variantData) {
                $productVariant = ProductVariant::create([
                    'product_id' => $product->id,
                    'size_id' => $variantData['size_id'],
                    'color_id' => $variantData['color_id'],
                    'stock' => $variantData['stock'] ?? 0,
                    'status' => 1,
                ]);

                // Upload images for this variant
                if (isset($variantImages[$index]) && !empty($variantImages[$index])) {
                    foreach ($variantImages[$index] as $imageKey) {
                        if ($request->hasFile("variant_image_{$imageKey}")) {
                            $imagePath = $request->file("variant_image_{$imageKey}")->store('products/variants', 'public');
                            ProductImage::create([
                                'product_variant_id' => $productVariant->id,
                                'image_url' => $imagePath,
                            ]);
                        }
                    }
                }
            }

            return response()->json([
                'message' => 'Product created successfully',
                'data' => $product->load(['brand', 'productVariants.size', 'productVariants.color', 'productVariants.productImages'])
            ], 201);
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
