<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\MediaManager;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductLocalization;
use App\Models\ProductPrice;
use App\Models\ProductVariation;
use App\Models\ProductVariationStock;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function storeProduct(Request $request){
        try {
            $branch = [
                ['id' => '1'],
                ['id' => '2'],
                ['id' => '3'],
            ];

            $brand = Brand::where('name', $request->brandname)->first();
            if ($brand){

            }else{

                $brand_image = '';

                $brand = new Brand();
                $brand->name = $request->brandname;
                $brand->meta_title = $request->brandname;
                $brand->meta_description = $request->brandname;
                $brand->meta_image = $brand_image->id ?? '';
                $brand->slug = $request->brandname;
                $brand->save();
            }


            // Categories
            if ($request->category_name){
                $category = Category::where('name',$request->category_name)->first();
                if ($category){

                }else{
                    if ($request->hasFile('category_image')) {
                        $category_image = new MediaManager;
                        $category_image->user_id = 1;

                        $category_image->media_file = $request->file('category_image')->store('uploads/media');
                        $category_image->media_size = $request->file('category_image')->getSize();
                        $category_image->media_name = $request->file('category_image')->getClientOriginalName();
                        $category_image->media_extension = $request->file('category_image')->getClientOriginalExtension();

                        if (getFileType(Str::lower($category_image->media_extension)) != null) {
                            $category_image->media_type = getFileType(Str::lower($category_image->media_extension));
                        } else {
                            $category_image->media_type = "unknown";
                        }
                        $category_image->save();
                    }


                    $category = new Category();
                    $category->name = $request->category_name;
                    $category->meta_title = $request->category_name;
                    $category->image = $category_image->id ?? '';
                    $category->level = 0;
                    $category->slug = $request->category_name;
                    $category->save();
                }

            }


            if ($request->sub_category_name){
                $sub_category = Category::where('name', $request->sub_category_name)->first();
                if ($sub_category) {
                } else {
                    $sub_category = new Category([
                        'name' => $request->sub_category_name,
                        'meta_title' => $request->sub_category_name,
                        'parent_id' => $category->id ?? null,
                        'level' => 1,
                        'slug' => $request->sub_category_name,
                    ]);
                    $sub_category->save();
                }

            }
            if ($request->sub_sub_category_name) {
                // Check if the category already exists in the database
                $sub_sub_category = Category::where('name',$request->sub_sub_category_name)->first();

                if ($sub_sub_category) {
                } else {
                    // Create a new category if it doesn't exist
                    $sub_sub_category = new Category([
                        'name' => $request->sub_sub_category_name,
                        'meta_title' => $request->sub_sub_category_name,
                        'parent_id' => $sub_category->id ?? null,
                        'level' => 2,
                        'slug' => $request->sub_sub_category_name,
                    ]);
                    $sub_sub_category->save();
                }
            }


            if ($request->hasFile('product_image')) {
                $product_image = new MediaManager;
                $product_image->user_id = 1;

                $product_image->media_file = $request->file('product_image')->store('uploads/media');
                $product_image->media_size = $request->file('product_image')->getSize();
                $product_image->media_name = $request->file('product_image')->getClientOriginalName();
                $product_image->media_extension = $request->file('product_image')->getClientOriginalExtension();

                if (getFileType(Str::lower($product_image->media_extension)) != null) {
                    $product_image->media_type = getFileType(Str::lower($product_image->media_extension));
                } else {
                    $product_image->media_type = "unknown";
                }
                $product_image->save();
            }

            $gallery_image_ids = [];
            if ($request->hasFile('product_gallery_images')) {
                foreach ($request->file('product_gallery_images') as $gallery_image) {
                    $product_gallery_images = new MediaManager;
                    $product_gallery_images->user_id = 1;

                    $media_file = $gallery_image->store('uploads/media');
                    $product_gallery_images->media_file = $media_file;
                    $product_gallery_images->media_size = $gallery_image->getSize();
                    $product_gallery_images->media_name = $gallery_image->getClientOriginalName();
                    $product_gallery_images->media_extension = $gallery_image->getClientOriginalExtension();

                    $media_extension = Str::lower($product_gallery_images->media_extension);
                    $product_gallery_images->media_type = getFileType($media_extension) ?? "unknown";

                    $product_gallery_images->save();
                    $gallery_image_ids[] = $product_gallery_images->id;
                }
            }

            $data = new Product();
            $data->name = $request->product_name;
//                dd($data->name);
            $data->weight = $request->product_weight;
            $data->shop_id = "1";
            $data->slug = Str::slug($request->product_name, '-') . '-' . strtolower(Str::random(5));
            $data->meta_description = $request->product_description;
            $data->meta_title = $request->product_name;
            $data->meta_img = $product_image->id ?? '';
            $data->min_purchase_qty = "1";
            $data->max_purchase_qty = "10";
            $data->description = $request->product_description;
            $data->short_description =  $request->product_short_description;
            $data->thumbnail_image = $product_image->id ?? '';
            $data->gallery_images = isset($gallery_image_ids) ? implode(',', $gallery_image_ids) :  $product_image->id;
            $data->is_published = "1";
//                $data->has_variation = 0;
//                $data->has_warranty = "0";
            $data->save();

//            $ProductLocalization = ProductLocalization::firstOrNew(['lang_key' => env('DEFAULT_LANGUAGE'), 'product_id' => $data->id]);
//            $ProductLocalization->name = $row['1'];
//            $ProductLocalization->description = $row['2'];
//            $ProductLocalization->save();

            $variation              = new ProductVariation;
            $variation->product_id  = $data->id;
            $variation->sku         = " ";
            $variation->code         = " " ;
            $variation->price       = " ";
            $variation->save();
            $product_variation_stock                          = new ProductVariationStock;
            $product_variation_stock->product_variation_id    = $variation->id;
//            $product_variation_stock->location_id             = $location->id;
            $product_variation_stock->stock_qty               = '100';
            $product_variation_stock->save();

            $cateProd = new ProductCategory();
            $cateProd->category_id = $sub_sub_category ? $sub_sub_category->id : ($sub_category ? $sub_category->id : $category->id);
            $cateProd->product_id = $data->id;
            $cateProd->save();

            // Save Product Prices
            foreach ($branch as $br) {
                $productPrice = new ProductPrice();
                $productPrice->product_id =  $data->id;
                $productPrice->branch_id =  $br['id'];
                $productPrice->min_price =  $request->product_price;
                $productPrice->max_price =  $request->product_price;
                $productPrice->stock_qty =  $request->product_qty ?? "20";
                $barcode = substr($request->barcode, 1);
                $productPrice->bar_code =  $barcode;
//                    $productPrice->bar_code =  $row['0'];
                $productPrice->status =  "active";
                $productPrice->save();
            }

            return response()->json(['status'=>1,'message'=>'success']);

        } catch (\Exception $e) {
            dd($e); // Handle the exception/error as needed
        }
    }
}
