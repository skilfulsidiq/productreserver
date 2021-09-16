<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //admin functionalities

    public function allUsers(){}
    public function updateUser(Request $request,$slug=null){}
    public function deleteUser($slug){}
}
