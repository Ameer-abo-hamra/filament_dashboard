<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Validator ;
class ContactController extends Controller
{
    use ResponseTrait;
    public function addcontact(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:contacts,name',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'content' => 'required|string',
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        try {
            $contact = contact::create([
                "image" => photo($request, "contact", "contacts"),
                "name" => $request->name,
                "content" => $request->content,
            ]);

            return $this->returnData("your data created successfully", $contact);
        } catch (\Exception $e) {
            return $this->returnError("An error occured :(");
        }

    }

    public function getcontactById($id)
    {
        try {
            $contact = contact::findOrFail($id);

            return $this->returnData("servoce : ", $contact);
        } catch (\Exception $e) {
            return $this->returnError("An error occured :(");
        }
    }
    public function getAllcontacts()
    {
        try {
            $contact = Contact::all();
            return $this->returnData("contact : ", $contact);
        } catch (\Exception $e) {
            return $this->returnError("An error occured :(");
        }
    }
}
