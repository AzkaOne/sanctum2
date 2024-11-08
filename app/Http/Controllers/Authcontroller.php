<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class Authcontroller extends Controller
{
    public function Register(Request $request){
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
             ]);

        try{
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => bcrypt($validatedData['password']),
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'acces_token' => $token,
                'token_type' => 'Bearer',
            ]);
        }catch (\Exception $e) {
            return response()->json(['error' => 'Register gagal, silahkan  coba kembali.'], 500);
        }
    }

    public function Login(Request $request){
        if(!Auth::attempt($request->only('email','password'))){
            return response()->json([
                'message' => 'Imvalid Login Details'
            ],401);
        }
        try{
            $user = User::where('email', $request['email'])->firstorFail();
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        } catch (\Exception $e){
            return response()->json([
                'error' => 'Login gagal, Silahkan coba  kemvbali'
            ], 500); 
        }
    }
}
