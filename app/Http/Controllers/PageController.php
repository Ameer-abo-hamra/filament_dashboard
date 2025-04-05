<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Validator;

class PageController extends Controller
{
    use ResponseTrait;
    public function addPage(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:pages,name',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'content' => 'required|string',
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        try {
            $page = Page::create([
                "image" => photo($request, "page", "pages"),
                "name" => $request->name,
                "content" => $request->content,
            ]);

            return $this->returnData("your data created successfully", $page);
        } catch (Exception $e) {
            return $this->returnError("An error occured :(");
        }

    }

    public function getPageById($id)
    {
        try {
            $page = Page::findOrFail($id);

            return $this->returnData("page : ", $page);
        } catch (Exception $e) {
            return $this->returnError("An error occured :(");
        }
    }
    public function getAllPages()
    {
        try {
            $page = Page::all();
            return $this->returnData("page : ", $page);
        } catch (Exception $e) {
            return $this->returnError("An error occured :(");
        }
    }
}
