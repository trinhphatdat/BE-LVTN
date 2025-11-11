<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use Illuminate\Validation\ValidationException;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::all();
        return response()->json($brands);
    }

    public function store(Request $request)
    {
        try {
            $request->validate(
                [
                    'name' => 'required|string|max:255',
                    'description' => 'nullable|string',
                    'logo_url' => 'required|image',
                    'status' => 'required',
                ],
                [
                    'name.required' => 'Tên thương hiệu là bắt buộc.',
                    'logo_url.required' => 'Logo thương hiệu là bắt buộc.',
                    'logo_url.image' => 'Logo thương hiệu phải là một tệp hình ảnh hợp lệ.',
                    'status.required' => 'Trạng thái thương hiệu là bắt buộc.',
                ],
            );

            $path = $request->file('logo_url')->store('brands', 'public');

            $brand = Brand::create([
                'name' => $request->name,
                'description' => $request->description,
                'logo_url' => $path,
                'status' => $request->status,
            ]);

            return response()->json([
                'message' => 'Brand created successfully',
                'data' => $brand,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating brand',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json(['message' => 'Brand not found'], 404);
        }
        return response()->json($brand);
    }

    public function update(Request $request, $id)
    {
        try {
            $brand = Brand::find($id);
            if (!$brand) {
                return response()->json(['message' => 'Brand not found'], 404);
            }

            $request->validate(
                [
                    'name' => 'required|string|max:255',
                    'description' => 'nullable|string',
                    'logo_url' => 'required|image',
                    'status' => 'required',
                ],
                [
                    'name.required' => 'Tên thương hiệu là bắt buộc.',
                    'logo_url.required' => 'Logo thương hiệu là bắt buộc.',
                    'logo_url.image' => 'Logo thương hiệu phải là một tệp hình ảnh hợp lệ.',
                    'status.required' => 'Trạng thái thương hiệu là bắt buộc.',
                ],
            );

            $dataToUpdate = [
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->status,
            ];

            // Nếu có upload logo mới
            if ($request->hasFile('logo_url')) {
                // Xóa logo cũ nếu tồn tại
                if ($brand->logo_url && \Storage::disk('public')->exists($brand->logo_url)) {
                    \Storage::disk('public')->delete($brand->logo_url);
                }

                $dataToUpdate['logo_url'] = $request->file('logo_url')->store('brands', 'public');
            }

            $brand->update($dataToUpdate);

            return response()->json(['message' => 'Brand updated successfully', 'data' => $brand]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating brand',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json(['message' => 'Brand not found'], 404);
        }

        // Xóa logo nếu tồn tại
        if ($brand->logo_url && \Storage::disk('public')->exists($brand->logo_url)) {
            \Storage::disk('public')->delete($brand->logo_url);
        }

        $brand->delete();

        return response()->json(['message' => 'Brand deleted successfully']);
    }
}
