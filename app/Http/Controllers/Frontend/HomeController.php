<?php

namespace App\Http\Controllers\Frontend;

use App\Exports\ExportPrpd;
use App\Http\Controllers\Controller;
use App\Imports\ExportProduct;
use App\Models\Blog;
use App\Models\Branch;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class HomeController extends Controller
{
    # set theme
    public function theme($name = "")
    {
        session(['theme' => $name]);
        return redirect()->route('home');
    }

    # homepage
    public function index()
    {
        $blogs = Blog::isActive()->latest()->take(3)->get();

        $sliders = [];
        if (getSetting('hero_sliders') != null) {
            $sliders = json_decode(getSetting('hero_sliders'));
        }

        $banner_section_one_banners = [];
        if (getSetting('banner_section_one_banners') != null) {
            $banner_section_one_banners = json_decode(getSetting('banner_section_one_banners'));
        }

        $client_feedback = [];
        if (getSetting('client_feedback') != null) {
            $client_feedback = json_decode(getSetting('client_feedback'));
        }


        return getView('pages.home', ['blogs' => $blogs, 'sliders' => $sliders, 'banner_section_one_banners' => $banner_section_one_banners, 'client_feedback' => $client_feedback]);
    }

    # all brands
    public function allBrands()
    {
        return getView('pages.brands');
    }

    # all categories
    public function allCategories()
    {

        return getView('pages.categories.index');
    }



    # all coupons
    public function allCoupons()
    {
        return getView('pages.coupons.index');
    }

    # all offers
    public function allOffers()
    {
        return getView('pages.offers');
    }

    # all blogs
    public function allBlogs(Request $request)
    {
        $searchKey  = null;
        $blogs = Blog::isActive()->latest();

        if ($request->search != null) {
            $blogs = $blogs->where('title', 'like', '%' . $request->search . '%');
            $searchKey = $request->search;
        }

        if ($request->category_id != null) {
            $blogs = $blogs->where('blog_category_id', $request->category_id);
        }

        $blogs = $blogs->paginate(paginationNumber(5));
        return getView('pages.blogs.index', ['blogs' => $blogs, 'searchKey' => $searchKey]);
    }

    # blog details
    public function showBlog($slug)
    {
        $blog = Blog::where('slug', $slug)->first();
        return getView('pages.blogs.blogDetails', ['blog' => $blog]);
    }

    # get all campaigns
    public function campaignIndex()
    {
        return getView('pages.campaigns.index');
    }

    # campaign details
    public function showCampaign($slug)
    {
        $campaign = Campaign::where('slug', $slug)->first();
        return getView('pages.campaigns.show', ['campaign' => $campaign]);
    }

    # about us page
    public function aboutUs()
    {
        $features = [];

        if (getSetting('about_us_features') != null) {
            $features = json_decode(getSetting('about_us_features'));
        }

        $why_choose_us = [];

        if (getSetting('about_us_why_choose_us') != null) {
            $why_choose_us = json_decode(getSetting('about_us_why_choose_us'));
        }

        return getView('pages.quickLinks.aboutUs', ['features' => $features, 'why_choose_us' => $why_choose_us]);
    }

    # contact us page
    public function contactUs()
    {
        return getView('pages.quickLinks.contactUs');
    }

    # quick link / dynamic pages
    public function showPage($slug)
    {
        $page = Page::where('slug', $slug)->first();
        return getView('pages.quickLinks.index', ['page' => $page]);
    }
    public function changeCity(Request $request){
        $data = $request->city_id;
        if ($data){
            Session::put('city',$data);
            return redirect()->back();
        }else{
            return redirect()->back()->with('message','success');
        }
    }
    public function productExport(){
        return Excel::download(new ExportPrpd(), 'product.csv');
    }
    public function allProducts(Request $request){
        $totalcount = $request->total;


//        $products = Product::where('is_published',1)->orderBy('id', 'DESC')
//            ->skip($totalcount)
//            ->limit(12)
//            ->get();

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
                }])->where('is_published',1)->orderBy('id', 'DESC')
                ->skip($totalcount)
                ->limit(12)
                ->get();;
        }



        $html = '';

        foreach ($products as $product) {
            $html .= "<div class='col-lg-3 col-6'>" . view('frontend.default.pages.partials.products.vertical-product-card', [
                    'product' => $product,
                    'bgClass' => 'bg-white',
                ])->render() . "</div>";
            $totalcount++;
        }

        $response = [
            'html' => $html,
            'totalcount' => $totalcount,
        ];

        return response()->json($response);
    }
    public function autoCompleteSearch(Request $request){
        $query = $request->get('query');
        $filterResult = Product::where('name', 'LIKE', '%'. $query. '%')->select('name')->get();
        return response()->json($filterResult);
    }

    public function uploader(Request $request)
    {
        $store = null;
        if ($request->file('file')->isValid()) {
            $store = $request->file('file')->store('uploads');
            return response()->json($store);
        } else {
            return response()->json(['error' => 'File upload failed'], 400);
        }
    }


    public function getReview(Request $request)
    {
        $totalcount = 12;
        $data = Review::with('user', 'reviewImage')->where('product_id',$request->productId)
            ->where('status','active')
            ->limit(12)
            ->orderBy('id','desc')
            ->get();
        $totalcount=0;
        $html = '';

        foreach ($data as $dt) {
            $userName = $dt->user->name;
            $userImage = $dt->user->avatar;
            $createdAt = Carbon::parse($dt->created_at);
            $timeAgo = $createdAt->diffForHumans();
            $html .= "<div class=\"col-md-8\">
    <div class=\"d-flex flex-column comment-section\">
        <div class=\"bg-white p-2 review\">
            <div class=\"d-flex flex-row user-info\">
                <img class=\"rounded-circle\" src=\"" . staticAsset('images/avatar.jpg') . "\" width=\"40\">
                <div class=\"d-flex flex-column justify-content-start ml-2\">
                    <span class=\"d-block font-weight-bold name mx-2\">$userName</span>
                    <span class=\"date text-black-50 mx-2\"> $timeAgo</span>
                </div>
            </div>
            <div class=\"mt-2\">
                <div class=\"stars\">";

// Generate stars based on rating
            for ($i = 1; $i <= $dt->rating; $i++) {
                $html .= "<i class=\"fa fa-star\"></i>";
            }

// Complete the star section and start the comment text
            $html .= "</div>
                <p class=\"comment-text\">$dt->comment</p>";

// Loop through review images
            foreach ($dt->reviewImage as $reviewimg) {
                $html .= "<img class=\"img-fluid pt-1\" src=\"" . staticAsset($reviewimg->images) . "\" alt=\"product_image\" height=\"84\" width=\"84\" id=\"#\"  data-bs-toggle=\"modal\" data-bs-target=\"#ImageModel\">";
            }

// Complete the review HTML block
            $html .= "</div>
        </div>
    </div>
</div>";
            $totalcount++;
        }
        $response = [
            'html' => $html,
            'totalcount' => $totalcount,
        ];

        return response()->json($response);

    }
}
