<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Task;
use App\Repositories\TaskRepository;

class HomeController extends Controller {
    public function index(Request $request){
        return view("home_keyword")->render();
    }

    public function help(Request $request){
        return view("home_help")->render();
    }

    public function tablelist($table){
        if (isset($_POST["action"])) {
            switch ($_POST["action"]) {
                case "testemail":
                    return $this->sendEMail("email_test", array(
                        'mail_subject' => "test",
                        "email" => "roy@trinoweb.com"
                    ));
                    break;
            }
        }
        return view("home_list", array("table" => $table))->render();
    }

    public function edituser($user_id = false){
        if (!$user_id) {
            $user_id = read("id");
        }
        return view("home_edituser", array("user_id" => $user_id))->render();
    }

    public function robocall(Request $request){
        echo '<?xml version="1.0" encoding="UTF-8"?><Response><Say voice="woman" language="en">' . $_GET["message"] . '</Say></Response>';
        die();
    }

    public function edit(Request $request){
        return view("home_editor")->render();
    }

    public function edittable(Request $request){
        return view("home_edittable")->render();
    }

    public function editmenu(Request $request){
        return view("home_editmenu")->render();
    }

    public function placeorder($POST = ""){
        if (!read("id")) {
            return array("Status" => false, "Reason" => "You are not logged in");
        }
        date_default_timezone_set("America/Toronto");
        $info = "";
        if (isset($_POST["info"])) {
            $info = $_POST["info"];
            unset($info["formatted_address"]);
        }
        if (is_array($POST)) {
            $_POST = $POST;
        }
        if (isset($_POST["action"])) {
            $ret = array("Status" => true, "Reason" => "");
            switch ($_POST["action"]) {
                case "deletecard":
                    initStripe();
                    $user = first("SELECT * FROM users WHERE id = " . read("id"));
                    try {
                        $ret["Status"] = false;
                        $cu = \Stripe\Customer::retrieve($user["stripecustid"]);
                        $cu->sources->retrieve($_POST["cardid"])->delete();
                        $ret["Reason"] = "'" + $_POST["cardid"] . "' was deleted";
                        $ret["Status"] = true;
                    } catch (\Stripe\Error\InvalidRequest $e) {
                        $ret["Reason"] = $e->getMessage();
                    }
                    break;
                case "closestrestaurant":
                    $ret["closest"] = $this->closestrestaurant($info, true);
                    break;
                case "changestatus":
                    if ($_POST["status"] == -1) {//email out
                        $info = false;
                        $user = $this->order_placed($_POST["orderid"], $info, 0);
                        $ret["Reason"] = "Receipt for order ID " . $user["orderid"] . " sent to '" . $user["email"] . "'";
                    } else {
                        $Status = array("Pending", "Confirmed", "Declined", "Delivered", "Canceled");
                        insertdb("orders", array("id" => $_POST["orderid"], "status" => $_POST["status"]));
                        $Status = $Status[$_POST["status"]];
                        $ret["Reason"] = "Order #" . $_POST["orderid"] . ": " . $Status;
                        if (debugmode) {
                            $ret["Reason"] .= " - Action: order_" . strtolower($Status);
                        }
                        $info = false;
                        $this->order_placed($_POST["orderid"], $info, -1, "order_" . strtolower($Status), $_POST["reason"]);
                    }
                    break;
                default:
                    $ret["Status"] = false;
                    $ret["Reason"] = "'" . $info["action"] . "' is unhandled";
            }
            return json_encode($ret);
        }
        if (!isset($info["user_id"]) || !$info["user_id"]) {
            $info["user_id"] = read("id");
        }
        $addressID = $this->processaddress($info);
        if (isset($_POST["order"])) {
            /*
            $restaurant = $this->closestrestaurant($info,true);
            if(!isset($restaurant["id"])){return false;}
            $info["restaurant_id"] = $restaurant["id"];
            */

            $info["placed_at"] = now();
            unset($info["name"]);
            unset($info["creditcard"]);
            unset($info["restaurant"]);
            if (isset($_POST["stripe"])) {
                $info["stripeToken"] = $_POST["stripe"];
            }

            $order = $_POST["order"];
            unset($info["istest"]);
            if(!isset($info["phone"]) || !trim($info["phone"])){
                $info["phone"] = read("phone");
            }
            $orderid = insertdb("orders", $info);
            $dir = resource_path("orders");//no / at the end
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            file_put_contents($dir . "/" . $orderid . ".json", json_encode($order, JSON_PRETTY_PRINT));
            $user = $this->order_placed($orderid, $info, -2);//get user data without processing the event
            if ($user["name"] != $_POST["name"] || $user["phone"] != $_POST["phone"]) {
                $user["name"] = $_POST["name"];
                $user["phone"] = $_POST["phone"];
                insertdb("users", array("id" => $info["user_id"], "name" => $_POST["name"], "phone" => $_POST["phone"]));//attempt to update user profile
            }

            $HTML = view("popups_receipt", array("orderid" => $orderid, "timer" => true, "place" => "placeorder", "style" => 2, "includeextradata" => true, "party" => "user"))->render();//generate total
            //if ($text) {return $text;} //shows email errors. Uncomment when email works
            if (isset($info["stripeToken"]) || $user["stripecustid"]) {//process stripe payment here
                $amount = select_field_where("orders", "id=" . $orderid, "price");
                if($GLOBALS["settings"]["onlyfiftycents"]) {
                    $amount = 50;//dont remove this
                } else if (strpos($amount, ".")) {
                    $amount = $amount * 100;//remove the period, make it in cents
                }
                $error = false;
                if ($amount > 0) {
                    initStripe();
                    try {
                        if ($user["stripecustid"]) {
                            $customer_id = $user["stripecustid"];//load customer ID from user profile
                            if (isset($info["stripeToken"]) && $info["stripeToken"]) {//update credit card info
                                $cu = \Stripe\Customer::retrieve($customer_id);
                                $cu->source = $info['stripeToken']; //obtained with Checkout
                                $cu->save();
                            }
                        } else {
                            $customer = \Stripe\Customer::create(array(
                                "source" => $info["stripeToken"],
                                "description" => $user["name"] . ' (ID:' . $user["id"] . ')'
                            ));
                            $customer_id = $customer["id"];
                            insertdb("users", array("id" => $user["id"], "stripecustid" => $customer_id));//attempt to update user profile
                        }

                        $charge = array(
                            "amount" => $amount,
                            "currency" => "cad",
                            //"source" => $info["stripeToken"],//charge card directly
                            "customer" => $customer_id,//charge customer ID
                            "description" => "Order ID: " . $orderid
                        );
                        if (isset($_POST["creditcard"]) && $_POST["creditcard"]) {
                            $charge["source"] = $_POST["creditcard"];//charge a specific credit card
                        }
                        // https://stripe.com/docs/charges https://stripe.com/docs/api
                        $charge = \Stripe\Charge::create($charge);// Create the charge on Stripe's servers - this will charge the user's card
                        insertdb("orders", array("id" => $orderid, "paid" => 1));//will only happen if the $charge succeeds
                        $this->order_placed($orderid, $info);
                    } catch (Stripe_CardError $e) {
                        $error = $e->getMessage();
                    } catch (Stripe_InvalidRequestError $e) {
                        $error = $e->getMessage();//Invalid parameters were supplied to Stripe's API
                    } catch (Stripe_AuthenticationError $e) {
                        $error = $e->getMessage();//Authentication with Stripe's API failed
                    } catch (Stripe_ApiConnectionError $e) {
                        $error = $e->getMessage();//Network communication with Stripe failed
                    } catch (Stripe_Error $e) {
                        $error = $e->getMessage();//Display a very generic error to the user
                    } catch (Exception $e) {
                        $error = $e->getMessage();//Something else happened, completely unrelated to Stripe
                    } catch (\Stripe\Error\Card $e) {
                        $error = $e->getMessage();
                    }
                } else {
                    $error = " Order ID " . $orderid . " total was $0.00";
                }
                if ($error) {
                    debugprint("Order ID: " . $orderid . " - Stripe error: " . $error);
                    return $error;// The card has been declined
                }
            }
            return '<div CLASS="ordersuccess" addressid="' . $addressID . '"></div>' . $HTML;
        } else {
            return $addressID;
        }
    }

