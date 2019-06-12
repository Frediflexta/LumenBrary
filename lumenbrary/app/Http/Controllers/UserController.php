<?php

namespace App\Http\Controllers;

use App\User;
use Validator;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Firebase\JWT\ExpiredException;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
  /**
   * Resource used https://medium.com/tech-tajawal/jwt-authentication-for-lumen-5-6-2376fd38d454 or https://laracasts.com/discuss/channels/lumen/how-to-install-jwt-auth-in-lumen?page=1
   * Create a new token.
   *
   * @param  \App\User   $user
   * @return string
   */
  protected function jwt(User $user)
  {
    $payload = [
      'iss' => 'lumen-jwt', // issuer of token
      'sub' => [$user->id, $user->password, $user->email], // subject of the token
      'iat' => time(),
      'exp' => time() + 60*60
    ];

    /**
    * As you can see we are passing `JWT_SECRET` as the second parameter that will 
    * be used to decode the token in the future.
    */
    return JWT::encode($payload, env('JWT_SECRET'));
  }

  /**
   * Register user
   *
   * @param Request $request
   * @return void
   */
  public function register(Request $request)
  {
    $this->validate($request,[
      'name' => 'required',
      'email' => 'required|email|unique:users',
      'password' => 'required|min:8'
    ]);

    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password)
    ]);

    return response()->json([
      'status' => 'success',
      'message' => 'user was created successfully',
      'data' => [
        'name' => $user->name,
        'email' => $user->email,
        'token' => $this->jwt($user)
      ]
    ]);
  }

  /**
   * Login user
   *
   * @param Request $request
   * @return void
   */
  public function login(Request $request)
  {
    $this->validate($request, [
      'email' => 'required|email',
      'password' => 'required|min:8'
    ]);

    $user = User::where('email', $request->input('email'))->first();

    if(!$user) {
      return response()->json([
        'error' => 'Email does not exist'
      ], 400);
    }

    if(Hash::check($request->input('password'), $user->password)) {
      return response()->json([
        'status' => 'success',
        'message' => 'Login was successfully',
        'data' => [
          'name' => $user->name,
          'email' => $user->email,
          'token' => $this->jwt($user)
        ]
        ], 200);
    }

    return response()->json([
      'status' => 'faliure',
      'error' => 'Wrong password'
    ], 400);
  }

}