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
                            'ismainproduct' => 1
                        ]);
        }

    }


    public function findvariants(){

        // finding main products 
        $data = XoProducts::select(['baseitemnum as bin', 'id','itemnum as in'])
                            ->where('ismainproduct', 1)
                            // ->distinct('baseitemnum')
                            ->get()
                            ->chunk(5) // shopify main product searching chunk size ==> take from env 
                            ->toarray();

        // dd($data);

        $allIds = [];

        // finding variants of each product 
        foreach($data as $d){
            foreach($d as $key=>$product){

                $variants = XoProducts::select(['id', 'baseitemnum as bin', 'itemnum as in'])
                                        ->where('baseitemnum', $product['bin'])
                                        ->where('ismainproduct', '!=', 1)
                                        ->where('itemnum', 'like', $product['bin'].'%' )
                                        ->get()
                                        ->toarray();

                echo "<pre>";
                // print_r($product);
                print_r($variants);
                // dd("asdf");

                // $data = XoProducts::select(['baseitemnum as bin', 'id','itemnum as in'])
                //                     ->where('')

            }
        }

        // marking all the matching products as main 
        foreach($allIds as $id){
            XoProducts::where(['id' => $id])
                        ->update([
                            'ismainproduct' => 1
                        ]);
        }

    }


    
}