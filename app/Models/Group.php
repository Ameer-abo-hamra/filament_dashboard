<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [        //     "whatsapp" => "string|min:10|max:10 ",
        "name",
        "description",
        "address",
        "deletable"

    ];

    public function sub()
    {
        return $this->belongsTo(Subscriber::class, 'sub_id');
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'group_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($group) {
            $group->items()->delete();
        });

        static::restoring(function ($group) {
            $group->items()->withTrashed()->restore();
        });
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
