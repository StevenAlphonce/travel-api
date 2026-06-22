<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

#[Signature('users:create')]
#[Description('Creates a new user')]
class CreateUserCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user['name'] = $this->ask('Name of the new user?');
        $user['email'] = $this->ask('Email of the new user?');
        $user['password']=$this->secret('Password of the new user?');
        $roleName=$this->choice('Which role would you like to create?', ['admin', 'user',1]);

        $role=Role::where('name',$roleName)->first();
        if(!$role){
            $this->error('Role not found');
            return-1;
        }

        $validate=validator($user,[
            'name'=>'required|string|max:255',
            'email'=>'required|string|email|max:255|unique:users',
            'password'=>'required|string|min:8|confirmed',
        ]);
        if($validate->fails()){
            foreach($validate->errors()->all() as $error){
                $this->error($error);
            }
            return -1;
        }
        DB::transaction(function () use($user,$role){
            $user['password']=Hash::make($user['password']);
            $newUser=User::create($user);
            $newUser->roles()->attach($role->id);
        });


        $this->info('User :'.$user['email'].' -created successfully.!');
    }
}
