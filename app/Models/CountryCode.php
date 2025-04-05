<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountryCode extends Model
{
    use HasFactory;

    protected $fillable = ['country_name', 'country_code', 'iso_code', 'region'];


    public function subs() {
        return $this->hasMany(Subscriber::class , "country_code_id");
    }
}

