<style>
    #map {
        height: 500px;
        border-color: red;
    }
</style>
<script>
    var map, infowindow, service, markers = new Array();
    $(window).load(function () {
        <?php
            startfile("popups_googlemaps");
            if(isset($latitude) && isset($longitude)){
                echo 'initMap(' . $latitude . ', ' . $longitude . ');';
            }
        ?>
    });

    function initMap(latitude, longitude) {
        if(isUndefined(latitude) || isUndefined(longitude)){log("FAILURE TO INIT MAPS"); return;}
        log("initMap: " + latitude + ", " + longitude);
        map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: latitude, lng: longitude},
            zoom: 15
        });

        infowindow = new google.maps.InfoWindow();
        service = new google.maps.places.PlacesService(map);
    }

    function addmarker(Name, Latitude, Longitude){
        var myLatlng = new google.maps.LatLng(parseFloat(Latitude),parseFloat(Longitude));
        var marker = new google.maps.Marker({
            map: map,
            position: myLatlng
        });
        google.maps.event.addListener(marker, 'click', function() {
            infowindow.setContent(Name);
            infowindow.open(map, this);
        });
    }

    function addmarker2(Name, Latitude, Longitude, Delimiter, Prepend, Append){
        if(isUndefined(Name)){
            for(var id = 0; id < markers.length; id++){
                addmarker(markers[id]["Prepend"] + markers[id]["Name"] + markers[id]["Append"], markers[id]["Latitude"], markers[id]["Longitude"]);
            }
        } else {
            if(isUndefined(Prepend)){Prepend="";}
            if(isUndefined(Append)){Append="";}
            if(isUndefined(Delimiter)){Delimiter="<BR>";}
            for(var id = 0; id < markers.length; id++){
                if(markers[id]["Latitude"] == Latitude && markers[id]["Longitude"] == Longitude){
                    markers[id]["Name"] += Delimiter + Name;
                    return id;
                }
            }
            markers.push({Name: Name, Latitude: Latitude, Longitude: Longitude, Prepend: Prepend, Append: Append});
        }
    }
</script>
<div id="map"></div>
@if(!isset($includeapi))
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBWSUc8EbZYVKF37jWVCb3lpBQwWqXUZw8&signed_in=true&libraries=places&callback=initMap" async defer></script>
@else
    <SCRIPT>
        //$(window).load(function() {
           //initMap();
        //});
    </SCRIPT>
@endif
<?php endfile("popups_googlemaps"); ?>