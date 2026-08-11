<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponseTrait;

    public function register(RegisterRequest $request)
    {
        $validatedData = $request->validated();
        $user = User::create($validatedData);
        $token = $user->createToken('authToken')->plainTextToken;

        return $this->successResponse(['user' => $user], 'User registered successfully', 201)
            ->withCookie(cookie('token', $token, 60 * 24));
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (!auth()->attempt($credentials)) {
            return $this->errorResponse('Invalid Credentials', 401);
        }

        $token = auth()->user()->createToken('authToken')->plainTextToken;

        return $this->successResponse(['user' => auth()->user()], 'Logged in successfully')
            ->withCookie(cookie('token', $token, 60 * 24));
    }

    public function getAuthenticatedUser()
    {
        return $this->successResponse(['user' => auth()->user()]);
    }

    public function logout(Request $request)
    {
        if (auth()->check()) {
            auth()->user()->tokens()->delete();
        }
        $cookie = cookie()->forget('token');

        return $this->successResponse(null, 'Logged out successfully')
            ->withoutCookie($cookie);
    }

    public function update(Request $request, string $id)
    {
        if ((int)$id !== auth()->id()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
        ]);

        $user = auth()->user();
        $user->update($validatedData);

        return $this->successResponse(['user' => $user->fresh()], 'User profile updated successfully');
    }
}
