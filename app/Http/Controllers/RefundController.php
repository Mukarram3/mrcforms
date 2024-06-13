<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Order;
use App\Models\OrderGroup;
use App\Models\OrderUpdate;
use App\Models\RefundRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RefundController extends Controller
{
    public function index(Request $request)
    {
        $searchKey = null;
        $searchKey = null;
        $searchCode = null;
        $deliveryStatus = null;
        $paymentStatus = null;
        $locationId = null;
        $posOrder = 0;
        $orders = RefundRequest::with('user','order.orderGroup','refundItems.orderItem.product_variation.product')->latest();

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


        $orders = $orders->paginate(paginationNumber());

        $locations = Location::where('is_published', 1)->latest()->get();
        return view('backend.pages.refund.index', compact('orders', 'searchKey', 'locations', 'locationId', 'searchCode', 'deliveryStatus', 'paymentStatus', 'posOrder'));
    }
    public function statusUpdate(Request $request,$id){

        $user = Auth::user();
//        dd($request->all());
        if ($request->status == 'rejected'){
        $order = Order::where('id',$request->order_id)->update([
            'refund_status'=>'rejected',
            'delivery_status'=>'refund_returned_rejected',
        ]);
            OrderUpdate::create([
                'order_id' => $request->order_id,
                'user_id' => $user->id,
                'note' => 'Refund Rejected request created.',
            ]);
            flash(localize('Refund/Return Rejected successfully'))->success();

        }

        if ($request->status == 'approved'){
            $order = Order::where('id',$request->order_id)->update([
                'refund_status'=>'approved',
                'delivery_status'=>'refund_returned',
            ]);
            OrderUpdate::create([
                'order_id' => $request->order_id,
                'user_id' => $user->id,
                'note' => 'Refund Approved request created.',
            ]);
            flash(localize('Refunded/Returned successfully'))->success();
        }
        $refund = RefundRequest::where('id',$id)->update([
                'admin_approval'=>$request->status,
        ]);


//        flash(localize('Status Updated successfully'))->success();

        return redirect()->back();
    }
}
