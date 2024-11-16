<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use App\Models\XoProducts;

class AjaxController extends Controller
{

public function postdata(Request $request){
        $validator = Validator::make($request->input(), [
            'item_name' => 'required|string',
            'item_code' => 'required|digits:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }

        $req = $request->only('item_name', 'item_code');

        // operations need to be performed on data here only 

        if($req){
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Data available!!!',
                'data' => $req
            ], 200);
        }else{
            return response()->json([
                'code' => 400,
                'status' => 'error',
                'message' => 'Data not available!!!'
            ], 400);            
        }
    }



    public function findmainitemandvariants(){

        $data = XoProducts::select(['baseitemnum as bin', 'id','itemnum as in'])
                            ->distinct('baseitemnum')
                            ->get()
                            ->chunk(5) // shopify main product searching chunk size ==> take from env 
                            ->toarray();
        
        $allIds = [];

        foreach($data as $d){
            foreach($d as $key=>$val){
                if($val['bin'] == $val['in']){
                    array_push($allIds, $val['id']);
                }
            }
        }

        // marking all the matching products as main 
        foreach($allIds as $id){
            XoProducts::where(['id' => $id])
                        ->update([
                            'is_main' => 1
                        ]);
        }

    }


}