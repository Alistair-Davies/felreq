<?php
function getTeachers() {
    global $link;
    $query = "SELECT DISTINCT teacher_id from lesson WHERE subject='CHEM'";
    $result = mysqli_query( $link, $query);
    $i=0;
    while ( $x = mysqli_fetch_array($result, MYSQLI_ASSOC) ) {
        $teachers[$i] = $x['teacher_id'];
        $i++;
    }
    return $teachers;
}

function generateTable($w) {
    global $weekdates;
    $teacher= $GLOBALS['teach'];

    $days = array('MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT');

    echo "<table class='timetable'><tr><th colspan='2'></th>";
    echo "<th>$teacher</th>";
    echo "</tr>";
    $d = 0;
    foreach ($days as $day) {
        echo "<tbody>";
        echo "<td class='tableDay' rowspan='7'> $day ";
        echo substr($weekdates[$w][$d], 0,5);
        echo "</td>";
        for ($x=1;$x <=6; $x++) {
            echo "<tr><td class='tablePeriod'>$x</td>";
            insertLessons($day, $x, $teacher);
            echo "</tr>";
        }
        echo "</tbody>";
        $d +=1;
    }
    echo "</table>";
}


function generateModal($type) {
      global $enableEdit;
	  if ($type == "info") {
		echo "<div id='myModal' class='modal'>";
		echo "<div class=\"modal-content\"><span class=\"close\">&times;</span>";
		echo "<span class='infTitle'><h3>Requisition for: <b><span id=infTitle></span></b></h3> </span>";
		echo "<span><br/><b>Title:</b> <div id=title></div><br><b>Description: </b><div id=desc></div><br><b> Risk Assessment:</b> <div id=ras></div><br><b> Risk Actions: </b><div id=rac></rac></span></div>";
		if ($enableEdit){
		    generateModal("edit");
		    echo "<span class=buttonContainer>";
		    echo "<button id='editButton' onclick=fillContent('edit')> Edit Requisition</button>";
		    echo "<form class='removeForm' action='' method='POST'><input id=rid type='hidden' name='remrid' value=''/><input onclick=\"return confirm('Are you sure? This will remove this requisition.');\" class='removeButton' type='submit' value='Remove requisition'/></form>";
		}
		echo "<form class='copyForm' action='' method='POST'><input id=copyInfo type='hidden' name='copyinfo' value=''/><input id='copyButton' class='fa' type='submit' value='Copy  &#xf0c5'/></form></span>";
		echo "</form></div>";
	  }
	  
	  else if ($type == "create") {
		echo "<div id='myModal2' class='modal'>";
	    echo "<div class=\"modal-content\">";
		echo "<form action='' method='post'>";
		echo "<span class='crTitle'><h3>Creating requisition for: <b><span id=reqfor></span></b></h3> </span>";
		echo "<input id=lesson_id type='hidden' name='lesson_id' value=''/><br/>";
		echo "<b>Title:</b><br/> <input id='createtitle' maxlength='40' type='text' name='title' value='' required /><br/>";
		echo "<b>Description:</b><br/><textarea id='createdesc' name='desc' value='' rows='5' cols='50' required></textarea><br/>";
		echo "<b>Risk Assessment:  </b><input type='radio' id='createyes' name='rass' value='YES'/> YES<input id='createno' type='radio' name='rass' value='NO' checked/> NO<br/><br/>";
		echo "<b>Risk Actions:</b><br/><textarea id='createracc' rows='5' cols='50' value='' name='rac' required></textarea><br/>";
		echo "<span class='buttonContainer'><input type='submit' class=createButton value='Create'/>";
		echo "<span class=\"cancel\">Cancel</span>";
        if (isset($_POST['copyinfo'])) {
            $p = htmlspecialchars(json_encode(explode('||',$_POST['copyinfo'])), ENT_QUOTES);

            echo "<button type='button' id='pasteButton' class='fa' onclick='pasteInfo($p); return true;'> Paste &#xf0ea</button>";
        }
        echo "</span></form>";
		echo "</div>";
		echo "</div>";
	  }

	  else if ($type == "edit") {
		echo "<div id='myModal3' class='modal'>";
		echo "<div class=\"modal-content\">";
		echo "<span><form action='' method='POST'>";
        echo "<span class='edTitle'><h3>Editing requisition for: <b><span id=edTitle></span></b></h3> </span><input id=editreqid type='hidden' name='updrid' value=''/>";
		echo "<br/><b>Title:</b><br/><input id=edittitle maxlength='40' type='text' name='title' value='' required/><br/>";
		echo "<b>Description:</b><br/><textarea id='editdesc' name='desc' rows='5' cols='50' value='' required></textarea><br/>";
		echo "<b>Risk Assessment:  </b><input id=editrassyes type='radio' name='rass' value='YES'/>YES <input id=editrassno type='radio' name='rass' value='NO'/> NO<br/><br/>";
		echo "<b>Risk Actions:</b><br/><textarea id='editracc' rows='5' cols='50' value='' name='rac' required></textarea><br/>";
		echo "<span class='buttonContainer'><input class=updateButton type='submit' value='Update'/></form>";
		echo "<span class='cancel2'>Cancel</span>";
		echo "</span>";
		echo "</div></div>";
	  }
	  
}

