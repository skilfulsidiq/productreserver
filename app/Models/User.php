<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laratrust\Traits\LaratrustUserTrait;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, LaratrustUserTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'verify_code',
        'is_verified',
        'last_login_at',
        'last_login_ip',
        'slug'
    ];
    protected $appends=['is_admin'];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    /**
       * Mutations
    **/
    public function setNameAttribute($v){
        $this->attributes['name'] = $v;
        $this->attributes['slug'] = Str::slug($v);
    }
     public function setPasswordAttribute($value){
        $this->attributes['password'] = Hash::make($value);
    }

    /**Acceser */
    public function getIsAdminAttribute(){
        return $this->hasRole('Admin');
    }
    public function getIsVerifiedAttribute(){
        if($this->attributes['is_verified']==1){
            return true;
        }
        return false;
    }
    /**
     * Relationship
     */
    public function products(){
        return $this->belongsToMany(Product::class,'product_reserves','user_id','product_id');
    }
    public function provider(){
        return $this->hasMany(Provider::class,'user_id');
    }
}
