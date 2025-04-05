<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
        protected $fillable = [ "sub_id" , "check_out_date" , "total"];
    public function sub() {
        return $this->belongsTo(Subscriber::class , "sub_id");
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'cart_item')
                    ->withPivot('check_out', 'price' , "amount" , "Minimum_sale")
                    ->withTimestamps();
    }
    public function orders()
{
    return $this->hasMany(Order::class);
}
}
