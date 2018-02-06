@extends(isset($isGET) ? 'layouts_app' : 'layouts_blank')
@section('content')
    <?php
    //http://localhost/ai/public/list/orders?action=getreceipt&orderid=224
    startfile("popups_receipt");
    $debugmode = !islive();
    $debug = "";
    $Order = first("SELECT orders.*, users.name, users.id as userid, users.email FROM orders, users WHERE orders.id = " . $orderid . " HAVING user_id = users.id");
    $filename = resource_path("orders") . "/" . $orderid . ".json";
    if (!isset($includeextradata)) {
        $includeextradata = false;
    }
    if (isset($JSON)) {//get raw JSON instead
        $style = 2;
        if ($JSON && $JSON != "false") {
            if (file_exists($filename)) {
                $Order["Order"] = json_decode(file_get_contents($filename));
                echo json_encode($Order);
                die();//only the JSON is desired, send it
            }
            echo json_encode(array("Status" => false, "Reason" => "File not found"));
            die();
        }
    } else if (!isset($style)) {
        $style = 1;
    }
    if (!$Order) {
        echo 'Order not found';
        return false;
    }
    switch ($style) {
        case 1:
            $includeextradata = true;
            $colspan = 6;
            if (!$debugmode) {
                $colspan -= 1;
            }
            break;
        case 2:
            $colspan = 4;
            $ordinals = array("1st", "2nd", "3rd", "4th", "5th", "6th", "7th", "8th", "9th", "10th");
            break;
    }
    //Hack to put CSS inline for emails
    if (!isset($inline)) {
        $inline = false;
    }
    if (!isset($timer)) {
        $timer = false;
    }
    $GLOBALS["inline"] = $inline;
    if (!function_exists("inline")) {
        function tomin($time){
            return left($time, strlen($time) - 2) * 60 + right($time, 2);
        }

        function minpad($time){
            if ($time < 10) {
                return "0" . $time;
            }
            return $time;
        }

        function inline($Class, $OnlyInline = false){
            if ($GLOBALS["inline"]) {
                $Style = array();
                $Class = explode(" ", $Class);
                foreach ($Class as $Classname) {
                    switch (strtolower($Classname)) {
                        //table-sm
                        case "table":
                            $Style[] = "";
                            break;
                        case "table-bordered":
                            $Style[] = "";
                            break;
                        case "bg-primary":
                            $Style[] = "";
                            break;
                        case "table-inverse":
                            $Style[] = "";
                            break;
                    }
                }
                return ' style="' . implode(" ", $Style) . '"';
            } else if (!$OnlyInline) {
                return ' class="' . $Class . '"';
            }
        }

        function parsetime($Time){
            return strtotime(date("j F Y ") . left($Time, 1) . " hours " . right($Time, 2) . " minutes");
        }

        function roundTime($timestamp, $increment = 15){
            $BEFOREminutes = date('i', $timestamp);
            $AFTERminutes = $increment + $BEFOREminutes - ($BEFOREminutes % $increment);
            $DIFFERENCEminutes = $AFTERminutes - $BEFOREminutes;
            return $timestamp + ($DIFFERENCEminutes * 60);
        }

        function GenerateDate($Date){
            $today = date("F j");
            if (isset($_GET["day"]) && is_numeric($_GET["day"]) && $_GET["day"] >= 0 && $_GET["day"] <= 6) {

            }
            $Date = str_replace($today, "Today (" . $today . ")", $Date);
            $tomorrow = date("F j", strtotime("+ 1 day"));
            $Date = str_replace($tomorrow, "Tomorrow (" . $tomorrow . ")", $Date);
            return $Date;
        }

        function todate($timestamp){
            return date("F j, Y G:i", $timestamp);
        }

        function getdiscount($subtotal){
            $subtotal = floor($subtotal/10)*10;
            for($dollars = $subtotal; $dollars > 0; $dollars-=10){
                $keyname = "over$" . $dollars;
                if(isset($GLOBALS["settings"][$keyname])){
                    return $GLOBALS["settings"][$keyname];
                }
            }
            return false;
        }
    }
    //edit countdown timer duration
    $minutes = getdeliverytime();
    $seconds = 0;
    $hours = 0;
    $duration = "";
    $timer = $place != "email";
    $day_of_week = date("w");
    if (isset($_GET["day"]) && is_numeric($_GET["day"]) && $_GET["day"] >= 0 && $_GET["day"] < 7) {
        $day_of_week = $_GET["day"];
    }

    if ($Order["deliverytime"]) {
        $duration = $Order["deliverytime"];
        $Time = trim(right($Order["deliverytime"], 4));//1500
        if (is_numeric($Time)) {
            $CurrentTime = date("Gi");
            if (isset($_GET["time"]) && is_numeric($_GET["time"]) && $_GET["time"] >= 0 && $_GET["time"] < 2400) {
                $CurrentTime = $_GET["time"];
            }
            $date = str_replace(" at ", "", left($Order["deliverytime"], strlen($Order["deliverytime"]) - 4));
            $duration = GenerateDate($date) . " at " . GenerateTime(intval($Time));
            $tomorrow = date("F j", strtotime("+ 1 day"));
            if ($date == $tomorrow) {
                $DeliveryTime = strtotime("midnight tomorrow") + tomin($Time) * 60;
                $minutes = ceil(($DeliveryTime - time()) / 60);
            } else if ($CurrentTime <= $Time && $timer) {
                $minutes = tomin($Time) - tomin($CurrentTime) + 1;
            } else {
                $timer = false;
            }
        } else if ($Order["deliverytime"] == "Deliver Now") {
            $time = strtotime($Order["placed_at"]) + ($minutes * 60);
            $open = parsetime(gethours($Order["restaurant_id"])[$day_of_week]["open"]) + ($minutes * 60);
            if ($time < $open && date("F j", $time) == date("F j", $open)) {
                $time = $open;
            }
            $time = roundTime($time);
            $duration = GenerateDate(date("F j ", $time)) . "at " . date("g:i A", $time);
            if (time() > $time) {
                $timer = false;//expired
            } else {
                $minutes = ceil(($time - time()) / 60);
            }
        } else {
            $timer = false;
        }
    }
    if (is_numeric($minutes) && $minutes > 59) {
        $hours = floor($minutes / 60);
        $minutes = $minutes % 60;
    }
    $time = '';
    if ($timer) {
        if ($minutes < 60) {
            $time = $minutes;
        } else {
            $time = floor($minutes / 60) . " " . minpad($minutes % 60);
        }
        $time .= "m:" . minpad($seconds) . "s";
    }
    $onlydebug = "Only shows in debug mode! - ";
