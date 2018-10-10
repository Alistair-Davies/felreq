<?php
    $dbname = 'felstedreq';
    $dbuser = 'root';
    $dbhost = 'localhost';
    $link = mysqli_connect( $dbhost, $dbuser)
    or die( "Unable to Connect to '$dbhost'" );
    mysqli_select_db( $link, $dbname )
    or die("Could not open the db '$dbname'");


function remove($w) {
    global $link;
    $removequery = " DELETE FROM requisition WHERE lesson_id in (SELECT lesson_id FROM lesson WHERE week='$w');";
    if (mysqli_query($link, $removequery)) {
        header('Location:'.$_SERVER['PHP_SELF']);
    }
    else {
        echo "<p> Error: ".$removequery."<br/>".mysqli_error($link);
    }
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

function generateTable($w) {
    global $weekdates;
    global $link;
    global $week;
    $subject= $GLOBALS['subject'];
    if (!isset($_GET['days'])) {
        $days = array('MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT');
    }
    else {
        $days = array($_GET['days']);
    }
    if (!isset($_GET['teachers'])) {
        $teachersquery = "SELECT DISTINCT teacher_id from lesson WHERE subject='$subject'";
        $teachersresult = mysqli_query( $link, $teachersquery);
        $i=0;
        while ( $x = mysqli_fetch_array($teachersresult, MYSQLI_ASSOC) ) {
            $teachers[$i] = $x['teacher_id'];
            $i++;
        }
    }
    else {
        $teachers = array($_GET['teachers']);
    }

    echo "<button onclick=focusDefault() class=DefaultView>Default Week View</button>";
    echo "<form class='clearReqForm' action='' method='POST'><input id=wid type='hidden' name='remweek' value=$week /><input onclick=\"return confirm('Are you sure? This will remove this weeks requisitions.');\" class='clearReq' type='submit' value='Clear Week'/></form>";
    echo "<table class='timetable'><tr><th colspan='2'></th>";
    foreach ($teachers as $tea) {
    echo "<th class=teacherName type='button' onclick=focusTeacher('$tea')>$tea</th>";
    }
    echo "</tr>";
    $d = 0;
        foreach ($days as $day) {
            echo "<tbody>";
            echo "<td type='button' onclick=focusDay('$day') class='tableDay' rowspan='7'> $day<br/>";
            echo substr($weekdates[$w][$d], 0, 5);
            echo "</td>";
            for ($x = 1; $x <= 6; $x++) {
                echo "<tr><td class='tablePeriod'>$x</td>";
                foreach($teachers as $teacher) {
                    insertLessons($day, $x, $teacher);
                }
                echo "</tr>";
            }
            echo "</tbody>";
            $d += 1;
        }
    echo "</table>";
}


function generateModal($type) {
	  if ($type == "info") {
		echo "<div id='myModal' class='modal'>";
		echo "<div class=\"modal-content\"><span class=\"close\">&times;</span>";
		echo "<span class='infTitle'><h3>Requisition for: <b><span id=infTitle></span><br/></b>from: <b><span id='tid'></span></b></h3> </span>";
		echo "<span><br/><b>Title:</b> <div id=title></div><br><b>Description: </b><div id=desc></div><br><b> Risk Assessment:</b> <div id=ras></div><br><b> Risk Actions: </b><div id=rac></rac></span></div>";
		generateModal("edit");
		echo "<button id='editButton' onclick=fillContent('edit')> Edit Requisition</button>";
		echo "<form class='removeForm' action='' method='POST'><input id=rid type='hidden' name='remrid' value=''/><input onclick=\"return confirm('Are you sure? This will remove this requisition.');\" class='removeButton' type='submit' value='Remove requisition'/></form>";
		echo "<form class='markDone' action='' method='POST'><input id=doneid type='hidden' name='doneID' value=''/><input id='doneEval' type='hidden' name='doneBool' value=''/><input id='doneButton' type='submit' value=''/></form>";
        echo "</div>";
	  }

	  else if ($type == "create") {
		echo "<div id='myModal2' class='modal'>";
	    echo "<div class=\"modal-content\">";
		echo "<form action='' method='post'>";
		echo "<span class='crTitle'><h3>Creating requisition for: <b><span id=reqfor></span></b></h3> </span>";
		echo "<input id=lesson_id type='hidden' name='lesson_id' value=''/><br/>";
		echo "<b>Title:</b><br/> <input id='createtitle' type='text' name='title' value=''/><br/>";
		echo "<b>Description:</b><br/><textarea id='createdesc' name='desc' value='' rows='5' cols='50'></textarea><br/>";
		echo "<b>Risk Assessment:  </b><input type='radio' name='rass' value='YES'/> YES<input type='radio' name='rass' value='NO'/> NO<br/><br/>";
		echo "<b>Risk Actions:</b><br/><textarea id='createracc' rows='5' cols='50' value='' name='rac'></textarea><br/>";
		echo "<input type='submit' class=createButton value='Create'/></form><span class=\"cancel\">Cancel</span></div>";
		echo "</div>";
	  }

	  else if ($type == "edit") {
		echo "<div id='myModal3' class='modal'>";
		echo "<div class=\"modal-content\">";
		echo "<span><form action='' method='POST'>";
		echo "<span class='edTitle'><h3>Editing requisition for: <b><span id=edTitle></span></b></h3> </span><input id=editreqid type='hidden' name='updrid' value=''/>";
		echo "<br/><b>Title:</b><br/><input id=edittitle type='text' name='title' value='' required/><br/>";
		echo "<b>Description:</b><br/><textarea id='editdesc' name='desc' rows='5' cols='50' value='' required></textarea><br/>";
		echo "<b>Risk Assessment:  </b><input id=editrassyes type='radio' name='rass' value='YES'/>YES <input id=editrassno type='radio' name='rass' value='NO'/> NO<br/><br/>";
		echo "<b>Risk Actions:</b><br/><textarea id='editracc' rows='5' cols='50' value='' name='rac' required></textarea><br/>";
        echo "<span class='buttonContainer'><input class=updateButton type='submit' value='Update'/></form>";
        echo "<span class='cancel2'>Cancel</span>";
        echo "</span>";
        echo "</div></div>";
	  }
}

function insertreq() {
	global $link;
	$processedDesc = str_replace(array("\\n","\r\n", "\n"), '<br/>',$_POST['desc']);
	$processedRacc = str_replace(array("\\n","\r\n", "\n"), '<br/>',$_POST['rac']);
	$newreq = array($_POST['lesson_id'], htmlspecialchars(str_replace(array("\\n","\r\n", "\n"), ' ', $_POST['title']), ENT_QUOTES), htmlspecialchars($processedDesc, ENT_QUOTES),$_POST['rass'], htmlspecialchars($processedRacc, ENT_QUOTES));
	$insert = "INSERT INTO requisition VALUES (DEFAULT, '$newreq[1]', '$newreq[2]', '$newreq[3]', '$newreq[4]', $newreq[0], FALSE);";
    if (mysqli_query($link, $insert)) {
		header('Location:'.$_SERVER['PHP_SELF']);
	}
	else {
		echo "<p> Error: ".$insert."<br/>".mysqli_error($link);
	}
	$_POST=array();
}

function removereq($rid) {
    global $link;
	$remove = "DELETE FROM requisition WHERE requisition_id=$rid;";
	if (mysqli_query($link, $remove)) {
		header('Location:'.$_SERVER['PHP_SELF']);
	}
	else {
		echo "<p> Error: ".$remove."<br/>".mysqli_error($link);
	}
	$_POST=array();
}

function markDone($id, $doneBool) {
    global $link;
    $updquery = "UPDATE requisition set done_bool=$doneBool WHERE requisition_id=$id;";
    if (mysqli_query($link, $updquery)) {
        header('Location:'.$_SERVER['PHP_SELF']);
    }
    else {
        echo "<p> Error: ".$updquery."<br/>".mysqli_error($link);
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
		header('Location:'.$_SERVER['PHP_SELF']);
	}
	else {
		echo "<p> Error: ".$update."<br/>".mysqli_error($link);
	}
	$_POST=array();
}

function insertLessons($day, $period, $teacher) {
	global $link;
	global $week;
      	$lesson_query = "SELECT name, room, subject, lesson_id FROM lesson WHERE teacher_id='$teacher' AND day='$day' AND period=$period AND week='$week'";
		#echo $lesson_query;
		$lesson_result = mysqli_query( $link, $lesson_query);
		if ($lesson_result->num_rows > 0 ) {
			while ( $lessonResult = mysqli_fetch_array($lesson_result, MYSQLI_ASSOC) ) {
				$lesson = array($lessonResult['name'],  $lessonResult['subject'],  $lessonResult['room'], $lessonResult['lesson_id']);
			}
			$requisition_query = "SELECT title, requisition_id, description, risk_assessment, risk_actions, done_bool FROM requisition WHERE lesson_id=$lesson[3]";
			$requisition_result = mysqli_query( $link, $requisition_query);
			while ( $requisition = mysqli_fetch_array($requisition_result, MYSQLI_ASSOC) ) {
				$req_title=$requisition['title'];
				$req_id=$requisition['requisition_id'];
				$req_desc = $requisition['description'];
				$req_ras=$requisition['risk_assessment'];
				$req_rac=$requisition['risk_actions'];
				$req_done=$requisition['done_bool'];
			}

			if (isset($req_title)) {
                echo '<td ';
                if ($req_done) {
                    echo "id='lessonDone';";
                }
                echo 'type="button" class="lesson" onclick="fillContent(\''."info".'\', \''.htmlspecialchars($req_title, ENT_QUOTES).'\', \''.htmlspecialchars($req_desc, ENT_QUOTES).'\', \''.$req_ras.'\', \''.htmlspecialchars($req_rac, ENT_QUOTES).'\', \''.$req_id.'\', \''.$lesson[0].'\', \''.$teacher.'\',\''.$req_done.'\' )">';
				echo '<span class="lessonInfo">', $lesson[0] , '<br>', $lesson[1], '<br>', $lesson[2], '</span>';
                echo "<div class='reqContainer'><br/><span class='reqTitle'><b>$req_title</b></span>";
                if (strcmp($req_ras, 'NO')){
				   echo '<span class="reqTick">&#10004</span></div>';
                }
                else {
                    echo '<span class="reqCross">&#10006</span></div>';
                }
				echo '</td>';
			}
			else {
			  echo '<td class="lessonCreate">';
			  //echo '<button onclick="fillContent(\''."create".'\', \''.$lesson[0].'\', \''.$lesson[3].'\')"';
			  //echo 'class="CreateRequisition">+</button>';
			  echo '<div class="lessonInfo">', $lesson[0] , '<br>', $lesson[1], '<br>', $lesson[2], '</div>';
			  echo "</td>";
			}
	    }
		else { echo "<td class='emptyLesson'></td>"; }
}

?>