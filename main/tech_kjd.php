<?php
  require("functions.php");
  check_user('tech');
  db_connect();
  require("functions_tech.php");
  $subject = "CHEM";
  require("defaultlayout_tech.php");
?>



