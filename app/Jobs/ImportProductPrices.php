<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ImportProductPrices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $branchId;
    public function __construct($branchId)
    {
        $this->branchId = $branchId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $exitCode = Artisan::call('process:imported_data', [
            'branch_id' => $this->branchId,
        ]);
        if ($exitCode === 0) {
            // Success message
            flash(localize('Data processing completed successfully.'))->success();
//            Session::flash('success', 'Data processing completed successfully.');
        } else {
            // Error message if needed
            flash(localize('Data processing failed. Please try again.'))->success();

//            Session::flash('error', 'Data processing failed. Please try again.');
        }
    }
}
