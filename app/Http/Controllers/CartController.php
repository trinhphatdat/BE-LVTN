<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Retrieve all carts
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
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|string',
        ]);

        // Create a new cart
        $cart = new Cart();
        $cart->user_id = $request->user_id;
        $cart->status = $request->status;
        $cart->save();

        return response()->json(['message' => 'Cart created successfully', 'cart' => $cart], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate the request
        $request->validate([
            'status' => 'required|string',
        ]);

        // Find the cart by ID
        $cart = Cart::findOrFail($id);

        // Update the cart status
        $cart->status = $request->status;
        $cart->save();

        return response()->json(['message' => 'Cart updated successfully', 'cart' => $cart], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the cart by ID
        $cart = Cart::findOrFail($id);

        // Delete the cart
        $cart->delete();

        return response()->json(['message' => 'Cart deleted successfully'], 200);
    }
}
