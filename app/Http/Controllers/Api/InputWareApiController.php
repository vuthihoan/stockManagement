<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InputWare;
use App\Models\DetailInputWare;
use App\Models\Product;
use App\Models\ProductZone;
use Illuminate\Http\Request;
use DB;

class InputWareApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = new InputWare();
        $input->input_code = $request->input_code;
         $input->input_content=isset($request->input_content) ? $request->input_content : '';
        $input->input_date= date("Y-m-d", strtotime(request('input_date')));
        $input->user_id= $request->user_id;
        $input->save();
        if ($request->get('product_id')) {
            foreach ($request->get('product_id') as $index=>$product_id) {
                  $product = new DetailInputWare();
                  $getProduct = Product::where('id', $product_id)->first();
                  $product->product_id = $product_id;
                  $product->detail_estimate_quantity = $request->estimate_quantity[$index];
                  $product->detail_input_amount = $request->estimate_quantity[$index] * $getProduct['product_price'];
                  $product->inputWare_id = $input->id;
                  $product->save();
                  $zone = $getProduct->zones()->get();
                  $proz = DB::table('product_zone')
                    ->where('product_id',$product_id)
                    ->where('zone_id',$zone[0]->id)
                    ->first();
                  $productZone = ProductZone::where('product_id',$product_id)->first();
                  $proz =  $productZone->where('zone_id',$zone[0]->id)->first();
            if (!is_null($proz)) {
                DB::table('product_zone')
                    ->where('product_id',$product_id)
                    ->where('zone_id',$zone[0]->id)
                    ->update([
                        'quantity_input' =>$proz->quantity_input + $request->estimate_quantity[$index],
                        'quantity_stock' => $proz->quantity_stock + $request->estimate_quantity[$index],
                    ]);

            } else {
                $quantity = new ProductZone;
                $quantity->product_id = $product_id;
                $quantity->zone_id = $zone[0]->id;
                $quantity->quantity_input = $request->estimate_quantity[$index];
                $quantity->quantity_stock = $request->estimate_quantity[$index];
                $quantity->save();
            }
            };
        };
}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}