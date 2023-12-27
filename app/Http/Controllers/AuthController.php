<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;

class AuthController extends Controller
{
    public function __construct()
    {
    }

    private function createToken($user)
    {
        $token = Crypt::encryptString($user->username);
        $max_time = time() + (60 * 60 * 24 * 15);
        $max_time = Crypt::encryptString($max_time);
        $token = $token . 't1MXlL' . $max_time;
        return $token;
    }

    public function checkToken($token)
    {
        $token = explode('t1MXlL', $token);
        if (count($token) != 2)
            return false;

        try {
            $token[0] = Crypt::decryptString($token[0]);
        } catch (\Throwable $th) {
            return false;
        }

        try {
            $token[1] = Crypt::decryptString($token[1]);
        } catch (\Throwable $th) {
            return false;
        }

        if (time() > intval($token[1]))
            return false;

        $user = User::where('username', $token[0])->first();

        if (!$user)
            return false;

        return $user;
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username'  => 'required|string',
            'password'  => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => '400',
                'message'   => 'Validation Error',
                'errors'    => $validator->errors(),
                'data'      => null,
            ], 400);
        }

        $user = User::where('username', $request->username)->first();
        if ($user && password_verify($request->password, $user->password)) {
            $token = $this->createToken($user);
            $user['api_token'] = $token;
            return response()->json([
                'status'    => '200',
                'message'   => 'Login Success',
                'errors'    => null,
                'data'      => [
                    'user_id'  => $user->user_id,
                    'name'     => $user->name,
                    'username' => $user->username,
                    'email'    => $user->email,
                    'role'     => $user->role,
                    'api_token' => $token,
                ],
            ], 200);
        } else {
            return response()->json([
                'status'    => '401',
                'message'   => 'Login Failed',
                'errors'    => 'Username or Password is Wrong',
                'data'      => null,
            ], 401);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string',
            'email'     => 'required|string|email|unique:users',
            'username'  => 'required|string|unique:users',
            'password'  => 'required|string',
            'role'      => 'required|string|in:teacher,student'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => '400',
                'message'   => 'Validation Error',
                'errors'    => $validator->errors(),
                'data'      => null,
            ], 400);
        }


        $user = User::create([
            'name'      => $request->name,
            'user_id'   => $request->role == 'teacher' ? 'GR' . rand(1000, 9999)  : 'SW' . rand(1000, 9999),
            'email'     => $request->email,
            'username'  => $request->username,
            'password'  => app('hash')->driver('bcrypt')->make($request->password),
            'role'      => $request->role,
        ]);

        $token = $this->createToken($user);
        $user['api_token'] = $token;

        return response()->json([
            'status'    => '200',
            'message'   => 'Register Success',
            'errors'    => null,
            'data'      => [
                'user_id'  => $user->user_id,
                'name'     => $user->name,
                'username' => $user->username,
                'email'    => $user->email,
                'role'     => $user->role,
                'api_token' => $token,
            ],
        ], 200);
    }

    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'api_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => '400',
                'message'   => 'Validation Error',
                'errors'    => $validator->errors(),
                'data'      => null,
            ], 400);
        }

        $isActive = $this->checkToken($request->api_token);
        if ($isActive) {
            $user = $isActive;
            return response()->json([
                'status'    => '200',
                'message'   => 'Token is Active',
                'errors'    => null,
                'data'      => [
                    'user_id'  => $user->user_id,
                    'name'     => $user->name,
                    'username' => $user->username,
                    'email'    => $user->email,
                    'role'     => $user->role,
                    'api_token' => $request->api_token,
                ]
            ], 200);
        } else {
            return response()->json([
                'status'    => '401',
                'message'   => 'Token is Inactive',
                'errors'    => null,
                'data'      => null,
            ], 401);
        }
    }

    public function data()
    {
        return 'data';
    }
}
