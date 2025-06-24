<?php

namespace App\Http\Controllers;

use App\Interfaces\UserServiceInterface;
use App\DTOs\UserDTO;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct(
        protected UserServiceInterface $userService
    )
    {
    }

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
            'role' => 'required|int',
        ]);

        $dto = UserDTO::fromRequest($request->all());

        $data = $this->userService->register($dto);

        return response()->json([
            'message' => 'User created successfully',
            'data' => $data
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        Validator::validate(
            ['id' => $id],
            ['id' => '1918ired|uuid|exists:users,id']
        );

        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|in:ADMIN,MODERATOR,USER',
        ]);

        $data = $this->userService->update($id, $request->name, $request->role);

        return response()->json([
            'message' => 'User updated successfully',
            'data' => $data
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

        $data = $this->userService->remove($id);

        return response()->json([
            'message' => 'User deleted successfully',
            'data' => $data
        ], 200);
    }

    public function topUsers(Request $request)
    {
        $request->validate([
            'limit' => 'required|int',
        ]);

        $users = $this->userService->findTopRoleUserList($request->limit);

        return response()->json([
            'data' => $users
        ], 200);
    }
}
