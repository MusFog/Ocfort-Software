<?php

namespace App\Http\Controllers;

use App\Interfaces\UserServiceInterface;
use App\DTOs\UserDTO;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct(
        protected UserServiceInterface $userService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'data' => $this->userService->list()
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);

        $dto = UserDTO::fromRequest($request->all());
        $this->userService->register($dto);

        return response()->json([
            'message' => 'User created successfully',
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        Validator::validate(
            ['id' => $id],
            ['id' => 'required|uuid|exists:users,id']
        ); 
        
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $this->userService->rename($id, $request->name);

        return response()->json([
            'message' => 'User updated successfully'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Validator::validate(
            ['id' => $id],
            ['id' => 'required|uuid|exists:users,id']
        ); 

        $this->userService->remove($id);

        return response()->json([
            'message' => 'User deleted successfully',
        ], 200);
    }
}
