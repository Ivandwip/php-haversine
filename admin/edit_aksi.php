<?php
// koneksi database
include '../koneksi.php';

// menangkap data yang di kirim dari form
$id = $_POST['id_lokasi'];
$nama = $_POST['nama_lokasi'];
$alamat = $_POST['alamat'];
$deskripsi = $_POST['deskripsi'];
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];

// update data ke database
mysqli_query($koneksi, "update lokasi set nama_lokasi='$nama', alamat='$alamat', deskripsi='$deskripsi', latitude='$latitude', longitude='$longitude' where id_lokasi='$id'");

// mengalihkan halaman kembali ke index.php
header("location:tampil_data.php");
