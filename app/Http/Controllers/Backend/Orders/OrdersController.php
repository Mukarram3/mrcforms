<?php

namespace App\Http\Controllers\Backend\Orders;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Language;
use App\Models\Location;
use App\Models\Order;
use App\Models\OrderGroup;
use App\Models\OrderItem;
use App\Models\OrderUpdate;
use App\Models\ProductPrice;
use App\Models\RefundItem;
use App\Models\RefundRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use PDF;
use Sample\RefundOrder;

class OrdersController extends Controller
{
    # construct
    public function __construct()
    {
        $this->middleware(['permission:orders'])->only('index');
        $this->middleware(['permission:manage_orders'])->only(['show', 'updatePaymentStatus', 'updateDeliveryStatus', 'downloadInvoice']);
    }

    # get all orders
    public function index(Request $request)
    {
        $searchKey = null;
        $searchCode = null;
        $deliveryStatus = null;
        $paymentStatus = null;
        $locationId = null;
        $posOrder = 0;

        $orders = Order::latest();


        # conditional 
//        order search by name and email
        if ($request->search != null) {
            $searchKey = $request->search;
            $orders = $orders->where(function ($q) use ($searchKey) {
                $customers = User::where('name', 'like', '%' . $searchKey . '%')
                    ->orWhere('email', 'like', '%' . $searchKey . '%')
                    ->pluck('id');
                $q->orWhereIn('user_id', $customers);
            });
        }
//        order search by CODE and Phone No

        if ($request->code != null) {
            $searchCode = $request->code;

            $orders = $orders->where(function ($q) use ($searchCode) {
                $orderGroup = OrderGroup::where('order_code', $searchCode)
                    ->orWhere('phone_no', 'like', '%' . $searchCode . '%')
                    ->pluck('id');
                $q->orWhereIn('order_group_id', $orderGroup);
            });
        }

        if ($request->delivery_status != null) {
            $deliveryStatus = $request->delivery_status;
            $orders = $orders->where('delivery_status', $deliveryStatus);
        }

        if ($request->payment_status != null) {
            $paymentStatus = $request->payment_status;
            $orders = $orders->where('payment_status', $paymentStatus);
        }

        if ($request->location_id != null) {
            $locationId = $request->location_id;
            $orders = $orders->where('location_id', $locationId);
        }


        if ($request->is_pos_order != null) {
            $posOrder = $request->is_pos_order;
        }

        $orders = $orders->where(function ($q) use ($posOrder) {
            $orderGroup = OrderGroup::where('is_pos_order', $posOrder)->pluck('id');
            $q->orWhereIn('order_group_id', $orderGroup);
        });

        $orders = $orders->paginate(paginationNumber());
        $locations = Location::where('is_published', 1)->latest()->get();
        return view('backend.pages.orders.index', compact('orders', 'searchKey', 'locations', 'locationId', 'searchCode', 'deliveryStatus', 'paymentStatus', 'posOrder'));
    }

    # show order details
    public function show($id)
    {
        $order = Order::find($id);
        return view('backend.pages.orders.show', compact('order'));
    }

    # update payment status 
    public function updatePaymentStatus(Request $request)
    {
        $order = Order::findOrFail((int)$request->order_id);
        $order->payment_status = $request->status;
        $order->save();

        OrderUpdate::create([
            'order_id' => $order->id,
            'user_id' => auth()->user()->id,
            'note' => 'Payment status updated to ' . ucwords(str_replace('_', ' ', $request->status)) . '.',
        ]);

        // todo::['mail notification']
        return true;
    }

    # update delivery status
    public function updateDeliveryStatus(Request $request)
    {
        $order = Order::findOrFail((int)$request->order_id);


        if ($order->delivery_status != orderCancelledStatus() && $request->status == orderCancelledStatus()) {
            $this->addQtyToStock($order);
        }
        if ($order->delivery_status == orderCancelledStatus() && $request->status != orderCancelledStatus()) {
            $this->removeQtyFromStock($order);
        }


        $order->delivery_status = $request->status;
        if ($request->tracking_number){
            $order->tracking_number = $request->tracking_number;
        }
        if ($request->canceled_reason){
            $order->canceled_reason = $request->canceled_reason;
        }
        $order->save();

        OrderUpdate::create([
            'order_id' => $order->id,
            'user_id' => auth()->user()->id,
            'note' => 'Delivery status updated to ' . ucwords(str_replace('_', ' ', $request->status)) . '.',
        ]);

        // todo::['mail notification'] 
        return true;
    }

