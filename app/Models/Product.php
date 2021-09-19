<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class Product extends Model
{
    use HasFactory;
      protected $fillable =['product_name','product_discount', 'product_price','product_cover_image','product_description', 'discount_start_date','discount_end_date', 'slug'];

     // protected $hidden = ['app_price'];
      protected $appends = ["description_short","is_discount_active","price_before_discount","price_after_discount", "price_after_discount_numeric", "in_reservedlist"];
    /**
     * Mutators
     */
    public function setProductNameAttribute($v){
        $this->attributes['product_name'] = $v;
        $this->attributes['slug'] = Str::slug($v);
    }
    public function setProductPriceAttribute($value){
        $this->attributes['product_price'] = (int)str_replace(',','',$value);
    }

    // public funciton getProductCover
     public function setProductCoverImageAttribute($value){
        $this->attributes['product_cover_image'] = url($value);
    }
    /**
     * Relationship
     */

    public function users(){
        return $this->belongsToMany(User::class,'product_reserves','product_id','user_id');
    }
    public function gallery(){
        return $this->hasMany(ProductGallery::class,'product_id');
    }

    //Accessors
      public function getDescriptionShortAttribute(){
        return mb_substr(strip_tags($this->product_description), 0, 70, 'utf-8');
    }

    public function getIsDiscountActiveAttribute(){
        if($this->product_discount > 0) {
            if($this->discount_start_date && $this->discount_end_date) {
                if($this->discount_start_date <= date("Y-m-d") && $this->discount_end_date >= date("Y-m-d")) {
                    return true;
                }

                return false;
            } else if($this->discount_start_date && !$this->discount_end_date) {
                if($this->discount_start_date <= date("Y-m-d")) {
                    return true;
                }

                return false;
            } else if(!$this->discount_start_date && $this->discount_end_date) {
                if($this->discount_end_date >= date("Y-m-d")) {
                    return true;
                }

                return false;
            }
        }

        return false;
    }

    public function getPriceAfterDiscountAttribute(){
        if($this->getIsDiscountActiveAttribute()) {
            return number_format($this->product_price - ($this->product_price * ($this->product_discount / 100)));
        }

        return number_format($this->product_price);
    }
    public function getPriceBeforeDiscountAttribute(){
        return number_format($this->product_price);
    }

    public function getPriceAfterDiscountNumericAttribute(){
        if($this->getIsDiscountActiveAttribute()) {
            return $this->product_price - ($this->product_price * ($this->product_discount / 100));
        }

        return $this->product_price;
    }

    public function getInReservedlistAttribute(){
        return false;
    }
}
