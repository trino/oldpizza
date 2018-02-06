<?php
    startfile("home_editmenu");
    if(read("profiletype") == 1 && count($_POST)){
        if(isset($_POST["changes"])){
            foreach($_POST["changes"] as $tablename => $table){
                foreach($table as $row){
                    if(!is_numeric( $row["id"] )){unset($row["id"]);}//is a new entry
                    insertdb($tablename, $row);//save data
                }
            }
        }
        if(isset($_POST["deleted"])){
            foreach($_POST["deleted"] as $row){
                if(is_numeric($row["keyid"])){
                    deleterow($row["table"], "id=" . $row["keyid"]);//deleted item
                }
            }
        }
        die();
    }
?>
@extends("layouts_app")
@section("content")
    <STYLE>
        hr{
            margin-top: 2px;
            margin-bottom: 2px;
        }

        input[type=checkbox]{
            height: 26px;
        }

        .currenttable{
            font-weight: bold;
        }

        .stayhere{
            position:fixed;
        }

        input[type=checkbox] {
            -webkit-appearance:checkbox;
        }

        .is-even, .is-odd{
            padding-left: 20px;
            padding-right: 20px;
        }

        .is-even{
            background: lightsteelblue;
        }

        .itemname{
            padding-top: 6px;
        }

        .card-block.bg-danger h2{
            color: white !important;
        }

        .clear-row{
            padding-left: 10px;
            padding-right: 10px;
        }

        .table_main{
            display: block;
            padding-left: 10px;
            padding-right: 24px;
        }

        #allergens li{
            margin-bottom: 0px;
            line-height: 16px;
        }
        #allergens label{
            margin-bottom: 0px;
        }
        #allergens input[type=checkbox]{
            height: 16px;
            width: 16px;
        }
    </STYLE>
    <div class="row m-t-1">
        <div class="col-md-12">
            <div class="card">
                <div class="card-block bg-danger">
                    <h2 class="pull-left">
                        <i class="fa fa-home"></i> Edit menu
                    </h2>
                </div>
                <div class="card-block">
                    <div class="row">
                        <div class="col-md-12">
                            @if(read("profiletype") != 1)
                                You are not authorized to view this page
                            @else
                                <div class="row">
                                    <div class="col-md-2">
                                        <DIV class="clear-row">
                                            <UL ID="catlist">
                                                <LI class="main hyperlink" onclick="main_click(this);" table="additional_toppings">Size costs</LI>
                                                <LI class="main hyperlink" onclick="main_click(this);" table="toppings">Pizza Toppings</LI>
                                                <LI class="main hyperlink" onclick="main_click(this);" table="wings_sauce">Wing Sauces</LI>
                                                <HR>
                                                Menu:
                                                <LI class="category hyperlink" onclick="newcategory();">[New Category]</LI>
                                                <?php
                                                    $categories = collapsearray(Query('SELECT category FROM `menu` GROUP BY category_id ORDER BY category ASC', true), "category");
                                                    foreach($categories as $category){
                                                        echo '<LI class="category hyperlink" onclick="cat_click(this);">' . $category . '</LI>';
                                                    }
                                                ?>
                                            </UL>
                                            <button class="btn btn-block btn-success" onclick="$('.newitembtn').trigger('click');">New</button>
                                            <button ID="savechanges" class="btn btn-block btn-success changes dont-show" onclick="savechanges();">Save Changes</button>
                                            <button ID="discardchanges" class="btn btn-block btn-secondary changes dont-show" onclick="discard(false);">Discard Changes</button>
                                            <DIV id="allergens"></DIV>
                                        </div>
                                    </DIV>
                                    <DIV CLASS="clearfix"></DIV>
                                    <div class="col-md-10">
                                        <?php
                                            $addon_tables = array("toppings", "wings_sauce");
                                            $tables = array_merge($addon_tables, array("menu", "additional_toppings"));
                                            foreach($tables as $table){
                                                echo '<DIV ID="table_' . $table . '" CLASS="table_main dont-show">Test ' . $table . '</DIV>' . "\r\n";
                                                echo '<datalist ID="categories_' . $table . '"></datalist>' . "\r\n";
                                            }
                                        ?>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if(read("profiletype") == 1)
        <SCRIPT>
            var changes = {};
            var deleted = new Array;
            var haschanges = 0;
            var newsitems = 0;

            function deleteitem(table, keyid){
                var name = $("input[table=" + table + "][keyid=" + keyid + "]").first().val();
                if(!name){name = "UNNAMED";}
                var type = "";
                switch(table){
                    case "additional_toppings":     type = "size"; break;
                    case "toppings":                type = "topping"; break;
                    case "wings_sauce":             type = "sauce"; break;
                    case "menu":                    type = "menu item"; break;
                }

                confirm2("Are you sure you want to delete the '" + name + "' " + type + "?", 'Delete Item', function() {
                    var index = findwhere(changes[table], "id", keyid);
                    if (index > -1) {
                        removeindex(changes[table], index);
                    }//remove from changes
                    if (!isNaN(keyid)) {
                        haschanged();
                        deleted.push({table: table, keyid: keyid});//send

                        index = findwhere(alldata[table], "id", keyid);
                        if (index > -1) {
                            removeindex(alldata[table], index);
                        }//remove from all data
                    }
                    $("." + table + "_" + keyid).remove();
                });
            }

            function undo(table, keyid){
                index = findwhere(alldata[table], "id", keyid);
                if (index > -1) {removeindex(alldata[table], index);}//remove from all data
                $("input[table=" + table + "][keyid=" + keyid + "]").each(function(){
                    var type = $(this).attr("type").toLocaleLowerCase();
                    var original = $(this).attr("original");
                    switch(type){
                        case "checkbox":
                            $(this).prop("checked", original.length>0);
                            break;
                        default:
                            if(!original.isEqual($(this).val())){
                                $(this).val(original);
                                //haschanges--;
                            }
                    }
                });
                $("." + table + "_" + keyid + "undo").hide();
            }

            function savechanges(){
                $.post(currentURL, {
                    _token: token,
                    changes: changes,
                    deleted: deleted
                }, function (result) {
                    haschanges = 0;
                    if(result){
                        alert(result);
                    } else {
                        location.reload(true);
                    }
                });
            }

            function discard(force){
                if(force) {force_discard();} else {confirm2("Are you sure you want to discard all changes you've made?", 'Discard Changes', function(){force_discard();});}
            }

            function force_discard(){
                var tables = Object.keys(alldata);
                for (var tableindex = 0; tableindex < tables.length; tableindex++) {
                    changes[tables[tableindex]] = new Array;
                }
                deleted = new Array;
                haschanges = 0;
                $(".changes").hide();
                newsitems = 0;
                $(".isnewitem").remove();
            }

            var alldata = {};
            var categories = {};
            var current_table = "";
            var current_category = "";
            <?php
                $CRLF = ";\r\n\t\t";
                echo 'categories["menu"] = ' . json_encode($categories) . $CRLF;
                foreach($addon_tables as $table){
                    echo 'categories["' . $table . '"] = ' . json_encode(collapsearray(Query('SELECT type FROM ' . $table . ' GROUP BY type ORDER BY type ASC', true), "type")) . $CRLF;
                    echo 'makecategories("' . $table . '")' . $CRLF;
                }
                foreach($tables as $table){
                    echo 'alldata["' . $table . '"] = ' . json_encode(Query("SELECT * FROM " . $table, true)) . $CRLF;
                }
            ?>

            function cat_click(element){
                $(".currenttable").removeClass("currenttable");
                $(element).addClass("currenttable");

                current_category = $(element).text();
                current_table="menu";
                $(".table_main").hide();
                var HTML = getmenuitems(current_category);
                $("#table_menu").html(HTML).show();
                $('input.currency').currencyInput();
            }

            function main_click(element){
                $(".currenttable").removeClass("currenttable");
                $(element).addClass("currenttable");

                current_table = $(element).attr("table");
                $(".table_main").hide();
                $("#table_" + current_table).show();
            }

            $(window).load(function () {
                //$( document ).ready(function() {
                processAll();
                $(".main").first().trigger("click");
            });

            function processAll(){
                var tables = Object.keys(alldata);
                for(var tableindex = 0; tableindex < tables.length; tableindex++){
                    var table_name = tables[tableindex];
                    var table_data = alldata[table_name];
                    var HTML = '<button class="btn btn-block btn-success newitembtn" onclick="newitem(' + "'" + table_name + "'" + ');">New</button><BR>';
                    for(var dataindex = 0; dataindex < table_data.length; dataindex++){
                        HTML += makeHTML(table_data[dataindex], table_name, isEvenOrOdd(dataindex));
                    }
                    changes[table_name] = new Array;
                    $("#table_" + table_name).html(HTML);
                }
            }

            function autoupdate(element){
                var table  = $(element).attr("table");
                var keyid  = $(element).attr("keyid");
                var column = $(element).attr("column");
                var value  = $(element).val();
                var name   = $(element).attr("name");
                var type   = $(element).attr("type").toLowerCase();
                if(type == "checkbox"){
                    value = $(element).prop("checked");
                } else if (type == "radio"){
                    value = "you need to finish the code for this!";
                }
                console.log(type + ": " + name + " = " + value);
                $("." + table + "_" + keyid + "undo").show();
                haschanged();
                var index = findwhere(changes[table], "id", keyid);
                if(index == -1){
                    var data = {id: keyid};
                    data[column] = value;
                    changes[table].push(data)
                } else {
                    changes[table][index][column] = value;
                }
                /*
                index = findwhere(alldata[table], "id", keyid);
                if(index > -1){alldata[table][index][column] = value;}
                */
            }

            function haschanged(){
                if(haschanges==0) {$(".changes").show();}
                haschanges++;
            }

            function getmenuitems(category){
                category=category.trim();
                var HTML = '<button class="btn btn-block btn-success" onclick="newitem(' + "'menu', '" + category + "'" + ');">New</button><BR>';
                for(var index = 0; index< alldata["menu"].length; index++){
                    var data = alldata["menu"][index];
                    if(data["category"].trim().isEqual(category)){
                        HTML += makeHTML(data, "menu", isEvenOrOdd(index));
                    }
                }
                return HTML;
            }

            function newcategory(){
                var catname = prompt(makestring("{cat_name}").trim());
                if(categoryexists("menu", catname)){
                    alert(makestring("{exists_already}", {name: catname}));
                } else {
                    categories["menu"].push(catname);
                    $("#categories_menu").append('<OPTION VALUE="' + catname + '">');
                    $("#catlist").append('<LI class="category hyperlink" onclick="cat_click(this);">' + catname + '</LI>');
                }
            }

            function categoryexists(cattype, catname){
                for(var i=0; i< categories[cattype].length; i++){
                    if(catname.isEqual(categories[cattype][i])){return true;}
                }
                return false;
            }

            function makecategories(category){
                var alldata = categories[category];
                var HTML = '';
                for(var index=0; index<alldata.length; index++){
                    HTML += '<OPTION VALUE="' + alldata[index] + '">';
                }
                $("#categories_" + category).html(HTML);
            }

            function newitem(table_name, category){
                var data = alldata[table_name][0];
                var keys = Object.keys(data);
                for(var i=0; i<keys.length; i++){
                    data[keys[i]] = "";
                }
                data["id"] = "new" + newsitems;
                switch(table_name) {
                    case 'menu':
                        data["category"] = category;
                        break;
                }
                alldata[table_name].push(data);
                var HTML = makeHTML(data, table_name, isEvenOrOdd(newsitems));
                $("#table_" + table_name).append(HTML);
                newsitems++;
                $("html, body").stop().animate({ scrollTop: $(document).height() }, "slow");
                $("#discardchanges").show();
            }

            function isEvenOrOdd(number){
                if(number % 2 == 0){return " is-even";}
                return " is-odd";
            }

            function makeHTML(data, table_name, HTMLclass){
                var HTML = '';
                if(isUndefined(HTMLclass)){HTMLclass = "";}
                HTMLclass += " " + table_name + "_" + data["id"];
                var undostyle = ' STYLE="display:none"';
                if(!isNumeric(data["id"])){HTMLclass += " isnewitem";}
                if(changes.hasOwnProperty(table_name)) {
                    var changeID = findwhere(changes[table_name], "id", data["id"]);
                    if (changeID > -1) {
                        var changedata = changes[table_name][changeID];
                        var keys = Object.keys(changedata);
                        for(var keyid=0; keyid<keys.length; keyid++){
                            var key = keys[keyid];
                            data[key + "_change"] = changedata[key];
                            undostyle="";
                        }
                    }
                }

                switch(table_name){
                    case 'additional_toppings':
                        var cols = 2;
                        HTML = makeinput2(cols, table_name, data, "Size", "size", "text", "This word must be inside the name of the item for it to be detected as this size");
                        HTML += makeinput2(cols, table_name, data, "Price", "price", "price");
                        break;
                    case 'toppings': case 'wings_sauce':
                        var cols = 4;
                        HTML = makeinput2(cols, table_name, data, "Name", "name", "text");
                        HTML += makeinput2(cols, table_name, data, "Category", "type", "category");
                        HTML += makeinput2(cols, table_name, data, "Is Free", "isfree", "checkbox", "For free addons like 'well done', or 'easy on the sauce'");
                        HTML += makeinput2(cols, table_name, data, "Group #", "groupid", "number", "If the Group # is above 0, only 1 item in this group can be added to the menu item");
                        break;
                    case 'menu':
                        var cols = 2 + <?= count($addon_tables); ?>;
                        HTML = makeinput2(cols, table_name, data, "Name", "item", "text");
                        HTML += makeinput2(cols, table_name, data, "Price", "price", "price");
                        <?php
                            foreach($addon_tables as $table){
                                echo 'HTML+= makeinput2(cols, table_name, data, "' . ucfirst($table) . '", "' . $table . '", "number", "How many items (ie: pizzas) does the customer have to customize")' . $CRLF;
                            }
                        ?>
                        HTML += makeinput2(cols, table_name, data, "Calories", "calories", "text", "for 2 items, separate with a / (ie: 200/400). For more items, use a - (ie: 200-400)");
                        HTML += makeinput2(cols, table_name, data, "Allergens", "allergens", "allergens");
                        break;
                    default:
                        HTML = table_name + " is unhandled";
                }

                HTML = '<DIV CLASS="row"><DIV CLASS="col-md-11' + HTMLclass +  '">' + HTML + '</DIV><DIV CLASS="col-md-1' + HTMLclass +  '">';
                HTML += '<BUTTON class="btn btn-danger" TITLE="Delete this item" onclick="deleteitem(' + "'" + table_name + "', '" + data["id"] + "'" + ');"><i class="fa fa-trash"></i></BUTTON> ';
                HTML += '<BUTTON class="btn btn-primary' + HTMLclass + 'undo" TITLE="Undo all changes to this item" onclick="undo(' + "'" + table_name + "', '" + data["id"] + "'" + ');"' + undostyle + '><i class="fa fa-undo"></i></BUTTON>';
                return HTML + '</DIV></DIV><DIV CLASS="col-md-12' + HTMLclass +  '"><HR></DIV>';
            }

            function makeinput2(columns, table, data, text, column, type, title){
                if(isUndefined(title)){title = "";}
                var newddata = data[column];
                if(data.hasOwnProperty(column + "_change")){
                    newddata = data[column + "_change"];
                }
                return makeinput(table, data["id"], text, column, type, data[column], title, newddata, columns);
            }

            function makeinput(table, primarykeyID, text, column, type, value, title, newddata, columns){
                if(isUndefined(newddata)){newddata = value;}
                var HTML = ' onchange="autoupdate(this);" class="autoupdate form-control';
                HTML += '" table="' + table + '" keyid="' + primarykeyID + '" column="' + column + '" NAME="' + table + '[' +  primarykeyID + "][" + column + ']" TITLE="' + title + '"';
                switch(type){
                    case "price":
                        value = Number(value).toFixed(2);
                        HTML = '<INPUT TYPE="NUMBER"' + HTML + 'VALUE="' + newddata + '" ORIGINAL="' + value + '" min="0.01" step="0.05" max="2500.00">';
                        break;
                    case "category":
                        HTML = '<INPUT TYPE="TEXT"' + HTML + 'VALUE="' + newddata + '" ORIGINAL="' + value + '" list="categories_' + table + '">';
                        break;
                    case "checkbox":
                        HTML = '<INPUT TYPE="CHECKBOX"' + HTML;
                        if(newddata.length > 0 && newddata != "0"){HTML += ' CHECKED';}
                        if(value.length > 0 && value != "0"){HTML += ' ORIGINAL="CHECKED"';} else {HTML += ' ORIGINAL=""';}
                        HTML += '>';
                        break;
                    case "allergens":
                        HTML = '<INPUT TYPE="text" READONLY' + HTML + 'ID="allergen' + primarykeyID + '" VALUE="' + newddata + '" ORIGINAL="' + value + '" ONCLICK="loadallergens(this);">';
                        break;
                    default:
                        switch(type){
                            case "number":
                                HTML += ' MIN="0"';
                                break;
                        }
                        HTML = '<INPUT TYPE="' + type + '"' + HTML + 'VALUE="' + newddata + '" ORIGINAL="' + value + '">';
                }
                return '<DIV CLASS="row"><DIV CLASS="col-md-2 itemname">' + text + ':</DIV><DIV CLASS="col-md-10">' + HTML + '</DIV></DIV>';
            }

            function loadallergens(element){
                var target = $(element).attr("id");
                var value = $(element).val().split(",");
                var allergens = ["eggs", "milk", "mustard", "peanuts", "seafood", "sesame", "soy", "sulphites", "treenuts", "wheat", "sugar", "vegetarian", "vegan"];
                var HTML = '<UL>';
                for(i=0;i<allergens.length;i++){
                    HTML += '<LI><LABEL CLASS="cursor-pointer"><INPUT TYPE="CHECKBOX" ALLERGEN="' + allergens[i] + '" ONCLICK="allergenclick(this);"> ' + ucfirst(allergens[i]) + '</LABEL></LI>';
                }
                HTML += '</UL>';
                $("#allergens").html("TESTING: " + target + " " + HTML).attr("attached-to", target);
            }
            function allergenclick(element){
                var checked =  $(element).prop("checked");
                var target = "#" + $("#allergens").attr("attached-to");
                var allergen = $(element).attr("allergen");
                var value = $(target).val().split(",");
                var includes_quantity = false;
                switch (allergen){
                    case "sugar":
                        includes_quantity = true;
                        break;
                }
                if(checked){//add
                    if(includes_quantity){
                        allergen += "=" + prompt("What would you like '" + allergen + "' to be?", "20 mg");
                    }
                    value.push(allergen);
                } else {//remove
                    var index = -1;
                    if(includes_quantity){
                        for(var i=0;i<value;i++){
                            if(value[i].startswith(allergen + "=")){
                                index = i;
                                break;
                            }
                        }
                    } else {
                        index = value.indexOf(allergen);
                    }
                    if(index>-1){
                        value = removeindex(value, index);
                    }
                }
                value = value.join(",");
                if(value.startswith(",")){
                    value = value.right(value.length-1);
                }
                $(target).val(value);
                autoupdate(target);
                log(target + "." + allergen + "=" + checked + " (" + value + ")");
            }

            (function($) {
                $.fn.currencyInput = function() {
                    this.each(function() {
                        var wrapper = $("<div class='currency-input' />");
                        $(this).wrap(wrapper);
                        $(this).before("<span class='currency-symbol'>$</span>");
                        $(this).change(function() {
                            var min = parseFloat($(this).attr("min"));
                            var max = parseFloat($(this).attr("max"));
                            var value = this.valueAsNumber;
                            if(value < min) {
                                value = min;
                            } else if(value > max) {
                                value = max;
                            }
                            $(this).val(value.toFixed(2));
                        });
                    });
                };
            })(jQuery);

            window.onbeforeunload = function (e) {
                if (haschanges>0) {
                    var message = "You have unsaved changes, are you sure you want to leave?", e = e || window.event;
                    if (e) {
                        e.returnValue = message;
                    }// For IE and Firefox
                    return message;// For Safari
                }
            };

            $(document).ready(function () {
                $("#profileinfo").remove();
                //$(".sticky-footer").remove();
            });
        </SCRIPT>
    @endif
    <?php endfile("home_editmenu"); ?>
@endsection