    # add qty to stock 
    private function addQtyToStock($order)
    {
        $orderItems = OrderItem::where('order_id', $order->id)->get();
        foreach ($orderItems as $orderItem) {
            $prod = $orderItem->product_variation->product;
            $stock = ProductPrice::where('product_id', $prod->id)->first();

            if ($stock) {
                $stock->stock_qty += $orderItem->qty;
                $stock->save();
            }

            $product = $orderItem->product_variation->product;
            $product->total_sale_count += $orderItem->qty;
            $product->save();

            if ($product->categories()->count() > 0) {
                foreach ($product->categories as $category) {
                    $category->total_sale_count += $orderItem->qty;
                    $category->save();
                }
            }
        }
    }

    # remove qty from stock  
    private function removeQtyFromStock($order)
    {
        $orderItems = OrderItem::where('order_id', $order->id)->get();
        foreach ($orderItems as $orderItem) {
            $prod = $orderItem->product_variation->product;

            $stock = ProductPrice::where('product_id', $prod->id)->first();

            if ($stock) {
                $stock->stock_qty += $orderItem->qty;
                $stock->save();
            }

            $product = $orderItem->product_variation->product;
            $product->total_sale_count -= $orderItem->qty;
            $product->save();

            if ($product->categories()->count() > 0) {
                foreach ($product->categories as $category) {
                    $category->total_sale_count -= $orderItem->qty;
                    $category->save();
                }
            }
        }
    }

    # download invoice
    public function downloadInvoice($id)
    {
        if (session()->has('locale')) {
            $language_code = session()->get('locale', Config::get('app.locale'));
        } else {
            $language_code = env('DEFAULT_LANGUAGE');
        }

        if (session()->has('currency_code')) {
            $currency_code = session()->get('currency_code', Config::get('app.currency_code'));
        } else {
            $currency_code = env('DEFAULT_CURRENCY');
        }

        if (Language::where('code', $language_code)->first()->is_rtl == 1) {
            $direction = 'rtl';
            $default_text_align = 'right';
            $reverse_text_align = 'left';
        } else {
            $direction = 'ltr';
            $default_text_align = 'left';
            $reverse_text_align = 'right';
        }

        if ($currency_code == 'BDT' || $language_code == 'bd') {
            # bengali font
            $font_family = "'Hind Siliguri','sans-serif'";
        } elseif ($currency_code == 'KHR' || $language_code == 'kh') {
            # khmer font
            $font_family = "'Khmeros','sans-serif'";
        } elseif ($currency_code == 'AMD') {
            # Armenia font
            $font_family = "'arnamu','sans-serif'";
        } elseif ($currency_code == 'AED' || $currency_code == 'EGP' || $language_code == 'sa' || $currency_code == 'IQD' || $language_code == 'ir') {
            # middle east/arabic font
            $font_family = "'XBRiyaz','sans-serif'";
        } else {
            # general for all
            $font_family = "'Roboto','sans-serif'";
        }

        $order = Order::findOrFail((int)$id);
        $logistic = \App\Models\Logistic::with('zones')->where('name',$order->logistic_name)->first();

        $pdf = PDF::loadView('backend.pages.orders.invoice1', [
            'order' => $order,
            'font_family' => $font_family,
            'direction' => $direction,
            'default_text_align' => $default_text_align,
            'reverse_text_align' => $reverse_text_align,
            'logistic' => $logistic,
        ]);

//        $pdf = PDF::loadView('backend.pages.orders.invoice1', [
//            'order' => $order,
//            'font_family' => $font_family,
//            'direction' => $direction,
//            'default_text_align' => $default_text_align,
//            'reverse_text_align' => $reverse_text_align
//        ]);
//        $logistic = \App\Models\Logistic::with('zones')->where('name',$order->logistic_name)->first();
//        return view('backend.pages.orders.invoice1', [
//            'order' => $order,
//            'font_family' => $font_family,
//            'direction' => $direction,
//            'default_text_align' => $default_text_align,
//            'reverse_text_align' => $reverse_text_align,
//            'logistic' => $logistic,
//        ]);

// Customize the file name (e.g., order code as prefix)
        $fileName = getSetting('order_code_prefix') . $order->orderGroup->order_code . '.pdf';

//// Download the PDF with a custom file name
        return $pdf->download($fileName);
    }


