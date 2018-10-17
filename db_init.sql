DROP DATABASE IF EXISTS felstedreq;
CREATE DATABASE felstedreq;
USE felstedreq;

CREATE TABLE teacher (
  name VARCHAR(20),
  teacher_id CHAR(3),
  PRIMARY KEY(teacher_id)
);

CREATE TABLE technician (
  name VARCHAR(20),
  subject ENUM('CHEM', 'PHYS', 'BIOL'),
  technician_id CHAR(3),
  PRIMARY KEY(technician_id)
);

CREATE TABLE lesson (
  name VARCHAR(20),
  room VARCHAR(20),
  day ENUM('MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'),
  period TINYINT,
  week ENUM('A', 'B'),
  subject ENUM('CHEM', 'PHYS', 'BIOL'),
  lesson_id INT NOT NULL AUTO_INCREMENT,
  teacher_id CHAR(3),
  PRIMARY KEY(lesson_id),
  FOREIGN KEY (teacher_id) REFERENCES teacher(teacher_id)
);

CREATE TABLE history_lesson (
  name VARCHAR(20),
  room VARCHAR(20),
  day ENUM('MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'),
  period TINYINT,
  week ENUM('A', 'B'),
  subject ENUM('CHEM', 'PHYS', 'BIOL'),
  lesson_id INT NOT NULL AUTO_INCREMENT,
  teacher_id CHAR(3),
  PRIMARY KEY(lesson_id),
  FOREIGN KEY (teacher_id) REFERENCES teacher(teacher_id)
);

CREATE TABLE requisition (
  requisition_id INT NOT NULL AUTO_INCREMENT,
  title VARCHAR(40),
  description TEXT NOT NULL,
  risk_assessment ENUM('YES', 'NO'),
  risk_actions TEXT NOT NULL, 
  lesson_id INT,
  done_bool tinyint(1),
  PRIMARY KEY(requisition_id),
  FOREIGN KEY (lesson_id) REFERENCES lesson(lesson_id)
);

CREATE TABLE history_requisition (
  requisition_id INT NOT NULL AUTO_INCREMENT,
  title VARCHAR(40),
  description TEXT NOT NULL,
  risk_assessment ENUM('YES', 'NO'),
  risk_actions TEXT NOT NULL,
  lesson_id INT,
  done_bool tinyint(1),
  PRIMARY KEY(requisition_id),
  FOREIGN KEY (lesson_id) REFERENCES history_lesson(lesson_id)
);

INSERT INTO technician VALUES ('Karen','CHEM', 'kjd');