<?php
namespace App\Http\Controllers;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller {
    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     * @var string
     */
    protected $redirectTo = '/tasks';

    /**
     * Create a new authentication controller instance.
     * @return void
     */
    public function __construct() {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Get a validator for an incoming registration request.
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data) {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     * @param  array  $data
     * @return User
     */
    protected function create(array $data) {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function gettoken(){
        return csrf_token();
    }

    //handles all authentication actions (login, logout, register, verify, forgot password)
    public function login($action= false, $email = false) {
        $now = now(true);//seconds from epoch date
        $attempttime = 300;//5 minutes
        if(!count($_POST)){$_POST = $_GET;}
        if(!$action){$action = $_POST["action"];}
        $ret = array("Status" => true, "Action" => $action);
        if ($action == "logout") {
            foreach (array("id", "name", "email", "phone") as $Key) {
                write($Key, '');
            }
            \Session::save();
        } else if ($action == "verify" && isset($_POST["code"])){//verification code URL clicked
            $user = first("SELECT * FROM users WHERE authcode = '" . $_POST["code"] . "'");
            if($user){
                $user["authcode"] = "";
                insertdb("users", $user);
                die("Your account has been verified");
            } else {
                die("Code not found");
            }
        } else {//actions which require a user
            if(!$email){$email= trim($_POST["email"]);}
            $user = getuser($email);// first("SELECT * FROM users WHERE email = '" . $email . "'");
            $passwordmismatch = "Password and email address do not match a known account";
            if ($user) {
                switch ($action) {
                    case "registration":
                        $ret["Status"] = false;
                        $ret["Reason"] = "Email address is in use";
                        break;
                    case "verify":
                        $this->sendverifemail($email);
                    case "login":
                        if ($user["lastlogin"] >= ($now - $attempttime) && $user["loginattempts"] > 5) {
                            $ret["Status"] = false;//brute-force prevention
                            $ret["Reason"] = "Too many login attempts. Please wait 5 minutes";
                        } else if($user["authcode"]) {
                            $ret["Status"] = false;//require the user to be verified
                            $ret["Reason"] = 'Email address not verified. Please click the [verify] button in your email';
                        } else if (\Hash::check($_POST["password"], $user["password"])) {//login successful
                            unset($user["password"]);//do not send this to the user!
                            $ret["User"] = $user;
                            foreach ($user as $Key => $Value) {
                                write($Key, $Value);
                            }
                            \Session::save();
                            $ret["Token"] = csrf_token();
                        } else {//login failed
                            $ret["Status"] = false;
                            $ret["Reason"] = $passwordmismatch;//"Password mismatch";
                            $user["lastlogin"] = $now;
                            if ($user["lastlogin"] >= ($now - $attempttime)) {
                                $user["loginattempts"]++;
                            } else {
                                $user["loginattempts"]=1;
                            }
                            insertdb("users", $user);
                        }
                        break;
                    case "forgotpassword":
                        $user["password"] = $this->generateRandomString(6);
                        $user["mail_subject"] = "Forgot password";
                        $text = $this->sendEMail("email_forgotpassword", $user);
                        if($text){//email failed to send
                            $ret["Status"] = false;
                            $ret["Reason"] = $text;
                        } else {//only save change if email was sent
                            $ret["Reason"] = "A new password has been emailed to you";
                            $user["password"] = \Hash::make($user["password"]);
                            unset($user["mail_subject"]);
                            unset($user["Addresses"]);
                            unset($user["Others"]);
                            insertdb("users", $user);
                        }
                        break;
                }
            } else {
                switch ($action) {
                    case "registration":
                        $RequireAuthorization = false;
                        $oldpassword = $_POST["password"];

                        $address = $_POST["address"];
                        unset($address["formatted_address"]);
                        unset($_POST["action"]);
                        unset($_POST["_token"]);
                        unset($_POST["address"]);
                        $_POST["remember_token"]="";
                        if($RequireAuthorization) {
                            $_POST["authcode"] = $this->guidv4();
                        }
                        $_POST["created_at"] = now();
                        $_POST["updated_at"] = 0;

                        $_POST["password"] = \Hash::make($_POST["password"]);
                        $address["user_id"] = insertdb("users", $_POST);
                        insertdb("useraddresses", $address);

                        $actions = actions("user_registered");//phone sms email
                        foreach($actions as $action){
                            switch($action["party"]){
                                case 0://customer
                                    if($action["email"]){$this->sendverifemail($_POST["email"], $RequireAuthorization, $oldpassword);}
                                    if($action["phone"]){$this->sendSMS($_POST["phone"], $action["message"], true);}
                                    if($action["sms"]){$this->sendSMS($_POST["phone"], $action["message"]);}
                                    break;
                                case 1://admin
                                    if($action["email"]){
                                        $this->sendEMail("email_test", array(
                                            'mail_subject' => "A new user has registered",
                                            "email" => "admin",
                                            "body" => $_POST["name"] . " has registered"
                                        ));
                                    }
                                    if($action["phone"]){$this->sendSMS("admin", $action["message"], true);}
                                    if($action["sms"]){$this->sendSMS("admin", $action["message"]);}
                                    break;
                            }
                        }
                        break;
                    case "forgotpassword":
                        $ret["Status"] = false;
                        $ret["Reason"] = "Email address not found.";
                        break;
                    default:
                        $ret["Status"] = false;
                        $ret["Reason"] = $passwordmismatch;//"Email address not found."
                }
            }
        }
        die(json_encode($ret));
    }


    //make a GUID
    function guidv4() {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }
        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    //sends the verification email to the user
    function sendverifemail($email, $RequireAuthorization, $oldpassword){
        $user = first("SELECT * FROM users WHERE email = '" . $email . "'");
        $user["password"] = $oldpassword;
        $user["requiresauth"] = $RequireAuthorization;
        if($RequireAuthorization) {
            $user["mail_subject"] = "Please click the verify button";
        } else {
            $user["mail_subject"] = "You have successfully registered!";
            //$user["body"] = "Thank you for registering";
            //$text = $this->sendEMail("email_test", $user);
        }
        $text = $this->sendEMail("email_verify", $user);
        if($text){
            $ret["Status"] = false;
            $ret["Reason"] = $text;
        } else {
            $ret["Status"] = true;
            if($RequireAuthorization) {
                $ret["Reason"] = "Please click the Verify button in your email";
            } else {
                $ret["Reason"] = $user["mail_subject"];
            }
        }
        die(json_encode($ret));
    }
}
