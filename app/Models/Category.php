<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;
    // protected $fillable = ['name', 'description', 'color', "image"];

    public function items()
    {
        return $this->hasMany(Item::class, "category_id");
    }

    public function adminInserted()
    {
        return $this->belongsTo(Admin::class, "inserted_by");
    }
    public function adminDeleted()
    {
        return $this->belongsTo(Admin::class, "deleteded_by");
    }
    public function adminUpdated()
    {
        return $this->belongsTo(Admin::class, "updateded_by");
    }
}