    function order_placed($orderid, &$info = false, $party = -1, $event = "order_placed", $Reason = ""){
        if (!$info) {
            $info = first("SELECT * FROM orders WHERE id = " . $orderid);
        }
        $user = first("SELECT * FROM users WHERE id = " . $info["user_id"]);
        $admin = first("SELECT * FROM users WHERE profiletype = 1");
        if ($party > -2) {
            $actions = actions($event, $party);
            if ($party > -1) {
                $actions = array($actions);
            }
            foreach ($actions as $action) {
                $phone_restro = false;
                switch ($action["party"]) {
                    case 0://customer
                        $party = "customer";
                        $email = $user["email"];
                        $phone = $user["phone"];
                        $name = $user["name"];
                        break;
                    case 1://admin
                        $party = "admin";
                        $email = $admin["email"];
                        $phone = $admin["phone"];
                        $name = $admin["name"];
                        break;
                    case 2://restaurant
                        $restaurant = $this->processrestaurant($info["restaurant_id"]);
                        $party = "restaurant";
                        $email = $restaurant["user"]["email"];
                        $name = $restaurant["restaurant"]["name"];
                        $phone_restro = filternonnumeric($restaurant["restaurant"]["phone"]);
                        $phone = filternonnumeric($restaurant["user"]["phone"]);
                        break;
                }


                if ($Reason) {
                    $action["message"] = str_replace("[reason]", $Reason, $action["message"]);
                }

                $action["message"] = str_replace("[orderid]", $orderid, $action["message"]);
                $message = str_replace("[sitename]", sitename, $action["message"]);
                $message = str_replace("[name]", $name, $message);
                if ($action["email"]) {
                    $action["message"] = str_replace("[url]", "", $message);
                    debugprint("Sending email to " . $party . ": " . $email);
                    $this->sendEMail("email_receipt", ["orderid" => $orderid, "email" => $email, "party" => $party, "mail_subject" => $action["message"]]);//send emails to customer also generates the cost
                }
                if ($action["sms"]) {
                    $action["message"] = str_replace("[url]", webroot("list/orders?action=getreceipt&orderid=") . $orderid, $message);//http://localhost/ai/public/list/orders?action=getreceipt&orderid=224
                    debugprint("Sending SMS to " . $party . ": " . $phone);
                    $this->sendSMS($phone, $action["message"]);
                }
                if ($action["phone"]) {
                    //if SMS restaurant then SMS user of the restaurant instead since all of the restaurant phones are land lines
                    $action["message"] = str_replace("[url]", "", $message);
                    if ($phone_restro) {$phone = $phone_restro;}
                    debugprint("Calling " . $party . ": " . $phone);
                    $this->sendSMS($phone, $action["message"], true);
                }
            }
        }
        $user["orderid"] = $orderid;
        return $user;
    }

