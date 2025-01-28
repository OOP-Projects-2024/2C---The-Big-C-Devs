CREATE DATABASE student_management;
USE student_management;
-- Create the tables
CREATE TABLE sk_courses (
  courseID       VARCHAR(12)    NOT NULL,
  courseName     VARCHAR(255)   NOT NULL,
  PRIMARY KEY (courseID)
);

CREATE TABLE sk_students (
  studentID    INT(11)        NOT NULL AUTO_INCREMENT,
  courseID     VARCHAR(12)    NOT NULL,
  firstName    VARCHAR(255)   NOT NULL,
  lastName     VARCHAR(255)   NOT NULL,
  email        VARCHAR(255)   NOT NULL,
  PRIMARY KEY (studentID),
  FOREIGN KEY (courseID) REFERENCES sk_courses(courseID)
);

CREATE TABLE assignments (
  assignmentID INT(11)        NOT NULL AUTO_INCREMENT,
  courseID     VARCHAR(12)    NOT NULL,
  title        VARCHAR(255)   NOT NULL,
  description  TEXT,
  dueDate      DATE           NOT NULL,
  PRIMARY KEY (assignmentID),
  FOREIGN KEY (courseID) REFERENCES sk_courses(courseID)
);

CREATE TABLE attendance (
  attendanceID INT(11)        NOT NULL AUTO_INCREMENT,
  studentID    INT(11)        NOT NULL,
  courseID     VARCHAR(12)    NOT NULL,
  date         DATE           NOT NULL,
  status       ENUM('present', 'absent', 'late') NOT NULL,
  PRIMARY KEY (attendanceID),
  FOREIGN KEY (studentID) REFERENCES sk_students(studentID),
  FOREIGN KEY (courseID) REFERENCES sk_courses(courseID),
  UNIQUE KEY (studentID, courseID, date)
);

CREATE TABLE grades (
  gradeID      INT(11)        NOT NULL AUTO_INCREMENT,
  studentID    INT(11)        NOT NULL,
  assignmentID INT(11)        NOT NULL,
  score        DECIMAL(5,2)   NOT NULL,
  PRIMARY KEY (gradeID),
  FOREIGN KEY (studentID) REFERENCES sk_students(studentID),
  FOREIGN KEY (assignmentID) REFERENCES assignments(assignmentID),
  UNIQUE KEY (studentID, assignmentID)
);

-- Insert data into the database
INSERT INTO sk_courses VALUES
('cs601', 'Web Application Development'),
('cs602', 'Server-Side Web Development'),
('cs701', 'Rich Internet Application Development');

INSERT INTO sk_students VALUES
(1, 'cs601', 'John', 'Doe', 'john@doe.com'),
(2, 'cs601', 'Jane', 'Doe', 'jane@doe.com'),
(3, 'cs602', 'John', 'Smith', 'john@smith.com'),
(4, 'cs602', 'Jane', 'Smith', 'jane@smith.com'),
(5, 'cs701', 'John', 'Doe', 'john@doe.com'),
(6, 'cs701', 'Jane', 'Smith', 'jane@smith.com');

-- Insert sample assignments
INSERT INTO assignments (courseID, title, description, dueDate) VALUES
('cs601', 'HTML Basics', 'Create a simple HTML page with proper structure', '2023-09-15'),
('cs601', 'CSS Styling', 'Style your HTML page using CSS', '2023-09-30'),
('cs602', 'PHP Fundamentals', 'Write a PHP script to process form data', '2023-10-10'),
('cs602', 'Database Integration', 'Connect your PHP application to a MySQL database', '2023-10-25'),
('cs701', 'JavaScript Basics', 'Create an interactive web page using JavaScript', '2023-11-05'),
('cs701', 'AJAX Implementation', 'Implement AJAX in your web application', '2023-11-20');

-- Insert sample attendance records
INSERT INTO attendance (studentID, courseID, date, status) VALUES
(1, 'cs601', '2023-09-01', 'present'),
(2, 'cs601', '2023-09-01', 'present'),
(3, 'cs602', '2023-09-01', 'absent'),
(4, 'cs602', '2023-09-01', 'present'),
(5, 'cs701', '2023-09-01', 'late'),
(6, 'cs701', '2023-09-01', 'present');

-- Insert sample grades
INSERT INTO grades (studentID, assignmentID, score) VALUES
(1, 1, 85.5),
(2, 1, 92.0),
(3, 3, 78.5),
(4, 3, 88.0),
(5, 5, 95.5),
(6, 5, 91.0);