<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartItemController extends Controller
{
    public function index() {}

    public function store(Request $request) {}

    public function show(string $cart_id) {}

    public function increment(string $id) {}

    public function decrement(string $id) {}

    public function update(Request $request, string $id) {}

    public function destroy(string $id) {}
}
