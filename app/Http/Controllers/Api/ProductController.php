<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\Product;
use App\Models\User;
use App\Models\ProductReserve;
use App\Models\ProductGallery;
use App\Traits\UploadAble;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProductController extends BaseController
{
    use UploadAble;
    /**
     * Admin funcitonalities
     */
    //all products
    public function adminDashboard(){

        $product = Product::count();
        $user = User::count();
        $reserved = ProductReserve::count();
        $data = ['total_products'=>$product,'total_users'=>$user,'total_reserveds'=>$reserved];
         return $this->sendSuccess($data,'Dashboard data');
    }

    //Admin add or update product
    public function addOrUpdateProduct(Request $request,$slug=null){

            $validator = Validator::make($request->all(),['product_name' => 'required','product_price'=>'required','product_description'=>'required']);
            if ($validator->fails()) {
                return $this->sendError('Validation Error',[ $validator->errors()],403);
            }

            $new_name = "";
            if($slug==null){
                $validator_two = Validator::make($request->all(),[
                    'image.*'=>'required|mimes:png,jpg,jpeg|max:1048'
                ]);
                if ($validator_two->fails()) {
                    return $this->sendError('Validation Error',[ $validator->errors()],403);
                }

            }
            // $images = [];
            // if($request['images']){
            //     $images = $request['images'];
            // }


        $only = $request->only(['product_name','product_discount', 'product_price','product_cover_image','product_description', 'discount_start_date','discount_end_date']);
        $only['discount_start_date'] = $this->formatIncomingDate($request['discount_start_date']);
        $only['discount_end_date'] = $this->formatIncomingDate($request['discount_end_date']);
        if($request->hasFile('image')){
                    $img = $request['image'];
                    $path = $this->uploadFile($img,'products');
                    $only['product_cover_image'] = $path;
                }
        try {
            DB::beginTransaction();
             $product = Product::updateOrCreate(['slug'=>$slug], $only);
            //   dd($images);
             // if(!empty($images)){
             //    foreach($images as $img){
             //         $path = $this->uploadFile($img,'products');
             //         $p = ProductGallery::create([
             //            'product_id'=>$product->id,
             //            'file_url'=>$path
             //         ]);


             //     }
             // }
            DB::commit();
            return $this->sendSuccess($product,'product updated successfully');
        } catch (\Exception $th) {
            DB::rollBack();
            Log::debug("adding product: ".$th->getMessage());
            return $this->sendError('Error','unable to add or update product');
        }
    }
    //delete a product using
    public function deleteProduct($slug){
        $product = Product::where('slug', $slug)->first();
        $product->users()->delete();
        // $res = ProductReserve::where('product_id',$product->id)->get();
        // if(!empty($res)){
        //     foreach($res as $r){
        //         $r->delete();
        //     }
        // }
        if($product->delete()){
            return $this->sendSuccess($product,'product deleted successfully');
        }
        return $this->sendError('Error', 'Error deleting product, try again');
    }

    /**All users */
    public function productinatedPagList(){
        $products = Product::with('gallery')->orderBy('created_at','desc')->paginate(10);
        return $this->sendSuccess($products,"All Products");
    }
    public function productList(){
        $products = Product::with('gallery')->orderBy('created_at','desc')->get();
        return $this->sendSuccess($products,"All Products");
    }

    protected function formatIncomingDate($date){
        if($date !=''){
            $b = Carbon::now();
            if(Carbon::hasFormatWithModifiers($date,'d/m/Y')){
                $b = Carbon::createFromFormat('d/m/Y', $date);

            }elseif(Carbon::hasFormatWithModifiers($date,'Y/m/d')){
            $b = Carbon::createFromFormat('Y/m/d', $date);
            }
            elseif(Carbon::hasFormatWithModifiers($date,'Y-m-d')){
            $b = Carbon::createFromFormat('Y-m-d', $date);
            }
            else{
                $b = Carbon::createFromFormat('d-m-Y', $date);
            }
            return $b;
            }

        return null;
    }
}
