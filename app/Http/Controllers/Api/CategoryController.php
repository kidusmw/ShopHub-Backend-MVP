<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all categories with their children
        $categories = Category::with('children')->get();

        // Return the categories as a JSON response
        return response()->json($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        // If validation fails, return errors
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Create the category
        $category = Category::create($validator->validated());

        // Return the created category
        return response()->json([
            'message' => 'Category created successfully',
            'category' => $category
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Find the category by ID
        $category = Category::with('children', 'parent')->findOrFail($id);

        // Return the category as a JSON response
        return response()->json($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Find the category by ID
        $category = Category::findOrFail($id);

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        // If validation fails, return errors
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Update the category
        $category->update($validator->validated());

        // Return the updated category
        return response()->json([
            'message' => 'Category updated successfully',
            'category' => $category
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the category by ID
        $category = Category::with('children')->findOrFail($id);

        // TODO: Optionally: prevent deleting if it has children

        // Delete the category
        $category->delete();

        // Return a success message
        return response()->json(['message' => 'Category deleted successfully']);
    }
}
