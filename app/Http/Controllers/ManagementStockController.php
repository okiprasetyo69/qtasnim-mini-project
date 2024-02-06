<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Items;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Helpers\GlobalHelper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ManagementStockController extends Controller
{
    public function createItem(Request $request){
        try{

            $items = new Items();
            $items->fill($request->all());

            $userId = auth()->user()->id;

            $validator = Validator::make($request->all(), [
                'item_name' => 'required',
                'item_type' => 'required',
                'stock' => 'required'
            ]);

            if($validator->fails()){
                return response()->json([
                    'data' => null,
                    'message' => $validator->errors(),
                    'status' => 422
                ]);
            }

            // update
            if($request->id != null){
                $items = $items::find($request->id);
            }
        
            $items->user_id = $userId;
            $items->item_name = $request->item_name;
            $items->stock = $request->stock;
            $items->item_type = $request->item_type;

            $items->save();
            return response()->json(
                [
                'status' => 200,
                'message' => true,
                'data' => $items
                ]
            ); 

        }catch(Exception $ex){
            Log::error($ex->getMessage());
            return false;
        }
    }

    public function getItems(Request $request){
        try{
            $userId = auth()->user()->id;
            $items = Items::where("user_id", $userId)->orderBy("item_name", "ASC");

            if($request->id != null){
                $items->where("id", $request->id);
            }

            $items = $items->get();
            return response()->json(
                [
                'status' => 200,
                'message' => true,
                'data' => $items
                ]
            );

        }catch(Exception $ex){
            Log::error($ex->getMessage());
            return false;
        }
    }

    public function deleteItem(Request $request){
        try{
            $item = Items::find($request->id);
            if($item == null){
                return response()->json([
                    'data' => null,
                    'message' => 'Data not found',
                    'status' => 400
                ]);
            }

            $item->delete();
            return response()->json(
                [
                'status' => 200,
                'message' => 'Success delete item.',
                ]
            );

        }catch(Exception $ex){
            Log::error($ex->getMessage());
            return false;
        }
    }

}
