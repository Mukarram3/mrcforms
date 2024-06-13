<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NavigationColor extends Controller
{
    public function navigationColor(Request $request){
        $data = \App\Models\NavigationColor::first();
        if ($data){
            $navStoreColor = \App\Models\NavigationColor::find($data->id);
        }else{
            $navStoreColor = new \App\Models\NavigationColor();
        }
        if ($request->color_code){
            $navStoreColor->background_color = $request->color_code;
        }
        else{
            $navStoreColor->background_color = $request->background_color;
        }



        $navStoreColor->save();

        return redirect()->back();
    }


}
