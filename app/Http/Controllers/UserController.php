<?php
namespace App\Http\Controllers;

use App\Http\Libraries\Helpers;
use App\User;
use App\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Routing\Controller;
use \Exception;

class UserController extends Controller
{

    /**
     * Authenticate a user and return the token if the provided credentials are correct.
     *
     * @param  Illuminate\Http\Request   $request
     * @return Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        // validating user input
        try {
            $this->validate($request, [
                'email' => 'required|email|max:191',
                'password' => 'required|max:20|min:5',
            ]);
        } catch (ValidationException $ex) {
            // return data is not valid
            return Helpers::error_reponse("Data is not valid.", 400, $ex->errors());
        }

        // Find the user by email
        $user = User::where('email', $request->input('email'))->first();
        if (!$user) {
            // return user not found
            return Helpers::error_reponse("User is not found.", 400);
        }

        // Verify the password and generate the token
        if (Hash::check($request->input('password'), $user->password)) {

            // get application settings to be stored on user device
            $settings = Settings::where("id", 1)->first();

            // return generatred token
            return Helpers::success_reponse([
                'settings' => $settings,
                'token' => Helpers::generate_token($user),
            ], 200);
        }

        // Bad Request response
        return Helpers::error_reponse("Email or password is wrong.", 400);
    }
    /**
     * Create a user and return the token if the provided credentials are correct.
     *
     * @param  Illuminate\Http\Request   $request
     * @return Illuminate\Http\Response
     */
    public function register(Request $request)
    {

        // validating user input
        try {
            $this->validate($request, [
                'email' => 'required|email|max:191',
                'password' => 'required|confirmed|max:20|min:5',
                'name' => 'required|max:191',
            ]);
        } catch (ValidationException $ex) {
            // return data is not valid
            return Helpers::error_reponse("Data is not valid.", 400, $ex->errors());
        }

        try {
            $name = $request->input('name');
            $email = $request->input('email');
            $password = password_hash($request->input('password'), PASSWORD_DEFAULT);
            $user = User::create(['name' => $name, 'email' => $email, 'password' => $password]);
            // user was created and generate token to login
            return Helpers::success_reponse([
                'token' => Helpers::generate_token($user),
                'message' => "Account was created.",
            ], 200);

        } catch (Exception $ex) {
            return Helpers::error_reponse("User already exsists.", 400);
        }

    }

    /**
     * Retrive a user account based on token
     *
     * @param  Illuminate\Http\Request   $request
     * @return Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        // return the current user profile + updated token
        return Helpers::success_reponse(['user' => $request->auth], 200, true);
    }

    /**
     * Update a user account based on token
     *
     * @param  Illuminate\Http\Request   $request
     * @return Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // validating user input
        try {
            $this->validate($request, [
                'email' => 'required|email|max:191',
                'name' => 'required|max:191',
            ]);
        } catch (ValidationException $ex) {
            // return data is not valid
            return Helpers::error_reponse("Data is not valid.", 400, $ex->errors());
        }

        try {
            $name = $request->input('name');
            $email = $request->input('email');
            $user = User::where('id', $request->auth->id)->update(['name' => $name, 'email' => $email]);
            // user was updated + refresh token
            return Helpers::success_reponse([
                'message' => "Account was updated.",
            ], 200, true);

        } catch (Exception $ex) {
            dd($ex);
            return Helpers::error_reponse("Something went worong, try again later.", 400);
        }
    }

    /**
     * Update a user password based on token
     *
     * @param  Illuminate\Http\Request   $request
     * @return Illuminate\Http\Response
     */
    public function updatePassword(Request $request)
    {
        // validating user input
        try {
            $this->validate($request, [
                'password' => 'required|confirmed|max:20|min:5',
            ]);
        } catch (ValidationException $ex) {
            // return data is not valid
            return Helpers::error_reponse("Data is not valid.", 400, $ex->errors());
        }

        try {
            $password = password_hash($request->input('password'), PASSWORD_DEFAULT);
            $user = User::where('id', $request->auth->id)->update(['password' => $password]);
            // user was updated + refresh token
            return Helpers::success_reponse([
                'message' => "Password was updated.",
            ], 200, true);

        } catch (Exception $ex) {
            dd($ex);
            return Helpers::error_reponse("Something went worong, try again later.", 400);
        }
    }

    /**
     * delete a user account based on token
     *
     * @param  Illuminate\Http\Request   $request
     * @return Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try {
            $user = User::where('id', $request->auth->id)->delete();
            // user was updated + refresh token
            return Helpers::success_reponse([
                'message' => "Account was deleted.",
            ], 200);

        } catch (Exception $ex) {
            dd($ex);
            return Helpers::error_reponse("Something went worong, try again later.", 400);
        }
    }
}
