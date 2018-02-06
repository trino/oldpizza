<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController {
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    //sends an email using a template
    public function sendEMail($template_name = "", $array = array()){
        if (isset($array["message"])) {
            $array["body"] = $array["message"];
            unset($array["message"]);
        }
        if (isset($array['email']) && is_array($array['email'])) {
            $emails = $array['email'];
            foreach ($emails as $email) {
                $array["email"] = $email;
                $this->sendEMail($template_name, $array);
            }
        } else if (isset($array['email']) && $array['email']) {
            if (!isset($array['mail_subject'])) {
                $array['mail_subject'] = "[NO mail_subject SET!]";
            }
            if ($array['email'] == "admin") {
                $array['email'] = first("SELECT email FROM users WHERE profiletype = 1")["email"];
            }
            try {
                \Mail::send($template_name, $array, function ($messages) use ($array, $template_name) {
                    $messages->to($array['email'])->subject($array['mail_subject']);
                });
            } catch (\Swift_TransportException $e) {
                $text = $e->getMessage();
                $email = view($template_name, $array);
                debugprint($template_name . " EMAIL TO " . $array['email'] . " FAILED: " . $text . "<P>" . $email);
                return "Email error: " . $text;
            }
        } else {
            $array["template_name"] = $template_name;
            var_dump($array);
            die();
        }
    }

    //is data JSON-parseable?
    function isJson($string){
        if ($string && !is_array($string)) {
            json_decode($string);
            return (json_last_error() == JSON_ERROR_NONE);
        }
    }

    //used for making raw HTTP requests
    function cURL($URL, $data = "", $username = "", $password = ""){
        $session = curl_init($URL);
        curl_setopt($session, CURLOPT_HEADER, false);
        curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);//not in post production
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($session, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($session, CURLOPT_POSTFIELDS, $data);
        }

        $datatype = "x-www-form-urlencoded;charset=UTF-8";
        if ($this->isJson($data)) {
            $datatype = "json";
        }
        $header = array('Content-type: application/' . $datatype, "User-Agent: Charlies");
        if ($username && $password) {
            $header[] = "Authorization: Basic " . base64_encode($username . ":" . $password);
        } else if ($username) {
            $header[] = "Authorization: Bearer " . $username;
            $header[] = "Accept-Encoding: gzip";
        } else if ($password) {
            $header[] = "Authorization: AccessKey " . $password;
        }
        curl_setopt($session, CURLOPT_HTTPHEADER, $header);

        $response = curl_exec($session);
        if (curl_errno($session)) {
            $response = "Error: " . curl_error($session);
        }
        curl_close($session);
        return $response;
    }

    //https://www.twilio.com/ $0.0075 per SMS, + $1 per month
    public function sendSMS($Phone, $Message, $Call = false){
        if (is_array($Phone)) {
            foreach ($Phone as $PhoneNumber) {
                $this->sendSMS($PhoneNumber, $Message, $Call);
            }
            return true;
        }
        $ret = iif($Call, "Calling", "Sending an SMS to") . ": " . $Phone . " - " . $Message;
        if ($Phone == "admin") {
            $Phone = first("SELECT phone FROM users WHERE profiletype = 1");
        } else {
            $Phone = filternonnumeric($Phone);
        }
        if (islive() && $Phone !== "9055123067" && $Phone) {//never call me
            $sid = 'AC81b73bac3d9c483e856c9b2c8184a5cd';
            $token = "3fd30e06e99b5c9882610a033ec59cbd";
            $fromnumber = "2897685936";
            if ($Call) {
                //$Message = "http://" . serverurl . "/call?message=" . urlencode($Message);
                $Message = "http://londonpizza.ca/call?message=" . urlencode($Message);
                $URL = "https://api.twilio.com/2010-04-01/Accounts/" . $sid . "/Calls";
                $data = array("From" => $fromnumber, "To" => $Phone, "Url" => $Message);
            } else {
                $URL = "https://api.twilio.com/2010-04-01/Accounts/" . $sid . "/Messages";
                $data = array("From" => $fromnumber, "To" => $Phone, "Body" => $Message);
            }
            // debugprint($ret);
            return $this->cURL($URL, http_build_query($data), $sid, $token);
        }
        debugprint('ERROR - ' . $ret . " - Is not live/valid or is a blocked number, did not contact");
    }
}
