<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\VerifyUser;
use App\AccountMaster;
use Mail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;


class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|regex:/[@]/|regex:/[.]/|unique:users',
            //'password' => 'required|string|min:8|confirmed',
            'password' => 'required|min:8|max:100|regex:/[A-Z]/|regex:/[@#$%&*]/|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    
    public function verify_email($data){
      $name = $data['name'];
      $email = $data['email'];
      $token = $data['token'];
      $imie = '';
      $nazwisko='';$password='';
      
      //Mail::raw('Please <a href="http://localhost/dialdesk_new/public/verify_email_register?token='.$imie.'"></a>Click Here For Verify You Email Address</a>',  function($message) use ($name, $imie, $nazwisko, $email, $password) {
      Mail::send(['name'=>'123'],$data,  function($message) use ($name, $imie, $nazwisko, $email, $password,$token) {
         $message->to($email, $name)
                 ->subject('Dialdesk Email Verification');
                 //$message->setBody('<h2>Welcome to the site '.$name.'</h2>  <br/><br/>Please click on the below link to verify your email account <br/><a href="http://localhost/dialdesk_new/public/verify_email_register?token='.base64_encode($id).'">Verify Email</a>','text/html');
                 $message->setBody('<h2>Welcome to the site '.$name.'</h2>  <br/><br/>Please click on the below link to verify your email account <br/><a href="'.url(config('app.url')).'/user/verify/'.$token.'">Verify Email</a>','text/html');
         $message->from('ispark@teammas.in','Dialdesk');
      });
      return "Basic Email Sent. Check your inbox.";
   }
    
    
    protected function create(array $data)
    {
        
        $UserRegisiter =  User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'password2' => $data['password'],
            //'UserName' => Hash::make($data['UserName']),
        ]);
        
        $AccountMaster = AccountMaster::create(['Client_Id'=>$UserRegisiter->id]);
        
        $token = str_random(40);
        
        $verifyUser = VerifyUser::create([
            'user_id' => $UserRegisiter->id,
            'token' => $token
        ]);
        
        
        
        $id = $UserRegisiter->id;
        $data['token'] = $token;
        $this->verify_email($data);
        return $UserRegisiter;
    }
    
    
    
    public function verifyUser($token)
    {
        //echo $token; exit;
        $verifyUser = VerifyUser::where('token', $token)->first();
        if(isset($verifyUser) ){
            $user = $verifyUser->user;
            if(!$user->verify_email || $user->verify_email=='No') {
                $verifyUser->user->verify_email = 'Yes';
                $verifyUser->user->verified_at = date("Y-m-d H:i:s");
                $verifyUser->user->save();
                $status = "Your e-mail is verified. You can now login.";
            }else{
                $status = "Your e-mail is already verified. You can now login.";
            }
        }else{
            return redirect('/login')->with('warning', "Sorry your email cannot be identified.");
        }

        return redirect('/login')->with('status', $status);
    }
    
    protected function registered(Request $request, $user)
    {
        $this->guard()->logout();
        return redirect('/login')->with('status', 'We sent you an activation code. Check your email and click on the link to verify.');
    }   
    
    
    public function password_reset_email($data){
      $name = $data['name'];
      $email = $data['email'];
      $token = $data['token'];
      $imie = '';
      $nazwisko='';$password='';
      
      //Mail::raw('Please <a href="http://localhost/dialdesk_new/public/verify_email_register?token='.$imie.'"></a>Click Here For Verify You Email Address</a>',  function($message) use ($name, $imie, $nazwisko, $email, $password) {
      Mail::send(['name'=>'123'],$data,  function($message) use ($name, $imie, $nazwisko, $email, $password,$token) {
         $message->to($email, $name)
                 ->subject('Dialdesk Password Reset');
                 //$message->setBody('<h2>Welcome to the site '.$name.'</h2>  <br/><br/>Please click on the below link to verify your email account <br/><a href="http://localhost/dialdesk_new/public/verify_email_register?token='.base64_encode($id).'">Verify Email</a>','text/html');
                 $message->setBody('<h2>Welcome to the site '.$name.'</h2>  <br/><br/>Please click on the below link to reset credentials <br/><a href="http://localhost/dialdesk_new/public/user/verify/'.$token.'">Verify Email</a>','text/html');
         $message->from('ispark@teammas.in','Dialdesk');
      });
      return "Basic Email Sent. Check your inbox.";
   }
    
    protected function resetPasswordReq(array $data)
    {
        
        $UserRegisiter =  User::where('email',$data['email']);
        if(isset($UserRegisiter))
        {
            $token = str_random(40);
            $verifyUser = VerifyUser::create([
            'user_id' => $UserRegisiter->id,
            'token' => $token,
                'token_for' => 'reset password',
            ]);
            $data['token'] = $token;
            $data['name'] = $UserRegisiter->name;
            $this->password_reset_email($data);
            return redirect('/login')->with('status', "We sent you an reset link. Check your email and click on the link to reset password.");
        }
        else{
            return redirect('/login')->with('warning', "Sorry your email cannot be identified.");
        }
        
        
        
        
        
        
        
        return $UserRegisiter;
    }
    
}
