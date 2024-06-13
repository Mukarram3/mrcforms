<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;


class ProductNoImage implements FromArray,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }
    public function array(): array
    {
        return $this->data;
    }
    public function headings(): array
    {
        return [
            'thubnail',
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
