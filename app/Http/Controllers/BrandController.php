<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $query = Brand::query();
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        $brands = $query->orderBy('created_at', 'desc')->get();
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
                'message' => 'Tạo thương hiệu thành công',
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
            return response()->json(['message' => 'Không tìm thấy thương hiệu'], 404);
        }
        return response()->json($brand);
    }

    public function update(Request $request, $id)
    {
        try {
            $brand = Brand::find($id);
            if (!$brand) {
                return response()->json(['message' => 'Không tìm thấy thương hiệu'], 404);
            }

            $request->validate(
                [
                    'name' => 'required|string|max:255',
                    'description' => 'nullable|string',
                    'status' => 'required',
                ],
                [
                    'name.required' => 'Tên thương hiệu là bắt buộc.',
                    'status.required' => 'Trạng thái thương hiệu là bắt buộc.',
                ],
            );

            $dataToUpdate = [
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->status,
            ];

            if ($request->hasFile('logo_url')) {
                if ($brand->logo_url && Storage::disk('public')->exists($brand->logo_url)) {
                    Storage::disk('public')->delete($brand->logo_url);
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

        if ($brand->logo_url && Storage::disk('public')->exists($brand->logo_url)) {
            Storage::disk('public')->delete($brand->logo_url);
        }

        $brand->delete();

        return response()->json(['message' => 'Thương hiệu đã được xóa thành công']);
    }
}
