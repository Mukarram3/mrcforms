<?php

namespace App\Imports;

use App\Models\Brand;
use App\Models\BrandLocalization;
use App\Models\Category;
use App\Models\CategoryLocalization;
use App\Models\MediaManager;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductLocalization;
use App\Models\ProductPrice;
use App\Models\ProductVariation;
use App\Models\ProductVariationStock;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ImportExcelProduct implements ToCollection,WithStartRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function startRow(): int
    {
        return 2;
    }
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
//            if (isset($row['0']) || isset($row['1']) || isset($row['2']) || isset($row['3']) || isset($row['4']) || isset($row['4']) || isset($row['5']) || isset($row['6']) || isset($row['7']) || isset($row['8']) || isset($row['9']) || isset($row['10'])) {

//            dd($row);
            try {
                $barcode = substr($row['0'], 1);
                $productPrice = ProductPrice::where('bar_code',$barcode)->first();
                if ($productPrice){
                    
                }else{
                    $branch = [
                        ['id' => '1'],
                        ['id' => '2'],
                        ['id' => '3'],
                    ];

                    // Brand
                    $brand = Brand::updateOrCreate(
                        ['name' => $row['10']],
                        [
                            'meta_title' => $row['10'],
                            'meta_description' => '',
                            'brand_image' => '',
                            'meta_image' => '',
                            'slug' => $row['10'],
                        ]
                    );

                    // Media
                    $media = MediaManager::create([
                        'media_file' => $row['8'],
                        'media_size' => '1024',
                        'media_type' => 'image',
                        'media_name' => 'image',
                        'media_extension' => 'jpg',
                    ]);

                    // Categories
                    if (!empty($row['9'])){
//                    $category = Category::updateOrCreate(
//                        ['name' => $row['9']],
//                        [
//                            'meta_title' => $row['9'],
//                            'sorting_order_level' => 0,
//                            'slug' => $row['9'],
//                        ]
//                    );

                        $category = Category::where('name',$row['9'])->first();
                        if ($category){

                        }else{
                            $category = new Category();
                            $category->name = $row['9'];
                            $category->meta_title = $row['9'];
                            $category->level = 0;
                            $category->slug = $row['9'];
                            $category->save();
                        }

                    }


                    if (!empty($row['11'])){
//                    $sub_category = new Category();
//                    $existing = Category::where('name',$row['11'])->first();
//                    if ($existing){
//                        $sub_category = Category::find($existing->id);
//                    }
//                    $sub_category->name = $row['11'];
//                    $sub_category->meta_title = $row['11'];
//                    $sub_category->parent_id = $category->id ?? null;
//                    $sub_category->level = 1;
//                    $sub_category->slug = $row['11'];
//                    $sub_category->save();



                        $sub_category = Category::where('name', $row['11'])->first();

                        if ($sub_category) {
                        } else {
                            $sub_category = new Category([
                                'name' => $row['11'],
                                'meta_title' => $row['11'],
                                'parent_id' => $category->id ?? null,
                                'level' => 1,
                                'slug' => $row['11'],
                            ]);
                            $sub_category->save();
                        }


//                    $sub_category = Category::updateOrCreate(
//                        ['name' => $row['11']],
//                        [
//                            'meta_title' => $row['11'],
//                            'sorting_order_level' => 0,
//                            'parent_id' => $category->id ?? null,
//                            'level' => 1,
//                            'slug' => $row['11'],
//                        ]
//                    );

                    }
                    if (!empty($row['12'])) {
                        // Check if the category already exists in the database
                        $sub_sub_category = Category::where('name', $row['12'])->first();

                        if ($sub_sub_category) {
                            // Update the existing category if found
//                        $sub_sub_category->name = $row['12'];
//                        $sub_sub_category->meta_title = $row['12'];
//                        $sub_sub_category->parent_id = $category->id ?? null;
//                        $sub_sub_category->level = 2;
//                        $sub_sub_category->slug = $row['12'];
//                        $sub_sub_category->save();

                        } else {
                            // Create a new category if it doesn't exist
                            $sub_sub_category = new Category([
                                'name' => $row['12'],
                                'meta_title' => $row['12'],
                                'parent_id' => $sub_category->id ?? null,
                                'level' => 2,
                                'slug' => $row['12'],
                            ]);
                            $sub_sub_category->save();
                        }
                    }

                    $data = new Product();
                    $data->name = $row[1];
                    $data->brand_id = $brand->id ?? '';
//                dd($data->name);
                    $data->weight = $row[4];
                    $data->shop_id = "1";
                    $data->slug = Str::slug($row[1], '-') . '-' . strtolower(Str::random(5));
                    $data->meta_description = $row[6];
                    $data->meta_title = $row[1];
                    $data->meta_img = $media->id ?? '';
                    $data->min_purchase_qty = "1";
                    $data->max_purchase_qty = "10";
                    $data->description = $row[2];
                    $data->short_description = $row[3];
                    $data->thumbnail_image = $media->id ?? '';
                    $data->gallery_images = $media->id ?? '';
                    $data->is_published = "1";
//                $data->has_variation = 0;
//                $data->has_warranty = "0";
                    $data->save();

                    $ProductLocalization = ProductLocalization::firstOrNew(['lang_key' => env('DEFAULT_LANGUAGE'), 'product_id' => $data->id]);
                    $ProductLocalization->name = $row['1'];
                    $ProductLocalization->description = $row['2'];
                    $ProductLocalization->save();

                    $variation              = new ProductVariation;
                    $variation->product_id  = $data->id;
                    $variation->sku         = " ";
                    $variation->code         = " " ;
                    $variation->price       = " ";
                    $variation->save();
                    $product_variation_stock                          = new ProductVariationStock;
                    $product_variation_stock->product_variation_id    = $variation->id;
//            $product_variation_stock->location_id             = $location->id;
                    $product_variation_stock->stock_qty               = '25';
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
                        $productPrice->min_price =  $row['5'];
                        $productPrice->max_price =  $row['5'];
                        $productPrice->stock_qty =  20;
                        $barcode = substr($row['0'], 1);
                        $productPrice->bar_code =  $barcode;
//                    $productPrice->bar_code =  $row['0'];
                        $productPrice->status =  "active";
                        $productPrice->save();
                    }
                }


            } catch (\Exception $e) {
                dd($e); // Handle the exception/error as needed
            }
    }
//    }
}
}