function insertLessons($day, $period, $teacher) {
	global $link;
	global $week;
	global $enableEdit;
      	$lesson_query = "SELECT name, room, subject, lesson_id FROM lesson WHERE teacher_id='$teacher' AND day='$day' AND period=$period AND week='$week'";
		#echo $lesson_query;
		$lesson_result = mysqli_query( $link, $lesson_query);
		if ($lesson_result->num_rows > 0 ) {
			while ( $lessonResult = mysqli_fetch_array($lesson_result, MYSQLI_ASSOC) ) {
				$lesson = array($lessonResult['name'],  $lessonResult['subject'],  $lessonResult['room'], $lessonResult['lesson_id']);
			}
			$requisition_query = "SELECT title, requisition_id, description, risk_assessment, risk_actions FROM requisition WHERE lesson_id=$lesson[3]";
			$requisition_result = mysqli_query( $link, $requisition_query);
			while ( $requisition = mysqli_fetch_array($requisition_result, MYSQLI_ASSOC) ) {
				$req_title=$requisition['title'];
				$req_id=$requisition['requisition_id'];
				$req_desc = $requisition['description'];
				$req_ras=$requisition['risk_assessment'];
				$req_rac=$requisition['risk_actions'];
			}
			
			if (isset($req_title)) {
			    echo '<td type="button" class="lesson" onclick="fillContent(\''."info".'\', \''.htmlspecialchars($req_title, ENT_QUOTES).'\', \''.htmlspecialchars($req_desc, ENT_QUOTES).'\', \''.$req_ras.'\', \''.htmlspecialchars($req_rac, ENT_QUOTES).'\', \''.$req_id.'\', \''.$lesson[0].'\')">';
				echo "<div><span class='reqTitle'><em>Requisition:</em><br/><b>$req_title</b></span>";
				echo '<span class="lessonInfo">', $lesson[0] , '<br>', $lesson[1], '<br>', $lesson[2], '</span><span class="indicator"></span></div>';
				echo '</td>';
			}
			else { 
			  echo '<td class="lessonCreate">';
			  if ($enableEdit){
			    echo '<button onclick="fillContent(\''."create".'\', \''.$lesson[0].'\', \''.$lesson[3].'\')"';
			    echo 'class="CreateRequisition">+</button>';
			  }
			  echo '<div class="lessonInfo">', $lesson[0] , '<br>', $lesson[1], '<br>', $lesson[2], '</div><span class="indicator"></span>';
			  echo "</td>";
			}
	    }
		else { echo "<td class='emptyLesson'></td>"; }
}
?>
