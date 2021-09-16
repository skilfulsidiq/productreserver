<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    /**
     * Admin funcitonalities
     */
    //all products
    public function adminProductList(){}

    //Admin add or update product
    public function addOrUpdateProduct(Request $request,$slug=null){}
    //delete a product using
    public function deleteProduct($slug){}

    /**All users */
    public function productList(){}
}
