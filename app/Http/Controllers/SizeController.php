<?php

namespace App\Http\Controllers;

use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SizeController extends Controller
{
    public function index()
    {
        $sizes = Size::all();
        return response()->json($sizes);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'length' => 'required|number',
                'width' => 'required|number',
                'sleeve' => 'required|number',
                'status' => 'required',
            ]);

            $size = Size::create([
                'name' => $request->name,
                'description' => $request->description,
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

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'length' => 'required|number',
            'width' => 'required|number',
            'sleeve' => 'required|number',
            'status' => 'required',
        ]);

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
