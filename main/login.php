<html>
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
      .container{
          width:30%;
          position:relative;
          left:50%;
          top:35%;
          transform: translate(-50%, -35%);
          height:30%;
      }
      #formcontainer {

          background-color:#F5F4F3;
          border: 2px #B4B1AE solid;
          border-radius: 10px;
          height:100%;
          width:100%;
      }

      #loginForm {
          position:relative;
          left:50%;
          top:40%;
          transform: translate(-50%, -40%);
          display:inline-block;
          text-align: center;
          font-size: 24px;
      }

      #usr {
          padding:10px 16px;
          border-radius:5px;
          margin:15px;
          border:1px #ccc solid;
      }

      #title {
          text-align: center;
          font-size: 28px;
      }



      #loginButton:hover {
          cursor:pointer;
          box-shadow: 0 2px 5px 0 rgba(0,0,0,0.24), 0 15px 40px 0 rgba(0,0,0,0.19);
      }

      #loginButton {
          background-color: AliceBlue;
          border: none;
          border-radius: 3px;
          color: GrayText;
          padding: 15px 32px;
          text-align: center;
          text-decoration: none;
          display: inline-block;
          font-size: 16px;
          -webkit-transition-duration: 0.4s;
          margin:10px;
          margin-top:15px;
      }

      #fail {
          display: inline-block;
          text-align: center;
          margin-top:10%;
          left:50%;
          position:relative;
          transform: translate(-50%, -0%);
          color: red;
      }

      .loginInformation {
          background: aliceblue;
          border: dashed 1px midnightblue;
          padding: 25px;
          font-size: 18px;
          border-radius: 10px;
          margin-top: 10px;
          text-align:center;
      }

      .logInformation p {
          display:inline-block;
      }

      .loginInformation b {
          font-size:40px;
          float:left;
      }
    </style>
</head>

<body>
<?php

session_start();
$_SESSION = array();
session_destroy();

$dbname = 'felstedreq';
$dbuser = 'admin';
$dbhost = 'localhost';
$dbpass = '1234';
$link = mysqli_connect( $dbhost, $dbuser, $dbpass, $dbname)
or die( mysqli_connect_error() );
mysqli_select_db( $link, $dbname )
or die("Could not open the db '$dbname'");

function do_logging($message, $level){
    $date = date("Y-m-d h:m:s");
    $file = __FILE__;
    error_log("[ $date ] [ $level ] [ $file ] $message".PHP_EOL,3,"/var/log/nginx/felreq");
}

if (isset($_POST['usr'])) {
    $usr =  $_POST['usr'];

    $tquery  = "SELECT teacher_id FROM teacher WHERE teacher_id = '$usr'";
    $techquery = "SELECT technician_id FROM technician WHERE technician_id='$usr'";

    $result = mysqli_query( $link, $tquery );
    $result2 = mysqli_query( $link, $techquery );
    //echo $tquery;
    if (mysqli_num_rows($result) == 1){
        session_start();
        $_SESSION['teach'] = $usr;
        do_logging("Teacher logged in as '$usr'", "INFO");
        header ("Location: teacher_$usr.php");
        exit();
    }
    else if (mysqli_num_rows($result2) == 1) {
        session_start();
        $_SESSION['tech'] = $usr;
        do_logging("Technician logged in as '$usr'", "INFO");
        header ("Location: tech_$usr.php");
        exit();
    }
    else {
        do_logging("Failed login attempt with '$usr'", "WARNING");
        $f = 1;
    }
}

?>

<script>
    function validate(){
        var y=document.getElementById("usr").value;
        if (!y.localeCompare("")) {
            alert("ID should not be blank. Please try again.");
            return false;
        }
        else {return true;}
    }

    function showFail() {
        document.getElementById("fail").innerHTML = "block";
    }
</script>
<div class="container">
    <h2 id="title">Felsted Requisition Sheet Login</h2>
    <div id=formcontainer>
        <form id="loginForm" action="" method="POST">
            <label for="usr"><b>ID:</b></label>
            <input type="text" id="usr" placeholder="Enter Teacher/Technician ID" name="usr" required>
            <br/>
            <button class="fa" id="loginButton" type="submit" onclick="return validate()" >Login  &#xf090;</button>
        </form>

        <?php if (isset($f)) {echo ' <br/><br/><p id=fail> That ID was not recognised. Please try again.</p>';} ?>
    </div>
    <div class="loginInformation"><b>&#9432;</b><p>Your login ID is three letters, usually your initials.</p></div>
</div>

</body>

</html>
