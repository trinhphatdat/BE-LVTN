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
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'logo_url' => 'nullable|string',
                'status' => 'required',
            ]);

            $brand = Brand::create([
                'name' => $request->name,
                'description' => $request->description,
                'logo_url' => $request->logo_url,
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

            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'logo_url' => 'nullable|string',
                'status' => 'required',
            ]);

            $brand->update([
                'name' => $request->name,
                'description' => $request->description,
                'logo_url' => $request->logo_url,
                'status' => $request->status,
            ]);

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

        $brand->delete();

        return response()->json(['message' => 'Brand deleted successfully']);
    }
}
