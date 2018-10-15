import csv
import xlrd
import os

def csv_from_excel():
    wb = xlrd.open_workbook('MasterStaffTT Sc 030918.xlsx')
    sh = wb.sheet_by_name('Staff Timetable')
    timetable = open('timetable.csv', 'w')
    wr = csv.writer(timetable, quoting=csv.QUOTE_ALL)

    for rownum in range(sh.nrows):
        wr.writerow(sh.row_values(rownum))

    timetable.close()

def listTeachers(data):
    teachers=[]
    numrows = len(data)
    row = 2
    while (row < numrows):
        teachers.append(data[row][0].strip())
        row+=1
    return teachers

def getTeacherLesson(teacher, data):
    column=0
    lessons = []
    days = ['MON||A||', 'TUE||A||', 'WED||A||', 'THU||A||', 'FRI||A||', 'SAT||A||',
            'MON||B||', 'TUE||B||', 'WED||B||', 'THU||B||', 'FRI||B||', 'SAT||B||']
    day = days[0]
    c=0
    while column < len(data)-1:
        column+=1
        if (column%7==0):
            c+=1
            day=days[c]
           # print(day)
            continue
        elif (data[column] == "\n\n" or data[column] == ''):
            continue
        else:
            period = str(column%7)+"||"
            less = period + day + data[column].replace('\n', '||')
            lessons.append(less)
        
    return lessons

def insertTeacherSQL(teacher):
    statement = "INSERT INTO teacher VALUES ('"+teacher+"','"+teacher+"');\n"
    return statement

def insertTeachersLessons(teacher, lessons):
    statements=[]
    for i in lessons:
        less = processLesson(i, teacher)
        if less != 0:
            statements.append(less)
    return statements

def processLesson(lesson, teacher):
    if "||||" in lesson:
        print("Disregarding ["+lesson.replace('||', ' ') +"] as suspected invalid lesson")
        return 0
    else:
      try:
          #print(lesson)
          d = lesson.split("||")
      except:
          print("Not a valid lesson: "+lesson.replace('||', ' '))
          return 0
      period = d[0]
      day = d[1]
      week = d[2]
      subject = d[3]
      if subject == 'BI':
          subject = 'BIOL'
      elif subject == 'CH':
          subject = 'CHEM'
      elif subject == 'PH':
          subject = 'PHYS'
      else:
           print("Not a valid subject: "+lesson.replace('||', ' '))
           return 0
      group = d[4]
      room = d[5]
      return "INSERT INTO lesson VALUES ('"+group+"','"+room+"','"+day+"',"+period+",'"+week+"','"+subject+"',NULL,'"+teacher+"');\n"

def insertTeacherHTML(teacher):
    return "<?php\n\trequire(\"functions.php\");\n\tif(!isset($_GET['teach'])\
||!strcmp($_GET['teach'], '"+teacher+"')){\n\t\t$teach=\""+teacher+"\";\n\
\t\t$enableEdit=1;\n\t}\n\telse{\n\t\t$teach=$_GET['teach'];\n\t\t$enableEdit=0;\n\t}\n\trequire\
(\"defaultlayout_teach.php\");\n?>"
    
csv_from_excel()


with open('timetable.csv','r', newline='') as f:
    reader = csv.reader(f)
    data=list(reader)
   # print(data)
    teachers = listTeachers(data)
    row = 2
    teacherLessons = {}
    for teacher in teachers:
        teacherLessons[teacher] = getTeacherLesson(teacher, data[row])
        row+=1
    f.close()

chemTeachers = ["AJP", "HJM", "LEB", "SMG"]
here = os.path.dirname(os.path.realpath(__file__))
directory = "main"

for teacher in chemTeachers:
    x = "teacher_"+teacher.lower()+".php"
    filepath = os.path.join(here, directory, x)
    try:
        f = open(filepath, 'w')
        f.write(insertTeacherHTML(teacher))
        f.close()
    except IOError as e:
        print ("problem with path: "+filepath)
        print(e)

f = open("db_insert.sql", "w")

for teacher in teacherLessons:
    f.write(insertTeacherSQL(teacher))
    lessons = insertTeachersLessons(teacher, teacherLessons[teacher])
    for lessonquery in lessons:
        f.write(lessonquery)
                            
