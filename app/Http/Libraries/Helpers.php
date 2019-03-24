<?php
namespace App\Http\Libraries;

use App\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;

class Helpers
{

    /**
     * Create a new auth token.
     *
     * @param  \App\User   $user
     * @return string
     */
    public static function generate_token(User $user)
    {
        $payload = [
            'iss' => "lumen-jwt", // Issuer of the token
            'sub' => $user->id, // Subject of the token
            'iat' => time(), // Time when JWT was issued.
            'exp' => time() + 60 * 60, // Expiration time
        ];

        // we are passing `JWT_SECRET` as the second parameter that will
        // be used to decode the token in the future.
        return JWT::encode($payload, env('JWT_SECRET'));
    }

    /**
     * Generate an error response.
     *
     * @param  String   message // error message
     * @param  Integer  code  // error code
     * @param  Array  extra_info  // extra information
     * @return Illuminate\Http\Response
     */
    public static function error_reponse($message, $code, $extra_info = [])
    {
        return response()->json([
            'error' => $message,
            'extra' => $extra_info,
        ], $code);
    }

    /**
     * Generate an success response.
     *
     * @param  Mixed   data  // response data
     * @param  Integer  code // success code
     * @param  Boolean  with_token // generatre new token
     * @return Illuminate\Http\Response
     */
    public static function success_reponse($data, $code,$with_token =false)
    {   if($with_token){
          $request = app('request');
          $data['token']= Helpers::generate_token($request->auth);
        }
        return response()->json($data, $code);
    }
}
