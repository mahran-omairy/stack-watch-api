<?php
namespace App\Http\Controllers;

use App\Http\Libraries\Helpers;
use App\Settings;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;
use \Exception;

class SettingsController extends Controller
{
    /**
     * get application settings.
     *
     * @param  Illuminate\Http\Request   $request
     * @return Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        try {

            $settings = Settings::where("id", 1)->first();
            return Helpers::success_reponse([
                'settings' => $settings,
            ], 200, false);

        } catch (Exception $ex) {
            return Helpers::error_reponse("Something went worong", 400);
        }
    }
}
