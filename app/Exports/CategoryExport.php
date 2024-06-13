<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CategoryExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
       return DB::table('categories')
           ->select('categories.name','categories.total_sale_count')->get();
    }

    public function headings():array{
        return[
            'Category Name',
            'Total Sales'
        ];
    }
}
