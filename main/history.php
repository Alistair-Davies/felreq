<?php

    require("functions_history.php");
    if (!isset($_GET['view'])) {
        //some sort of header
        echo "<div class='listOflinks'>";
        include("weekLinks.php");
        echo "</div>";
    }
    else {
        $dbname = 'felstedreq';
        $dbuser = 'admin';
        $dbhost = 'localhost';
        $dbpass = '1234';
        $link = mysqli_connect( $dbhost, $dbuser, $dbpass, $dbname)
        or die( mysqli_connect_error() );
        mysqli_select_db( $link, $dbname )
        or die("Could not open the db '$dbname'");

        echo '<div class="header">';
        echo '<form method="GET" action=""><input type="submit" value="back"/></form></div>';
        insertHistory($_GET['view']);
        generateTable($_GET['view']);
    }
?>
