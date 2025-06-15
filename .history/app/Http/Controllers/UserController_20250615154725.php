<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Jobs\SendEmail;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();

        return response()->json([
            'users' => $users
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

        $user = User::create([
            'id' => Str::uuid(),
            'name' => $request->name,
            'email' => $request->email
        ]);

        SendEmail::dispatch($user->id, now()->addMinutes(1));

        return response()->json([
            'message' => 'User created successfully',
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email
        ]);

        return response()->json([
            'message' => 'User updated successfully',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->categories()->detach();
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ], 200);
    }
}
