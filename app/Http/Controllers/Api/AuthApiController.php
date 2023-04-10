<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthApiController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard('api');
    }

    /**
     * Get a JWT token via given credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if ($token = $this->guard()->attempt($credentials)) {
            return $this->respondWithToken($token);
        }

        return response()->json(['error' => 'Invalid Login Details'], 401);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {

        return response()->json($this->guard()->user(), 200);
    }

    /**
     * Verify Token User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify_token(Request $request)
    {

        // JWTAuth::parseToken()->authenticate()
        // $checkuser = $this->guard()->check();
        try {
            if (! $token = JWTAuth::parseToken()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.'
                ], 401);
            }

            return $this->respondWithToken($request->token);
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json([
                    'success' => false,
                    'message' => 'token_invalid.'
                ], $e->getStatusCode());
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return response()->json([
                    'success' => false,
                    'message' => 'token_expired.'
                ], $e->getStatusCode());
            } else if ( $e instanceof \Tymon\JWTAuth\Exceptions\JWTException) {
                return response()->json([
                    'success' => false,
                    'message' => 'token_absent.'
                ], $e->getStatusCode());
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.'
                ], 500);
            }
        }

        // if (!$checkuser = JWTAuth::authenticate()) {
        //         return response()->json([
        //             'success' => false,
        //             'message' => 'Unauthenticated.'
        //         ], 401);
        // }

        // return $this->respondWithToken($request->token);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->guard()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'success' => true,
            'message' => 'Login Berhasil',
            'errors' => '',
            'id' => $this->guard()->user()->id,
            'id_toko' => $this->guard()->user()->id_toko,
            'name' => $this->guard()->user()->name,
            'email' => $this->guard()->user()->email,
            'hp' => $this->guard()->user()->hp,
            'role' => $this->guard()->user()->role,
            'email_verified_at' => $this->guard()->user()->email_verified_at,
            'created_at' => $this->guard()->user()->created_at,
            'updated_at' => $this->guard()->user()->updated_at,
            'token' => $token,
            'token_type' => 'bearer',
            // 'expires_in' => \Carbon\Carbon::now()->addMinutes(config('jwt.ttl'))->format('d-m-Y H:i:s')
        ]);
    }
}
