<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();

        return response()->json([
            'categories' => $categories
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $category = Category::create([
            'id' => Str::uuid(),
            'title' => $request->title
        ]);

        if ($request->user_id) {
            $category->users()->attach($request->user_id);
        }

        return response()->json([
            'message' => 'Category created successfully',
            'category' => $category
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $category->update([
            'title' => $request->title
        ]);

        return response()->json([
            'message' => 'Category updated successfully',
            'category' => $category
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->users()->detach();
        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully',
        ], 200);
    }
}
