<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transactions;
use App\Models\Items;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Helpers\GlobalHelper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function createTransaction(Request $request){
        try{

            $transactions = new Transactions();
            $items = new Items();
            $transactions->fill($request->all());
            $userId = auth()->user()->id;

            $validator = Validator::make($request->all(), [
                'item_id' => 'required',
                'total_item_sold' => 'required',
            ]);

            if($validator->fails()){
                return response()->json([
                    'data' => null,
                    'message' => $validator->errors(),
                    'status' => 422
                ]);
            }

            // Set to attribute & date now
            $transactions->user_id = auth()->user()->id;;
            $transactions->item_id = $request->item_id;
            $transactions->total_item_sold = $request->total_item_sold;
            $transactions->transaction_date =  date('Y-m-d');

            // get actual stock item
            $items = $items::where("id", $request->item_id)->first();
            if($items == null){
                return response()->json([
                    'data' => null,
                    'message' => "Data item not found. Please check item available !",
                    'status' => 422
                ]);
            }
            $items->stock = $items->stock - $request->total_item_sold;

            // save transactions 
            $transactions->save();
            
            // update current stock item
            $items->save();

            return response()->json(
                [
                'status' => 200,
                'message' => true,
                'data' => $transactions
                ]
            ); 

        }catch(Exception $ex){
            Log::error($ex->getMessage());
            return false;
        }
    }

    public function getTransactions(Request $request){
        try{
            $transactions = Transactions::with("items");

            $itemName = $request->item_name;

            if($request->start_date != null){
                $transactions->where("transaction_date", ">=",$request->start_date);
            }

            if($request->end_date != null){
                $transactions->where("transaction_date", "<=",$request->end_date);
            }
            
            if($request->item_name != null){
                $transactions->whereHas("items", function($q) use ($request) {
                    $q->where("item_name", "like", "%" . $request->item_name. "%");
                });
            }

            if($request->sort_by != null){
                $transactions->orderBy("total_item_sold", $request->sort_by);
            }

            $transactions = $transactions->get();

            return response()->json(
                [
                'status' => 200,
                'message' => true,
                'data' => $transactions
                ]
            ); 

        } catch(Exception $ex){
            Log::error($ex->getMessage());
            return false;
        }
    }
}
