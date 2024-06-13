<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Backend\Orders\OrdersController;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderCollection;
use App\Models\Order;
use App\Models\OrderUpdate;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function getOrderData(Request  $request){
        $data  = Order::with('user','orderGroup','orderItems.product_variation.product')->orderBy('id','desc')->paginate(100);
        return new OrderCollection($data);

//        return response($data);
    }

    public function orderDetails(Request $request,$id){
        $data = Order::with('user','orderGroup','orderItems.product_variation.product')->where('id',$id)->get();
        return new OrderCollection($data);
    }
    public function updateDeliveryStatus(Request $request){
        try {
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

//        OrderUpdate::create([
//            'order_id' => $order->id,
//            'user_id' => auth()->user()->id,
//            'note' => 'Delivery status updated to ' . ucwords(str_replace('_', ' ', $request->status)) . '.',
//        ]);

            // todo::['mail notification']
            return true;
        }catch (\Exception $e){
            return  response($e->getMessage());
        }
    }
}
