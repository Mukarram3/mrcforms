<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;


class OrderExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return  DB::table('orders')
        ->join('order_groups', 'orders.order_group_id', 'order_groups.id')
        ->join('order_items', 'orders.id', 'order_items.order_id')
        ->select(
            'orders.created_at',
            DB::raw('COUNT(order_items.order_id) as order_items_count'),
            'orders.payment_status',
            'orders.delivery_status',
            'order_groups.grand_total_amount'
        )
        ->groupBy(
            'orders.created_at',
            'orders.payment_status',
            'orders.delivery_status',
            'order_groups.grand_total_amount'
        )
        ->get();
    }
    public function headings(): array
    {
        return [
        'placed_on',
        'items',
        'payment status',
        'delivery status',
        'total amount',
    ];
    }
}
