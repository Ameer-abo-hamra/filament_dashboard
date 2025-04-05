<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
class  Subscriber extends Authenticatable implements JWTSubject
{
    use HasFactory, SoftDeletes, Notifiable;

    protected $fillable = ['email', 'username', 'password', 'full_name', 'id', 'verification_code', 'mobile', 'gender', 'address', 'birthdate', 'nationality', 'reset_token', "address","country_code_id"] ;

    protected $hidden = ['password'];

    public function groups()
    {
        return $this->hasMany(Group::class, 'sub_id');
    }

    public function countryCode() {
        return $this->belongsTo(CountryCode::class , "country_code_id");
    }
    public function orders()
    {
        return $this->hasMany(Order::class, 'sub_id');
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

    public function cart() {
        return $this->hasOne(Cart::class , "sub_id");
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
