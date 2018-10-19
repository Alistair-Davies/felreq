<?php

function insertHistory($weekname) {
    global $link;
    $queries = file_get_contents("/home/pi/repos/felreq/history/$weekname.sql");
    if (mysqli_multi_query($link, $queries)) {
        while (mysqli_more_results($link) && mysqli_next_result($link)) {
            $i = mysqli_store_result($link);}
        
        do_logging("Replaced history with $weekname information", "INFO");
    }
    else {
        do_logging("Problem replacing history with $weekname data", "ERROR");
    }

}

function do_logging($message, $level){
    $date = date("Y-m-d h:m:s");
    $file = __FILE__;
    error_log("[ $date ] [ $level ] [ $file ] $message".PHP_EOL,3,"/var/log/nginx/felreq");
}

function generateModal()
{
    echo "<div id='myModal' class='modal'>";
    echo "<div class=\"modal-content\"><span class=\"close\">&times;</span>";
    echo "<span class='infTitle'><h3>Requisition for: <b><span id=infTitle></span><br/></b>from: <b><span id='tid'></span></b></h3> </span>";
    echo "<span><br/><b>Title:</b> <div id=title></div><br><b>Description: </b><div id=desc></div><br><b> Risk Assessment:</b> <div id=ras></div><br><b> Risk Actions: </b><div id=rac></rac></span></div>";
    echo "</div></div>";
}

function generateTable($w) {
    global $link;
    $weekLetter = $w[0];

    $days = array('MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT');

    $teachersquery = "SELECT DISTINCT teacher_id from history_lesson;";
    $teachersresult = mysqli_query( $link, $teachersquery);
    $i=0;
    while ( $x = mysqli_fetch_array($teachersresult, MYSQLI_ASSOC) ) {
        $teachers[$i] = $x['teacher_id'];
        $i++;
    }
    
    echo "<table class='timetable'><tr><th colspan='2'></th>";
    foreach ($teachers as $tea) {
        echo "<th class=teacherName>$tea</th>";
    }
    echo "</tr>";
    $d = 0;
    foreach ($days as $day) {
        echo "<tbody>";
        echo "<td class='tableDay' rowspan='7'> $day<br/>";
        echo "</td>";
        for ($x = 1; $x <= 6; $x++) {
            echo "<tr><td class='tablePeriod'>$x</td>";
            foreach($teachers as $teacher) {
                insertLessons($day, $x, $teacher, $weekLetter);
            }
            echo "</tr>";
        }
        echo "</tbody>";
        $d += 1;
    }
    echo "</table>";
}

function insertLessons($day, $period, $teacher, $weekLetter) {
    global $link;

    $lesson_query = "SELECT name, room, subject, lesson_id FROM history_lesson WHERE teacher_id='$teacher' AND day='$day' AND period=$period AND week='$weekLetter'";
    $lesson_result = mysqli_query( $link, $lesson_query);
    if ($lesson_result->num_rows > 0 ) {
        while ( $lessonResult = mysqli_fetch_array($lesson_result, MYSQLI_ASSOC) ) {
            $lesson = array($lessonResult['name'],  $lessonResult['subject'],  $lessonResult['room'], $lessonResult['lesson_id']);
        }
        $requisition_query = "SELECT title, requisition_id, description, risk_assessment, risk_actions, done_bool FROM history_requisition WHERE lesson_id=$lesson[3]";
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
            echo '<div class="lessonInfo">', $lesson[0] , '<br>', $lesson[1], '<br>', $lesson[2], '</div>';
            echo "</td>";
        }
    }
    else { echo "<td class='emptyLesson'></td>"; }
}
?>
