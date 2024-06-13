<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductVariationInfoResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductPrice;
use App\Models\ProductTag;
use App\Models\ProductVariation;
use App\Models\Review;
use App\Models\ReviewImage;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    # product listing
    public function index(Request $request)
    {
        $searchKey = null;
        $per_page = 30;
        $sort_by = $request->sort_by ? $request->sort_by : "new";
        $maxRange = ProductPrice::max('max_price');
        $min_value = 0;
        $max_value = $maxRange;

//        $products = Product::query();

        if (\Illuminate\Support\Facades\Session::has('city')) {
            $cityId = \Illuminate\Support\Facades\Session::get('city');

            $branch = \App\Models\Branch::whereHas('cities', function ($q) use ($cityId) {
                $q->where('city_id', $cityId);
            })->latest()->first();

            $branchId = $branch->id ?? null;

            $products = Product::whereHas('productPrices', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId)
                    ->where('stock_qty', '>', 0);
            })->with(['productPrices' => function ($query) use ($branchId) {
                    $query->where('branch_id', $branchId)
                        ->where('stock_qty', '>', 0);
                }]);
        }

//        if ($request->search != null) {
//            $products = $products->where('name', 'like', '%' . $request->search . '%');
//            $searchKey = $request->search;
//        }

        if ($request->search != null) {
            $products = $products->where(function($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%')
                    ->orWhereHas('categories', function($categoryQuery) use ($request) {
                        $categoryQuery->where('name', 'like', '%' . $request->search . '%');
                    });
            });

            $searchKey = $request->search;
        }



        if ($request->per_page != null) {
            $per_page = $request->per_page;
        }

        if ($sort_by == 'new') {
            $products = $products->latest();
        } else {
            $products = $products->orderBy('total_sale_count', 'DESC');
        }

        if ($request->min_price != null) {
            $min_value = $request->min_price;
        }

        if ($request->max_price != null) {
            $max_value = $request->max_price;
        }

        if ($request->min_price || $request->max_price) {
            $products = $products->with('productPrice')
                ->whereHas('productPrice', function ($query) use ($min_value, $max_value) {
                    $query->whereBetween('min_price', [$min_value, $max_value]);
                });
        }

        if ($request->category_id && $request->category_id != null) {
            $categoryIds = [$request->category_id];

// Fetch child categories recursively
            function getChildCategoryIds($categoryId) {
                $category = Category::find($categoryId);
                $childIds = [$categoryId];

                if ($category) {
                    foreach ($category->childrenCategories as $childCategory) {
                        $childIds = array_merge($childIds, getChildCategoryIds($childCategory->id));
                    }
                }

                return $childIds;
            }

            $categoryIds = getChildCategoryIds($request->category_id);

// Fetch products for given category and its children
            $product_category_product_ids = ProductCategory::whereIn('category_id', $categoryIds)->pluck('product_id');
            $products = $products->whereIn('id', $product_category_product_ids);
        }

        if ($request->tag_id && $request->tag_id != null) {
            $product_tag_product_ids = ProductTag::where('tag_id', $request->tag_id)->pluck('product_id');
            $products = $products->whereIn('id', $product_tag_product_ids);
        }

        $products = $products->where('is_published', 1)->paginate(paginationNumber($per_page));
        $tags = Tag::all();

        return getView('pages.products.index', [
            'products' => $products,
            'searchKey' => $searchKey,
            'per_page' => $per_page,
            'sort_by' => $sort_by,
            'max_range' => $maxRange,
            'min_value' => $min_value,
            'max_value' => $max_value,
            'tags' => $tags,
        ]);
    }

    # product show
    public function show($slug)
    {

        if (\Illuminate\Support\Facades\Session::has('city')) {
            $cityId = \Illuminate\Support\Facades\Session::get('city');

            $branch = \App\Models\Branch::whereHas('cities', function ($q) use ($cityId) {
                $q->where('city_id', $cityId);
            })->latest()->first();

            $branchId = $branch->id ?? null;

            $product = Product::whereHas('productPrices', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })->with(['productPrices' => function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            }])->where('slug', $slug)->first();

        }else{
            $product                        = Product::with('productPrices')->where('slug', $slug)->first();

        }



        $productCategories              = $product->categories()->pluck('category_id');

        $category = Category::where('id',$productCategories)->first();

        // Function to recursively fetch parent categories
        $breadcrumb = $this->getParentCategories($category);
        $breadcrumb = array_reverse($breadcrumb);

        $breadcrumbString = '';

        $count = count($breadcrumb);
        foreach ($breadcrumb as $index => $cat) {
            if ($index < $count - 1) {
                $breadcrumbString .= '<a href="'. route('products.index').'?category_id='.$cat->id. '">' . $cat->name . '</a> > ';
            } else {
                $breadcrumbString .= $cat->name;
            }
        }


        // Append the current category name without a link
        $breadcrumbString = $breadcrumbString ;


