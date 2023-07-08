<?php

require('../database.php');

if (isset($_GET['id'])) {
  $id = $_GET['id'];

  // Delete the record from the database
  $sql = "DELETE FROM pegawai WHERE id = $id";
  if ($conn->query($sql) === TRUE) {
      echo "Record deleted successfully";
      header("Refresh:0;url=index.php");
    exit;
  } else {
      echo "Error deleting record: " . $conn->error;
  }
}