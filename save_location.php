<?php
if (isset($_POST['latitude']) && isset($_POST['longitude'])) {
  // Get the user's latitude and longitude
  $userLatitude = $_POST['latitude'];
  $userLongitude = $_POST['longitude'];

  // Store the user's location in the session or handle as needed
  session_start();
  $_SESSION['userLatitude'] = $userLatitude;
  $_SESSION['userLongitude'] = $userLongitude;

  // Optionally, return a success message
  echo "Location received: Latitude = $userLatitude, Longitude = $userLongitude";
} else {
  echo "No location data received.";
}
?>
