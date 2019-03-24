<?php
namespace App\Http\Middleware;
use Closure;
use Exception;
use App\User;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use App\Http\Libraries\Helpers;
class JwtMiddleware
{
    public function handle($request, Closure $next, $guard = null)
    {
        $token = trim(substr($request->header('authorization'), 7));

        
        if(!$token) {
            // Unauthorized response if token not there
            return Helpers::error_reponse("Token not provided.", 401);
        }
        try {
            $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
        } catch(ExpiredException $e) {
            return Helpers::error_reponse("Provided token is expired.", 400);
        } catch(Exception $e) {
            return Helpers::error_reponse("An error while decoding token.", 400);
        }
        $user = User::find($credentials->sub);
        // Now let's put the user in the request class so that you can grab it from there
        $request->auth = $user;
        return $next($request);
    }
}