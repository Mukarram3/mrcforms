<?php

namespace App\Http\Controllers\Backend\Appearance;

use App\Http\Controllers\Controller;
use App\Models\HomeOwlSlider;
use App\Models\MediaManager;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class HeroController extends Controller
{
    # construct
    public function __construct()
    {
        $this->middleware(['permission:homepage'])->only(['hero', 'edit', 'delete']);
    }

    # get the sliders
    private function getSliders()
    {
        $sliders = [];

        if (getSetting('hero_sliders') != null) {
            $sliders = json_decode(getSetting('hero_sliders'));
        }
        return $sliders;
    }

    # homepage hero configuration
    public function hero()
    {
        $sliders = $this->getSliders();
        $homefirstsliders = HomeOwlSlider::where('type','first_slider')->get();
        $homeSecondSlider = HomeOwlSlider::where('type','Second_Slider')->get();
        $homeThirdSlider = HomeOwlSlider::where('type','third_slider')->get();

        return view('backend.pages.appearance.homepage.hero', compact('sliders','homefirstsliders','homeSecondSlider','homeThirdSlider'));
    }

    # store homepage hero configuration
    public function storeHero(Request $request)
    {
        $data = new HomeOwlSlider();
        $data->images= $request->image;
        $data->slider_link= $request->slider_link;
        $data->type="first_slider";
        $data->save();
//        $sliderImage = SystemSetting::where('entity', 'hero_sliders')->first();
//        if (!is_null($sliderImage)) {
//            if (!is_null($sliderImage->value) && $sliderImage->value != '') {
//                $sliders            = json_decode($sliderImage->value);
//                $newSlider          = new MediaManager; //temp obj
//                $newSlider->id      = rand(100000, 999999);
//                $newSlider->sub_title   = $request->sub_title ? $request->sub_title : '';
//                $newSlider->title       = $request->title ? $request->title : '';
//                $newSlider->text        = $request->text ? $request->text : '';
//                $newSlider->image       = $request->image ? $request->image : '';
//                $newSlider->link        = $request->link ? $request->link : '';
//
//
//
//                array_push($sliders, $newSlider);
//                $sliderImage->value = json_encode($sliders);
//                $sliderImage->save();
//            } else {
//                $value                  = [];
//                $newSlider              = new MediaManager; //temp obj
//                $newSlider->id          = rand(100000, 999999);
//                $newSlider->sub_title   = $request->sub_title ? $request->sub_title : '';
//                $newSlider->title       = $request->title ? $request->title : '';
//                $newSlider->text        = $request->text ? $request->text : '';
//                $newSlider->image       = $request->image ? $request->image : '';
//                $newSlider->link        = $request->link ? $request->link : '';
//
//                array_push($value, $newSlider);
//                $sliderImage->value = json_encode($value);
//                $sliderImage->save();
//            }
//        } else {
//            $sliderImage = new SystemSetting;
//            $sliderImage->entity = 'hero_sliders';
//
//            $value              = [];
//            $newSlider          = new MediaManager; //temp obj
//            $newSlider->id      = rand(100000, 999999);
//            $newSlider->sub_title   = $request->sub_title ? $request->sub_title : '';
//            $newSlider->title       = $request->title ? $request->title : '';
//            $newSlider->text        = $request->text ? $request->text : '';
//            $newSlider->image       = $request->image ? $request->image : '';
//            $newSlider->link        = $request->link ? $request->link : '';
//
//            array_push($value, $newSlider);
//            $sliderImage->value = json_encode($value);
//            $sliderImage->save();
//        }
//        cacheClear();
        flash(localize('Slider image added successfully'))->success();
        return back();
    }

    # edit hero slider
    public function edit($id)
    {
//        $sliders = $this->getSliders();
        $firstSlider = HomeOwlSlider::find($id);

        return view('backend.pages.appearance.homepage.heroEdit', compact( 'id','firstSlider'));
    }

    # update hero slider
    public function update(Request $request)
    {
//        dd($request->all());
        $firstSlider = HomeOwlSlider::find($request->id);
        $firstSlider->slider_link = $request->slider_link;
        $firstSlider->images = $request->image;
        $firstSlider->update();


//        $sliderImage = SystemSetting::where('entity', 'hero_sliders')->first();
//
//        $sliders = $this->getSliders();
//        $tempSliders = [];
//
//        foreach ($sliders as $slider) {
//            if ($slider->id == $request->id) {
//                $slider->sub_title  = $request->sub_title;
//                $slider->title      = $request->title;
//                $slider->text       = $request->text;
//                $slider->image      = $request->image;
//                $slider->link       = $request->link;
//                array_push($tempSliders, $slider);
//            } else {
//                array_push($tempSliders, $slider);
//            }
//        }
//
//        $sliderImage->value = json_encode($tempSliders);
//        $sliderImage->save();
//        cacheClear();
        flash(localize('Slider updated successfully'))->success();
        return redirect()->route('admin.appearance.homepage.hero');
    }

    # delete hero slider
    public function delete($id)
    {
        $firstSlider = HomeOwlSlider::find($id);

        if($firstSlider){
            $firstSlider->delete();
        }


//        $sliderImage = SystemSetting::where('entity', 'hero_sliders')->first();
//
//        $sliders = $this->getSliders();
//        $tempSliders = [];
//
//        foreach ($sliders as $slider) {
//            if ($slider->id != $id) {
//                array_push($tempSliders, $slider);
//            }
//        }
//
//        $sliderImage->value = json_encode($tempSliders);
//        $sliderImage->save();

        cacheClear();
        flash(localize('Slider deleted successfully'))->success();
        return redirect()->route('admin.appearance.homepage.hero');
    }

//SLIDER TWO ADD DELETE UPDATE


    public function storeSecondSlider(Request $request){


            $secondValue = new HomeOwlSlider();
            $secondValue->images = $request->image;
            $secondValue->slider_link= $request->slider_link;
            $secondValue->type = "Second_Slider";
            $secondValue->save();

        flash(localize('Slider image added successfully'))->success();
        return back();
    }

// Third Slider

 public function storeThirdSlider(Request $request){

        $thirdValue = new HomeOwlSlider();
        $thirdValue->images = $request->image;
        $thirdValue->slider_link= $request->slider_link;
        $thirdValue->type = "third_slider";
        $thirdValue->save();

     flash(localize('Slider image added successfully'))->success();
     return back();

 }
}
