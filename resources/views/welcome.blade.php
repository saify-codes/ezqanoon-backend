<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Boundary Search Map with Autocomplete</title>
  <style>
    html, body { height: 100%; margin: 0; padding: 0; }
    #map { height: 100%; width: 100%; }
    #controls {
      position: absolute;
      top: 10px; left: 50%;
      transform: translateX(-50%);
      z-index: 5;
      background: rgba(255,255,255,0.9);
      padding: 8px; border-radius: 4px;
    }
    #search-input { width: 250px; padding: 4px; }
    #search-btn { padding: 4px 8px; }
  </style>
  <script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCdnc_ajbCbaokL69YrB11WT0lvhFSWBFI&token=93297&libraries=places&callback=initMap"
    async defer>
  </script>
</head>
<body>
  <div id="controls">
    <input id="search-input" placeholder="Enter location (e.g. Pechs Block 6)" />
    <button id="search-btn">Search</button>
  </div>
  <div id="map"></div>

  <script>
    let map, autocomplete;

    function initMap() {
      map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: 24.8607, lng: 67.0011 },
        zoom: 12,
      });

      const input = document.getElementById("search-input");
      autocomplete = new google.maps.places.Autocomplete(input, {
        types: ["geocode"],
        // componentRestrictions: { country: "pk" }  // optional
      });
      autocomplete.setFields(["formatted_address"]);
      autocomplete.addListener("place_changed", () => {
        const place = autocomplete.getPlace();
        if (!place.formatted_address) {
          return alert("Please select an option from the dropdown.");
        }
        searchBoundary(place.formatted_address);
      });

      document.getElementById("search-btn").addEventListener("click", () => {
        const q = input.value.trim();
        if (!q) return alert("Please enter a location name.");
        searchBoundary(q);
      });
    }

    function searchBoundary(query) {
      const url = "https://nominatim.openstreetmap.org/search?" +
        new URLSearchParams({
          q: query,
          format: "geojson",
          polygon_geojson: 1,
          limit: 1
        });

      fetch(url)
        .then(res => res.json())
        .then(geojson => {
          if (!geojson.features || geojson.features.length === 0) {
            return alert(`No boundary found for "${query}".`);
          }
          map.data.forEach(f => map.data.remove(f));
          map.data.addGeoJson(geojson);
          const bounds = new google.maps.LatLngBounds();
          map.data.forEach(feature =>
            feature.getGeometry().forEachLatLng(latlng => bounds.extend(latlng))
          );
          map.fitBounds(bounds);
        })
        .catch(err => {
          console.error(err);
          alert("Error fetching boundary data.");
        });
    }
  </script>
</body>
</html>