    /**
     * @param $data
     * @param bool|false $gethours
     * @return array|bool|\mysqli_result
     */
    function closestrestaurant($data, $gethours = false){
        //if(!isset($data['radius'])){$data['radius'] = 100;}//default radius
        $SQL = "SELECT address_id FROM restaurants WHERE address_id > 0 AND is_delivery = 1";
        if (isset($data["restaurant_id"])) {
            $SQL .= " AND id = " . $data["restaurant_id"];
        }
        $owners = implode(",", collapsearray(Query($SQL, true), "address_id"));
        $SQL = "SELECT *, ( 6371 * acos( cos( radians('" . $data['latitude'] . "') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('" . $data['longitude'] . "') ) + sin( radians('" . $data['latitude'] . "') ) * sin( radians( latitude ) ) ) ) AS distance FROM useraddresses WHERE id IN (" . $owners . ")";
        if (isset($data['radius'])) {
            $SQL .= " HAVING distance <= " . $data['radius'];
        }
        if (!isset($data["limit"])) {
            $data["limit"] = 1;
        }
        //$SQL .= " ORDER BY distance ASC LIMIT " . $data["limit"];
        $SQL .= " ORDER BY distance ASC";

        $Restaurants = Query($SQL, true);//useraddresses
        if ($Restaurants) {
            if ($gethours) {
                if ($data["limit"] == 1) {
                    $Restaurants = $this->processrestaurant($Restaurants[0]);
                    $Restaurants["SQL"] = $SQL;
                } else {
                    foreach ($Restaurants as $Index => $Restaurant) {
                        $Restaurants[$Index] = $this->processrestaurant($Restaurant);
                    }
                }
            }
        }
        return $Restaurants;
    }

    function processrestaurant($Restaurant){
        if (!is_array($Restaurant)) {
            $Restaurant = array("id" => $Restaurant);
            $Restaurant["restaurant"] = first("SELECT * FROM restaurants WHERE id = " . $Restaurant["id"]);
            $UserID = first("SELECT user_id FROM useraddresses WHERE id = " . $Restaurant["restaurant"]["address_id"]);
            $Restaurant["user_id"] = $UserID["user_id"];
        } else {
            $Restaurant["restaurant"] = first("SELECT * FROM restaurants WHERE address_id = " . $Restaurant["id"]);
        }
        $Restaurant["hours"] = gethours($Restaurant["id"]);
        $Restaurant["user"] = first("SELECT id, name, phone, email FROM users WHERE id = " . $Restaurant["user_id"]);//do not send password
        $Restaurant["shortage"] = first("SELECT item_id, tablename FROM shortage WHERE restaurant_id = " . $Restaurant["id"], false);
        /*if($Restaurant["shortage"]){
            foreach($Restaurant["shortage"] as $Index => $Data){
                if($Data["tablename"] != "menu"){
                    $Restaurant["shortage"][$Index]["addon"] = first("SELECT name FROM " . $Data["tablename"] . " WHERE id = " . $Data["item_id"])["name"];
                }
            }
        }*/
        return $Restaurant;
    }

    function processaddress($info){
        //autosave address changes
        $address = first("SELECT * FROM useraddresses WHERE user_id = " . $info["user_id"] . " AND number = '" . $info["number"] . "' AND street = '" . $info["street"] . "' AND city = '" . $info["city"] . "'");
        if (!$address) {
            $address = array(
                "user_id" => $info["user_id"],
                "number" => $info["number"],
                "city" => $info["city"],
                "unit" => $info["unit"],
                //"buzzcode" => $info["buzzcode"],
                "street" => $info["street"],
                "postalcode" => $info["postalcode"],
                "province" => $info["province"],
                "latitude" => $info["latitude"],
                "longitude" => $info["longitude"],
            );
            return insertdb("useraddresses", $address);
        } else if ($info["unit"] != $address["unit"]) {
            $address["unit"] = $info["unit"];
            return insertdb("useraddresses", $address);
        }
    }
}
