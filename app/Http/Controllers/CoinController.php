<?php

namespace App\Http\Controllers;

use App\Models\Coin;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class CoinController extends Controller
{
    use ResponseTrait ;
        public function getAll() {
            return $this->returnData("" , Coin::all());
        }
}
