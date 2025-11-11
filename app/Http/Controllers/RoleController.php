<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Validation\ValidationException;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return response()->json($roles);
    }

    public function store(Request $request)
    {
        try {
            $request->validate(
                [
                    'name' => 'required|string|max:255',
                    'status' => 'required',
                ],
                [
                    'name.required' => 'Tên vai trò không được để trống',
                    'status.required' => 'Tình trạng vai trò không được để trống',
                ]
            );

            $role = Role::create([
                'name' => $request->name,
                'status' => $request->status,
            ]);

            return response()->json([
                'message' => 'Role created successfully',
                'data' => $role,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating role',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(string $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json(['message' => 'Role không tồn tại'], 404);
        }
        return response()->json($role);
    }

    public function update(Request $request, string $id)
    {
        try {
            $role = Role::find($id);
            if (!$role) {
                return response()->json(['message' => 'Role không tồn tại'], 404);
            }

            $request->validate([
                'name' => 'required|string|max:255',
                'status' => 'required',
            ]);

            $role->update([
                'name' => $request->name,
                'status' => $request->status,
            ]);

            return response()->json([
                'message' => 'Role updated successfully',
                'data' => $role,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating role',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json(['message' => 'Role không tồn tại'], 404);
        }

        $role->status = 0;
        $role->save();

        // $role->delete();
        return response()->json(['message' => 'Xoá role thành công']);
    }
}
