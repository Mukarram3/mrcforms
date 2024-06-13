<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportPrpd implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = DB::table('products')
            ->join('product_prices', 'products.id', 'product_prices.product_id')
            ->join('product_categories', 'product_categories.product_id', 'products.id')
            ->join('categories', 'product_categories.category_id', 'categories.id')
            ->select('products.name as productName', 'products.description as productDescription', 'products.short_description as productShortDescription', 'categories.name as categoryName', 'products.weight', 'product_prices.max_price', 'product_prices.stock_qty', DB::raw("CONCAT('*', product_prices.bar_code) AS bar_code"))
            ->get();
        return $data;
    }
    public function headings(): array
    {
        return [
            'productName',
//            'brandName',
            'productDescription',
            'productShortDescription',
            'categoryName',
            'weight',
            'price',
            'stock_qty',
            'bar_code'
        ];
    }
}
