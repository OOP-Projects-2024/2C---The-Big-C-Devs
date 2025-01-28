<?php
class Attendance {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function recordAttendance($studentID, $courseID, $date, $status) {
        $query = 'INSERT INTO attendance (studentID, courseID, date, status) 
                  VALUES (:studentID, :courseID, :date, :status)
                  ON DUPLICATE KEY UPDATE status = :status';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':studentID', $studentID);
        $stmt->bindParam(':courseID', $courseID);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':status', $status);

        return $stmt->execute();
    }

    public function getAttendanceByStudent($studentID, $courseID) {
        $query = 'SELECT * FROM attendance 
                  WHERE studentID = :studentID AND courseID = :courseID 
                  ORDER BY date DESC';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':studentID', $studentID);
        $stmt->bindParam(':courseID', $courseID);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAttendanceByCourse($courseID, $date) {
        $query = 'SELECT s.studentID, s.firstName, s.lastName, a.status 
                  FROM sk_students s 
                  LEFT JOIN attendance a ON s.studentID = a.studentID AND a.date = :date 
                  WHERE s.courseID = :courseID';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':courseID', $courseID);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

