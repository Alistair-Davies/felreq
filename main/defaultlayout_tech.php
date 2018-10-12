<head>
    <link rel="stylesheet" type="text/css" href="techcss.css">
</head>
<?php
if (isset($_POST['lesson_id'])){insertreq();}
if (isset($_POST['remrid'])){removereq($_POST['remrid']);}
if (isset($_POST['updrid'])){updatereq($_POST['updrid']);}
if (isset($_POST['remweek'])){remove($_POST['remweek']);}
if (isset($_POST['doneID']) && isset($_POST['doneBool'])){markDone($_POST['doneID'], $_POST['doneBool']);}
$weekdates = getStartAndEndDate(date('W')-1,date('Y'));
?>
<div class="tab">
    <button class="tablinks" onclick="openWeek(event, 'thisweek')">This Week <?php echo $weekdates[0][0], ' - ', $weekdates[0][6]; ?></button>
    <button class="tablinks" onclick="openWeek(event, 'nextweek')">Next Week <?php echo $weekdates[1][0], ' - ', $weekdates[1][6]; ?></button>
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
    echo "<span class='weekLetter'><p> $week </p></span>";
    generateTable(0);
    ?>
</div>

<div id="nextweek" class="tabcontent">
    <?php
    $week=$weekdates[1][7];
    echo "<span class='weekLetter'><p> $week </p></span>";
    generateTable(1);
    ?>
</div>


<script>
    var tab = sessionStorage.getItem("sessionTab");
    if (tab) { openWeek(event, tab); }

    function openWeek(evt, weekview) {
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
    }
</script>

<script>

    var modal = document.getElementById('myModal');
    var modal2 = document.getElementById('myModal2');
    var modal3 = document.getElementById('myModal3');
    var close1 = document.getElementsByClassName("close")[0];
    var cancel = document.getElementsByClassName("cancel")[0];
    var cancel2 = document.getElementsByClassName("cancel2")[0];

    function clearReq(w) {

    }

    function focusDay(d) {
        var loc = window.location.href;
        if (loc.indexOf("?")>-1){
            loc = loc.substr(0,loc.indexOf("?"));
        }
        window.location.assign(loc + "?days=" +d);
    }

    function focusTeacher(t) {
        var loc = window.location.href;
        if (loc.indexOf("?")>-1){
            loc = loc.substr(0,loc.indexOf("?"));
        }
        window.location.assign(loc + "?teachers=" +t);
    }

    function focusDefault() {
        var loc = window.location.href;
        if (loc.indexOf("?")>-1){
            loc = loc.substr(0,loc.indexOf("?"));
        }
        window.location.assign(loc);
    }

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
            var editButton = document.getElementById('editButton');
            var doneButton = document.getElementById('doneButton');
            document.getElementById('infTitle').innerHTML=lid;
            document.getElementById('tid').innerHTML=tid;

            document.getElementById('rid').value=rid;
            document.getElementById('doneid').value=rid;
            if (rdone==0) {
               doneButton.value="\u2611";
               document.getElementById('doneEval').value = "TRUE";
            }
            else {
                doneButton.value="\u2610";
                document.getElementById('doneEval').value = "FALSE";
            }
            title.innerHTML=t;
            desc.innerHTML=d.replace(/&lt;br\/&gt;/g, '<br/>');;
            rass.innerHTML=ras;
            racc.innerHTML=rac.replace(/&lt;br\/&gt;/g, '<br/>');;
            editButton.onclick = function() { fillContent('edit', t, d, ras, rac, rid, lid, tid); };

        }
        else if (type == "create") {
            document.getElementsByTagName("body")[0].style ='overflow:hidden';
            modal2.style.display = "block";
            var title = document.getElementById('reqfor');
            document.getElementById('lesson_id').value=d;
            title.innerHTML=t;
            cancel.onclick = function() {
                modal2.style.display = "none";
                document.getElementsByTagName("body")[0].style ='overflow:visible';
            };
        }
        else if (type == "edit") {
            modal3.style.display = "block";
            document.getElementById('edTitle').innerHTML=lid;
            document.getElementById('editreqid').value=rid;
            document.getElementById('edittitle').value=t.replace(/&#039;/g, "'").replace(/&quot;/g, '"').replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>');
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

