# felreq

Scripts:
 * db_init.sql - INITIALISATION FOR DATABASE, creates tables and inserts one technican (kjd).
 
 * setup.py - Python script which: processes excel timetable document into lessons, creates sql insert statements for all teachers and all lessons, generates php base files for teachers (currently a static list of 4) under the alias teacher_(teacher_name).php
 
 * db_insert.sql - the script setup.py creates for inserting lessons into db.

 * saveWeek.py - Called by PHP on defaultlayout_tech.php on button click. Creates a sql script in history/ with format Add.mm.yy-dd.mm.yy.sql which contans information on that week.

* Add.mm.yy-dd.mm.yy.sql - A sql script which clears history_lesson and history_requisition tables, then inserts corresponding weeks data into those tables. (A way of saving reqisition weeks.) - To be used by history.php.
