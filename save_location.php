<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['latitude']) && isset($_POST['longitude'])) {
        // Store the latitude and longitude in the session
        $_SESSION['userLatitude'] = $_POST['latitude'];
        $_SESSION['userLongitude'] = $_POST['longitude'];

        echo "Location saved successfully";
        } else {
        echo "Location data is missing";
        }
        } else {
        echo "Invalid request method";
}
?>
