<?php

namespace App\Imports;

use App\Models\ProductPrice;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithUpserts;

class ProductPriceImport implements ToModel,WithUpserts,WithStartRow,WithHeadingRow
{
    public function startRow(): int
    {
        return 2;
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function  __construct($branch_id)
    {
        $this->branch_id = $branch_id;
    }
    public function model(array $row)
    {
        return new ProductPrice([
            'bar_code' => $row['bar_code'],
            'branch_id' => $this->branch_id,
            'min_price' => $row['price'],
            'max_price' => $row['price'],
            'stock_qty' => $row['stock_quantity'],
            // Additional fields if needed
        ]);
    }

    public function uniqueBy()
    {
        return ['bar_code', 'branch_id'];
    }

}
