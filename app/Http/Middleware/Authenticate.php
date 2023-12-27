<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Auth\Factory as Auth;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->header('Authorization')) {
            return response()->json([
                'status' => '401',
                'message' => 'Unauthorized',
                'errors' => null,
                'data' => null,
            ], 401);
        }

        else {
            $token = explode('t1MXlL', $request->header('Authorization'));
            if (count($token) != 2) {
                return response()->json([
                    'status' => '401',
                    'message' => 'Unauthorized',
                    'errors' => null,
                    'data' => null,
                ], 401);
            }
            try {
                $token[0] = Crypt::decryptString($token[0]);
            } catch (\Throwable $th) {
                return response()->json([
                    'status' => '401',
                    'message' => 'Unauthorized',
                    'errors' => null,
                    'data' => null,
                ], 401);
            }

            try {
                $token[1] = Crypt::decryptString($token[1]);
            } catch (\Throwable $th) {
                return response()->json([
                    'status' => '401',
                    'message' => 'Unauthorized',
                    'errors' => null,
                    'data' => null,
                ], 401);
            }

            if (time() > intval($token[1])) {
                return response()->json([
                    'status' => '401',
                    'message' => 'Unauthorized',
                    'errors' => null,
                    'data' => null,
                ], 401);
            }

            $user = User::where('username', $token[0])->first();

            if (!$user) {
                return response()->json([
                    'status' => '401',
                    'message' => 'Unauthorized',
                    'errors' => null,
                    'data' => null,
                ], 401);
            }

        }
        
        $request->request->add(['userid' => $user->user_id]);
        return $next($request);
    }
}
