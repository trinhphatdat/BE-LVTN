<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Color;
use Illuminate\Validation\ValidationException;

class ColorController extends Controller
{
    public function index()
    {
        $colors = Color::all();
        return response()->json($colors);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'hex_code' => 'required|string|size:7',
                'status' => 'required',
            ]);

            $color = Color::create([
                'name' => $request->name,
                'hex_code' => $request->hex_code,
                'status' => $request->status,
            ]);

            return response()->json(['message' => 'Color created successfully', 'color' => $color], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating color',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $color = Color::find($id);
        if (!$color) {
            return response()->json(['message' => 'Color not found'], 404);
        }
        return response()->json($color);
    }

    public function update(Request $request, $id)
    {
        try {
            $color = Color::find($id);
            if (!$color) {
                return response()->json(['message' => 'Color not found'], 404);
            }

            $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'hex_code' => 'sometimes|required|string|size:7',
                'status' => 'required',
            ]);

            $color->update($request->only(['name', 'hex_code']));

            return response()->json(['message' => 'Color updated successfully', 'color' => $color]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating color',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        $color = Color::find($id);
        if (!$color) {
            return response()->json(['message' => 'Color not found'], 404);
        }

        $color->delete();
        return response()->json(['message' => 'Color deleted successfully']);
    }
}
