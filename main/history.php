
<head>
    <link rel="stylesheet" type="text/css" href="historycss.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<?php
    session_start();
    if (!isset($_SESSION['tech'])) {
        header("Location:login.php");
    }
    $tech = $_SESSION['tech'];

    require("functions_history.php");
    if (!isset($_GET['view'])) {
		echo "<div class='histContainer'><h1>History Archives</h1>";
        echo "<form class='buttonForms' method='POST' action='tech_$tech.php'><input class='backButton' type='submit' value='Back to current week'/></form>";
        echo '<form method="POST" action="login.php"><input class="fa" id="logoutButton" type="submit" value="Logout &#xf08b;"/></form>';
        echo "<div class='listOflinks'>";
        include("weekLinks.php");
        echo "</div>";
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
        
		$historydate = $_GET['view'];
        echo "<div class='header'><h1>History: $historydate</h1></div>";
        echo '<form method="GET" action=""><input class="backButton" type="submit" value="back"/></form>';

        generateModal();
        insertHistory($historydate);
        generateTable($historydate);
    }
?>

<script>
    var close1 = document.getElementsByClassName("close")[0];
    var modal = document.getElementById('myModal');

    function fillContent(type,t,d,ras,rac,rid,lid,tid,rdone) {

        close1.onclick = function() {
            modal.style.display = "none";
            document.getElementsByTagName("body")[0].style ='overflow:visible';
        };

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
                document.getElementsByTagName("body")[0].style ='overflow:visible';
            }
        };

        if (type == "info") {
            modal.style.display = "block";
            document.getElementsByTagName("body")[0].style ='overflow:hidden';
            var title = document.getElementById('title');
            var desc = document.getElementById('desc');
            var rass = document.getElementById('ras');
            var racc = document.getElementById('rac');
            document.getElementById('infTitle').innerHTML=lid;
            document.getElementById('tid').innerHTML=tid;

            title.innerHTML=t;
            desc.innerHTML=d.replace(/&lt;br\/&gt;/g, '<br/>');;
            rass.innerHTML=ras;
            racc.innerHTML=rac.replace(/&lt;br\/&gt;/g, '<br/>');;
        }

    }

</script>
