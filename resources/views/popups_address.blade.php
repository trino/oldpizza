<?php
    startfile("popups_address");
    if (!isset($style))     {$style = 0;}
    if (!isset($firefox))   {$firefox = true;}
    if (!isset($required))  {$required = "";} else {$required = " required";}
    if (!isset($class))     {$class = "";} else {$class = " " . $class;}
    if (!isset($icons))     {$icons = false;}
    if (isset($autored))    {$required .= ' autored="' . $autored . '"';}

    switch ($style) {
        case 0:
            echo '<DIV CLASS="row"><DIV CLASS="col-md-2">Address</DIV><DIV CLASS="col-md-10" ID="gmapc">';
            echo '<INPUT class="form-control" TYPE="text" ID="formatted_address" ' . $required . ' name="formatted_address"></div></DIV>';
            break;
        case 1:
            if($icons) {echo '<div class="input_left_icon"><span class="fa-stack fa-2x"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-home text-white fa-stack-1x"></i></span></div><div class="input_right">';}
            echo '<SPAN ID="gmapc"><INPUT TYPE="text" ID="formatted_address" PLACEHOLDER="Start by Typing Address" CLASS="form-control formatted_address' . $class . '"' . $required . ' name="formatted_address"></SPAN>';
            if($icons) {echo '</div>';}
            echo '<STYLE>.address:focus{z-index: 999;}</STYLE>';
            break;
    }
    if (!isset($user_id)) {$user_id = read("id");}
    if (!isset($form)) {$form = true;}
?>
<STYLE>
    .pac-container {
        z-index: 99999999999 !important;
    }
</STYLE>
    @if($form) <FORM ID="googleaddress"> @endif
        @if($icons) <div class="input_left_icon"><span class="fa-stack fa-2x"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-pencil text-white fa-stack-1x"></i></span></div><div class="input_right"> @endif
        <INPUT TYPE="text" NAME="unit" ID="add_unit" PLACEHOLDER="Address Notes" CLASS="form-control address" TITLE="ie: Apt/Unit, buzz code, which door to go to">
        @if($icons) </div> @endif
        <INPUT TYPE="text" NAME="number" ID="add_number" PLACEHOLDER="Street Number" {{ $required }} CLASS="form-control street_number address dont-show">
        <INPUT TYPE="text" NAME="street" ID="add_street" PLACEHOLDER="Street" {{ $required }} CLASS="form-control route address dont-show">
        <INPUT TYPE="text" NAME="city" ID="add_city" PLACEHOLDER="City" {{ $required }} CLASS="form-control locality address dont-show">
        <INPUT TYPE="text" NAME="province" ID="add_province" PLACEHOLDER="Province" {{ $required }} CLASS="form-control administrative_area_level_1 address dont-show">
        <INPUT TYPE="text" NAME="postalcode" ID="add_postalcode" PLACEHOLDER="Postal Code" {{ $required }} CLASS="form-control postal_code address dont-show">
        <INPUT TYPE="text" NAME="latitude" ID="add_latitude" PLACEHOLDER="Latitude" {{ $required }} CLASS="form-control latitude address dont-show">
        <INPUT TYPE="text" NAME="longitude" ID="add_longitude" PLACEHOLDER="Longitude" {{ $required }} CLASS="form-control longitude address dont-show">
        <INPUT TYPE="hidden" NAME="user_id" ID="add_user_id" PLACEHOLDER="user_id" {{ $required }} CLASS="form-control session_id_val address" value="{{$user_id}}">
    @if($form) </FORM> @endif

