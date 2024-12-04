<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->api([], 1, $validator->errors()->first(), 400);
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {

            $user = Auth::user();
            $data['user'] = new UserResource($user);
            $data['token'] = $user->createToken('my-app-token')->plainTextToken;

            return response()->api($data);
        } else {

            return response()->api([], 1, __('auth.failed'), 401);
        }
    }

    public function register(StoreUserRequest $request)
    {
        $validatedData = $request->validated();

        $validatedData['password'] = Hash::make($validatedData['password']);
        $user = User::create($validatedData);

        $user->assignRole($request->role);
        $data = [
            'user' => new UserResource($user),
            'token' => $user->createToken('my-app-token')->plainTextToken,
        ];

        return response()->api($data);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validatedData = $request->validated();
        $validatedData['password'] = Hash::make($request->password);
        $user->update($validatedData);

        return response()->api([
            "user" => new UserResource($user)
        ]);
    }

    public function logout()
    {
        $user = Auth::user();
        $user->tokens()->delete();
        return response()->api();
    }
}
