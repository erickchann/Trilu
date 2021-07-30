<?php

namespace App\Http\Controllers;

use App\Models\LoginToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'first_name' => 'required|alpha|between:2,20',
            'last_name' => 'required|alpha|between:2,20',
            'username' => 'required|between:5,12|unique:users,username|regex:/^[a-z0-9.-]*$/',
            'password' => 'required|between:5,12'
        ]);

        if ($validate->fails()) {
            return response()->json(['message' => 'invalid field'], 422);
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'username' => $request->username,
            'password' => bcrypt($request->password)
        ]);

        $token = bcrypt($user->id);

        return response()->json(['token' => $token], 200);
    }

    public function login(Request $request)
    {
        if (Auth::attempt($request->only(['username', 'password']))) {
            $logged = LoginToken::where('user_id', Auth::id())->first();
            $token = bcrypt(Auth::id());

            if (!$logged) {
                LoginToken::create(['user_id' => Auth::id(), 'token' => $token]);
            } else {
                $logged->update(['token' => $token]);
            }

            return response()->json(['token' => $token], 200);
        }
        return response()->json(['message' => 'invalid login'], 401);
    }

    public function logout(Request $request) {
        $token = LoginToken::where('token', $request->token)->first();

        if (!$token) {
            return response()->json(['message' => 'unauthorized user'], 401);
        }

        $token->delete();

        return response()->json(['message' => 'logout success'], 200);
    }
}