<SCRIPT>
    //if($firefox)
        if(is_firefox_for_android) {
            $(window).load(function () {
                var HTML = $("#gmapc").html();
                HTML = HTML.replaceAll("style=", "oldstyle=");
                log("Moving: " + HTML);
                $("#gmapffac").html(HTML);
                $("#gmapc").html('<DIV CLASS="fake-form-control"><SPAN CLASS="address fake-address" ID="ffaddress"></SPAN><BUTTON CLASS="btn btn-sm btn-primary radius0 pull-right full-height" ONCLICK="handlefirefox();return false;">EDIT</BUTTON></DIV><DIV CLASS="separator"></DIV>');
                initAutocomplete();
            });
        }
    //endif

    function editaddresses() {
        $("#checkoutmodal").modal("hide");
        $("#profilemodal").modal("show");
    }

    function isnewaddress(number, street, city) {
        var AddNew = number && street && city;
        $("#saveaddresses option").each(function () {
            var ID = $(this).val();
            if (ID > 0) {
                if (number.isEqual($(this).attr("number")) && street.isEqual($(this).attr("street")) && city.isEqual($(this).attr("city"))) {
                    return false;
                }
            }
        });
        return AddNew;
    }

    function deleteaddress(ID) {
        if (ID < 0) {//add new address
            var address = getform("#orderinfo");
            $.post(webroot + "placeorder", {
                _token: token,
                info: address
            }, function (result) {
                address["id"] = result;
                var HTML = AddressToOption(address);
                $(".saveaddresses").append(HTML);
                if (ID == -1) {
                    addresses();
                }
            });
        } else {
            confirm2("Are you sure you want to delete '" + $("#add_" + ID).text().trim() + "'?", 'Delete Address', function () {
                $.post("<?= webroot("public/list/useraddresses"); ?>", {
                    _token: token,
                    action: "deleteitem",
                    id: ID
                }, function (result) {
                    if (handleresult(result)) {
                        $("#add_" + ID).fadeOut(500, function () {
                            $("#add_" + ID).remove();
                        });
                        $(".saveaddresses option[value=" + ID + "]").remove();
                    }
                });
            });
        }
    }

    function initAutocomplete() {
        var cityBounds = new google.maps.LatLngBounds(
                new google.maps.LatLng(42.873863, -81.501312),//southWest
                new google.maps.LatLng(43.043212, -81.092071)//northEast
        );//london ontario boundaries

        formatted_address = new google.maps.places.Autocomplete(
                /** @type {!HTMLInputElement} */(document.getElementById('formatted_address')), {
                    bounds: cityBounds,//limit to London Ontario
                    types: ['geocode'],
                    componentRestrictions: {country: "ca"}
                });
        formatted_address.addListener('place_changed', fillInAddress);
    }

    function fillInAddress() {
        // Get the place details from the formatted_address object.
        var place = formatted_address.getPlace();
        log(JSON.stringify(place));
        var lat = place.geometry.location.lat();
        var lng = place.geometry.location.lng();

        var addressdata = {};

        $('.formatted_fordb').val(place.formatted_address); // this formatted_address is a google maps object
        $('.latitude').val(lat);
        $('.longitude').val(lng);

        var componentForm = {
            street_number: 'short_name',
            //route: 'long_name',//street name
            route: 'short_name',//street name
            locality: 'long_name',//ON Canada
            administrative_area_level_1: 'long_name',
            country: 'long_name',
            postal_code: 'short_name'
        };
        //2396 Kingsway, locality: Vancouver, administrative_area_level_1: British Columbia, country: Canada, postal_code: V5R 5G9
        var streetformat = "[street_number] [route], [locality], [administrative_area_level_1_s]";// [postal_code]";
        for (var i = 0; i < place.address_components.length; i++) {
            var addressType = place.address_components[i].types[0];
            if (componentForm[addressType]) {
                var val = place.address_components[i][componentForm[addressType]];
                addressdata[addressType] = val;
                streetformat = streetformat.replace("[" + addressType + "]", val);
                $('.' + addressType).val(val);

                val = place.address_components[i]['short_name'];
                streetformat = streetformat.replace("[" + addressType + "_s]", val);
                val = place.address_components[i]['long_name'];
                streetformat = streetformat.replace("[" + addressType + "_l]", val);
            }
        }
        if (isnewaddress(addressdata["street_number"], addressdata["route"], addressdata["locality"])) {
            $("#saveaddressbtn").removeAttr("disabled");
        } else {
            $("#saveaddressbtn").attr("disabled", true);
        }
        $('.formatted_address').val(streetformat);
        place.formatted_address = streetformat;
        @if(isset($findclosest))
            if (isFunction(addresshaschanged)) {
                addresshaschanged(place);
            }
        @endif
        return place;
    }
</SCRIPT>
<?php
    if (!isset($dontincludeGoogle)) {
        echo '<script src="https://maps.googleapis.com/maps/api/js?signed_in=true&libraries=places&callback=initAutocomplete&key=AIzaSyBWSUc8EbZYVKF37jWVCb3lpBQwWqXUZw8"></script>';
    } else {
        ?>
            <SCRIPT LANGUAGE="JavaScript">
                window.onload = function () {
                    log("init autocomplete");
                    initAutocomplete();
                };
            </SCRIPT>
        <?php
    }
    endfile("popups_address");
?>