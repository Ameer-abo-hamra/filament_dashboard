<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Filament\Panel;

class Admin extends Authenticatable
{
    use HasFactory;

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
    protected $fillable = ['email', 'password' , "name"];
    public function getNameAttribute(): string
    {
        return $this->attributes['name'] ?? $this->email; // استخدم البريد الإلكتروني كبديل إذا لم يكن هناك اسم.
    }
    public function insertedBrands()
    {
        return $this->hasMany(Brand::class, "inserted_by");
    }
    public function updatedBrands()
    {
        return $this->hasMany(Brand::class, "updated_by");
    }
    public function deletedBrands()
    {
        return $this->hasMany(Brand::class, "deleted_by");
    }
    //////////////////////////
    public function insertedCategories()
    {
        return $this->hasMany(Category::class, "inserted_by");
    }
    public function updatedCategories()
    {
        return $this->hasMany(Category::class, "updated_by");
    }
    public function deletedCategories()
    {
        return $this->hasMany(Category::class, "deleted_by");
    }
    //////////////////////////
    public function insertedGroups()
    {
        return $this->hasMany(Group::class, "inserted_by");
    }
    public function updatedGroups()
    {
        return $this->hasMany(Group::class, "updated_by");
    }
    public function deletedGroups()
    {
        return $this->hasMany(Group::class, "deleted_by");
    }
    ///////////////////////////
    public function insertedItems()
    {
        return $this->hasMany(Item::class, "inserted_by");
    }
    public function updatedItems()
    {
        return $this->hasMany(Item::class, "updated_by");
    }
    public function deletedItems()
    {
        return $this->hasMany(Item::class, "deleted_by");
    }
    ////////////////////////////
    public function insertedSubscribers()
    {
        return $this->hasMany(Subscriber::class, "inserted_by");
    }
    public function updatedSubscribers()
    {
        return $this->hasMany(Subscriber::class, "updated_by");
    }
    public function deletedSubscribers()
    {
        return $this->hasMany(Subscriber::class, "deleted_by");
    }
///////////////////

public function updatedOrders()
{
    return $this->hasMany(Order::class, "updated_by");
}
    protected $casts = ['password' => 'hashed'];
}
