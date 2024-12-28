document.addEventListener("DOMContentLoaded", function () {
  const line1Input = document.getElementById("line-1");
  const villageInput = document.getElementById("village");
  const postalCodeInput = document.getElementById("postal-code");
  const cityInput = document.getElementById("city");
  const stateInput = document.getElementById("state");
  const mapContainer = document.getElementById("map");
  const latSpan = document.getElementById("latitude");
  const lngSpan = document.getElementById("longitude");
  const useMyLocationBtn = document.getElementById("use-my-location-btn");

  let map, marker, geocoder;

  // Initialize Google Places Autocomplete
  const autocomplete = new google.maps.places.Autocomplete(line1Input, {
    types: ["geocode"],
    componentRestrictions: { country: "MY" },
  });

  // Dynamically position pac-container on stateInput focus
  stateInput.addEventListener("focus", function () {
    const pacContainer = document.querySelector(".pac-container");
    if (pacContainer) {
      const stateRect = stateInput.getBoundingClientRect();
      pacContainer.style.top = `${stateRect.bottom + window.scrollY}px`;
      pacContainer.style.left = `${stateRect.left + window.scrollX}px`;
      pacContainer.style.width = `${stateInput.offsetWidth}px`;
      pacContainer.style.zIndex = 10000; // Ensure it's on top
    }
  });

  // Initialize the map
  function initMap() {
    const initialPosition = { lat: 3.139, lng: 101.686 }; // Default: Kuala Lumpur
    map = new google.maps.Map(mapContainer, {
      center: initialPosition,
      zoom: 12,
    });

    geocoder = new google.maps.Geocoder();

    // Place a draggable marker on the map
    marker = new google.maps.Marker({
      position: initialPosition,
      map: map,
      draggable: true,
    });

    // Update coordinates and address on marker drag
    marker.addListener("dragend", () => {
      const position = marker.getPosition();
      updateAddressFromCoordinates(position.lat(), position.lng());
    });

    // Update map and address when autocomplete is selected
    autocomplete.addListener("place_changed", () => {
      const place = autocomplete.getPlace();
      if (place.geometry) {
        const location = place.geometry.location;
        map.setCenter(location);
        marker.setPosition(location);
        updateAddressFromComponents(place.address_components);
      }
    });

    // Update address on map click
    map.addListener("click", (e) => {
      const lat = e.latLng.lat();
      const lng = e.latLng.lng();
      marker.setPosition(e.latLng);
      updateAddressFromCoordinates(lat, lng);
    });
  }

  // Update address fields from Google Maps components
  function updateAddressFromComponents(components) {
    let address = {
      line_1: "",
      village: "",
      postal_code: "",
      city: "",
      state: "",
    };

    components.forEach((component) => {
      const types = component.types;
      if (types.includes("street_number")) {
        address.line_1 = component.long_name + " " + address.line_1;
      }
      if (types.includes("route")) {
        address.line_1 += component.long_name;
      }
      if (types.includes("neighborhood") || types.includes("sublocality")) {
        address.village = component.long_name;
      }
      if (types.includes("postal_code")) {
        address.postal_code = component.long_name;
      }
      if (types.includes("locality")) {
        address.city = component.long_name;
      }
      if (types.includes("administrative_area_level_1")) {
        address.state = component.long_name;
      }
    });

    // Fill the form fields
    line1Input.value = address.line_1;
    villageInput.value = address.village;
    postalCodeInput.value = address.postal_code;
    cityInput.value = address.city;
    stateInput.value = address.state;
  }

  // Update address fields based on coordinates
  function updateAddressFromCoordinates(lat, lng) {
    latSpan.textContent = lat.toFixed(6);
    lngSpan.textContent = lng.toFixed(6);

    const latLng = { lat: lat, lng: lng };
    geocoder.geocode({ location: latLng }, (results, status) => {
      if (status === "OK" && results[0]) {
        updateAddressFromComponents(results[0].address_components);
      } else {
        console.error("Geocoder failed: " + status);
      }
    });
  }

  // Use current location to update map and address
  useMyLocationBtn.addEventListener("click", (e) => {
    e.preventDefault();
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        (position) => {
          const lat = position.coords.latitude;
          const lng = position.coords.longitude;
          const latLng = { lat, lng };
          map.setCenter(latLng);
          marker.setPosition(latLng);
          updateAddressFromCoordinates(lat, lng);
        },
        (error) => {
          console.error("Error getting location: " + error.message);
          alert("Unable to retrieve your location.");
        }
      );
    } else {
      alert("Geolocation is not supported by your browser.");
    }
  });

  initMap();
});
