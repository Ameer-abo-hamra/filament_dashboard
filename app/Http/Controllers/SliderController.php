<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    use ResponseTrait;
    public function addSlider(Request $request)
    {

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:sliders,name',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'content' => 'required|string',
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        try {
            $service = Slider::create([
                "image" => photo($request, "slider", "slideres"),
                "name" => $request->name,
                "content" => $request->content,
            ]);

            return $this->returnData("your data created successfully", $service);
        } catch (\Exception $e) {
            return $this->returnError("An error occured :(");
        }

    }
    public function getsliderById($id)
    {
        try {
            $service = Slider::findOrFail($id);

            return $this->returnData("slider : ", $service);
        } catch (\Exception $e) {
            return $this->returnError("An error occured :(");
        }
    }
    public function getAllslideres()
    {
        try {
            $service = Slider::all();
            return $this->returnData("Slider : ", $service);
        } catch (\Exception $e) {
            return $this->returnError("An error occured :(");
        }
    }


}
