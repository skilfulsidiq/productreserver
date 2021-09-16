<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductReserve;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReserveProductController extends BaseController
{
    //
    //user reserve a product

    public function reserveForAProduct($product_id){
        $user = User::where('id',Auth::id())->first();
        // $product_id = $request['product_id'];
        // $product= Product::where('id',$product_id)->first();
        $user->products()->syncWithoutDetaching([$product_id]);
        return $this->sendSuccess('Product reserved','Product reserve');
    }
    public function userReservedProducts(){
         $user = Auth::user();
         $products = $user->products;
         return $this->sendSuccess($products,'All Products reserved');
    }

    //Admin list of all reserved products
    public function allReservedProducts(){
          $products = ProductReserve::with('product','user')->orderBy('created_at','desc')->get();
         return $this->sendSuccess($products,'All Products reserved');
    }
}
