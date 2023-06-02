<?php
$host = "0.0.0.0"; // nama host database
$username = "root"; // username database
$password = "adam1703"; // password database
$dbname = "sisedm"; // nama database

// membuat koneksi ke database
$conn = new mysqli($host, $username, $password, $dbname);

// memeriksa apakah terdapat error pada koneksi
if ($conn->connect_error) {
  die("Koneksi ke database gagal: " . $conn->connect_error);
}

$conn->query("SET sql_mode = '';");
