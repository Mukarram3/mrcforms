<?php

namespace App\Http\Controllers\Backend\Appearance;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\TopTrendingProduct;
use Illuminate\Http\Request;

class TopTrendingProductsController extends Controller
{
    # construct
    public function __construct()
    {
        $this->middleware(['permission:homepage'])->only('index');
    }

    # trending products
    public function index()
    {
        $categories = Category::latest()->get();
        return view('backend.pages.appearance.homepage.topTrendingProducts', compact('categories'));
    }

    # get products based on category
    public function getProducts(Request $request)
    {

        $html = '';
        if ($request->trending_product_categories) {
            $productIdsFromCategories = ProductCategory::whereIn('category_id', $request->trending_product_categories)->pluck('product_id');
            $products = Product::whereIn('id', $productIdsFromCategories)->get();

            $top_trending_products = getSetting('top_trending_products') != null ? json_decode(getSetting('top_trending_products')) : [];

            foreach ($products as $product) {
                if (in_array($product->id, $top_trending_products)) {
                    $html .= '<option value="' . $product->id . '" selected>' . $product->collectLocalization('name') . '</option>';
                } else {
                    $html .= '<option value="' . $product->id . '">' . $product->collectLocalization('name') . '</option>';
                }
            }
        }

        echo json_encode($html);
    }

    public function store(Request  $request){
//        dd($request->all());
        foreach ($request->input('branch_ids') as $branchId) {
            // Find existing record for the current branch_id or create a new one
            $data = TopTrendingProduct::firstOrNew(['branch_id' => $branchId]);

            // Update the fields
            $data->category_id = json_encode($request->input("trending_product_categories.$branchId"));
            $data->products = json_encode($request->input("top_trending_products.$branchId"));

            // Save to the database
            $data->save();
        }
        return back();
    }
}
