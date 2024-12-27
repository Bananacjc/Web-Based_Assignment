document.addEventListener("DOMContentLoaded", function () {
  const addressInput = document.getElementById("address-input");
  const mapContainer = document.getElementById("map");
  const latSpan = document.getElementById("latitude");
  const lngSpan = document.getElementById("longitude");
  const geolocationButton = document.createElement("button");
  geolocationButton.textContent = "Use My Location";
  geolocationButton.classList.add("btn", "use-location-btn");
  mapContainer.parentElement.insertBefore(geolocationButton, mapContainer);

  let map, marker, geocoder;

  $(addressInput).on("focus", function () {
    const pacContainer = $(".pac-container");
    $(this).parent().append(pacContainer);
  });

  // Initialize Google Places Autocomplete
  const autocomplete = new google.maps.places.Autocomplete(addressInput, {
    types: ["geocode"], // Restrict to geocoded results
    componentRestrictions: {
      country: "MY",
    }, // Optional: Restrict to Malaysia
  });

  // Initialize the map
  function initMap() {
    const initialPosition = {
      lat: 3.139,
      lng: 101.686,
    }; // Default: Kuala Lumpur
    map = new google.maps.Map(mapContainer, {
      center: initialPosition,
      zoom: 12,
    });

    // Initialize the geocoder
    geocoder = new google.maps.Geocoder();

    // Place a draggable marker on the map
    marker = new google.maps.Marker({
      position: initialPosition,
      map: map,
      draggable: true,
    });

    // Update latitude, longitude, and address on marker drag
    marker.addListener("dragend", () => {
      const position = marker.getPosition();
      const lat = position.lat();
      const lng = position.lng();
      updateCoordinates(lat, lng);
      getAddressFromCoordinates(lat, lng);
    });

    // Update map position and marker when address is selected
    autocomplete.addListener("place_changed", () => {
      const place = autocomplete.getPlace();
      if (place.geometry) {
        const location = place.geometry.location;
        map.setCenter(location);
        marker.setPosition(location);
        const lat = location.lat();
        const lng = location.lng();
        updateCoordinates(lat, lng);
        // Autofill address if selected from the autocomplete
        if (place.formatted_address) {
          addressInput.value = place.formatted_address;
        }
      }
    });

    // Update coordinates and address on map click
    map.addListener("click", (e) => {
      const lat = e.latLng.lat();
      const lng = e.latLng.lng();
      marker.setPosition(e.latLng);
      updateCoordinates(lat, lng);
      getAddressFromCoordinates(lat, lng);
    });
  }

  // Update the latitude and longitude display
  function updateCoordinates(lat, lng) {
    latSpan.textContent = lat.toFixed(6);
    lngSpan.textContent = lng.toFixed(6);
  }

  // Get the address from latitude and longitude using Geocoder
  function getAddressFromCoordinates(lat, lng) {
    const latLng = {
      lat: lat,
      lng: lng,
    };
    geocoder.geocode(
      {
        location: latLng,
      },
      (results, status) => {
        if (status === "OK" && results[0]) {
          addressInput.value = results[0].formatted_address; // Autofill the address input
        } else {
          console.error("Geocoder failed: " + status);
        }
      }
    );
  }

  // Get the user's current location
  function getCurrentLocation() {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        (position) => {
          const lat = position.coords.latitude;
          const lng = position.coords.longitude;
          const latLng = {
            lat,
            lng,
          };
          map.setCenter(latLng);
          marker.setPosition(latLng);
          updateCoordinates(lat, lng);
          getAddressFromCoordinates(lat, lng); // Autofill address input
        },
        (error) => {
          console.error("Error getting location:", error.message);
          alert(
            "Unable to retrieve your location. Please allow location access."
          );
        }
      );
    } else {
      alert("Geolocation is not supported by your browser.");
    }
  }

  // Add event listener to geolocation button
  geolocationButton.addEventListener("click", (e) => {
    e.preventDefault();
    getCurrentLocation();
  });

  // Load the map
  initMap();
});
