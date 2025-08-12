<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VariantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $variant = Variant::findOrFail($id);

        $data = $request->validate([
            'sku' => 'sometimes|required|string|unique:variants,sku,' . $id,
            'stock' => 'sometimes|required|integer|min:0',
            'price' => 'sometimes|required|numeric|min:0',
            // add more fields if needed
        ]);

        $variant->update($data);

        // If attribute options need updating, handle here as well (optional)

        return response()->json(['message' => 'Variant updated', 'variant' => $variant]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $variant = Variant::findOrFail($id);
        $variant->delete();

        return response()->json(['message' => 'Variant deleted']);
    }
}
