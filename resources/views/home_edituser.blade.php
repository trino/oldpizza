<?php
    startfile("home_edituser");
    $currentURL = webroot("public/user/info");
    $includesection = Request::url() == $currentURL;

    if(!isset($user_id) || !$user_id){$user_id = read("id");}

    if(isset($_POST["action"])){
        switch($_POST["action"]){
            case "testemail":
                if(!isset($_POST["user_id"])){$_POST["user_id"]=0;}
                $found = false;
                if(isset($_POST["email"]) && $_POST["email"]){
                    $found = first("SELECT * FROM users WHERE id != " . $_POST["user_id"] . " AND email = '" . $_POST["email"] . "'");
                }
                if($found){echo "false";} else { echo "true"; }
                break;
            case "saveitem":
                $user = first("SELECT * FROM users WHERE id=" . $user_id);
                $_POST = $_POST["value"];

                //password check
                if($_POST["newpassword"]){
                    if ( ($_POST["oldpassword"] == $user["password"]) || Hash::check($_POST["oldpassword"], $user["password"])) {
                        $user["password"] = Hash::make($_POST["newpassword"]);
                    } else {
                        die("Password mismatch");
                    }
                }

                //email check
                if(isset($_POST["email"]) && $_POST["email"] != $user["email"]){
                    $found = first("SELECT * FROM users WHERE id != " . $user_id . " AND email = '" . $_POST["email"] . "'");
                    if(!$found){$user["email"] = $_POST["email"];}
                }

                $user["name"] = $_POST["name"];
                $user["phone"] = $_POST["phone"];
                $user["updated_at"] = now();

                insertdb("users", $user);//save
                echo "Data saved";
                break;
        }
        die();
    }

    if(!function_exists("obfuscate")){
        //replaces the middle of a valid credit card number with $maskingCharacter (invalid cards show as such)
        function obfuscate($CardNumber, $maskingCharacter = "*") {
            if (!isvalid_creditcard($CardNumber)) {
                return "[INVALID CARD NUMBER]";
            }
            return substr($CardNumber, 0, 4) . str_repeat($maskingCharacter, strlen($CardNumber) - 8) . substr($CardNumber, -4);
        }

        //checks if a credit card is valid, returns what kind of card it is if it's valid, or $Invalid if it's not
        function isvalid_creditcard($CardNumber, $Invalid = "") {
            $CardNumber = preg_replace('/\D/', '', $CardNumber);
            // http://stackoverflow.com/questions/174730/what-is-the-best-way-to-validate-a-credit-card-in-php
            // https://en.wikipedia.org/wiki/Bank_card_number#Issuer_identification_number_.28IIN.29
            if ($CardNumber) {
                $length = 0;
                $mod10 = true;
                $Prefix = left($CardNumber, 2);
                if ($Prefix >= 51 && $Prefix <= 55) {
                    $length = 16; //mastercard
                    $type = "mastercard";
                } else if ($Prefix == 34 || $Prefix == 37) {
                    $length = 15; //amex
                    $type = "americanExpress";
                } else if (left($CardNumber, 1) == 4) {
                    $length = array(13, 16); //visa
                    $type = "visa";
                } else if ($Prefix == 65) {
                    $length = 16; //discover
                    $type = "discover";
                } else {
                    $Prefix = left($CardNumber, 6);
                    if ($Prefix >= 622126 || $Prefix <= 622925) {
                        $length = 16; //discover
                        $type = "discover";
                    } else {
                        $Prefix = left($CardNumber, 3);
                        if ($Prefix >= 644 || $Prefix <= 649 || left($CardNumber, 4) == 6011) {
                            $length = 16; //discover
                            $type = "discover";
                        }
                    }
                }
                if ($length) {
                    if (!is_array($length)) {
                        $length = array($length);
                    }
                    $Prefix = false;
                    foreach ($length as $digits) {
                        if (strlen($CardNumber) == $digits) {
                            $Prefix = true;
                        }
                    }
                    if ($Prefix) {
                        if ($mod10) {
                            if (luhn_check($CardNumber)) {
                                return $type;
                            }
                        }
                        return $type;
                    }
                }
            }
            return $Invalid;
        }

        //checks if a card is valid
        function luhn_check($number) {
            // Strip any non-digits (useful for credit card numbers with spaces and hyphens)
            $number = preg_replace('/\D/', '', $number);

            // Set the string length and parity
            $number_length = strlen($number);
            $parity = $number_length % 2;

            // Loop through each digit and do the maths
            $total = 0;
            for ($i = 0; $i < $number_length; $i++) {
                $digit = $number[$i];
                // Multiply alternate digits by two
                if ($i % 2 == $parity) {
                    $digit *= 2;
                    // If the sum is two digits, add them together (in effect)
                    if ($digit > 9) {
                        $digit -= 9;
                    }
                }
                // Total up the digits
                $total += $digit;
            }

            // If the total mod 10 equals 0, the number is valid
            return ($total % 10 == 0) ? TRUE : FALSE;
        }

        function startfield($Name = false){
            if($Name){
                echo '<DIV CLASS="row"><DIV CLASS="col-md-2">' . $Name . ':</DIV><DIV CLASS="col-md-10">';
            } else {
                echo '</DIV></DIV>';
            }
        }
    }
    $user = getuser(false, false);
    $email = false;
?>

@if($includesection)
    @extends("layouts_app")
    @section("content")
        <div class="row m-t-1">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-block bg-danger">
                        <h2 class="pull-left">
                            <A HREF="{{ webroot("public") }}"><i class="fa fa-home" aria-hidden="true"></i></A> Edit user
                        </h2>
                        <A HREF="{{ webroot("public/list/useraddresses?user_id=" . $user_id ) }}" class="btn btn-sm btn-secondary waves-effect float-right">Edit Addresses</A>
                    </div>
                    <div class="card-block">
                        <div class="row">
                            <div class="col-md-12">
@endif

<FORM NAME="user" id="userform">
    @include("popups_edituser")
    <DIV CLASS="row">
        <DIV CLASS="col-md-12" align="center">
            <BUTTON CLASS="btn btn-primary" onclick="return userform_submit(false);">Save</BUTTON>
        </DIV>
    </DIV>
</FORM>

@if($includesection)
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endfile("home_edituser"); ?>
    @endsection
@endif