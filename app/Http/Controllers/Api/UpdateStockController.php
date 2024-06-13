<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Product;
use App\Models\ProductLocalization;
use App\Models\ProductPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UpdateStockController extends Controller
{
    public function updateStock(Request $request){
        $validator = \Validator::make($request->all(), [
            'branch_code' => 'required',
            'bar_code' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(),422);
        }
        $branch = Branch::where('branch_code',$request->branch_code)->first();
        if ($branch){
            $product_price = ProductPrice::where('branch_id',$branch->id)->where('bar_code',$request->bar_code)->first();
            if ($product_price){
                if ($request->price){
                    $product_price->min_price = $request->price;
                    $product_price->max_price = $request->price;
                }
                if ($request->stock_qty >= 0){
                    $product_price->stock_qty= $request->stock_qty;
                }
                $product_price->save();
                return response()->json(['status'=>1]);
            }else{
                return  response()->json(['status'=>0]);
            }
        }else{
            return response()->json(['status'=>0]);
        }
    }


    public function productUpdate(Request $request){
//        dd($request->all());
        $validator = \Validator::make($request->all(), [
            'bar_code' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(),422);
        }
        $barCode = $request->bar_code;
        $productPrice = ProductPrice::where('bar_code', $barCode)->first();

        if ($productPrice) {
            $product = Product::where('id', $productPrice->product_id)->first();

            if ($product) {
                // Assigning values from request or keeping the existing values if request values are null
                $product = Product::find($product->id);
                if ($request->name){
                    $product->name = $request->name;
                }
                $product->weight = $request->weight ?? $product->weight;
                $product->description = $request->description ?? $product->description;
                $product->short_description = $request->short_description ?? $product->short_description;

                // Generating a slug based on request or the product name and a random string
                $product->slug = $request->slug ? Str::slug($request->slug, '-') : Str::slug($product->name, '-') . '-' . strtolower(Str::random(5));

                $product->save();


                $ProductLocalization = ProductLocalization::firstOrNew(['product_id' => $product->id]);
                if ($ProductLocalization){
                    $ProductLocalization->name = $request->name;
                    $ProductLocalization->description = $request->description;
                    $ProductLocalization->short_description = $request->short_description;
                    $ProductLocalization->save();
                }
            }
        }
        return response()->json(['status'=>1,'prod'=>$product]);
    }

    public function stockUploadinBulk(Request $request){
//        dd($request->all());
        $jsonContent = $request->stock;


        // Import user data into the users table

        foreach ($jsonContent as $data) {
            $branch = Branch::where('branch_code',$data['branch_code'])->first();
            if ($branch){
                $product_price = ProductPrice::where('branch_id',$branch->id)->where('bar_code',$data['bar_code'])->first();
                if ($product_price){
                    if (isset($data['price'])){
                        $product_price->min_price = $data['price'];
                        $product_price->max_price = $data['price'];
                    }
                    if ($request->stock_qty >= 0){
                        $product_price->stock_qty= $data['stock_qty'];
                    }
                    $product_price->save();
                }
            }
        }
        return response()->json(['status'=>1]);
    }
}