<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

class JwtMiddlewareDev
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response
  {
    // Set the database connection to 'mysql2'
    DB::setDefaultConnection('mysql2');

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
