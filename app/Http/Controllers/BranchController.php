<?php

namespace App\Http\Controllers;

use App\Exports\ProductBranchExport;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Imports\ExportProduct;
use App\Imports\ImportExcelProduct;
use App\Imports\ImportMultipleCouponCode;
use App\Imports\ImportProduct;
use App\Imports\ProductPriceImport;
use App\Jobs\ImportProductPrices;
use App\Lib\Csvreader;
use App\Models\Branch;
use App\Models\BranchCity;
use App\Models\City;
use App\Models\Location;
use App\Models\Order;
use App\Models\ProductPrice;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class BranchController extends Controller
{
    public function index(Request $request){
        $searchKey = null;
        $is_active = null;

        $branches = Branch::latest();
        if ($request->search != null) {
            $branches = $branches->where('name', 'like', '%' . $request->search . '%');
            $searchKey = $request->search;
        }

        if ($request->is_published != null) {
            $branches = $branches->where('status', $request->is_active);
            $is_active    = $request->is_active;
        }
        $branches = $branches->paginate(paginationNumber());
        return view('backend.pages.branch.index', compact('branches', 'searchKey', 'is_active'));
    }
    public function create()
    {

        $cities = City::where('is_active',1)->get();
        return view('backend.pages.branch.create',compact('cities'));
    }
    public function store(Request $request)
    {
        $branch = new Branch();
        $branch->name = $request->name;
        $branch->phone = $request->phone;
        $branch->status = 'active';
        $branch->save();

        foreach ($request->city_name as $city){
            $data = new BranchCity();
            $data->branch_id = $branch->id;
            $data->city_id = $city;
            $data->save();
        }
        flash(localize('Branch has been added successfully'))->success();
        return redirect()->route('admin.branch.index');
    }

    public function edit($id)
    {
        $branch = Branch::find((int)$id);
        $cities = City::where('is_active',1)->get();

        return view('backend.pages.branch.edit', compact('branch','cities'));
    }
    public function updatePublishedStatus(Request $request)
    {
        $branch = Branch::findOrFail($request->id);
        $branch->status = $request->status;
        if ($branch->save()) {
            return 1;
        }
        return 0;
    }
    public function update(Request $request)
    {
        $branch = Branch::where('id', $request->id)->first();
        $branch->name = $request->name;
        $branch->phone = $request->phone;
        $branch->save();

        $city = BranchCity::where('branch_id',$branch->id)->delete();
        foreach ($request->city_name as $city){
            $data = new BranchCity();
            $data->branch_id = $branch->id;
            $data->city_id = $city;
            $data->save();
        }

        flash(localize('Branch has been updated successfully'))->success();
        return redirect()->route('admin.branch.index');
    }

    public function importExport(){
        $branches = Branch::all();
        return view('backend.pages.branch.importexport.index',compact('branches'));
    }
    public function import(Request $request){

        $request->validate([
            'branch_id' => 'required',
            'excel' => 'required|mimes:csv,txt',
        ]);

//        $path = $request->file('excel')->storeAs('uploads', 'import.csv', 'public');

        // Execute the command in the background
        $path = $request->file('excel')->storeAs('uploads', 'import.csv', 'public');

        // Dispatch the job for background processing
        ImportProductPrices::dispatch($request->branch_id);

        flash(localize('Data processing started in the background.'))->success();
        return redirect()->back();

//        $csvReader = new Csvreader();
//        $path = $request->file('excel')->getRealPath();
//        $csvdata = $csvReader->parse_file($path);
//        dd($csvdata);
//        $path = $request->file('excel')->storeAs('uploads', 'import.csv', 'public');
//
//        // Pass the branch ID and the file path to the job
//
//        if (count($csvdata) > 1) {
//            foreach ($csvdata as $post_data) {
//                $barcode = $post_data['Bar Code'];
//
//                // Find the existing data
//                $existingData = ProductPrice::where('branch_id', $request->branch_id)
//                    ->where('bar_code', $barcode)
//                    ->first();
//
//                // Check if existing data is found
//                if ($existingData) {
//                    // Update the existing data
//                    $existingData->min_price = $post_data['Price'];
//                    $existingData->max_price = $post_data['Price'];
//                    $existingData->stock_qty = $post_data['Stock Quantity'];
//
//                    // Save the changes
//                    $existingData->save();
//                } else {
//
//                }
//            }
//        }

//        Excel::import(new ProductPriceImport($request->branch_id), $file);

//        foreach ($csv_data as $row) {
//            $barcode =  $row[2];
//            $existingData = ProductPrice::where('branch_id', $request->branch_id)
//                ->where('bar_code', $barcode)
//                ->first();
//            if ($existingData) {
//                $existingData->min_price = $row[0];
//                $existingData->max_price = $row[0];
//                $existingData->stock_qty = $row[1];
//                $existingData->save();
//            }
//        }
//        flash(localize('Import Data  successfully'))->success();
//        return back();
    }
    public function export(Request $request){

        $branch_name  = "";
        $branch = Branch::where('id',$request->branch_id)->first();
        if ($branch){
            $branch_name = $branch->name;
        }
        return Excel::download(new ExportProduct($request->branch_id), $branch_name.'1.csv');
        flash(localize('Export Data  successfully'))->success();
        return back();
    }
    public function productImport(Request $request){
        Excel::import(new ImportExcelProduct(), $request->file('excel'));
        flash(localize('Import Data  successfully'))->success();
        return back();
    }

    public function updateAddress(Request $request){
        $data = UserAddress::find($request->address_id);
        if ($request->name){
            $data->full_name = $request->name;
        }

        if($request->phone){
            $data->phone = $request->phone;
        }

        if($request->address){
            $data->address = $request->address;
        }

        if($request->city_id){
            $data->city_id = $request->city_id;
        }
        $data->save();
        $address = UserAddress::with('city')->where('id',$data->id)->first();
        return response()->json(['status'=>'success','data'=>$address]);
    }

    public function getBranch(Request $request){
        $branch = Branch::where('id',$request->branch_id)->first()->name;
        $order = Order::where('id',$request->order_id)->update([
            'branch_name'=>$branch,
        ]);
        return response()->json(['branch'=>$branch,'message'=>'success']);
    }


    public function productExportBranch(Request $request){
        $branch_name  = "";
        $branch = Branch::where('id',$request->branch_id)->first();
        if ($branch){
            $branch_name = $branch->name;
        }
        return Excel::download(new ProductBranchExport($request->branch_id), $branch_name.'product.csv');
        flash(localize('Export Data successfully'))->success();
        return back();
    }


    public function CustomerImport(Request $request){
        $jsonFile = $request->file('json_file');
        $jsonContent = file_get_contents($jsonFile->getPathname());

        $jsonData = json_decode($jsonContent, true);

        // Import user data into the users table

        foreach ($jsonData as $user) {

            $firstname = $user['firstname'];
            $middlename = $user['middlename'];
            $lastname = $user['lastname'];

            $name = $firstname." ".$middlename." ".$lastname;
//            $name = implode(',', $array);
            User::create([
                'name' => $name,
                'email' => $user['email'],
                'password' => $user['password_hash'],
            ]);
        }
        return redirect()->back()->with('success', 'User data imported successfully.');
    }
}
