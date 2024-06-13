<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DeliveryStatusReportExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
            $order = Order::groupBy('delivery_status')->selectRaw('delivery_status, count(delivery_status) as total_order')->get();
            return $order;
    }

    public function headings():array{
        return [
            'Status',
            "total count"
        ];
    }
}
