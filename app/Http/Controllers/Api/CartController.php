<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


use App\Models\Cart;
use App\Models\CartItem;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cart  = Cart::with('items.variant.product')
            ->where('user_id', Auth::id())
            ->first();

        return response()->json($cart, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'variant_id' => 'required|exists:variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

        $cartItem = CartItem::updateOrCreate(
            // Create or update the cart item
            [
                'cart_id' => $cart->id,
                'variant_id' => $request->variant_id
            ],
            [
                'quantity' => $request->quantity // Update quantity if item already exists
            ]
        );

        return response()->json($cartItem, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CartItem $cartItem)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $cartItem->update([
            'quantity' => $request->quantity
        ]);

        return response()->json($cartItem, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CartItem $cartItem)
    {
        $cartItem->delete();

        return response()->json(['message' => 'Cart item removed successfully'], 200);
    }

    /**
     * Clear the cart for the authenticated user.
     */
    public function clear()
    {
        $cart = Cart::where('user_id', Auth::id())->first();

        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        $cart->items()->delete();

        return response()->json(['message' => 'Cart cleared successfully'], 200);
    }
}
