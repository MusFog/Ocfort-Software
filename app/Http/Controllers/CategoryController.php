<?php

namespace App\Http\Controllers;

use App\Interfaces\CategoryServiceInterface;
use App\DTOs\CategoryDTO;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryServiceInterface $categoryService
    ) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'data' => $this->categoryService->list()
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

        $dto = CategoryDTO::fromRequest($request->all());
        $this->categoryService->create($dto);

        return response()->json([
            'message' => 'Category created successfully'
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        Validator::validate(
            ['id' => $id],
            ['id' => 'required|uuid|exists:categories,id']
        );

        $request->validate([
            'title' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id'
        ]);

        $dto = CategoryDTO::withId($request->all());
        $this->categoryService->update($id, $dto);

        return response()->json([
            'message' => 'Category updated successfully'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Validator::validate(
            ['id' => $id],
            ['id' => 'required|uuid|exists:categories,id']
        );

        $this->categoryService->delete($id);

        return response()->json([
            'message' => 'Category deleted successfully'
        ], 200);
    }
}
