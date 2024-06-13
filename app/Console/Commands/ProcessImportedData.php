<?php

namespace App\Console\Commands;

use App\Lib\Csvreader;
use App\Models\ProductPrice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProcessImportedData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:imported_data {branch_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process imported data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $branchId = $this->argument('branch_id');

        $csvReader = new Csvreader();
        $path = storage_path('app/public/uploads/import.csv'); // Update the path as needed
        $csvData = $csvReader->parse_file($path);

        if (count($csvData) > 1) {
            foreach ($csvData as $postData) {
                $barcode = $postData['Bar Code'];

                $existingData = ProductPrice::where('branch_id', $branchId)
                    ->where('bar_code', $barcode)
                    ->update([
                        'min_price'=>$postData['Price'],
                        'max_price'=>$postData['Price'],
                        'stock_qty'=>$postData['Stock Quantity'],
                    ]);

            }
        }

//        $data = array_map('str_getcsv', file(storage_path('app/public/uploads/import.csv')));
//        $csv_data = array_slice($data, 1); // Remove header row
//
//        foreach ($csv_data as $row) {
//            DB::table('product_prices')
//                ->where('branch_id', $branchId)
//                ->where('bar_code', $row[2])
//                ->update([
//                    'min_price' => $row[0],
//                    'max_price' => $row[0],
//                    'stock_qty' => $row[1],
//                ]);
//        }

        $this->info('Data processing completed.');
    }
}