?>

    <div class="alert alert-success text-center text-sm-center mb-0">
        <h2 style="font-weight: normal !important;">Delivery {{ $duration }}</h2>
    </div>

    @if($includeextradata)
        @if($timer)

            <!--div style="font-size: 2rem !important;" CLASS="mb-2 countdown btn-lg badge badge-pill badge-success" hours="<?= $hours; ?>" minutes="<?= $minutes; ?>" seconds="<?= $seconds; ?>"
             title="Time is approximate and not a guarantee"><?= $time; ?></div-->
        @elseif($place != "email")
            <span class="badge badge-pill badge-danger">[EXPIRED]</span>
        @endif
    @endif

    @if($style==1)
        <TABLE <?= inline("table table-sm "); ?>>
            <TR>
                <TH>#</TH>
                <TH>Item</TH>
                <TH align="right"> Sub-total</TH>
                <TH>Addons</TH>
                @if($debugmode)
                    <TH TITLE="<?= $onlydebug; ?>">Count</TH>
                @endif
                <th align="right">Price</th>
            </TR>
    @else
        <TABLE WIDTH="100%" class="table table-sm  bg-white " style="border: 15px solid #fff !important;" cellspacing="0" cellpadding="0">
    @endif

    <?php
        $integrity = true;
        if (!function_exists("findkey")) {
            function findkey($arr, $key, $value){
                return array_search($value, array_column($arr, $key));
            }

            //finds the size of the item
            function getsize($itemname, $isfree){
                $currentsize = "";
                foreach ($isfree as $value) {
                    $size = $value["size"];
                    $cost = $value["price"];
                    if (!is_array($cost)) {
                        if (textcontains($itemname, $size) && strlen($size) > strlen($currentsize)) {
                            $currentsize = $size;
                        }
                    }
                }
                return $currentsize;
            }

            function textcontains($text, $searchfor){
                return strpos(strtolower($text), strtolower($searchfor)) !== false;
            }

            function hasaddons($menuitem, $tables){
                foreach ($tables as $table => $value) {
                    if (isset($menuitem[$table])) {
                        if ($menuitem[$table]) {
                            return true;
                        }
                    }
                }
                return false;
            }

            function checkforclones(&$items, $OriginalID, $OriginalItem, $menuitem){
                $Quantity = 1;
                foreach ($items as $ID => $item) {
                    if ($ID != $OriginalID && $item->itemid == $OriginalItem->itemid) {
                        $Quantity += 1;
                        $items[$ID]->clone = true;
                    }
                }
                return $Quantity;
            }

            function iconexists($imagefile){
                return file_exists(public_path() . '/images/icon-' . $imagefile . ".png");
            }

            function showifabove1($quantity, $text = ""){
                if ($quantity > 1) {
                    return $quantity . $text;
                }
            }
        }

        //check all data again, do not trust the prices from the user!!
        $tables = array("toppings", "wings_sauce", "additional_toppings");
        foreach ($tables as $ID => $table) {
            $tables[$table] = Query("SELECT * FROM " . $table, true);
            unset($tables[$ID]);
        }

        $deliveryfee = findkey($tables["additional_toppings"], "size", "Delivery");
        $deliveryfee = $tables["additional_toppings"][$deliveryfee]["price"];

        if (file_exists($filename)) {
            $filename = file_get_contents($filename);
            try {
                $items = json_decode($filename);
                $itemIDs = array();
                foreach ($items as $item) {
                    if (isset($item->itemid)) {
                        $itemIDs[] = $item->itemid;
                    }
                }
                $itemIDs = implode(",", array_unique($itemIDs));
                if (!$itemIDs) {
                    die("Order is empty");
                }

                $menu = Query("SELECT * FROM menu WHERE id IN(" . $itemIDs . ")", true);
                $localdir = webroot("public/images/icon-");
                if ($place == "email" && !islive()) {
                    $localdir = "http://" . serverurl . "/public/images/icon-";
                }

                //convert the JSON into an HTML receipt, using only item/addon IDs, reobtaining cost/names from the database for security
                $subtotal = 0;
                foreach ($items as $ID => $item) {
                    unset($items[$ID]->clone);
                }
                foreach ($items as $ID => $item) {
                    if (is_object($item)) {
                        $quantity = 1;
                        $menukey = findkey($menu, "id", $item->itemid);
                        if (!isset($item->clone)) {
                            $menuitem = $menu[$menukey];
                            if (!hasaddons($menuitem, $tables)) {
                                $quantity = checkforclones($items, $ID, $item, $menuitem);
                            }
                            $size = getsize($menuitem["item"], $tables["additional_toppings"]);
                            $addonscost = "0.00";
                            if ($size) {
                                $addonscost = findkey($tables["additional_toppings"], "size", $size);
                                $addonscost = $tables["additional_toppings"][$addonscost]["price"];
                            }
                            $itemtotal = $menuitem["price"];
                            $paidtoppings = 0;
                            $freetoppings = 0;

                            $totaladdons = 0;
                            foreach ($tables as $name => $data) {
                                if (isset($menuitem[$name])) {
                                    $totaladdons += $menuitem[$name];
                                }
                            }

                            switch ($style) {
                                case 1:
                                    if ($debugmode) {
                                        $debug = ' TITLE="' . $onlydebug . var_export($item, true) . '"';
                                    }
                                    //echo '<TR><TD>' . ($ID + 1) . '</TD><TD' . $debug . '>' . $item->itemname . '</TD>';
                                    echo '<TR><TD>' . showifabove1($quantity) . '</TD><TD' . $debug . '>' . $item->itemname . '</TD>';
                                    if ($debugmode) {
                                        $debug = ' TITLE="' . $onlydebug . print_r($menuitem, true) . '"';
                                    }
                                    echo '<TD ALIGN="RIGHT"' . $debug . '>$' . number_format($menuitem["price"], 2) . '</TD><TD>';
                                    break;
                                case 2:
                                    $imagefile = str_replace(" ", "-", strtolower($menuitem["category"]));
                                    if (right($imagefile, 5) == "pizza" || !iconexists($imagefile)) {
                                        $imagefile = str_replace(" ", "-", strtolower($item->itemname));
                                        if (!iconexists($imagefile)) {
                                            $imagefile = "pizza";

                                            if (strtolower(right(trim($item->itemname), 5)) == "salad") {
                                                $imagefile = "salad";
                                            }
                                        }
                                    }
                                    // $imagefile = '<img class="pull-left" src="' . $localdir . $imagefile . ".png" . '" style="width:22px;margin-right:5px;">';
                                    echo '<TR><TD valign="middle" style="font-weight: bold;">' . showifabove1($quantity, 'x&nbsp;') . $item->itemname . '</TD><TD width="1%"></TD><TD width="1%"></TD><TD ALIGN="RIGHT" WIDTH="5%">';
                                    break;
                            }

                            $HTML = "";
                            if (isset($item->itemaddons)) {
                                if ($style == 1) {
                                    $HTML = '<TABLE style="border:1px solid #eceeef;!important;" WIDTH="100%">';
                                }
                                $addoncount = count($item->itemaddons);
                                foreach ($item->itemaddons as $addonID => $addon) {
                                    $toppings = array();
                                    if (isset($addon->tablename)) {
                                        $tablename = $addon->tablename;
                                        switch ($tablename) {
                                            case "toppings":
                                                $itemtype = "Pizza";
                                                $none = "No Toppings";
                                                break;
                                            case "wings_sauce":
                                                $itemtype = "lb";
                                                $none = "No Sauce";
                                                break;
                                        }
                                        if (isset($addon->addons)) {
                                            $toppings = $addon->addons;
                                        }
                                    }
                                    $newtoppings = array();
                                    foreach ($toppings as $topping) {
                                        if (isset($topping->id)) {//search by id
                                            $id = $topping->id;
                                            $toppingkey = findkey($tables[$tablename], "id", $topping->id);
                                        } else {//search by name
                                            $toppingkey = findkey($tables[$tablename], "name", $topping->text);
                                        }

                                        $topping = $tables[$tablename][$toppingkey];
                                        if ($topping["isfree"]) {
                                            $freetoppings++;
                                            $topping["name"] = '<I>' . $topping["name"] . '</I>';
                                        } else {
                                            $paidtoppings++;
                                        }
                                        if ($debugmode) {
                                            $debug = ' TITLE="' . $onlydebug . print_r($topping, true) . '"';
                                        }
                                        $newtoppings[] = '<SPAN' . $debug . '>' . $topping["name"] . '</SPAN>';
                                    }
                                    if (!$newtoppings) {
                                        $newtoppings[] = $none;
                                    }

                                    if ($style == 1) {
                                        $itemtitle = $itemtype . ' #' . ($addonID + 1);
                                        $HTML .= '<TR><TH NOWRAP>' . $itemtitle . '</TH></TR><TR><TD>' . implode(", ", $newtoppings) . '</TD></TR>';
                                    } else {
                                        $itemtitle = "";
                                        if ($addoncount > 1) {
                                            $itemtitle = $ordinals[$addonID] . " " . $itemtype . ": ";
                                        }
                                        $HTML .= $itemtitle . implode(", ", $newtoppings) . "<BR>";
                                    }
                                }
                                if ($style == 1) {
                                    echo $HTML . '</TABLE>';
                                }
                            }

                            $toppingscost = $addonscost * $paidtoppings;
                            $itemtotal = ($menuitem["price"] + $toppingscost) * $quantity;

                            if ($style == 1) {
                                echo '</TD>';
                                if ($debugmode) {
                                    echo '<TD NOWRAP>';
                                    if ($totaladdons) {
                                        echo $paidtoppings . ' paid<BR>' . $freetoppings . ' free';
                                        echo '<BR>$' . number_format($addonscost, 2) . '<BR>each';//'<BR>' . $size .
                                    }
                                    if ($debugmode) {
                                        $debug = ' TITLE="' . $onlydebug . 'User side: $' . $item->itemprice . '"';
                                    }
                                    echo '</TD>';
                                }
                                echo '<TD ALIGN="RIGHT"' . $debug . '>';
                            }
                            echo '$' . number_format($itemtotal, 2) . '</TD></TR>';
                            if ($style == 2 && $HTML) {
                                echo '<TR><TD COLSPAN="' . $colspan . '">' . $HTML . '</TD></TR>';
                            }
                            $subtotal += $itemtotal;
                        }
                    }
                }

                $tax_percent = 0.13;
                $tax = ($subtotal + $deliveryfee) * $tax_percent;
                $total = $subtotal + $deliveryfee + $tax;
                $colspanminus1 = $colspan - 1;
                echo '<TR><TD COLSPAN="' . $colspanminus1 . '" ALIGN="RIGHT">Sub-total $&nbsp;</TD><TD ALIGN="RIGHT">' . number_format($subtotal, 2) . '</TD></TR>';
                $discount = getdiscount($subtotal);
                if($discount){
                    $originaldiscount = $discount;
                    if(left($discount,1) == "$"){
                        $discount = right($discount, strlen($discount)-1);
                    } else if (right($discount, 1) == "%"){
                        $discount = left($discount, strlen($discount)-1) * 0.01 * $subtotal;
                    }
                    echo '<TR><TD COLSPAN="' . $colspanminus1 . '" ALIGN="RIGHT" TITLE="' . $originaldiscount . '">Discount $&nbsp;</TD><TD ALIGN="RIGHT">' . number_format($discount, 2) . '</TD></TR>';
                    $subtotal = $subtotal - $discount;
                }
                echo '<TR><TD COLSPAN="' . $colspanminus1 . '" ALIGN="RIGHT">Delivery $&nbsp;</TD><TD ALIGN="RIGHT">' . number_format($deliveryfee, 2) . '</TD></TR>';
                echo '<TR><TD COLSPAN="' . $colspanminus1 . '" ALIGN="RIGHT">Tax $&nbsp;</TD><TD ALIGN="RIGHT">' . number_format($tax, 2) . '</TD></TR>';
                echo '<TR style="font-weight: bold;"><TD COLSPAN="' . $colspanminus1 . '" ALIGN="RIGHT">Total $&nbsp;</TD><TD ALIGN="RIGHT">' . number_format($total, 2) . '</TD></TR>';
                echo '<TR><TD COLSPAN="' . $colspanminus1 . '" ALIGN="RIGHT">&nbsp;</TD><TD ALIGN="RIGHT"><span>Paid</span></TD></TR>';

                insertdb("orders", array("id" => $orderid, "price" => $total));//saved for stripe
            } catch (exception $e) {
                echo 'Caught exception: ', $e->getMessage() . " on line " . $e->getLine() . "<BR>";
                echo $filename;
            }
            if ($style == 2 && !$includeextradata) {
                echo '<TR><TD COLSPAN="' . $colspan . '">';
                if (isset($JSON)) {
                    echo '<BUTTON CLASS="btn btn-block btn-primary mb-3" ONCLICK="orders(' . $orderid . ', true);">LOAD ORDER</BUTTON>';
                } else {
                    echo $Order["name"] . " - " . $Order["email"] . "<BR>" . formatphone($Order["phone"]) . " " . formatphone($Order["cell"]) . "<BR>" . $Order["number"] . " " . $Order["street"] . '<BR>' . $Order["city"] . ", " . $Order["province"] . "<BR>" . $Order["postalcode"] . '<BR>' . $Order["unit"];
                }
                echo '</TD></TR>';
            }
        } else {
            echo '<TR><TD COLSPAN="' . $colspan . '" ALIGN="CENTER"><B TITLE="' . $filename . '">ORDER NOT FOUND</B></TD></TR>';
        }
        endfile("popups_receipt");
        ?>
    </TABLE>

    @if ($Order["cookingnotes"])
        <B>Notes </B><br> {{$Order["cookingnotes"]}}
    @endif

    @if(!isset($JSON))
        <h2 class="mt-2">Delivery Info</h2>
        <?php
            echo $Order["name"] . "<BR>" . $Order["number"] . " " . $Order["street"] . '<BR>' . $Order["city"] . " " . $Order["province"] . " " . $Order["postalcode"] . "<br>";
            if($Order["unit"]){echo $Order["unit"]. '<BR>';}
            echo formatphone($Order["phone"]);
        ?>

        <h2 class="mt-2">Order #<span ID="receipt_id"><?= $orderid; ?></span></h2>

        <?php
            $Restaurant = first("SELECT * FROM restaurants WHERE id = " . $Order["restaurant_id"]);
            $Raddress = first("SELECT * FROM useraddresses WHERE id = " . $Restaurant["address_id"]);
            echo $Restaurant["name"] . "<BR>" . $Raddress["number"] . " " . $Raddress["street"] . "<br>" .
                    $Raddress["city"] . " " . $Raddress["province"] . " " . $Raddress["postalcode"] . '<BR>' . $Raddress["unit"] . " " . formatphone($Restaurant["phone"]);
            echo '<INPUT TYPE="HIDDEN" ID="cust_latitude" VALUE="' . $Order["latitude"] . '"><INPUT TYPE="HIDDEN" ID="cust_longitude" VALUE="' . $Order["longitude"]
                    . '"><INPUT TYPE="HIDDEN" ID="rest_latitude" VALUE="' . $Raddress["latitude"]
                    . '"><INPUT TYPE="HIDDEN" ID="rest_longitude" VALUE="' . $Raddress["longitude"] . '">';
        ?>
    @endif

    @if($includeextradata)
        @if($party != "restaurant")
            <h2 class="mt-2">Questions about your order?</h2>
            Please contact the restaurant directly
            <DIV CLASS="clearfix"></DIV>
        @endif
        <br>
        <a class="pull-left btn-link btn pl-0" href="<?= webroot("help"); ?>">MORE INFO</a>
    @endif

    @if($timer && $place != "getreceipt")
        <SCRIPT>
            if (isUndefined(countdown)) {
                var countdown = window.setTimeout(function () {
                    incrementtime()
                }, 1000);
            }

            function backtotime(timestamp) {
                var d = new Date(timestamp * 1000);
                return d.getHours() + ":" + d.getMinutes();
            }

            function incrementtime() {
                if (!$(".countdown").hasAttr("timestamp")) {
                    var seconds = Number($(".countdown").attr("seconds"));
                    var minutes = Number($(".countdown").attr("minutes"));
                    var timestamp = getNow();
                    $(".countdown").attr("startingtime", backtotime(timestamp));
                    timestamp += (minutes * 60) + seconds;
                    $(".countdown").attr("endingtime", backtotime(timestamp));
                    $(".countdown").attr("timestamp", timestamp);
                } else {
                    var timestamp = $(".countdown").attr("timestamp");
                    var seconds = timestamp - getNow();
                    var minutes = Math.floor(seconds / 60);
                    seconds = seconds % 60;
                }
                var hours = Math.floor(minutes / 60);

                var result = false;
                if (seconds == 0) {
                    if (minutes == 0) {
                        result = "[EXPIRED]";
                        window.clearInterval(countdown);
                    } else {
                        minutes -= 1;
                    }
                    seconds = 59;
                } else {
                    seconds -= 1;
                }
                if (!result) {
                    if (hours == 0) {
                        result = minutes;
                    } else {
                        result = hours + "h:" + minpad(minutes % 60);
                    }
                    result += "m:" + minpad(seconds) + "s";
                }
                $(".countdown").text(result);
                countdown = window.setTimeout(function () {
                    incrementtime()
                }, 1000);
            }

            function minpad(time) {
                if (time < 10) {
                    return "0" + time;
                }
                return time;
            }
        </SCRIPT>
    @endif
@endsection