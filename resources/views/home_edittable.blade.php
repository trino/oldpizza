<button onclick="window.history.back();">Go Back</button> <button onclick="location.reload();">Refresh</button><P>
<TABLE>
    <?php
        startfile("home_edittable");
        $con = connectdb("ai");
        $currentURL = Request::url();
        $backURL = $currentURL;
        if(isset($_GET["table"])){
            if(isset($_GET["delete"])){
                deleterow($_GET["table"], "id = " . $_GET["delete"]);
                echo 'Row ' . $_GET["delete"] . ' was deleted from ' . $_GET["table"] . '<BR>';
            }
            echo '<FORM METHOD="GET" ACTION="' . $currentURL . '">';
            $currentURL .= "?table=" . $_GET["table"];
            $query = "SELECT * FROM " . $_GET["table"];
            if(isset($_GET["id"]) && $_GET["id"]){
                $query .= " WHERE id = " . $_GET["id"];
                $backURL .= "?table=" . $_GET["table"];
                echo '<INPUT TYPE="HIDDEN" NAME="id" VALUE="' . $_GET["id"] . '">';
            }
            if(isset($_GET["save"])){
                $dataarray = $_GET;
                unset($dataarray["table"]);
                unset($dataarray["save"]);
                insertdb($_GET["table"], $dataarray);
                echo 'Data has been saved to ' . $_GET["table"] . '<BR>';
            }
            echo '<A HREF="' . $backURL . '">Go Up</A>';
            $results = Query($query, true);
            $firstresult = true;
            echo '<INPUT TYPE="HIDDEN" NAME="table" VALUE="' . $_GET["table"] . '">';
            if(isset($_GET["id"]) && !$_GET["id"]){
                $results = array($results[0]);
            }
            foreach($results as $result){
                if(isset($_GET["id"])){
                    if(!$_GET["id"]){
                        foreach($result as $index => $value){
                            $result[$index] = "";
                        }
                    }
                    if(in_array($_GET["table"], array("toppings", "wings_sauce"))){
                        $categories = Query("SELECT DISTINCT(type) FROM " . $_GET["table"],true);
                        foreach($categories as $index => $category){
                            $categories[$index] = '<option value="' . $category["type"] .'">';
                        }
                        $result["name"] = '<INPUT TYPE="TEXT" NAME="name" VALUE="' . $result["name"] . '">';
                        $result["type"] = '<input type="text" name="type" value="' . $result["type"] . '" list="categories"><datalist id="categories">' . implode("\r\n", $categories) . '</datalist>';
                        $result["isfree"] = printoptions("isfree", array("No", "Yes"), $result["isfree"], array(0,1));
                        $result["qualifiers"] = '<INPUT TYPE="TEXT" NAME="qualifiers" VALUE="' . $result["qualifiers"] . '" TITLE="Leave blank for Half/Single/Double, must have 3 items in a comma delimited list if not blank">';
                    } else {
                        foreach($result as $index => $value){
                            if($index == "weight" && $_GET["table"] == "keywords"){
                                $result[$index] = '<INPUT TYPE="NUMBER" NAME="weight" VALUE="' . $value . '" MIN="1" MAX="5">';
                            } else if($index != "id"){
                                $result[$index] = '<INPUT TYPE="TEXT" NAME="' . $index . '" VALUE="' . $value . '">';
                            }
                        }
                        $result["Actions"] = '<A ONCLICK="return confirm(' . "'Are you sure you want to delete " . $result["id"] . "?'" . ');" HREF="' . $currentURL . '&delete=' . $result["id"] .'">Delete</A>';
                    }
                    echo '<INPUT TYPE="SUBMIT" NAME="save" VALUE="Save">';
                } else {
                    $result["Actions"] = '<A HREF="' . $currentURL . '&id=' . $result["id"] .'">Edit</A> ';
                    $result["Actions"] .= '<A ONCLICK="return confirm(' . "'Are you sure you want to delete " . $result["id"] . "?'" . ');" HREF="' . $currentURL . '&delete=' . $result["id"] .'">Delete</A>';
                }
                printrow($result, $firstresult, "id", $_GET["table"]);
            }
            if(!isset($_GET["id"])){
                $cols= count($result)-1;
                echo '<TR><TD COLSPAN="' . $cols . '"></TD><TD><A HREF="' . $currentURL . '&id=0">New</A></TD></TR>';
            }
        } else {
            foreach(enum_tables() as $table){
                echo '<TR><TD><A HREF="?table=' . $table . '">' . $table . '</A></TD></TR>';
            }
        }
        endfile("home_edittable");
    ?>
</TABLE>