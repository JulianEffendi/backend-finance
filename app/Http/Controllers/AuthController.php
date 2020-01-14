<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Validator;
use App\Service\ApiResponse;
use Laravel\Passport\Passport;

class AuthController extends Controller
{
    /**
     * Auth Register
     *
     * @return \Illuminate\Http\Response
     */
    public function profile() {
        $user = Auth::user()->first(['id', 'name', 'email', 'username']);
        return ApiResponse::success($user);
    }

    /**
     * Auth login
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if ( $this->checkAuth() ) {
            $user = Auth::user();
            $user->update(['last_login_at' => date('Y-m-d H:i')]);

            // set expire time token
            if ($request->has('remember_me')) {
                Passport::personalAccessTokensExpireIn(now()->addDays(30));
            } else {
                Passport::personalAccessTokensExpireIn(now()->addHours(6));
            }

            return ApiResponse::success([
                'token' => $user->createToken('TINYToken')->accessToken,
                'name' => $user->name,
            ]);
        }

        return $this->unauthorized();
    }

    /**
     * Unauthorized error message
     *
     * @return \Illuminate\Http\Response
     */
    public function unauthorized()
    {
        return ApiResponse::error("Unauthorized", 401);
    }

    /**
     * Logout the user
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return ApiResponse::success(null, 'Logout berhasil.');
    }


    /**
     * Check login auth
     *
     * @return void
     */
    private function checkAuth()
    {
        $username = request('username');
        $user = User::where('username', $username)
            ->orWhere('email', $username)
            ->first();

        if (empty($user)) { return false; }

        if(filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $auth = Auth::attempt([
                'email' => $user->username,
                'password' => request('password')
            ]);
        } else {
            $auth = Auth::attempt([
                'username' => $user->username,
                'password' => request('password')
            ]);
        }

        return $auth;
    }
}
