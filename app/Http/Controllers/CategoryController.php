<?php
namespace App\Http\Controllers;

use App\Category;
use App\Http\Libraries\Helpers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Routing\Controller;
use \Exception;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Retrive category based on category id and loged in user id.
     *
     * @param  Illuminate\Http\Request   $request
     * @return Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $year = $request->input("year") ?? date("Y");
        $month = $request->input("month") ?? date("m");

        try {
            // load category with it's envelops
            $category = Category::with([
                'envelops' => function($query) use ($year,$month){
                    $query->whereYear('envelops.created_at' ,$year ) 
                        ->whereMonth('envelops.created_at' ,$month);
                }
            ])->where("id", $request->route('id'))
            ->first()->toArray();

            return Helpers::success_reponse([
                'category' => $category,
                //'envelops'=> $category->envelops
            ], 200, true);
        } catch (Exception $ex) {
            dd($ex);
            return Helpers::error_reponse("Category does not exsist.", 400);
        }
    }

    /**
     * Create new category for current user
     *
     * @param  Illuminate\Http\Request   $request
     * @return Illuminate\Http\Response
     */

    public function create(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => 'required|max:191',
                'icon' => 'required|max:20',
            ]);
        } catch (ValidationException $ex) {
            // return data is not valid
            return Helpers::error_reponse("Data is not valid.", 400, $ex->errors());
        }

        try {
            $name = $request->input('name');
            $icon = $request->input('icon');
            $cateogy = Category::create(['name' => $name, 'icon' => $icon, 'user_id' => $request->auth->id]);
            // cateogry was created and generate new token
            return Helpers::success_reponse([
                'message' => "Category was created.",
            ], 200, true);

        } catch (Exception $ex) {
            return Helpers::error_reponse("Category could not be creared.", 400);
        }
    }

    /**
     * Update a category based on id and user token
     *
     * @param  Illuminate\Http\Request   $request
     * @return Illuminate\Http\Response
     */

    public function update(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => 'required|max:191',
                'icon' => 'required|max:20',
            ]);
        } catch (ValidationException $ex) {
            // return data is not valid
            return Helpers::error_reponse("Data is not valid.", 400, $ex->errors());
        }

        try {
            $id = $request->route('id');
            $name = $request->input('name');
            $icon = $request->input('icon');

            $category = Category::where("id", $id)
                ->update([
                    "name" => $name,
                    "icon" => $icon,
                ]);

            // cateogry was updated and generate new token
            if ($category) {
                return Helpers::success_reponse([
                    'message' => "Category was updated.",
                ], 200, true);
            } else {
                throw new Exception;
            }

        } catch (Exception $ex) {
            return Helpers::error_reponse("Category could not be updated.", 400);
        }
    }

    /**
     * Delete a category based on id and user token
     *
     * @param  Illuminate\Http\Request   $request
     * @return Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {

        try {
            $category = Category::where('id', $request->route("id"))->delete();

            // category was deleted + refresh token
            if ($category) {
                return Helpers::success_reponse([
                    'message' => "Category was deleted.",
                ], 200, true);
            } else {
                throw new Exception;
            }

        } catch (Exception $ex) {
         
            return Helpers::error_reponse("Something went worong, try again later.", 400);
        }
    }

    /**
     * List all cagtegories for user
     *
     * @param  Illuminate\Http\Request   $request
     * @return Illuminate\Http\Response
     */
    function list(Request $request) {
        try {
            $categories = Category::where("user_id", $request->auth->id)->get();

            // category was deleted + refresh token
            if ($categories) {
                return Helpers::success_reponse([
                    'categories' => $categories,
                ], 200, true);
            } else {
                throw new Exception;
            }

        } catch (Exception $ex) {
      
            return Helpers::error_reponse("Something went worong, try again later.", 400);
        }
    }


         /**
     * List all months for category
     * 
     * @param  Illuminate\Http\Request   $request
     * @return Illuminate\Http\Response
     */
    function listMonth(Request $request) {
       
        try {
            $months = Category::select(DB::raw('YEAR(`created_at`) as `cYear` , MONTH(`created_at`) as `cMonth`'))
            ->where("user_id", $request->auth->id)
            ->groupBy('cYear','cMonth')->get();
             return Helpers::success_reponse([
                    'months' => $months,
                ], 200, false);
            

        } catch (Exception $ex) {
            dd( $ex);
            return Helpers::error_reponse("Something went worong, try again later.", 400);
        }
}
    
}
