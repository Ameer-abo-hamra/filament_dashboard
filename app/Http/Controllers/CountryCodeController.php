<?php

namespace App\Http\Controllers;

use App\Models\CountryCode;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class CountryCodeController extends Controller
{
    use ResponseTrait;
    public function getAll()
    {
        return $this->returnData("", CountryCode::all());
    }
}
