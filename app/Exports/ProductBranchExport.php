<?php

namespace App\Exports;

use App\Models\ProductPrice;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductBranchExport implements FromCollection,WithHeadings
{
    public function  __construct($branch_id)
    {
        $this->branch_id = $branch_id;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = DB::table('products')->join('product_prices','products.id','product_prices.product_id')
            ->join('product_categories','product_categories.product_id','products.id')
            ->join('categories','product_categories.category_id','categories.id')

            ->where('product_prices.branch_id',$this->branch_id)
            ->select('products.name as productName','categories.name as categoryName','products.weight','product_prices.max_price','product_prices.stock_qty', DB::raw("CONCAT('*', product_prices.bar_code) AS bar_code"))
            ->get();


//        ->join('brands','products.brand_id','brands.id')
//        'brands.name as brandName'
        return  $data;
    }

    public function headings(): array
    {
        return [
            'productName',
//            'brandName',
            'categoryName',
            'weight',
            'price',
            'stock_qty',
            'bar_code'
        ];
    }
}
