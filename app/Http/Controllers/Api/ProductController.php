<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::select('id', 'title', 'price', 'discount_price', 'status')
            ->where('status', 'available')
            ->paginate(10);

        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'user_id' => 'required|exists:users,id', // vendor
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'status' => 'required|in:available,out_of_stock,draft',

            // variants array (optional)
            'variants' => 'array',
            'variants.*.name' => 'string|max:255',
            'variants.*.sku' => 'string|unique:variants,sku',
            'variants.*.stock' => 'integer|min:0',
            'variants.*.price' => 'numeric|min:0',

            // attribute option ids for each variant
            'variants.*.attribute_option_ids' => 'array',
            'variants.*.attribute_option_ids.*' => 'exists:attribute_options,id',

            // Add validation for images here
            'images' => 'array',
            'images.*' => 'image|max:2048', // max 2MB per image
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        // Create the product
        $product = Product::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'user_id' => $data['user_id'],
            'category_id' => $data['category_id'],
            'price' => $data['price'],
            'discount_price' => $data['discount_price'] ?? null,
            'status' => $data['status'],
        ]);

        // Handle image uploads if present
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public'); // store in storage/app/public/products
                // Save image record linked to product
                $product->images()->create(['image_path' => $path]);
            }
        }

        // Create variants and link attribute options
        if (!empty($data['variants'])) {
            // return response()->json(['message' => 'Variants creation is not implemented yet', 'data' => $data['variants']], 501);
            foreach ($data['variants'] as $variantData) {
                $variant = $product->variants()->create([
                    'name' => $variantData['name'],
                    'sku' => $variantData['sku'],
                    'stock' => $variantData['stock'],
                    'price' => $variantData['price'],
                ]);
                $variant->attributeOptions()->attach($variantData['attribute_option_ids']);
            }
        }

        return response()->json(['message' => 'Product created', 'product_id' => $product->id], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with([
            'category:id,name',
            'vendor:id,name',
            'images:id,product_id,url',
            'variants.attributeOptions.attribute'
        ])->findOrFail($id);

        // Build attributes array (unique attribute names with all possible values)
        $attributes = [];

        foreach ($product->variants as $variant) {
            foreach ($variant->attributeOptions as $option) {
                $attrName = $option->attribute->name;
                if (!isset($attributes[$attrName])) {
                    $attributes[$attrName] = [];
                }
                if (!in_array($option->value, $attributes[$attrName])) {
                    $attributes[$attrName][] = $option->value;
                }
            }
        }

        // Format variants with attribute values
        $variants = $product->variants->map(function ($variant) {
            $attributeValues = [];
            foreach ($variant->attributeOptions as $option) {
                $attributeValues[$option->attribute->name] = $option->value;
            }

            return [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'stock' => $variant->stock,
                'price' => $variant->price,
                'attribute_values' => $attributeValues,
            ];
        });

        return response()->json([
            'id' => $product->id,
            'title' => $product->title,
            'description' => $product->description,
            'price' => (float) $product->price,
            'discount_price' => $product->discount_price !== null ? (float) $product->discount_price : null,
            'status' => $product->status,
            'category' => [
                'id' => $product->category->id,
                'name' => $product->category->name,
            ],
            'vendor' => [
                'id' => $product->vendor->id,
                'name' => $product->vendor->name,
            ],
            'images' => $product->images->pluck('image_path')->toArray(),
            'attributes' => collect($attributes)->map(function ($values, $name) {
                return [
                    'name' => $name,
                    'values' => $values,
                ];
            })->values(),
            'variants' => $variants,
            // Optional static shipping and policy info
            'shipping' => [
                'to' => 'Ethiopia',
                'estimated_delivery' => '3-5 business days',
            ],
            'policy' => [
                'return' => '7-day return policy',
                'refund' => 'Refund after inspection',
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'exists:categories,id',
            'price' => 'numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'status' => 'in:available,out_of_stock,draft',

            // Add validation for images
            'image' => 'image',
            'images' => 'file',
            'images.*' => 'image|max:2048',

            // For variants update, you can extend this as needed
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $data = $validator->validated();
        $product->update($data);


        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $product->images()->create(['image_path' => $path]);
            }
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $path = $image->store('products', 'public');
            $product->images()->create(['image_path' => $path]);
        }

        return response()->json(['message' => 'Product updated']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::with(['images', 'variants.attributeOptions'])->findOrFail($id);

        // Delete product images from storage and DB
        foreach ($product->images as $image) {
            // Delete image file from storage disk 'public'
            \Storage::disk('public')->delete($image->url);
            $image->delete();
        }

        // Detach attribute options from variants, then delete variants
        foreach ($product->variants as $variant) {
            $variant->attributeOptions()->detach();
            $variant->delete();
        }

        // Finally delete the product
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