//        dd($breadcrumbString);


        $productIdsWithTheseCategories  = ProductCategory::whereIn('category_id', $productCategories)->where('product_id', '!=', $product->id)->pluck('product_id');



        if (\Illuminate\Support\Facades\Session::has('city')) {
            $cityId = \Illuminate\Support\Facades\Session::get('city');

            $branch = \App\Models\Branch::whereHas('cities', function ($q) use ($cityId) {
                $q->where('city_id', $cityId);
            })->latest()->first();

            $branchId = $branch->id ?? null;

            $relatedProducts = Product::whereHas('productPrices', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId)
                    ->where('stock_qty', '>', 0);
            })->with(['productPrices' => function ($query) use ($branchId) {
                $query->where('branch_id', $branchId)
                    ->where('stock_qty', '>', 0);
            }])->where('is_published',1)->whereIn('id', $productIdsWithTheseCategories)->limit(15)->get();

        }else{
            $relatedProducts                = Product::where('is_published',1)->whereIn('id', $productIdsWithTheseCategories)->limit(15)->get();

        }


        $product_page_widgets = [];
        if (getSetting('product_page_widgets') != null) {
            $product_page_widgets = json_decode(getSetting('product_page_widgets'));
        }

//        dd($relatedProducts);
        addNewRecentlyViewProduct($product->id);


        $overallAverageRating = Review::where('product_id',$product->id)->avg('rating');

        $individualRatings = Review::where('product_id',$product->id)->selectRaw('rating, AVG(rating) as average_rating')
            ->groupBy('rating')
            ->orderBy('rating')
            ->get();

        return getView('pages.products.show', ['product' => $product, 'relatedProducts' => $relatedProducts, 'product_page_widgets' => $product_page_widgets,
            'overAllAverageRating'=>$overallAverageRating,'individualRating'=>$individualRatings,'categoryBreadcrumb'=>$breadcrumbString]);
    }

    # product info
    public function showInfo(Request $request)
    {
        $product = Product::find($request->id);
        return getView('pages.partials.products.product-view-box', ['product' => $product]);
    }

    # product variation info
    public function getVariationInfo(Request $request)
    {
        $variationKey = "";
        foreach ($request->variation_id as $variationId) {
            $fieldName      = 'variation_value_for_variation_' . $variationId;
            $variationKey  .=  $variationId . ':' . $request[$fieldName] . '/';
        }
        $productVariation = ProductVariation::where('variation_key', $variationKey)->where('product_id', $request->product_id)->first();

        return new ProductVariationInfoResource($productVariation);
    }



    public function productReview(Request $request){
//        dd($request->all());
        $validate = $request->validate([
           'rating' => 'required',
        ]);
        $data = new Review();
        $data->rating = $request->rating;
        $data->product_id = $request->productId;
        $data->comment = $request->comment;
        $data->status = 'inactive';
        $data->user_id = Auth::user()->id;
        $data->save();

        foreach ($request->images as $image) {
            if ($image instanceof \Illuminate\Http\UploadedFile && $image->isValid()) {
                $reviewImage = new ReviewImage();
                $reviewImage->review_id = $data->id;
                $path = $image->store('uploads');
                $reviewImage->images = $path;
                $reviewImage->save();
            }
        }
        return response()->json(['status'=>'success',]);
    }

    public function getProductReview(Request $request){
        $data = Review::with('reviewImage')->where('product_id',$request->product_id)
            ->where('user_id',Auth::user()->id)->first();
//        dd($data);
        if ($data){
            foreach ($data->reviewImage as $image){
                $image->images = url('/')."/public/".$image->images;
            }
        }
        return response()->json(['status'=>'success','review'=>$data]);
    }

    private function getParentCategories($category, &$breadcrumb = [])
    {
        $breadcrumb[] = $category;

//        dd($breadcrumb);
        if ($category->parentCategory) {
            $this->getParentCategories($category->parentCategory, $breadcrumb);
        }
        return $breadcrumb;
    }
}
