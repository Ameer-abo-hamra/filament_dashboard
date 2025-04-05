<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "name",
        "description",
        "is_new",
        "price",
        "amount",
        "total",
        "category_id",
        "brand_id",
        "group_id",
        "image",
        "Minimum_sale",
        "commission",
        "coin_id",
        "deleted_by",
        "updated_by",
        "inserted_by"
    ];
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function coin()
    {
        return $this->belongsTo(Coin::class);
    }
    public function carts()
    {
        return $this->belongsToMany(Cart::class, 'cart_item')
            ->withPivot('check_out', 'amount', 'price' , "Minimum_sale")
            ->withTimestamps();
    }
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_item')
            ->withPivot('amount', 'price', 'status', "name");
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, "brand_id");
    }
    public function category()
    {
        return $this->belongsTo(Category::class, "category_id");
    }

    public function adminInserted()
    {
        return $this->belongsTo(Admin::class, "inserted_by");
    }
    public function adminDeleted()
    {
        return $this->belongsTo(Admin::class, "deleted_by");
    }
    public function adminUpdated()
    {
        return $this->belongsTo(Admin::class, "updated_by");
    }
}
