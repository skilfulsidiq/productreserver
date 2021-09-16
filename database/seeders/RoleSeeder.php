<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = ['Admin','User'];
        foreach($roles as $role){
            Role::create(['name'=>$role,'display_name'=>$role,'description'=>'description for '.$role]);
        }
        $admin = User::create([
            'name'=>'Super Admin',
            'email'=>'admin@test.com',
            'password'=>'password'
        ]);
        $admin->attachRole('Admin');

    }
}
