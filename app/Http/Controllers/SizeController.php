<?php

namespace App\Http\Controllers;

use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SizeController extends Controller
{
    public function index(Request $request)
    {
        $query = Size::query();
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        $sizes = $query->orderBy('created_at', 'asc')->get();
        return response()->json($sizes);
    }

    public function store(Request $request)
    {
        try {
            $request->validate(
                [
                    'name' => 'required|string|max:255',
                    'length' => 'required|numeric',
                    'width' => 'required|numeric',
                    'sleeve' => 'required|numeric',
                    'status' => 'required',
                ],
                [
                    'name.required' => 'Tên kích cỡ không được để trống',
                    'length.required' => 'Chiều dài không được để trống',
                    'width.required' => 'Chiều rộng không được để trống',
                    'sleeve.required' => 'Chiều dài tay không được để trống',
                    'status.required' => 'Trạng thái không được để trống',

                    'length.numeric' => 'Chiều dài phải là số',
                    'width.numeric' => 'Chiều rộng phải là số',
                    'sleeve.numeric' => 'Chiều dài tay phải là số',
                ]
            );

            $size = Size::create([
                'name' => $request->name,
                'length' => $request->length,
                'width' => $request->width,
                'sleeve' => $request->sleeve,
                'status' => $request->status,
            ]);

            return response()->json([
                'message' => 'Size created successfully',
                'data' => $size,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating size',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $size = Size::find($id);
        if (!$size) {
            return response()->json(['message' => 'Size not found'], 404);
        }
        return response()->json($size);
    }

    public function update(Request $request, $id)
    {
        $size = Size::find($id);
        if (!$size) {
            return response()->json(['message' => 'Size not found'], 404);
        }

        $request->validate(
            [
                'name' => 'required|string|max:255',
                'length' => 'required|numeric',
                'width' => 'required|numeric',
                'sleeve' => 'required|numeric',
                'status' => 'required',
            ],
            [
                'name.required' => 'Tên kích cỡ không được để trống',
                'length.required' => 'Chiều dài không được để trống',
                'width.required' => 'Chiều rộng không được để trống',
                'sleeve.required' => 'Chiều dài tay không được để trống',
                'status.required' => 'Trạng thái không được để trống',

                'length.numeric' => 'Chiều dài phải là số',
                'width.numeric' => 'Chiều rộng phải là số',
                'sleeve.numeric' => 'Chiều dài tay phải là số',
            ]
        );

        $size->update([
            'name' => $request->name,
            'description' => $request->description,
            'length' => $request->length,
            'width' => $request->width,
            'sleeve' => $request->sleeve,
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Size updated successfully',
            'data' => $size,
        ]);
    }

    public function destroy($id)
    {
        $size = Size::find($id);
        if (!$size) {
            return response()->json(['message' => 'Size not found'], 404);
        }

        $size->delete();

        return response()->json(['message' => 'Size deleted successfully']);
    }
}
