<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class JwtMiddleware extends BaseMiddleware
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
      
      if($auth = $request->header('X-Authorization')) {
        if (\Str::startsWith($auth, 'Bearer ')) 
        {            
          $request->headers->set('Authorization', $auth);
        }
        else {
          $request->headers->set('Authorization', 'Bearer '.$auth);
        }
      }
      try {
        $user = JWTAuth::parseToken()->authenticate();
        $request['auth_user'] = $user;
      } catch (Exception $e) {
        if ($e instanceof TokenInvalidException){
          return response()->json(['success' => false, 'error'=>['code' => 401, 'message' => 'Token is Invalid']]);
        }else if ($e instanceof TokenExpiredException){
          return response()->json(['success' => false, 'error'=>['code' => 402, 'message' => 'Token is Expired']]);
        }else{
          return response()->json(['success' => false, 'error'=>['code' => 403, 'message' => 'Authorization Token not found']]);
        }
      }
      return $next($request);
    }
}
