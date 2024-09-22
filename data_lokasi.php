<?php
include "header2.php";
include 'rumus/haversine.php'; // Include Haversine class

session_start();

// Default user location (Bekasi)
$userLatitude = -6.233889;
$userLongitude = 107.002222;

// Use the location from session if available
if (isset($_SESSION['userLatitude']) && isset($_SESSION['userLongitude'])) {
  $userLatitude = $_SESSION['userLatitude'];
  $userLongitude = $_SESSION['userLongitude'];
}


?>

<!-- start banner Area -->
<section class="about-banner relative">
  <div class="overlay overlay-bg"></div>
  <div class="container">
    <div class="row d-flex align-items-center justify-content-center">
      <div class="about-content col-lg-12">
        <h1 class="text-white">
          Data Bank Sampah
        </h1>
        <p class="text-white link-nav">Halaman ini memuat informasi Tempat Bank Sampah di Kota Bekasi</p>
      </div>
    </div>
  </div>
</section>


<!-- End banner Area -->
<!-- Start about-info Area -->
<!-- In your existing PHP file where the location data is displayed -->
<section class="about-info-area section-gap">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-30 into-right" data-aos="fade-up" data-aos-delay="100">
        <div class="col-md-12">
          <div class="panel panel-info panel-dashboard">
            <div class="panel-body">
            <div id="loading" style="text-align: center;">
              <img src="loading-spinner.gif" alt="Loading..." style="display: none;" id="loadingSpinner" />
              <p id="loadingText">Loading data, please wait...</p>
            </div>
              <table class="table table-bordered table-striped table-admin">
                <thead>
                  <tr>
                    <th>Nama Lokasi</th>
                    <th>Alamat</th>
                    <th>Jarak</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody id="locationTableBody">
                  <!-- Locations will be dynamically updated here -->
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- End about-info Area -->
<script>
// Declare userLatitude and userLongitude globally
let userLatitude = <?php echo $userLatitude; ?>;
let userLongitude = <?php echo $userLongitude; ?>;

// Get the user's current location in real-time
function getUserLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.watchPosition(sendLocationToServer, showError, {
        enableHighAccuracy: true,
        timeout: 5000,
        maximumAge: 0
        });
    } else {
        alert("Geolocation is not supported by this browser.");
    }
}

// Send the latitude and longitude to the server via AJAX
function sendLocationToServer(position) {
    userLatitude = position.coords.latitude;
    userLongitude = position.coords.longitude;

    // Use AJAX to send the data to the server
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "save_location.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    // Send the latitude and longitude to PHP
    xhr.send("latitude=" + userLatitude + "&longitude=" + userLongitude);

    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            console.log("Location sent to server: " + xhr.responseText);
            }
    };
}

// Handle location errors
function showError(error) {
    switch (error.code) {
        case error.PERMISSION_DENIED:
        alert("User denied the request for Geolocation.");
        break;
        case error.POSITION_UNAVAILABLE:
        alert("Location information is unavailable.");
        break;
        case error.TIMEOUT:
        alert("The request to get user location timed out.");
        break;
        case error.UNKNOWN_ERROR:
        alert("An unknown error occurred.");
        break;
    }
}

// Refresh location data every 10 seconds
function refreshLocations() {
// Show loading spinner
document.getElementById('loadingSpinner').style.display = 'block';
document.getElementById('loadingText').style.display = 'block';

$.ajax({
url: 'http://localhost/php-haversine/ambildata.php', // Replace with your actual data endpoint
type: 'GET',
dataType: 'json',
success: function (data) {
const tableBody = document.getElementById('locationTableBody');
tableBody.innerHTML = ''; // Clear current content

if (data && data.results) {
// Array to hold locations with their calculated distance
const locationsWithDistance = [];

// Calculate distance for each location and store in the array
data.results.forEach((location) => {
const distance = calculateDistance(userLatitude, userLongitude, location.latitude, location.longitude);
locationsWithDistance.push({
...location,
distance: distance
});
});

// Sort locations by distance (ascending)
locationsWithDistance.sort((a, b) => a.distance - b.distance);

// Hide loading spinner once data is loaded
document.getElementById('loadingSpinner').style.display = 'none';
document.getElementById('loadingText').style.display = 'none';

// Now populate the table with sorted data
locationsWithDistance.forEach((location) => {
const row = `
<tr>
  <td>${location.nama_lokasi}</td>
  <td>${location.alamat}</td>
  <td>${location.distance.toFixed(2)} KM</td>
  <td>
    <a href="detail.php?id_lokasi=${location.id_lokasi}" class="btn btn-success">
      <i class="fa fa-map-marker"></i> Detail dan Lokasi
    </a>
  </td>
</tr>`;
tableBody.innerHTML += row;
});
}
},
error: function () {
alert('Failed to refresh locations');

// Hide loading spinner in case of error
document.getElementById('loadingSpinner').style.display = 'none';
document.getElementById('loadingText').style.display = 'none';
}
});
}

// Automatically refresh locations every 10 seconds
setInterval(refreshLocations, 10000);

// Call refreshLocations when the page first loads to display the initial data
window.onload = refreshLocations;


// Function to calculate distance (optional, if done on the client side)
function calculateDistance(lat1, lon1, lat2, lon2) {
const R = 6371; // Radius of the Earth in kilometers
const dLat = (lat2 - lat1) * (Math.PI / 180);
const dLon = (lon2 - lon1) * (Math.PI / 180);
const a =
Math.sin(dLat / 2) * Math.sin(dLat / 2) +
Math.cos(lat1 * (Math.PI / 180)) * Math.cos(lat2 * (Math.PI / 180)) *
Math.sin(dLon / 2) * Math.sin(dLon / 2);
const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
const distance = R * c; // Distance in kilometers
return distance;
}

</script>

<?php include "footer.php"; ?>
