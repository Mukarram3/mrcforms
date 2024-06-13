<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ImportProduct implements ToCollection,WithStartRow
{
    public function startRow(): int
    {
        return 2;
    }
    public function  __construct($branch_id)
    {
        $this->branch_id= $branch_id;
    }
    public function collection(Collection $rows)
    {
        foreach ($rows as $row)
        {
            try {
                $productPrice = DB::table('product_prices')
                    ->where('branch_id',$this->branch_id)
                    ->where('bar_code',$row[2])
                    ->update([
                    'min_price' => $row[0],
                    'max_price' => $row[0],
                    'stock_qty' => $row[1],
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return $e;
            }
        }
    }
}
