<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class UserController extends Controller
{
    public function index() {
        $users = User::all();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }
    public function login(Request $request){

        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required',
        ]);

        if(!\Auth::attempt($request->only('email', 'password'))){
            return response()->json([
                'success' => false,
                'message' => 'Email o contraseña incorrectos.',
            ],401);
        }
       $user = \Auth::user();

       if(!$user->active) {
          return response()->json([
              'success' => false,
              'message' => 'Tu usuario se encuentra desactivado.',
          ],401);
       }

    $token = $user->createToken('token')->plainTextToken;

       return response()->json([
           'success' => true,
           'data' => [
               'token' => $token,
               'user' => $user
           ]
       ]);
}
public function logout(Request $request){

        $request->user()->currentAccessToken()->delete();

    return response()->json([
        'success' => true
    ]);
  }
  public function show(Request $request){

        $user = $request->user();

        if(!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }
            return response()->json([
               'success' => true,
               'data' => $user,
            ]);
        }
}