    public function branchOrder(Request $request,$id)
    {
        $branch_name = Branch::where('id',$id)->first();
        $searchKey = null;
        $searchCode = null;
        $deliveryStatus = null;
        $paymentStatus = null;
        $locationId = null;
        $posOrder = 0;

        $orders = Order::where('branch_name',$branch_name->name)->latest();

        # conditional
        if ($request->search != null) {
            $searchKey = $request->search;
            $orders = $orders->where(function ($q) use ($searchKey) {
                $customers = User::where('name', 'like', '%' . $searchKey . '%')
                    ->orWhere('phone', 'like', '%' . $searchKey)
                    ->pluck('id');
                $q->orWhereIn('user_id', $customers);
            });
        }

        if ($request->code != null) {
            $searchCode = $request->code;
            $orders = $orders->where(function ($q) use ($searchCode) {
                $orderGroup = OrderGroup::where('order_code', $searchCode)->pluck('id');
                $q->orWhereIn('order_group_id', $orderGroup);
            });
        }

        if ($request->delivery_status != null) {
            $deliveryStatus = $request->delivery_status;
            $orders = $orders->where('delivery_status', $deliveryStatus);
        }

        if ($request->payment_status != null) {
            $paymentStatus = $request->payment_status;
            $orders = $orders->where('payment_status', $paymentStatus);
        }

        if ($request->location_id != null) {
            $locationId = $request->location_id;
            $orders = $orders->where('location_id', $locationId);
        }


        if ($request->is_pos_order != null) {
            $posOrder = $request->is_pos_order;
        }

        $orders = $orders->where(function ($q) use ($posOrder) {
            $orderGroup = OrderGroup::where('is_pos_order', $posOrder)->pluck('id');
            $q->orWhereIn('order_group_id', $orderGroup);
        });

        $orders = $orders->paginate(paginationNumber());
        $locations = Location::where('is_published', 1)->latest()->get();
        return view('backend.pages.orders.index', compact('orders', 'searchKey', 'locations', 'locationId', 'searchCode', 'deliveryStatus', 'paymentStatus', 'posOrder'));
    }

    public function refundOrder(Request $request){
        $order = Order::with('orderItems')->where('id',$request->order_id)->first();

//        dd($order);
        $user = Auth::user();
        $refund = new RefundRequest();
        $refund->order_id = $request->order_id;
        $refund->user_id = $user->id;
        $refund->reason = $request->refund_reason;
        $refund->refund_note = $request->refund_note;
        $refund->admin_approval = "pending";

        $amount = 0;
        foreach($order->orderItems as $refund_item){
                $item = $refund_item->firstWhere('id',$request->item_id);
//                dd($item);
                $amount += $request->item_qty * ($item->unit_price + $item->tax);
        }
        $refund->amount = $amount;

        $attachments = [];
        if($request->hasFile('images')){
            foreach($request->file('images') as $key => $attachment){

                $arr = explode('.', $attachment->getClientOriginalName());

                $image_path = $attachment->store('uploads');
                array_push($attachments, $image_path);
            }
        }


        $refund->attachments = implode(",",$attachments);
        $refund->save();

        $refundItems = new RefundItem();
        $refundItems->refund_request_id = $refund->id;
        $refundItems->order_item_id = $request->item_id;
        $refundItems->quantity = $request->item_qty;
        $refundItems->save();



        OrderUpdate::create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'note' => 'Refund request created.',
        ]);


        return response()->json([
            'status'=>'success',
            'message'=>"Your Request Submitted Successfully...",
        ]);
    }

    public function directCreate(Request $request){

        $lastOrderGroup = OrderGroup::latest()->first();

//        if (){
//            $lastOrderGroup = OrderGroup::latest()->first();
//            $order = Order::where('order_group_id',$lastOrderGroup->id)->first();
//            return view('backend.pages.orders.show', compact('order'));
//        }

        $orderGroup = OrderGroup::create([
            'order_code'=> $lastOrderGroup->order_code + 1,
//            'shipping_address_id'=> 22,
            'shipping_address_id'=> 1,
        ]);
        $newOrder = Order::create([
            'order_group_id' => $orderGroup->id,
        ]);
        $order = Order::find($newOrder->id);



        return view('backend.pages.orders.show', compact('order'));
    }

//    public function invoiceone(){
//        return view('backend.pages.orders.invoice1');
//    }
}
