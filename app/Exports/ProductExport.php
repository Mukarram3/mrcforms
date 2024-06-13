<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
            return DB::table('products')
            ->select('products.name','products.total_sale_count')->get();
    }

  public function headings():array{
        return [

            'Product Name',
            'Total Sales',
        ];
  }
}
