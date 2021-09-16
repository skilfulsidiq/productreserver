<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends BaseController
{
    //admin functionalities

    public function allUsers(){
        $users = User::all();
        return $this->sendSuccess($users,'All users');
    }
    public function updateUser(Request $request,$slug=null){
        $validator = Validator::make($request->all(),['name' => 'required','email'=>'required|email','password'=>'required']);
            if ($validator->fails()) {
                return $this->sendError('Validation Error',[ $validator->errors()],403);
            }
            $only = $request->only(['name','email','password']);
        $user = User::updateOrCreate(['slug'=>$slug],$only);
        if($request['role']){
            $user->attachRole($request['role']);
        }
        if($user){
            return $this->sendSuccess($user,'user info updated');
        }
        return $this->sendError('fail','unable to update user');
    }
    public function deleteUser($slug){
        $user = User::where('slug',$slug)->first();
         if($user->delete()){
            return $this->sendSuccess($user,'user info deleted');
        }
        return $this->sendError('fail','unable to delete user');
    }
}
