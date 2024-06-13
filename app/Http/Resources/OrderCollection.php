<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($data) {
                return [
                    'order_id' => (int) $data->id,
                    'delivery_status' => $data->delivery_status ,
                    'payment_status' => $data->payment_status,
                    'applied_coupon_code' => $data->applied_coupon_code,
                    'coupon_discount_amount' => (float) $data->coupon_discount_amount,
                    'admin_earning_percentage' => (float) $data->admin_earning_percentage,
                    'total_admin_earnings' => (float) $data->total_admin_earnings,
                    'total_vendor_earnings' => (float) $data->total_vendor_earnings,
                    'logistic_name' =>  $data->logistic_name,
                    'pickup_or_delivery' => $data->pickup_or_delivery,
                    'shipping_delivery_type' => $data->shipping_delivery_type,
                    'shipping_cost' => (float) $data->shipping_cost,
                    'refund_status' =>  $data->refund_status,
                    'created_date' => Carbon::parse($data->created_at)->format('l, F j, Y \a\t g:i A'),
                    'created_at' => $data->created_at,
                    'order_group' => [
                        "order_code"=> $data->orderGroup->order_code,
                        "phone_no"=> $data->orderGroup->phone_no,
                        "sub_total_amount"=> (double) $data->orderGroup->sub_total_amount,
                        "total_tax_amount"=> (double) $data->orderGroup->total_tax_amount,
                        "total_coupon_discount_amount"=> (double) $data->orderGroup->total_coupon_discount_amount,
                        "total_shipping_cost"=> (double) $data->orderGroup->total_shipping_cost,
                        "grand_total_amount"=> (double) $data->orderGroup->grand_total_amount,
                        "payment_method"=> (string) $data->orderGroup->payment_method,
                        "payment_status"=> (string) $data->orderGroup->payment_status,
                        "additional_discount_type"=> (string) $data->orderGroup->additional_discount_type,
                    ],
                    'user' => [
                        "name" => $data->user ? $data->user->name : null,
                        "email" => $data->user ? $data->user->email : null,
                        "phone" => $data->user ? $data->user->phone : null,
                        "user_balance" => $data->user ? $data->user->user_balance : null,
                    ],
                    "order_items" => $data->orderItems,
                ];
            })
        ];
    }
}
