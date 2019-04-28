<?php
namespace App\Http\Controllers;

use App\Envelop;
use App\Http\Libraries\Helpers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Routing\Controller;
use \Exception;
use Illuminate\Support\Facades\DB;

class EnvelopController extends Controller
{

    /**
     * Create a new envelop for a spicific category.
     *
     * @param  Illuminate\Http\Request   $request
     * @return Illuminate\Http\Response
     */ 
    public function create(Request $request)
    {
        try {
            $this->validate($request, [
                'category_id' => 'required|integer',
                'name' => 'required|max:191',
                'icon' => 'required|max:30',
                'amount' => 'required|numeric',
                'type' => 'required',
                'created_at' => 'required|date',
            ]);
        } catch (ValidationException $ex) {
            // return data is not valid
            return Helpers::error_reponse("Data is not valid.", 400, $ex->errors());
        }

        try {
            $category_id = $request->input('category_id');
            $name = $request->input('name');
            $icon = $request->input('icon');
            $amount = $request->input('amount');
            $type = $request->input('type');
            $created_at = $request->input('created_at');
            $envelop = Envelop::create([
                'category_id' => $category_id, 'name' => $name,
                'icon' => $icon, 'amount' => $amount,
                'type' => $type, 'created_at' => $created_at]);
            // Envelop was created and generate new token
            return Helpers::success_reponse([
                'message' => "Envelop was created.",
            ], 200, true);

        } catch (Exception $ex) {
            return Helpers::error_reponse("Eenvelop could not be creared.", 400);
        }
    }


    /**
     * delete an envelop.
     *
     * @param  Illuminate\Http\Request   $request
     * @return Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
       
        try {
            $envelop = Envelop::where("id", $request->route('id'))
            ->delete();
            // envelop was deleted and generate new token
            if ($envelop) {
                return Helpers::success_reponse([
                    'message' => "Envelop was deleted.",
                ], 200, true);
            } else {
                throw new Exception;
            }

        } catch (Exception $ex) {
            return Helpers::error_reponse("Envelop could not be deleted.", 400);
        }
    }

    /**
     * envelops summary
     *
     * @param  Illuminate\Http\Request   $request
     * @return Illuminate\Http\Response
     */
    public function summary(Request $request){
        
        $year = $request->input("year") ?? date("Y");
        $month = $request->input("month") ?? date("m");
  

        try {
        $summary = Envelop::join("categories", "envelops.category_id","=","categories.id")
        ->select(DB::raw('SUM(envelops.amount) as amount ,envelops.type'))
        ->where("categories.user_id", $request->auth->id)
        ->whereYear('envelops.created_at' ,$year ) 
        ->whereMonth('envelops.created_at' ,$month )
        ->groupBy('envelops.type')->get();
        return Helpers::success_reponse([
            'summary' => $summary,
        ], 200, false);
    } catch (Exception $ex) {
        dd($ex);
        return Helpers::error_reponse("Something went wrong!", 400);
    }
    }
}
