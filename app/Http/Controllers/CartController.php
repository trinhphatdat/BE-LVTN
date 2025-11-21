<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $carts = Cart::all();

        return response()->json($carts, 200);
    }
    public function getCartByUser(Request $request)
    {
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->first();

        if ($cart) {
            return response()->json(['cart' => $cart], 200);
        } else {
            return response()->json(['cart' => null], 200);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|string',
        ]);

        $cart = new Cart();
        $cart->user_id = $request->user_id;
        $cart->status = $request->status;
        $cart->save();

        return response()->json(['message' => 'Cart created successfully', 'cart' => $cart], 201);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $cart = Cart::findOrFail($id);
        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }
        $cart->status = $request->status;
        $cart->save();

        return response()->json(['message' => 'Cart updated successfully', 'cart' => $cart], 200);
    }

    public function destroy(string $id)
    {
        $cart = Cart::findOrFail($id);

        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }
        $cart->delete();

        return response()->json(['message' => 'Cart deleted successfully'], 200);
    }
}
