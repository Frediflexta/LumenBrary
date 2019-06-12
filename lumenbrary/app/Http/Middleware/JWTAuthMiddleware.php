<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use App\User;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;

class JWTAuthMiddleware
{
  /**
   * Handle token checks when handling protected routes
   *
   * @param [type] $request
   * @param Closure $next
   * @param [type] $guard
   * @return void
   */
  public function handle($request, Closure $next, $guard = null)
  {
    $token = $request->header('token');

    if(!$token) {
      // Unauthorized response if token not there
      return response()->json([
        'status' => 'failure',
        'message' => 'Please provide a token'
      ], 401);
    }

    try {
      $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
    } catch(ExpiredException $error) {
      return response()->json([
        'status' => 'failure',
        'message' => 'The token provided is expired',
      ], 400);
    } catch(Exception $error) {
      return response()->json([
        'status' => 'failure',
        'message' => 'An error occured while decoding the token, please try again later',
      ], 400);
    }
    // Now let's check if the user is a valid one
    $user = User::where('email', $credentials->sub[1])->first();
    $request->auth = $user;

    return $next($request);
  }
}