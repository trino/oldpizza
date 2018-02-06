<?php
    switch($_SERVER["SERVER_NAME"]){
        case 'scpizza.ca':
            define("serverurl", "scpizza.ca");
            define("sitename", "scpizza.ca");
            define("cityname", "Stoney Creek");
            break;
        case 'londonpizza.ca':
            define("serverurl", "londonpizza.ca");
            define("sitename", "londonpizza.ca");
            define("cityname", "London");
            break;
        default:
            define("serverurl", "http://" . $_SERVER["SERVER_NAME"] . "/ai/");
            define("sitename", "localhost");
            define("cityname", "local");
    }
    $webroot = webroot();
    date_default_timezone_set("America/Toronto");
    $Filename = base_path() . "/ai.sql";

    function webroot($file = ""){
        $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
        $webroot = $_SERVER["REQUEST_URI"];
        $start = strpos($webroot, "/", 1) + 1;
        $webroot = substr($webroot, 0, $start);
        $protocol = "http";
        if (islive()) {
            //$webroot = str_replace("application/", "", $webroot);
            //$webroot = str_replace("public/", "", $webroot);
            $webroot = "/";
            if ($isSecure) {
                $protocol = "https";
            }
        }
        return $protocol . '://' . $_SERVER['HTTP_HOST'] . $webroot . $file;
    }

    $dirroot = getcwd();

    //error_reporting(E_ERROR | E_PARSE);//suppress warnings
    //include("../veritas3-1/config/app.php");//config file is not meant to be run without cake, thus error reporting needs to be suppressed
    //error_reporting(E_ALL);//re-enable warnings
    $con = "";//connectdb();
    function connectdb($database = "ai", $username = "root", $password = ""){
        global $con;
        $localhost = "localhost";
        if ($_SERVER["SERVER_NAME"] == "localhost") {
            $localhost .= ":3306";
        }
        if (islive()) {
            switch (sitename) {
                case "londonpizza.ca": case "scpizza.ca":
                    $database = "londonpi_db";
                    $username = "londonpi_user";
                    $password = "Pass1234!";
                    break;
                default:
                    die("Username/password for '" . sitename . "' are not on file");
            }
        } else {
            $_SERVER['SERVER_ADDR'] = gethostbyname(gethostname());
        }
        $GLOBALS["database"] = $database;
        $con = mysqli_connect($localhost, $username, $password, $database) or die("Error " . mysqli_connect_error($con));
        return $con;
    }

    function left($text, $length){
        return substr($text, 0, $length);
    }

    function right($text, $length){
        return substr($text, -$length);
    }

    function mid($text, $start, $length){
        return substr($text, $start, $length);
    }

    function insertdb($Table, $DataArray, $PrimaryKey = "id", $Execute = True){
        global $con;
        if (is_object($con)) {
            $DataArray = escapearray($DataArray);
        }
        filtersubarrays($DataArray);
        $query = "INSERT INTO " . $Table . " (" . getarrayasstring($DataArray, True) . ") VALUES (" . getarrayasstring($DataArray, False) . ")";
        if ($PrimaryKey && isset($DataArray[$PrimaryKey])) {
            $query .= " ON DUPLICATE KEY UPDATE";
            $delimeter = " ";
            foreach ($DataArray as $Key => $Value) {
                if ($Key != $PrimaryKey) {
                    $query .= $delimeter . $Key . "='" . $Value . "'";
                    $delimeter = ", ";
                }
            }
        }
        $query .= ";";
        if ($Execute && is_object($con)) {
            mysqli_query($con, $query) or die ('Unable to execute query. ' . mysqli_error($con) . "<P>Query: " . $query);
            return $con->insert_id;
        }
        return $query;
    }

    function escapearray($DataArray){
        global $con;
        foreach ($DataArray as $Key => $Value) {
            if (!is_array($Value)) {
                $DataArray[$Key] = mysqli_real_escape_string($con, $Value);
            }
        }
        return $DataArray;
    }

    function getarrayasstring($DataArray, $Keys = True){
        if ($Keys) {
            $DataArray = array_keys($DataArray);
            return implode(", ", $DataArray);
        } else {
            $DataArray = array_values($DataArray);
            $DataArray = implode("', '", $DataArray);
            return "'" . $DataArray . "'";
        }
    }

    function filtersubarrays(&$array){
        foreach ($array as $key => $row) {
            if (is_array($row))
                unset($array[$key]);
        }
    }

    /*
     * $getcol:
     *  blank, returns first query results
     *  false, returns all results after the get(). use while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) { to get results
     *  true, returns the SQL query
     *  "COUNT()" returns the count of the results
     *  "ALL()" all the results in an array
     *  a string returns that specific column
     *  $OrderBy/$Dir/$GroupBy = order by column/direction (ASC/DESC)/group by column
    */
    function select_field_where($Table, $Where, $getcol = "", $OrderBy = "", $Dir = "ASC", $GroupBy = "", $LimitBy = 0, $Start = 0){
        $query = "SELECT * FROM " . $Table;
        if ($Where) {
            $query .= " WHERE " . $Where;
        }
        if ($OrderBy) {
            $query .= " ORDER BY " . $OrderBy . " " . $Dir;
        }
        if ($GroupBy) {
            $query .= " GROUP BY " . $GroupBy;
        }
        if ($LimitBy) {
            $query .= " LIMIT " . $LimitBy;
            if ($Start) {
                $query .= " OFFSET " . $Start;
            }
        }
        if ($getcol === true) {
            return $query;
        }
        $result = Query($query);
        if ($getcol !== false) {
            if ($getcol == "COUNT()") {
                return iterator_count($result);
            } else if ($getcol == "ALL()") {
                return first($result, false);
            } else {
                $result = first($result);
                if ($getcol) {
                    return $result[$getcol];
                }
            }
        }
        return $result;
    }

    function describe($table){
        return Query("DESCRIBE " . $table, true);
    }

    function deleterow($Table, $Where = false){
        if ($Where) {
            $Where = " WHERE " . $Where;
        }
        Query("DELETE FROM " . $Table . $Where);
    }

    function first($query, $Only1 = true){
        global $con;
        if (!is_object($query)) {
            $query = Query($query);
        }
        if ($query) {
            $ret = array();
            while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
                if ($Only1) {
                    return $row;
                }
                $ret[] = $row;
            }
            return $ret;
        }
    }

    function get($Key, $default = "", $arr = false){
        if (is_array($arr)) {
            if (isset($arr[$Key])) {
                return $arr[$Key];
            }
        } else {
            if (isset($_POST[$Key])) {
                return $_POST[$Key];
            }
            if (isset($_GET[$Key])) {
                return $_GET[$Key];
            }
        }
        return $default;
    }

    function collapsearray($Arr, $ValueKey = false, $KeyKey = false, $Delimiter = false){
        foreach ($Arr as $index => $value) {
            if (!$ValueKey) {
                foreach ($value as $key2 => $value2) {
                    $ValueKey = $key2;
                    break;
                }
            }
            if ($Delimiter) {
                $Arr[$index] = explode($Delimiter, $value[$ValueKey]);
            } else {
                if ($KeyKey) {
                    $Arr[$value[$KeyKey]] = $value[$ValueKey];
                    unset($Arr[$index]);
                } else {
                    $Arr[$index] = $value[$ValueKey];
                }
            }
        }
        return $Arr;
    }

    function flattenarray($arr, $key){
        foreach ($arr as $index => $value) {
            $arr[$index] = $value[$key];
        }
        return $arr;
    }

    function enum_tables($table = ""){
        return flattenarray(Query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='" . $GLOBALS["database"] . "'" . iif($table, "AND TABLE_NAME='" . $table . "'"), true), "TABLE_NAME");
    }

    if (!function_exists("mysqli_fetch_all")) {
        function mysqli_fetch_all($result) {
            $data = [];
            if (is_object($result)) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
            }
            return $data;
        }
    }

    function Query($query, $all = false){
        global $con;//use while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) { to get results
        if ($all) {
            $result = $con->query($query);
            if (is_object($result)) {
                return mysqli_fetch_all($result, MYSQLI_ASSOC);// or die ('Unable to execute query. '. mysqli_error($con) . "<P>Query: " . $query);
            } else {
                debugprint($query . " returned no results");
            }
        }
        return $con->query($query);
    }

    function printoption($option, $selected = "", $value = ""){
        $tempstr = "";
        if ($option === $selected || $value === $selected) {
            $tempstr = " selected";
        }
        if (strlen($value) > 0) {
            $value = " value='" . $value . "'";
        }
        return '<option' . $value . $tempstr . ">" . $option . "</option>";
    }

    function printoptions($name, $valuearray, $selected = "", $optionarray = false, $isdisabled = ""){
        $tempstr = '<SELECT ' . $isdisabled . ' name="' . $name . '" id="' . $name . '" CLASS="form-control">';
        if (!$optionarray) {
            $optionarray = $valuearray;
        }
        for ($temp = 0; $temp < count($valuearray); $temp += 1) {
            if (is_array($optionarray)) {
                $value = $optionarray[$temp];
            } else {
                $value = $temp;
            }
            $tempstr .= printoption($valuearray[$temp], $selected, $value);
        }
        $tempstr .= '</SELECT>';
        return $tempstr;
    }

    function iif($value, $istrue, $isfalse = ""){
        if ($value) {
            return $istrue;
        }
        return $isfalse;
    }

    //DOC2018: this function apparently exists in PHP so I had to add the error check //
    if(!function_exists("is_iterable")) {
        function is_iterable($var) {
            return (is_array($var) || $var instanceof Traversable);
        }
    }

    //$src = source array, $keys = the keys to remove
    function removekeys($src, $keys){
        return array_diff_key($src, array_flip($keys));
    }

    function printrow($row, &$FirstResult = false, $PrimaryKey = "id", $TableID = ""){
        if ($FirstResult) {
            echo '<TABLE BORDER="1" CLASS="autotable"';
            if ($TableID) {
                echo ' ID="' . $TableID . '"';
            }
            echo '><THEAD><TR>';
            $ID = 0;
            foreach ($row as $Key => $Value) {
                echo '<TH CLASS="' . $TableID . 'colheader ' . $Key . '" ID="' . $TableID . '-col' . $ID . '">' . $Key . '</TH>';
                $ID++;
            }
            echo '</TR></THEAD>';
            $FirstResult = false;
        }

        echo '<TR ID="' . $TableID . 'row' . $row[$PrimaryKey] . '">';
        foreach ($row as $Key => $Value) {
            echo '<TD CLASS="' . $Key . '" ID="' . $TableID . 'row' . $row[$PrimaryKey] . '-' . $Key . '"';
            if (is_numeric($Value)) {
                echo ' ALIGN="RIGHT"';
            }
            if ($Value == "*") {
                echo ' ALIGN="CENTER"';
            }
            echo '>';
            if (is_array($Value)) {
                $FirstResult2 = true;
                printrow($Value, $FirstResult2, $PrimaryKey, $TableID . $row[$PrimaryKey] . "-" . $Key);
                echo '</TABLE>';
            } else {
                echo $Value;
            }
            echo '</TD>';
        }
        echo '</TR>';
    }

    function printfile($filename){
        echo '<DIV CLASS="blue">' . $filename . '</DIV>';
    }

    function filternumeric($text, $withwhat = ''){
        return preg_replace('/[0-9]/', $withwhat, $text);
    }

    function filternonnumeric($text, $withwhat = ''){
        return preg_replace('/[^0-9]/', $withwhat, $text);
    }

    function filternonalphanumeric($text, $withwhat = ''){
        return preg_replace("/[^A-Za-z0-9 ]/", $withwhat, $text);
    }

    function setsetting($Key, $Value){
        return insertdb("settings", array("keyname" => $Key, "value" => $Value), "keyname");
    }

    function millitime() {
        $microtime = microtime();
        $comps = explode(' ', $microtime);
        return sprintf('%d%03d', $comps[1], $comps[0] * 1000);
    }

    function getsetting($Key, $Default = ""){
        if (!enum_tables("settings")) {
            return $Default;
        }
        $Value = Query("SELECT value FROM settings WHERE keyname='" . $Key . "'", true);
        if (isset($Value[0]["value"])) {
            return $Value[0]["value"];
        }
        return $Default;
    }

    function drop_table($table = false){
        if ($table === false) {
            Query("SET foreign_key_checks = 0");
            $tables = enum_tables();
            foreach ($tables as $table) {
                drop_table($table);
            }
        } else {
            Query("DROP TABLE IF EXISTS " . $table);
        }
    }

    function importSQL($filename){
        drop_table();
        $templine = '';// Temporary variable, used to store current query
        $lines = file($filename);// Read in entire file
        foreach ($lines as $line) {// Loop through each line
            if (substr($line, 0, 2) == '--' || $line == '') {
                continue;
            }// Skip it if it's a comment
            $templine .= $line;// Add this line to the current segment
            if (substr(trim($line), -1, 1) == ';') {// If it has a semicolon at the end, it's the end of the query
                Query($templine);// Perform the query
                $templine = '';// Reset temp variable to empty
            }
        }
        Query("COMMIT;");
    }

    function isFileUpToDate($SettingKey, $Filename){
        if (file_exists($Filename)) {
            $lastSQLupdate = getsetting($SettingKey, "0");
            $lastFILupdate = filemtime($Filename);
            return $lastFILupdate > $lastSQLupdate;
        }
    }

    function loadsettings(){
        $settings = first("SELECT * FROM `settings` WHERE keyname NOT IN (SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='" . $GLOBALS["database"] . "') AND keyname NOT IN ('lastSQL', 'menucache')", false);
        foreach($settings as $ID => $Value){
            $settings[$Value["keyname"]] = $Value["value"];
            unset($settings[$ID]);
        }
        return $settings;
    }

    $con = connectdb();
    $settings = loadsettings();

    define("debugmode", $GLOBALS["settings"]["debugmode"]);
    if (isFileUpToDate("lastSQL", $Filename)) {
        $lastSQLupdate = getsetting("lastSQL", "0");
        $lastFILupdate = filemtime($Filename);
        importSQL($Filename);
        $con = connectdb();
        $settings = loadsettings();
        setsetting("lastSQL", $lastFILupdate);
        echo '<DIV CLASS="red">' . $lastSQLupdate . ' SQL was out of date, imported AI.sql on ' . $lastFILupdate . '</DIV>';
    }

    function read($Name){
        if (\Session::has('session_' . $Name)) {
            return \Session::get('session_' . $Name);
        }
    }

    //write to session
    function write($Name, $Value, $Save = false){
        \Session::put('session_' . $Name, $Value);
        if ($Save) {
            \Session::save();
        }
    }

    //returns the current date/time
    function now($totime = false, $now = false){
        if (!$now) {
            $now = time();
            if (read("profiletype") == 1 && isset($_GET["time"])) {
                if (is_numeric($_GET["time"]) && $_GET["time"] >= 0 && $_GET["time"] <= 2400) {
                    $hour = floor($_GET["time"] / 100);
                    $minute = $_GET["time"] % 100;
                    $now = mktime($hour, $minute);
                }
            }
        }
        if (!is_numeric($now)) {
            return $now;
        }
        if ($totime === true) {
            return $now;
        }
        if ($totime !== false && $totime !== true) {
            return date($totime, $now);
        }
        return date("Y-m-d H:i:s", $now);
    }

    //write text to royslog.txt
    function debugprint($text, $path = "royslog.txt", $DeleteFirst = false){
        $todaytime = date("Y-m-d") . " " . date("h:i:s a");
        $dashes = "----------------------------------------------------------------------------------------------\r\n";
        if (is_array($text)) {
            $text = print_r($text, true);
        }
        $dir = getdirectory($path);
        if (!is_dir($dir) && $dir) {
            mkdir($dir, 0777, true);
        }
        $ID = read("id");
        $Name = iif($ID, read("name"), "[NOT LOGGED IN]");
        $text = $dashes . $todaytime . ' (USER # ' . $ID . ": " . $Name . ")  --  " . str_replace(array("%dashes%", "<BR>", "%20"), array($dashes, "\r\n", " "), $text) . "\r\n";
        file_put_contents($path, $text, iif($DeleteFirst, 0, FILE_APPEND));
        return $text;
    }

    function getdirectory($path){
        return pathinfo(str_replace("\\", "/", $path), PATHINFO_DIRNAME);
    }

    function getfilename($path, $WithExtension = false){
        if ($WithExtension) {
            return pathinfo($path, PATHINFO_BASENAME); //filename only, with extension
        } else {
            return pathinfo($path, PATHINFO_FILENAME); //filename only, no extension
        }
    }

    //get the lower-cased extension of a file path
    //HOME/WINDOWS/TEST.JPG returns jpg
    function getextension($path){
        return strtolower(pathinfo($path, PATHINFO_EXTENSION)); // extension only, no period
    }

    function file_size($path){
        if (file_exists($path)) {
            return filesize($path);
        }
        return 0;
    }

    //gets the last key of an array
    function lastkey($array){
        $keys = array_keys($array);
        return last($keys);
    }

    function getiterator($arr, $key, $value, $retValue = true){
        foreach ($arr as $index => $item) {
            if (is_array($item)) {
                if (isset($item[$key]) && $item[$key] == $value) {
                    if ($retValue) {
                        return $value;
                    }
                    return $index;
                }
            } else if (is_object($item)) {
                if (isset($item->$key) && $item->$key == $value) {
                    if ($retValue) {
                        return $value;
                    }
                    return $index;
                }
            }
        }
    }

    function getuser($IDorEmail = false, $IncludeOther = true){
        $field = "email";
        if (!$IDorEmail) {
            $IDorEmail = read("id");
        }
        if (is_numeric($IDorEmail)) {
            $field = "id";
        } else {
            $IDorEmail = "'" . $IDorEmail . "'";
        }
        $user = first("SELECT * FROM users WHERE " . $field . " = " . $IDorEmail);
        if (!$user) {
            return false;
        }
        if ($IncludeOther) {
            $user["Addresses"] = Query("SELECT * FROM useraddresses WHERE user_id = " . $user["id"], true);
            $user["Orders"] = Query("SELECT id, placed_at FROM `orders` WHERE user_id = " . $user["id"] . " ORDER BY id DESC LIMIT 5", true);
            foreach ($user["Orders"] as $Index => $Order) {
                $user["Orders"][$Index]["placed_at"] = verbosedate($Order["placed_at"]);
            }
            if ($user["stripecustid"]) {
                initStripe();
                try {
                    $customer = \Stripe\Customer::Retrieve($user["stripecustid"]);//get all credit cards
                    foreach ($customer->sources->data as $Index => $Value) {
                        $customer->sources->data[$Index] = getProtectedValue($Value, "_values");
                        unset($customer->sources->data[$Index]["metadata"]);
                    }
                    $user["Stripe"] = $customer->sources->data;
                } catch (Stripe\Error\Base $e) {
                    Query("UPDATE users SET stripecustid = '' WHERE " . $field . " = " . $IDorEmail . ";");//stripecustid is likely invalid, delete it
                    $user["StripeError"] = $e->getMessage();
                }
            }
        }
        return $user;
    }

    //gets the protected value of an object ("_properties" is one used by most objects)
    function getProtectedValue($obj, $name = "_properties"){
        $array = (array)$obj;
        $prefix = chr(0) . '*' . chr(0);
        if (isset($array[$prefix . $name])) {
            return $array[$prefix . $name];
        }
    }

    $GLOBALS["testlive"] = false;
    function initStripe(){
        //Set secret key: remember to change this to live secret key in production
        if ((!islive() || (isset($_POST["istest"]) && $_POST["istest"])) && !$GLOBALS["testlive"]) {
            \Stripe\Stripe::setApiKey("BJi8zV1i3D90vmaaBoLKywL84HlstXEg"); //test
        } else {
            \Stripe\Stripe::setApiKey("3qL9w2o6A0xePqv8C6ufRKbAqkKTDJAW"); //live
        }
    }

    function isencrypted($text){
        if (left($text, 9) == "eyJpdiI6I") {
            try {
                $value = decrypt($text);
                $text = $value;
            } catch (\Exception $e) {
            }
        }
        return $text;
    }

    function islive(){
        $server = $_SERVER["SERVER_NAME"];
        return $server == serverurl;
        /*if($server == "localhost" || $server == "127.0.0.1"){return false;}
        if(strpos($server, ".") !== false){
            if(is_numeric(str_replace(".", "", $server))) {
                $server = explode(".", $server);
                if ($server[0] == "10") {return false;}
                if ($server[0] == "172" && $server[1] > "15" && $server[1] < 32) {return false;}
                if ($server[0] == "192" && $server[1] == "168") {return false;}
            }
        }
        return true;*/
    }

    function verbosedate($date){
        if (!is_numeric($date)) {
            $date = strtotime($date);
        }
        $append = "";
        switch (date("G:i", $date)) {
            case "0:00":
                $append = " (Midnight)";
                break;
            case "12:00":
                $append = " (Noon)";
                break;
        }
        return date("l F j, Y @ g:i A", $date) . $append;
    }

    function gethours($RestaurantID = -1){
        $hours = first("SELECT * FROM `hours` WHERE restaurant_id = " . $RestaurantID . " or restaurant_id = 0 ORDER BY restaurant_id DESC LIMIT 1");
        $ret = array();
        foreach ($hours as $day => $time) {
            $dayofweek = left($day, 1);
            if (is_numeric($dayofweek)) {
                $timeofday = right($day, strlen($day) - 2);
                $ret[$dayofweek][$timeofday] = $time;
            }
        }
        return $ret;
    }

    function GenerateTime($time = ""){
        if (!$time) {
            $time = date("Gi");
        }
        $minutes = $time % 100;
        $thehours = intval(floor($time / 100));
        $hoursAMPM = $thehours % 12;
        if ($hoursAMPM == 0) {
            $hoursAMPM = 12;
        }
        $tempstr = $hoursAMPM . ":";
        if ($minutes == 0) {
            $tempstr .= "00";
        } else if ($minutes < 10) {
            $tempstr .= "0" + $minutes;
        } else {
            $tempstr .= $minutes;
        }
        $extra = "";
        if ($time == 0) {
            $extra = " (Midnight)";
        } else if ($time == 1200) {
            $extra = " (Noon)";
        }
        if ($time < 1200) {
            return $tempstr . " AM" . $extra;
        } else {
            return $tempstr . " PM" . $extra;
        }
    }

    function like_match($pattern, $subject){
        $pattern = str_replace('%', '.*', preg_quote($pattern, '/'));
        return (bool)preg_match("/^{$pattern}$/i", $subject);
    }

    function lastupdatetime($table){//will not work on live!
        if (first("SHOW TABLE STATUS FROM " . $GLOBALS["database"] . " LIKE '" . $table . "';")["Engine"] == "InnoDB") {
            $filename = first('SHOW VARIABLES WHERE Variable_name = "datadir"')["Value"] . $GLOBALS["database"] . '/' . $table . '.ibd';
            return filemtime($filename);//UNIX datestamp
        }
        return first("SELECT UPDATE_TIME FROM information_schema.tables WHERE  TABLE_SCHEMA = '" . $GLOBALS["database"] . "' AND TABLE_NAME = '" . $table . "'")["UPDATE_TIME"];//unknown format
    }

    function startfile($filename){
        date_default_timezone_set("America/Toronto");
        $GLOBALS["filetimes"][$filename]["start"] = microtime(true);
    }

    function endfile($filename){
        $GLOBALS["filetimes"][$filename]["end"] = microtime(true);
    }

    function countSQL($table, $SQL = "*"){
        return first("SELECT COUNT(" . $SQL . ") as count FROM " . $table)["count"];
    }

    function actions($eventname, $party = -1){
        //party:  0=customer, 1=admin, 2=restaurant
        //events: order_placed, order_canceled/order_pending/order_confirmed/order_declined/order_delivered (done), user_registered (done)
        $SQL = "SELECT party, sms, phone, email, message FROM actions WHERE eventname = '" . $eventname . "'";
        if ($party > -1) {
            $SQL .= " AND party = " . $party;
        }
        return first($SQL, $party > -1);
    }

    function getdeliverytime($var = "DeliveryTime"){
        return first("SELECT price FROM additional_toppings WHERE size = '" . $var . "'")["price"];
    }

    function formatphone($phone){
        $phone = filternonnumeric($phone);
        if (strlen($phone) == 10) {
            return preg_replace('/(\d{3})(\d{3})(\d{4})/', "($1) $2-$3", $phone);
        }
        return $phone;
    }
?>