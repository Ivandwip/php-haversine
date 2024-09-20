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

// Instantiate the Haversine class
$haversine = new Haversine();
$data = file_get_contents('http://localhost/SIG-LOKASI-1/ambildata.php');
$locations = []; // Array untuk menyimpan lokasi dan jaraknya
$no = 1;
if (json_decode($data, true)) {
    $obj = json_decode($data);
    foreach ($obj->results as $item) {
        $distance = $haversine->calculateDistance($userLatitude, $userLongitude, $item->latitude, $item->longitude);

        // Simpan semua data ke dalam array
        $locations[] = [
            'no' => $no,
            'nama_lokasi' => $item->nama_lokasi,
            'alamat' => $item->alamat,
            'distance' => $distance,
            'id_lokasi' => $item->id_lokasi
        ];
        $no++;
    }
} else {
    echo "Data tidak ada.";
}
usort($locations, function ($a, $b) {
  return $a['distance'] <=> $b['distance']; // Ascending order
});
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
<section class="about-info-area section-gap">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6 info-left">
        <img class="img-fluid" src="img/about/info-img.jpg" alt="">
      </div>

      <div class="col-lg-30 into-right" data-aos="fade-up" data-aos-delay="100">

        <div class="col-md-12">
          <div class="panel panel-info panel-dashboard">
            <div class="panel-heading centered">

            </div>
            <div class="panel-body">
              <table class="table table-bordered table-striped table-admin">
                <thead>
                  <tr>
                    <th>Nama Lokasi</th>
                    <th>Alamat</th>
                    <th>Jarak</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
    if (!empty($locations)) {
        foreach ($locations as $location) {
    ?>
                  <tr>
                    <td><?php echo $location['nama_lokasi']; ?></td>
                    <td><?php echo $location['alamat']; ?></td>
                    <td><?php echo number_format($location['distance'], 2); ?> KM</td>
                    <td class="ctr">
                      <div class="btn-group">
                        <a href="detail.php?id_lokasi=<?php echo $location['id_lokasi']; ?>" rel="tooltip"
                          data-original-title="Lihat File" data-placement="top" class="btn btn-success">
                          <i class="fa fa-map-marker"> </i> Detail dan Lokasi
                        </a>&nbsp;
                      </div>
                    </td>
                  </tr>
                  <?php
        }
    } else {
        echo "<tr><td colspan='5'>Data tidak ada.</td></tr>";
    }
    ?>
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
  // Get the user's current location
  function getUserLocation() {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(sendLocationToServer, showError);
    } else {
      alert("Geolocation is not supported by this browser.");
    }
  }

  function sendLocationToServer(position) {
    const latitude = position.coords.latitude;
    const longitude = position.coords.longitude;

    // Use AJAX to send the data to the server
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "save_location.php", true); // URL to PHP file that handles location data
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    // Send the latitude and longitude to PHP
    xhr.send("latitude=" + latitude + "&longitude=" + longitude);

    xhr.onreadystatechange = function () {
      if (xhr.readyState == 4 && xhr.status == 200) {
        // Handle the response from PHP if necessary
        console.log("Location sent to server: " + xhr.responseText);
      }
    };
  }

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

  // Call the function to get user location when the page loads
  getUserLocation();
</script>

<?php include "footer.php"; ?>
