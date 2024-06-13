<?php

namespace App\Http\Controllers\Backend\Logistics;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Models\City;
use App\Models\Logistic;
use App\Models\LogisticZone;
use App\Models\LogisticZoneCity;
use App\Models\Order;
use App\Models\OrderGroup;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogisticZonesController extends Controller
{
    # construct
    public function __construct()
    {
        $this->middleware(['permission:shipping_zones'])->only('index');
        $this->middleware(['permission:add_shipping_zones'])->only(['create', 'store']);
        $this->middleware(['permission:edit_shipping_zones'])->only(['edit', 'update']);
        $this->middleware(['permission:delete_shipping_zones'])->only(['delete']);
    }

    # zone list
    public function index(Request $request)
    {
        $searchKey = null;
        $searchLogistic = null;
        $logisticZones = LogisticZone::latest();
        if ($request->search != null) {
            $logisticZones = $logisticZones->where('name', 'like', '%' . $request->search . '%');
            $searchKey = $request->search;
        }

        if ($request->searchLogistic) {
            $logisticZones->where('logistic_id', $request->searchLogistic);
            $searchLogistic = $request->searchLogistic;
        }
        $logisticZones = $logisticZones->paginate(paginationNumber());
        return view('backend.pages.fulfillments.logisticZones.index', compact('logisticZones', 'searchKey', 'searchLogistic'));
    }

    # create zone
    public function create()
    {
        $logistics = Logistic::where('is_published', 1)->latest()->get();
        return view('backend.pages.fulfillments.logisticZones.create', compact('logistics'));
    }


    # create zone
    public function getLogisticCities(Request $request)
    {
        $logistic = Logistic::find($request->logistic_id);
        $html = '<option value="">' . localize("Select City") . '</option>';

        if (!is_null($logistic)) {
            $logisticCities = $logistic->cities()->pluck('city_id');
            $cities =    City::isActive()->whereNotIn('id', $logisticCities)->latest()->get();

            foreach ($cities as $city) {
                $html .= '<option value="' . $city->id . '">' . $city->name . '</option>';
            }
        }

        echo json_encode($html);
    }
    public function getAllProducts(Request $request)
    {
//        $products = ProductPrice::with('product')->where('branch_id',$request->branch_id)->get();
        $products = DB::table('products')
            ->join('product_prices', 'product_prices.product_id', 'products.id')
            ->where('branch_id', $request->branch_id)
            ->where('product_prices.stock_qty', '>', 0)
            ->select('products.id', 'products.name', 'product_prices.bar_code')
            ->get();

        $html = '<option value="">' . localize("Select Product") . '</option>';

        if (!is_null($products)) {
            foreach ($products as $product) {
                $html .= '<option value="' . $product->id . '">' . "(" . $product->bar_code . ")" . $product->name . '</option>';
            }
        }

        echo json_encode($html);
    }

    # zone store
    public function store(Request $request)
    {
        $logisticZone = new LogisticZone;
        $logisticZone->name = $request->name;
        $logisticZone->logistic_id = $request->logistic_id;
        $logisticZone->standard_delivery_charge = $request->standard_delivery_charge;
        $logisticZone->standard_delivery_time = $request->standard_delivery_time;
        $logisticZone->additional_cost = $request->additional_cost;
        $logisticZone->cost_per_kg = $request->cost_per_kg;
        $logisticZone->save();

        foreach ($request->city_ids as $city_id) {
            LogisticZoneCity::where('logistic_id', $logisticZone->logistic_id)
                ->where('city_id', $city_id)
                ->delete();
            $logisticZoneCity                   = new LogisticZoneCity;
            $logisticZoneCity->logistic_id      = $logisticZone->logistic_id;
            $logisticZoneCity->logistic_zone_id = $logisticZone->id;
            $logisticZoneCity->city_id          = $city_id;
            $logisticZoneCity->save();
        }

        flash(localize('Zone has been inserted successfully'))->success();
        return redirect()->route('admin.logisticZones.index');
    }

    # edit zone
    public function edit(Request $request, $id)
    {
        $logisticZone = LogisticZone::findOrFail($id);
        $cities       = City::isActive()->latest()->get();
        return view('backend.pages.fulfillments.logisticZones.edit', compact('logisticZone', 'cities'));
    }

    # update zone
    public function update(Request $request)
    {
        $logisticZone = LogisticZone::findOrFail($request->id);
        $logisticZone->name = $request->name;

        $logisticZone->additional_cost = $request->additional_cost;
        $logisticZone->cost_per_kg = $request->cost_per_kg;

        $logisticZone->standard_delivery_charge = $request->standard_delivery_charge;
        if ($request->express_delivery_charge) {
            $logisticZone->express_delivery_charge = $request->express_delivery_charge;
        }

        $logisticZone->standard_delivery_time = $request->standard_delivery_time;
        if ($request->express_delivery_charge) {
            $logisticZone->express_delivery_time = $request->express_delivery_time;
        }


        $logisticZone->save();


        LogisticZoneCity::where('logistic_id', $logisticZone->logistic_id)->delete();
        foreach ($request->city_ids as $city_id) {
            $logisticZoneCity                   = new LogisticZoneCity;
            $logisticZoneCity->logistic_id      = $logisticZone->logistic_id;
            $logisticZoneCity->logistic_zone_id = $logisticZone->id;
            $logisticZoneCity->city_id          = $city_id;
            $logisticZoneCity->save();
        }

        flash(localize('Zone has been updated successfully'))->success();
        return back();
    }

    # delete zone
    public function delete($id)
    {
        $logisticZone = LogisticZone::findOrFail($id);
        LogisticZoneCity::where('logistic_zone_id', $logisticZone->id)->delete();
        $logisticZone->delete();
        flash(localize('Zone has been deleted successfully'))->success();
        return back();
    }




    public function orderUpdate(Request $request){
        $order = Order::where('id',$request->order_id)->first();
        $product = Product::with('productPrice','variations')->where('id',$request->product_id)->first();

        $logisticZone = LogisticZone::where('id',$order->logistic_id)->first();
        $cost_per_kg = $logisticZone ? $logisticZone->cost_per_kg : 0 ;
        $additional_cost = $logisticZone ? $logisticZone->additional_cost : 0 ;
        $product_price = $product->productPrice[0] ? $product->productPrice[0]['weight'] : 0 ;
        $shippingAmount = $this->calculateShippingCost($product_price,$cost_per_kg,$additional_cost);

        $orderItems = new OrderItem();
        $orderItems->order_id = $order->id;
        $orderItems->product_variation_id = $product->variations[0]['id'];
        $orderItems->qty = 1;
        $orderItems->unit_price = $product->productPrice[0]['max_price'];
        $orderItems->total_tax = 0;
        $orderItems->total_price = $orderItems->unit_price + $orderItems->total_tax;
        $orderItems->save();

        $order->shipping_cost = $order->shipping_cost + $shippingAmount;
        $order->save();
        $orderGroup = OrderGroup::where('order_code',$request->order_code)->first();
        $orderGroup->sub_total_amount = $orderGroup->sub_total_amount +  $product->productPrice[0]['max_price'];
        $orderGroup->total_shipping_cost = $orderGroup->total_shipping_cost +  $shippingAmount;
        $orderGroup->grand_total_amount = $orderGroup->sub_total_amount +  $orderGroup->total_shipping_cost;
        $orderGroup->save();

        $product = array([
            'image'=> uploadedAsset($product->thumbnail_image),
            'name' => $product->name,
        ]);

        return response()->json(['status'=>'success','orderItems'=>$orderItems,'product'=>$product,
            'total_shiping_cost'=>$orderGroup->total_shipping_cost,'sub_total'=>$orderGroup->sub_total_amount,
            'grand_total'=>$orderGroup->grand_total_amount,
            ]);

    }


    function calculateShippingCost($weightInGrams,$cost_per_kg,$additional_cost) {
        $baseCost = $cost_per_kg;

        // Calculate the number of additional 1000g increments beyond the first 1000g
        $additionalIncrements = ceil(($weightInGrams - 1000) / 1000);

        // Calculate the total shipping cost
        $totalCost = $baseCost + ($additionalIncrements * $additional_cost);
//        dd($totalCost);
        return $totalCost;
    }


    public function orderProductUpdate(Request $request){

            $orderproduct = OrderItem::findOrfail($request->product_id);
            if ($orderproduct){
                if ($request->unit_price){
                    $orderproduct->unit_price = $request->unit_price;
                }
                if ($request->qty){
                    $orderproduct->qty = $request->qty;
                    $orderproduct->total_price = $orderproduct->unit_price * $request->qty;
                    $orderGroup = OrderGroup::findOrfail($request->order_group_id);
                    if ($orderGroup){
                        $orderGroup->sub_total_amount = $orderproduct->total_price;
                        $orderGroup->total_shipping_cost = $orderGroup->total_shipping_cost *  $request->qty;
                        $orderGroup->grand_total_amount = $orderGroup->sub_total_amount +  $orderGroup->total_shipping_cost;
                        $orderGroup->save();
                    }
                }
                if ($request->total_price){
                    $orderproduct->total_price = $request->total_price;
                }
                $orderproduct->save();
                return response()->json(['status'=>'success','data'=>$orderproduct,'sub_total'=>$orderGroup->sub_total_amount,'shipping_cost'=>$orderGroup->total_shipping_cost,'grand_total'=>$orderGroup->grand_total_amount]);
            }
    }

    //Delete Invoice Product
    public function deleteInvoiceProduct($id){
        {
            try {
                // Find the order item by ID and delete it
                $orderItem = OrderItem::find($id);
                if ($orderItem) {
                    $orderItem->delete();
                    return response()->json(['message' => 'Product deleted successfully']);
                } else {
                    return response()->json(['error' => 'Product not found']);
                }
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error deleting product', 'message' => $e->getMessage()]);
            }
        }
    }


//subtotal
    public function orderSubTotalUpdate(Request $request){
      $subtotal = OrderGroup::findOrfail($request->order_id);
        $subtotal->sub_total_amount = $request->sub_total_amount;
        $subtotal->save();
        return response()->json(['status'=>'success','data'=>$subtotal]);
    }

//shipping
    public function orderShippingUpdate(Request $request){
        $order = OrderGroup::findOrfail($request->order_id);
        $order->total_shipping_cost = $request->shipping_cost;
        $order->grand_total_amount = $order->sub_total_amount + $request->shipping_cost;
        $order->save();
        return response()->json(['status'=>'success','data'=>$order]);
    }

    //grand total
    public function orderGrandTotalUpdate(Request $request){
     $grandTotal = OrderGroup::findOrfail($request->order_id);
        $grandTotal->grand_total_amount = $request->grand_total_amount;
        $grandTotal->save();
        return response()->json(['status'=>'success','data'=>$grandTotal]);
    }

    //payment method
    public function orderPaymentMethodUpdate(Request $request){
        $paymentMethod = OrderGroup::findOrfail($request->order_id);
        $paymentMethod->payment_method = $request->payment_method;
        $paymentMethod->save();
        return response()->json(['status'=>'success','data'=>$paymentMethod]);
    }

    //logistic
    public function orderPLogisticUpdate(Request $request){
        $logistic = Order::findOrfail($request->order_id);
        $logistic->logistic_name = $request->logistic_name;
        $logistic->save();
        return response()->json(['status'=>'success','data'=>$logistic]);
    }
}



