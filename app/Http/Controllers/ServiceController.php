<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Validator;

class ServiceController extends Controller
{
    use ResponseTrait;
    public function addService(Request $request)
    {

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:services,name',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'content' => 'required|string',
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        try {
            $service = Service::create([
                "image" => photo($request, "service", "services"),
                "name" => $request->name,
                "content" => $request->content,
            ]);

            return $this->returnData("your data created successfully", $service);
        } catch (\Exception $e) {
            return $this->returnError("An error occured :(");
        }

    }

    public function getserviceById($id)
    {
        try {
            $service = service::findOrFail($id);

            return $this->returnData("servoce : ", $service);
        } catch (\Exception $e) {
            return $this->returnError("An error occured :(");
        }
    }
    public function getAllservices()
    {
        try {
            $service = service::all();
            return $this->returnData("service : ", $service);
        } catch (\Exception $e) {
            return $this->returnError("An error occured :(");
        }
    }
}
