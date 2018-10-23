<?php
function check_user($type) {
    session_start();
    if (!isset($_SESSION[$type])) {
        header("Location:login.php");
    }
}

function db_connect() {
    $dbname = 'felstedreq';
    $dbuser = 'admin';
    $dbhost = 'localhost';
    $dbpass = '1234';
    $link = mysqli_connect( $dbhost, $dbuser, $dbpass, $dbname)
    or die( mysqli_connect_error() );
    mysqli_select_db( $link, $dbname )
    or die("Could not open the db '$dbname'");
    return $link;
}

function do_logging($message, $level){
    $date = date("Y-m-d h:m:s");
    $file = __FILE__;
    error_log("[ $date ] [ $level ] [ $file ] $message".PHP_EOL,3,"/var/log/nginx/felreq");
}

function getStartAndEndDate($weeknum, $year) {
    $time = strtotime("1 January $year", time());
    $day = date('w', $time);
    $time += ((7*$weeknum)+1-$day)*24*3600;
    $firstWeek[0] = date('d/m/y', $time);
    for ($i=1; $i<7; $i++) {
        $time += 86400;
        $firstWeek[$i] = date('d/m/y', $time);
    }
    $time += 86400;
    $secondWeek[0] = date('d/m/y', $time);
    for ($i=1; $i<7; $i++) {
        $time += 86400;
        $secondWeek[$i] = date('d/m/y', $time);
    }
    if ($weeknum%2==0) {
        $firstWeek[7] = 'A';
        $secondWeek[7] = 'B';
    }
    else {
        $firstWeek[7] = 'B';
        $secondWeek[7] = 'A';
    }
    $return = array(
        $firstWeek,
        $secondWeek,
    );
    return $return;
}

function insertreq() {
    global $link;
    $processedDesc = str_replace(array("\\n","\r\n", "\n"), '<br/>',$_POST['desc']);
    $processedRacc = str_replace(array("\\n","\r\n", "\n"), '<br/>',$_POST['rac']);
    $newreq = array($_POST['lesson_id'], htmlspecialchars(str_replace(array("\\n","\r\n", "\n"), ' ', $_POST['title']), ENT_QUOTES), htmlspecialchars($processedDesc, ENT_QUOTES),$_POST['rass'], htmlspecialchars($processedRacc, ENT_QUOTES));
    $insert = "INSERT INTO requisition VALUES (DEFAULT, '$newreq[1]', '$newreq[2]', '$newreq[3]', '$newreq[4]', $newreq[0], FALSE);";
    if (mysqli_query($link, $insert)) {
        do_logging("Successful MYSQL query: $insert", "INFO");
        header('Location:'.$_SERVER['PHP_SELF']);
    }
    else {
        do_logging("Unsuccessful MYSQL query: $insert", "ERROR");
        echo "<p> Error: ".$insert."<br/>".mysqli_error($link);
    }
    $_POST=array();
}

function removereq($rid) {
    global $link;
    $remove = "DELETE FROM requisition WHERE requisition_id=$rid;";
    if (mysqli_query($link, $remove)) {
        do_logging("Successful MYSQL query: $remove", "INFO");
        header('Location:'.$_SERVER['PHP_SELF']);
    }
    else {
        do_logging("Unsuccessful MYSQL query: $remove", "ERROR");
        echo "<p> Error: ".$remove."<br/>".mysqli_error($link);
    }
    $_POST=array();
}

function updatereq($rid) {
    global $link;
    $processedDesc = str_replace(array("\\n","\r\n", "\n"), '<br/>',$_POST['desc']);
    $processedRacc = str_replace(array("\\n","\r\n", "\n"), '<br/>',$_POST['rac']);
    $updatereq = array(htmlspecialchars(str_replace(array("\\n","\r\n", "\n"), ' ', $_POST['title']), ENT_QUOTES), htmlspecialchars($processedDesc, ENT_QUOTES), $_POST['rass'], htmlspecialchars($processedRacc, ENT_QUOTES));
    $update = "UPDATE requisition SET title='$updatereq[0]', description='$updatereq[1]', risk_assessment='$updatereq[2]', risk_actions='$updatereq[3]' WHERE requisition_id=$rid";
    if (mysqli_query($link, $update)) {
        do_logging("Successful MYSQL query: $update", "INFO");
        header('Location:'.$_SERVER['PHP_SELF']);
    }
    else {
        do_logging("Unsuccessful MYSQL query: $update", "ERROR");
        echo "<p> Error: ".$update."<br/>".mysqli_error($link);
    }
    $_POST=array();
}
?>
