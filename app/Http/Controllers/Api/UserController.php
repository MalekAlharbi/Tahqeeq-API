<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function register(Request $request)
    {
        //
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

        $user = User::create($validatedData);
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'user' => $user,
        ])->withCookie(cookie('token', $token, 60 * 24));
    }
    public function login(Request $request)
    {
        //
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!auth()->attempt($validatedData)) {
            return response()->json([
                'message' => 'Invalid Credentials'
            ],401);
        }
        $token = auth()->user()->createToken('authToken')->plainTextToken;
        return response()->json([
            'user' => auth()->user(),
        ])->withCookie(cookie('token', $token, 60 * 24));
    }

    public function getAuthenticatedUser()
    {
        return response()->json(auth()->user());
    }

    public function logout(Request $request){
        if (auth()->check()) {
            auth()->user()->tokens()->delete();
        }
        $cookie = cookie()->forget('token');
        return response()->json([
            'message' => 'Logged out'
        ])->withoutCookie($cookie);
    }

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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
