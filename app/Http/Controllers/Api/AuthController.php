<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AccountVerificationNotification;
use App\Notifications\ForgotPasswordNotification;
use App\Notifications\WelcomeUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends BaseController
{
    protected function validateProvider($provider){
        if(!in_array($provider,['facebook','google'])){
            return $this->sendError('Error','Please login in using facebook or google',422);
        }
    }
    public function redirectToProvider($provider){
        $validated = $this->validateProvider($provider);
        if(!is_null($validated)){
            return $validated;
        }
        return Socialite::driver($provider)->stateless()->redirect();
    }
    public function solvedProviderCallback($provider){
        $validated = $this->validateProvider($provider);
        if(!is_null($validated)){
            return $validated;
        }
        try{
            $user = Socialite::driver($provider)->stateless()->user();
        }catch(\Exception $e){
            return $this->sendError('Error','Invalid credential provided',401);
        }
        $createdUser= User::firstOrCreate(['email'=>$user->getEmail()],[
            'name'=>$user->getName(),
            'email_verified_at'=>now(),
            'is_verified'=>1,
            'verify_code'=>mt_rand(100000, 999999)
        ]);
        $createdUser->provider()->updateOrCreate([
            'provider'=>$provider,
            'provider_id'=>$user->getId()
        ],['avatar'=>$user->getAvatar()]
        );
          $token =$createdUser->createToken('token-word');
        
        $data = ['user' =>$user, 'token' => $token->plainTextToken];
        return $this->sendSuccess($data,'Loggedin successful');
    }
    public function login(Request $request){
        $validator = Validator::make($request->all(),['email' => 'required|email','password' => 'required']);

        if ($validator->fails()) {
            return $this->sendError('Validation Error',[ $validator->errors()],403);
        }
        $ip = $request->getClientIp();
        $email = $request['email'];
        $password = $request['password'];
        $tokenword = $email.Carbon::now().$password;
        $user = User::where('email', $email)->first();
        if($user){
            if(Hash::check($password,$user->password)){
                Auth::login($user);
                $user->last_login_at = Carbon::now()->toDateTimeString();
                $user->last_login_ip = $ip;
                $user->save();
                $token =$user->createToken($tokenword);

                $data = ['user' =>$user, 'token' => $token->plainTextToken];
                // $tokenData =  $token->plainTextToken;
                return $this->sendSuccess($data,'Loggedin successful');
            } else {
                return $this->sendError('failed','invalid email or password');
            }
        }else{
            return $this->sendError('failed','Account does not exist');
        }
    }
    public function register(Request $request){
          $validator = Validator::make($request->all(),['name'=>'required','email' => 'required|email','password' => 'required']);

        if ($validator->fails()) {
            return $this->sendError('Validation Error',[ $validator->errors()],403);
        }
        $email = $request['email'];
        $user = User::where('email',$email)->first();
        if (!empty($user)) {
               return $this->sendError('failed','Account already exist, pls login');
        }
        $verified_code = mt_rand(100000, 999999);
        $tokenword = $email.Carbon::now().$request['password'];
         $ip = $request->getClientIp();
        try{
            $user = User::create([
                'name'=>$request['name'],
                'email'=>$request['email'],
                'password'=>$request['password'],
                'verify_code'=>$verified_code,
                'last_login_at' => Carbon::now()->toDateTimeString(),
                'last_login_ip' =>$ip
            ]);
             Auth::login($user);
            $user->attachRole('User');
            $token = $user->createToken($tokenword);
            $when = Carbon::now()->addMinutes(1);
            try {
                $user->notify(new WelcomeUser($user))->delay($when);

            } catch (\Exception $e) {
                Log::debug('Error sending mail to '.$user->email);
            }

            $data = ['user' =>$user,  'token' => $token->plainTextToken];
            return $this->sendSuccess($data,'Account created successfully');
        }catch(\Exception $e){
              Log::debug('Error registing '.$e->getMessage());
              return $this->sendError('failed','Account not created, try again');
        }
    }
    public function forgotPassword(Request $request){
         $validator = Validator::make($request->all(),['email' => 'required|email']);

        if ($validator->fails()) {
            return $this->sendError('Validation Error',[ $validator->errors()],403);
        }
        $email = $request['email'];
        $user = User::where(['email'=>$email])->first();

        $verify_code = mt_rand(100000, 999999);
		if(!empty($user)){
            $user->verify_code = $verify_code;
            $user->save();
            $delay = Carbon::now()->addMinutes(1);
            try {
                $user->notify((new ForgotPasswordNotification($user))->delay($delay));

                return $this->sendSuccess($user,'An email has been sent to '. $user->email);

            } catch (\Exception $e) {
                 Log::debug('Error registing '.$e->getMessage());
                 return $this->sendError('Error','Mail to'. $user->email." try again");
            }
        }
        return $this->sendError( 'Error','This account does not exist');
    }
    public function changePasswordWithCode(Request $request){
         $validator = Validator::make($request->all(),['email' => 'required|email', 'code'=>'required', 'password' => 'required']);

        if ($validator->fails()) {
            return $this->sendError('Validation Error',[ $validator->errors()],403);
        }
        $code = $request['code'];
        $email = $request['email'];
        $user = User::where(['email'=>$email])->first();
		if(!empty($user)){
            if($code == $user->verify_code){
                $user->password = $request['password'];
                $user->save();
                return $this->sendSuccess($user,'password changed successfully');
		    }else{
                return $this->sendError('Error','incorrect code,enter the correct code sent to '.$email);
		    }

        }
       return $this->sendError('Error','Account does not exist');
    }

     public function verifyEmail(Request $request){
          $validator = Validator::make($request->all(),['code' => 'required']);

        if ($validator->fails()) {
            return $this->sendError('Validation Error',[ $validator->errors()],403);
        }
    	$code = $request['code'];
        $user_id = Auth::id();
        $user = User::where(['id'=>$user_id])->first();
		if($code == $user->verify_code){
			$user->is_verified = 1;
            $user->save();
            // $data = ['is_verified'=>$user->is_verified,'user'=>$user];
            return $this->sendSuccess($user,'code matched');
		}
        return $this->sendError('Error','Wrong code, enter the code sent to yr mail');

    }
    public function resendVerificationCode(){
         $user_id = Auth::id();
        $user = User::where('id',$user_id)->first();
        $verify_code = mt_rand(100000, 999999);
        if(!empty($user)){
            $user->verify_code = $verify_code;
            $user->save();

            $when = Carbon::now()->addMinutes(1);
            try {
                $user->notify(new AccountVerificationNotification($user));
                return $this->sendSuccess($user->email,'An email has been sent to '. $user->email);

            } catch (\Exception $e) {
                 return $this->sendError('Error',' Error sending mail, try again');
            }
        }
        return $this->sendError('Error', $user->email.' does not exist');
    }
    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();

        return $this->sendSuccess(true,'Successfullylogout');
    }
}
