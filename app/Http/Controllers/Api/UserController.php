<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends BaseController
{
    //admin functionalities
    public function allRoles(){
        $roles= Role::all(['id','name']);
        return $this->sendSuccess($roles,'All roles');
    }
    public function allUsers(){
        $users = User::all();
        return $this->sendSuccess($users,'All users');
    }
    public function updateUser(Request $request,$slug=null){
        $validator = Validator::make($request->all(),['name' => 'required','email'=>'required|email']);
            if ($validator->fails()) {
                return $this->sendError('Validation Error',[ $validator->errors()],403);
            }
            $only = $request->only(['name','email']);
            if($slug==null){
                $validator_two = Validator::make($request->all(),[
                    'password'=>'required'
                ]);
                if ($validator_two->fails()) {
                    return $this->sendError('Validation Error',[ $validator->errors()],403);
                }
                $only['password'] = $request['password'];

            }
            
        $user = User::updateOrCreate(['slug'=>$slug],$only);
        if($request['role']){
            $user->attachRole($request['role']);
        }else{
             $user->attachRole('User');
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
