<?php

namespace App\Http\Controllers\Backend\Stocks;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchCity;
use App\Models\Cart;
use App\Models\City;
use App\Models\Location;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Nwidart\Modules\Commands\SeedCommand;

class LocationsController extends Controller
{
    # construct
    public function __construct()
    {
        $this->middleware(['permission:show_locations'])->only('index');
        $this->middleware(['permission:add_location'])->only(['create', 'store']);
        $this->middleware(['permission:edit_location'])->only(['edit', 'update']);
        $this->middleware(['permission:publish_locations'])->only(['updatePublishedStatus', 'updateDefaultStatus']);
    }

    # location index
    public function index(Request $request)
    {
        $searchKey = null;
        $is_published = null;

        $locations = Location::latest();
        if ($request->search != null) {
            $locations = $locations->where('name', 'like', '%' . $request->search . '%');
            $searchKey = $request->search;
        }

        if ($request->is_published != null) {
            $locations = $locations->where('is_published', $request->is_published);
            $is_published    = $request->is_published;
        }

        $locations = $locations->paginate(paginationNumber());
        return view('backend.pages.stocks.locations.index', compact('locations', 'searchKey', 'is_published'));
    }

    # change the currency
    public function changeLocation(Request $request)
    {
//        if ($request->location_id){
            $branch = Branch::where('id',$request->location_id)->first();
            $city = BranchCity::where('branch_id',$branch->id)->first();
            Session::put('city', $city->id);
            Session::put('branch_id', $request->location_id);
            return true;
//        }
    }

    # add location
    public function create()
    {
        return view('backend.pages.stocks.locations.create');
    }

    # add location
    public function store(Request $request)
    {
        $location = new Location;
        $location->name = $request->name;
        $location->address = $request->address;
        $location->banner = $request->image;
        if (Location::count() == 0) {
            $location->is_default = 1;
        }
        $location->save();
        flash(localize('Location has been added successfully'))->success();
        return redirect()->route('admin.locations.index');
    }


    # edit location
    public function edit($id)
    {
        $location = Location::find((int)$id);
        return view('backend.pages.stocks.locations.edit', compact('location'));
    }

    # update location
    public function update(Request $request)
    {
        $location = Location::where('id', $request->id)->first();
        $location->name = $request->name;
        $location->address = $request->address;
        $location->banner = $request->image;
        $location->save();
        flash(localize('Location has been updated successfully'))->success();
        return redirect()->route('admin.locations.index');
    }

    # update published
    public function updatePublishedStatus(Request $request)
    {
        $location = Location::findOrFail($request->id);
        if ($location->is_default == 1) {
            return 3;
        }
        $location->is_published = $request->status;
        if ($location->save()) {
            return 1;
        }
        return 0;
    }

    # update default
    public function updateDefaultStatus(Request $request)
    {
        $location = Location::findOrFail($request->id);
        $default = Location::where('is_default', 1)->first();
        if (!is_null($default)) {
            $default->is_default = 0;
            $default->save();
        }
        $location->is_default = $request->status;
        if ($location->save()) {
            return 1;
        }
        return 0;
    }

    public function backupDownload(){

        {

            // Get the list of files in the backup directory
            $backupPath = public_path() . "/backup/";

// Get all files in the backup directory
            $files = glob($backupPath . '*.zip');

// Sort files by modification time to get the latest backup
            usort($files, function ($a, $b) {
                return filemtime($b) - filemtime($a);
            });

            if (!empty($files)) {
                // Get the path to the latest backup file
                $latestBackup = $files[0];

                // Provide a custom file name for the download
                $fileName = basename($latestBackup); // Use the original backup file name

                // Download the file and set a custom name for the user
                return response()->download($latestBackup, $fileName);
            }

// Handle situation where no backups are found
            return response()->json(['message' => 'No backups found.']);
        }
    }

    public function backup(){
        return view('backend.pages.backup.index');
    }
}
