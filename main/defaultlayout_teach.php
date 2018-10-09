<head>
<link rel="stylesheet" type="text/css" href="teachercss.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<?php 
  if (isset($_POST['lesson_id'])){insertreq();}
  if (isset($_POST['remrid'])){removereq($_POST['remrid']);}
  if (isset($_POST['updrid'])){updatereq($_POST['updrid']);}
  $weekdates = getStartAndEndDate(date('W')-1,date('Y'));
?>


<div class="tab">
  <button class="tablinks" onclick="openCity(event, 'thisweek')">This Week <?php echo $weekdates[0][0], ' - ', $weekdates[0][6]; ?></button>
  <button class="tablinks" onclick="openCity(event, 'nextweek')">Next Week <?php echo $weekdates[1][0], ' - ', $weekdates[1][6]; ?></button>
</div>


  <div class="modals">
    <?php 
	generateModal("create");
    generateModal("info");
	?>
  </div>


<div id="thisweek" class="tabcontent">
  <?php 
	   $week=$weekdates[0][7];
	   echo "<span class='weekLetter'><p> $week - $teach</p></span>";

      echo '<div class="selectTeacher">';
      $teachers=getTeachers();
      foreach($teachers as $tea) {
          $url = $_SERVER['PHP_SELF'] . "?teach=$tea";

          echo "<form class='teaOption' action='$url' method='POST'>";
          if (isset($_POST['copyinfo'])) {
              $copyinfo = $_POST['copyinfo'];

              echo "<input type='hidden' value='$copyinfo' name='copyinfo'</input>";
          }
          echo "<input type='submit' class='teacherOption' value='$tea'></input></form>";
      }
      echo '</div>';

      generateTable(0);
  ?>
</div>

<div id="nextweek" class="tabcontent">
  <?php 
	   $week=$weekdates[1][7];
       echo "<span class='weekLetter'><p> $week - $teach </p></span>";

      echo '<div class="selectTeacher">';
      $teachers=getTeachers();
      foreach($teachers as $tea) {
          $url = $_SERVER['PHP_SELF'] . "?teach=$tea";

          echo "<form class='teaOption' action='$url' method='POST'>";
          if (isset($_POST['copyinfo'])) {
              $copyinfo = $_POST['copyinfo'];

              echo "<input type='hidden' value='$copyinfo' name='copyinfo'</input>";
          }
          echo "<input type='submit' class='teacherOption' value='$tea'></input></form>";
      }
      echo '</div>';

       generateTable(1);
  ?> 
</div>

<div id="Tokyo" class="tabcontent">
  <h3>Tokyo</h3>
  <p>Tokyo is the capital of Japan.</p>
</div>

<script>
  var tab = sessionStorage.getItem("sessionTab");
  if (tab) { openCity(event, tab); }

function openCity(evt, weekview) {
    var i, tabcontent, tablinks;
	sessionStorage.sessionTab = weekview;

    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    document.getElementById(weekview).style.display = "block";
    evt.currentTarget.className += " active";
}
</script>

<script>

    var modal = document.getElementById('myModal');
    var modal2 = document.getElementById('myModal2');
    var modal3 = document.getElementById('myModal3');
    var close1 = document.getElementsByClassName("close")[0];
    var cancel = document.getElementsByClassName("cancel")[0];
    var cancel2 = document.getElementsByClassName("cancel2")[0];

    function pasteInfo(info) {
        var tit = document.getElementById('createtitle');
        var des = document.getElementById('createdesc');
        var racc = document.getElementById('createracc');
        if (info[2] =="YES") { document.getElementById('createyes').checked="checked"; }
        else { document.getElementById('createno').checked="checked"; }
        tit.value = info[0];
        des.value = info[1];
        racc.value = info[3];
    }

    function fillContent(type,t,d,ras,rac,rid,lid) {

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
            if (document.getElementById('rid')!==null) {
                var editButton = document.getElementById('editButton');
                document.getElementById('rid').value=rid;
                editButton.onclick = function() { fillContent('edit', t, d, ras, rac, rid, lid); };
            }

            document.getElementById('infTitle').innerHTML=lid;
            document.getElementById('copyInfo').value=t+'||'+d+'||'+ras+'||'+rac;
            title.innerHTML=t;
            desc.innerHTML=d.replace(/&lt;br\/&gt;/g, '<br/>');
            rass.innerHTML=ras;
            racc.innerHTML=rac.replace(/&lt;br\/&gt;/g, '<br/>');

        }
        else if (type == "create") {
            document.getElementsByTagName("body")[0].style ='overflow:hidden';
            modal2.style.display = "block";
            var title = document.getElementById('reqfor');
            document.getElementById('lesson_id').value=d;
            title.innerHTML=t;
            title.innerHTML=t;
            cancel.onclick = function() {
                document.getElementById('createtitle').value='';
                document.getElementById('createdesc').value='';
                document.getElementById('createracc').value='';
                document.getElementById('createno').checked="checked";
                modal2.style.display = "none";
                document.getElementsByTagName("body")[0].style ='overflow:visible';
            };
        }
        else if (type == "edit") {
            modal3.style.display = "block";
            document.getElementById('edTitle').innerHTML=lid;
            document.getElementById('editreqid').value=rid;
            document.getElementById('edittitle').value=t;
            document.getElementById('editdesc').value=d.replace(/&lt;br\/&gt;/g, '\n').replace(/&#039;/g, "'").replace(/&quot;/g, '"').replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>');
            if (ras =="YES") { document.getElementById('editrassyes').checked="checked"; }
            else { document.getElementById('editrassno').checked="checked"; }
            document.getElementById('editracc').value=rac.replace(/&lt;br\/&gt;/g, '\n').replace(/&#039;/g, "'").replace(/&quot;/g, '"').replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>');

            cancel2.onclick = function() {
                modal3.style.display = "none";
            };
        }

    }
</script>

</html>

