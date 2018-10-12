# felreq

Scripts:
 * db_init.sql - INITIALISATION FOR DATABASE, creates tables and inserts one technican (kjd).
 
 * setup.py - Python script which: processes excel timetable document into lessons, creates sql insert statements for all teachers and all lessons, generates php base files for teachers (currently a static list of 4) under the alias teacher_(teacher_name).php
 
 * db_insert.sql - the script setup.py creates for inserting lessons into db.
