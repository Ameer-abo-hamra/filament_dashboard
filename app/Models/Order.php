<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['cart_id', 'sub_id', 'total', 'order_date', "status" , "deletable" , "editable", "updated_by" ];

    // العلاقة مع العناصر
    public function items()
    {
        return $this->belongsToMany(Item::class, 'order_item')
            ->withPivot('amount', 'price', 'status' , "name");
    }
    public function cart()
{
    return $this->belongsTo(Cart::class);
}

public function sub() {
    return $this->belongsTo(Subscriber::class , "sub_id");
}
public function adminUpdated()
{
    return $this->belongsTo(Admin::class, "updated_by");
}
public function getItemsCountAttribute()
{
    return $this->items->count();
}

}
