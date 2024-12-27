<?php
require '../_base.php'; // Include base functions and database connection
?>

<!DOCTYPE html>
<html>
<head>
    <title>hehe</title>
</head>
<body>
<form id="address-form" action="" method="post">
            <input type="hidden" name="form_type" value="address_management" />
            <input type="hidden" name="action" id="action" value="save-address" />
            <input type="hidden" name="index" id="address-index" value="" />
            <div style="position: relative;">
                <input
                    id="address-input"
                    type="text"
                    placeholder="Enter your address"
                    autocomplete="off"
                    class="input-box" />
            </div>

            <div id="location-container">
                <div id="map" style="width: 100%; height: 400px; margin-top: 20px;"></div>
                <div id="coordinates">
                    <p>Latitude: <span id="latitude">0</span></p>
                    <p>Longitude: <span id="longitude">0</span></p>
                </div>
            </div>

            <button class="btn" type="submit" id="save-address-btn">Add Address</button>
        </form>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBFPpOlKxMJuu6PxnVrwxNd1G6vERpptro&libraries=places"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const addressInput = document.getElementById("address-input");
        const mapContainer = document.getElementById("map");
        const latSpan = document.getElementById("latitude");
        const lngSpan = document.getElementById("longitude");

        let map, marker;

        // Initialize Google Places Autocomplete
        const autocomplete = new google.maps.places.Autocomplete(addressInput, {
            types: ['geocode'], // Restrict to geocoded results
            componentRestrictions: {
                country: "MY"
            }, // Optional: Restrict to Malaysia
        });

        // Initialize the map
        function initMap() {
            const initialPosition = {
                lat: 3.139,
                lng: 101.686
            }; // Default: Kuala Lumpur
            map = new google.maps.Map(mapContainer, {
                center: initialPosition,
                zoom: 12,
            });

            // Place a draggable marker on the map
            marker = new google.maps.Marker({
                position: initialPosition,
                map: map,
                draggable: true,
            });

            // Update latitude and longitude on marker drag
            marker.addListener("dragend", () => {
                const position = marker.getPosition();
                updateCoordinates(position.lat(), position.lng());
            });

            // Update map position when address is selected
            autocomplete.addListener("place_changed", () => {
                const place = autocomplete.getPlace();
                if (place.geometry) {
                    const location = place.geometry.location;
                    map.setCenter(location);
                    marker.setPosition(location);
                    updateCoordinates(location.lat(), location.lng());
                }
            });

            // Update coordinates on map click
            map.addListener("click", (e) => {
                marker.setPosition(e.latLng);
                updateCoordinates(e.latLng.lat(), e.latLng.lng());
            });
        }

        // Update the latitude and longitude display
        function updateCoordinates(lat, lng) {
            latSpan.textContent = lat.toFixed(6);
            lngSpan.textContent = lng.toFixed(6);
        }

        // Load the map
        initMap();
    });
</script>
<?php include '../_foot.php'; ?>
