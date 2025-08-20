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
        // Fetch the cart for the authenticated user with order status false
        // and include related items, variants, and products.
        $cart = Cart::with('items.variant.product')
            ->where('user_id', Auth::id())
            ->where('order_status', false)
            ->first();

        // If no cart is found, return a 404 response.
        if (!$cart) {
            return response()->json(['message' => 'No active cart found'], 404);
        }

        // Return the cart with its items.
        return response()->json($cart, 200);
    }

    /**
     * Display cart history (Ordered carts)
     */
    public function cartHistory() {
        // Fetch all carts for the authenticated user with order status true
        $carts = Cart::with('items.variant.product')
            ->where('user_id', Auth::id())
            ->where('order_status', true)
            ->get();

        // If no carts are found, return a 404 response.
        if ($carts->isEmpty()) {
            return response()->json(['message' => 'No cart history found'], 404);
        }

        // Return the cart history with its items.
        return response()->json($carts, 200);
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

        $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);

        $cartItem = CartItem::updateOrCreate(
            [
                'cart_id' => $cart->id,
                'variant_id' => $request->variant_id
            ],
            [
                'quantity' => $request->quantity
            ]
        );

        return response()->json($cartItem, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [ 'quantity' => 'required|integer|min:1', ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $cartItem = CartItem::find($id);
        if (!$cartItem) {
            return response()->json(['message' => 'Cart item not found'], 404);
        }

        $cartItem->update([ 'quantity' => $request->quantity ]);
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

    /**
     * Checkout the cart for the authenticated user.
     */
    public function checkout($cartId)
    {
        // Fetch the cart for the authenticated user with order status false and cartID
        $cart = Cart::where('id', $cartId)
            ->where('user_id', Auth::id())
            ->where('order_status', false)
            ->first();

        if (!$cart) {
            return response()->json(['message' => 'Active cart not found'], 404);
        }

        // Proceed with the checkout process (e.g., payment processing)
        // ...
        // Update the cart status to ordered
        $cart->update(['order_status' => true]);

        return response()->json([
            'message' => 'Checkout successful',
            'cart' => $cart
        ], 200);
    }
}
