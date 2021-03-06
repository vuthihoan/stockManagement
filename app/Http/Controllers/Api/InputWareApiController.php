<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InputResource as InputResource;
use App\Models\InputWare;
use App\Models\DetailInputWare;
use App\Models\Product;
use App\Models\ProductZone;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Carbon;

class InputWareApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response([
            'data' => InputWare::all()->map(function ($input) {
                return new InputResource($input);
            })
        ]);
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
                  $product->inputWare_id = $input->id;
                  $product->zone_id =  $request->zone_id[$index];
                  $product->save();
                //   $proz = DB::table('product_zone')
                //     ->where('product_id',$product_id)
                //     ->where('zone_id',$request->zone_id[$index])
                //     ->first();
                // if (!is_null($proz)) {
                //     DB::table('product_zone')
                //         ->where('product_id',$product_id)
                //         ->where('zone_id',$request->zone_id[$index])
                //         ->update([
                //             'quantity_input' =>$proz->quantity_input + $request->estimate_quantity[$index],
                //             'quantity_stock' => $proz->quantity_stock + $request->estimate_quantity[$index],
                //         ]);

                //     } else {
                //         $quantity = new ProductZone;
                //         $quantity->product_id = $product_id;
                //         $quantity->zone_id = $request->zone_id[$index];
                //         $quantity->quantity_input = $request->estimate_quantity[$index];
                //         $quantity->quantity_stock = $request->estimate_quantity[$index];
                //         $quantity->save();
                //     }
            };
        };
        $message = ['status' => 'success', 'content' => 'Phiếu nhập kho được tạo thành công'];
        return response()->json(['url'=> route('inputs.index'), 'message' => $message], 200);
}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return InputResource
     */
    public function show($input_id)
    {
        $input =InputWare::find($input_id);
        return new InputResource( $input);
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
        $input = InputWare::find($id);
        $input->input_code = $request->input_code;
        $input->input_content=isset($request->input_content) ? $request->input_content : '';
        $input->input_date= date("Y-m-d", strtotime(request('input_date')));
        $input->user_id= $request->user_id;
        $input->status= $request->status;
        $input->save();
        $detail = $input->detail_input_wares()->delete();
        if ($request->get('product_id')) {
            foreach ($request->get('product_id') as $index=>$product_id) {
                  $product = new DetailInputWare();
                  $product->product_id = $product_id;
                  $product->detail_estimate_quantity = $request->detail_estimate_quantity[$index];
                  $product->inputWare_id = $input->id;
                  $product->zone_id =  $request->zone_id[$index];
                  $product->save();
            };
        };
                
        $message = ['status' => 'success', 'content' => 'Phiếu nhập kho được cập nhật thành công'];
        return response()->json(['url'=> route('inputs.index'), 'message' => $message], 200);

    }


    public function updateInput(Request $request, $id)
    {
        $input = InputWare::find($id);
        $input->status= 2;
        $input->save();
        $detail = $input->detail_input_wares()->delete();
        if ($request->get('product_id')) {
            foreach ($request->get('product_id') as $index=>$product_id) {
                  $product = new DetailInputWare();
                  $product->product_id = $product_id;
                  $product->detail_estimate_quantity = $request->detail_estimate_quantity[$index];
                  $product->detail_input_quantity = $request->quantity[$index];
                  $product->inputWare_id = $input->id;
                  $product->zone_id =  $request->zone_id[$index];
                  $product->save();
                  $proz = DB::table('product_zone')
                    ->where('product_id',$product_id)
                    ->where('zone_id',$request->zone_id[$index])
                    ->first();
                if (!is_null($proz)) {
                    DB::table('product_zone')
                        ->where('product_id',$product_id)
                        ->where('zone_id',$request->zone_id[$index])
                        ->update([
                            'quantity_input' =>$proz->quantity_input + $request->quantity[$index],
                            'quantity_stock' => $proz->quantity_stock + $request->quantity[$index],
                        ]);

                    } else {
                        $quantity = new ProductZone;
                        $quantity->product_id = $product_id;
                        $quantity->zone_id = $request->zone_id[$index];
                        $quantity->quantity_input = $request->quantity[$index];
                        $quantity->quantity_stock = $request->quantity[$index];
                        $quantity->save();
                    }
            };
        };
        $message = ['status' => 'success', 'content' => 'Phiếu nhập kho được tạo thành công'];
        return response()->json(['url'=> route('inputs.index'), 'message' => $message], 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($input_id)
    {
        $input = InputWare::find($input_id);
        $detail = $input->detail_input_wares()->get();
        foreach ($detail as $detail) {
            $detail->delete();
        }
        $input->delete();

        $message = ['status' => 'success', 'content' => 'Phiếu nhập kho đã bị xóa'];
        return response()->json(['url'=> route('inputs.index'), 'message' => $message], 200);
    }
}
