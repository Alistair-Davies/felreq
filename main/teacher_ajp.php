<?php
  require("functions.php");
  if (!isset($_GET['teach']) || ! strcmp($_GET['teach'],'AJP')) {
      $teach = "AJP";
      $enableEdit=1;
  }
  else {
    $teach=$_GET['teach'];
    $enableEdit = 0;
  }
  require("defaultlayout_teach.php");
?>



