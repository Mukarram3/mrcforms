<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SaleAmountExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return DB::table('order_items')
            ->select('order_items.total_price',
                'order_items.created_at')->get();
    }

    public function headings():array{
        return [
            'Date',
            'Total Sales'
        ];
    }
}
