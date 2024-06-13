<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductPrice;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportProduct implements FromCollection,WithHeadings
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    public function  __construct($branch_id)
    {
        $this->branch_id= $branch_id;
    }

    public function collection()
    {
        return ProductPrice::where('branch_id', $this->branch_id)
            ->select('min_price as price', 'stock_qty', 'bar_code')
            ->orderBy('id', 'ASC')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Price',
            'Stock Quantity',
            'Bar Code',
        ];
    }
}
