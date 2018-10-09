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

CREATE TABLE requisition (
  requisition_id INT NOT NULL AUTO_INCREMENT,
  title VARCHAR(40),
  description VARCHAR(1000),
  risk_assessment ENUM('YES', 'NO'),
  risk_actions VARCHAR(400), 
  lesson_id INT,
  PRIMARY KEY(requisition_id),
  FOREIGN KEY (lesson_id) REFERENCES lesson(lesson_id)
); 

INSERT INTO teacher VALUES ('Alistair', 'ali');
INSERT INTO teacher VALUES ('Tim', 'guy');
INSERT INTO teacher VALUES ('Melony', 'mel');
INSERT INTO teacher VALUES ('jim', 'jim');
INSERT INTO teacher VALUES ('simon', 'sim');

INSERT INTO technician VALUES ('Karen', 'CHEM', 'kjd');

INSERT INTO lesson VALUES ('10 Set 6', 'CHITT', 'MON', 2, 'A', 'CHEM', NULL, 'ali');
INSERT INTO lesson VALUES ('40 set 4', 'CHITT', 'TUE', 4, 'A', 'CHEM', NULL, 'ali');
INSERT INTO lesson VALUES ('9 set 1', 'CHITT', 'THU', 6, 'A', 'CHEM', NULL, 'ali');
INSERT INTO lesson VALUES ('9 set 1', 'CHITT', 'WED', 2, 'B', 'CHEM', NULL, 'ali');
INSERT INTO lesson VALUES ('9 set 1', 'CHITT', 'THU', 6, 'A', 'CHEM', NULL, 'guy');
INSERT INTO lesson VALUES ('33 set 44', 'CHITT', 'TUE', 3, 'A', 'CHEM', NULL, 'mel');
INSERT INTO lesson VALUES ('32 set 43', 'CHITT', 'TUE', 3, 'A', 'CHEM', NULL, 'jim');
INSERT INTO lesson VALUES ('32 set 43', 'CHITT', 'TUE', 3, 'A', 'CHEM', NULL, 'sim');



INSERT INTO requisition VALUES (1, 'Jelly Baby', 'I need acid and stuff', 'YES', 'I did the risk assess', 1);
INSERT INTO requisition VALUES (2, 'Magnets', 'Give me some magne', 'YES', 'I did the risk assess', 2);
INSERT INTO requisition VALUES (3, 'Magnets', 'Give me some magne', 'YES', 'I did the risk assess', 6);
  