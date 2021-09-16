<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductGallery extends Model
{
    use HasFactory;
    protected $fillable = ['product_id','file_url'];

    public function product(){
        return $this->belongsT0(Product::class,'product_id')->withDefault();
    }

    public function getFileUrlAttribute(){
        return url($this->file_url);
    }
}
