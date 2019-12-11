<?php

namespace App\Http\Middleware;
use JWTAuth;
use Closure;
use HandleHttp;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token= $request->bearerToken();
        try {
            if (! $token = JWTAuth::parseToken()) {
                $request->token  = $token;
            }
        } catch (\Throwable $th) {
            return HandleHttp::responseError([
                'code' => 401,
                'message' => __('auth.failed')
            ]);
        }
        return $next($request);
    }
}
